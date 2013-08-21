@echo off

SET IP_ADDR=127.0.0.1
SET IP_ADDR_V6=::1
SET RAD_DB=..\..\etc\raddb

echo **** FreeRADIUS 2.2.0 Sanity Tests ****
echo.
echo These tests assume FreeRADIUS server is up and running (127.0.0.1:1812)
echo.

pause

..\radclient.exe -x -s -r 1 -d %RAD_DB% -f radclient.conf %IP_ADDR%:1812 auth testing123

pause

..\radclient.exe -x -s -r 1 -d %RAD_DB% -f radclient-acct-start.conf %IP_ADDR% acct testing123

pause

..\radclient.exe -x -s -r 1 -d %RAD_DB% -f radclient-status.conf %IP_ADDR% status testing123

pause

..\radclient.exe -x -s -r 1 -d %RAD_DB% -f radclient-md5.conf %IP_ADDR%:1812 auth testing123

pause

..\radclient.exe -x -s -r 1 -d %RAD_DB% -f radclient-mschap.conf %IP_ADDR%:1812 auth testing123

pause

..\radeapclient.exe -x -s -r 1 -d %RAD_DB% -f eap-md5-bis.conf %IP_ADDR%:1812 auth testing123

pause

..\radclient.exe -x -s -r 1 -d %RAD_DB% -f wimax.conf %IP_ADDR%:1812 auth testing123

pause

cd ..

radwho.exe -d ..\etc\raddb

pause

cd tests

..\radclient.exe -x -s -r 1 -d %RAD_DB% -f radclient-acct-stop.conf %IP_ADDR% acct testing123

pause

..\radclient.exe -x -s -r 1 -6 -d %RAD_DB% -f radclient-acct-start.conf %IP_ADDR_V6% acct testing123

pause

..\radclient.exe -x -s -r 1 -6 -d %RAD_DB% -f radclient-ipv6.conf %IP_ADDR_V6% auth testing123
