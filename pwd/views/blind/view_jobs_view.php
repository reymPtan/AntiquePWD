<?php
// =====================================================
// pwd/views/blind/view_jobs_view.php
// BLIND PWD – JOB LIST VIEW (TTS + SCREEN READER SAFE)
// =====================================================
?>

<section class="card blind-card" role="region" aria-label="Available jobs list">

    <h1 class="page-title" tabindex="0">
        Available Jobs for You
    </h1>

    <p tabindex="0">
        Use the Tab key to move through jobs.  
        Press Enter to apply.  
        Use the voice buttons to hear job details.
    </p>

    <?php if (empty($jobs)): ?>
        <div class="alert alert-info" tabindex="0">
            No available jobs matched your profile at this time.
        </div>
    <?php else: ?>

        <div class="job-list" aria-label="Job listings">

            <?php foreach ($jobs as $job): ?>
                <?php
                $requires = (int)($job['requires_skills'] ?? 0);
                $matches  = (int)($job['match_skills'] ?? 0);
                $skills   = $job['required_names'] ?? [];
                ?>

                <article class="job-card blind-job-card"
                         tabindex="0"
                         role="group"
                         aria-label="Job <?= htmlspecialchars($job['job_title']) ?>">

                    <!-- JOB HEADER -->
                    <header>
                        <h2 tabindex="0">
                            <?= htmlspecialchars($job['job_title']) ?>
                        </h2>

                        <p tabindex="0">
                            Company:
                            <?= htmlspecialchars($job['business_name']) ?>
                        </p>

                        <!-- 🎙 TTS BUTTON -->
                        <button type="button"
                                class="btn-tts"
                                tabindex="0"
                                aria-label="Read job details"
                                data-speak="
                                Job <?= htmlspecialchars($job['job_title']) ?>.
                                Company <?= htmlspecialchars($job['business_name']) ?>.
                                Location <?= htmlspecialchars($job['job_location']) ?>.
                                Salary <?= htmlspecialchars($job['salary_range'] ?? 'Not specified') ?>.
                                Skill match <?= $matches ?> out of <?= $requires ?>.
                                ">
                            🎙 Read Job
                        </button>
                    </header>

                    <!-- JOB DETAILS -->
                    <div class="job-body">
                        <p tabindex="0">
                            <strong>Location:</strong>
                            <?= htmlspecialchars($job['job_location']) ?>
                        </p>

                        <?php if (!empty($job['salary_range'])): ?>
                            <p tabindex="0">
                                <strong>Salary:</strong>
                                <?= htmlspecialchars($job['salary_range']) ?>
                            </p>
                        <?php endif; ?>

                        <!-- SKILLS -->
                        <div tabindex="0" aria-label="Skill requirements">
                            <?php if ($requires > 0): ?>
                                <p>
                                    Skill Match:
                                    <strong><?= $matches ?></strong> /
                                    <strong><?= $requires ?></strong>
                                </p>

                                <ul>
                                    <?php foreach ($skills as $skill): ?>
                                        <li tabindex="0"><?= htmlspecialchars($skill) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p>
                                    No specific skills required.
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- APPLY BUTTON -->
                    <footer>
                        <a href="apply_job.php?job_id=<?= (int)$job['job_id'] ?>"
                           class="blind-btn"
                           tabindex="0"
                           aria-label="Apply for <?= htmlspecialchars($job['job_title']) ?>">
                            Apply for this Job
                        </a>
                    </footer>

                </article>

            <?php endforeach; ?>

        </div>
    <?php endif; ?>

    <!-- BACK -->
    <div class="blind-nav" style="margin-top:1.5rem;">
        <a href="dashboard_blind.php"
           class="blind-btn"
           tabindex="0"
           aria-label="Back to Blind Dashboard">
            ← Back to Blind Dashboard
        </a>
    </div>

</section>

<!-- =====================================================
     🎙 GLOBAL TTS HANDLER (BLIND)
===================================================== -->
<script>
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('btn-tts')) {
        const text = e.target.getAttribute('data-speak');
        if (text) {
            window.speechSynthesis.cancel();
            const msg = new SpeechSynthesisUtterance(text);
            msg.lang = 'en-US';
            msg.rate = 0.9;
            window.speechSynthesis.speak(msg);
        }
    }
});
</script>
