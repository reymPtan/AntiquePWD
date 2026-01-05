<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
requirePwdLogin();

$disability = $_SESSION['disability_category'] ?? '';
$pwdNumber  = $_SESSION['pwd_number'];

$pageTitle = 'My Applications';
$breadcrumbs = [
    ['label' => 'Home', 'url' => '/pwd-employment-system/index.php'],
];

if ($disability === 'Blind') {
    $breadcrumbs[] = ['label' => 'Blind Dashboard', 'url' => '/pwd-employment-system/pwd/dashboard_blind.php'];
} elseif ($disability === 'Deaf') {
    $breadcrumbs[] = ['label' => 'Deaf Dashboard', 'url' => '/pwd-employment-system/pwd/dashboard_deaf.php'];
} else {
    $breadcrumbs[] = ['label' => 'Physical Dashboard', 'url' => '/pwd-employment-system/pwd/dashboard_physical.php'];
}
$breadcrumbs[] = ['label' => 'My Applications'];

require_once __DIR__ . '/../includes/header.php';

// Load applications
$sql = "SELECT a.application_id,
               a.date_applied,
               a.status,
               a.status_updated_at,
               j.job_title,
               j.job_location,
               e.business_name
        FROM job_applications a
        JOIN job_postings j ON a.job_id = j.job_id
        JOIN employers e ON j.business_permit_no = e.business_permit_no
        WHERE a.pwd_number = ?
        ORDER BY a.date_applied DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $pwdNumber);
$stmt->execute();
$res = $stmt->get_result();
$applications = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$viewBase = __DIR__ . '/views';
if ($disability === 'Blind') {
    require $viewBase . '/blind/my_applications_view.php';
} elseif ($disability === 'Deaf') {
    require $viewBase . '/deaf/my_applications_view.php';
} else {
    require $viewBase . '/physical/my_applications_view.php';
}

require_once __DIR__ . '/../includes/footer.php';