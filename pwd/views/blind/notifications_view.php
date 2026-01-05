<?php
// pwd/views/blind/notifications_view.php
?>
<section class="card blind-notifications">
    <h1 class="page-title">Notifications (Blind)</h1>

    <?php if (empty($notifications)): ?>
        <p tabindex="0">You have no notifications.</p>
    <?php else: ?>
        <ul class="notification-list">
            <?php foreach ($notifications as $n): ?>
                <?php
                    $itemClass = 'notification-item';
                    if (isset($n['is_read']) && (int)$n['is_read'] === 0) {
                        $itemClass .= ' unread';
                    } else {
                        $itemClass .= ' read';
                    }
                ?>
                <li class="<?= $itemClass ?>" tabindex="0">
                    <strong><?= htmlspecialchars($n['title']) ?></strong>
                    <span><?= nl2br(htmlspecialchars($n['message'])) ?></span>
                    <small><?= htmlspecialchars($n['created_at']) ?></small>

                    <?php if (!empty($n['link'])): ?>
                        <a href="<?= htmlspecialchars($n['link']) ?>">Open related page</a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>