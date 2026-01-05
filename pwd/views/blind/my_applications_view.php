<?php
// pwd/views/blind/my_applications_view.php
?>
<section class="card">
    <h1 class="page-title">My Job Applications (Blind)</h1>

    <?php if (!$applications): ?>
        <p tabindex="0">You have not applied to any job yet.</p>
    <?php else: ?>
        <table class="table accessible-table" role="table">
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Company</th>
                    <th>Location</th>
                    <th>Date Applied</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($applications as $app): ?>
                <tr tabindex="0">
                    <td><?= htmlspecialchars($app['job_title']) ?></td>
                    <td><?= htmlspecialchars($app['business_name']) ?></td>
                    <td><?= htmlspecialchars($app['job_location']) ?></td>
                    <td><?= htmlspecialchars($app['date_applied']) ?></td>
                    <td><?= htmlspecialchars($app['status']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="blind-nav" style="margin-top:1rem;">
        <a href="view_jobs.php" class="blind-btn">Back to Jobs</a>
    </div>
</section>