<?php
// pwd/views/deaf/apply_job_view.php
?>
<section class="card">
    <h1 class="page-title">Apply to Job (Deaf)</h1>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($job): ?>
        <div class="icon-grid">
            <div class="icon-btn">
                <div class="icon">💼</div>
                <div class="nav-btn-title"><?= htmlspecialchars($job['job_title']) ?></div>
                <div class="nav-btn-desc">
                    <?= htmlspecialchars($job['business_name']) ?><br>
                    <?= htmlspecialchars($job['job_location']) ?><br>
                    <?= htmlspecialchars($job['salary_range']) ?><br>
                    Target: <?= htmlspecialchars($job['disability_target']) ?>
                </div>
            </div>
        </div>

        <?php if (!$success): ?>
        <form method="post" style="margin-top:1rem;">
            <input type="hidden" name="job_id" value="<?= (int)$job['job_id'] ?>">
            <button type="submit" class="btn btn-outline">Confirm Application</button>
        </form>
        <?php endif; ?>
    <?php endif; ?>

    <p style="margin-top:1rem;">
        <a href="view_jobs.php" class="btn-link">← Back to Jobs</a>
    </p>
</section>