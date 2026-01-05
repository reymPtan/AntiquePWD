<?php
// pwd/jobs_available.php
// Shows jobs filtered by PWD disability using job_roles.allow_*

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requirePWDLogin();

$pwdNumber   = $_SESSION['pwd_number'] ?? '';
$fullName    = $_SESSION['full_name'] ?? '';
$disability  = $_SESSION['disability_category'] ?? ''; // e.g. 'Blind', 'Deaf', 'Physical Disability'

// 1. Normalize disability value
$disabilityClean = strtolower(trim($disability));

// Default: walang lalabas kapag hindi kilala
$condition = '0=1';
$labelFor  = 'Unknown';

if (strpos($disabilityClean, 'blind') !== false) {
    $condition = 'jr.allow_blind = 1';
    $labelFor  = 'Blind';
} elseif (strpos($disabilityClean, 'deaf') !== false) {
    $condition = 'jr.allow_deaf = 1';
    $labelFor  = 'Deaf';
} elseif (strpos($disabilityClean, 'physical') !== false) {
    $condition = 'jr.allow_physical = 1';
    $labelFor  = 'Physical Disability';
}

// 2. Query jobs strictly using flags
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
            e.business_name,
            jr.role_name,
            jr.allow_blind,
            jr.allow_deaf,
            jr.allow_physical
        FROM job_postings jp
        JOIN employers e ON jp.business_permit_no = e.business_permit_no
        JOIN job_roles jr ON jp.role_id = jr.role_id
        WHERE jp.status = 'Open'
          AND ($condition)
          AND (jp.application_deadline IS NULL OR jp.application_deadline >= CURDATE())
        ORDER BY jp.date_posted DESC";

$result = $conn->query($sql);

$pageTitle = 'Available Jobs';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="card">
    <h2>Available Jobs for <?= htmlspecialchars($labelFor) ?> PWD</h2>
    <p>
        You are registered as: <strong><?= htmlspecialchars($disability) ?></strong><br>
        The system only shows jobs that are marked as suitable for this category.
    </p>

    <?php
    $appliedStatus = $_GET['applied'] ?? '';
    if ($appliedStatus === 'success'): ?>
        <div class="alert alert-success">
            Your application has been submitted and is now <strong>Pending</strong>.
        </div>
    <?php elseif ($appliedStatus === 'duplicate'): ?>
        <div class="alert alert-warning">
            You already applied for this job.
        </div>
    <?php elseif ($appliedStatus === 'job_not_found'): ?>
        <div class="alert alert-error">
            The job you tried to apply to could not be found. It may have been removed.
        </div>
    <?php elseif ($appliedStatus === 'job_closed'): ?>
        <div class="alert alert-error">
            This job is already closed.
        </div>
    <?php elseif ($appliedStatus === 'job_expired'): ?>
        <div class="alert alert-error">
            This job is already past its application deadline.
        </div>
    <?php elseif ($appliedStatus === 'max_hires_reached'): ?>
        <div class="alert alert-error">
            This job already reached its maximum number of hires.
        </div>
    <?php elseif ($appliedStatus === 'not_suitable'): ?>
        <div class="alert alert-error">
            This job is not marked as suitable for your registered disability category.
        </div>
    <?php elseif ($appliedStatus === 'invalid_job'): ?>
        <div class="alert alert-error">
            Invalid job selected.
        </div>
    <?php elseif ($appliedStatus === 'error_saving'): ?>
        <div class="alert alert-error">
            There was an error saving your application. Please try again.
        </div>
    <?php endif; ?>

    <?php if ($result === false): ?>
        <p>Error loading jobs: <?= htmlspecialchars($conn->error) ?></p>

    <?php elseif ($result->num_rows === 0): ?>
        <p>No matching jobs available at the moment. Please check again later.</p>

    <?php else: ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <article class="card job-card">
                <h3><?= htmlspecialchars($row['job_title']) ?></h3>
                <p><strong>Role:</strong> <?= htmlspecialchars($row['role_name']) ?></p>
                <p><strong>Company:</strong> <?= htmlspecialchars($row['business_name']) ?></p>

                <?php if (!empty($row['job_location'])): ?>
                    <p><strong>Location:</strong> <?= htmlspecialchars($row['job_location']) ?></p>
                <?php endif; ?>

                <?php if (!empty($row['salary_range'])): ?>
                    <p><strong>Salary:</strong> <?= htmlspecialchars($row['salary_range']) ?></p>
                <?php endif; ?>

                <p><strong>Posted on:</strong> <?= htmlspecialchars($row['date_posted']) ?></p>
                <p><strong>Hires:</strong> <?= (int)$row['hired_count'] ?> / <?= (int)$row['max_hires'] ?></p>

                <?php if (!empty($row['job_description'])): ?>
                    <p><?= nl2br(htmlspecialchars($row['job_description'])) ?></p>
                <?php endif; ?>

                <?php
                $targets = [];
                if ((int)$row['allow_blind'] === 1)    $targets[] = 'Blind';
                if ((int)$row['allow_deaf'] === 1)     $targets[] = 'Deaf';
                if ((int)$row['allow_physical'] === 1) $targets[] = 'Physical Disability';
                $targetText = $targets ? implode(', ', $targets) : 'Not specified';
                ?>
                <p style="font-size: 12px; color:#555;">
                    <strong>Target Disability:</strong> <?= htmlspecialchars($targetText) ?>
                </p>

                <form method="post" action="apply_job.php">
                    <input type="hidden" name="job_id" value="<?= (int)$row['job_id'] ?>">
                    <button type="submit">Apply to this job</button>
                </form>
            </article>
        <?php endwhile; ?>
    <?php endif; ?>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>