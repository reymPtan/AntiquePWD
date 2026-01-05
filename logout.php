<?php
require_once __DIR__ . '/config/auth.php';
session_unset();
session_destroy();
header('Location: /pwd-employment-system/index.php');
exit;
