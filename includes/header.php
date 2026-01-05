<?php
if (!isset($pageTitle)) {
    $pageTitle = 'PWD Employment Information System – Antique';
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userName  = $_SESSION['full_name'] ?? null;
$roleLabel = $_SESSION['role_label'] ?? null;
$disabilityCategory = $_SESSION['disability_category'] ?? null;

/* =======================
   ✅ ADDED: ADMIN DETECTION
   ======================= */
$isAdmin = isset($_SESSION['admin_id']);
$adminRole = $_SESSION['admin_role'] ?? null;

/* =====================================================
   DETECT CURRENT PAGE (FOR DASHBOARD BUTTON LOGIC)
   ===================================================== */
$currentPage = basename($_SERVER['PHP_SELF']);

$isDashboardPage = in_array($currentPage, [
    // PWD dashboards
    'dashboard_blind.php',
    'dashboard_deaf.php',
    'dashboard_physical.php',

    // ✅ ADDED: ADMIN dashboards
    'dashboard_super.php',
    'dashboard_pwd_admin.php',
    'dashboard_employer_admin.php',
    'dashboard.php' // router
]);

/* =====================================================
   THEME CSS BASED ON DISABILITY
   ===================================================== */
$themeCss = null;
if ($disabilityCategory === 'Blind') {
    $themeCss = '/pwd-employment-system/assets/css/theme_blind.css';
} elseif ($disabilityCategory === 'Deaf') {
    $themeCss = '/pwd-employment-system/assets/css/theme_deaf.css';
} elseif ($disabilityCategory === 'Physical Disability') {
    $themeCss = '/pwd-employment-system/assets/css/theme_physical.css';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- BASE UI -->
    <link rel="stylesheet" href="/pwd-employment-system/assets/css/base.css">

    <!-- DISABILITY THEME -->
    <?php if ($themeCss): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($themeCss) ?>">
    <?php endif; ?>
</head>

<body class="app-shell">

<header class="topbar" role="banner">
    <div class="topbar-inner">

        <!-- LEFT : BRAND -->
        <div class="topbar-brand">
            <span>Province of Antique</span>
            <span>PWD Employment Information System</span>
        </div>

        <!-- =================================================
             CENTER : DASHBOARD BUTTON (PWD + ADMIN)
             ================================================= -->

        <!-- ✅ ADMIN DASHBOARD BUTTON -->
        <?php if ($isAdmin && !$isDashboardPage): ?>
            <nav class="topbar-nav" aria-label="Admin dashboard navigation">
                <a href="/pwd-employment-system/admin/dashboard.php"
                
                
                </a>
            </nav>
        <?php endif; ?>

        <!-- ✅ EXISTING PWD DASHBOARD BUTTON (UNCHANGED) -->
        <?php if ($userName && !$isAdmin && !$isDashboardPage && $disabilityCategory): ?>
            <nav class="topbar-nav" aria-label="PWD dashboard navigation">
                <?php if ($disabilityCategory === 'Blind'): ?>
                    <a href="/pwd-employment-system/pwd/dashboard_blind.php"
                    
                       aria-label="Back to Blind Dashboard">
                      
                    </a>
                <?php elseif ($disabilityCategory === 'Deaf'): ?>
                    <a href="/pwd-employment-system/pwd/dashboard_deaf.php"
                      
                    </a>
                <?php else: ?>
                    <a href="/pwd-employment-system/pwd/dashboard_physical.php"
                      
                    </a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>

        <!-- RIGHT : USER ACTIONS -->
        <?php if ($userName || $isAdmin): ?>
            <div class="topbar-user" aria-label="User status">

                <div>
                    <div class="topbar-user-name">
                        <?= htmlspecialchars($userName ?? $_SESSION['admin_name'] ?? 'Administrator') ?>
                    </div>
                    <div style="font-size:0.8rem;color:#6b7280;">
                        <?= htmlspecialchars($roleLabel ?? $adminRole) ?>
                    </div>
                </div>

                <a href="/pwd-employment-system/logout.php"
                   class="btn btn-danger btn-sm">
                    Logout
                </a>

            </div>
        <?php endif; ?>

    </div>
</header>

<main class="app-main">

<?php
/* =====================================================
   BLIND TTS GLOBAL INCLUDE (UNCHANGED)
   ===================================================== */
if ($disabilityCategory === 'Blind') {
    require_once __DIR__ . '/blind_tts.php';
}
?>