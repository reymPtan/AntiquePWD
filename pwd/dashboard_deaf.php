<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
requirePwdLogin();

$_SESSION['role_label'] = 'PWD – Deaf';

$pageTitle = 'Deaf Dashboard';
$breadcrumbs = [
    ['label' => 'Home', 'url' => '/pwd-employment-system/index.php'],
    ['label' => 'Deaf Dashboard'],
];

require_once __DIR__ . '/../includes/header.php';
?>
<section class="card">
    <h1 class="page-title">Deaf PWD Dashboard</h1>
    <p class="page-subtitle">
        Visual shortcuts to your main pages.
    </p>

    <div class="icon-grid">
        <a href="view_jobs.php" class="icon-btn">
            <div class="icon">💼</div>
            <div class="nav-btn-title">Jobs Available</div>
            <div class="nav-btn-desc">See matching job offers.</div>
        </a>
        <a href="my_applications.php" class="icon-btn">
            <div class="icon">📄</div>
            <div class="nav-btn-title">My Applications</div>
            <div class="nav-btn-desc">Track application status.</div>
        </a>
        <a href="notifications.php" class="icon-btn">
            <div class="icon">🔔</div>
            <div class="nav-btn-title">Notifications</div>
            <div class="nav-btn-desc">Important updates.</div>
        </a>
        <a href="profile.php" class="icon-btn">
            <div class="icon">👤</div>
            <div class="nav-btn-title">My Profile</div>
            <div class="nav-btn-desc">View your information.</div>
        </a>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>