<section class="card">
    <h1 class="page-title">My Profile – Deaf</h1>
    <p class="page-subtitle">Visual summary of your information.</p>

    <div class="icon-grid">
        <div class="icon-btn">
            <div class="icon">👤</div>
            <div class="nav-btn-title"><?= htmlspecialchars($pwd['full_name']) ?></div>
            <div class="nav-btn-desc">Full Name</div>
        </div>

        <div class="icon-btn">
            <div class="icon">♿</div>
            <div class="nav-btn-title">
                <?= htmlspecialchars($pwd['deaf_type'] ?: $pwd['disability_category']) ?>
            </div>
            <div class="nav-btn-desc">Disability</div>
        </div>

        <div class="icon-btn">
            <div class="icon">📍</div>
            <div class="nav-btn-title"><?= htmlspecialchars($pwd['municipality']) ?></div>
            <div class="nav-btn-desc">Municipality</div>
        </div>

        <div class="icon-btn">
            <div class="icon">🎓</div>
            <div class="nav-btn-title"><?= htmlspecialchars($pwd['educational_level']) ?></div>
            <div class="nav-btn-desc">Education</div>
        </div>

        <div class="icon-btn">
            <div class="icon">💼</div>
            <div class="nav-btn-title"><?= htmlspecialchars($pwd['employment_status']) ?></div>
            <div class="nav-btn-desc">Employment</div>
        </div>
    </div>
</section>