<?php
// employer/dashboard.php
// Employer dashboard – clean, modern, no redundant dashboard button

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireEmployerLogin();

$permitNo  = $_SESSION['business_permit_no'] ?? '';
$pageTitle = 'Employer Dashboard';

$employer = null;

if ($permitNo) {
    $sql = "SELECT
                business_name,
                employer_photo,
                city_municipality,
                province,
                type_of_business,
                line_of_business,
                date_issued,
                valid_until
            FROM employers
            WHERE business_permit_no = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $permitNo);
    $stmt->execute();
    $res = $stmt->get_result();
    $employer = $res->fetch_assoc();
    $stmt->close();
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="card employer-dashboard-card">

    <!-- ================= HEADER ================= -->
    <header class="page-header employer-header">

        <!-- LOGO (SMALL + AESTHETIC) -->
        <div class="employer-logo-wrapper">
            <?php if (!empty($employer['employer_photo'])): ?>
                <img src="/pwd-employment-system/<?= htmlspecialchars($employer['employer_photo']) ?>"
                     alt="Employer logo"
                     class="employer-logo-small">
            <?php else: ?>
                <div class="employer-logo-small placeholder-logo">
                    LOGO
                </div>
            <?php endif; ?>
        </div>

        <!-- COMPANY INFO -->
        <div class="employer-header-text">
            <h1 class="page-title">
                <?= htmlspecialchars($employer['business_name'] ?? 'My Company') ?>
            </h1>

            <p class="page-subtitle">
                Permit No: <strong><?= htmlspecialchars($permitNo) ?></strong><br>
                <?= htmlspecialchars($employer['city_municipality'] ?? '') ?>,
                <?= htmlspecialchars($employer['province'] ?? '') ?>
            </p>

            <?php if (!empty($employer['type_of_business'])): ?>
                <p class="small-note">
                    <?= htmlspecialchars($employer['type_of_business']) ?>
                    <?php if (!empty($employer['line_of_business'])): ?>
                        – <?= htmlspecialchars($employer['line_of_business']) ?>
                    <?php endif; ?>
                </p>
            <?php endif; ?>

            <?php if (!empty($employer['date_issued']) && !empty($employer['valid_until'])): ?>
                <p class="small-note">
                    Valid: <?= htmlspecialchars($employer['date_issued']) ?>
                    to <?= htmlspecialchars($employer['valid_until']) ?>
                </p>
            <?php endif; ?>
        </div>

    </header>

    <!-- ================= ACTIONS ================= -->
    <section class="dashboard-actions">
        <h2 class="section-title">Employer Actions</h2>

        <div class="button-grid">
            <a href="post_job.php" class="btn primary-btn">
                ➕ Post a Job
            </a>

            <a href="my_jobs.php" class="btn secondary-btn">
                📄 My Job Posts
            </a>

            <a href="view_applicants_all.php" class="btn secondary-btn">
                👥 Applicants
            </a>

            <a href="notifications.php" class="btn tertiary-btn">
                🔔 Notifications
            </a>
        </div>
    </section>

</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>