<?php
// index.php
// SINGLE LOGIN FOR:
//  - PWD (with OTP)
//  - Employer
//  - Admin (SUPER / PWD_ADMIN / EMPLOYER_ADMIN)

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$loginError = '';
$prefillId  = $_GET['qr_pwd'] ?? '';

// ==========================
// HANDLE LOGIN SUBMIT
// ==========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $loginId  = trim($_POST['login_id'] ?? '');
    $password = $_POST['password'] ?? '';
    $prefillId = $loginId;

    if ($loginId === '' || $password === '') {
        $loginError = 'Login ID and password are required.';
    } else {

        // PATTERNS
        $pwdPattern = '/^06-[0-9]{2}-[0-9]{4}-[0-9]{7}$/';
        $empPattern = '/^[0-9]{4}-[A-Z]{3}-[0-9]{6}$/';

        // =====================================================
        // 1️⃣ PWD LOGIN (WITH OTP)
        // =====================================================
        if (preg_match($pwdPattern, $loginId)) {

            $stmt = $conn->prepare(
                "SELECT pwd_number, full_name, password_hash, disability_category
                 FROM pwd_profiles
                 WHERE pwd_number = ?"
            );
            $stmt->bind_param('s', $loginId);
            $stmt->execute();
            $res = $stmt->get_result();
            $pwd = $res->fetch_assoc();
            $stmt->close();

            if ($pwd && password_verify($password, $pwd['password_hash'])) {

                // =============================
                // OTP GENERATION (DEMO)
                // =============================
                $otp = random_int(100000, 999999);
                $otpHash = password_hash($otp, PASSWORD_DEFAULT);
                $expires = date('Y-m-d H:i:s', strtotime('+5 minutes'));

                $stmtOtp = $conn->prepare(
                    "INSERT INTO pwd_otp (pwd_number, otp_hash, expires_at)
                     VALUES (?, ?, ?)"
                );
                $stmtOtp->bind_param('sss', $pwd['pwd_number'], $otpHash, $expires);
                $stmtOtp->execute();
                $stmtOtp->close();

                // =============================
                // TEMP OTP SESSION
                // =============================
                $_SESSION['OTP_PWD']        = $pwd['pwd_number'];
                $_SESSION['OTP_FULL_NAME'] = $pwd['full_name'];
                $_SESSION['OTP_DISABILITY']= $pwd['disability_category'];
                $_SESSION['OTP_DEMO']      = $otp; // DEMO ONLY

                header('Location: /pwd-employment-system/pwd/otp_verify.php');
                exit;

            } else {
                $loginError = 'Invalid PWD ID or password.';
            }

        // =====================================================
        // 2️⃣ EMPLOYER LOGIN
        // =====================================================
        } elseif (preg_match($empPattern, $loginId)) {

            $stmt = $conn->prepare(
                "SELECT business_permit_no, business_name, password_hash
                 FROM employers
                 WHERE business_permit_no = ? AND is_active = 1"
            );
            $stmt->bind_param('s', $loginId);
            $stmt->execute();
            $res = $stmt->get_result();
            $emp = $res->fetch_assoc();
            $stmt->close();

            if ($emp && password_verify($password, $emp['password_hash'])) {
                $_SESSION['business_permit_no'] = $emp['business_permit_no'];
                $_SESSION['full_name']          = $emp['business_name'];

                header('Location: /pwd-employment-system/employer/dashboard.php');
                exit;
            } else {
                $loginError = 'Invalid Employer credentials.';
            }

        // =====================================================
        // 3️⃣ ADMIN LOGIN
        // =====================================================
        } else {

            $stmt = $conn->prepare(
                "SELECT admin_id, full_name, role, password_hash
                 FROM admin_users
                 WHERE username = ? AND is_active = 1"
            );
            $stmt->bind_param('s', $loginId);
            $stmt->execute();
            $res = $stmt->get_result();
            $admin = $res->fetch_assoc();
            $stmt->close();

            if ($admin && password_verify($password, $admin['password_hash'])) {

                $_SESSION['admin_id']   = (int)$admin['admin_id'];
                $_SESSION['full_name']  = $admin['full_name'];
                $_SESSION['role_label'] = $admin['role'];

                if ($admin['role'] === 'SUPER') {
                    header('Location: /pwd-employment-system/admin/dashboard_super.php');
                } elseif ($admin['role'] === 'PWD_ADMIN') {
                    header('Location: /pwd-employment-system/admin/dashboard_pwd_admin.php');
                } else {
                    header('Location: /pwd-employment-system/admin/dashboard_employer_admin.php');
                }
                exit;

            } else {
                $loginError = 'Invalid admin credentials.';
            }
        }
    }
}

// ==========================
// RENDER LOGIN PAGE
// ==========================
$pageTitle = 'PWD Employment Information System – Antique';
require_once __DIR__ . '/includes/header.php';
?>

<section class="card">

    <h2>System Login</h2>

    <?php if ($loginError): ?>
        <div class="alert alert-error"><?= htmlspecialchars($loginError) ?></div>
    <?php endif; ?>

    <form method="post">
        <label>Login ID / Username</label>
        <input type="text" name="login_id" value="<?= htmlspecialchars($prefillId) ?>" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>

    <p class="small-note">
        🧾 PWD QR Login:<br>
        <a href="/pwd-employment-system/pwd/qr_login_scan.php">Scan QR</a> |
        <a href="/pwd-employment-system/pwd/qr_login_upload.php">Upload QR</a>
    </p>

</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>