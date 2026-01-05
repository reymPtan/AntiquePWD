<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requirePwdLogin();

$pwdNumber = $_SESSION['pwd_number'];

$sql = "
SELECT
    h.job_id,
    j.job_title,
    j.business_name,
    h.end_date,
    h.remarks
FROM employment_history h
JOIN job_postings j ON h.job_id = j.job_id
WHERE h.pwd_number = ?
ORDER BY h.end_date DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $pwdNumber);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

require_once __DIR__ . '/../includes/header.php';
?>

<section class="card">
    <h2>Employment History</h2>

    <?php if ($result->num_rows === 0): ?>
        <p>No employment history yet.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Employer</th>
                    <th>End Date</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['job_title']) ?></td>
                    <td><?= htmlspecialchars($row['business_name']) ?></td>
                    <td><?= htmlspecialchars($row['end_date']) ?></td>
                    <td><?= htmlspecialchars($row['remarks']) ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>