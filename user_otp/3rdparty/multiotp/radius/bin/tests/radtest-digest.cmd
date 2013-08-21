@echo off

SET IP_ADDR=127.0.0.1
SET IP_ADDR_V6=::1
SET RAD_DB=..\..\etc\raddb

echo **** FreeRADIUS 2.2.0 Sanity Tests ****
echo.
echo **** Digest Tests ****
echo.
echo These tests assume FreeRADIUS server is up and running (127.0.0.1:1812)
echo.

pause

..\radclient.exe -x -s -r 1 -d %RAD_DB% -f digest\digest-auth_int-MD5 %IP_ADDR%:1812 auth testing123

pause

..\radclient.exe -x -s -r 1 -d %RAD_DB% -f digest\digest-auth_int-MD5_Sess %IP_ADDR%:1812 auth testing123

pause

..\radclient.exe -x -s -r 1 -d %RAD_DB% -f digest\digest-auth_int-noalgo %IP_ADDR%:1812 auth testing123

pause

..\radclient.exe -x -s -r 1 -d %RAD_DB% -f digest\digest-auth-int %IP_ADDR%:1812 auth testing123

pause

..\radclient.exe -x -s -r 1 -d %RAD_DB% -f digest\digest-auth-MD5 %IP_ADDR%:1812 auth testing123

pause

..\radclient.exe -x -s -r 1 -d %RAD_DB% -f digest\digest-auth-MD5_Sess %IP_ADDR%:1812 auth testing123

pause

..\radclient.exe -x -s -r 1 -d %RAD_DB% -f digest\digest-auth-noalgo %IP_ADDR%:1812 auth testing123

pause

..\radclient.exe -x -s -r 1 -d %RAD_DB% -f digest\digest-md5-sess %IP_ADDR%:1812 auth testing123