<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireEmployerLogin();

$permitNo = $_SESSION['business_permit_no'];

$jobId = (int)($_GET['job_id'] ?? 0);
if ($jobId <= 0) die('Invalid job');

$sql = "SELECT job_id, job_title, status FROM job_postings
        WHERE job_id=? AND business_permit_no=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('is', $jobId, $permitNo);
$stmt->execute();
$job = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$job) die('Unauthorized');

$sql = "SELECT 
            a.application_id,
            a.pwd_number,
            a.status,
            a.date_applied,
            p.full_name,
            p.disability_category
        FROM job_applications a
        JOIN pwd_profiles p ON p.pwd_number = a.pwd_number
        WHERE a.job_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $jobId);
$stmt->execute();
$applicants = $stmt->get_result();
$stmt->close();

require_once __DIR__ . '/../includes/header.php';
?>

<section class="card">
<h2>Applicants for: <?= htmlspecialchars($job['job_title']) ?></h2>

<table class="table">
<tr>
    <th>PWD ID</th>
    <th>Name</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while ($row = $applicants->fetch_assoc()): ?>
<tr>
    <td><?= $row['pwd_number'] ?></td>
    <td><?= $row['full_name'] ?></td>
    <td><?= $row['status'] ?></td>
    <td>

    <?php if ($row['status'] === 'Pending'): ?>
        <form method="post" action="application_decide.php">
            <input type="hidden" name="application_id" value="<?= $row['application_id'] ?>">
            <button name="action" value="accept">Accept</button>
            <button name="action" value="reject">Reject</button>
        </form>

    <?php elseif ($row['status'] === 'Accepted'): ?>
        <form method="post" action="end_contract.php"
              onsubmit="return confirm('End this contract?')">
            <input type="hidden" name="application_id" value="<?= $row['application_id'] ?>">
            <button style="background:red;color:white;">End Contract</button>
        </form>

    <?php else: ?>
        No action
    <?php endif; ?>

    </td>
</tr>
<?php endwhile; ?>
</table>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>