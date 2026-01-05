<?php
// pwd/views/blind/apply_job_view.php
// Blind-friendly Apply Job confirmation view (TTS + keyboard)
?>

<section class="card" tabindex="0" aria-label="Confirm job application">

    <h1 class="page-title"
        tabindex="0"
        aria-label="Confirm job application">
        Confirm Job Application
    </h1>

    <?php if ($error): ?>
        <div class="alert alert-error"
             tabindex="0"
             aria-label="Error message">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"
             tabindex="0"
             aria-label="Application submitted successfully">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if ($job): ?>

        <!-- JOB SUMMARY (READABLE BY SCREEN READER) -->
        <div class="job-card"
             tabindex="0"
             role="region"
             aria-label="Job details summary">

            <h2 tabindex="0">
                <?= htmlspecialchars($job['job_title']) ?>
            </h2>

            <p tabindex="0">
                Company:
                <?= htmlspecialchars($job['business_name']) ?>
            </p>

            <p tabindex="0">
                Location:
                <?= htmlspecialchars($job['job_location']) ?>
            </p>

            <?php if (!empty($job['salary_range'])): ?>
                <p tabindex="0">
                    Salary:
                    <?= htmlspecialchars($job['salary_range']) ?>
                </p>
            <?php endif; ?>

            <p tabindex="0">
                Target Disability:
                <?= htmlspecialchars($job['disability_target']) ?>
            </p>

            <!-- 🎙 VOICE READ JOB SUMMARY -->
            <button type="button"
                    class="btn-tts"
                    tabindex="0"
                    aria-label="Read job summary"
                    data-speak="
                    You are applying for <?= htmlspecialchars($job['job_title']) ?>.
                    Company <?= htmlspecialchars($job['business_name']) ?>.
                    Location <?= htmlspecialchars($job['job_location']) ?>.
                    Salary <?= htmlspecialchars($job['salary_range'] ?? 'not specified') ?>.
                    ">
                🎙 Read Job Summary
            </button>
        </div>

        <?php if (!$success): ?>
            <!-- CONFIRMATION QUESTION -->
            <p tabindex="0"
               aria-label="Are you sure you want to apply for this job?">
                Are you sure you want to apply for this job?
            </p>

            <!-- CONFIRM FORM -->
            <form method="post">
                <input type="hidden" name="job_id" value="<?= (int)$job['job_id'] ?>">

                <button type="submit"
                        class="blind-btn"
                        tabindex="0"
                        aria-label="Confirm application"
                        onclick="speechSynthesis.speak(new SpeechSynthesisUtterance('Application submitted'))">
                    Yes, Apply
                </button>
            </form>
        <?php endif; ?>

    <?php endif; ?>

    <!-- BACK BUTTON -->
    <div class="blind-nav" style="margin-top:1.2rem;">
        <button type="button"
                class="blind-btn"
                tabindex="0"
                aria-label="Go back to job list"
                onclick="
                    speechSynthesis.speak(
                        new SpeechSynthesisUtterance('Going back to job list')
                    );
                    history.back();
                ">
            Go Back
        </button>
    </div>

</section>

<!-- 🔊 TTS HANDLER -->
<script>
document.addEventListener('click', function(e) {
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
