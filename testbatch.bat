@echo off

set CUR_YYYY=%date:~10,4%
set CUR_MM=%date:~4,2%
set CUR_DD=%date:~7,2%
set CUR_HH=%time:~0,2%
if %CUR_HH% lss 10 (set CUR_HH=0%time:~1,1%)

set CUR_NN=%time:~3,2%
set CUR_SS=%time:~6,2%

set SUBFILENAME=%CUR_YYYY%%CUR_MM%%CUR_DD%-%CUR_HH%%CUR_NN%%CUR_SS%

"c:\xampp\mysql\bin\mysqldump.exe" -uSGHISadmin -p!9aj59hQX5l1SU5n -hlocalhost SGHIS > D:\DB\SGHIS_FULLBACKUP_%SUBFILENAME%.sql
"c:\xampp\mysql\bin\mysqldump.exe" -uSGHISadmin -p!9aj59hQX5l1SU5n -hlocalhost SGHIS > C:\Users\joshl\Desktop\DB\SGHIS_FULLBACKUP_%SUBFILENAME%.sql
echo Done!
pause
exit