<?php
// pwd/notifications.php
// PWD notifications page (Blind / Deaf / Physical)

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requirePwdLogin();

$pwdNumber  = $_SESSION['pwd_number'];
$disability = $_SESSION['disability_category'] ?? 'Blind';

$pageTitle = 'Notifications';
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
$breadcrumbs[] = ['label' => 'Notifications'];

require_once __DIR__ . '/../includes/header.php';

// Load notifications
$sql = "SELECT title, message, link, is_read, created_at
        FROM notifications
        WHERE user_type = 'PWD'
          AND pwd_number = ?
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo '<p class="alert alert-error">SQL error (notifications SELECT): '
        . htmlspecialchars($conn->error) . '</p>';
    $notifications = [];
} else {
    $stmt->bind_param("s", $pwdNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    $notifications = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Mark as read
$mark = $conn->prepare("
    UPDATE notifications
    SET is_read = 1
    WHERE user_type = 'PWD'
      AND pwd_number = ?
");
if ($mark) {
    $mark->bind_param("s", $pwdNumber);
    $mark->execute();
    $mark->close();
}

// Load view
$viewBase = __DIR__ . '/views';

if ($disability === 'Blind') {
    require $viewBase . '/blind/notifications_view.php';
} elseif ($disability === 'Deaf') {
    require $viewBase . '/deaf/notifications_view.php';
} else {
    require $viewBase . '/physical/notifications_view.php';
}

require_once __DIR__ . '/../includes/footer.php';