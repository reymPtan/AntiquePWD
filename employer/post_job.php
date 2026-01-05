<?php
// employer/post_job.php
// Employer creates a new job post with disability target + required skills
// + FIXED JOB LOCATION (Antique municipalities only)

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireEmployerLogin();

$permitNo  = $_SESSION['business_permit_no'] ?? '';
$pageTitle = 'Post a Job';

/* ===============================
   LOAD EMPLOYER INFO
   =============================== */
$employer = null;
if ($permitNo) {
    $stmt = $conn->prepare("SELECT business_name FROM employers WHERE business_permit_no = ?");
    $stmt->bind_param('s', $permitNo);
    $stmt->execute();
    $res = $stmt->get_result();
    $employer = $res->fetch_assoc();
    $stmt->close();
}

/* ===============================
   LOAD JOB ROLES
   =============================== */
$jobRoles = [];
$sqlRoles = "SELECT role_id, role_name, allow_blind, allow_deaf, allow_physical
             FROM job_roles
             ORDER BY role_name";

if ($r = $conn->query($sqlRoles)) {
    while ($row = $r->fetch_assoc()) {
        $jobRoles[] = $row;
    }
    $r->free();
}

/* ===============================
   LOAD SKILLS (GROUPED)
   =============================== */
$skillsByCat = [
    'Blind'               => [],
    'Deaf'                => [],
    'Physical Disability' => [],
];

$sqlSkills = "SELECT skill_id, skill_name, disability_category
              FROM skills_master
              WHERE is_active = 1
              ORDER BY disability_category, skill_name";

if ($s = $conn->query($sqlSkills)) {
    while ($row = $s->fetch_assoc()) {
        $cat = $row['disability_category'];
        if (isset($skillsByCat[$cat])) {
            $skillsByCat[$cat][] = $row;
        }
    }
    $s->free();
}

/* ===============================
   FIXED MUNICIPALITIES OF ANTIQUE
   =============================== */
$antiqueMunicipalities = [
    'Anini-y',
    'Barbaza',
    'Belison',
    'Bugasong',
    'Caluya',
    'Culasi',
    'Hamtic',
    'Laua-an',
    'Libertad',
    'Pandan',
    'Patnongon',
    'San Jose',
    'San Remigio',
    'Sebaste',
    'Sibalom',
    'Tibiao',
    'Tobias Fornier (Dao)',
    'Valderrama'
];

require_once __DIR__ . '/../includes/header.php';
?>

<section class="card">
    <header class="page-header">
        <h1 class="page-title">Post a New Job</h1>
        <?php if ($employer): ?>
            <p class="page-subtitle">
                <?= htmlspecialchars($employer['business_name']) ?> – <?= htmlspecialchars($permitNo) ?>
            </p>
        <?php endif; ?>
    </header>

    <form action="post_job_save.php" method="post" class="form-grid">

        <!-- JOB ROLE -->
        <div class="form-row">
            <label for="role_id">Job Role / Title <span class="required">*</span></label>
            <select name="role_id" id="role_id" required>
                <option value="">-- Select a role --</option>
                <?php foreach ($jobRoles as $role): ?>
                    <option
                        value="<?= (int)$role['role_id'] ?>"
                        data-allow-blind="<?= (int)$role['allow_blind'] ?>"
                        data-allow-deaf="<?= (int)$role['allow_deaf'] ?>"
                        data-allow-physical="<?= (int)$role['allow_physical'] ?>"
                    >
                        <?= htmlspecialchars($role['role_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- DISABILITY TARGET -->
        <div class="form-row">
            <label for="disability_target">Disability Target <span class="required">*</span></label>
            <select name="disability_target" id="disability_target" required>
                <option value="">-- Select --</option>
                <option value="Blind">Blind</option>
                <option value="Deaf">Deaf</option>
                <option value="Physical Disability">Physical Disability</option>
            </select>
        </div>

        <!-- JOB DETAILS -->
        <div class="form-row">
            <label for="job_title">Custom Job Title (optional)</label>
            <input type="text" name="job_title" id="job_title"
                   placeholder="If different from role name">
        </div>

        <div class="form-row">
            <label for="job_description">Job Description <span class="required">*</span></label>
            <textarea name="job_description" id="job_description" rows="4" required></textarea>
        </div>

        <!-- ✅ FIXED JOB LOCATION -->
        <div class="form-row">
            <label for="job_location">Job Location (Municipality – Antique) <span class="required">*</span></label>
            <select name="job_location" id="job_location" required>
                <option value="">-- Select Municipality --</option>
                <?php foreach ($antiqueMunicipalities as $municipality): ?>
                    <option value="<?= htmlspecialchars($municipality) ?>">
                        <?= htmlspecialchars($municipality) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-row">
            <label for="salary_range">Salary Range</label>
            <input type="text" name="salary_range" id="salary_range"
                   placeholder="e.g. ₱12,000–₱15,000 / month">
        </div>

        <div class="form-row">
            <label for="max_hires">Number of PWDs to Hire <span class="required">*</span></label>
            <input type="number" name="max_hires" id="max_hires" min="1" value="1" required>
        </div>

        <div class="form-row">
            <label for="application_deadline">Application Deadline</label>
            <input type="date" name="application_deadline" id="application_deadline">
        </div>

        <!-- REQUIRED SKILLS -->
        <fieldset class="form-row">
            <legend>Required Skills</legend>

            <?php foreach ($skillsByCat as $cat => $list): ?>
                <div class="skill-group" data-cat="<?= htmlspecialchars($cat) ?>">
                    <h3><?= htmlspecialchars($cat) ?> Skills</h3>
                    <?php if (empty($list)): ?>
                        <p class="small-note">No skills available.</p>
                    <?php else: ?>
                        <?php foreach ($list as $skill): ?>
                            <label>
                                <input type="checkbox" name="skills[]"
                                       value="<?= (int)$skill['skill_id'] ?>">
                                <?= htmlspecialchars($skill['skill_name']) ?>
                            </label><br>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </fieldset>

        <!-- ACTIONS -->
        <div class="form-actions">
            <button type="submit" class="btn primary-btn">Publish Job</button>
            <a href="my_jobs.php" class="btn secondary-btn">Cancel</a>
        </div>

    </form>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<script>
(function () {
    const roleSelect   = document.getElementById('role_id');
    const targetSelect = document.getElementById('disability_target');
    const skillGroups  = document.querySelectorAll('.skill-group');

    function hideAllSkills() {
        skillGroups.forEach(group => {
            group.style.display = 'none';
        });
    }

    function showSkillsForTarget(target) {
        hideAllSkills();
        if (!target) return;

        skillGroups.forEach(group => {
            if (group.dataset.cat === target) {
                group.style.display = 'block';
            }
        });
    }

    function updateTargetFromRole() {
        const opt = roleSelect.options[roleSelect.selectedIndex];
        if (!opt) return;

        const allowed = [];
        if (opt.dataset.allowBlind === '1') allowed.push('Blind');
        if (opt.dataset.allowDeaf === '1') allowed.push('Deaf');
        if (opt.dataset.allowPhysical === '1') allowed.push('Physical Disability');

        // Auto-select if only one disability allowed
        if (allowed.length === 1) {
            targetSelect.value = allowed[0];
            showSkillsForTarget(allowed[0]);
        }
    }

    // EVENTS
    roleSelect.addEventListener('change', updateTargetFromRole);
    targetSelect.addEventListener('change', function () {
        showSkillsForTarget(this.value);
    });

    // INIT
    hideAllSkills();
})();
</script>