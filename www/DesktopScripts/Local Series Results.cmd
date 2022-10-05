echo off

:getraceid
   set /p raceid=Enter the raceid: 
   IF [%raceid%]==[] (CALL :getraceid ) ELSE (CALL :upload)
   EXIT /b

:upload

   cd C:\Users\smg\Dropbox\CES\Workspace\userRegister\raceday\python
   echo "Starting Local DB Upload..."

   python loadresultsdb_stageplacement_stagewins.py %raceid% local

   echo "Starting Individual Series Results Calculation...."

   python IndStandings_stage_points.py 14 local

   echo "Starting TeamSeries Results Calculation...."

   python TeamStandingsdb.py 14 local 

   C:\Windows\System32\timeout.exe /t 300




   
