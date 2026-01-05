<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

$loginError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare('SELECT admin_id, full_name, role, password_hash FROM admin_users WHERE username = ? AND is_active = 1');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    if ($row && password_verify($password, $row['password_hash'])) {
        $_SESSION['admin_id'] = $row['admin_id'];
        $_SESSION['admin_role'] = $row['role'];
        $_SESSION['full_name'] = $row['full_name'];

        if ($row['role'] === 'SUPER') {
            header('Location: dashboard_super.php');
        } elseif ($row['role'] === 'PWD_ADMIN') {
            header('Location: dashboard_pwd_admin.php');
        } else {
            header('Location: dashboard_employer_admin.php');
        }
        exit;
    } else {
        $loginError = 'Invalid admin username or password.';
    }
}

$pageTitle = 'Admin Login';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="card">
    <h2>Admin Login</h2>
    <?php if ($loginError): ?>
        <div class="alert alert-error"><?= htmlspecialchars($loginError) ?></div>
    <?php endif; ?>
    <form method="post">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" required>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Login</button>
    </form>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
