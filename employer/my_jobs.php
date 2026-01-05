<?php
// employer/my_jobs.php
// Shows list of jobs posted by the logged-in employer

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireEmployerLogin();

$permitNo = $_SESSION['business_permit_no'] ?? '';

$sql = "SELECT
            jp.job_id,
            jp.job_title,
            jp.job_description,
            jp.job_location,
            jp.salary_range,
            jp.date_posted,
            jp.max_hires,
            jp.hired_count,
            jp.status,
            jp.application_deadline,
            jr.role_name,
            jr.allow_blind,
            jr.allow_deaf,
            jr.allow_physical
        FROM job_postings jp
        JOIN job_roles jr ON jp.role_id = jr.role_id
        WHERE jp.business_permit_no = ?
        ORDER BY jp.date_posted DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('Error loading jobs: ' . htmlspecialchars($conn->error));
}
$stmt->bind_param('s', $permitNo);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

$pageTitle = 'My Job Posts';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="card">
    <h2>My Job Posts</h2>

    <p><a href="post_job.php" class="btn">+ Post New Job</a></p>

    <?php if ($result->num_rows === 0): ?>
        <p>You have not posted any jobs yet.</p>

    <?php else: ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <article class="card job-card">
                <h3><?= htmlspecialchars($row['job_title']) ?></h3>
                <p><strong>Role:</strong> <?= htmlspecialchars($row['role_name']) ?></p>

                <p>
                    <strong>Status:</strong> <?= htmlspecialchars($row['status']) ?><br>
                    <strong>Hires:</strong> <?= (int)$row['hired_count'] ?> / <?= (int)$row['max_hires'] ?><br>
                    <strong>Posted on:</strong> <?= htmlspecialchars($row['date_posted']) ?>
                    <?php if (!empty($row['application_deadline'])): ?>
                        <br><strong>Deadline:</strong> <?= htmlspecialchars($row['application_deadline']) ?>
                    <?php endif; ?>
                </p>

                <?php if (!empty($row['job_location'])): ?>
                    <p><strong>Location:</strong> <?= htmlspecialchars($row['job_location']) ?></p>
                <?php endif; ?>

                <?php if (!empty($row['salary_range'])): ?>
                    <p><strong>Salary:</strong> <?= htmlspecialchars($row['salary_range']) ?></p>
                <?php endif; ?>

                <?php if (!empty($row['job_description'])): ?>
                    <p><?= nl2br(htmlspecialchars($row['job_description'])) ?></p>
                <?php endif; ?>

                <?php
                // 🔹 TARGET DISABILITY SNIPPET DITO RIN
                $targets = [];
                if ((int)$row['allow_blind'] === 1)    $targets[] = 'Blind';
                if ((int)$row['allow_deaf'] === 1)     $targets[] = 'Deaf';
                if ((int)$row['allow_physical'] === 1) $targets[] = 'Physical Disability';
                $targetText = $targets ? implode(', ', $targets) : 'Not specified';
                ?>
                <p style="font-size: 12px; color:#555;">
                    <strong>Target Disability:</strong> <?= htmlspecialchars($targetText) ?>
                </p>

                <p>
                    <a href="view_applicants.php?job_id=<?= (int)$row['job_id'] ?>" class="btn">
                        View Applicants
                    </a>
                </p>
            </article>
        <?php endwhile; ?>
    <?php endif; ?>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>