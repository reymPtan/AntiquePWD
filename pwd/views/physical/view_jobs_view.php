<?php
// pwd/views/physical/view_jobs_view.php
// Jobs list para sa Physical Disability – simple, spacious layout
?>
<section class="card physical-card">
    <header class="page-header">
        <h1 class="page-title">
            Available Jobs
        </h1>
        <p class="page-subtitle">
            These jobs are matched to your profile and skills.
        </p>
    </header>

    <?php if (empty($jobs)): ?>
        <div class="alert alert-info">
            There are no matching jobs at this time. Please check again later.
        </div>
    <?php else: ?>

        <div class="job-list physical-job-list">
            <?php foreach ($jobs as $job): ?>
                <?php
                $requires = (int)($job['requires_skills'] ?? 0);
                $matches  = (int)($job['match_skills'] ?? 0);
                $skills   = is_array($job['required_names'] ?? null)
                            ? $job['required_names']
                            : [];
                ?>

                <article class="job-card physical-job-card">
                    <header class="job-header">
                        <h2 class="job-title">
                            <?= htmlspecialchars($job['job_title']) ?>
                        </h2>
                        <p class="job-company">
                            <?= htmlspecialchars($job['business_name'] ?? 'Company') ?>
                        </p>
                    </header>

                    <div class="job-body">
                        <p>
                            <strong>Location:</strong>
                            <?= htmlspecialchars($job['job_location']) ?>
                        </p>

                        <?php if (!empty($job['salary_range'])): ?>
                            <p>
                                <strong>Salary:</strong>
                                <?= htmlspecialchars($job['salary_range']) ?>
                            </p>
                        <?php endif; ?>

                        <?php if (!empty($job['application_deadline'])): ?>
                            <p>
                                <strong>Deadline:</strong>
                                <?= htmlspecialchars($job['application_deadline']) ?>
                            </p>
                        <?php endif; ?>

                        <div class="job-skills-box">
                            <?php if ($requires > 0): ?>
                                <p class="skill-title">
                                    ✅ Skill Match:
                                    <strong><?= $matches ?></strong> /
                                    <strong><?= $requires ?></strong>
                                </p>
                                <p class="skill-label">
                                    📌 Required Skills:
                                </p>
                                <ul class="skill-list">
                                    <?php foreach($skills as $skill): ?>
                                        <li class="skill-item">
                                            <?= htmlspecialchars($skill) ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="skill-title">
                                    ✔ No specific skills required
                                    <span class="skill-note">(Open to all qualified PWDs)</span>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <footer class="job-footer">
                        <a href="apply_job.php?job_id=<?= (int)$job['job_id'] ?>"
                           class="btn primary-btn">
                            Apply for this Job
                        </a>
                    </footer>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="page-actions">
        <a href="dashboard_physical.php" class="btn secondary-btn">
            ← Back to Physical Disability Dashboard
        </a>
    </div>
</section>
