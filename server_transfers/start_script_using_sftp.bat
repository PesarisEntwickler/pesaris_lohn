@echo off
set Jahr=%date:~-4%
set Monat=%date:~-7,2%
set Tag=%date:~-10,2%
set std=%time:~-11,2%
set min=%time:~-8,2%
if %std%==0 set std=00
if %std%==1 set std=01
if %std%==2 set std=02
if %std%==3 set std=03
if %std%==4 set std=04
if %std%==5 set std=05
if %std%==6 set std=06
if %std%==7 set std=07
if %std%==8 set std=08
if %std%==9 set std=09

set stamp=%Jahr%%Monat%%Tag%_%std%%min%
mkdir  srv2_%stamp%
cd     srv2_%stamp%
"C:\Program Files (x86)\PuTTY\PSFTP.EXE" -b "C:\pesaris_lohn\server_transfers\script_using_sftp.txt" webdev@srv2.copronet.ch -pw h3rM$f8Q 
del /Q /s data-hidden\CUSTOMER\lohndev
del /Q data\kernel\common-functions\config*.php
del /Q /s .gitignore
del /Q /s platzhalter
del /Q /F /s data-hidden\OLD_SRC
del /Q /F /s data-hidden\CUSTOMER\development\*.*
del /Q /F /s data-hidden\CUSTOMER\lohndev\*.*
cd ..
pause
 