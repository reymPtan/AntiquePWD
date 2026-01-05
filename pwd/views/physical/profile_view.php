<section class="card physical-ui">
    <h1 class="page-title">My Profile – Physical Disability</h1>

    <div class="big-btn">
        <strong>PWD ID Number</strong><br>
        <?= htmlspecialchars($pwd['pwd_number']) ?>
    </div>

    <div class="big-btn">
        <strong>Full Name</strong><br>
        <?= htmlspecialchars($pwd['full_name']) ?>
    </div>

    <div class="big-btn">
        <strong>Municipality</strong><br>
        <?= htmlspecialchars($pwd['municipality']) ?>
    </div>

    <div class="big-btn">
        <strong>Disability</strong><br>
        <?= htmlspecialchars($pwd['physical_type'] ?: $pwd['disability_category']) ?>
    </div>

    <div class="big-btn">
        <strong>Education</strong><br>
        <?= htmlspecialchars($pwd['educational_level']) ?>
    </div>

    <div class="big-btn">
        <strong>Employment Status</strong><br>
        <?= htmlspecialchars($pwd['employment_status']) ?>
    </div>
</section>