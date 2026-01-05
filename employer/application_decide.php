<?php
// employer/application_decide.php
// FINAL VERSION – updates application, job, and PWD employment status

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/notify.php';

requireEmployerLogin();

$permitNo = $_SESSION['business_permit_no'] ?? '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: my_jobs.php');
    exit;
}

$applicationId = (int)($_POST['application_id'] ?? 0);
$action        = $_POST['action'] ?? '';

if ($applicationId <= 0 || !in_array($action, ['accept', 'reject'], true)) {
    exit('Invalid request.');
}

/* =====================================================
   1️⃣ LOAD APPLICATION + JOB + VERIFY OWNERSHIP
   ===================================================== */
$sql = "
SELECT
    a.application_id,
    a.job_id,
    a.pwd_number,
    a.status AS application_status,
    j.job_title,
    j.business_permit_no,
    j.max_hires,
    j.hired_count,
    j.status AS job_status
FROM job_applications a
JOIN job_postings j ON a.job_id = j.job_id
WHERE a.application_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $applicationId);
$stmt->execute();
$app = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$app) {
    exit('Application not found.');
}

if ($app['business_permit_no'] !== $permitNo) {
    exit('Unauthorized action.');
}

if ($app['job_status'] !== 'Open') {
    exit('Job is already closed.');
}

$pwdNumber  = $app['pwd_number'];
$jobId      = (int)$app['job_id'];
$jobTitle   = $app['job_title'];
$maxHires   = (int)$app['max_hires'];
$hiredCount = (int)$app['hired_count'];

/* =====================================================
   2️⃣ ACCEPT APPLICATION
   ===================================================== */
if ($action === 'accept') {

   // ===============================
// AFTER SUCCESSFUL ACCEPT
// ===============================

// 🔔 Notify PWD
notifyPwd(
    $conn,
    $pwdNumber,
    'Job Application Accepted',
    'Congratulations! You are now EMPLOYED. This job has been assigned to you.',
    '/pwd/profile.php'
);

// 🔔 Notify Employer (optional)
notifyEmployer(
    $conn,
    $_SESSION['employer_id'],
    'PWD Hired',
    'You have successfully hired a PWD for this job.',
    '/employer/my_jobs.php'
);

    // 2.4 Notify PWD
    notifyPwd(
        $conn,
        $pwdNumber,
        'Job Application Accepted',
        "Congratulations! Your application for '{$jobTitle}' has been ACCEPTED.",
        '/pwd-employment-system/pwd/profile.php'
    );
}

/* =====================================================
   3️⃣ REJECT APPLICATION
   ===================================================== */
if ($action === 'reject') {

    $sql = "
        UPDATE job_applications
        SET status = 'Rejected',
            status_updated_at = NOW()
        WHERE application_id = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $applicationId);
    $stmt->execute();
    $stmt->close();

    notifyPwd(
        $conn,
        $pwdNumber,
        'Job Application Result',
        "Your application for '{$jobTitle}' was not selected.",
        '/pwd-employment-system/pwd/my_applications.php'
    );
}

/* =====================================================
   4️⃣ REDIRECT BACK
   ===================================================== */
header('Location: view_applicants.php?job_id=' . $jobId);
exit;