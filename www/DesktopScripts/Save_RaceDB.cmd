echo off


:getpath
   set /p path=Enter the path to save the database file: 
   IF [%path%]==[] (CALL :getpath ) ELSE (CALL :getfile)
   EXIT /b

:getfile
   set /p file=Enter the name for the sql file: 
   IF [%file%]==[] (CALL :getfile ) ELSE (CALL :savesql)
   EXIT /b


:savesql
   set CUR_YYYY=%date:~10,4%
   set CUR_MM=%date:~4,2%
   set CUR_DD=%date:~7,2%
   set CUR_HH=%time:~0,2%
   if %CUR_HH% lss 10 (set CUR_HH=0%time:~1,1%)

   set CUR_NN=%time:~3,2%
   set CUR_SS=%time:~6,2%
   set CUR_MS=%time:~9,2%

   echo Saving mysql db to Dropbox %path%\%file%_%CUR_YYYY%-%CUR_MM%-%CUR_DD%_%CUR_HH%-%CUR_NN%.sql
   c:\MAMP\bin\mysql\bin\mysqldump --defaults-extra-file=c:\Users\smg\Dropbox\CES\TimingDocs\config.cnf lcsportident_events > %path%\%file%_%CUR_YYYY%-%CUR_MM%-%CUR_DD%_%CUR_HH%.%CUR_NN%.sql
   
   C:\Windows\System32\timeout.exe /t 300
   goto :savesql
   

   
