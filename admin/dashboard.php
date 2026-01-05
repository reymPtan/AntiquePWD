<?php
require_once __DIR__ . '/../config/auth.php';


$role = $_SESSION['admin_role'] ?? '';

switch ($role) {
    case 'SUPER':
        header('Location: /pwd-employment-system/admin/dashboard_super.php');
        break;

    case 'PWD_ADMIN':
        header('Location: /pwd-employment-system/admin/dashboard_pwd_admin.php');
        break;

    case 'EMPLOYER_ADMIN':
        header('Location: /pwd-employment-system/admin/dashboard_employer_admin.php');
        break;

    default:
        // safety fallback
        header('Location: /pwd-employment-system/admin/login.php');
}

exit;