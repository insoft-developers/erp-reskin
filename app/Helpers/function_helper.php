<?php

use SimpleSoftwareIO\QrCode\Facades\QrCode;

function generate_qrcode($param)
{
    $qr = QrCode::size(200)
        // ->gradient($from[0], $from[1], $from[2], $to[0], $to[1], $to[2], 'diagonal')
        ->margin(1)
        ->generate($param);
    return $qr;
}
