<?php

include 'vendor/autoload.php';

use neto737\xeonCAPTCHA;

if ($_GET['type'] === 'default') {
    $xeon = new xeonCAPTCHA(xeonCAPTCHA::IMG_PNG, false);

    $xeon->generateCAPTCHA(155, 30, 20, 5, 'xeonCAPTCHA', '', 22);
} else {
    $xeon = new xeonCAPTCHA(xeonCAPTCHA::IMG_PNG, true);

    $xeon->generateCAPTCHA(155, 30, 20, 5, 'xeonCAPTCHA', '', 22);
}