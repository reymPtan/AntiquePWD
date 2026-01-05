<?php
// employer/accept_application.php
// Accept a PWD application and update ALL related tables

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireEmployerLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Invalid request');
}

$applicationId = (int)($_POST['application_id'] ?? 0);

if ($applicationId <= 0) {
    exit('Invalid application');
}

/* =====================================================
   1️⃣ GET APPLICATION DETAILS
   ===================================================== */
$sql = "
SELECT 
    a.application_id,
    a.pwd_number,
    a.job_id,
    j.business_permit_no
FROM job_applications a
JOIN job_postings j ON a.job_id = j.job_id
WHERE a.application_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $applicationId);
$stmt->execute();
$res = $stmt->get_result();
$app = $res->fetch_assoc();
$stmt->close();

if (!$app) {
    exit('Application not found');
}

$pwdNumber = $app['pwd_number'];
$jobId     = (int)$app['job_id'];

/* =====================================================
   2️⃣ START TRANSACTION (IMPORTANT)
   ===================================================== */
$conn->begin_transaction();

try {

    /* ===============================================
       STEP 1: ACCEPT APPLICATION
       =============================================== */
    $sql = "UPDATE job_applications
            SET application_status = 'Accepted'
            WHERE application_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $applicationId);
    $stmt->execute();
    $stmt->close();

    /* ===============================================
       STEP 2: CLOSE JOB + SET HIRED PWD
       =============================================== */
    $sql = "UPDATE job_postings
            SET status = 'Closed',
                hired_pwd_id = ?
            WHERE job_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $pwdNumber, $jobId);
    $stmt->execute();
    $stmt->close();

    /* ===============================================
       STEP 3: MARK PWD AS EMPLOYED ✅
       =============================================== */
    $sql = "UPDATE pwd_profiles
            SET employment_status = 'Employed'
            WHERE pwd_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $pwdNumber);
    $stmt->execute();
    $stmt->close();

    /* ===============================================
       STEP 4: AUTO-REJECT OTHER PENDING APPLICATIONS
       =============================================== */
    $sql = "UPDATE job_applications
            SET application_status = 'Rejected'
            WHERE job_id = ?
              AND application_status = 'Pending'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $jobId);
    $stmt->execute();
    $stmt->close();

    /* ===============================================
       COMMIT ALL CHANGES
       =============================================== */
    $conn->commit();

    // Redirect back to employer dashboard
    header('Location: dashboard.php?accepted=1');
    exit;

} catch (Exception $e) {

    // Rollback if something fails
    $conn->rollback();
    exit('Error accepting application');

}