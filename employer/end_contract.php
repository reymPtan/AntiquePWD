<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/notify.php';

notifyPwdEndContract($conn, $pwdNumber, $jobTitle);
requireEmployerLogin();

$permitNo = $_SESSION['business_permit_no'];

$applicationId = (int)($_POST['application_id'] ?? 0);
if ($applicationId <= 0) die('Invalid request');

/* 1️⃣ GET APPLICATION + JOB */
$sql = "SELECT 
            a.application_id,
            a.pwd_number,
            a.job_id,
            j.max_hires,
            j.hired_count,
            j.business_permit_no
        FROM job_applications a
        JOIN job_postings j ON j.job_id = a.job_id
        WHERE a.application_id = ? AND a.status='Accepted'";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $applicationId);
$stmt->execute();
$app = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$app) die('Application not found');

if ($app['business_permit_no'] !== $permitNo) {
    die('Unauthorized');
}

$pwdNumber = $app['pwd_number'];
$jobId     = $app['job_id'];

// 🔔 Notify PWD – Contract Ended
notifyPwd(
    $conn,
    $pwdNumber,
    'Contract Ended',
    'Your employment has ended. You may now apply for new jobs.',
    '/pwd/view_jobs.php'
);

// 🔔 Notify Employer
notifyEmployer(
    $conn,
    $_SESSION['employer_id'],
    'Contract Ended',
    'The PWD employment contract has been closed.',
    '/employer/dashboard.php'
);

/* 2️⃣ UPDATE APPLICATION */
$conn->query("UPDATE job_applications 
              SET status='Ended', status_updated_at=NOW()
              WHERE application_id=$applicationId");

/* 3️⃣ UPDATE PWD PROFILE → UNEMPLOYED */
$conn->query("UPDATE pwd_profiles
              SET employment_status='Unemployed'
              WHERE pwd_number='$pwdNumber'");

/* 4️⃣ ADD EMPLOYMENT HISTORY */
$conn->query("INSERT INTO employment_history
              (pwd_number, job_id, start_date, end_date, remarks)
              VALUES ('$pwdNumber','$jobId',NOW(),NOW(),'Contract Ended')");

/* 5️⃣ UPDATE JOB POSTING */
$newCount = max(0, $app['hired_count'] - 1);

$conn->query("UPDATE job_postings
              SET hired_count=$newCount, status='Open'
              WHERE job_id=$jobId");

/* 6️⃣ REDIRECT */
header("Location: view_applicants.php?job_id=$jobId");
exit;