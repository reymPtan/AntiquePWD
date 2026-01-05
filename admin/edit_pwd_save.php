<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireAdminRole(['SUPER','PWD_ADMIN']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') die('Invalid request');

$sql = "UPDATE pwd_profiles SET
            full_name = ?,
            sex = ?,
            birthdate = ?,
            municipality = ?,
            address = ?,
            contact_number = ?,
            employment_status = ?
        WHERE pwd_number = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    'ssssssss',
    $_POST['full_name'],
    $_POST['sex'],
    $_POST['birthdate'],
    $_POST['municipality'],
    $_POST['address'],
    $_POST['contact_number'],
    $_POST['employment_status'],
    $_POST['pwd_number']
);

$stmt->execute();
$stmt->close();

header('Location: pwd_list.php');
exit;