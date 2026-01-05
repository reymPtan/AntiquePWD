<?php
// includes/notify.php
// CENTRAL NOTIFICATION HELPER – COMPLETE

function notifyPwd(
    mysqli $conn,
    string $pwdNumber,
    string $title,
    string $message,
    ?string $link = null
): void {
    $sql = "INSERT INTO notifications
            (user_type, pwd_number, title, message, link, is_read, created_at)
            VALUES ('PWD', ?, ?, ?, ?, 0, NOW())";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log('notifyPwd error: ' . $conn->error);
        return;
    }
    $stmt->bind_param('ssss', $pwdNumber, $title, $message, $link);
    $stmt->execute();
    $stmt->close();
}

function notifyEmployer(
    mysqli $conn,
    int $employerId,
    string $title,
    string $message,
    ?string $link = null
): void {
    $sql = "INSERT INTO notifications
            (user_type, employer_id, title, message, link, is_read, created_at)
            VALUES ('EMPLOYER', ?, ?, ?, ?, 0, NOW())";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log('notifyEmployer error: ' . $conn->error);
        return;
    }
    $stmt->bind_param('isss', $employerId, $title, $message, $link);
    $stmt->execute();
    $stmt->close();
}

function notifyAdmin(
    mysqli $conn,
    int $adminId,
    string $title,
    string $message,
    ?string $link = null
): void {
    $sql = "INSERT INTO notifications
            (user_type, admin_id, title, message, link, is_read, created_at)
            VALUES ('ADMIN', ?, ?, ?, ?, 0, NOW())";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log('notifyAdmin error: ' . $conn->error);
        return;
    }
    $stmt->bind_param('isss', $adminId, $title, $message, $link);
    $stmt->execute();
    $stmt->close();
}