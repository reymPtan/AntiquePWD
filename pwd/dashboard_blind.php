<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
requirePwdLogin();

/* 🔒 Extra safety: lock to Blind only */
if (($_SESSION['disability_category'] ?? '') !== 'Blind') {
    http_response_code(403);
    exit('Access denied');
}

$_SESSION['role_label'] = 'PWD – Blind';

$pageTitle = 'Blind Dashboard';
$breadcrumbs = [
    ['label' => 'Home', 'url' => '/pwd-employment-system/index.php'],
    ['label' => 'Blind Dashboard'],
];

require_once __DIR__ . '/../includes/header.php';

/* 🔊 ADD THIS: Blind Text-to-Speech (GLOBAL FOR THIS PAGE) */
require_once __DIR__ . '/../includes/blind_tts.php';
?>

<section class="card" tabindex="0" aria-label="Blind PWD Dashboard">
    <h1 class="page-title" aria-label="Blind PWD Dashboard">
        Blind PWD Dashboard
    </h1>

    <p tabindex="0" aria-label="Navigation instructions">
        Use the Tab key to move between buttons. Press Enter to activate.
    </p>

    <div class="blind-nav" role="navigation" aria-label="Blind dashboard navigation">

        <a href="view_jobs.php"
           class="blind-btn"
           tabindex="0"
           aria-label="View available jobs">
            Jobs Available
        </a>

        <a href="my_applications.php"
           class="blind-btn"
           tabindex="0"
           aria-label="View my job applications">
            My Applications
        </a>

        <a href="notifications.php"
           class="blind-btn"
           tabindex="0"
           aria-label="View notifications">
            Notifications
        </a>

        <a href="profile.php"
           class="blind-btn"
           tabindex="0"
           aria-label="View my profile">
            My Profile
        </a>

    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>