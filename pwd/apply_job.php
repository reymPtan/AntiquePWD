<?php
// pwd/apply_job.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/notify.php';

requirePwdLogin();

$pwdNumber  = $_SESSION['pwd_number'];
$disability = $_SESSION['disability_category'] ?? '';

$pageTitle = 'Apply Job';

$error   = '';
$success = '';
$job     = null;

/* =====================================================
   1. BLOCK APPLY IF ALREADY EMPLOYED
===================================================== */
$sql = "SELECT employment_status FROM pwd_profiles WHERE pwd_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $pwdNumber);
$stmt->execute();
$res = $stmt->get_result();
$pwdRow = $res->fetch_assoc();
$stmt->close();

if ($pwdRow && $pwdRow['employment_status'] === 'Employed') {
    $error = 'You are currently employed and cannot apply for another job.';
}

/* =====================================================
   2. GET JOB ID
===================================================== */
$jobId = isset($_GET['job_id']) ? (int)$_GET['job_id'] : 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jobId = (int)($_POST['job_id'] ?? 0);
}

if ($jobId <= 0 && !$error) {
    $error = 'Invalid job selected.';
}

/* =====================================================
   3. LOAD JOB DETAILS
===================================================== */
if (!$error) {
    $sql = "
        SELECT j.job_id, j.job_title, j.job_location, j.salary_range,
               j.status, j.disability_target,
               e.business_name
        FROM job_postings j
        JOIN employers e ON j.business_permit_no = e.business_permit_no
        WHERE j.job_id = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $jobId);
    $stmt->execute();
    $res = $stmt->get_result();
    $job = $res->fetch_assoc();
    $stmt->close();

    if (!$job) {
        $error = 'Job not found.';
    } elseif ($job['status'] !== 'Open') {
        $error = 'This job is already closed.';
    } elseif ($job['disability_target'] !== $disability) {
        $error = 'This job is not intended for your disability category.';
    }
}

/* =====================================================
   4. APPLY JOB (POST)
===================================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error && $job) {

    // Prevent duplicate application
    $sql = "SELECT application_id FROM job_applications
            WHERE job_id = ? AND pwd_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('is', $jobId, $pwdNumber);
    $stmt->execute();
    $res = $stmt->get_result();
    $exists = $res->fetch_assoc();
    $stmt->close();

    if ($exists) {
        $error = 'You already applied to this job.';
    } else {

        // ✅ CORRECT COLUMN NAMES
        $sql = "
            INSERT INTO job_applications
            (job_id, pwd_number, date_applied, status)
            VALUES (?, ?, NOW(), 'Pending')
        ";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die('SQL ERROR: ' . $conn->error);
        }

        $stmt->bind_param('is', $jobId, $pwdNumber);

        if ($stmt->execute()) {
            $success = 'Your application has been submitted successfully.';

            // Notify PWD
            notifyPwd(
                $conn,
                $pwdNumber,
                'Application Submitted',
                "You applied for {$job['job_title']} at {$job['business_name']}.",
                '/pwd-employment-system/pwd/my_applications.php'
            );
        } else {
            $error = 'Failed to submit application.';
        }

        $stmt->close();
    }
}

/* =====================================================
   5. LOAD VIEW
===================================================== */
require_once __DIR__ . '/../includes/header.php';

$viewBase = __DIR__ . '/views';

if ($disability === 'Blind') {
    require $viewBase . '/blind/apply_job_view.php';
} elseif ($disability === 'Deaf') {
    require $viewBase . '/deaf/apply_job_view.php';
} else {
    require $viewBase . '/physical/apply_job_view.php';
}

require_once __DIR__ . '/../includes/footer.php';