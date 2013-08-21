multiOTP is a GNU LGPL implementation of a strong two-factor authentication PHP class

(c) 2010-2013 SysCo systemes de communication sa
http://www.multiOTP.net/

Current build: 4.0.4 (2013-08-20)

The multiOTP package is the lightest package available that provides so many
strong authentication functionalities and goodies, and best of all, for anyone
that is interested about security issues, it's a fully open source solution!

This package is the result of a *LOT* of work. If you are happy using this
package, [Donation] are always welcome to support this project.
Please check http://www.multiOTP.net/ and you will find the magic button ;-)

The multiOTP class supports currently the following algorithms:
- OATH/HOTP RFC 4226 (http://www.ietf.org/rfc/rfc4226.txt)
- OATH/TOTP HOTPTimeBased RFC 4226 extension
- mOTP (http://motp.sourceforge.net)
- Google Authenticator
  (OATH/HOTP or OATH/TOTP based with a base32 seed and QRcode provisioning)
- SMS tokens (using aspsms, clickatell, intellisms, or even your own script)


Table of contents
 * Roadmap for futures releases
 * What's new in the releases
 * Change Log of released version
 * Content of the package
 * When and how can I use this package ?
 * How to install the multiOTP web service under Windows ?
 * How to install the multiOTP radius server under Windows ?
 * Configuring multiOTP with TekRADIUS or TekRADIUS LT under Windows
 * How to install the multiOTP web service under Linux ?
 * Configuring multiOTP with FreeRADIUS under Linux (new method)
 * Configuring multiOTP with FreeRADIUS under Linux (deprecated method)
 * How to configure multiOTP to use the client/server feature ?
 * Compatible clients applications and devices
 * multiOTP PHP class documentation
 * multiOTP command line tool


ROADMAP FOR THE NEXT RELEASES
=============================
- CSV import
  (username,algo,seed,pin,digits,interval,prefix_pin_needed,email,sms,token_serial)
- self-registration of unattributed tokens
  (by typing "username:serialnumber" as the username and "OTP" in the password field)
- automatic resync/unlock option during authentication
  (by typing "username:sync" as the username and "OTP1 OTP2" in the password field)
- LDAP/AD support (partial implementation already done)
- PSKC v12 provisioning file format support (with encrypted data), like for Vasco tokens
- MS-CHAP and MS-CHAPv2 authentication support
- OATH/OCRA support
- SOAP API
- YubiKey support (http://yubico.com/yubikey)
- basic web GUI
- syslog support
- radius gateway support
- sms-revolution SMS provider support
- generic web based SMS provider support
- ...

- multiOTP Pro as a tiny virtual appliance (per user licence, free up to 5 users)
  - friendly and easy to use management web interface (no CLI anymore)
  - multilanguage support (english, french, german, others on demand)
  - including Radius and web service
  - 1 core
  - 1 GB RAM
  - 4GB disk space
  - ...

- multiOTP Pro as an embedded hardware device (including a 25 users licence)
  - friendly and easy to use management web interface
  - including Radius and web service
  - one Ethernet connection
  - micro USB plug for the power
  - less than 5W of power consumption
  - data stored on a regular SDRAM and/or in a onboard eMMC
  - firmware upgradable
  - ...

- multiOTP Pro commercial support
  - yearly priority mail support
    (instead of community support which is best effort based)


WHAT'S NEW IN THE RELEASES
==========================
What's new in 4.0.x releases
- Full client/server support with local cache
- CHAP authentication support
- Emergency scratch passwords list (providing a list of 10 emergency one-time-usage passwords)
- SMS code sending (with clickatell, aspsms, intellisms and custom exec support)
- integrated Google Authenticator support with integrated base 32 seed handling
- Conversion from hardware HOTP/TOTP tokens to software tokens
- QRcode generation for HOTP/TOTP automatic provisioning
- Integrated QRcode generator library (from Y. Swetake)
- Group attribute for any user (sent back through the Radius attribute Filter-Id)
- A lot of new options, also available in command line
- Options are stored in an external configuration file (or in the database)
- Full MySQL support, including tables creation
- Fully automatic build chain (invisible for you, but very nice for me)
- (Parts of the) comments have been reformatted and enhanced, but still some work to do...

What's new in 3.9.x releases
- Support for account with multiple users
- Some bug fixes

What's new in 3.2.x releases
- Google Authenticator support. Special information to handle the base 32 seed.
- Better MySQL backend integration (still in beta). Now it is possible to store all
  information in a MySQL backend instead of a flat file 


CHANGE LOG OF RELEASED VERSIONS
===============================
2013-08-20 4.0.4  SysCo/al Adding an optional group attribute for the user
                            (which will be send with the Radius Filter-Id option)
                           Adding scratch passwords generation (if the token is lost)
                           Automatic database schema upgrade using method UpgradeSchemaIfNeeded()
                           Adding client/server support with local cache
                           Adding CHAP authentication support (PAP is of course still supported)
                           The encryption key is now a parameter of the class constructor
                           The method SetEncryptionKey('MyPersonalEncryptionKey') IS DEPRECATED
                           The method DefineMySqlConnection IS DEPRECATED
                           Full MySQL support, including tables creation (see example and SetSqlXXXX methods)
                           Adding email, sms and seed_password to users attributes
                           Adding sms support (aspsms, clickatell, intellisms, exec)
                           Adding prefix support for debug mode (in order to send Reply-Message := to Radius)
                           Adding a lot of new methods to handle easier the users and the tokens
                           General speedup by using available native functions for hash_hmac and others
                           Default max_time_window has been lowered to 600 seconds (thanks Stefan for suggestion)
                           Integrated Google Authenticator support with integrated base 32 seed handling
                           Integrated QRcode generator library (from Y. Swetake)
                           General options in an external configuration file
                           Comments have been reformatted and enhanced for automatic documentation
                           Development process enhanced, source code reorganized, external contributions are
                            added automatically at the end of the library after an internal build release
2011-10-25 3.9.2  SysCo/al Some quick fixes after intensive check
                           Improved get_script_dir() in CLI for Linux/Windows compatibility
2011-09-15 3.9.1  SysCo/al Some quick fixes concerning multiple users
2011-09-13 3.9.0  SysCo/al Adding support for account with multiple users
2011-07-06 3.2.0  SysCo/al Encryption hash handling with additional error message 33
                            (if the key has changed)
                           Adding more examples
                           Adding generic user with multiple account
                            (Real account name is combined: "user" and "account password")
                           Adding log options, now default doesn't log token value anymore
                           Debugging MySQL backend support for the token handling
                           Fixed automatic detection of \ or / for script path detection
2010-12-19 3.1.1  SysCo/al Better MySQL backend support (still in beta), including in CLI version
2010-09-15 3.1.0  SysCo/al Removed bad extra spaces in the multiotp.php file for Linux
                           Beta MySQL backend support
2010-09-02 3.0.0  SysCo/al Adding tokens handling support, including importing XML tokens definition file
                           Enhanced flat database file format
                            (multiotp is still compatible with old versions)
                           Internal method SetDataReadFlag renamed to SetUserDataReadFlag
                           Internal method GetDataReadFlag renamed to GetUserDataReadFlag
2010-08-21 2.0.4  SysCo/al Enhancement in order to use an alternate php "compiler"
                            for Windows command line
                           Documentation enhancement
2010-08-18 2.0.3  SysCo/al Minor notice fix, define timezone if not defined (for embedded command line)
                           If user doesn't exist, do not create the related flat file after a check
2010-07-21 2.0.2  SysCo/al Fix to create correctly the folders "users" and "log" if needed
2010-07-19 2.0.1  SysCo/al Foreach was not working well in "compiled" Windows command line
2010-07-19 2.0.0  SysCo/al New design using a class, mOTP support, cleaning of the code
2010-06-15 1.1.5  SysCo/al Adding OATH/TOTP support
2010-06-15 1.1.4  SysCo/al Project renamed to multiotp to avoid overlapping
2010-06-08 1.1.3  SysCo/al Typo in script folder detection
2010-06-08 1.1.2  SysCo/al Typo in variable name
2010-06-08 1.1.1  SysCo/al Status bar during resynchronization
2010-06-08 1.1.0  SysCo/al Fix in the example, distribution not compressed
2010-06-07 1.0.0  SysCo/al Initial implementation


CONTENT OF THE PACKAGE
======================
- multiotp.class.php       : the main file, it is the class itself
- multiotp.cli.header.php  : header file to be merged with the class for a single file command line tool
- multiotp.php             : command line tool (which is the merge of the header and the class)
- multiotp.exe             : command line tool for Windows with embedded PHP
                             (signed with our certificate)
- multiotp.server.php      : the web service file (the class is already merged in the file)
- check.multiotp.php       : PHP script to validate some multiOTP functionalities
- checkmultiotp.cmd        : Windows script to validate some multiOTP functionalities
- radius_install.cmd       : Windows script to install and start the multiOTP radius web server
- radius_uninstall.cmd     : Windows script to stop and uninstall the multiOTP radius web server
- webservice_install.cmd   : Windows script to install and start the multiOTP web service
- webservice_uninstall.cmd : Windows script to stop and uninstall the multiOTP web service

In the alternate folder:
- multiotp.exe            : an other embedded version of multiotp.exe which needs
                            some external files in the same folder (included)

In the qrcode folder:
- all necessary files to be able to generate QRcode

In the radius folder:
- all necessary files to be able to install a Windows radius server already
  configured with multiOTP support (using freeradius implementation for Windows)

In the webservice folder:
- all necessary files to be able to install a Windows multiOTP web service
  (using mongoose as the light web server on port 8112)


WHEN AND HOW CAN I USE THIS PACKAGE ? 
=====================================
If you decide to have strong authentication inside your company, this is
definitely the package you need! You will be able to have strong
authentication for your VPN accesses, your SSL gateway, your intranet
websites and even your Windows login for desktops AND laptops!

The multiOTP class can be used alone (for example to have strong 
authentication for your PHP based web application), as a command line tool
(to handle users and have strong authentication using command line) or
finally coupled with a radius server like TekRADIUS or FreeRADIUS to be
able to have a strong authentication through the RADIUS protocol for
external devices like firewalls for example.

The default backend storage is done in flat files, but you can also defined a
MySQL server as the backend server. To use MySQL, you will only have to provide
the server, the username, the password and the database name. Tables will be
created/updated automatically by multiOTP. The schema is also upgraded
automatically if you install a new release of multiOTP.

Starting with version 4.x, you can also install a multiOTP web service
on a server, and this way some other multiOTP implementations (like laptops) 
can connect to the web service and caching the tokens information (if allowed).

Inside a company, you will probably use multiOTP with a radius server or as
a web service (see below on how to install these services).

If you are running under Windows, TekRADIUS or TekRADIUS LT will do the job 
(http:/www.tekradius.com/).
The difference is that TekRADIUS needs an MS-SQL SERVER (or MS-SQL Express) 
and TekRADIUS LT uses only an embedded SQLite database.
A Windows FreeRADIUS implementation is also existing and is working well too.
(http://sourceforge.net/projects/freeradius/)

If you are running under Linux, FreeRADIUS will do the job.
(http://freeradius.org/)

Now, you can register your different devices like firewalls, SSL, etc.
in the radius server and provide the IP address(es) of the device(s)
(often called NAS) and their shared Secret.

If you want to have strong authentication on Windows logon, have a look at the
open source MultiOneTimePassword Credential Provider from Last Squirrel IT.
It works with Windows Vista/7/2008/8/2012 in both 32 and 64 bits.
The Credential Provider is using directly a local version of multiOTP which
can be configured as a client of a centralized server (with caching support)
(https://code.google.com/p/multi-one-time-password--credential-provider/)

LSE Experts is providing a commercial Radius Credential Provider which can talk
directly with a radius server.
(http://www.lsexperts.de)

When the backend is set, it's time to create/define the tokens. You will have
to select token generators for your users. Currently, the library supports the
mOTP, TOTP, HOTP, SMS and scratch passwords (printed on paper).

mOTP is a free implementation of strong tokens that asks a PIN to generate a
code. This code depends on the time and the PIN typed by the user.

The easiest tokens to use are TOTP, they are time based and well supported with
Google Authenticator. Provisioning will be done simply by flashing a QRcode.

Software tokens with mOTP support
  iPhone:    iOTP from PDTS (type iOTP in the Apple AppStore)
  Android:   Mobile-OTP (http://motp.sf.net/Mobile-OTP.apk)
  PalmOS:    Mobile-OTP (http://motp.sf.net/mobileotp_palm.zip)
  Java J2ME (Nokia and other Java capable phones): MobileOTP
            (http://motp.sf.net/MobileOTP.jad)
  ...
  
Software tokens with OATH compliant TOTP or HOTP support
  Check the various markets of your devices, for examples:
    Google Authenticator (Android/iPhone/iPad/BlackBerry)
    oathtoken for iPhone/iPad: http://code.google.com/p/oathtoken/
    androidtoken for Android: http://code.google.com/p/androidtoken/
    ...

Hardware tokens
  Feitian provides OATH compliant TOTP and HOTP tokens
     (seed is provided in a standardized token definition PSKC xml file)
   - OTP c100: OATH/HOTP, 6 digits
   - OTP c200: OATH/TOTP, 6 digits, 60 seconds time interval
     (seed is provided in a standardized token definition PSKC xml file)
  ZyXEL OTP provides HOTP OATH compliant tokens (v2 and old v1 tokens)
   - ZyWALL OTPv2 (rebranded SafeNet/Aladdin eToken PASS) : OATH/HOTP, 6 digits
     (seed is extracted from the importAlpine.dat downloaded file,
      the seed is the sccKey attribute)
   - ZyWALL OTPv1 (rebranded Authenex A-Key 3600): OATH/HOTP, 6 digits
     (seed is extracted from the OTP_data01_upgrade.sql SQL file,
      SEED field at the end of the file)
  Seamoon provides OATH compliant TOTP tokens
   - Seamoon KingKey: OATH/TOTP, 6 digits, 60 seconds time interval
     (seed is provided in a specific smd file)
    ...

If you want to use software tokens with Apps like Google Authenticator, you can
create a QRcode provisioning in two EASY steps:
 - create the token for the user: multiotp -fastcreate my_user
 - generate the provisioning QRcode: multiotp -qrcode my_user my_qrcode.png


HOW TO INSTALL THE MULTIOTP WEB SERVICE UNDER WINDOWS ?
=======================================================
Installing the multiOTP web service is VERY easy. Simply run the
webservice_install script. Mongoose configuration file will be created,
firewall rules will be adapted and the service will be installed and started.
The service is called multiOTPservice.


HOW TO INSTALL THE MULTIOTP RADIUS SERVER UNDER WINDOWS ?
=========================================================
Installing the multiOTP web service is VERY easy too. Simply run the
radius_install script. The etc/raddb/modules/multiotp file will be created,
firewall rules will be adapted and the service will be installed and started.
The service is called multiOTPradius and the secret is multiotpsecret for any
client including 127.0.0.1.


CONFIGURING MULTIOTP WITH TEKRADIUS OR TEKRADIUS LT UNDER WINDOWS
=================================================================
TekRADIUS supports a Default Username to be used when a matching user
profile cannot be found for an incoming RADIUS authentication request.
So a quick and easy way is to create in the TekRADIUS Manager a User
named 'Default' that belongs to the existing 'Default' Group.
Then add to this Default user the following attribute :
Check  External-Executable  C:\multitop\multiotp.exe %ietf|1% %ietf|2%

  
HOW TO INSTALL THE MULTIOTP WEB SERVICE UNDER LINUX ?
=====================================================
The multiOTP web service is a simple web site. If you are under Linux and you
are reading this document, you have for sure the necessary skill to configure
your favorite web server in order to have an URL that will launch the page
multiotp.server.php which is in the main folder of the multiOTP distribution.


CONFIGURING MULTIOTP WITH FREERADIUS UNDER LINUX (NEW METHOD)
=============================================================
1) Create a new module file called "multiotp" in etc/raddb/modules/ containing:
# Exec module instance for multiOTP (http://www.multiotp.net/).
# for Linux  : replace '/path/to' with the actual path to the multiotp.php file.
# for Windows: replace '/path/to' with the actual path to the multiotp.exe file (also with /).
exec multiotp {
        wait = yes
        input_pairs = request
        output_pairs = reply
        program = "/path/to/multiotp.exe %{User-Name} %{User-Password} -chap-challenge=%{CHAP-Challenge} -chap-password=%{CHAP-Password}"
        shell_escape = yes
}

2) In the configuration file called "default" in etc/raddb/sites-enabled/
    a) Add the multiOTP handling
    #
    # Handle multiOTP (http://www.multiotp.net/) authentication.
    # This must be add BEFORE the first "pap" entry found in the file.
    multiotp
    
    b) Add the multiotp authentication handling
    #
    # Handle multiOTP (http://www.multiotp.net/) authentication.
    # This must be add BEFORE the first "Auth-Type PAP" entry found in the file.
    Auth-Type multiotp {
        multiotp
    }
    
    c) Comment the first line containing only "chap"
    #chap is now handled by multiOTP
    
3) In the configuration file called "inner-tunnel" in etc/raddb/sites-enabled/
    a) Add the multiOTP handling
    #
    # Handle multiOTP (http://www.multiotp.net/) authentication.
    # This must be add BEFORE the first "pap" entry found in the file.
    multiotp
    
    b) Add the multiOTP authentication handling
    #
    # Handle multiOTP (http://www.multiotp.net/) authentication.
    # This must be add BEFORE the first "Auth-Type PAP" entry found in the file.
    Auth-Type multiotp {
        multiotp
    }
    
    c) Comment the first line containing only "chap"
    #chap is now handled by multiOTP

4) In the configuration file called "policy.conf" in etc/raddb/
    a) Add the multiOTP authorization policy
    #
    # Handle multiOTP (http://www.multiotp.net/) authorization policy.
    # This must be add just before the last "}"
    multiotp.authorize {
        if (!control:Auth-Type) {
            update control {
                Auth-Type := multiotp
            }
        }
	}

5) In the configuration file called "radiusd.conf" in etc/raddb/
    a) Depending which port(s) and/or ip address(es) you want to listen, change
       the corresponding ipaddr and port parameters

6) In the configuration file called "clients.conf" in etc/raddb/
    a) Add the clients IP, mask and secret that you want to authorize.
    #
    # Handle multiOTP (http://www.multiotp.net/) for some clients.
    client 0.0.0.0 {
    netmask = 0
    secret = multiotpsecret
    }
   
7) Now, to see what's going on, you can:
   - stop the service : /etc/init.d/freeradius stop
   - launch the FreeRADIUS server in debug mode : /usr/sbin/freeradius -X
   - try to make some authentication requests

8) When you have checked that everything works well:
   - stop the debug mode (CTRL + C)
   - restart the service /etc/init.d/freeradius restart


CONFIGURING MULTIOTP WITH FREERADIUS UNDER LINUX (DEPRECATED METHOD)
====================================================================
Define a DEFAULT entry in the /etc/freeradius/users file like this:
DEFAULT Auth-Type = Accept
Exec-Program-Wait = "/usr/local/bin/multiotp.php %{User-Name} %{User-Password}",
Fall-Through = Yes,
Reply-Message = "Hello, %{User-Name}"


HOW TO CONFIGURE MULTIOTP TO USE THE CLIENT/SERVER FEATURE ?
============================================================
A) On the server
1) Install the multiOTP web service on the server side. If you are using the
   unmodified included installer to install it under Windows, the URL for the
   multiOTP web service will be http://ip.address.of.server:8112
2) Set the shared secret key you will use to encode the data between the
   server and the client: multiotp -config server-secret=MySharedSecret
3) If you want to allow the client to cache the data on its side, set the
   options accordingly (enable the cache and define the lifetime of the cache):
   multiotp -config server-cache-level=1 server-cache-lifetime=15552000

B) On the client(s)
1) Set the shared secret key you will use to encode the data between the
   client and the server: multiotp -config server-secret=MySharedSecret
2) If you want to have cache support if allowed by the multiOTP web service,
   set the option accordingly: multiotp -config server-cache-level=1
3) Define the timeout after which you will switch to the next server(s) and
   finally locally: multiotp -config server-timeout=3
4) Last but not least, define the server(s) you want to connect with:
   multiotp -config server-url=http://ip.address.of.server:8112;http://url2


COMPATIBLE CLIENTS APPLICATIONS AND DEVICES
===========================================
MultiOneTimePassword Credential Provider (mOTP-CP)
If you want to have strong authentication on Windows logon, have a look at the
open source MultiOneTimePassword Credential Provider from Last Squirrel IT.
It works with Windows Vista/7/2008/8/2012 in both 32 and 64 bits.
The Credential Provider is using directly a local version of multiOTP which
can be configured as a client of a centralized server (with caching support)
(https://code.google.com/p/multi-one-time-password--credential-provider/)

LSE Experts is providing a commercial Radius Credential Provider which can talk
directly with any radius server to check the token. multiOTP will work with it.
(http://www.lsexperts.de)

Any firewall can connect with the Radius protocol to a multiOTP radius server.
On advanced firewalls like the ZyXEL ZyWALL USG series, you can do some advanced
things like:
- receiving a specific group for each multiOTP user (using the Filter-Id
option). This is very useful to allow specific rules for some groups.
- VPN connections can be set-up to have a strong authentication (X-Auth).
- Strong Web authentication can be combined with specific firewall rules.
- ...


MULTIOTP PHP CLASS DOCUMENTATION
================================
Have a look into the source code if you want to know how to use it,
and you may also check multiotp.cli.header.php which implements the class.


MULTIOTP COMMAND LINE TOOL
==========================

multiOTP 4.0.4 (2013-08-20), running with embedded PHP version 4.4.4
(c) 2010-2013 SysCo systemes de communication sa
http://www.multiOTP.net   (you can try the [Donate] button ;-)

multiotp will check if the token of a user is correct, based on a specified
algorithm (currently Mobile-OTP (http://motp.sf.net), OATH/HOTP (RFC 4226) 
and OATH/TOTP (HOTPTimeBased RFC 4226 extension) are implemented).
Supported authentication methods are PAP and CHAP.
SMS-code are supported (current providers: aspsms,clickatell,intellisms).
Customized SMS sender program supported by specifying exec as SMS provider.

Google Authenticator base32_seed tokens must be of n*8 characters.
Google Authenticator TOTP tokens must have a 30 seconds interval.
Available characters in base32 are only ABCDEFGHIJKLMNOPQRSTUVWXYZ234567

To quickly create a user, use the -fascreate option with the name of the user.
A quickly created user is compatible with Google Auth (30 seconds, 6 digits).

If a token is locked (return code 24), you have to resync the token to unlock.
Requesting an SMS token (put sms as the password), and typing the received
 token correctly will also unlock the token.

The check will return 0 for a correct token, and the other return code means:

Return codes:

 0 OK: Token accepted
11 INFO: User successfully created or updated
12 INFO: User successfully deleted
13 INFO: User PIN code successfully changed
14 INFO: Token has been resynchronized successfully
15 INFO: Tokens definition file successfully imported
16 INFO: QRcode successfully created
17 INFO: UrlLink successfully created
18 INFO: SMS code request received
19 INFO: Requested operation successfully done
21 ERROR: User doesn't exist
22 ERROR: User already exists
23 ERROR: Invalid algorithm
24 ERROR: Token locked (too many tries)
25 ERROR: Token delayed (too many tries, but still a hope in a few minutes)
26 ERROR: The time based token has already been used
27 ERROR: Resynchronization of the token has failed
28 ERROR: Unable to write the changes in the file
29 ERROR: Token doesn't exist
30 ERROR: At least one parameter is missing
31 ERROR: Tokens definition file doesn't exist
32 ERROR: Tokens definition file not successfully imported
33 ERROR: Encryption hash error, encryption key is not the same
34 ERROR: Linked user doesn't exist
35 ERROR: User not created
39 ERROR: Requested operation aborted
41 ERROR: SQL error
50 ERROR: QRcode not created
51 ERROR: UrlLink not created (no provisionable client for this protocol)
60 ERROR: No information on where to send SMS code
61 ERROR: SMS code request received, but an error occured during transmission
62 ERROR: SMS provider not supported
70 ERROR: Server authentication error
71 ERROR: Server request is not correctly formatted
72 ERROR: Server answer is not correctly formatted
80 ERROR: Server cache error
81 ERROR: Cache too old for this user, account autolocked
99 ERROR: Authentication failed (and other possible unknown errors)


Usage:

 multiotp user token (to check if the token is accepted)
 multiotp -checkpam (to check with pam-script, using PAM_USER and PAM_AUTHTOK)

 multiotp user sms (send an SMS token to the user)

 multiotp user [-chap-id=0x..] -chap-challenge=0x... -chap-password=0x...
   (the first byte of the chap-password value can contain the chap-id value)

 multiotp -fastcreate user [pin] (create a Google Auth compatible token)
 multiotp -createga user base32_seed [pin] (create Google Authenticator user)
 multiotp -create [-prefix-pin] user algo seed pin digits [pos|interval]
 multiotp -create -token-id [-prefix-pin] user token-id pin

  token-id: id of the previously imported token to attribute to the user
      user: name of the user (should be the account name)
      algo: available algorithms are mOTP, HOTP and TOTP
      seed: hexadecimal seed of the token
       pin: private pin code of the user
    digits: number of digits given by the token
       pos: for HOTP algorithm, position of the next awaited event
  interval: for mOTP and TOTP algorithms, token interval time in seconds

 multiotp -import tokens_definition_file (auto-detect format)
 multiotp -import-sql tokens_definition_file.sql (ZyXEL/Authenex)
 multiotp -import-dat importAlpine.dat (SafeWord/Aladdin/SafeNet tokens)
 multiotp -import-alpine-xml alpineXml.xml (SafeWord/Aladdin/SafeNet)
 multiotp -import-xml xml_tokens_definition_file.xml (Feitian, generic)

 multiotp -qrcode user png_file_name.png (only for TOTP and HOTP)
 multiotp -urllink user (only for TOTP and HOTP, generate provisioning URL)

 multiotp -scratchlist user (generate and display scratch passwords for the user)

 multiotp -resync [-status] user token1 token2 (two consecutive tokens)
 multiotp -update-pin user pin

 multiotp -delete user
 multiotp -lock user
 multiotp -unlock user

 multiotp -config option1=value1 option2=value2 ... optionN=valueN
  options are  backend-type: backend storage type (files|mysql)
                      debug: [0|1] enable/disable enhanced log information
                             (code result are also displayed on the console)
               debug-prefix: add a prefix when using the debug mode
                             (for example 'Reply-Message := ' for Radius)
                display-log: [0|1] enable/disable log display on the console
        ldap-account-suffix: LDAP/AD account suffix if ldap-domain-name not set
         ldap-cn-identifier: LDAP/AD cn identifier (default is sAMAccountName)
               ldap-base-dn: LDAP/AD base
               ldap-bind-dn: LDAP/AD bind 
    ldap-domain-controllers: LDAP/AD domain controller(s), comma separated
           ldap-domain-name: LDAP/AD domain name (NETBIOS or FQDN style)
              ldap-password: LDAP/AD default account password
                  ldap-port: LDAP/AD port (default is set to 389)
              ldap-username: LDAP/AD default account username (to browse tree)
                        log: [0|1] enable/disable log permanently
         server-cache-level: [0|1] enable/allow cache from server to client
      server-cache-lifetime: lifetime in seconds of the cached information
              server-secret: shared secret used for client/server operation
             server-timeout: timeout value for the connection to the server
                server-type: [xml] type of the server
                             (only xml server are able to do caching)
                 server-url: full url of the server for client/server mode
                             (server_url_1;server_url_2 is accepted)
                 sms-api-id: SMS API id (clickatell only, give your XML API id)
                             with exec as provider, define the script to call
                             (available variables: %from, %to, %message)
                sms-message: SMS message to display before the OTP
             sms-originator: SMS sender (if authorized by provider)
               sms-password: SMS account password
               sms-provider: SMS provider (aspsms,clickatell,intellisms,exec)
                sms-userkey: SMS account username or userkey
                 sql-server: SQL server (FQDN or IP)
               sql-username: SQL username
               sql-password: SQL password
               sql-database: SQL database
           sql-config-table: SQL config table, default is multiotp_config
          sql-devices-table: SQL devices table, default is multiotp_devices
              sql-log-table: SQL log table, default is multiotp_log
           sql-tokens-table: SQL tokens table, default is multiotp_tokens
            sql-users-table: SQL users table, default is multiotp_users
   tel-default-country-code: Default country code for phone number

 multiotp -initialize-backend (when all options are set, it will initialize
                               the backend, including creating the tables)

 multiotp -set user option1=value1 option2=value2 ... optionN=valueN
  options are  email: update the email of the user
         description: set a description to the user, used for example during
                      the QRcode generation as the description of the account
          prefix-pin: [0|1] the pin and the token must by merged by the user
                      (if your pin is 1234 and your token displays 5556677,
                      you will have to type 1234556677)
                 pin: set/update the private pin code of the user
                 sms: set/update the sms phone number of the user


Other commands:

 multiotp -phpinfo
 multiotp -showlog
 multiotp -tokenslist
 multiotp -userslist


Other parameters:

 -base-dir=/full/path/to/the/main/folder/of/multiotp/
           (if the script folder is wrongly detected, this will fix the issue)


Switches:

 -debug       Enhanced log information activated and code result on console
              (the permanent state of debug can be set with -config debug=1)
 -display-log Log information will also be displayed on the console
              (the permanent state can be set with -config display-log=1)
 -help        Display this help page
 -keep-local  Keep local user even if the server doesn't have it
              (if the server doesn't have it, the local one will be checked)
 -log         Log operation in the log file/database (in the log subdirectory)
              (the permanent state of log can be set with -config log=1)
 -mysql       MySQL connection information, comma separated (server,
              user,password,database[,log_table[,users_table[,tokens_table]]])
              (this switch is DEPRECATED, use the -config switch instead)
 -param       All parameters are logged for debugging purposes
 -prefix-pin  The pin and the token must be typed merged by the user
              (if you pin is 1234 and your token displays 5556677,
               you will have to type 1234556677)
              (this switch is DEPRECATED, use the -set switch instead)
 -status      Display a status bar during resynchronization
 -version     Display the current version of the library


Examples:

 multiotp -log -debug jimmy ea2315
 multiotp -log anna 546078
 multiotp -log -checkpam
 multiotp john 5678124578

 multiotp jimmy sms

 multiotp -fastcreate gademo
 multiotp -debug -createga gauser 2233445566777733
 multiotp -debug -create -prefix-pin alan TOTP 3683453456769abc3452 2233 6 60
 multiotp -debug -create -prefix-pin anna TOTP 56821bac24fbd2343393 4455 6 30
 multiotp -debug -create -prefix-pin john HOTP 31323334353637383930 5678 6 137
 multiotp -debug -create -token-id -prefix-pin rick 2010090201901 2345
 multiotp -log -create jimmy mOTP 004f5a158bca13984d349a7f23 1234 6 10

 multiotp -scratchlist gademo

 multiotp -set gademo description="VPN code for gademo"

 multiotp -debug -import 10OTP_data01_upgrade.sql
 multiotp -debug -import-xml tokens.xml
 multiotp -debug -import-dat importAlpine.dat

 multiotp -debug -qrcode gademo gademo.png
 multiotp -debug -urllink john

 multiotp -resync john 5678456789 5678345231
 multiotp -resync -status anna 4455487352 4455983513
 multiotp -update-pin alan 4417

 multiotp -config debug-prefix="Reply-Message := "

 multiotp -config server-cache-level=1 server-cache-lifetime=15552000
 multiotp -config server-secret=MySharedSecret server-type=xml
 multiotp -config server-timeout=3
 multiotp -config server-url=http://my.server/multiotp/;my.server2:8112/secure/

 multiotp -config sms-provider=clickatell sms-userkey=CL1 sms-password=PASS
 multiotp -config sms-api-id=1234567
 multiotp -config sms-message="Your SMS-code is:" sms-originator=Company
 multiotp -config sms-message="Type %s as code" sms-originator=0041797654321

 multiotp -config sms-provider=exec sms-api-id=/path/to/myapp %from %to "%msg"

 multiotp -config backend-type=mysql sql-server=fqdn.or.ip sql-database=dbname
 multiotp -config sql-username=user sql-password=pass
 multiotp -initialize-backend


multiOTP web service is working fine with any web server supporting PHP.
 - nginx is a light one under Linux (http://nginx.org/)
 - Mongoose is a light one under Windows (http://code.google.com/p/mongoose/)

multiOTP is working fine with FreeRADIUS under Linux (http://freeradius.org/)
multiOTP is also working fine with the last Windows port of FreeRADIUS
(http://sourceforge.net/projects/freeradius/)

When used with TekRADIUS (http://www.tekradius.com) the External-Executable
must be called like this: C:\multiotp\multiotp.exe %ietf|1% %ietf|2%

Other products and services based on multiOTP are :
 multiOTP Pro     - an extended library with additional features
                    (http://www.multiOTP.com)
 multiOTP Pro VM  - multiOTP Pro as a tiny virtual appliance
                    (http://www.multiOTP.com)
 multiOTP Pro box - multiOTP Pro as a cheap embedded hardware device
                    (http://www.multiOTP.com)
 secuPASS.net     - a simple SMS trusting service for free WLAN Hotspot
                    (http://www.secuPASS.net)
 mOTP-CP          - an Open-Source Credential Provider for the Windows Logon
                    (http://goo.gl/BZAhKR)
 ownCloud OTP     - a One Time Password app for ownCloud (http://owncloud.org)
                    (http://apps.owncloud.com/content/show.php/?content=159196)

If you need specific developments concerning strong authentication,
do not hesitate to contact us per email at support@sysco.ch.


