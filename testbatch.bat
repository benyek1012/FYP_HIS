@echo off

set TIMESTAMP=%DATE:~10,4%%DATE:~4,2%%DATE:~7,2%_%TIME:~0,2%_%TIME:~3,2%

"c:\xampp\mysql\bin\mysqldump.exe" -uSGHISadmin -p!9aj59hQX5l1SU5n -hlocalhost SGHIS> C:\xampp\mysql\bin\full_backup.sql
echo Done!
pause
