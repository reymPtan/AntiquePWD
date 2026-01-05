<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
requireAdminRole(['SUPER']);

$res = $conn->query("
    SELECT l.log_id, l.action, l.details, l.created_at,
           a.full_name AS admin_name, a.role
    FROM audit_logs l
    JOIN admin_users a ON l.admin_id = a.admin_id
    ORDER BY l.created_at DESC
    LIMIT 100
");

$pageTitle = 'Audit Logs';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="card">
    <h2>Audit Logs (Last 100 Actions)</h2>
    <table class="table">
        <thead>
        <tr>
            <th>Time</th>
            <th>Admin</th>
            <th>Role</th>
            <th>Action</th>
            <th>Details</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($res && $res->num_rows > 0): ?>
            <?php while ($row = $res->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                    <td><?= htmlspecialchars($row['admin_name']) ?></td>
                    <td><?= htmlspecialchars($row['role']) ?></td>
                    <td><?= htmlspecialchars($row['action']) ?></td>
                    <td><?= htmlspecialchars($row['details']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">No audit logs yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
