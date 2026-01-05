<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireAdminRole(['SUPER','PWD_ADMIN']);
$pageTitle = 'Register PWD';

/* ===============================
   MUNICIPALITIES & BARANGAYS
   =============================== */
$antiqueBarangays = [
    'Anini-y' => ['Igtumarom','Igcadac','San Ramon'],
    'Barbaza' => ['Binanuahan','Esparar','Mayabay'],
    'Belison' => ['Borocboroc','Delima','Sinaja'],
    'Bugasong' => ['Bacan','Bagtason','Cubay'],
    'Caluya' => ['Caluya','Semirara','Sibay'],
    'Culasi' => ['Centro Poblacion','Malalison','San Luis'],
    'Hamtic' => ['Bongbongan','Botbot','Casipitan','Funda','Malandog'],
    'Laua-an' => ['Balit','Calangcang','Poblacion'],
    'Libertad' => ['Cagamutan','Inyawan','Panangkilon'],
    'Pandan' => ['Centro Norte','Centro Sur','Duyong'],
    'Patnongon' => ['Apolong','Igbalangao','Magarang'],
    'San Jose' => [
        'Barangay 1','Barangay 2','Barangay 3','Barangay 4',
        'Barangay 5','Barangay 6','Barangay 7','Barangay 8',
        'Barangay 9','Barangay 10','Barangay 11','Barangay 12',
        'Barangay 13','Barangay 14','Barangay 15','Barangay 16',
        'Barangay 17','Barangay 18'
    ],
    'San Remigio' => ['Banbanan','Insubuan','Orquia'],
    'Sebaste' => ['Abiera','Idio','Poblacion'],
    'Sibalom' => ['Bongbongan I','Bongbongan II','Igdalaguit','Odiong'],
    'Tibiao' => ['Banderaahan','Salazar','Tuno'],
    'Tobias Fornier (Dao)' => ['Aguirre','Ballescas','Poblacion'],
    'Valderrama' => ['Busog','Canipayan','Igdalaquit']
];

/* ===============================
   LOAD SKILLS
   =============================== */
$skillsByCat = [
    'Any' => [],
    'Blind' => [],
    'Deaf' => [],
    'Physical Disability' => []
];

$res = $conn->query("
    SELECT skill_id, skill_name, disability_category
    FROM skills_master
    WHERE is_active = 1
    ORDER BY disability_category, skill_name
");
while ($r = $res->fetch_assoc()) {
    $skillsByCat[$r['disability_category']][] = $r;
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="card">
<h1>Register New PWD</h1>

<form action="register_pwd_save.php" method="post" enctype="multipart/form-data" class="form-grid">

<!-- PASSWORD -->
<div class="form-row">
    <label>Initial Password *</label>
    <input type="text" name="password" value="pwd12345" required>
</div>

<!-- PERSONAL -->
<div class="form-row">
    <label>Full Name *</label>
    <input type="text" name="full_name" required>
</div>

<div class="form-row">
    <label>Sex *</label>
    <select name="sex" required>
        <option value="">-- Select --</option>
        <option>Male</option>
        <option>Female</option>
        <option>Prefer not to say</option>
    </select>
</div>

<div class="form-row">
    <label>Birthdate *</label>
    <input type="date" name="birthdate" required>
</div>

<div class="form-row">
    <label>PWD Photo *</label>
    <input type="file" name="pwd_photo" accept="image/*" required>
</div>

<!-- LOCATION -->
<div class="form-row">
    <label>Municipality *</label>
    <select id="municipality" name="municipality" required>
        <option value="">-- Select Municipality --</option>
        <?php foreach ($antiqueBarangays as $mun => $b): ?>
            <option value="<?= $mun ?>"><?= $mun ?></option>
        <?php endforeach; ?>
    </select>
</div>

<div class="form-row">
    <label>Barangay *</label>
    <select id="barangay" name="barangay" required>
        <option value="">-- Select Barangay --</option>
    </select>
</div>

<div class="form-row">
    <label>Contact Number</label>
    <input type="text" name="contact_number">
</div>

<!-- DISABILITY -->
<div class="form-row">
    <label>Main Disability *</label>
    <select id="disability_category" name="disability_category" required>
        <option value="">-- Select --</option>
        <option>Blind</option>
        <option>Deaf</option>
        <option>Physical Disability</option>
    </select>
</div>

<div class="form-row blind-only">
    <label>Blind Type</label>
    <select name="blind_type">
        <option value="">-- Select --</option>
        <option>Total blindness</option>
        <option>Low vision</option>
        <option>Color blindness</option>
        <option>Blind with light perception</option>
    </select>
</div>

<div class="form-row deaf-only">
    <label>Deaf Type</label>
    <select name="deaf_type">
        <option value="">-- Select --</option>
        <option>Deaf (cannot hear)</option>
        <option>Hard of hearing</option>
        <option>Deaf with speech difficulty</option>
    </select>
</div>

<div class="form-row physical-only">
    <label>Physical Disability Type</label>
    <select name="physical_type">
        <option value="">-- Select --</option>
        <option>Wheelchair user</option>
        <option>Amputee</option>
        <option>Paralyzed</option>
        <option>Cerebral palsy</option>
        <option>Other physical disability</option>
    </select>
</div>

<div class="form-row">
    <label>Cause of Disability *</label>
    <select name="cause_of_disability" required>
        <option value="">-- Select --</option>
        <option>Congenital</option>
        <option>Illness</option>
        <option>Accident</option>
        <option>Work-related injury</option>
        <option>Age-related</option>
        <option>Others</option>
    </select>
</div>

<!-- EDUCATION -->
<div class="form-row">
    <label>Educational Level *</label>
    <select name="educational_level" required>
        <option>No formal education</option>
        <option>Elementary</option>
        <option>High School</option>
        <option>Senior High School</option>
        <option>Vocational / TESDA</option>
        <option>College Level</option>
        <option>College Graduate</option>
    </select>
</div>

<div class="form-row">
    <label>Employment Status *</label>
    <select name="employment_status" required>
        <option>Employed – Regular</option>
        <option>Employed – Part-time</option>
        <option>Self-employed</option>
        <option>Unemployed</option>
        <option>Student</option>
        <option>Unable to work</option>
    </select>
</div>

<!-- GUARDIAN -->
<div class="form-row">
    <label>Guardian Name</label>
    <input type="text" name="guardian_name">
</div>

<div class="form-row">
    <label>Guardian Relationship</label>
    <select name="guardian_relationship">
        <option value="">-- Select --</option>
        <option>Mother</option>
        <option>Father</option>
        <option>Sister</option>
        <option>Brother</option>
        <option>Spouse</option>
        <option>Relative</option>
        <option>Legal Guardian</option>
        <option>Others</option>
    </select>
</div>

<div class="form-row">
    <label>Guardian Contact</label>
    <input type="text" name="guardian_contact">
</div>

<!-- SKILLS -->
<fieldset class="form-row">
<legend>Skills</legend>

<?php foreach ($skillsByCat as $cat => $skills): ?>
<?php if ($skills): ?>
<div class="skill-group" data-cat="<?= $cat ?>">
<h4><?= $cat ?> Skills</h4>
<?php foreach ($skills as $s): ?>
<label>
<input type="checkbox" name="skills[]" value="<?= $s['skill_id'] ?>">
<?= htmlspecialchars($s['skill_name']) ?>
</label><br>
<?php endforeach; ?>
</div>
<?php endif; ?>
<?php endforeach; ?>

</fieldset>

<div class="form-actions">
    <button type="submit" class="btn primary-btn">Save PWD</button>
    <a href="pwd_list.php" class="btn secondary-btn">Cancel</a>
</div>

</form>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<script>
/* MUNICIPALITY → BARANGAY */
const barangays = <?= json_encode($antiqueBarangays) ?>;
const mun = document.getElementById('municipality');
const bgy = document.getElementById('barangay');

mun.addEventListener('change', () => {
    bgy.innerHTML = '<option value="">-- Select Barangay --</option>';
    if (barangays[mun.value]) {
        barangays[mun.value].forEach(x => {
            let o = document.createElement('option');
            o.value = x; o.textContent = x;
            bgy.appendChild(o);
        });
    }
});

/* DISABILITY + SKILLS FILTER */
const dc = document.getElementById('disability_category');
const skillGroups = document.querySelectorAll('.skill-group');
const blind = document.querySelector('.blind-only');
const deaf = document.querySelector('.deaf-only');
const phys = document.querySelector('.physical-only');

function updateUI() {
    blind.style.display = deaf.style.display = phys.style.display = 'none';

    if (dc.value === 'Blind') blind.style.display = 'block';
    if (dc.value === 'Deaf') deaf.style.display = 'block';
    if (dc.value === 'Physical Disability') phys.style.display = 'block';

    skillGroups.forEach(g => {
        const cat = g.dataset.cat;
        g.style.display = (!dc.value && cat === 'Any') ||
                          (dc.value && (cat === 'Any' || cat === dc.value))
                          ? 'block' : 'none';
    });
}
dc.addEventListener('change', updateUI);
updateUI();
</script>