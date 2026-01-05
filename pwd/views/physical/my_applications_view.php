<?php
// pwd/views/physical/my_applications_view.php
?>
<section class="card physical-ui">
    <h1 class="page-title">My Job Applications (Physical Disability)</h1>

    <?php if (!$applications): ?>
        <div class="big-btn">
            You have not applied to any job yet.
        </div>
    <?php else: ?>
        <?php foreach ($applications as $app): ?>
            <div class="big-btn">
                <strong><?= htmlspecialchars($app['job_title']) ?></strong><br>
                <?= htmlspecialchars($app['business_name']) ?><br>
                Location: <?= htmlspecialchars($app['job_location']) ?><br>
                Applied: <?= htmlspecialchars($app['date_applied']) ?><br>
                Status: <?= htmlspecialchars($app['status']) ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>