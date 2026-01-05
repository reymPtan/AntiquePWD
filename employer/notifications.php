<?php
// employer/notifications.php
// Employer Notifications Page (FIXED VERSION)

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireEmployerLogin();

// ✅ USE BUSINESS PERMIT NO (NOT employer_id)
$permitNo = $_SESSION['business_permit_no'] ?? '';

if (!$permitNo) {
    exit('Employer session missing.');
}

$pageTitle = 'Employer Notifications';

// ================================
// LOAD NOTIFICATIONS
// ================================
$sql = "
    SELECT
        notification_id,
        title,
        message,
        link,
        is_read,
        created_at
    FROM notifications
    WHERE user_type = 'EMPLOYER'
      AND business_permit_no = ?
    ORDER BY created_at DESC
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    exit('Prepare failed: ' . $conn->error);
}

$stmt->bind_param('s', $permitNo);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

require_once __DIR__ . '/../includes/header.php';
?>

<section class="card">
    <h2>Employer Notifications</h2>

    <?php if ($result->num_rows === 0): ?>
        <p class="small-note">No notifications yet.</p>
    <?php else: ?>
        <ul class="notification-list">
            <?php while ($row = $result->fetch_assoc()): ?>
                <li class="notification-item <?= $row['is_read'] ? 'read' : 'unread' ?>">
                    <strong><?= htmlspecialchars($row['title']) ?></strong>
                    <span><?= htmlspecialchars($row['message']) ?></span>
                    <small><?= htmlspecialchars($row['created_at']) ?></small>

                    <?php if (!empty($row['link'])): ?>
                        <a href="<?= htmlspecialchars($row['link']) ?>">Open</a>
                    <?php endif; ?>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>