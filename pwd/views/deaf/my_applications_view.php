<?php
// pwd/views/deaf/my_applications_view.php
?>
<section class="card">
    <h1 class="page-title">My Job Applications (Deaf)</h1>

    <?php if (!$applications): ?>
        <p>You have not applied to any job yet.</p>
    <?php else: ?>
        <div class="icon-grid">
        <?php foreach ($applications as $app): ?>
            <div class="icon-btn">
                <div class="icon">📄</div>
                <div class="nav-btn-title"><?= htmlspecialchars($app['job_title']) ?></div>
                <div class="nav-btn-desc">
                    <?= htmlspecialchars($app['business_name']) ?><br>
                    <?= htmlspecialchars($app['job_location']) ?><br>
                    Applied: <?= htmlspecialchars($app['date_applied']) ?><br>
                    Status: <?= htmlspecialchars($app['status']) ?>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>