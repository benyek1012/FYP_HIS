<?php

return [
    'bsVersion' => '4.x',
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',


    'languages' => [
    'en' => 'English',
    'ms' => 'Bahasa Melayu'
    ],

    'borangdafter' => 'smb://LAPTOP-FO6A8DRV/BORANGDAFTAR',
    'chargesheet' => 'smb://LAPTOP-FO6A8DRV/CHARGESHEET',
    'casehistory' => 'smb://LAPTOP-FO6A8DRV/CASENOTE',
    'sticker' => 'smb://LAPTOP-FO6A8DRV/STICKER',
    'receipt' => 'smb://LAPTOP-FO6A8DRV/RECEIPT',
    'bill' => 'smb://LAPTOP-FO6A8DRV/OFFICIALBILL',
	'bill2' => 'smb://LAPTOP-FO6A8DRV/BILLRECEIPT',


    'borangdafter_offset' => 1, 
    'chargesheet_offset' => 2,
    'casehistory_offset' => 57,
    'sticker_offset' => 0,
    'receipt_offset' => 1,
    'bill_offset' => 1,

    'printerstatus' => 'false',  // anything beside true will not print
    'printeroverwritefont' => 'false', // set font of printer
];
