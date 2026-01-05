<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['OTP_PWD'])) {
    header('Location: /pwd-employment-system/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $otpInput = trim($_POST['otp'] ?? '');

    if ($otpInput === '') {
        $error = 'OTP is required.';
    } else {

        $stmt = $conn->prepare(
            "SELECT otp_id, otp_hash, expires_at
             FROM pwd_otp
             WHERE pwd_number = ? AND is_used = 0
             ORDER BY otp_id DESC
             LIMIT 1"
        );
        $stmt->bind_param('s', $_SESSION['OTP_PWD']);
        $stmt->execute();
        $res = $stmt->get_result();
        $otp = $res->fetch_assoc();
        $stmt->close();

        if (!$otp) {
            $error = 'No OTP found.';
        } elseif (strtotime($otp['expires_at']) < time()) {
            $error = 'OTP expired.';
        } elseif (!password_verify($otpInput, $otp['otp_hash'])) {
            $error = 'Invalid OTP.';
        } else {

            // MARK OTP USED
            $stmt = $conn->prepare("UPDATE pwd_otp SET is_used = 1 WHERE otp_id = ?");
            $stmt->bind_param('i', $otp['otp_id']);
            $stmt->execute();
            $stmt->close();

            // FINAL LOGIN SESSION
            $_SESSION['pwd_number']          = $_SESSION['OTP_PWD'];
            $_SESSION['full_name']           = $_SESSION['OTP_FULL_NAME'];
            $_SESSION['disability_category'] = $_SESSION['OTP_DISABILITY'];

            unset($_SESSION['OTP_PWD'], $_SESSION['OTP_FULL_NAME'], $_SESSION['OTP_DISABILITY'], $_SESSION['OTP_DEMO']);

            // REDIRECT BY DISABILITY
            if ($_SESSION['disability_category'] === 'Blind') {
                header('Location: /pwd-employment-system/pwd/dashboard_blind.php');
            } elseif ($_SESSION['disability_category'] === 'Deaf') {
                header('Location: /pwd-employment-system/pwd/dashboard_deaf.php');
            } else {
                header('Location: /pwd-employment-system/pwd/dashboard_physical.php');
            }
            exit;
        }
    }
}

$pageTitle = 'OTP Verification';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="card">
    <h2>OTP Verification</h2>

    <p><strong>Demo OTP:</strong> <?= htmlspecialchars($_SESSION['OTP_DEMO']) ?></p>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <label>Enter OTP</label>
        <input type="text" name="otp" maxlength="6" required>
        <button type="submit">Verify OTP</button>
    </form>

    <p class="small-note">
        ⏱ OTP valid for 5 minutes (demo only).
    </p>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>