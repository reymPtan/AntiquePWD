<?php
// employer/post_job_save.php
// Saves a new job post + required skills + sends notifications to matching PWDs

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireEmployerLogin();

$permitNo = $_SESSION['business_permit_no'] ?? '';
if (!$permitNo) {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: post_job.php');
    exit;
}

// Get inputs
$role_id            = (int)($_POST['role_id'] ?? 0);
$disability_target  = $_POST['disability_target'] ?? '';
$job_title_input    = trim($_POST['job_title'] ?? '');
$job_description    = trim($_POST['job_description'] ?? '');
$job_location       = trim($_POST['job_location'] ?? '');
$salary_range       = trim($_POST['salary_range'] ?? '');
$max_hires          = (int)($_POST['max_hires'] ?? 1);
$application_deadline = $_POST['application_deadline'] ?? '';
$skillsSelected     = $_POST['skills'] ?? [];

// Simple validation
$errors = [];

if ($role_id <= 0) {
    $errors[] = 'Job role is required.';
}
if ($disability_target === '') {
    $errors[] = 'Disability target is required.';
}
if ($job_description === '') {
    $errors[] = 'Job description is required.';
}
if ($job_location === '') {
    $errors[] = 'Job location is required.';
}
if ($max_hires <= 0) {
    $errors[] = 'Number of PWDs to hire must be at least 1.';
}

if (!empty($errors)) {
    require_once __DIR__ . '/../includes/header.php';
    echo '<section class="card">';
    echo '<h1>Post Job Error</h1>';
    echo '<div class="alert alert-error"><ul>';
    foreach ($errors as $e) {
        echo '<li>' . htmlspecialchars($e) . '</li>';
    }
    echo '</ul></div>';
    echo '<a href="post_job.php" class="btn secondary-btn">← Back to Post Job</a>';
    echo '</section>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Get role name for default title
$roleName = '';
$stmtRole = $conn->prepare("SELECT role_name FROM job_roles WHERE role_id = ?");
if ($stmtRole) {
    $stmtRole->bind_param('i', $role_id);
    $stmtRole->execute();
    $res = $stmtRole->get_result();
    if ($row = $res->fetch_assoc()) {
        $roleName = $row['role_name'];
    }
    $stmtRole->close();
}

$job_title = $job_title_input !== '' ? $job_title_input : $roleName;

// Normalize deadline
$deadline = null;
if (!empty($application_deadline)) {
    $deadline = $application_deadline;
}

// Start transaction
$conn->begin_transaction();

try {
    // Insert job
    $sqlJob = "INSERT INTO job_postings (
                    business_permit_no,
                    role_id,
                    disability_target,
                    job_title,
                    job_description,
                    job_location,
                    salary_range,
                    max_hires,
                    hired_count,
                    status,
                    application_deadline,
                    date_posted
               ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, 0, 'Open', ?, NOW()
               )";

       $stmtJob = $conn->prepare($sqlJob);
    if (!$stmtJob) {
        throw new Exception('Prepare job failed: ' . $conn->error);
    }

    // Types: s = string, i = integer
    $stmtJob->bind_param(
        'sisssssis',
        $permitNo,          // s
        $role_id,           // i
        $disability_target, // s
        $job_title,         // s
        $job_description,   // s
        $job_location,      // s
        $salary_range,      // s
        $max_hires,         // i
        $deadline           // s (can be null/empty)
    );
    
    if (!$stmtJob->execute()) {
        throw new Exception('Insert job failed: ' . $stmtJob->error);
    }

    $job_id = $stmtJob->insert_id;
    $stmtJob->close();

    // Insert required skills
    $jobSkillIds = [];
    if (is_array($skillsSelected) && !empty($skillsSelected)) {
        $sqlJS = "INSERT INTO job_required_skills (job_id, skill_id, is_required) VALUES (?, ?, 1)";
        $stmtJS = $conn->prepare($sqlJS);
        if (!$stmtJS) {
            throw new Exception('Prepare job skills failed: ' . $conn->error);
        }

        foreach ($skillsSelected as $sid) {
            $skillId = (int)$sid;
            if ($skillId <= 0) continue;
            $stmtJS->bind_param('ii', $job_id, $skillId);
            if (!$stmtJS->execute()) {
                throw new Exception('Insert job skill failed: ' . $stmtJS->error);
            }
            $jobSkillIds[] = $skillId;
        }

        $stmtJS->close();
    }

    // SMART NOTIFICATION: same disability + same municipality + skill match
    $sqlPwd = "SELECT p.pwd_number, p.full_name, p.municipality
               FROM pwd_profiles p
               WHERE p.disability_category = ?
                 AND p.municipality = ?";

    $stmtPwd = $conn->prepare($sqlPwd);
    if (!$stmtPwd) {
        throw new Exception('Prepare PWD query failed: ' . $conn->error);
    }

    $stmtPwd->bind_param('ss', $disability_target, $job_location);
    $stmtPwd->execute();
    $resPwd = $stmtPwd->get_result();

    $requireSkills = $jobSkillIds;

    while ($pwd = $resPwd->fetch_assoc()) {
        $pwdNum = $pwd['pwd_number'];

        $hasMatch = true;
        if (!empty($requireSkills)) {
            $hasMatch = false;

            $sqlPS = "SELECT skill_id FROM pwd_skills WHERE pwd_number = ?";
            $stmtPS = $conn->prepare($sqlPS);
            if ($stmtPS) {
                $stmtPS->bind_param('s', $pwdNum);
                $stmtPS->execute();
                $resPS = $stmtPS->get_result();
                $pwdSkillIds = [];
                while ($rowPS = $resPS->fetch_assoc()) {
                    $pwdSkillIds[] = (int)$rowPS['skill_id'];
                }
                $stmtPS->close();

                foreach ($pwdSkillIds as $psid) {
                    if (in_array($psid, $requireSkills, true)) {
                        $hasMatch = true;
                        break;
                    }
                }
            }
        }

        if ($hasMatch) {
            $title   = 'New Nearby Job Match';
            $message = "New job posted: '{$job_title}' in {$job_location}. You may review and apply if interested.";
            $link    = '/pwd-employment-system/pwd/view_jobs.php';

            $sqlNotif = "INSERT INTO notifications
                         (user_type, pwd_number, title, message, link, is_read, created_at)
                         VALUES ('PWD', ?, ?, ?, ?, 0, NOW())";
            $stmtN = $conn->prepare($sqlNotif);
            if ($stmtN) {
                $stmtN->bind_param('ssss', $pwdNum, $title, $message, $link);
                $stmtN->execute();
                $stmtN->close();
            }
        }
    }

    $stmtPwd->close();

    $conn->commit();

    $_SESSION['success'] = 'Job posted successfully.';
    header('Location: my_jobs.php');
    exit;

} catch (Exception $e) {
    $conn->rollback();
    require_once __DIR__ . '/../includes/header.php';
    echo '<section class="card">';
    echo '<h1>Post Job Error</h1>';
    echo '<div class="alert alert-error">';
    echo 'Error: ' . htmlspecialchars($e->getMessage());
    echo '</div>';
    echo '<a href="post_job.php" class="btn secondary-btn">← Back to Post Job</a>';
    echo '</section>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}