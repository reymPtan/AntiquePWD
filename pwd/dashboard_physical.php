<?php
// pwd/dashboard_physical.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requirePwdLogin();

$pwd_number = $_SESSION['pwd_number'] ?? '';
$pageTitle  = 'Physical Disability Dashboard';

$pwd = null;

if ($pwd_number) {
    $sql = "SELECT 
                pwd_number,
                full_name,
                disability_category,
                municipality,
                province,
                pwd_photo_front
            FROM pwd_profiles
            WHERE pwd_number = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $pwd_number);
    $stmt->execute();
    $res = $stmt->get_result();
    $pwd = $res->fetch_assoc();
    $stmt->close();
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="card physical-dashboard-card">
    <header class="page-header">
        <div class="pwd-header">
            <div class="pwd-photo-wrapper">
                <?php if (!empty($pwd['pwd_photo_front'])): ?>
                    <img src="/pwd-employment-system/<?= htmlspecialchars($pwd['pwd_photo_front']) ?>"
                         alt="PWD picture"
                         class="pwd-photo">
                <?php else: ?>
                    <div class="pwd-photo placeholder-photo">
                        PWD
                    </div>
                <?php endif; ?>
            </div>

            <div class="pwd-header-text">
                <h1 class="page-title">
                    <?= htmlspecialchars($pwd['full_name'] ?? 'PWD User') ?>
                </h1>
                <p class="page-subtitle">
                    PWD ID: <strong><?= htmlspecialchars($pwd['pwd_number'] ?? '') ?></strong><br>
                    Disability: <strong><?= htmlspecialchars($pwd['disability_category'] ?? 'Physical Disability') ?></strong><br>
                    <?= htmlspecialchars($pwd['municipality'] ?? '') ?>,
                    <?= htmlspecialchars($pwd['province'] ?? 'Antique') ?>
                </p>
            </div>
        </div>
    </header>

    <section class="dashboard-actions">
        <h2 class="section-title">Main Menu</h2>
        <div class="button-grid">
            <a href="view_jobs.php" class="btn primary-btn">View Jobs</a>
            <a href="my_applications.php" class="btn secondary-btn">My Applications</a>
            <a href="profile.php" class="btn secondary-btn">My Profile</a>
            <a href="print_id.php" class="btn tertiary-btn">Print PWD ID</a>
            <a href="../notifications.php" class="btn tertiary-btn">Notifications</a>
        </div>
    </section>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>