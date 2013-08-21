@echo off

SET IP_ADDR=127.0.0.1
SET IP_ADDR_V6=::1
SET RAD_DB=..\..\etc\raddb

echo **** FreeRADIUS 2.2.0 Sanity Tests ****
echo.
echo **** EAP-SIM Tests ****
echo.
echo These tests assume FreeRADIUS server is up and running (127.0.0.1:1812)
echo.

pause

..\radeapclient.exe -x -s -r 1 -d %RAD_DB% -f eap-sim01.conf %IP_ADDR%:1812 auth testing123

pause

..\radeapclient.exe -x -s -r 1 -d %RAD_DB% -f eap-sim02.conf %IP_ADDR%:1812 auth testing123