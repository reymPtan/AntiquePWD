
<?php
// admin/register_employer_save.php
// Auto-generate business_permit_no + save employer + upload employer picture

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireAdminRole(['SUPER', 'EMPLOYER_ADMIN']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register_employer.php');
    exit;
}

$business_name    = trim($_POST['business_name'] ?? '');
$registered_owner = trim($_POST['registered_owner'] ?? '');
$city             = trim($_POST['city_municipality'] ?? '');
$province         = trim($_POST['province'] ?? '');
$barangay         = trim($_POST['barangay'] ?? '');
$business_address = trim($_POST['business_address'] ?? '');
$zip_code         = trim($_POST['zip_code'] ?? '');
$type_of_business = trim($_POST['type_of_business'] ?? '');
$line_of_business = trim($_POST['line_of_business'] ?? '');
$valid_until      = $_POST['valid_until'] ?? '';
$password         = $_POST['password'] ?? 'employer123';

if ($business_name === '' || $registered_owner === '' ||
    $city === '' || $province === '' ||
    $business_address === '' || $type_of_business === '' ||
    $line_of_business === '' || $valid_until === '') {

    $_SESSION['error'] = 'Please complete all required fields.';
    header('Location: register_employer.php');
    exit;
}

// ✅ AUTO-GENERATE BUSINESS PERMIT NO.
// Format: YYYY-MUN-XXXXXX
$year = date('Y');

// 3-letter MUN code from city (letters only, first 3, uppercase)
$munCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $city), 0, 3));
if ($munCode === '') {
    $munCode = 'ANT';
}

// Count existing employers for this year + mun code -> next serial
$sqlCount = "SELECT COUNT(*) AS cnt
             FROM employers
             WHERE business_permit_no LIKE CONCAT(?, '-', ?, '-%')";

$stmt = $conn->prepare($sqlCount);
if (!$stmt) {
    die('Prepare count error: ' . $conn->error);
}
$stmt->bind_param('ss', $year, $munCode);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

$serial = ((int)$row['cnt']) + 1;
$serialStr = str_pad((string)$serial, 6, '0', STR_PAD_LEFT);

$business_permit_no = $year . '-' . $munCode . '-' . $serialStr;

// password hash
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// date issued = today
$date_issued = date('Y-m-d');

// ✅ HANDLE EMPLOYER PHOTO UPLOAD
$employer_photo = '';

if (!empty($_FILES['employer_photo']['name'])) {
    $uploadDir = __DIR__ . '/../uploads/employer_photos/';
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
            die('Failed to create upload directory: ' . $uploadDir);
        }
    }

    $fileTmp  = $_FILES['employer_photo']['tmp_name'];
    $fileName = $_FILES['employer_photo']['name'];
    $fileErr  = $_FILES['employer_photo']['error'];

    if ($fileErr === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png'])) {
            $_SESSION['error'] = 'Invalid employer picture format. Use JPG or PNG only.';
            header('Location: register_employer.php');
            exit;
        }

        // filename: permit_no + extension, ex: 2025_SJB_000001.jpg
        $safeName = str_replace(['-'], '_', $business_permit_no) . '.' . $ext;
        $dest     = $uploadDir . $safeName;

        if (!move_uploaded_file($fileTmp, $dest)) {
            die('Failed to move uploaded file to ' . $dest);
        }

        // store relative path (for using in <img src>)
        $employer_photo = 'uploads/employer_photos/' . $safeName;
    } else {
        die('Upload error code: ' . $fileErr);
    }
} else {
    $_SESSION['error'] = 'Employer picture is required.';
    header('Location: register_employer.php');
    exit;
}

// ✅ INSERT EMPLOYER
$sql = "INSERT INTO employers (
            business_permit_no,
            password_hash,
            date_issued,
            valid_until,
            business_name,
            registered_owner,
            business_address,
            barangay,
            city_municipality,
            province,
            zip_code,
            type_of_business,
            line_of_business,
            employer_photo
        ) VALUES (
            ?,?,?,?,?,?,
            ?,?,?,?,?,?,
            ?,?
        )";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('Prepare insert error: ' . $conn->error);
}

$stmt->bind_param(
    'ssssssssssssss',
    $business_permit_no,
    $password_hash,
    $date_issued,
    $valid_until,
    $business_name,
    $registered_owner,
    $business_address,
    $barangay,
    $city,
    $province,
    $zip_code,
    $type_of_business,
    $line_of_business,
    $employer_photo
);

if (!$stmt->execute()) {
    die('Execute insert error: ' . $stmt->error);
}

$stmt->close();

$_SESSION['success'] = 'Employer registered. Generated Permit No: ' . $business_permit_no;
header('Location: register_employer.php');
exit;