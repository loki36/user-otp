@ECHO OFF
REM ************************************************************
REM
REM multiOTP - Strong two-factor authentication web service
REM http://www.multiotp.net
REM
REM      Filename: webservice_install.cmd
REM       Version: 4.0.4
REM      Language: Windows batch file for Windows NT4/2K/XP/2003/7/2008/8/2012
REM     Copyright: SysCo systèmes de communication sa
REM       Created: 2013-08-19 SysCo/al
REM Last modified: 2013-08-19 SysCo/al
REM      Web site: http://developer.sysco.ch/multiotp/
REM         Email: developer@sysco.ch
REM
REM Description
REM
REM   webservice_install is a small script that will install
REM   the web service of multiOTP under Windows using mongoose.
REM   (http://code.google.com/p/mongoose/)
REM
REM
REM Usage
REM  
REM   The script must be launched in the top folder of multiOTP.
REM
REM
REM Licence
REM
REM   Copyright (c) 2013, SysCo systèmes de communication sa
REM   SysCo (tm) is a trademark of SysCo systèmes de communication sa
REM   (http://www.sysco.ch/)
REM   All rights reserved.
REM
REM   This file is part of the multiOTP project.
REM
REM
REM Change Log
REM
REM   2013-08-19 4.0.4   SysCo/al Initial release
REM
REM ************************************************************

SET _port=8112
SET _ssl_port=8113

SET _folder=%~d0%~p0

SC stop multiOTPservice >NUL
SC delete multiOTPservice >NUL

ECHO # multiOTP Web Service> "%_folder%webservice\mongoose.conf"
ECHO.>> "%_folder%webservice\mongoose.conf"
ECHO cgi_interpreter php-cgi.exe>> "%_folder%webservice\mongoose.conf"
ECHO cgi_pattern **.php$>> "%_folder%webservice\mongoose.conf"
ECHO enable_directory_listing no>> "%_folder%webservice\mongoose.conf"
ECHO index_files multiotp.server.php>> "%_folder%webservice\mongoose.conf"
ECHO listening_ports %_port%,%_ssl_port%s>> "%_folder%webservice\mongoose.conf"
ECHO document_root web_root>> "%_folder%webservice\mongoose.conf"
ECHO ssl_certificate ssl_cert.pem>> "%_folder%webservice\mongoose.conf"
ECHO url_rewrite_patterns **=%_folder%multiotp.server.php>> %_folder%webservice\mongoose.conf"

SC create multiOTPservice binPath= "%_folder%webservice\SRVANY.EXE" start= auto displayname= "multiOTP Web Service" >NUL
SC description multiOTPservice "Runs the multiOTP Web Service on port %_port%." >NUL

REG ADD HKLM\SYSTEM\CurrentControlSet\Services\multiOTPservice\Parameters /f /v Application /t REG_SZ /d "%_folder%webservice\mongoose.exe" >NUL
REG ADD HKLM\SYSTEM\CurrentControlSet\Services\multiOTPservice\Parameters /f /v AppDirectory /t REG_SZ /d "%_folder%webservice" >NUL

netsh firewall delete allowedprogram "%_folder%webservice\mongoose.exe" >NUL
netsh firewall add allowedprogram "%_folder%webservice\mongoose.exe" "multiOTP Web Service" ENABLE >NUL

netsh advfirewall firewall delete rule name="multiOTP Web Service" >NUL
netsh advfirewall firewall add rule name="multiOTP Web Service" dir=in action=allow program="%_folder%webservice\mongoose.exe" enable=yes >NUL

SC start multiOTPservice >NUL

SET _folder=
SET _port=
SET _ssl_port=

REM START http://127.0.0.1:%_port%
