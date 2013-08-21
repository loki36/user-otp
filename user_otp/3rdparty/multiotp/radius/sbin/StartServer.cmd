@echo off

SET ODBCINI=C:\FreeRADIUS\etc\odbc.ini

SET ODBCSYSINI=C:\FreeRADIUS\etc

SET FREETDS=C:\FreeRADIUS\etc\freeTDS.conf

SET TDSDUMP=C:\FreeRADIUS\var\log\freetds.log

SET TDSVER=8.0

SET RANDFILE=C:\FreeRADIUS\bin\rfile.rnd

SET PATH=C:\FreeRADIUS\lib;C:\FreeRADIUS\bin;%PATH%

Rem SET LD_LIBRARY_PATH=C:\FreeRADIUS\lib:C:\FreeRADIUS\bin

Rem SET ASA_DATABASE=asademo

cd /D C:\FreeRADIUS\sbin

radiusd.exe -X -d ..\etc\raddb