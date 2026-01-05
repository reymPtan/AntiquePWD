<?php
require_once __DIR__ . '/lib/phpqrcode.php';

header('Content-Type: image/png');

// Increase size = 10
QRcode::png('Hello PWD QR Works!', null, QR_ECLEVEL_L, 10);