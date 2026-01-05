<section class="card blind-profile" tabindex="0">

    <h1 class="page-title" tabindex="0">
        My Profile – Blind PWD
    </h1>

    <p tabindex="0">
        Below is your official profile information.
    </p>

    <table class="table accessible-table" role="table">
        <tr tabindex="0">
            <th>PWD ID Number</th>
            <td><?= htmlspecialchars($pwd['pwd_number']) ?></td>
        </tr>

        <tr tabindex="0">
            <th>Full Name</th>
            <td><?= htmlspecialchars($pwd['full_name']) ?></td>
        </tr>

        <tr tabindex="0">
            <th>Municipality</th>
            <td><?= htmlspecialchars($pwd['municipality']) ?></td>
        </tr>

        <tr tabindex="0">
            <th>Disability Category</th>
            <td><?= htmlspecialchars($pwd['disability_category']) ?></td>
        </tr>

        <?php if ($pwd['blind_type']): ?>
        <tr tabindex="0">
            <th>Blind Type</th>
            <td><?= htmlspecialchars($pwd['blind_type']) ?></td>
        </tr>
        <?php endif; ?>

        <tr tabindex="0">
            <th>Cause of Disability</th>
            <td><?= htmlspecialchars($pwd['cause_of_disability']) ?></td>
        </tr>

        <tr tabindex="0">
            <th>Education</th>
            <td><?= htmlspecialchars($pwd['educational_level']) ?></td>
        </tr>

        <tr tabindex="0">
            <th>Employment Status</th>
            <td>
                <?php if ($pwd['employment_status'] === 'Employed'): ?>
                    <strong style="color:#22c55e;">EMPLOYED</strong>
                <?php else: ?>
                    <strong style="color:#ef4444;">UNEMPLOYED</strong>
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <!-- TTS READ PROFILE -->
    <button class="blind-btn btn-tts"
        data-speak="
        Profile summary.
        Name <?= htmlspecialchars($pwd['full_name']) ?>.
        Employment status <?= htmlspecialchars($pwd['employment_status']) ?>.
        ">
        🎙 Read Profile Summary
    </button>

    <div class="blind-nav" style="margin-top:1.2rem;">
        <a href="dashboard_blind.php" class="blind-btn">
            Back to Dashboard
        </a>
    </div>

</section>