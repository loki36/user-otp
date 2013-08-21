@echo off

SET IP_ADDR=127.0.0.1
SET IP_ADDR_V6=::1

echo **** FreeRADIUS 2.2.0 EAP tests ****
echo.
echo These tests assume FreeRADIUS server is up and running (127.0.0.1:1812)
echo.
echo. The following tests will be performed:
echo.
echo      - EAP-MD5
echo.
echo      - EAP-MSCHAP v2
echo.
echo      - TTLS-PAP
echo.
echo      - EAP-TTLS-MD5
echo.
echo      - TTLS-CHAP
echo.
echo      - TTLS-GTC
echo.
echo      - TTLS-MSCHAPv2
echo.
echo      - PEAPv0-MD5
echo.
echo      - PEAPv0-MSCHAPv2
echo.
echo      - OTP (experimental)
echo.
echo      - IKEv2 (experimental)
echo.

pause

..\eapol_test.exe -a %IP_ADDR% -p 1812 -s testing123 -c eap-md5.conf

pause

..\eapol_test.exe -a %IP_ADDR% -p 1812 -s testing123 -c eap-mschapv2.conf

pause

..\eapol_test.exe -a %IP_ADDR% -p 1812 -s testing123 -c eap-ttls-pap.conf

pause

..\eapol_test.exe -a %IP_ADDR% -p 1812 -s testing123 -c eap-ttls-eap-md5.conf

pause

..\eapol_test.exe -a %IP_ADDR% -p 1812 -s testing123 -c eap-ttls-chap.conf

pause

..\eapol_test.exe -a %IP_ADDR% -p 1812 -s testing123 -c eap-ttls-mschapv2.conf

pause

..\eapol_test.exe -a %IP_ADDR% -p 1812 -s testing123 -c eap-ttls-eap-gtc.conf

pause

..\eapol_test.exe -a %IP_ADDR% -p 1812 -s testing123 -c eap-peapv0-md5.conf

pause

..\eapol_test.exe -a %IP_ADDR% -p 1812 -s testing123 -c eap-peapv0-eap-mschapv2.conf

pause

..\eapol_test.exe -a %IP_ADDR% -p 1812 -s testing123 -c eap-ttls-eap-otp.conf

pause

..\eapol_test.exe -a %IP_ADDR% -p 1812 -s testing123 -c eap-ttls-eap-ikev2.conf