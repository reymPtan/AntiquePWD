<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireAdminRole(['SUPER']);

$pageTitle = 'Super Admin Dashboard';
$_SESSION['role_label'] = 'Super Admin';

/* ===============================
   SUMMARY COUNTS
   =============================== */
$totalPWD = $conn->query("SELECT COUNT(*) c FROM pwd_profiles")->fetch_assoc()['c'] ?? 0;
$totalEmployer = $conn->query("SELECT COUNT(*) c FROM employers")->fetch_assoc()['c'] ?? 0;
$totalJobs = $conn->query("SELECT COUNT(*) c FROM job_postings")->fetch_assoc()['c'] ?? 0;
$totalApplications = $conn->query("SELECT COUNT(*) c FROM job_applications")->fetch_assoc()['c'] ?? 0;

/* ===============================
   EMPLOYMENT STATUS
   =============================== */
$empLabels = [];
$empTotals = [];
$res = $conn->query("
    SELECT employment_status, COUNT(*) c
    FROM pwd_profiles
    GROUP BY employment_status
");
while ($row = $res->fetch_assoc()) {
    $empLabels[] = $row['employment_status'];
    $empTotals[] = (int)$row['c'];
}

/* ===============================
   PWD PER MUNICIPALITY
   =============================== */
$pwdMunLabels = [];
$pwdMunTotals = [];
$res = $conn->query("
    SELECT municipality, COUNT(*) total
    FROM pwd_profiles
    GROUP BY municipality
    ORDER BY municipality
");
while ($row = $res->fetch_assoc()) {
    $pwdMunLabels[] = $row['municipality'];
    $pwdMunTotals[] = (int)$row['total'];
}

/* ===============================
   JOBS PER MUNICIPALITY
   =============================== */
$jobMunLabels = [];
$jobMunTotals = [];
$res = $conn->query("
    SELECT job_location AS municipality, COUNT(*) total
    FROM job_postings
    GROUP BY job_location
    ORDER BY job_location
");
while ($row = $res->fetch_assoc()) {
    $jobMunLabels[] = $row['municipality'];
    $jobMunTotals[] = (int)$row['total'];
}

/* ===============================
   PWD BY DISABILITY (WHOLE ANTIQUE)
   =============================== */
$disLabels = [];
$disTotals = [];
$res = $conn->query("
    SELECT disability_category, COUNT(*) total
    FROM pwd_profiles
    GROUP BY disability_category
");
while ($row = $res->fetch_assoc()) {
    $disLabels[] = $row['disability_category'];
    $disTotals[] = (int)$row['total'];
}

require_once __DIR__ . '/../includes/header.php';
?>

<style>
.dashboard-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px}
.card{background:#fff;padding:20px;border-radius:10px;box-shadow:0 4px 8px rgba(0,0,0,.08)}
.chart-box{margin-top:40px}
.nav-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px}
.nav-btn{display:block;padding:16px;border-radius:10px;background:#f9fafb;
    text-decoration:none;color:#111827;border:1px solid #e5e7eb}
.nav-btn:hover{background:#eef2ff}
.btn-logout{background:#dc2626;color:#fff;border:none;padding:8px 14px;border-radius:6px}
.btn-download{display:inline-block;margin-top:10px;background:#16a34a;color:#fff;
    padding:8px 14px;border-radius:6px;text-decoration:none}
</style>

<!-- HEADER -->
<section class="card">
<header style="display:flex;justify-content:space-between;align-items:center">
<div>
<h1>Super Admin Dashboard</h1>
</div>
<form method="post" action="../logout.php">
<button class="btn-logout">Logout</button>
</form>
</header>
</section>

<!-- ACTION BUTTONS (RESTORED & PERMANENT) -->
<section class="card mt-2">
<div class="nav-grid">
<a href="register_pwd.php" class="nav-btn"><strong>Register PWD</strong></a>
<a href="register_employer.php" class="nav-btn"><strong>Register Employer</strong></a>
<a href="pwd_list.php" class="nav-btn"><strong>PWD List</strong></a>
<a href="audit_logs.php" class="nav-btn"><strong>Audit Logs</strong></a>
</div>
</section>

<!-- SUMMARY -->
<section class="card mt-3">
<div class="dashboard-grid">
<div class="card"><h3>Total PWDs</h3><p><?= $totalPWD ?></p></div>
<div class="card"><h3>Total Employers</h3><p><?= $totalEmployer ?></p></div>
<div class="card"><h3>Total Jobs</h3><p><?= $totalJobs ?></p></div>
<div class="card"><h3>Total Applications</h3><p><?= $totalApplications ?></p></div>
</div>
<a href="export_pwd_report_excel.php" class="btn-download">Download Excel Report</a>
</section>

<!-- CHARTS -->
<section class="card mt-3">
<div class="chart-box"><h3>PWD Employment Status</h3><canvas id="employmentChart"></canvas></div>
<div class="chart-box"><h3>PWD per Municipality</h3><canvas id="pwdMunicipalityChart"></canvas></div>
<div class="chart-box"><h3>Jobs per Municipality</h3><canvas id="jobsMunicipalityChart"></canvas></div>
<div class="chart-box"><h3>PWD by Disability (Whole Antique)</h3><canvas id="pwdDisabilityChart"></canvas></div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(employmentChart,{type:'doughnut',
data:{labels:<?= json_encode($empLabels) ?>,datasets:[{data:<?= json_encode($empTotals) ?>}]}});

new Chart(pwdMunicipalityChart,{type:'bar',
data:{labels:<?= json_encode($pwdMunLabels) ?>,datasets:[{data:<?= json_encode($pwdMunTotals) ?>}]}});

new Chart(jobsMunicipalityChart,{type:'bar',
data:{labels:<?= json_encode($jobMunLabels) ?>,datasets:[{data:<?= json_encode($jobMunTotals) ?>}]}});

new Chart(pwdDisabilityChart,{type:'pie',
data:{labels:<?= json_encode($disLabels) ?>,datasets:[{data:<?= json_encode($disTotals) ?>}]}});</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>