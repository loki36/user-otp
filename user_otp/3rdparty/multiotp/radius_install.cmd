@ECHO OFF
REM ************************************************************
REM
REM multiOTP - Strong two-factor authentication radius server
REM http://www.multiotp.net
REM
REM      Filename: radius_install.cmd
REM       Version: 4.0.4
REM      Language: Windows batch file for Windows NT4/2K/XP/2003/7/2008/8/2012
REM     Copyright: SysCo systèmes de communication sa
REM       Created: 2013-08-20 SysCo/al
REM Last modified: 2013-08-20 SysCo/al
REM      Web site: http://developer.sysco.ch/multiotp/
REM         Email: developer@sysco.ch
REM
REM Description
REM
REM   radius_install is a small script that will install
REM   the radius server of multiOTP under Windows using freeradius.
REM   (http://sourceforge.net/projects/freeradius/)
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
REM   2013-08-20 4.0.4   SysCo/al Initial release
REM
REM ************************************************************

SET _auth_port=1812
SET _account_port=1813

SET _folder=%~d0%~p0

SC stop multiOTPradius >NUL
SC delete multiOTPradius >NUL

ECHO # Exec module instance for multiOTP (http://www.multiotp.net/).>%_folder%radius\etc\raddb\modules\multiotp
ECHO exec multiotp {>>%_folder%radius\etc\raddb\modules\multiotp
ECHO         wait = yes>>%_folder%radius\etc\raddb\modules\multiotp
ECHO         input_pairs = request>>%_folder%radius\etc\raddb\modules\multiotp
ECHO         output_pairs = reply>>%_folder%radius\etc\raddb\modules\multiotp
ECHO         program = "../../multiotp.exe -base-dir=%_folder% -keep-local -log -debug %%{User-Name} %%{User-Password} -src=%%{Packet-Src-IP-Address} -chap-challenge=%%{CHAP-Challenge} -chap-password=%%{CHAP-Password} -ms-chap-challenge=%%{MS-CHAP-Challenge} -ms-chap-response=%%{MS-CHAP-Response} -ms-chap2-response=%%{MS-CHAP2-Response}">>%_folder%radius\etc\raddb\modules\multiotp
ECHO         shell_escape = yes>>%_folder%radius\etc\raddb\modules\multiotp
ECHO }>>%_folder%radius\etc\raddb\modules\multiotp

%_folder%tools\FART "%_folder%radius\etc\raddb\modules\multiotp" "\\" "!!!/!!!" >NUL
%_folder%tools\FART --remove "%_folder%radius\etc\raddb\modules\multiotp" "!!!" >NUL

SC create multiOTPradius binPath= "%_folder%radius\SRVANY.EXE" start= auto displayname= "multiOTP Radius server" >NUL
SC description multiOTPradius "Runs the multiOTP radius server on port %_port%." >NUL

REG ADD HKLM\SYSTEM\CurrentControlSet\Services\multiOTPradius\Parameters /f /v Application /t REG_SZ /d "%_folder%radius\sbin\radiusd.exe" >NUL
REG ADD HKLM\SYSTEM\CurrentControlSet\Services\multiOTPradius\Parameters /f /v AppParameters /t REG_SZ /d "-X -d %_folder%radius\etc\raddb" >NUL
REG ADD HKLM\SYSTEM\CurrentControlSet\Services\multiOTPradius\Parameters /f /v AppDirectory /t REG_SZ /d "%_folder%radius\sbin" >NUL

netsh firewall delete allowedprogram "%_folder%radius\sbin\radiusd.exe" >NUL
netsh firewall add allowedprogram "%_folder%radius\sbin\radiusd.exe" "multiOTP Radius server" ENABLE >NUL

netsh advfirewall firewall delete rule name="multiOTP Radius server" >NUL
netsh advfirewall firewall add rule name="multiOTP Radius server" dir=in action=allow program="%_folder%radius\sbin\radiusd.exe" enable=yes >NUL

SC start multiOTPradius >NUL

SET _auth_port=
SET _account_port=
SET _folder=
