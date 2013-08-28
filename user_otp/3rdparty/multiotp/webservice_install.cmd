@ECHO OFF
REM ************************************************************
REM
REM multiOTP - Strong two-factor authentication web service
REM http://www.multiotp.net
REM
REM      Filename: webservice_install.cmd
REM       Version: 4.0.6
REM      Language: Windows batch file for Windows 2K/XP/2003/7/2008/8/2012
REM     Copyright: SysCo syst�mes de communication sa
REM       Created: 2013-08-19 SysCo/al
REM Last modified: 2013-08-25 SysCo/al
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
REM   Default ports are 8112 and 8113
REM
REM
REM Licence
REM
REM   Copyright (c) 2013, SysCo syst�mes de communication sa
REM   SysCo (tm) is a trademark of SysCo syst�mes de communication sa
REM   (http://www.sysco.ch/)
REM   All rights reserved.
REM
REM   This file is part of the multiOTP project.
REM
REM
REM Change Log
REM
REM   2013-08-25 4.0.6 SysCo/al Service can also be set in the command line
REM                             (webservice_install [http_port [https_port [service_tag [service_name]]]])
REM   2013-08-21 4.0.5 SysCo/al Ports can be set in the command line
REM   2013-08-19 4.0.4 SysCo/al Initial release
REM
REM ************************************************************

REM Ports variables are not overwritten if already defined
IF "%_web_port%"=="" SET _web_port=8112
IF "%_web_ssl_port%"=="" SET _web_ssl_port=8113

REM Define the service tag and the service name
SET _service_tag=multiOTPservice
SET _service_name=multiOTP Web Service

REM Ports and service information can be overwritten if passing parameters
IF NOT "%1"=="" SET _web_port=%1
IF NOT "%2"=="" SET _web_ssl_port=%2
IF NOT "%3"=="" SET _service_tag=%3
IF NOT "%4"=="" SET _service_name=%4 %5 %6 %7 %8 %9

REM Define the current folder
SET _folder=%~d0%~p0

REM Stop and delete the service (if already existing)
SC stop %_service_tag% >NUL
SC delete %_service_tag% >NUL

REM Create the mongoose configuration file for the multiOTP web service
ECHO # %_service_name%> "%_folder%webservice\mongoose.conf"
ECHO.>> "%_folder%webservice\mongoose.conf"
ECHO cgi_interpreter php-cgi.exe>> "%_folder%webservice\mongoose.conf"
ECHO cgi_pattern **.php$>> "%_folder%webservice\mongoose.conf"
ECHO enable_directory_listing no>> "%_folder%webservice\mongoose.conf"
ECHO index_files multiotp.server.php>> "%_folder%webservice\mongoose.conf"
ECHO listening_ports %_web_port%,%_web_ssl_port%s>> "%_folder%webservice\mongoose.conf"
ECHO document_root web_root>> "%_folder%webservice\mongoose.conf"
ECHO ssl_certificate ssl_cert.pem>> "%_folder%webservice\mongoose.conf"
ECHO url_rewrite_patterns **=%_folder%multiotp.server.php>> %_folder%webservice\mongoose.conf"

REM Create the service
SC create %_service_tag% binPath= "%_folder%webservice\SRVANY.EXE" start= auto displayname= "%_service_name%" >NUL
SC description %_service_tag% "Runs the %_service_name% on ports %_web_port%/%_web_ssl_port%." >NUL

REM Define the parameters of the service (launched by SRVANY)
REG ADD "HKLM\SYSTEM\CurrentControlSet\Services\%_service_tag%\Parameters" /f /v Application /t REG_SZ /d "%_folder%webservice\mongoose.exe" >NUL
REG ADD "HKLM\SYSTEM\CurrentControlSet\Services\%_service_tag%\Parameters" /f /v AppDirectory /t REG_SZ /d "%_folder%webservice" >NUL

REM Basic firewall rules for the service
netsh firewall delete allowedprogram "%_folder%webservice\mongoose.exe" >NUL
netsh firewall add allowedprogram "%_folder%webservice\mongoose.exe" "%_service_name%" ENABLE >NUL

REM Enhanced firewall rules for the service
netsh advfirewall firewall delete rule name="%_service_name%" >NUL
netsh advfirewall firewall add rule name="%_service_name%" dir=in action=allow program="%_folder%webservice\mongoose.exe" enable=yes >NUL

REM Start the service
SC start %_service_tag% >NUL

REM Clean the environment variables
SET _folder=
SET _service_tag=
SET _web_port=
SET _web_ssl_port=

REM Call the URL of the multiOTP web service
IF NOT "%_multiotp_ni%"=="1" START http://127.0.0.1:%_web_port%
