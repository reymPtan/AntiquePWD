<?php
require_once __DIR__ . '/config/database.php';
session_start();

$idNumber = $_POST['id_number'] ?? '';

$sql = "SELECT user_id, role, full_name, disability_category 
        FROM users WHERE id_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $idNumber);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    exit('Invalid ID');
}

// SESSION INIT
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['role'] = $user['role'];
$_SESSION['full_name'] = $user['full_name'];
$_SESSION['disability_category'] = $user['disability_category'] ?? null;
$_SESSION['__ACCESS_TOKEN'] = bin2hex(random_bytes(32));

// REDIRECT TO ROUTER
header('Location: router.php');
exit;