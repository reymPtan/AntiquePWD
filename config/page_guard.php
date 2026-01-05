<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function deny() {
    http_response_code(403);
    exit('ACCESS DENIED');
}

function requireLogin() {
    if (!isset($_SESSION['user_id'], $_SESSION['role'], $_SESSION['__ACCESS_TOKEN'])) {
        deny();
    }
}

function requireRole(string $role) {
    requireLogin();
    if ($_SESSION['role'] !== $role) {
        session_destroy();
        deny();
    }
}