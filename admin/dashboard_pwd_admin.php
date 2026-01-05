<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireAdminRole(['PWD_ADMIN','SUPER']);

$pageTitle = 'PWD Admin Dashboard';
$_SESSION['role_label'] = 'PWD Administrator';

require_once __DIR__ . '/../includes/header.php';
?>

<!-- =========================
     DASHBOARD HEADER
========================== -->
<section class="card dashboard-card">

    <header class="page-header">
        <h1 class="page-title">PWD Admin Dashboard</h1>
        <p class="page-subtitle">
        </p>
    </header>

    <!-- =========================
         ACTION BUTTONS GRID
    ========================== -->
    <div class="nav-grid mt-2">

        <a href="register_pwd.php" class="nav-btn">
            <span class="nav-btn-title">Register PWD</span>
            <span class="nav-btn-desc">Create new PWD profiles</span>
        </a>

        <a href="pwd_list.php" class="nav-btn">
            <span class="nav-btn-title">PWD List</span>
            <span class="nav-btn-desc">View all registered PWDs</span>
        </a>

        <a href="reports.php" class="nav-btn">
            <span class="nav-btn-title">PWD Reports</span>
            <span class="nav-btn-desc">Generate PWD statistics</span>
        </a>

    </div>
</section>

<!-- =========================
     QUICK SUMMARY (OPTIONAL)
========================== -->
<section class="card mt-3">

    <h2 class="section-title">Quick Overview</h2>

    <?php
    $totalPWD = $conn->query("SELECT COUNT(*) c FROM pwd_profiles")->fetch_assoc()['c'] ?? 0;
    ?>

    <div class="dashboard-grid mt-2">
        <div class="card card-compact">
            <h3>Total Registered PWDs</h3>
            <p class="stat-number"><?= $totalPWD ?></p>
        </div>
    </div>

</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>