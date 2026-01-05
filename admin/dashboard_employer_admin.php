<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireAdminRole(['EMPLOYER_ADMIN','SUPER']);

$pageTitle = 'Employer Admin Dashboard';
$_SESSION['role_label'] = 'Employer Administrator';

require_once __DIR__ . '/../includes/header.php';
?>

<!-- =========================
     DASHBOARD HEADER
========================== -->
<section class="card dashboard-card">

    <header class="page-header">
        <h1 class="page-title">Employer Admin Dashboard</h1>
        <p class="page-subtitle">
            Manage employer registration, job postings, and monitoring
        </p>
    </header>

    <!-- =========================
         ACTION BUTTONS GRID
    ========================== -->
    <div class="nav-grid mt-2">

        <a href="register_employer.php" class="nav-btn">
            <span class="nav-btn-title">Register Employer</span>
            <span class="nav-btn-desc">Add and approve employer accounts</span>
        </a>

        <a href="reports.php" class="nav-btn">
            <span class="nav-btn-title">Employer & Job Reports</span>
            <span class="nav-btn-desc">View job postings and employer statistics</span>
        </a>

    </div>
</section>

<!-- =========================
     QUICK SUMMARY
========================== -->
<section class="card mt-3">

    <h2 class="section-title">Quick Overview</h2>

    <?php
    $totalEmployers = $conn->query("SELECT COUNT(*) c FROM employers")->fetch_assoc()['c'] ?? 0;
    $totalJobs = $conn->query("SELECT COUNT(*) c FROM job_postings")->fetch_assoc()['c'] ?? 0;
    ?>

    <div class="dashboard-grid mt-2">

        <div class="card card-compact">
            <h3>Total Employers</h3>
            <p class="stat-number"><?= $totalEmployers ?></p>
        </div>

        <div class="card card-compact">
            <h3>Total Job Posts</h3>
            <p class="stat-number"><?= $totalJobs ?></p>
        </div>

    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>