<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireAdminRole(['SUPER','PWD_ADMIN']);

$pwdNumber = $_GET['pwd_number'] ?? '';
if (!$pwdNumber) die('Missing PWD number.');

$stmt = $conn->prepare("SELECT * FROM pwd_profiles WHERE pwd_number = ?");
$stmt->bind_param('s', $pwdNumber);
$stmt->execute();
$pwd = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$pwd) die('PWD not found.');

$pageTitle = 'Edit PWD Profile';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="card">
    <header class="page-header">
        <h1 class="page-title">Edit PWD Profile</h1>
        <p class="page-subtitle"><?= htmlspecialchars($pwdNumber) ?></p>
    </header>

    <form method="post" action="edit_pwd_save.php" class="form-grid">

        <input type="hidden" name="pwd_number" value="<?= htmlspecialchars($pwdNumber) ?>">

        <div class="form-row">
            <label>Full Name</label>
            <input type="text" name="full_name" value="<?= htmlspecialchars($pwd['full_name']) ?>" required>
        </div>

        <div class="form-row">
            <label>Sex</label>
            <select name="sex">
                <?php foreach (['Male','Female','Prefer not to say'] as $s): ?>
                    <option value="<?= $s ?>" <?= $pwd['sex']===$s?'selected':'' ?>><?= $s ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-row">
            <label>Birthdate</label>
            <input type="date" name="birthdate" value="<?= $pwd['birthdate'] ?>">
        </div>

        <div class="form-row">
            <label>Municipality</label>
            <input type="text" name="municipality" value="<?= htmlspecialchars($pwd['municipality']) ?>">
        </div>

        <div class="form-row">
            <label>Address</label>
            <textarea name="address"><?= htmlspecialchars($pwd['address']) ?></textarea>
        </div>

        <div class="form-row">
            <label>Contact</label>
            <input type="text" name="contact_number" value="<?= htmlspecialchars($pwd['contact_number']) ?>">
        </div>

        <div class="form-row">
            <label>Employment Status</label>
            <select name="employment_status">
                <?php
                $opts = [
                    'Employed – Regular','Employed – Part-time',
                    'Self-employed','Unemployed','Student','Unable to work'
                ];
                foreach ($opts as $o):
                ?>
                    <option value="<?= $o ?>" <?= $pwd['employment_status']===$o?'selected':'' ?>>
                        <?= $o ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-actions">
            <button class="btn primary-btn">Save Changes</button>
            <a href="pwd_list.php" class="btn secondary-btn">Cancel</a>
        </div>

    </form>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>