<?php
require_once __DIR__ . '/../config/database.php';

function createAdmin($username, $password, $name, $role, $conn) {
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
        INSERT INTO admin_users 
        (username, password_hash, full_name, role, is_active)
        VALUES ( ?, ?, ?, ?, 1 )
    ");

    $stmt->bind_param("ssss", $username, $hash, $name, $role);
    $stmt->execute();

    echo "Created admin: <b>$username</b> [$role] with password <b>$password</b><br>";
}

/* ========== CREATE ADMINS ========== */

createAdmin(
    "pwdadmin",
    "PwdAdmin123!",
    "PWD Registration Admin",
    "PWD_ADMIN",
    $conn
);

createAdmin(
    "employeradmin",
    "EmployerAdmin123!",
    "Employer Registration Admin",
    "EMPLOYER_ADMIN",
    $conn
);

echo "<hr>✅ DONE! Delete this file after use.";
