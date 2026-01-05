<?php
// admin/skills_form.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireAdminRole(['SUPER', 'PWD_ADMIN']);

$pageTitle = 'Add Skill';
$breadcrumbs = [
    ['label' => 'Admin Dashboard', 'url' => '/pwd-employment-system/admin/dashboard.php'],
    ['label' => 'Skills Masterlist', 'url' => '/pwd-employment-system/admin/skills_list.php'],
    ['label' => 'Add Skill'],
];

require_once __DIR__ . '/../includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $skillName = trim($_POST['skill_name'] ?? '');
    $desc      = trim($_POST['description'] ?? '');
    $cat       = $_POST['disability_category'] ?? 'Any';

    if ($skillName === '') {
        $error = 'Skill name is required.';
    } else {
        $sql = "INSERT INTO skills_master (skill_name, description, disability_category, is_active)
                VALUES (?, ?, ?, 1)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $error = 'Error preparing statement: ' . htmlspecialchars($conn->error);
        } else {
            $stmt->bind_param('sss', $skillName, $desc, $cat);
            if ($stmt->execute()) {
                $success = 'Skill added successfully.';
            } else {
                $error = 'Error saving skill.';
            }
            $stmt->close();
        }
    }
}
?>
<section class="card">
    <a href="skills_list.php" class="btn btn-secondary">← Back to Skills</a>
    <h1>Add Skill</h1>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post">
        <label>Skill Name</label>
        <input type="text" name="skill_name" required>

        <label>Description (optional)</label>
        <textarea name="description" rows="3"></textarea>

        <label>Disability Category</label>
        <select name="disability_category" required>
            <option value="Any">Any (All PWD)</option>
            <option value="Blind">Blind</option>
            <option value="Deaf">Deaf</option>
            <option value="Physical Disability">Physical Disability</option>
        </select>

        <button type="submit">Save Skill</button>
    </form>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>