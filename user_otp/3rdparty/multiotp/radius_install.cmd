@ECHO OFF
REM ************************************************************
REM
REM multiOTP - Strong two-factor authentication radius server
REM http://www.multiotp.net
REM
REM      Filename: radius_install.cmd
REM       Version: 4.0.6
REM      Language: Windows batch file for Windows 2K/XP/2003/7/2008/8/2012
REM     Copyright: SysCo syst�mes de communication sa
REM       Created: 2013-08-20 SysCo/al
REM Last modified: 2013-08-25 SysCo/al
REM      Web site: http://developer.sysco.ch/multiotp/
REM         Email: developer@sysco.ch
REM
REM Description
REM
REM   radius_install is a small script that will install the
REM   radius server of multiOTP under Windows using freeradius.
REM   (http://sourceforge.net/projects/freeradius/)
REM
REM
REM Usage
REM  
REM   The script must be launched in the top folder of multiOTP.
REM   Default ports are 1812 and 1813
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
REM Users feedbacks and comments
REM
REM  2013-08-21 Henk van der Helm (again ;-)
REM    Thanks Henk for his first valuable feedback with some bugs reports
REM
REM
REM Change Log
REM
REM   2013-08-25 4.0.6 SysCo/al Service can also be set in the command line
REM                             (radius_install [auth_port [account_port [service_tag [service_name]]]])
REM   2013-08-21 4.0.5 SysCo/al Fix the mix between _port and _auth_port in the script
REM                             The service is now *really* using the defined ports
REM                             Ports can be set in the command line
REM   2013-08-20 4.0.4 SysCo/al Initial release
REM
REM ************************************************************

SET _radius_secret=multiotpsecret

REM Ports variables are not overwritten if already defined
IF "%_auth_port%"=="" SET _auth_port=1812
IF "%_account_port%"=="" SET _account_port=1813

REM Define the service tag and the service name
SET _service_tag=multiOTPradius
SET _service_name=multiOTP Radius server

REM Ports and service information can be overwritten if passing parameters
IF NOT "%1"=="" SET _auth_port=%1
IF NOT "%2"=="" SET _account_port=%2
IF NOT "%3"=="" SET _service_tag=%3
IF NOT "%4"=="" SET _service_name=%4 %5 %6 %7 %8 %9

REM Define the current folder
SET _folder=%~d0%~p0

REM Stop and delete the service (if already existing)
SC stop %_service_tag% >NUL
SC delete %_service_tag% >NUL

REM Create the multiotp module for the radius server
ECHO # Exec module instance for multiOTP (http://www.multiotp.net/).>%_folder%radius\etc\raddb\modules\multiotp
ECHO exec multiotp {>>%_folder%radius\etc\raddb\modules\multiotp
ECHO         wait = yes>>%_folder%radius\etc\raddb\modules\multiotp
ECHO         input_pairs = request>>%_folder%radius\etc\raddb\modules\multiotp
ECHO         output_pairs = reply>>%_folder%radius\etc\raddb\modules\multiotp
ECHO         program = "../../multiotp.exe -keep-local -log -debug **"%%{User-Name}**" **"%%{User-Password}**" -src=%%{Packet-Src-IP-Address} -chap-challenge=%%{CHAP-Challenge} -chap-password=%%{CHAP-Password} -ms-chap-challenge=%%{MS-CHAP-Challenge} -ms-chap-response=%%{MS-CHAP-Response} -ms-chap2-response=%%{MS-CHAP2-Response}">>%_folder%radius\etc\raddb\modules\multiotp
ECHO         shell_escape = yes>>%_folder%radius\etc\raddb\modules\multiotp
ECHO }>>%_folder%radius\etc\raddb\modules\multiotp

REM Sorry, this is an *ugly* trick to change "\" to "/" with the FART tool
%_folder%tools\FART "%_folder%radius\etc\raddb\modules\multiotp" "\\" "!!!/!!!" >NUL
%_folder%tools\FART --remove "%_folder%radius\etc\raddb\modules\multiotp" "!!!" >NUL
%_folder%tools\FART "%_folder%radius\etc\raddb\modules\multiotp" "**" "\\" >NUL

REM Customize the etc/raddb/radiusd.conf configuration file
COPY "%_folder%radius\etc\raddb\radiusd.template.conf" "%_folder%radius\etc\raddb\radiusd.conf" /Y >NUL
%_folder%tools\FART "%_folder%radius\etc\raddb\radiusd.conf" "_auth_port" "%_auth_port%" >NUL
%_folder%tools\FART "%_folder%radius\etc\raddb\radiusd.conf" "_account_port" "%_account_port%" >NUL

REM Customize the etc/raddb/clients.conf configuration file
COPY "%_folder%radius\etc\raddb\clients.template.conf" "%_folder%radius\etc\raddb\clients.conf" /Y >NUL
%_folder%tools\FART "%_folder%radius\etc\raddb\clients.conf" "_radius_secret" "%_radius_secret%" >NUL

REM Create the service
SC create %_service_tag% binPath= "%_folder%radius\SRVANY.EXE" start= auto displayname= "%_service_name%" >NUL
SC description %_service_tag% "Runs the %_service_name% on ports %_auth_port%/%_account_port%." >NUL

REM Define the parameters of the service (launched by SRVANY)
REG ADD "HKLM\SYSTEM\CurrentControlSet\Services\%_service_tag%\Parameters" /f /v Application /t REG_SZ /d "%_folder%radius\sbin\radiusd.exe" >NUL
REG ADD "HKLM\SYSTEM\CurrentControlSet\Services\%_service_tag%\Parameters" /f /v AppParameters /t REG_SZ /d "-X -d %_folder%radius\etc\raddb" >NUL
REG ADD "HKLM\SYSTEM\CurrentControlSet\Services\%_service_tag%\Parameters" /f /v AppDirectory /t REG_SZ /d "%_folder%radius\sbin" >NUL

REM Basic firewall rules for the service
netsh firewall delete allowedprogram "%_folder%radius\sbin\radiusd.exe" >NUL
netsh firewall add allowedprogram "%_folder%radius\sbin\radiusd.exe" "%_service_name%" ENABLE >NUL

REM Enhanced firewall rules for the service
netsh advfirewall firewall delete rule name="%_service_name%" >NUL
netsh advfirewall firewall add rule name="%_service_name%" dir=in action=allow program="%_folder%radius\sbin\radiusd.exe" enable=yes >NUL

REM Start the service
SC start %_service_tag% >NUL

REM Clean the environment variables
SET _account_port=
SET _auth_port=
SET _folder=
SET _radius_secret=
SET _service_name=
SET _service_tag=
