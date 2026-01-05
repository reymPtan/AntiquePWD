<?php
// admin/register_pwd_save.php
// Processes PWD registration + auto-generate PWD ID + photo upload + saves skills

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireAdminRole(['SUPER', 'PWD_ADMIN']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register_pwd.php');
    exit;
}

// BASIC FIELDS (no pwd_number from form, auto-generate later)
$password     = $_POST['password'] ?? 'pwd12345';
$full_name    = trim($_POST['full_name'] ?? '');
$sex          = $_POST['sex'] ?? '';
$birthdate    = $_POST['birthdate'] ?? '';
$province     = 'Antique';
$municipality = trim($_POST['municipality'] ?? '');
$address      = trim($_POST['address'] ?? '');
$contact      = trim($_POST['contact_number'] ?? '');

$disCat       = $_POST['disability_category'] ?? '';
$blindType    = $_POST['blind_type'] ?? null;
$deafType     = $_POST['deaf_type'] ?? null;
$physType     = $_POST['physical_type'] ?? null;
$cause        = $_POST['cause_of_disability'] ?? '';

$educ         = $_POST['educational_level'] ?? '';
$employment   = $_POST['employment_status'] ?? '';

$guardianName = trim($_POST['guardian_name'] ?? '');
$guardianRel  = $_POST['guardian_relationship'] ?? '';
$guardianContact = trim($_POST['guardian_contact'] ?? '');

$skillsSelected = $_POST['skills'] ?? [];

// basic validation
if ($full_name === '' || $sex === '' || $birthdate === '' ||
    $municipality === '' || $address === '' || $disCat === '' ||
    $cause === '' || $educ === '' || $employment === '') {

    $_SESSION['error'] = 'Please fill in all required fields.';
    header('Location: register_pwd.php');
    exit;
}

// keep only one subtype
if ($disCat === 'Blind') {
    $deafType = null;
    $physType = null;
} elseif ($disCat === 'Deaf') {
    $blindType = null;
    $physType = null;
} elseif ($disCat === 'Physical Disability') {
    $blindType = null;
    $deafType  = null;
}

// AUTO-GENERATE PWD ID NUMBER
// Format: 06-01-0001-0000001  (Region 06, Province 01, City 0001, serial 7 digits)
$serial = 1;
$res = $conn->query("SELECT COUNT(*) AS cnt FROM pwd_profiles");
if ($res) {
    $row = $res->fetch_assoc();
    $serial = ((int)$row['cnt']) + 1;
    $res->free();
}
$serialStr  = str_pad((string)$serial, 7, '0', STR_PAD_LEFT);
$pwd_number = '06-01-0001-' . $serialStr;

// PASSWORD HASH
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// HANDLE PWD PHOTO UPLOAD
$pwd_photo_front = '';
$pwd_photo_back  = ''; // no back photo now

if (!empty($_FILES['pwd_photo']['name'])) {
    $uploadDir = __DIR__ . '/../uploads/pwd_photos/';
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0777, true);
    }

    $fileTmp  = $_FILES['pwd_photo']['tmp_name'];
    $fileName = $_FILES['pwd_photo']['name'];
    $fileSize = $_FILES['pwd_photo']['size'];
    $fileErr  = $_FILES['pwd_photo']['error'];

    if ($fileErr === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png'])) {
            $_SESSION['error'] = 'Invalid photo format. Use JPG or PNG only.';
            header('Location: register_pwd.php');
            exit;
        }

        // new filename: pwd_number + extension
        $newName = str_replace(['-'], '_', $pwd_number) . '.' . $ext;
        $dest    = $uploadDir . $newName;

        if (!move_uploaded_file($fileTmp, $dest)) {
            $_SESSION['error'] = 'Failed to save PWD photo.';
            header('Location: register_pwd.php');
            exit;
        }

        // store relative path (for display)
        $pwd_photo_front = 'uploads/pwd_photos/' . $newName;
    } else {
        $_SESSION['error'] = 'Error uploading PWD photo.';
        header('Location: register_pwd.php');
        exit;
    }
} else {
    $_SESSION['error'] = 'PWD photo is required.';
    header('Location: register_pwd.php');
    exit;
}

// verification defaults
$is_verified          = '1';
$verification_status  = 'Valid';
$verification_message = 'Registered by admin';
$verified_at          = date('Y-m-d H:i:s');

// INSERT INTO pwd_profiles
$sql = "INSERT INTO pwd_profiles (
            pwd_number,
            password_hash,
            full_name,
            sex,
            birthdate,
            province,
            municipality,
            address,
            contact_number,
            disability_category,
            blind_type,
            deaf_type,
            physical_type,
            cause_of_disability,
            educational_level,
            employment_status,
            guardian_name,
            guardian_relationship,
            guardian_contact,
            pwd_photo_front,
            pwd_photo_back,
            is_verified,
            verification_status,
            verification_message,
            verified_at
        ) VALUES (
            ?,?,?,?,?,?,?,?,?,?,
            ?,?,?,?,?,?,?,?,?,?,
            ?,?,?,?,?
        )";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    $_SESSION['error'] = 'Database error: ' . $conn->error;
    header('Location: register_pwd.php');
    exit;
}

$stmt->bind_param(
    'sssssssssssssssssssssssss',
    $pwd_number,
    $password_hash,
    $full_name,
    $sex,
    $birthdate,
    $province,
    $municipality,
    $address,
    $contact,
    $disCat,
    $blindType,
    $deafType,
    $physType,
    $cause,
    $educ,
    $employment,
    $guardianName,
    $guardianRel,
    $guardianContact,
    $pwd_photo_front,
    $pwd_photo_back,
    $is_verified,
    $verification_status,
    $verification_message,
    $verified_at
);

if (!$stmt->execute()) {
    $_SESSION['error'] = 'Error saving PWD: ' . $stmt->error;
    $stmt->close();
    header('Location: register_pwd.php');
    exit;
}

$stmt->close();

// SAVE SKILLS INTO pwd_skills
if (!empty($skillsSelected)) {
    $sqlSkills = "INSERT INTO pwd_skills (pwd_number, skill_id) VALUES (?, ?)";
    $stmtSkills = $conn->prepare($sqlSkills);

    if ($stmtSkills) {
        foreach ($skillsSelected as $sid) {
            $sid = (int)$sid;
            if ($sid > 0) {
                $stmtSkills->bind_param('si', $pwd_number, $sid);
                $stmtSkills->execute();
            }
        }
        $stmtSkills->close();
    }
}

// success
$_SESSION['success'] = 'PWD registered successfully. Generated ID: ' . $pwd_number;
header('Location: pwd_list.php');
exit;