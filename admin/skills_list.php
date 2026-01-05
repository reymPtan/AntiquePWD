<?php
// admin/skills_list.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireAdminRole(['SUPER', 'PWD_ADMIN']);

$pageTitle = 'Skills Masterlist';
$breadcrumbs = [
    ['label' => 'Admin Dashboard', 'url' => '/pwd-employment-system/admin/dashboard.php'],
    ['label' => 'Skills Masterlist'],
];

require_once __DIR__ . '/../includes/header.php';

$sql = "SELECT skill_id, skill_name, disability_category, is_active
        FROM skills_master
        ORDER BY disability_category, skill_name";
$res = $conn->query($sql);
?>
<section class="card">
    <a href="dashboard.php" class="btn btn-secondary">← Back</a>
    <h1>Skills Masterlist</h1>

    <p><a class="btn" href="skills_form.php">+ Add Skill</a></p>

    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Skill Name</th>
                <th>Disability Category</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($res && $res->num_rows > 0): ?>
            <?php while ($row = $res->fetch_assoc()): ?>
                <tr>
                    <td><?= (int)$row['skill_id'] ?></td>
                    <td><?= htmlspecialchars($row['skill_name']) ?></td>
                    <td><?= htmlspecialchars($row['disability_category']) ?></td>
                    <td><?= $row['is_active'] ? 'Active' : 'Inactive' ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4">No skills found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>