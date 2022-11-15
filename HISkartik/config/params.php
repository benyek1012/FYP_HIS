<?php

return [
    'bsVersion' => '4.x',
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',


    'languages' => [
    'en' => 'English',
    'ms' => 'Bahasa Melayu'
    ],

    'borangdafter' => 'smb://LT-CPTING/EPSON',  //smb://JOSH2-LAPTOP/EPSON    , smb://DESKTOP-7044BNO/Epson JOSH-PC/EPSON
    'chargesheet' => 'smb://LT-CPTING/EPSON',
    'casehistory' => 'smb://LT-CPTING/EPSON',
    'sticker' => 'smb://LT-CPTING/EPSON',
    'receipt' => 'smb://LT-CPTING/EPSON',
    'bill' => 'smb://LT-CPTING/EPSON',

    'borangdafter_offset' => 0, 
    'chargesheet_offset' => 0,
    'casehistory_offset' => 0,
    'sticker_offset' => 0,
    'receipt_offset' => 0,
    'bill_offset' => 0,

    'printerstatus' => 'false',  // anything beside true will not print
    'printeroverwritefont' => 'false', // set font of printer
];
