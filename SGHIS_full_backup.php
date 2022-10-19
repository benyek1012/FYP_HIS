<?php

$databases = ['sghis'];
$user = 'root'; #can be changed
$pass = '';
$host = 'localhost';

    date_default_timezone_set("Asia/Kuala_Lumpur"); #timezone
    
    if(!file_exists("C:/Users/joshl/Documents/Databases/")) { #creates the folder directory
      mkdir("C:/Users/joshl/Documents/Databases/");
    }

    foreach ($databases as $database) { #creates directory for the sql backup
      if(!file_exists("C:/Users/joshl/Documents/Databases/$database")) {
        mkdir("C:/Users/joshl/Documents/Databases/$database"); 
      }

      $filename = $database."_".date('F_d_Y').'@'.date('g_ia').uniqid("_", false); 
      $folder = "C:/Users/joshl/Documents/Databases/weekly/$database/" . $filename. '.sql'; #can be changed

      exec("C:/xampp/mysql/bin/mysqldump --user={$user} --password={$pass} --host={$host} {$database} --result-file={$folder}", $output);
    } 
	
print_r($output);