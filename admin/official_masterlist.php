<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
requireAdminRole(['SUPER']);

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pwdNumber = trim($_POST['pwd_number'] ?? '');
    $fullName  = trim($_POST['full_name'] ?? '');
    $birthdate = $_POST['birthdate'] ?? '';
    $status    = $_POST['status'] ?? 'Active';

    $pwdPattern = '/^06-[0-9]{2}-[0-9]{4}-[0-9]{7}$/';

    if (!preg_match($pwdPattern, $pwdNumber)) {
        $errors[] = 'PWD Number format is invalid.';
    }
    if ($fullName === '') {
        $errors[] = 'Full name is required.';
    }
    if ($birthdate === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthdate)) {
        $errors[] = 'Birthdate is required in YYYY-MM-DD.';
    }
    if (!in_array($status, ['Active','Expired','Blocked'], true)) {
        $errors[] = 'Invalid status.';
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("
            INSERT INTO official_pwd_ids (pwd_number, full_name, birthdate, status)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE full_name = VALUES(full_name),
                                    birthdate = VALUES(birthdate),
                                    status = VALUES(status)
        ");
        $stmt->bind_param('ssss', $pwdNumber, $fullName, $birthdate, $status);
        if ($stmt->execute()) {
            $success = 'Record saved/updated successfully.';
        } else {
            $errors[] = 'Database error: ' . $conn->error;
        }
    }
}

$res = $conn->query('SELECT pwd_number, full_name, birthdate, status, created_at FROM official_pwd_ids ORDER BY created_at DESC');

$pageTitle = 'Official PWD Masterlist';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="card">
    <h2>Official PWD Masterlist</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <h3>Add / Update Entry</h3>
    <form method="post">
        <label for="pwd_number">PWD Number</label>
        <input type="text" id="pwd_number" name="pwd_number" required>

        <label for="full_name">Full Name (as in masterlist)</label>
        <input type="text" id="full_name" name="full_name" required>

        <label for="birthdate">Birthdate (YYYY-MM-DD)</label>
        <input type="date" id="birthdate" name="birthdate" required>

        <label for="status">Status</label>
        <select id="status" name="status">
            <option value="Active">Active</option>
            <option value="Expired">Expired</option>
            <option value="Blocked">Blocked</option>
        </select>

        <button type="submit">Save to Masterlist</button>
    </form>

    <h3>Existing Entries</h3>
    <table class="table">
        <thead>
        <tr>
            <th>PWD Number</th>
            <th>Full Name</th>
            <th>Birthdate</th>
            <th>Status</th>
            <th>Created At</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($res && $res->num_rows > 0): ?>
            <?php while ($row = $res->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['pwd_number']) ?></td>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= htmlspecialchars($row['birthdate']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">No records in the masterlist yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
