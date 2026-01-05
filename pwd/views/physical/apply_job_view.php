<?php
// pwd/views/physical/apply_job_view.php
?>
<section class="card physical-ui">
    <h1 class="page-title">Apply to Job (Physical Disability)</h1>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($job): ?>
        <div class="big-btn">
            <strong><?= htmlspecialchars($job['job_title']) ?></strong><br>
            <?= htmlspecialchars($job['business_name']) ?><br>
            Location: <?= htmlspecialchars($job['job_location']) ?><br>
            Salary: <?= htmlspecialchars($job['salary_range']) ?><br>
            Target: <?= htmlspecialchars($job['disability_target']) ?>
        </div>

        <?php if (!$success): ?>
        <form method="post">
            <input type="hidden" name="job_id" value="<?= (int)$job['job_id'] ?>">
            <button type="submit" class="btn btn-outline">
                Confirm Application
            </button>
        </form>
        <?php endif; ?>
    <?php endif; ?>

    <p style="margin-top:1rem;">
        <a href="view_jobs.php" class="btn-link">← Back to Jobs</a>
    </p>
</section>