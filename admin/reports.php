<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
requireAdminRole(['SUPER','PWD_ADMIN','EMPLOYER_ADMIN']);

$reportPwd = $conn->query("
    SELECT disability_category, COUNT(*) AS total
    FROM pwd_profiles
    GROUP BY disability_category
");

$reportJobs = $conn->query("
    SELECT status, COUNT(*) AS total
    FROM job_postings
    GROUP BY status
");

$reportApps = $conn->query("
    SELECT status, COUNT(*) AS total
    FROM job_applications
    GROUP BY status
");

$pageTitle = 'System Reports';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="card">
    <h2>System Reports</h2>

    <h3>PWD Count by Disability Category</h3>
    <table class="table">
        <thead><tr><th>Disability Category</th><th>Total PWDs</th></tr></thead>
        <tbody>
        <?php if ($reportPwd && $reportPwd->num_rows > 0): ?>
            <?php while ($row = $reportPwd->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['disability_category']) ?></td>
                    <td><?= (int)$row['total'] ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="2">No PWD data yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <h3>Job Posts by Status</h3>
    <table class="table">
        <thead><tr><th>Status</th><th>Total Jobs</th></tr></thead>
        <tbody>
        <?php if ($reportJobs && $reportJobs->num_rows > 0): ?>
            <?php while ($row = $reportJobs->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td><?= (int)$row['total'] ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="2">No job data yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <h3>Job Applications by Status</h3>
    <table class="table">
        <thead><tr><th>Status</th><th>Total Applications</th></tr></thead>
        <tbody>
        <?php if ($reportApps && $reportApps->num_rows > 0): ?>
            <?php while ($row = $reportApps->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td><?= (int)$row['total'] ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="2">No application data yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
