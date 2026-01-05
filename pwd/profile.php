<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requirePwdLogin();

$pwdNumber = $_SESSION['pwd_number'];

$pageTitle = 'My Profile';

/* =====================================================
   ALWAYS LOAD FROM DATABASE (SOURCE OF TRUTH)
   ===================================================== */
$sql = "
SELECT
    pwd_number,
    full_name,
    municipality,
    disability_category,
    blind_type,
    deaf_type,
    physical_type,
    cause_of_disability,
    educational_level,
    employment_status
FROM pwd_profiles
WHERE pwd_number = ?
LIMIT 1
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $pwdNumber);
$stmt->execute();
$pwd = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$pwd) {
    exit('Profile not found.');
}

/* OPTIONAL: sync session */
$_SESSION['employment_status'] = $pwd['employment_status'];

require_once __DIR__ . '/../includes/header.php';

$viewBase = __DIR__ . '/views';
if ($pwd['disability_category'] === 'Blind') {
    require $viewBase . '/blind/profile_view.php';
} elseif ($pwd['disability_category'] === 'Deaf') {
    require $viewBase . '/deaf/profile_view.php';
} else {
    require $viewBase . '/physical/profile_view.php';
}

require_once __DIR__ . '/../includes/footer.php';