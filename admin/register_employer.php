<?php
// admin/register_employer.php
// Admin/Super Admin registers employer. Business Permit No will be auto-generated.
// With Employer Picture upload (JPG/PNG).

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireAdminRole(['SUPER', 'EMPLOYER_ADMIN']);

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

$pageTitle = 'Register Employer';

require_once __DIR__ . '/../includes/header.php';
?>

<section class="card">
    <header class="page-header">
        <h1 class="page-title">Register Employer</h1>
       
    </header>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <form action="register_employer_save.php"
          method="post"
          enctype="multipart/form-data"
          class="form-grid">

        <div class="form-row">
            <label>Business Permit No.</label>
           
        </div>

        <div class="form-row">
            <label for="business_name">Business Name / Trade Name <span class="required">*</span></label>
            <input type="text" id="business_name" name="business_name" required>
        </div>

        <div class="form-row">
            <label for="registered_owner">Registered Owner / Entity <span class="required">*</span></label>
            <input type="text" id="registered_owner" name="registered_owner" required>
        </div>

        <!-- EMPLOYER PICTURE -->
        <div class="form-row">
            <label for="employer_photo">Employer Logo / Picture <span class="required">*</span></label>
            <input type="file"
                   id="employer_photo"
                   name="employer_photo"
                   accept="image/jpeg,image/jpg,image/png"
           </div>



        <div class="form-row">
            <label for="province">Province <span class="required">*</span></label>
            <input type="text" id="province" name="province" value="Antique" required>
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
            <label for="type_of_business">Type / Nature of Business <span class="required">*</span></label>
            <input type="text" id="type_of_business" name="type_of_business" required>
        </div>

        <div class="form-row">
            <label for="line_of_business">Line of Business / Activity <span class="required">*</span></label>
            <input type="text" id="line_of_business" name="line_of_business" required>
        </div>

        <div class="form-row">
            <label for="valid_until">Valid Until (Permit expiry) <span class="required">*</span></label>
            <input type="date" id="valid_until" name="valid_until" required>
        </div>

        <div class="form-row">
            <label for="password">Initial Password <span class="required">*</span></label>
            <input type="text" id="password" name="password" value="employer123" required>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn primary-btn">Save Employer</button>
            <a href="employer_list.php" class="btn secondary-btn">Cancel</a>
        </div>

        <div class="page-actions">
            <a href="dashboard.php" class="btn link-btn">← Back to Admin Dashboard</a>
        </div>
    </form>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>