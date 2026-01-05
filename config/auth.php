<?php
// config/auth.php
// Handles session + access control for PWD, Employer, and Admin.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ==========================
   PWD AUTH
   ========================== */

function isPwdLoggedIn(): bool {
    return isset($_SESSION['pwd_number']);   // <-- VERY IMPORTANT
}

function requirePwdLogin(): void {
    if (!isPwdLoggedIn()) {
        header('Location: /pwd-employment-system/index.php');
        exit;
    }
}

/* ==========================
   EMPLOYER AUTH
   ========================== */

function isEmployerLoggedIn(): bool {
    return isset($_SESSION['business_permit_no']);
}

function requireEmployerLogin(): void {
    if (!isEmployerLoggedIn()) {
        header('Location: /pwd-employment-system/index.php');
        exit;
    }
}

/* ==========================
   ADMIN AUTH
   ========================== */

function isAdminLoggedIn(): bool {
    return isset($_SESSION['admin_id']);
}

function requireAdminRole(array $allowedRoles): void {
    if (!isAdminLoggedIn()) {
        header('Location: /pwd-employment-system/admin/login.php');
        exit;
    }

    $role = $_SESSION['admin_role'] ?? null;
    if (!in_array($role, $allowedRoles, true)) {
        // Not allowed → simple message or redirect
        echo 'You are not allowed to access this page.';
        exit;
    }
}

/* ==========================
   AUDIT LOG HELPER
   ========================== */

function addAuditLog(mysqli $conn, int $adminId, string $action, string $details = ''): void
{
    $sql = "INSERT INTO audit_logs (admin_id, action, details, created_at)
            VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log('Audit log prepare error: ' . $conn->error);
        return;
    }
    $stmt->bind_param('iss', $adminId, $action, $details);
    $stmt->execute();
    $stmt->close();
}