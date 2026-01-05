<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireAdminRole(['PWD_ADMIN','SUPER']);

$pwdNumber = $_GET['pwd_number'] ?? '';
if (!$pwdNumber) die('Invalid request');

$sql = "SELECT 
            pwd_number,
            full_name,
            sex,
            birthdate,
            municipality,
            province,
            disability_category,
            pwd_photo_front
        FROM pwd_profiles
        WHERE pwd_number = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $pwdNumber);
$stmt->execute();
$res = $stmt->get_result();
$pwd = $res->fetch_assoc();
$stmt->close();

if (!$pwd) die('PWD not found');

$photo = $pwd['pwd_photo_front']
    ? '../' . ltrim($pwd['pwd_photo_front'], '/')
    : '../assets/img/no-photo.png';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>PWD ID</title>

<style>
body{
    font-family: Arial, sans-serif;
    background:#e5e7eb;
}

/* ID SIZE (National ID proportion) */
.id-wrap{
    display:flex;
    gap:30px;
    justify-content:center;
    margin-top:20px;
}

.id-card{
    width:345px;
    height:215px;
    background:#fff;
    border-radius:10px;
    border:1px solid #111;
    overflow:hidden;
}

/* ================= FRONT ================= */
.front-header{
    display:flex;
    align-items:center;
    padding:6px 10px;
    background:linear-gradient(90deg,#1e3a8a,#2563eb);
    color:#fff;
    font-size:11px;
    font-weight:bold;
}

.front-header img{
    height:28px;
    margin-right:8px;
}

.front-body{
    display:flex;
    padding:10px;
}

.photo img{
    width:95px;
    height:115px;
    object-fit:cover;
    border:1px solid #000;
}

.details{
    margin-left:10px;
    font-size:11px;
    width:100%;
}

.details .name{
    font-size:14px;
    font-weight:bold;
    margin-bottom:4px;
}

.details div{
    margin-bottom:3px;
}

.label{
    font-weight:bold;
}

.front-footer{
    border-top:1px solid #000;
    padding:4px;
    font-size:9px;
    text-align:center;
}

/* ================= BACK ================= */
.back-header{
    background:linear-gradient(90deg,#16a34a,#22c55e);
    color:#fff;
    font-size:12px;
    text-align:center;
    font-weight:bold;
    padding:6px;
}

.back-body{
    padding:10px;
    font-size:11px;
}

.back-body ul{
    padding-left:16px;
    margin:6px 0;
}

.back-body li{
    margin-bottom:4px;
}

.back-footer{
    border-top:1px solid #000;
    padding:4px;
    font-size:9px;
    text-align:center;
}
</style>
</head>

<body onload="window.print()">

<div class="id-wrap">

<!-- ================= FRONT ID ================= -->
<div class="id-card">

    <div class="front-header">
        REPUBLIC OF THE PHILIPPINES<br>
        PERSON WITH DISABILITY IDENTIFICATION CARD
    </div>

    <div class="front-body">
        <div class="photo">
            <img src="<?= htmlspecialchars($photo) ?>">
        </div>

        <div class="details">
            <div class="name"><?= htmlspecialchars($pwd['full_name']) ?></div>
            <div><span class="label">PWD No:</span> <?= $pwd['pwd_number'] ?></div>
            <div><span class="label">Sex:</span> <?= $pwd['sex'] ?></div>
            <div><span class="label">Birthdate:</span> <?= $pwd['birthdate'] ?></div>
            <div><span class="label">Disability:</span> <?= $pwd['disability_category'] ?></div>
            <div><span class="label">Address:</span>
                <?= $pwd['municipality'] . ', ' . $pwd['province'] ?>
            </div>
        </div>
    </div>

    <div class="front-footer">
        Issued by the Provincial Government of Antique
    </div>

</div>

<!-- ================= BACK ID ================= -->
<div class="id-card">

    <div class="back-header">
        IMPORTANT INFORMATION
    </div>

    <div class="back-body">
        <strong>This card certifies that the holder is a Person With Disability (PWD).</strong>

        <ul>
            <li>Entitled to privileges under RA 7277 & RA 10754</li>
            <li>Subject to verification by authorized personnel</li>
            <li>Non-transferable</li>
        </ul>

        <p>
            If found, please return to the nearest LGU – Province of Antique.
        </p>
    </div>

    <div class="back-footer">
        PWD Employment Information System – Antique
    </div>

</div>

</div>

</body>
</html>