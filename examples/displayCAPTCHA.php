<?php

include 'vendor/autoload.php';

use neto737\xeonCAPTCHA;

$type = isset($_GET['type']) ? $_GET['type'] : 'default';

if ($type === 'math') {
    $xeon = new xeonCAPTCHA(xeonCAPTCHA::IMG_PNG, true);
} else {
    $xeon = new xeonCAPTCHA(xeonCAPTCHA::IMG_PNG, false);
}

$xeon->generateCAPTCHA(155, 30, 20, 5, 'xeonCAPTCHA', '', 22);