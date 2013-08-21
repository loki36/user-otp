@ECHO OFF
REM ************************************************************
REM
REM multiOTP - Strong two-factor authentication PHP class package
REM http://www.multiotp.net
REM
REM      Filename: checkmultiotp.cmd
REM       Version: 4.0.4
REM      Language: Windows batch file for Windows NT4/2K/XP/2003/7/2008/8/2012
REM     Copyright: SysCo systèmes de communication sa
REM       Created: 2010-07-10 SysCo/al
REM Last modified: 2013-08-20 SysCo/al
REM      Web site: http://developer.sysco.ch/multiotp/
REM         Email: developer@sysco.ch
REM
REM Description
REM
REM   checkmultiotp is a small script that will check
REM   multiotp compliance with RFC4226. It must be launched
REM   in the same directory as the multiotp.exe file.
REM
REM
REM Usage
REM  
REM   The script must be launched in the same directory as multiotp.exe.
REM
REM
REM External file needed
REM
REM   multiotp.exe
REM
REM
REM External file created
REM
REM   Multiotp class will create some internals folders and files
REM
REM
REM Licence
REM
REM   Copyright (c) 2010-2013, SysCo systèmes de communication sa
REM   SysCo (tm) is a trademark of SysCo systèmes de communication sa
REM   (http://www.sysco.ch/)
REM   All rights reserved.
REM
REM   This file is part of the multiOTP project.
REM
REM
REM Change Log
REM
REM   2013-08-20 4.0.4   SysCo/al Testing new options of the multiOTP library
REM   2010-09-02 3.0.0   SysCo/al More flexible variable definition to launch multiotp
REM   2010-08-21 2.0.4   SysCo/al More documentation, tests results resume
REM   2010-07-19 2.0.1   SysCo/al More documentation
REM   2010-07-19 2.0.0   SysCo/al New version for the new multiotp implementation
REM   2010-06-08 1.1.0   SysCo/al Project renamed to multiotp to avoid overlapping
REM   2010-06-08 1.0.0   SysCo/al Initial release
REM
REM ************************************************************

SET _check_dir=%~d0%~p0
SET _multiotp="%_check_dir%multiotp.exe"

SET SUCCESS=0
SET TOTAL_TESTS=0

ECHO multiotp HOTP implementation check
ECHO (RFC 4226, http://www.ietf.org/rfc/rfc4226.txt)
ECHO -----------------------------------------------

ECHO.
%_multiotp% -version

IF /I "%1"=="ni" SET ni=1
IF /I "%2"=="ni" SET ni=1
IF /I NOT "%1"=="MySQL" GOTO NoMySQL
IF /I NOT "%2"=="MySQL" GOTO NoMySQL
ECHO.
ECHO Upgrading the database scheme
%_multiotp% -log -config backend-type=mysql
%_multiotp% -log -initialize-backend
IF NOT ERRORLEVEL 19 ECHO - OK! Database scheme successfully updated
IF ERRORLEVEL 19 ECHO - KO! Error updating the database scheme
IF NOT ERRORLEVEL 19 SET /A SUCCESS=SUCCESS+1
SET /A TOTAL_TESTS=TOTAL_TESTS+1
:NoMySQL


ECHO.
ECHO Deleting the test_user
%_multiotp% -log -delete test_user
IF NOT ERRORLEVEL 13 ECHO - User test_user successfully deleted
IF ERRORLEVEL 13 ECHO - User test_user was not existing

ECHO.
ECHO Creating user test_user with the RFC test values HOTP token
%_multiotp% -log -create test_user HOTP 3132333435363738393031323334353637383930 1234 6 0
IF NOT ERRORLEVEL 12 ECHO - OK! User test_user successfully created
IF ERRORLEVEL 12 ECHO - KO! Error creating the user test_user
IF NOT ERRORLEVEL 12 SET /A SUCCESS=SUCCESS+1
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Authenticating test_user with the first token of the RFC test values
%_multiotp% -keep-local -log test_user 755224
IF NOT ERRORLEVEL 1 ECHO - OK! Token of the user test_user successfully accepted
IF ERRORLEVEL 1 ECHO - KO! Error authenticating the user test_user with the first token
IF NOT ERRORLEVEL 1 SET /A SUCCESS=SUCCESS+1
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Testing the replay rejection
%_multiotp% -keep-local -log test_user 755224
IF ERRORLEVEL 1 ECHO - OK! Token of the user test_user successfully REJECTED (replay)
IF NOT ERRORLEVEL 1 ECHO - KO! Replayed token *WRONGLY* accepted
IF ERRORLEVEL 1 SET /A SUCCESS=SUCCESS+1
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Resynchronizing the key
%_multiotp% -keep-local -log -resync -status test_user 338314 254676
IF NOT ERRORLEVEL 15 ECHO - OK! Token of the user test_user successfully resynchronized
IF ERRORLEVEL 15 ECHO - KO! Token of the user test_user NOT resynchronized
IF NOT ERRORLEVEL 15 SET /A SUCCESS=SUCCESS+1
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Testing a false resynchronisation (in the past, may take some time)
%_multiotp% -keep-local -log -resync -status test_user 287082 359152
IF ERRORLEVEL 20 ECHO - OK! Token of test_user successfully NOT resynchronized (in the past)
IF NOT ERRORLEVEL 20 ECHO - KO! Token of user test_user *WRONGLY* resynchronized
IF ERRORLEVEL 20 SET /A SUCCESS=SUCCESS+1
SET /A TOTAL_TESTS=TOTAL_TESTS+1


ECHO.
ECHO Deleting the test_user2
%_multiotp% -log -delete test_user2
IF NOT ERRORLEVEL 13 ECHO - User test_user2 successfully deleted
IF ERRORLEVEL 13 ECHO - User test_user2 was not existing

ECHO.
ECHO Creating user test_user2 with the RFC test values HOTP token and PIN prefix
ECHO (like Authenex / ZyXEL / Billion is doing for their OTP solution)
%_multiotp% -log -create -prefix-pin test_user2 HOTP 3132333435363738393031323334353637383930 1234 6 0
IF NOT ERRORLEVEL 12 ECHO - OK! User test_user2 successfully created
IF ERRORLEVEL 12 ECHO - KO! Error creating the user test_user2
IF NOT ERRORLEVEL 12 SET /A SUCCESS=SUCCESS+1
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Authenticating test_user2 with the first token of the RFC test values with PIN
%_multiotp% -keep-local -log test_user2 1234755224
IF NOT ERRORLEVEL 1 ECHO - OK! Token of the user test_user2 (with prefix PIN) successfully accepted
IF ERRORLEVEL 1 ECHO - KO! Error authenticating the user test_user2 with the first token and PIN prefix
IF NOT ERRORLEVEL 1 SET /A SUCCESS=SUCCESS+1
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Installing and starting the RADIUS server (wait 10 seconds)
CALL %_check_dir%radius_install.cmd
PING 127.0.0.1 -n 10 >NUL 

ECHO.
ECHO Authenticating test_user2 with the second token through the RADIUS server
ECHO User-Name = "test_user2">%TEMP%\radiustest.conf
ECHO User-Password = "1234287082">>%TEMP%\radiustest.conf
ECHO NAS-IP-Address = 127.0.0.1>>%TEMP%\radiustest.conf
ECHO NAS-Port = 1812>>%TEMP%\radiustest.conf
%_check_dir%radius\bin\radclient.exe -c 1 -d %_check_dir%radius\etc\raddb -f %TEMP%\radiustest.conf -q -r 1 -t 5 127.0.0.1:1812 auth multiotpsecret
IF NOT ERRORLEVEL 1 ECHO - OK! Token of the user test_user2 successfully accepted by RADIUS server
IF ERRORLEVEL 1 ECHO - KO! Error authenticating the user test_user2 with by the RADIUS server
IF NOT ERRORLEVEL 1 SET /A SUCCESS=SUCCESS+1
SET /A TOTAL_TESTS=TOTAL_TESTS+1
DEL %TEMP%\radiustest.conf /Q

ECHO.
ECHO Stopping and uninstalling the RADIUS server
CALL %_check_dir%radius_uninstall.cmd

ECHO.
ECHO Installing and starting the multiOTP web service (wait 10 seconds)
CALL %_check_dir%webservice_install.cmd
PING 127.0.0.1 -n 10 >NUL 

ECHO.
ECHO Check the default multiOTP web service page
%_check_dir%tools\wget http://127.0.0.1:8112 --quiet --output-document=%TEMP%multiOTPwebservice.check --timeout=5 --tries=1
FIND /C "Web service is ready" %TEMP%multiOTPwebservice.check >NUL
IF NOT ERRORLEVEL 1 ECHO - OK! multiOTP web service is responding correctly
IF ERRORLEVEL 1 ECHO - KO! multiOTP web service is not responding correctly on http://127.0.0.1:8112
IF NOT ERRORLEVEL 1 SET /A SUCCESS=SUCCESS+1
SET /A TOTAL_TESTS=TOTAL_TESTS+1
DEL %TEMP%multiOTPwebservice.check /Q

ECHO.
ECHO Stopping and uninstalling the multiOTP web service
CALL %_check_dir%webservice_uninstall.cmd

ECHO.
ECHO Generating scratch passwords for test_user2
FOR /f "tokens=1*" %%a, in ('%_multiotp% -keep-local -scratchlist test_user2') DO (
SET _password=%%a
ECHO %%a
)
IF NOT ERRORLEVEL 20 ECHO - OK! Scratch list for test_user2 successfully created
IF ERRORLEVEL 20 ECHO - KO! Scratch list for test_user2 NOT successfully created
IF NOT ERRORLEVEL 20 SET /A SUCCESS=SUCCESS+1
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Testing the last scratch password (%_password%) for test_user2
%_multiotp% -keep-local -log test_user2 %_password%
IF NOT ERRORLEVEL 1 ECHO - OK! Scratch password accepted for test_user2
IF ERRORLEVEL 1 ECHO - KO! Scratch password NOT accepted for test_user2
IF NOT ERRORLEVEL 1 SET /A SUCCESS=SUCCESS+1
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.
ECHO Testing again the last scratch password (%_password%) for test_user2
%_multiotp% -keep-local -log test_user2 %_password%
IF ERRORLEVEL 1 ECHO - OK! Scratch password is not accepted a second time for test_user2
IF NOT ERRORLEVEL 1 ECHO - KO! Scratch password IS WRONGLY accepted a second time for test_user2
IF ERRORLEVEL 1 SET /A SUCCESS=SUCCESS+1
SET /A TOTAL_TESTS=TOTAL_TESTS+1

ECHO.

REM GOTO DelTestUserSkip

ECHO.
ECHO Deleting the test_user
%_multiotp% -log -delete test_user
IF NOT ERRORLEVEL 13 ECHO - User test_user successfully deleted
IF ERRORLEVEL 13 ECHO - User test_user was not existing

ECHO.
ECHO Deleting the test_user2
%_multiotp% -log -delete test_user2
IF NOT ERRORLEVEL 13 ECHO - User test_user2 successfully deleted
IF ERRORLEVEL 13 ECHO - User test_user2 was not existing

:DelTestUserSkip

ECHO.
ECHO.

IF "%ni%"=="1" GOTO NoResultSummary
IF %SUCCESS% EQU %TOTAL_TESTS% ECHO OK! ALL %SUCCESS% TESTS HAVE PASSED SUCCESSFULLY !
IF %SUCCESS% NEQ %TOTAL_TESTS% ECHO KO! ONLY %SUCCESS%/%TOTAL_TESTS% TESTS HAVE PASSED SUCCESSFULLY !
:NoResultSummary

ECHO.

SET _check_dir=
SET _multiotp=

IF "%ni%"=="1" Goto NoPause

PAUSE

:NoPause
