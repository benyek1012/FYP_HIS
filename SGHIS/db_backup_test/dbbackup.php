<?php
$bklac = "C:\\xampp\htdocs\\HISkartik\\db_backup_test\\db_backup";
$fileName = date("Ymd_His") . ".sql";
$location = $bklac . $fileName;
echo "DB backup is started!" . PHP_EOL;

exec("");

echo "DB backup is done!";