@echo off
%~d0
cd %~dp0
REM if [%1]==[] goto eof
:loop
php.exe -c php.ini -f index.php -- %*
REM shift
REM if not [%1]==[] goto loop

:eof