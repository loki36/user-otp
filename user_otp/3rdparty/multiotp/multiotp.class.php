<?php
/**
 * @file  multiotp.class.php
 * @brief Main file of the multiOTP PHP class.
 *
 * @mainpage
 *
 * multiOTP PHP class - Strong two-factor authentication PHP class
 * http://www.multiOTP.net/
 *
 * The multiOTP package is the lightest package available that provides so many
 * strong authentication functionalities and goodies, and best of all, for anyone
 * that is interested about security issues, it's a fully open source solution!

 * This package is the result of a *LOT* of work. If you are happy using this
 * package, [Donation] are always welcome to support this project.
 * Please check http://www.multiOTP.net/ and you will find the magic button ;-)
 *
 * The multiOTP class is a strong authentication class in pure PHP
 * that supports the following algorithms (mOTP is recommended):
 *  - mOTP (http://motp.sourceforge.net/)
 *  - OATH/HOTP RFC 4226 (http://tools.ietf.org/html/rfc4226)
 *  - OATH/TOTP RFC 6238 (http://tools.ietf.org/html/rfc6238)
 *  - Google Authenticator (OATH/HOTP or OATH/TOTP based with a base32 seed)
 *    (http://code.google.com/p/google-authenticator/)
 *  - SMS tokens
 *  - emergency scratch passwords
 *
 * This class can be used as is in your own PHP project, but it can also be
 * used easily as an external authentication provider with at least the
 * following RADIUS servers (using the multiotp command line script):
 *  - TekRADIUS, a free RADIUS server for Windows with MS-SQL backend
 *    (http:/www.tekradius.com/)
 *  - TekRADIUS LT, a free RADIUS server for Windows with SQLite backend
 *    (http:/www.tekradius.com/)
 *  - FreeRADIUS, a free RADIUS server implementation for Linux and
 *    and *nix environments (http://freeradius.org/)
 *  - FreeRADIUS for Windows, the FreeRADIUS implementation ported
 *    for Windows (http://sourceforge.net/projects/freeradius/)
 *
 * This class is also used as the central component in various commercial
 * products and services developed by SysCo systemes de communication sa:
 *  - multiOTP Pro, a forthcoming virtual appliance and device to
 *    provide a complete strong authentication solution with a
 *    web based interface (http://www.multiotp.com/)
 *  - secuPASS.net, a simple service to centralize provisioning and SMS
 *    authentication for (free) Wifi hotspot (http://www.secupass.net/)
 *
 * The Readme file contains additional information.
 *
 * PHP 4.4.4 or higher is supported.
 *
 * @author    Andre Liechti, SysCo systemes de communication sa, <developer@sysco.ch>
 * @version   4.0.4
 * @date      2013-08-20
 * @since     2010-06-08
 * @copyright (c) 2010-2013 by SysCo systemes de communication sa
 * @copyright GNU Lesser General Public License
 *
 *//*
 *
 * LICENCE
 *
 *   Copyright (c) 2010-2013, SysCo systemes de communication sa
 *   SysCo (tm) is a trademark of SysCo systemes de communication sa
 *   (http://www.sysco.ch/)
 *   All rights reserved.
 * 
 *   This file is part of the multiOTP project.
 *
 *   multiOTP PHP class is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU Lesser General Public License as
 *   published by the Free Software Foundation, either version 3 of the License,
 *   or (at your option) any later version.
 * 
 *   multiOTP PHP class is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU Lesser General Public License for more details.
 * 
 *   You should have received a copy of the GNU Lesser General Public
 *   License along with multiOTP PHP class.
 *   If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * Usage
 *
 *   require_once('multiotp.class.php');
 *   $multiotp = new Multiotp('MyPersonalEncryptionKey');
 *   // The use of $multiotp->SetEncryptionKey('MyPersonalEncryptionKey')
 *   // after creating the class without argument is DEPRECATED
 *   $multiotp->SetUser('user);
 *   $result = $multiotp->CheckToken('token');
 *
 *
 * Examples
 *
 *  Create a new user
 *    require_once('multiotp.class.php');
 *    $multiotp = new Multiotp('MyPersonalEncryptionKey');
 *    // The use of $multiotp->SetEncryptionKey('MyPersonalEncryptionKey')
 *    // after creating the class without argument is DEPRECATED
 *    $multiotp->EnableVerboseLog(); // Could be helpful at the beginning
 *    $multiotp->SetUser(“username”);
 *    $multiotp->SetUserPrefixPin(0); // We don’t want the prefix PIN feature for this example
 *    $multiotp->SetUserAlgorithm(“TOTP”);
 *    $multiotp->SetUserTokenSeed(“D6F9DF7C0110C85D6F9D”);
 *    $multiotp->SetUserPin(“1111”); // Useless for TOTP in this case without prefix PIN feature
 *    $multiotp->SetUserTokenNumberOfDigits(6);
 *    $multiotp->SetUserTokenTimeInterval(30);
 *    $multiotp->WriteUserData();
 *  
 *  
 *  Verify a token
 *    require_once('multiotp.class.php');
 *    $multiotp = new Multiotp('MyPersonalEncryptionKey');
 *    // The use of $multiotp->SetEncryptionKey('MyPersonalEncryptionKey')
 *    // after creating the class without argument is DEPRECATED
 *    $multiotp->EnableVerboseLog(); // Could be helpful at the beginning
 *    $multiotp->SetUser(“username”);
 *    if (0 == $multiotp->CheckToken('token'))
 *    {
 *        echo "Authentication accepted.";
 *    }
 *    else
 *    {
 *        echo "Authentication rejected.";
 *    }
 *  
 *  
 *  Resync a user (normally only useful for HOTP, but useful too if TOTP/mOTP device or server is not well synchronized)
 *    require_once('multiotp.class.php');
 *    $multiotp = new Multiotp('MyPersonalEncryptionKey');
 *    // The use of $multiotp->SetEncryptionKey('MyPersonalEncryptionKey')
 *    // after creating the class without argument is DEPRECATED
 *    $multiotp->EnableVerboseLog(); // Could be helpful at the beginning
 *    $multiotp->SetUser(“username”);
 *    if (0 == $multiotp->CheckToken(”token1”,”token2”)) // it must two consecutive tokens
 *    {
 *        echo “Synchronization successful”;
 *    }
 *    else
 *    {
 *        echo “Synchronization failed”;
 *    }
 *
 *
 *  Verify a token and be sure to encrypt some more data in the flat file
 *    require_once('multiotp.class.php');
 *    $multiotp = new Multiotp('MyPersonalEncryptionKey');
 *    // The use of $multiotp->SetEncryptionKey('MyPersonalEncryptionKey')
 *    // after creating the class without argument is DEPRECATED
 *    $multiotp->EnableVerboseLog(); // Could be helpful at the beginning
 *    $multiotp->SetAttributesToEncrypt('*user_pin*token_seed*token_serial*seed_password*');
 *    $multiotp->SetUser(“username”);
 *    if (0 == $multiotp->CheckToken('token'))
 *    {
 *        echo "Authentication accepted.";
 *    }
 *    else
 *    {
 *        echo "Authentication rejected.";
 *    }
 *  
 *
 *   For examples on how to integrate it with radius servers, please have a look
 *   to the readme.txt file or read the header of the multiotp.cli.header.php file.
 *
 *
 * External files created
 *
 *   Users database files in the subfolder called users (or anywhere else if defined)
 *   Tokens database files in the subfolder called tokens (or anywhere else if defined)
 *   Log file in the subfolder called log (or anywhere else if defined)
 *   Configuration file in the subfolder called config (or anywhere else if defined)
 *
 *
 * External file needed
 *
 *   Users database files in the subfolder called users
 *   Tokens database files in the subfolder called tokens
 *
 *
 * Special issues
 *
 *   If you need specific developements concerning strong authentication,
 *   do not hesistate to contact us per email at developer@sysco.ch.
 *
 *
 * Other related ressources
 *
 *   Mobile-OTP: Strong Two-Factor Authentication with Mobile Phones:
 *     http://motp.sourceforge.net/
 *
 *   The Initiative for Open Authentication:
 *     http://www.openauthentication.org/
 *
 *   TekRADIUS, a free RADIUS server for windows, available in two versions (MS-SQL and SQLite):
 *     http://www.tekradius.com/
 *
 *   FreeRADIUS, a free Radius server implementation for Linux and *nix environments:
 *     http://www.freeradius.org/
 *
 *   FreeRADIUS for Windows, a free Radius server implementation ported
 *     for Windows (http://sourceforge.net/projects/freeradius/)
 *
 *   Additional Portable Symmetric Key Container (PSKC) Algorithm Profiles
 *     http://tools.ietf.org/html/draft-hoyer-keyprov-pskc-algorithm-profiles-00
 *
 *   Google Authenticator (based on OATH/TOTP)
 *     http://code.google.com/p/google-authenticator/
 *
 *
 *
 * Users feedbacks and comments
 *
 * 2013-08-15 Donator AB (Sweden)
 *   MANY thanks for your appreciated $$$ sponsorship to support us to add self-registration in a next release.
 *
 * 2013-08-13 Daniel Särnström (Donator AB)
 *   Daniel proposed to add self-registration and pskc v12 with encrypted data support.
 * 
 * 2013-07-23 Stefan Kügler (again ;-)
 *   Stefan proposed to add the possibility to show the log, which is especially convenient for MySQL log.
 *   He proposed also to be able to call an external program to send SMS.
 *
 * 2013-07-11 Stefan Kügler
 *   Stefan proposed to add a lock and unlock option for the user.
 *
 * 2013-06-19 SerNet GmbH
 *   MANY thanks for your appreciated $$$ sponsorship after we implemented some features proposed by Stefan Kügler.
 *
 * 2013-06-13 Henk van der Helm (again ;-)
 *   Henk proposed to be able to have a specific description for the software token.
 *   (we use the already existing user description attribute)
 *
 * 2013-05-14 Henk van der Helm
 *   Henk asked to support also the provider IntelliSMS. Thanks for the donation!
 *
 * 2013-05-03 Stefan Kügler
 *   Stefan proposed to lower the default max_time_window to 600 seconds.
 *
 * 2013-03-04 Alan DeKok
 *   Alan proposed in the freeradius mailing-list to put a prefix to be able to handle the
 *   debug info by the freeradius server.
 *
 * 2012-03-16 Nicolas Goralski
 *   Nicolas proposed an enhancement in order to support PAM
 *     (with the -checkpam option in the command line edition)
 *
 * 2011-05-19 Fabiano Domeniconi
 *   Fabiano found old info in the samples, CheckToken() is not boolean anymore! Samples fixed.
 *
 * 2011-04-24 Steven Roddis
 *   Steven asked for more examples. Thanks to Steven for the donation ;-)
 *
 * 2010-09-15 Jasper Pol
 *   Jasper has added an initial MySQL backend support
 *
 * 2010-09-13 Brenno Hiemstra
 *   Brenno reported bad extra spaces after the #!/usr/bin/php in the Linux version of multiotp.php
 *
 * 2010-08-20 BirdNet, C. Christophi
 *   Documentation enhancement proposal for the TekRADIUS part, thanks !
 *
 * 2010-07-19 SysCo/al
 *   Well, as requested by some users, the new "class" design is done, enjoy !
 *
 *
 * Todos
 *
 *   Add more comments in the main class file
 *   Add more information in the log
 *   Add more verbose information in the log
 *
 *
 * Change Log
 *
 *   2013-08-20 4.0.4  SysCo/al Adding an optional group attribute for the user
 *                               (which will be send with the Radius Filter-Id option)
 *                              Adding scratch passwords generation (if the token is lost)
 *                              Automatic database schema upgrade using method UpgradeSchemaIfNeeded()
 *                              Adding client/server support with local cache
 *                              Adding CHAP authentication support (PAP is of course still supported)
 *                              The encryption key is now a parameter of the class constructor
 *                              The method SetEncryptionKey('MyPersonalEncryptionKey') IS DEPRECATED
 *                              The method DefineMySqlConnection IS DEPRECATED
 *                              Full MySQL support, including tables creation (see example and SetSqlXXXX methods)
 *                              Adding email, sms and seed_password to users attributes
 *                              Adding sms support (aspsms, clickatell, intellisms, exec)
 *                              Adding prefix support for debug mode (in order to send Reply-Message := to Radius)
 *                              Adding a lot of new methods to handle easier the users and the tokens
 *                              General speedup by using available native functions for hash_hmac and others
 *                              Default max_time_window has been lowered to 600 seconds (thanks Stefan for suggestion)
 *                              Integrated Google Authenticator support with integrated base 32 seed handling
 *                              Integrated QRcode generator library (from Y. Swetake)
 *                              General options in an external configuration file
 *                              Comments have been reformatted and enhanced for automatic documentation
 *                              Development process enhanced, source code reorganized, external contributions are
 *                               added automatically at the end of the library after an internal build release
 *   2011-10-25 3.9.2  SysCo/al Some quick fixes after intensive check
 *                              Improved get_script_dir() in CLI for Linux/Windows compatibility
 *   2011-09-15 3.9.1  SysCo/al Some quick fixes concerning multiple users
 *   2011-09-13 3.9.0  SysCo/al Adding support for account with multiple users
 *   2011-07-06 3.2.0  SysCo/al Encryption hash handling with additional error message 33
 *                               (if the key has changed)
 *                              Adding more examples
 *                              Adding generic user with multiple account
 *                               (Real account name is combined: "user" and "account password")
 *                              Adding log options, now default doesn't log token value anymore
 *                              Debugging MySQL backend support for the token handling
 *                              Fixed automatic detection of \ or / for script path detection
 *   2010-12-19 3.1.1  SysCo/al Better MySQL backend support, including in CLI version
 *   2010-09-15 3.1.0  SysCo/al Removed bad extra spaces in the multiotp.php file for Linux
 *                              MySQL backend support
 *   2010-09-02 3.0.0  SysCo/al Adding tokens handling support
 *                               including importing XML tokens definition file
 *                               (http://tools.ietf.org/html/draft-hoyer-keyprov-pskc-algorithm-profiles-00)
 *                              Enhanced flat database file format (multiotp is still compatible with old versions)
 *                              Internal method SetDataReadFlag renamed to SetUserDataReadFlag
 *                              Internal method GetDataReadFlag renamed to GetUserDataReadFlag
 *   2010-08-21 2.0.4  SysCo/al Enhancement in order to use an alternate php "compiler" for Windows command line
 *                              Documentation enhancement
 *   2010-08-18 2.0.3  SysCo/al Minor notice fix
 *   2010-07-21 2.0.2  SysCo/al Fix to create correctly the folders "users" and "log" if needed
 *   2010-07-19 2.0.1  SysCo/al Foreach was not working well in "compiled" Windows command line
 *   2010-07-19 2.0.0  SysCo/al New design using a class, mOTP support, cleaning of the code
 *   2010-06-15 1.1.5  SysCo/al Adding OATH/TOTP support
 *   2010-06-15 1.1.4  SysCo/al Project renamed to multiotp to avoid overlapping
 *   2010-06-08 1.1.3  SysCo/al Typo in script folder detection
 *   2010-06-08 1.1.2  SysCo/al Typo in variable name
 *   2010-06-08 1.1.1  SysCo/al Status bar during resynchronization
 *   2010-06-08 1.1.0  SysCo/al Fix in the example, distribution not compressed
 *   2010-06-07 1.0.0  SysCo/al Initial implementation
 *********************************************************************/

/**
 * @class  Multiotp
 * @brief  Main class definition of the multiOTP project.
 *
 * @author  Andre Liechti, SysCo systemes de communication sa, <developer@sysco.ch>
 * @version 4.0.4
 * @date    2013-08-20
 * @since   2010-07-18
 */
 class Multiotp
{
    var $_version;                  // Current version of the library
    var $_date;                     // Current date of the library
    var $_copyright;                // Copyright message of the library, don't change it !
    var $_website;                  // Website dedicated to this LGPL library, please don't change it !

    var $_base_dir;                 // Specific base directory
    var $_valid_algorithms;         // String containing valid algorithms to be used, separated by *, like *mOTP*HOTP*TOTP*
    var $_attributes_to_encrypt;    // Attributes to encrypt in the flat files
    var $_encryption_key;           // Symetric encryption key for the users files and the tokens files
    var $_source_tag;               // Source tag of the request (for a shared installation for example)
    var $_source_ip;                // Source IP of the request (for a RADIUS request for example, Packet-Src-IP-Address)
    var $_source_mac;               // Source MAC of the request (for a RADIUS request for example, Called-Station-Id)
    var $_calling_ip;               // Source IP of the request (for a RADIUS request for example, Framed-IP-Address)
    var $_calling_mac;              // Source MAC of the request (for a RADIUS request for example, Calling-Station-Id)
    var $_chap_challenge;           // CHAP-Challenge (instead of traditionnal PAP password)
    var $_chap_id;                  // CHAP-Id (instead of traditionnal PAP password)
    var $_chap_password;            // CHAP-Password (instead of traditionnal PAP password)
    var $_ms_chap_challenge;        // MS-CHAP
    var $_ms_chap_response;         // MS-CHAP
    var $_ms_chap2_response;        // MS-CHAP2
    var $_errors_text;              // An array containing errors text description
    var $_config_data;              // An array with all the general config related info
    var $_config_folder;            // Folder where the general config file is written
    var $_user;                     // Current user, case insensitive
    var $_user_data;                // An array with all the user related info
    var $_user_data_read_flag;      // Indicate if the user data has been read from the database file
    var $_users_folder;             // Folder where users definition files are stored
    var $_devices_folder;           // Folder where devices definition files are stored
    var $_groups_folder;            // Folder where groups definition files are stored
    var $_token;                    // Current token, case insensitive
    var $_token_data;               // An array with all the token related info
    var $_token_data_read_flag;     // Indicate if the token data has been read from the database file
    var $_tokens_folder;            // Folder where tokens definition files are stored
    var $_log_folder;               // Folder where log file is written
    var $_log_file_name;            // Name of the log file
    var $_log_flag;                 // Enable or disable the log
    var $_log_header_written;       // Internal flag to know if the header was already written or not in the log file
    var $_log_verbose_flag;         // Enable or disable the verbose mode for the log
    var $_log_display_flag;         // Log will also be displayed on the console
    var $_last_imported_tokens;     // An array containing the names (which are mostly the serials) of the last imported tokens
    var $_reply_array_for_radius;   // Specific reply message(s) for the radius (to be displayed in all cases by the command line tool)
    var $_initialize_backend;       // Initialize backend flag
    var $_scratch_passwords_amount; // Number of scratch passwords to generate
    var $_debug_via_html;           // Set the debug output to HTML standard
    var $_linux_file_mode;          // File mode of the created linux files in octal (for example '0644')
    var $_server_challenge;         // Server challenge for client-server mutual authentication
    var $_servers_last_timeout;     // Last time all servers where timed out
    var $_servers_retry_delay;      // Next time servers will be retried in s (in the same object)
    var $_xml_dump_in_log;          // For internal debugging only

    
    function Multiotp($encryption_key = '', $initialize_backend = FALSE, $base_dir = '')
    /**
     * @brief   Class constructor.
     *
     * @param   string  $encryption_key      A specific encryption key to encrypt stored data instead of the default one.
     * @param   boolean $initialize_backend  If we initialize the backend, we don't want to write in the database before the end of the initialization.
     * @retval  void
     *
     * @author  Andre Liechti, SysCo systemes de communication sa, <developer@sysco.ch>
     * @version 4.0.4
     * @date    2013-08-20
     * @since   2010-07-18
     */
    {
        $this->_class                   = 'multiOTP';
        $this->_version                 = '4.0.4'; // You should add a suffix for your changes (for example 4.0.0-andy-07)
        $this->_date                    = '2013-08-20'; // You should add a suffix for your changes (for example YYYY-MM-DD / YYY2-M2-D2)
        $this->_copyright               = '(c) 2010-2013 SysCo systemes de communication sa'; // This is a copyright, don't change it !
        $this->_website                 = 'http://www.multiOTP.net'; // Website dedicated to this LGPL library, please don't change it !
        
        $this->_log_header_written      = FALSE; // Flag indicating if the header has already been written in the log file or not
        $this->_valid_algorithms        = '*mOTP*HOTP*TOTP*'; // Supported algorithms, don't change it (unless you have added the handling of a new algorithm ;-)
        $this->_attributes_to_encrypt   = '*admin_password_hash*ldap_password*scratch_passwords*seed_password*server_secret*sms_api_id*sms_otp*sms_password*sms_userkey*sql_password*token_seed*user_pin*'; // This default list of attributes can be changed using SetAttributesToEncrypt(). Each attribute must be between "*".
        if ('' == $encryption_key)
        {
            $this->_encryption_key = 'MuLtIoTpEnCrYpTiOn'; // This default value should be changed for each project using SetEncryptionKey()
        }
        else
        {
            $this->_encryption_key = $encryption_key;
        }
        
        $this->_base_dir                = $base_dir;
        
        $this->_source_tag              = '';

        $this->_source_ip               = '';
        $this->_source_mac              = '';
        
        $this->_calling_ip              = '';
        $this->_calling_mac             = '';

        $this->_chap_challenge          = '';
        $this->_chap_id                 = '';
        $this->_chap_password           = '';
        
        $this->_user                    = ''; // Name of the current user to authenticate
        $this->_user_data_read_flag     = FALSE; // Flag to know if the data concerning the current user has been read
        $this->_users_folder            = ''; // Folders which contain the users flat files
        
        $this->_log_file_name           = 'multiotp.log';
        $this->_log_flag                = FALSE;
        $this->_log_folder              = ''; // Folder which contains the log file
        $this->_log_verbose_flag        = FALSE;
        $this->_log_display_flag        = FALSE;
        
        $this->_mysql_database_link     = NULL;
        
        $this->_migration_from_file     = FALSE; // To allow an automatic migration of users profiles,
                                                 // enable a database backend and set the migration option ;-) !

        $this->_reply_array_for_radius = array();

        $this->_initialize_backend = $initialize_backend;
        
        $this->_debug_via_html = FALSE;
        
        $this->_scratch_passwords_amount = 10;
        
        $this->_linux_file_mode = '';

        $this->_server_challenge = $this->_encryption_key;

        $this->_servers_last_timeout = 0;
        $this->_servers_retry_delay  = 10;

        $this->_keep_local = FALSE;
        
        $this->_xml_dump_in_log = FALSE; // For debugging purpose only
        
        $this->_sql_tables_schema['config']  = array('actual_version'          => "varchar(255) DEFAULT ''",
                                                     'admin_password_hash'     => "varchar(255) DEFAULT ''",
                                                     //'backend_type'          => "varchar(255) DEFAULT ''",
                                                     //'backend_type_validated'=> "int(10) DEFAULT 0",
                                                     'debug'                   => "int(10) DEFAULT 0",
                                                     'display_log'             => "int(10) DEFAULT 0",
                                                     'failure_delayed_time'    => "int(10) DEFAULT 0",
                                                     'group_attribute'         => "varchar(255) DEFAULT ''",
                                                     'ldap_account_suffix'     => "varchar(255) DEFAULT ''",
                                                     'ldap_base_dn'            => "varchar(255) DEFAULT ''",
                                                     'ldap_bind_dn'            => "varchar(255) DEFAULT ''",
                                                     'ldap_cn_identifier'      => "varchar(255) DEFAULT ''",
                                                     'ldap_domain_controllers' => "varchar(255) DEFAULT ''",
                                                     'ldap_domain_name'        => "varchar(255) DEFAULT ''",
                                                     'ldap_password'           => "varchar(255) DEFAULT ''",
                                                     'ldap_port'               => "varchar(255) DEFAULT ''",
                                                     'ldap_username'           => "varchar(255) DEFAULT ''",
                                                     'log'                     => "int(10) DEFAULT 0",
                                                     'max_block_failures'      => "int(10) DEFAULT 0",
                                                     'max_delayed_failures'    => "int(10) DEFAULT 0",
                                                     'max_event_resync_window' => "int(10) DEFAULT 0",
                                                     'max_event_window'        => "int(10) DEFAULT 0",
                                                     'max_time_resync_window'  => "int(10) DEFAULT 0",
                                                     'max_time_window'         => "int(10) DEFAULT 0",
                                                     'scratch_passwords_digits'=> "int(10) DEFAULT 6",
                                                     'server_cache_level'      => "int(10) DEFAULT 0",
                                                     'server_cache_lifetime'   => "int(10) DEFAULT 15552000",
                                                     'server_secret'           => "varchar(255) DEFAULT ''",
                                                     'server_timeout'          => "int(10) DEFAULT 5",
                                                     'server_type'             => "varchar(255) DEFAULT 'xml'",
                                                     'server_url'              => "varchar(255) DEFAULT ''",
                                                     'sms_api_id'              => "varchar(255) DEFAULT ''",
                                                     'sms_message_prefix'      => "varchar(255) DEFAULT ''",
                                                     'sms_originator'          => "varchar(255) DEFAULT ''",
                                                     'sms_password'            => "varchar(255) DEFAULT ''",
                                                     'sms_provider'            => "varchar(255) DEFAULT ''",
                                                     'sms_userkey'             => "varchar(255) DEFAULT ''",
                                                     'sms_digits'              => "int(10) DEFAULT 0",
                                                     'sms_timeout'             => "int(10) DEFAULT 0",
                                                     //'sql_server'              => "varchar(255) DEFAULT ''",
                                                     //'sql_username'            => "varchar(255) DEFAULT ''",
                                                     //'sql_password'            => "varchar(255) DEFAULT ''",
                                                     //'sql_database'            => "varchar(255) DEFAULT ''",
                                                     //'sql_config_table'        => "varchar(255) DEFAULT ''",
                                                     'sql_devices_table'       => "varchar(255) DEFAULT ''",
                                                     'sql_groups_table'        => "varchar(255) DEFAULT ''",
                                                     'sql_log_table'           => "varchar(255) DEFAULT ''",
                                                     'sql_tokens_table'        => "varchar(255) DEFAULT ''",
                                                     'sql_users_table'         => "varchar(255) DEFAULT ''",
                                                     'tel_default_country_code'=> "varchar(255) DEFAULT ''",
                                                     'verbose_log_prefix'      => "varchar(255) DEFAULT ''",
                                                     'encryption_hash'         => "varchar(255) DEFAULT ''");

        $this->_sql_tables_schema['devices'] = array('creation_time'           => "int(10) DEFAULT 0",
                                                     'description'             => "varchar(255) DEFAULT ''",
                                                     'device_group'            => "varchar(255) DEFAULT ''",
                                                     'device_secret'           => "varchar(255) DEFAULT ''",
                                                     'ip_or_fqdn'              => "varchar(255) DEFAULT ''",
                                                     'last_activity'           => "int(10) DEFAULT 0",
                                                     'encryption_hash'         => "varchar(255) DEFAULT ''");

        $this->_sql_tables_schema['groups']  = array('description'             => "varchar(255) DEFAULT ''",
                                                     'encryption_hash'         => "varchar(255) DEFAULT ''");

        $this->_sql_tables_schema['log']     = array('datetime'                => "datetime DEFAULT NULL",
                                                     'logentry'                => "text",
                                                     'user'                    => "varchar(255) DEFAULT ''");

        $this->_sql_tables_schema['tokens']  = array('algorithm'               => "varchar(255) DEFAULT ''",
                                                     'attributed_users'        => "varchar(255) DEFAULT ''",
                                                     'delta_time'              => "int(10) DEFAULT 0",
                                                     'error_counter'           => "int(10) DEFAULT 0",
                                                     'format'                  => "varchar(255) DEFAULT ''",
                                                     'issuer'                  => "varchar(255) DEFAULT ''",
                                                     'key_algorithm'           => "varchar(255) DEFAULT ''",
                                                     'last_error'              => "int(10) DEFAULT 0",
                                                     'last_event'              => "int(10) DEFAULT 0",
                                                     'last_login'              => "int(10) DEFAULT 0",
                                                     'locked'                  => "int(1) DEFAULT 0",
                                                     'manufacturer'            => "varchar(255) DEFAULT ''",
                                                     'number_of_digits'        => "int(10) DEFAULT 6",
                                                     'otp'                     => "varchar(255) DEFAULT ''",
                                                     'time_interval'           => "int(10) DEFAULT 0",
                                                     'token_id'                => "varchar(255) DEFAULT ''",
                                                     'token_seed'              => "varchar(255) DEFAULT ''",
                                                     'token_serial'            => "varchar(255) DEFAULT ''",
                                                     'encryption_hash'         => "varchar(255) DEFAULT ''");

        $this->_sql_tables_schema['users']   = array('algorithm'               => "varchar(255) DEFAULT ''",
                                                     'autolock_time'           => "int(10) DEFAULT 0",
                                                     'delta_time'              => "int(10) DEFAULT 0",
                                                     'description'             => "varchar(255) DEFAULT ''",
                                                     'email'                   => "varchar(255) DEFAULT ''",
                                                     'error_counter'           => "int(10) DEFAULT 0",
                                                     'group'                   => "varchar(255) DEFAULT ''",
                                                     'key_id'                  => "varchar(255) DEFAULT ''",
                                                     'last_error'              => "int(10) DEFAULT 0",
                                                     'last_event'              => "int(10) DEFAULT 0",
                                                     'last_login'              => "int(10) DEFAULT 0",
                                                     'locked'                  => "int(1) DEFAULT 0",
                                                     'multi_account'           => "int(10) DEFAULT 0",
                                                     'number_of_digits'        => "int(10) DEFAULT 6",
                                                     'request_prefix_ldap_pwd' => "int(10) DEFAULT 0",
                                                     'request_prefix_pin'      => "int(10) DEFAULT 0",
                                                     'scratch_passwords'       => "varchar(255) DEFAULT ''",
                                                     'seed_password'           => "varchar(255) DEFAULT ''",
                                                     'sms'                     => "varchar(255) DEFAULT ''",
                                                     'sms_otp'                 => "varchar(255) DEFAULT ''",
                                                     'sms_validity'            => "int(10) DEFAULT 0",
                                                     'time_interval'           => "int(10) DEFAULT 0",
                                                     'token_seed'              => "varchar(255) DEFAULT ''",
                                                     'token_serial'            => "varchar(255) DEFAULT ''",
                                                     'user'                    => "varchar(255) DEFAULT ''",
                                                     'user_pin'                => "varchar(255) DEFAULT ''",
                                                     'encryption_hash'         => "varchar(255) DEFAULT ''");

        // Reset/initialize the user array
        $this->ResetUserArray();
        
        // Reset/initialize the token array
        $this->ResetTokenArray();

        // Reset/initialize the config array
        $this->ResetConfigArray();
        
        // Reset/initialize the errors text array
        $this->ResetErrorsArray();

        // In case of initialization, we will disable the backend validation
        $this->ReadConfigData();
    }

    
    function UpgradeSchemaIfNeeded()
    {
        if ($this->GetActualVersion() != $this->GetVersion())
        {
            $this->InitializeBackend();
            $this->SetActualVersion($this->GetVersion());
            $this->WriteConfigData();
        }
    }


    // Reset the errors array
    function ResetErrorsArray()
    {
        $this->_errors_text[0] = "OK: Token accepted";

        $this->_errors_text[11] = "INFO: User successfully created or updated";
        $this->_errors_text[12] = "INFO: User successfully deleted";
        $this->_errors_text[13] = "INFO: User PIN code successfully changed";
        $this->_errors_text[14] = "INFO: Token has been resynchronized successfully";
        $this->_errors_text[15] = "INFO: Tokens definition file successfully imported";
        $this->_errors_text[16] = "INFO: QRcode successfully created";
        $this->_errors_text[17] = "INFO: UrlLink successfully created";
        $this->_errors_text[18] = "INFO: SMS code request received";
        $this->_errors_text[19] = "INFO: Requested operation successfully done";

        $this->_errors_text[21] = "ERROR: User doesn't exist";
        $this->_errors_text[22] = "ERROR: User already exists";
        $this->_errors_text[23] = "ERROR: Invalid algorithm";
        $this->_errors_text[24] = "ERROR: Token locked (too many tries)";
        $this->_errors_text[25] = "ERROR: Token delayed (too many tries, but still a hope in a few minutes)";
        $this->_errors_text[26] = "ERROR: The time based token has already been used";
        $this->_errors_text[27] = "ERROR: Resynchronization of the token has failed";
        $this->_errors_text[28] = "ERROR: Unable to write the changes in the file";
        $this->_errors_text[29] = "ERROR: Token doesn't exist";
        $this->_errors_text[30] = "ERROR: At least one parameter is missing";
        $this->_errors_text[31] = "ERROR: Tokens definition file doesn't exist";
        $this->_errors_text[32] = "ERROR: Tokens definition file not successfully imported";
        $this->_errors_text[33] = "ERROR: Encryption hash error, encryption key is not the same";
        $this->_errors_text[34] = "ERROR: Linked user doesn't exist";
        $this->_errors_text[35] = "ERROR: User not created";
        $this->_errors_text[39] = "ERROR: Requested operation aborted";
       
        $this->_errors_text[41] = "ERROR: SQL error";
        
        $this->_errors_text[50] = "ERROR: QRcode not created";
        $this->_errors_text[51] = "ERROR: UrlLink not created (no provisionable client for this protocol)";

        $this->_errors_text[60] = "ERROR: No information on where to send SMS code";
        $this->_errors_text[61] = "ERROR: SMS code request received, but an error occured during transmission";
        $this->_errors_text[62] = "ERROR: SMS provider not supported";
        
        $this->_errors_text[70] = "ERROR: Server authentication error";
        $this->_errors_text[71] = "ERROR: Server request is not correctly formatted";
        $this->_errors_text[72] = "ERROR: Server answer is not correctly formatted";
        
        $this->_errors_text[80] = "ERROR: Server cache error";
        $this->_errors_text[81] = "ERROR: Cache too old for this user, account autolocked";

        $this->_errors_text[99] = "ERROR: Authentication failed (and other possible unknown errors)";
    }
    
        
    // Reset the config array
    function ResetConfigArray()
    {
        // First, we reset all values (we know the key based on the schema)
        reset($this->_sql_tables_schema['config']);
        while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['config']))
        {
            $this->_config_data[$valid_key] = '';
        }

        // Backend storage type
        $this->_config_data['backend_type'] = 'files';     // Currently, only files and mysql are supported.
        $this->_config_data['backend_type_validated'] = 0; // By default, backend_type is not validated
        
        // Debug mode (to enable it permanently)
        $this->_config_data['debug'] = 0;
        
        // Display log mode (to enable it permanently)
        $this->_config_data['display_log'] = 0;
        
        // Locking delay in seconds between two trials after "max_delayed_failures" failures
        $this->_config_data['failure_delayed_time'] = 300;

        // Group attribute (mainly used for RADIUS answer)
        $this->_config_data['group_attribute'] = 'Filter-Id';
        
        // LDAP  connection information
        $this->_config_data['ldap_cn_identifier'] = 'sAMAccountName';
        $this->_config_data['ldap_port'] = '389';

        // Log mode (to enable it permanently)
        $this->_config_data['log'] = 0;
        
        // Number of consecutive failures before blocking the token. A blocked token needs a resync
        $this->_config_data['max_block_failures'] = 6;

        // Number of consecutive failures before locking and delaying the next request
        $this->_config_data['max_delayed_failures'] = 3;

        // Maximum number of events accepted to sync event based algorithm(s) token
        $this->_config_data['max_event_resync_window'] = 10000;

        // Maximum number of event gaps accepted for event based algorithm(s) token
        $this->_config_data['max_event_window'] = 100;

        // Maximum time window (in seconds) to be accepted for resync (+/-)
        // Initialized to more than +/- one day
        $this->_config_data['max_time_resync_window'] = 90000;

        // Maximum time window to be accepted, in seconds (+/-)
        // Initialized to a little bit more than +/- 10 minutes
        // (was 8000 seconds in version 3.x, and Stefan Kügler suggested to put a lower default value)
        $this->_config_data['max_time_window'] = 600;

        $this->_config_data['scratch_passwords_digits'] = 6;

        // Client-server configuration
        $this->_config_data['server_cache_level'] = 0;
        $this->_config_data['server_cache_lifetime'] = 15552000; // 6 monthes
        $this->_config_data['server_secret'] = 'ClientServerSecret';
        $this->_config_data['server_timeout'] = 5;
        $this->_config_data['server_type'] = 'xml';

        // SMS number of digits
        $this->_config_data['sms_digits'] = 6;

        // SMS message prefix
        // $this->_config_data['sms_message_prefix'] = 'Your SMS-Code is:';
        $this->_config_data['sms_message_prefix'] = '%s is your SMS-Code';

        // SMS originator/sender
        $this->_config_data['sms_originator'] = 'multiOTP';

        // SMS timeout before authenticating (in seconds)
        $this->_config_data['sms_timeout'] = 180;

        // Default SQL table names. If empty, the related data will be written to a file.
        $this->_config_data['sql_config_table']  = 'multiotp_config';
        $this->_config_data['sql_devices_table'] = 'multiotp_devices';
        $this->_config_data['sql_groups_table']  = 'multiotp_groups';
        $this->_config_data['sql_log_table']     = 'multiotp_log';
        $this->_config_data['sql_tokens_table']  = 'multiotp_tokens';
        $this->_config_data['sql_users_table']   = 'multiotp_users';
    }


    function SetConfigFolder($folder, $create = TRUE, $read_config = TRUE)
    /**
     * @brief   Set the configuration folder (for the config file).
     *
     * @param   string  $folder       Full path to the config folder.
     * @param   boolean $create       Create the folder if it doesn't exists.
     * @param   boolean $read_config  Read directly the configuration file.
     * @retval  void
     *
     * @author  Andre Liechti, SysCo systemes de communication sa, <developer@sysco.ch>
     * @version 4.0.0
     * @date    2013-05-13
     */
    {
        $new_folder = $this->ConvertToUnixPath($folder);
        if (substr($new_folder,-1) != "/")
        {
            $new_folder.="/";
        }
        $new_folder = $this->ConvertToWindowsPathIfNeeded($new_folder);
        $this->_config_folder = $new_folder;
        if ($create && (!file_exists($new_folder)))
        {
            if (!@mkdir($new_folder))
            {
                $this->WriteLog("Error: unable to create the missing config folder ".$new_folder);
            }
        }
        if ($read_config)
        {
            $this->ReadConfigData();
        }
    }


    function GetConfigFolder($create_if_not_exist = FALSE)
    /**
     * @brief   Get the configuration folder (for the config file).
     *
     * @param   boolean $create_if_not_exist Create the folder if it doesn't exists.
     * @retval  string  Full path to the config folder.
     *
     * @author  Andre Liechti, SysCo systemes de communication sa, <developer@sysco.ch>
     * @version 4.0.0
     * @date    2013-05-13
     */
    {
        $config_folder = $this->ConvertToWindowsPathIfNeeded($this->_config_folder);
        if ('' == $config_folder)
        {
            $this->SetConfigFolder($this->GetScriptFolder()."config/", $create_if_not_exist);
        }
        elseif (!file_exists($config_folder))
        {
            if ($create_if_not_exist)
            {
                if (!@mkdir($config_folder))
                {
                    $this->WriteLog("Error: unable to create the missing config folder ".$config_folder);
                }
            }
        }
        return $this->ConvertToWindowsPathIfNeeded($this->_config_folder);
    }

    
    function SetAdminPassword($password)
    {
        $this->SetConfigAttribute('admin_password_hash',md5($this->GetHashSalt().$password.$this->GetHashSalt()));
    }


    function SetAdminPasswordHash($password_hash)
    {
        $this->SetConfigAttribute('admin_password_hash',$password_hash);
    }

    // Regular security check: the client side should return md5(hash_salt + password + hash_salt)
    function CheckAdminPassword($password)
    {
        return ($this->GetConfigAttribute('admin_password_hash') == md5($this->GetHashSalt().$password.$this->GetHashSalt()));
    }
    
    
    // Better security check: the client side should return md5(salt + md5(hash_salt + password + hash_salt) + salt)
    function CheckAdminPasswordHashWithRandomSalt($password_hash_with_salt)
    {
        return md5($this->GetRandomSalt().$this->GetConfigAttribute('admin_password_hash').$this->GetRandomSalt()) == $password_hash_with_salt;
    }


    function EnableDebugViaHtml()
    {
        $this->_debug_via_html = TRUE;
    }


    function IsDebugViaHtml()
    {
        return ($this->_debug_via_html);
    }


    function EnableKeepLocal()
    {
        $this->_keep_local = TRUE;
    }


    function IsKeepLocal()
    {
        return ($this->_keep_local);
    }

    
    function SetLinuxFileMode($mode)
    {
        $this->_linux_file_mode = $mode;
    }


    function GetLinuxFileMode()
    {
        return ($this->_linux_file_mode);
    }

    
    function SetConfigData($key, $value)
    {
        if (isset($this->_config_data[$key]))
        {
            $this->_config_data[$key] = $value;
        }
    }


    function SetLogOption($value)
    {
        $this->_config_data['log'] = $value;
        if (1 == $this->_config_data['log'])
        {
            $this->EnableLog();
        }
    }


    function SetDebugOption($value)
    {
        $this->_config_data['debug'] = $value;
        if (1 == $this->_config_data['debug'])
        {
            $this->EnableVerboseLog();
        }
    }

    
    function SetDisplayLogOption($value)
    {
        $this->_config_data['display_log'] = $value;
        if (1 == $this->_config_data['display_log'])
        {
            $this->EnableDisplayLog();
        }
    }

    
    function SetMigrationFromFile($value)
    {
        $this->_migration_from_file = ($value?TRUE:FALSE);
    }

    
    function GetMigrationFromFile()
    {
        return $this->_migration_from_file;
    }

    
    function SetBackendType($type)
    {
        $this->_config_data['backend_type'] = $type;
        $this->_config_data['backend_type_validated'] = 0;
    }


    function GetBackendType()
    {
        return $this->_config_data['backend_type'];
    }


    function SetBackendTypeValidated($backend_type_validated, $value)
    {
        if ('' != $backend_type_validated)
        {
            $this->_config_data['backend_type'] = $backend_type_validated;
        }
        $this->_config_data['backend_type_validated'] = ($value?1:0);
    }

    function GetBackendTypeValidated()
    {
        return (1 == $this->_config_data['backend_type_validated']);
    }

    function SetScratchPasswordsDigits($value)
    {
        $this->_config_data['scratch_passwords_digits'] = $value;
    }


    function GetScratchPasswordsDigits()
    {
        return $this->_config_data['scratch_passwords_digits'];
    }


    function SetGroupAttribute($value)
    {
        $this->_config_data['group_attribute'] = $value;
    }


    function GetGroupAttribute()
    {
        return $this->_config_data['group_attribute'];
    }


    function SetSqlServer($server)
    {
        $this->_config_data['sql_server'] = $server;
    }


    function SetSqlUsername($username)
    {
        $this->_config_data['sql_username'] = $username;
    }


    function SetSqlPassword($password)
    {
        $this->_config_data['sql_password'] = $password;
    }


    function SetSqlDatabase($database)
    {
        $this->_config_data['sql_database'] = $database;
    }


    function SetSqlTableName($table_to_define, $table_name)
    {
        if (isset($this->_config_data['sql_'.$table_to_define.'_table']))
        {
            $this->_config_data['sql_'.$table_to_define.'_table'] = $table_name;
        }
    }


    function MySqlAddRowIfNeeded($table, $row, $row_type)
    {
        $result = FALSE;
        if (NULL != $this->_mysql_database_link)
        {
            $sql_query = "SELECT `".$row."` FROM ".$table;
            if (($select_row = mysql_query($sql_query, $this->_mysql_database_link)))
            {
                $result = TRUE;
            }
            elseif (!$select_row)
            {
                $sql_query = "ALTER TABLE ".$table." ADD `".$row."` ".$row_type;
                if (!mysql_query($sql_query, $this->_mysql_database_link))
                {
                    $this->WriteLog(mysql_error().' '.$sql_query, TRUE);
                    $result = FALSE;
                }
            }
        }
        elseif ($this->GetVerboseFlag())
        {
            $this->WriteLog('KO, the link is down!', TRUE);
        }
        return $result;
    }
    
    
    function OpenMysqlDatabase()
    {
        if (NULL != $this->_mysql_database_link)
        {
            $result = TRUE;
        }
        else
        {
            $result = FALSE;
            if (('' != $this->_config_data['sql_server']) &&
                ('' != $this->_config_data['sql_username']) &&
                ('' != $this->_config_data['sql_password']) &&
                ('' != $this->_config_data['sql_database']))
            {
                if (!($this->_mysql_database_link = mysql_connect($this->_config_data['sql_server'],
                                                                  $this->_config_data['sql_username'],
                                                                  $this->_config_data['sql_password'])))
                {
                    $this->WriteLog("Error: Bad SQL authentication parameters, ".mysql_error(), TRUE);
                }
                else
                {
                    if (!mysql_select_db($this->_config_data['sql_database']))
                    {
                        $this->WriteLog("Error: Bad SQL database", TRUE);
                        mysql_close($this->_mysql_database_link);
                        $this->_mysql_database_link = NULL;
                    }
                    else
                    {
                        $result = TRUE;
                    }
                }
            }
        }
        return $result;
    }


    function InitializeBackend()
    {
        $write_config_data = FALSE;
        
        $backend_type = $this->GetBackendType();
        if ('mysql' == $backend_type)
        {
            if ($this->OpenMysqlDatabase())
            {
                if ('' != $this->_config_data['sql_config_table'])
                {
                    if (!mysql_query("CREATE TABLE IF NOT EXISTS `".$this->_config_data['sql_config_table']."` (unique_id bigint(20) NOT NULL AUTO_INCREMENT, PRIMARY KEY (unique_id));", $this->_mysql_database_link))
                    {
                        $this->WriteLog("Error: Bad SQL request (CREATE TABLE config), ".mysql_error(), TRUE);
                        return 41;
                    }
                    reset($this->_sql_tables_schema['config']);
                    while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['config']))
                    {
                        $this->MySqlAddRowIfNeeded($this->_config_data['sql_config_table'], $valid_key, $valid_format);
                    }                            
                }
                if ('' != $this->_config_data['sql_devices_table'])
                {
                    if (!mysql_query("CREATE TABLE IF NOT EXISTS `".$this->_config_data['sql_devices_table']."` (unique_id bigint(20) NOT NULL AUTO_INCREMENT, PRIMARY KEY (unique_id));", $this->_mysql_database_link))
                    {
                        $this->WriteLog("Error: Bad SQL request (CREATE TABLE devices), ".mysql_error(), TRUE);
                        return 41;
                    }
                    reset($this->_sql_tables_schema['devices']);
                    while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['devices']))
                    {
                        $this->MySqlAddRowIfNeeded($this->_config_data['sql_devices_table'], $valid_key, $valid_format);
                    }                            
                }
                if ('' != $this->_config_data['sql_groups_table'])
                {
                    if (!mysql_query("CREATE TABLE IF NOT EXISTS `".$this->_config_data['sql_groups_table']."` (unique_id bigint(20) NOT NULL AUTO_INCREMENT, PRIMARY KEY (unique_id));", $this->_mysql_database_link))
                    {
                        $this->WriteLog("Error: Bad SQL request (CREATE TABLE groups), ".mysql_error(), TRUE);
                        return 41;
                    }
                    reset($this->_sql_tables_schema['groups']);
                    while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['groups']))
                    {
                        $this->MySqlAddRowIfNeeded($this->_config_data['sql_groups_table'], $valid_key, $valid_format);
                    }                            
                }
                if ('' != $this->_config_data['sql_log_table'])
                {
                    if (!mysql_query("CREATE TABLE IF NOT EXISTS `".$this->_config_data['sql_log_table']."` (unique_id bigint(20) NOT NULL AUTO_INCREMENT, PRIMARY KEY (unique_id));", $this->_mysql_database_link))
                    {
                        $this->WriteLog("Error: Bad SQL request (CREATE TABLE logs), ".mysql_error(), TRUE);
                        return 41;
                    }
                    reset($this->_sql_tables_schema['log']);
                    while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['log']))
                    {
                        $this->MySqlAddRowIfNeeded($this->_config_data['sql_log_table'], $valid_key, $valid_format);
                    }                            
                }
                if ('' != $this->_config_data['sql_tokens_table'])
                {
                    if (!mysql_query("CREATE TABLE IF NOT EXISTS `".$this->_config_data['sql_tokens_table']."` (unique_id bigint(20) NOT NULL AUTO_INCREMENT, PRIMARY KEY (unique_id));", $this->_mysql_database_link))
                    {
                        $this->WriteLog("Error: Bad SQL request (CREATE TABLE tokens), ".mysql_error(), TRUE);
                        return 41;
                    }
                    reset($this->_sql_tables_schema['tokens']);
                    while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['tokens']))
                    {
                        $this->MySqlAddRowIfNeeded($this->_config_data['sql_tokens_table'], $valid_key, $valid_format);
                    }                            
                }
                if ('' != $this->_config_data['sql_users_table'])
                {
                    if (!mysql_query("CREATE TABLE IF NOT EXISTS `".$this->_config_data['sql_users_table']."` (unique_id bigint(20) NOT NULL AUTO_INCREMENT, PRIMARY KEY (unique_id));", $this->_mysql_database_link))
                    {
                        $this->WriteLog("Error: Bad SQL request (CREATE TABLE users), ".mysql_error(), TRUE);
                        return 41;
                    }
                    reset($this->_sql_tables_schema['users']);
                    while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['users']))
                    {
                        $this->MySqlAddRowIfNeeded($this->_config_data['sql_users_table'], $valid_key, $valid_format);
                    }                            
                }
                $this->SetBackendTypeValidated($backend_type, TRUE);
                $write_config_data = TRUE;
            }
        }
        if ($write_config_data)
        {
            $this->WriteConfigData();
        }
        return 19;
    }


    function IsConfigOptionInSchema($schema, $option)
    {
        $in_the_schema = FALSE;
        if (isset($this->_sql_tables_schema[$schema]))
        {
            reset($this->_sql_tables_schema[$schema]);
            while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema[$schema]))
            {
                if ($valid_key == $option)
                {
                    $in_the_schema = TRUE;
                }
            }
        }
        return $in_the_schema;
    }


    function ReadConfigData()
    {
        $result = FALSE;
        
        // We initialize the encryption hash to empty
        $this->_config_data['encryption_hash'] = '';

        // First, we read the config file in any case
        $config_filename = 'multiotp.ini'; // File exists in v3 format only, we don't need any conversion
        if (file_exists($this->GetConfigFolder().$config_filename))
        {
            $config_file_handler = fopen($this->GetConfigFolder().$config_filename, "rt");
            $first_line = trim(fgets($config_file_handler));
            
            while (!feof($config_file_handler))
            {
                $line = str_replace(chr(10), '', str_replace(chr(13), '', fgets($config_file_handler)));
                $line_array = explode("=",$line,2);
                if (('#' != substr($line, 0, 1)) && (';' != substr($line, 0, 1)) && ('' != trim($line)) && (isset($line_array[1])))
                {
                    if (":" == substr($line_array[0], -1))
                    {
                        $line_array[0] = substr($line_array[0], 0, strlen($line_array[0]) -1);
                        $line_array[1] = $this->Decrypt($line_array[0],$line_array[1]);
                    }
                    if ('' != $line_array[0])
                    {
                        $this->_config_data[strtolower($line_array[0])] = $line_array[1];
                    }
                }
            }
            fclose($config_file_handler);
            $result = TRUE;
            if ('' != $this->_config_data['encryption_hash'])
            {
                if ($this->_config_data['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey()))
                {
                    $this->_config_data['encryption_hash'] = "ERROR";
                    $this->WriteLog("Error: the file encryption key has been changed");
                    $result = FALSE;
                }
            }
        }
        
        if ($this->_initialize_backend)
        {
            $this->SetBackendTypeValidated('', FALSE);
            $this->WriteConfigData();
        }
        // And now, we override the values if another backend type is defined
        if ($this->GetBackendTypeValidated())
        {
            switch ($this->GetBackendType())
            {
                case 'mysql':
                    if ($this->OpenMysqlDatabase())
                    {
                        if ('' != $this->_config_data['sql_config_table'])
                        {
                            $sQuery  = "SELECT * FROM `".$this->_config_data['sql_config_table']."` ";
                            if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                            {
                                $this->WriteLog(mysql_error().' '.$sQuery, TRUE);
                            }
                            else
                            {
                                $aRow    = mysql_fetch_assoc($rResult);
                                $result = TRUE;
                                while(list($key, $value) = @each($aRow))
                                {
                                    $in_the_schema = FALSE;
                                    reset($this->_sql_tables_schema['config']);
                                    while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['config']))
                                    {
                                        if ($valid_key == $key)
                                        {
                                            $in_the_schema = TRUE;
                                        }
                                    }
                                    if ($in_the_schema)
                                    {
                                        // This was the old rule, but it's not a good one
                                        // if (FALSE !== strpos(strtolower($this->_attributes_to_encrypt), strtolower('*'.$key.'*')))
                                        if (('ENC:' == substr($value,0,4)) && (':ENC' == substr($value,-4)))
                                        {
                                            $value = substr($value,4);
                                            $value = substr($value,0,strlen($value)-4);
                                            $this->_config_data[$key] = $this->Decrypt($key,$value);
                                        }
                                        else
                                        {
                                            $this->_config_data[$key] = $value;
                                        }
                                    }
                                    elseif (('unique_id' != $key) && $this->GetVerboseFlag())
                                    {
                                        $this->WriteLog("Warning: the key ".$key." is not in the config database schema");
                                    }
                                }
                            }
                        }
                        if ('' != $this->_config_data['encryption_hash'])
                        {
                            if ($this->_config_data['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey()))
                            {
                                $this->_config_data['encryption_hash'] = "ERROR";
                                $this->WriteLog("Error: the mysql encryption key has been changed");
                                $result = FALSE;
                            }
                        }
                    }
                    break;
                default:
                // Nothing to do if the backend type is unknown
                    break;
            }
        }

        
        if (1 == $this->_config_data['log'])
        {
            $this->EnableLog();
        }

        if (1 == $this->_config_data['debug'])
        {
            $this->EnableVerboseLog();
        }
        
        if (1 == $this->_config_data['display_log'])
        {
            $this->EnableDisplayLog();
        }
        
        return $result;
    }


    function WriteConfigData($file_only = FALSE)
    {
        $config_filename = 'multiotp.ini';
        $file_created = (!file_exists($this->GetConfigFolder().$config_filename));
        $result = FALSE;
        // First, we write the config file in any case

        if (!($config_file_handler = fopen($this->GetConfigFolder(TRUE).$config_filename, "wt")))
        {
            $this->WriteLog("Error: config file cannot be written");
        }
        else
        {
            fwrite($config_file_handler,"multiotp-database-format-v3"."\n");
            fwrite($config_file_handler,"; If backend is set to something different than files,\n");
            fwrite($config_file_handler,"; and backend_type_validated is set to 1,\n");
            fwrite($config_file_handler,"; only the specific information needed for the backend\n");
            fwrite($config_file_handler,"; is used from this config file.\n");
            fwrite($config_file_handler,"\n");
            reset($this->_config_data);
            while(list($key, $value) = each($this->_config_data))
            {
                if ('' != trim($key))
                {
                    $line = strtolower($key);
                    if (FALSE !== strpos(strtolower($this->_attributes_to_encrypt), strtolower('*'.$key.'*')))
                    {
                        $value = $this->Encrypt($key,$value);
                        $line = $line.":";
                    }
                    $line = $line."=".$value;
                    fwrite($config_file_handler,$line."\n");
                }
            }
            $result = TRUE;
            fclose($config_file_handler);
            if ($file_created && ('' != $this->GetLinuxFileMode()))
            {
                chmod($this->GetConfigFolder().$config_filename, octdec($this->GetLinuxFileMode()));
            }
        }
   
        $this->_config_data['encryption_hash'] = $this->CalculateControlHash($this->GetEncryptionKey());
 
        // And now, we write in the specific backend type if another one is defined
        if ($this->GetBackendTypeValidated() && (!$file_only))
        {
            switch ($this->GetBackendType())
            {
                case 'mysql':
                    if ($this->OpenMysqlDatabase())
                    {
                        if ('' != $this->_config_data['sql_config_table'])
                        {
                            $sQi_Columns = '';
                            $sQi_Values  = '';
                            $sQu_Data    = '';
                            reset($this->_config_data);
                            while(list($key, $value) = each($this->_config_data))
                            {
                                $in_the_schema = FALSE;
                                reset($this->_sql_tables_schema['config']);
                                while(list($valid_key, $valid_format) = each($this->_sql_tables_schema['config']))
                                {
                                    if ($valid_key == $key)
                                    {
                                        $in_the_schema = TRUE;
                                    }
                                }
                                if ($in_the_schema) 
                                {
                                    if ((FALSE !== strpos(strtolower($this->_attributes_to_encrypt), strtolower('*'.$key.'*'))) && ('' != $value))
                                    {
                                        $value = 'ENC:'.$this->Encrypt($key,$value).':ENC';
                                    }
                                    $sQu_Data    .= "`{$key}`='{$value}',"; // Data for UPDATE query
                                    $sQi_Columns .= "`{$key}`,"; // Columns for INSERT query
                                    $sQi_Values  .= "'{$value}',"; // Values for INSERT query
                                }
                                /*
                                 * Some keys or not in the configuration table for good reasons,
                                 * that's why we do not write anything about that in the log.
                                 *
                                elseif (('unique_id' != $key) && $this->GetVerboseFlag())
                                {
                                    $this->WriteLog("Warning: the key ".$key." is not in the config database schema");
                                }
                                */
                            }
                            $sQuery = "SELECT * FROM `".$this->_config_data['sql_config_table']."`";
                            if (!($result = mysql_query($sQuery, $this->_mysql_database_link)))
                            {
                                $this->WriteLog("Error: SQL database query error ($sQuery) : ".mysql_error(), TRUE);
                            }
                            $num_rows = mysql_num_rows($result);
                            if ($num_rows > 0)
                            {
                                $sQuery = "UPDATE `".$this->_config_data['sql_config_table']."` SET ".substr($sQu_Data,0,-1);
                                if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                                {
                                    $this->WriteLog("Error: SQL database query error ($sQuery) : ".mysql_error(), TRUE);
                                }
                            }
                            else
                            {
                                $sQuery = "INSERT INTO `".$this->_config_data['sql_config_table']."` (".substr($sQi_Columns,0,-1).") VALUES (".substr($sQi_Values,0,-1).")";
                                if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link) or $this->WriteLog(mysql_error())))
                                {
                                    $this->WriteLog("Error: SQL database query error ($sQuery) : ".mysql_error(), TRUE);
                                    $result = FALSE;
                                }
                                elseif (0 == mysql_affected_rows($this->_mysql_database_link))
                                {
                                    $this->WriteLog("Error: SQL database entry for config cannot be created or changed");
                                    $result = FALSE;
                                }
                            }
                        }
                    }
                    break;
                default:
                    break;
            }
        }
        return $result;
    }


    // Reset the user array
    function ResetUserArray()
    {
        // First, we reset all values (we know the key based on the schema)
        reset($this->_sql_tables_schema['users']);
        while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['users']))
        {
            $this->_user_data[$valid_key] = '';
        }

        // User is a special multi-account user (the real user is in the token, like this: "user[space]token"
        $this->_user_data['multi_account'] = 0;

        // User sms otp validity
        $this->_user_data['sms_validity'] = 0;
        
        // Time interval in seconds for a time based token
        $this->_user_data['time_interval'] = 0;
        
        // Number of digits returned by the token
        $this->_user_data['number_of_digits'] = 6;
        
        // Request the pin as a prefix of the returned token value
        $this->_user_data['request_prefix_pin'] = 0;
        
        // Request the ldap password as a prefix of the returned token value
        $this->_user_data['request_prefix_ldap_pwd'] = 0;
        
        // Last successful login
        $this->_user_data['last_login'] =  0;
        
        // Last successful event
        $this->_user_data['last_event'] = -1;
        
        // Last error login
        $this->_user_data['last_error'] =  0;
        
        // Autolock time (for cached data)
        $this->_user_data['autolock_time'] = 0;
        
        // Delta time in seconds for a time based token
        $this->_user_data['delta_time'] = 0;
        
        // Token seed, default set to the RFC test seed, hexadecimal coded
        $this->_user_data['token_seed'] = '3132333435363738393031323334353637383930';

        // Login error counter
        $this->_user_data['error_counter'] = 0;

        // Token locked
        $this->_user_data['locked'] = 0;

        // The user data array is not read actually
        $this->SetUserDataReadFlag(FALSE);
    }

    
    function ResetTokenArray()
    {
        // First, we reset all values (we know the key based on the schema)
        reset($this->_sql_tables_schema['tokens']);
        while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['tokens']))
        {
            $this->_token_data[$valid_key] = '';
        }

        // Token encryption hash
        $this->_token_data['manufacturer'] = 'multiOTP';
        $this->_token_data['issuer'] = 'multiOTP';
        
        // Number of digits returned by the token
        $this->_token_data['number_of_digits'] = 6;
        
        // Last successful event
        $this->_token_data['last_event'] = -1;
        
        $this->_token_data['delta_time'] = 0;
        $this->_token_data['time_interval'] = 0;
        
        // Token seed, default set to the RFC test seed, hexadecimal coded
        $this->_token_data['token_seed'] = '3132333435363738393031323334353637383930';
        
        // Last successful login
        $this->_token_data['last_login'] =  0;
        
        // Last error login
        $this->_token_data['last_error'] =  0;
        
        // Login error counter
        $this->_token_data['error_counter'] = 0;

        // Token locked
        $this->_token_data['locked'] = 0;
        
        // The token data array is not read actually
        $this->SetTokenDataReadFlag(FALSE);
    }

    
    function CleanPhoneNumber($phone_number)
    {
        $pn = trim(preg_replace('[\D]', '', $phone_number));
        // $pn_len = strlen($pn);
      
        if ('00' == substr($pn,0, 2))
        {
            $pn = substr($pn, 2);
        }
        elseif ('0' == substr($pn,0, 1))
        {
            $pn = $this->GetTelDefaultCountryCode() . substr($pn, 1);
        }

        return $pn;
    }

    function GetClassName()
    {
        return $this->_class;
    }
    
    
    function GetVersion()
    {
        return $this->_version;
    }
    
    
    function GetDate()
    {
        return $this->_date;
    }

    
    function GetVersionDate()
    {
        return $this->_version." (".$this->_date.")";
    }
    
    
    function GetFullVersionInfo()
    {
        return $this->_class." ".$this->_version." (".$this->_date.")";
    }
    

    function GetCopyright()
    {
        return $this->_copyright;
    }


    function GetWebsite()
    {
        return $this->_website;
    }

    
    function SetSourceTag($value)
    {
        $this->_source_tag = $value;
    }
    

    function GetSourceTag()
    {
        return $this->_source_tag;
    }


    function SetSourceIp($value)
    {
        $this->_source_ip = $value;
    }
    

    function GetSourceIp()
    {
        return $this->_source_ip;
    }


    function SetSourceMac($value)
    {
        $this->_source_mac = $value;
    }
    

    function GetSourceMac()
    {
        return $this->_source_mac;
    }


    function SetCallingIp($value)
    {
        $this->_calling_ip = $value;
    }
    

    function GetCallingIp()
    {
        return $this->_calling_ip;
    }


    function SetCallingMac($value)
    {
        $this->_calling_mac = $value;
    }
    

    function GetCallingMac()
    {
        return $this->_calling_mac;
    }

    
    function SetChapChallenge($hex_value)
    {
        $pos = strpos(strtolower($hex_value), 'x');
        if (FALSE === $pos)
        {
            $temp = $hex_value;
        }
        else
        {
            $temp = substr($hex_value, $pos+1);
        }
        $this->_chap_challenge = strtolower($temp);
    }
    

    function GetChapChallenge()
    {
        return strtolower($this->_chap_challenge);
    }

    
    function SetChapPassword($hex_value)
    {
        $pos = strpos(strtolower($hex_value), 'x');
        if (FALSE === $pos)
        {
            $temp = $hex_value;
        }
        else
        {
            $temp = substr($hex_value, $pos+1);
        }
        
        if (32 < strlen($temp))
        {
            $this->SetChapId(substr($temp, 0, 2));
            $temp = substr($temp, 2);
        }
        $this->_chap_password = strtolower($temp);
    }
    

    function GetChapPassword()
    {
        return strtolower($this->_chap_password);
    }


    function SetMsChapChallenge($hex_value)
    {
        $pos = strpos(strtolower($hex_value), 'x');
        if (FALSE === $pos)
        {
            $temp = $hex_value;
        }
        else
        {
            $temp = substr($hex_value, $pos+1);
        }
        
        $this->_ms_chap_challenge = strtolower($temp);
    }
    

    function GetMsChapChallenge()
    {
        return strtolower($this->_ms_chap_challenge);
    }

    
    function SetMsChapResponse($hex_value)
    {
        $pos = strpos(strtolower($hex_value), 'x');
        if (FALSE === $pos)
        {
            $temp = $hex_value;
        }
        else
        {
            $temp = substr($hex_value, $pos+1);
        }
        
        $this->_ms_chap_response = strtolower($temp);
    }
    

    function GetMsChapResponse()
    {
        return strtolower($this->_ms_chap_response);
    }


    function SetMsChap2Response($hex_value)
    {
        $pos = strpos(strtolower($hex_value), 'x');
        if (FALSE === $pos)
        {
            $temp = $hex_value;
        }
        else
        {
            $temp = substr($hex_value, $pos+1);
        }
        
        $this->_ms_chap2_response = strtolower($temp);
    }
    

    function GetMsChap2Response()
    {
        return strtolower($this->_ms_chap2_response);
    }


    function SetChapId($hex_value)
    {
        $pos = strpos(strtolower($hex_value), 'x');
        if (FALSE === $pos)
        {
            $temp = $hex_value;
        }
        else
        {
            $temp = substr($hex_value, $pos+1);
        }
        $this->_chap_id = substr(strtolower($temp), 0, 2);
    }
    

    function GetChapId()
    {
        return strtolower($this->_chap_id);
    }


    function SetSmsProvider($value)
    {
        $this->_config_data['sms_provider'] = $value;
    }


    function GetSmsProvider()
    {
        return $this->_config_data['sms_provider'];
    }


    function SetSmsOriginator($value)
    {
        $this->_config_data['sms_originator'] = $value;
    }


    function GetSmsOriginator()
    {
        return $this->_config_data['sms_originator'];
    }

    
    function SetTelDefaultCountryCode($value)
    {
        $this->_config_data['tel_default_country_code'] = $value;
    }


    function GetTelDefaultCountryCode()
    {
        return $this->_config_data['tel_default_country_code'];
    }


    function SetSmsUserkey($value)
    {
        $this->_config_data['sms_userkey'] = $value;
    }


    function GetSmsUserkey()
    {
        return $this->_config_data['sms_userkey'];
    }


    function SetSmsPassword($value)
    {
        $this->_config_data['sms_password'] = $value;
    }


    function GetSmsPassword()
    {
        return $this->_config_data['sms_password'];
    }


    function SetSmsApiId($value)
    {
        $this->_config_data['sms_api_id'] = $value;
    }


    function GetSmsApiId()
    {
        return $this->_config_data['sms_api_id'];
    }


    function SetLdapAccountSuffix($value)
    {
        $this->_config_data['ldap_account_suffix'] = $value;
    }


    function GetLdapAccountSuffix()
    {
        return $this->_config_data['ldap_account_suffix'];
    }


    function SetLdapCnIdentifier($value)
    {
        $this->_config_data['ldap_cn_identifier'] = $value;
    }


    function GetLdapCnIdentifier()
    {
        return $this->_config_data['ldap_cn_identifier'];
    }


    function SetLdapBaseDn($value)
    {
        $this->_config_data['ldap_base_dn'] = $value;
    }


    function GetLdapBaseDn()
    {
        return $this->_config_data['ldap_base_dn'];
    }


    function SetLdapBindDn($value)
    {
        $this->_config_data['ldap_bind_dn'] = $value;
    }


    function GetLdapBindDn()
    {
        return $this->_config_data['ldap_bind_dn'];
    }


    function SetLdapDomainControllers($value)
    {
        $this->_config_data['ldap_domain_controllers'] = $value;
    }


    function GetLdapDomainControllers()
    {
        return $this->_config_data['ldap_domain_controllers'];
    }


    function SetLdapDomainName($value)
    {
        $this->_config_data['ldap_domain_name'] = $value;
    }


    function GetLdapDomainName()
    {
        return $this->_config_data['ldap_domain_name'];
    }


    function SetLdapPassword($value)
    {
        $this->_config_data['ldap_password'] = $value;
    }


    function GetLdapPassword()
    {
        return $this->_config_data['ldap_password'];
    }


    function SetLdapPort($value)
    {
        $this->_config_data['ldap_port'] = intval($value);
    }


    function GetLdapPort()
    {
        return $this->_config_data['ldap_port'];
    }


    function SetLdapUsername($value)
    {
        $this->_config_data['ldap_username'] = $value;
    }


    function GetLdapUsername()
    {
        return $this->_config_data['ldap_username'];
    }


    function SetSmsMessage($value)
    {
        $this->_config_data['sms_message_prefix'] = $value;
    }


    function GetSmsMessage()
    {
        return $this->_config_data['sms_message_prefix'];
    }


    function SetSmsDigits($value)
    {
        $this->_config_data['sms_digits'] = intval($value);
    }


    function GetSmsDigits()
    {
        return $this->_config_data['sms_digits'];
    }


    function SetSmsTimeout($value)
    {
        $this->_config_data['sms_timeout'] = intval($value);
    }


    function GetSmsTimeout()
    {
        return $this->_config_data['sms_timeout'];
    }


    function SetConfigAttribute($attribute, $value)
    {
        $result = FALSE;
        if ($this->IsConfigOptionInSchema('config',$attribute))
        {
            $this->_config_data[$attribute] = $value;
            $result = TRUE;
        }
        return $result;
    }
    

    function GetConfigAttribute($attribute)
    {
        return isset($this->_config_data[$attribute])?$this->_config_data[$attribute]:'';
    }
    

    function SetMaxTimeWindow($time_window)
    {
        $this->_config_data['max_time_window'] = intval($time_window);
    }


    function GetMaxTimeWindow()
    {
        return $this->_config_data['max_time_window'];
    }


    function SetMaxTimeResyncWindow($time_resync_window)
    {
        $this->_config_data['max_time_resync_window'] = intval($time_resync_window);
    }


    function GetMaxTimeResyncWindow()
    {
        return $this->_config_data['max_time_resync_window'];
    }


    function SetMaxEventWindow($event_window)
    {
        $this->_config_data['max_event_window'] = intval($event_window);
    }


    function GetMaxEventWindow()
    {
        return $this->_config_data['max_event_window'];
    }
    
    
    function SetMaxEventResyncWindow($event_resync_window)
    {
        $this->_config_data['max_event_resync_window'] = intval($event_resync_window);
    }


    function GetMaxEventResyncWindow()
    {
        return $this->_config_data['max_event_resync_window'];
    }
    
    
    function SetMaxBlockFailures($max_failures)
    {
        $this->_config_data['max_block_failures'] = $max_failures;
    }


    function GetMaxBlockFailures()
    {
        return $this->_config_data['max_block_failures'];
    }

    
    function SetServerCacheLevel($value)
    {
        $this->_config_data['server_cache_level'] = intval($value);
    }


    function GetServerCacheLevel()
    {
        return intval($this->_config_data['server_cache_level']);
    }


    function SetServerCacheLifetime($value)
    {
        $this->_config_data['server_cache_lifetime'] = intval($value);
    }


    function GetServerCacheLifetime()
    {
        return intval($this->_config_data['server_cache_lifetime']);
    }


    function SetServerChallenge($value)
    {
        $this->_server_challenge = $value;
    }


    function GetServerChallenge()
    {
        return $this->_server_challenge;
    }


    function SetServerSecret($value)
    {
        $this->_config_data['server_secret'] = $value;
    }


    function GetServerSecret()
    {
        return $this->_config_data['server_secret'];
    }


    function SetServerType($value)
    {
        $this->_config_data['server_type'] = $value;
    }


    function GetServerType()
    {
        return $this->_config_data['server_type'];
    }


    function SetServerTimeout($value)
    {
        $this->_config_data['server_timeout'] = intval($value);
    }


    function GetServerTimeout()
    {
        return intval($this->_config_data['server_timeout']);
    }


    function SetServerUrl($value)
    {
        $this->_config_data['server_url'] = trim($value);
    }


    function GetServerUrl()
    {
        return trim($this->_config_data['server_url']);
    }


    /*********************************************************************
     *
     * Name: DefineMySqlConnection
     * Short description: Define the SQL parameters for the MySQL backend
     *                    (deprecated)
     *
     * Creation 2010-12-18
     * Update 2013-06-09
     * @package multiotp
     * @version 4.0.1
     * @author SysCo/al
     *
     * @param   string  $sql_server        MySQL server
     * @param   string  $sql_user          MySQL user
     * @param   string  $sql_passwd        MySQL password
     * @param   string  $sql_db            MySQL database
     * @param   string  $sql_log_table     MySQL log table
     * @param   string  $sql_users_table   MySQL users table
     * @param   string  $sql_tokens_table  MySQL tokens table
     *
     *********************************************************************/
    function DefineMySqlConnection($sql_server, $sql_user, $sql_passwd, $sql_db, $sql_log_table = NULL, $sql_users_table = NULL, $sql_tokens_table = NULL)
    {
        // Backend storage type
        $this->SetBackendType('mysql');
        $this->SetSqlServer($sql_server);
        $this->SetSqlUsername($sql_user);
        $this->SetSqlPassword($sql_passwd);
        $this->SetSqlDatabase($sql_db);
        
        // If table names are not defined, we keep the default value defined in the class constructor.
        if (NULL !== $sql_log_table)
        {
            $this->SetSqlTableName('log', $sql_log_table);
        }
        if (NULL !== $sql_users_table)
        {
            $this->SetSqlTableName('users', $sql_users_table);
        }
        if (NULL !== $sql_tokens_table)
        {
            $this->SetSqlTableName('tokens', $sql_tokens_table);
        }
    }
    
    /*********************************************************************
     *
     * Name: ComputeMotp
     * Short description: Compute the mOTP result
     *
     * Creation 2010-06-07
     * Update 2010-07-19
     * @package multiotp
     * @version 2.0.0
     * @author SysCo/al
     *
     * @param   string  $seed_and_pin  Key used to compute the mOTP result
     * @param   int     $timestep      Timestep used to calculate the token
     * @param   int     $token_size    Token size
     * @return  string                 mOTP result
     *
     *********************************************************************/
    function ComputeMotp($seed_and_pin, $timestep, $token_size)
    {
        return substr(md5($timestep.$seed_and_pin),0,$token_size);
    }


    /*********************************************************************
     *
     * Name: ComputeOathHotp
     * Short description: Compute the OATH defined hash
     *
     * Creation 2010-06-07
     * Update 2010-07-19
     * @package multiotp
     * @version 2.0.0
     * @author SysCo/al
     *
     * @param   string  $key      Key used to compute the OATH hash
     * @param   int     $counter  Counter position
     * @return  string            Full OATH hash
     *
     *********************************************************************/
    function ComputeOathHotp($key, $counter)
    {
        // Counter
        //the counter value can be more than one byte long, so we need to go multiple times
        $cur_counter = array(0,0,0,0,0,0,0,0);
        for($i=7;$i>=0;$i--)
        {
            $cur_counter[$i] = pack ('C*', $counter);
            $counter = $counter >> 8;
        }
        $bin_counter = implode($cur_counter);
        // Pad to 8 chars
        if (strlen ($bin_counter) < 8)
        {
            $bin_counter = str_repeat(chr(0), 8 - strlen($bin_counter)) . $bin_counter;
        }

        // HMAC hash
        $hash = hash_hmac('sha1', $bin_counter, $key);
        return $hash;
    }

    
    /*********************************************************************
     *
     * Name: ComputeOathTruncate
     * Short description: Truncate the result as defined by the OATH
     *
     * Creation 2010-06-07
     * Update 2010-07-19
     * @package multiotp
     * @version 2.0.0
     * @author SysCo/al
     *
     * @param   string  $hash     Full OATH hash to be truncated
     * @param   int     $length   Length of the result token
     * @return  string            Truncated OATH hash
     *
     *********************************************************************/
    function ComputeOathTruncate($hash, $length = 6)
    {
        // Convert to decimal
        foreach(str_split($hash,2) as $hex)
        {
            $hmac_result[]=hexdec($hex);
        }

        // Find offset
        $offset = $hmac_result[19] & 0xf;

        // Algorithm from RFC
        return
        substr(str_repeat('0',$length).((
            (($hmac_result[$offset+0] & 0x7f) << 24 ) |
            (($hmac_result[$offset+1] & 0xff) << 16 ) |
            (($hmac_result[$offset+2] & 0xff) << 8 ) |
            ($hmac_result[$offset+3] & 0xff)
        ) % pow(10,$length)),-$length); // & 0x7FFFFFFF before the pow()
    }

    
    function CalculateChapPassword($secret, $hex_chap_id = '', $hex_chap_challenge = '')
    {
        if ($hex_chap_id != '')
        {
            $id = hex2bin($hex_chap_id);
        }
        elseif (32 < strlen($this->GetChapPassword()))
        {
            $id = hex2bin(substr($this->GetChapPassword(),0,2));
        }
        else
        {
            $id = hex2bin($this->GetChapId());
        }
        
        if ($hex_chap_challenge != '')
        {
            $challenge = hex2bin($hex_chap_challenge);
        }
        else
        {
            $challenge = hex2bin($this->GetChapChallenge());
        }
        
        return md5($id.$secret.$challenge);
    }


    function SetEncryptionKey($key, $read_config = TRUE)
    {
        $this->_encryption_key = $key;
        if ($read_config)
        {
            $this->ReadConfigData();
        }
    }
    
    
    function GetEncryptionKey()
    {
        return $this->_encryption_key;
    }


    function CalculateControlHash($value_to_hash)
    {
        return strtoupper(md5("CaLcUlAtE".$value_to_hash."cOnTrOlHaSh"));
    }


    function Encrypt($key, $value, $encryption_key = '')
    {
        $result = '';
        if ('' != $encryption_key)
        {
            $encrypt = $encryption_key;
        }
        else
        {
            $encrypt = $this->_encryption_key;
        }
        if (strlen($encrypt) > 0)
        {
            if (0 < strlen($value))
            {
                for ($i=0;  $i < strlen($value); $i++)
                {
                    $encrypt_char = ord(substr($encrypt,$i % strlen($encrypt),1));
                    $key_char = ord(substr($key,$i % strlen($key),1));
                    $result .= chr($encrypt_char^$key_char^ord(substr($value,$i,1)));
                }
                $result = base64_encode($result);
            }
        }
        else
        {
            $result = $value;
        }
        return $result;
    }
    
    
    function Decrypt($key, $value, $encryption_key = '')
    {
        $result = '';
        if ('' != $encryption_key)
        {
            $encrypt = $encryption_key;
        }
        else
        {
            $encrypt = $this->_encryption_key;
        }
        if (strlen($encrypt) > 0)
        {
            if (0 < strlen($value))
            {
                $value_to_decrypt = base64_decode($value);
                for ($i=0;  $i < strlen($value_to_decrypt); $i++)
                {
                    $encrypt_char = ord(substr($encrypt,$i % strlen($encrypt),1));
                    $key_char = ord(substr($key,$i % strlen($key),1));
                    $result .= chr($encrypt_char^$key_char^ord(substr($value_to_decrypt,$i,1)));
                }
            }
        }
        else
        {
            $result = $value;
        }
        return $result;
    }

    
    function SetMaxDelayedFailures($failures)
    {
        $this->_config_data['max_delayed_failures'] = $failures;
    }

    
    function GetMaxDelayedFailures()
    {
        return $this->_config_data['max_delayed_failures'];
    }


    function SetMaxDelayedTime($seconds)
    {
        $this->_config_data['failure_delayed_time'] = $seconds;
    }

    
    function GetMaxDelayedTime()
    {
        return $this->_config_data['failure_delayed_time'];
    }

    
    function SetActualVersion($value)
    {
        $this->_config_data['actual_version'] = $value;
    }

    
    function GetActualVersion()
    {
        return $this->_config_data['actual_version'];
    }


    /*********************************************************************
     *
     * Name: CreateUser
     * Short description: Create a new user
     *
     * Creation 2013-02-08
     * Update 2013-02-08
     * @package multiotp
     * @version 1.0.0
     * @author SysCo/al
     *
     * @param   string  $user      
     * @param   int     $request_prefix_pin
     * @return  boolean
     *
     *********************************************************************/
    function CreateUser($user, $request_prefix_pin, $algorithm, $seed = '', $pin = '', $number_of_digits = 6, $time_interval_or_next_event = 30, $email = '', $sms = '', $description = '', $group = '')
    {
        if ($this->ReadUserData($user, TRUE, TRUE) || ('' == $user))
        {
            $result = FALSE; // ERROR: user already exists, or user is not set
            if ('' == $user)
            {
                $this->WriteLog("Error: user is not set");
            }
            else
            {
                $this->WriteLog("Error: user ".$user." already exists");
            }
        }
        else
        {
            $this->SetUser($user);
            $this->SetUserPrefixPin($request_prefix_pin);
            $this->SetUserAlgorithm($algorithm);

            $the_seed = $seed;
            if ('' == $the_seed)
            {
                $the_seed = substr(md5(date("YmdHis").rand(100000,999999)),0,20).substr(md5(rand(100000,999999).date("YmdHis")),0,20);
            }
            $this->SetUserTokenSeed($the_seed);
            $the_pin = $pin;
            if ('' == $the_pin)
            {
                $the_pin = rand(1000,9999);
            }
            $this->SetUserPin($the_pin);
            $this->SetUserTokenNumberOfDigits($number_of_digits);
            if ("hotp" == strtolower($algorithm))
            {
                $this->SetUserTokenLastEvent($time_interval_or_next_event - 1);
                $this->SetUserTokenTimeInterval(0);
            }
            else
            {
                $this->SetUserTokenLastEvent(-1);
                $this->SetUserTokenTimeInterval($time_interval_or_next_event);
            }
            
            $this_email = trim($email);
            if (('' == $this_email) && (FALSE !== strpos($user, '@')))
            {
                $this_email = $user;
            }
            
            $this->SetUserEmail($this_email);
            $this->SetUserGroup(trim($group));
            $this->SetUserSms($sms);
            $this->SetUserDescription($description);
            $result = $this->WriteUserData();
            if ($result)
            {
                $this->WriteLog("Info: user ".$user." successfully created");
            }
            else
            {
                $this->WriteLog("Error: user ".$user." not created");
            }
        }
        return $result;
    }


    /*********************************************************************
     * Name: CreateUserFromToken
     * Short description: Create a new user based on a token
     *
     * Creation 2013-02-17
     * Update 2013-02-17
     * @package multiotp
     * @version 1.0.0
     * @author SysCo/al
     *
     * @param   string  $user
     * @param   string  $token
     * @param   int     $request_prefix_pin
     * @param   int     $pin
     * @param   int     $email
     * @param   int     $sms
     * @return  int
     *********************************************************************/
    function CreateUserFromToken($user, $token, $email = '', $sms = '', $pin = '', $request_prefix_pin = 0, $description = '', $group = '')
    {
        if ($this->ReadUserData($user, TRUE, TRUE) || ('' == $user))
        {
            $result = FALSE;
            if ('' == $user)
            {
                $this->WriteLog("Error: user is not set");
            }
            else
            {
                $this->WriteLog("Error: user ".$user." already exists");
            }
        }
        elseif (!$this->ReadTokenData($token))
        {
            $result = FALSE;
            $this->WriteLog("Error: token ".$token." cannot be read");
        }
        else
        {
            $this->SetUser($user);
            $this->SetUserPrefixPin($request_prefix_pin);
            $this->SetUserKeyId($token);
            $this->SetUserAlgorithm($this->GetTokenAlgorithm());
            $this->SetUserTokenSeed($this->GetTokenSeed());
            $this->SetUserTokenNumberOfDigits($this->GetTokenNumberOfDigits());
            $this->SetUserTokenTimeInterval($this->GetTokenTimeInterval());
            $this->SetUserTokenLastEvent($this->GetTokenLastEvent());

            $the_pin = $pin;
            if ('' == $the_pin)
            {
                $the_pin = rand(1000,9999);
            }
            $this->SetUserPin($the_pin);
            
            $this_email = trim($email);
            if (('' == $this_email) && (FALSE !== strpos($user, '@')))
            {
                $this_email = $user;
            }
            
            $this->SetUserEmail($this_email);
            $this->SetUserGroup(trim($group));
            $this->SetUserSms($sms);
            $this->SetUserDescription($description);
            
            $result = $this->WriteUserData();
            if ($result)
            {
                $this->WriteLog("Info: user ".$user." successfully created");
            }
            else
            {
                $this->WriteLog("Error: user ".$user." not created");
            }
        }
        return $result;
    }


    /*********************************************************************
     *
     * Name: GetUserTokenQrCode
     * Short description: Create the QRcode for the current user
     *
     * Creation 2013-02-17
     * Update 2013-02-17
     * @package multiotp
     * @version 1.0.0
     * @author SysCo/al
     *
     * @param   string  $user
     * @param   string  $display_name
     * @param   string  $file_name
     * @return  boolean or binary
     *
     *********************************************************************/
    function GetUserTokenQrCode($user = '', $display_name = '', $file_name = 'binary')
    {
        $result = FALSE;
        if ('' != $user)
        {
            $this->SetUser($user);
        }
        if (!function_exists('ImageCreate'))
        {
            $result = FALSE;
            $this->WriteLog("Error: PHP GD library is not installed");
        }
        elseif ($this->ReadUserData())
        {
            $the_user       = $this->GetUser();
            $description    = $this->GetUserDescription();
            $q_algorithm    = $this->GetUserAlgorithm();
            $q_period       = $this->GetUserTokenTimeInterval();
            $q_digits       = $this->GetUserTokenNumberOfDigits();
            $q_seed         = $this->GetUserTokenSeed();
            $q_counter      = $this->GetUserTokenLastEvent() + 1;
            $q_display_name = (('' != $display_name)?$display_name:(('' != $description)?$description:$the_user));

            $path = $this->GetScriptFolder()."qrcode/data";
            $image_path = $this->GetScriptFolder()."qrcode/image";

            switch (strtolower($q_algorithm))
            {
                case 'totp':
                    $result = MultiotpQrcode('otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?period='.$q_period.'&digits='.$q_digits.'&secret='.base32_encode(hex2bin($q_seed)), $file_name, $path, $image_path);
                    break;
                case 'hotp':
                    $result = MultiotpQrcode('otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?counter='.$q_counter.'&digits='.$q_digits.'&secret='.base32_encode(hex2bin($q_seed)), $file_name, $path, $image_path);
                    break;
                /*
                case 'motp':
                    $result = MultiotpQrcode('otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?counter='.$q_counter.'&digits='.$q_digits.'&secret='.base32_encode(hex2bin($q_seed)), $file_name, $path, $image_path);
                    break;
                */
                default:
                    // $result = MultiotpQrcode('http://www.multiotp.net/no_qrcode_compatible_client_for_this_algorithm', $file_name, $path, $image_path);
                    $result = FALSE;
                    $this->WriteLog("Error: No known QRcode compatible client for this algorithm");
            }
        }
        else
        {
            $result = FALSE;
        }
        return $result;
    }


    /*********************************************************************
     *
     * Name: GetTokenQrCode
     * Short description: Create the QRcode for the current token
     *
     * Creation 2013-02-18
     * Update 2013-02-18
     * @package multiotp
     * @version 1.0.0
     * @author SysCo/al
     *
     * @param   string  $token
     * @param   string  $display_name
     * @param   string  $file_name
     * @return  boolean or binary
     *
     *********************************************************************/
    function GetTokenQrCode($token = '', $display_name = '', $file_name = 'binary')
    {
        $result = FALSE;
        if ('' != $token)
        {
            $this->SetToken($token);
        }
        if (!function_exists('ImageCreate'))
        {
            $result = FALSE;
            $this->WriteLog("Error: PHP GD library is not installed");
        }
        elseif ($this->ReadTokenData())
        {
            $the_token      = $this->GetToken();
            $q_algorithm    = $this->GetTokenAlgorithm();
            $q_period       = $this->GetTokenTimeInterval();
            $q_digits       = $this->GetTokenNumberOfDigits();
            $q_seed         = $this->GetTokenSeed();
            $q_counter      = $this->GetTokenLastEvent() + 1;
            $q_display_name = (('' != $display_name)?$display_name:$the_token);

            $path = $this->GetScriptFolder()."qrcode/data";
            $image_path = $this->GetScriptFolder()."qrcode/image";

            switch (strtolower($q_algorithm))
            {
                case 'totp':
                    $result = MultiotpQrcode('otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?period='.$q_period.'&digits='.$q_digits.'&secret='.base32_encode(hex2bin($q_seed)), $file_name, $path, $image_path);
                    break;
                case 'hotp':
                    $result = MultiotpQrcode('otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?counter='.$q_counter.'&digits='.$q_digits.'&secret='.base32_encode(hex2bin($q_seed)), $file_name, $path, $image_path);
                    break;
                /*
                case 'motp':
                    $result = MultiotpQrcode('otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?period='.$q_period.'&digits='.$q_digits.'&secret='.base32_encode(hex2bin($q_seed)), $file_name, $path, $image_path);
                    break;
                */
                default:
                    // $result = MultiotpQrcode('http://www.multiotp.net/no_qrcode_compatible_client_for_this_algorithm', $file_name, $path, $image_path);
                    $result = FALSE;
                    $this->WriteLog("Error: No known QRcode compatible client for this algorithm");
            }
        }
        else
        {
            $result = FALSE;
        }
        return $result;
    }


    /*********************************************************************
     *
     * Name: GetUserTokenUrlLink
     * Short description: Create the Urllink for the current user
     *
     * Creation 2013-04-29
     * Update 2013-04-29
     * @package multiotp
     * @version 1.0.0
     * @author SysCo/al
     *
     * @param   string  $user
     * @param   string  $display_name
     * @return  boolean (FALSE) or string
     *
     *********************************************************************/
    function GetUserTokenUrlLink($user = '', $display_name = '')
    {
        $result = FALSE;
        if ('' != $user)
        {
            $this->SetUser($user);
        }

        if ($this->ReadUserData())
        {
            $the_user       = $this->GetUser();
            $description    = $this->GetUserDescription();
            $q_algorithm    = $this->GetUserAlgorithm();
            $q_period       = $this->GetUserTokenTimeInterval();
            $q_digits       = $this->GetUserTokenNumberOfDigits();
            $q_seed         = $this->GetUserTokenSeed();
            $q_counter      = $this->GetUserTokenLastEvent() + 1;
            $q_display_name = (('' != $display_name)?$display_name:(('' != $description)?$description:$the_user));

            switch (strtolower($q_algorithm))
            {
                case 'totp':
                    $result = 'otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?period='.$q_period.'&digits='.$q_digits.'&secret='.base32_encode(hex2bin($q_seed));
                    break;
                case 'hotp':
                    $result = 'otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?counter='.$q_counter.'&digits='.$q_digits.'&secret='.base32_encode(hex2bin($q_seed));
                    break;
                /*
                case 'motp':
                    $result = 'otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?counter='.$q_counter.'&digits='.$q_digits.'&secret=' . base32_encode(hex2bin($q_seed));
                    break;
                */
                default:
                    // $result = 'http://www.multiotp.net/no_qrcode_compatible_client_for_this_algorithm';
                    $result = FALSE;
                    $this->WriteLog("Error: No known URL compatible client for this algorithm");
            }
        }
        else
        {
            // $result = '';
        }
        return $result;
    }


    /*********************************************************************
     *
     * Name: FastCreateUser
     * Short description: Quickly create a new user with a new token
     *
     * Creation 2013-02-16
     * Update 2013-02-16
     * @package multiotp
     * @version 1.0.0
     * @author SysCo/al
     *
     * @param   string  $user      
     * @param   int     $request_prefix_pin
     * @return  boolean
     *
     *********************************************************************/
    function FastCreateUser($user, $email = '', $sms = '')
    {
        if ($this->ReadUserData($user, TRUE, TRUE) || ('' == $user))
        {
            $result = FALSE;
        }
        else
        {
            if ($this->GetVerboseFlag())
            {
                $this->WriteLog("DEBUG: Create a token for user ".$user);
            }
            if (!$this->CreateToken())
            {
                if ($this->GetVerboseFlag())
                {
                    $this->WriteLog("DEBUG: Token creation failed for user ".$user);
                }
                $result = FALSE;
            }
            else
            {
                $token = $this->GetToken();
                $result = $this->CreateUserFromToken($user, $token, $email, $sms);
                if (!$result)
                {
                    $this->WriteLog("DEBUG: CreateUserFromToken failed for ".$user);
                }
            }
        }
        return $result;
    }


    function SetUser($user)
    {
        if ('' != $user)
        {
            $this->ResetUserArray();
            $this->_user = $user;
            $this->ReadUserData('', TRUE); // First parameter empty, otherwise it will loop with SetUser !
        }
   }

    
    function RenameCurrentUser($new_user)
    {
        $result = FALSE;
        if ($this->CheckUserExists($new_user)) // Check if the new user already exists
        {
            $this->WriteLog("Error: unable to rename the current user ".$this->GetUser()." to ".$new_user." because it already exists");
        }
        else
        {
            if ($this->CheckUserExists()) // Check if the current user already exists
            {
                if ((($this->GetBackendTypeValidated()) && ('' != $this->_config_data['sql_users_table'])) || ('files' == $this->GetBackendType()))
                {
                    switch ($this->GetBackendType())
                    {
                        case 'mysql':
                            if ($this->OpenMysqlDatabase())
                            {
                                if ('' != $this->_config_data['sql_users_table'])
                                {
                                    $sQuery = "UPDATE `".$this->_config_data['sql_users_table']."` SET user='".$new_user."' WHERE `user`='".$this->GetUser()."'";
                                    if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                                    {
                                        $this->WriteLog("Error: Could not rename the user ".$this->_user.": ".mysql_error());
                                    }
                                    elseif (0 == mysql_affected_rows($this->_mysql_database_link))
                                    {
                                        $this->WriteLog("Error: Could not rename the user ".$this->_user.". User does not exist");
                                    }
                                    else
                                    {
                                        $this->WriteLog("Info: user ".$this->_user." successfully renamed");
                                        $result = TRUE;
                                    }
                                }
                            }
                            break;
                        case 'files':
                        default:
                            $old_user_filename = strtolower($this->GetUser()).'.db';
                            $new_user_filename = strtolower($new_user).'.db';
                            rename($this->GetUsersFolder().$old_user_filename, $this->GetUsersFolder().$new_user_filename);
                            $result = TRUE;
                            break;
                    }
                }
            }
            if ($result)
            {
                $this->_user = $new_user;
            }
        }
        return $result;
    }


    function GetUser()
    {
        return $this->_user;
    }

    
    // Check if user exists (locally only)
    function CheckUserExists($user = '', $do_not_check_on_server = FALSE)
    {
        $check_user = ('' != $user)?$user:$this->GetUser();
        $result = FALSE;

        $server_result = -1;
        if ((!$do_not_check_on_server) && ('' != $this->GetServerUrl()))
        {
            $server_result = $this->CheckUserExistsOnServer($check_user);
            if (22 == $server_result)
            {
                // We return only if the user exists, so we check also the local one
                $result = TRUE;
                return $result;
            }
        }

        if ((($this->GetBackendTypeValidated()) && ('' != $this->_config_data['sql_users_table'])) || ('files' == $this->GetBackendType()))
        {
            switch ($this->GetBackendType())
            {
                case 'mysql':
                    if ($this->OpenMysqlDatabase())
                    {
                        $sQuery  = "SELECT * FROM `".$this->_config_data['sql_users_table']."` WHERE `user` = '{$check_user}'";
                        if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                        {
                            $this->WriteLog("Error: Unable to access the database: ".mysql_error());
                        }
                        elseif (0 == mysql_affected_rows($this->_mysql_database_link))
                        {
                            $this->WriteLog("Error: User ".$user.". does not exist");
                        }
                        else
                        {
                            $result = TRUE;
                            $this->WriteLog("Info: user ".$user." exists");
                        }
                    }
                    break;
                case 'files':
                default:
                    $user_filename = strtolower($check_user).'.db';
                    $result = file_exists($this->GetUsersFolder().$user_filename);
                    break;
            }
        }
        return $result;
    }


    function GetUsersList()
    {
        $users_list = '';
        if ((($this->GetBackendTypeValidated()) && ('' != $this->_config_data['sql_users_table'])) || ('files' == $this->GetBackendType()))
        {
            switch ($this->GetBackendType())
            {
                case 'mysql':
                    if ($this->OpenMysqlDatabase())
                    {
                        $sQuery  = "SELECT user FROM `".$this->_config_data['sql_users_table'];
                        if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                        {
                            $this->WriteLog("Error: Unable to access the database: ".mysql_error());
                        }
                        else
                        {
                            while ($aRow = mysql_fetch_assoc($rResult))
                            {
                                $users_list.= (('' != $users_list)?"\t":'').$aRow['user'];
                            }                         
                        }
                    }
                    break;
                case 'files':
                default:
                    if ($users_handle = @opendir($this->GetUsersFolder()))
                    {
                        while ($file = readdir($users_handle))
                        {
                            if (substr($file, -3) == ".db")
                            {
                                $users_list.= (('' != $users_list)?"\t":'').substr($file,0,-3);
                            }
                        }
                        closedir($users_handle);
                    }
            }
        }
    return $users_list;
    }

    
    function GetAlgorithmsList()
    {
        $algorithms_list = '';
        $algorithms_array = explode("*",$this->_valid_algorithms);
        foreach ($algorithms_array as $algorithm_one)
        {
            if ('' != trim($algorithm_one))
            {
                $algorithms_list.= (('' != $algorithms_list)?"\t":'').trim($algorithm_one);
            }
        }
        return $algorithms_list;
    }


    function GetUserScratchPasswordsArray($user = '')
    {
        if ('' != $user)
        {
            $this->SetUser($user);
        }
        return (explode(",",$this->_user_data['scratch_passwords']));
    }

    
    function RemoveUserUsedScratchPassword($to_remove)
    {
        $scratch_passwords = trim($this->_user_data['scratch_passwords']);
        if (FALSE !== ($pos = strpos($scratch_passwords, $to_remove)))
        {
            $scratch_passwords = trim(substr($scratch_passwords.' ', $pos+strlen($to_remove)+1));
            $this->_user_data['scratch_passwords'] = $scratch_passwords;
            $result = $this->WriteUserData();
        }
    }
    
    function GetUserScratchPasswordsList($user = '')
    {
        if ('' != $user)
        {
            $this->SetUser($user);
        }
        $digits = $this->GetScratchPasswordsDigits();
        $seed = hex2bin(md5('sCratchP@sswordS'.$this->GetUser()).rand(10000,99999));
        $scratch_loop = $this->_scratch_passwords_amount;
        $scratch_passwords = trim($this->_user_data['scratch_passwords']);
        if (strlen($scratch_passwords) > ((1.5 * $scratch_loop) * (1 + $digits)))
        {
            $scratch_passwords = '';
        }
        $passwords_list = '';

        for ($i=0; $i<$scratch_loop; $i++)
        {
            $one_password = $this->ComputeOathTruncate($this->ComputeOathHotp($seed,$i),$digits);
            $scratch_passwords.= (('' != $scratch_passwords)?",":'').$one_password;
            $passwords_list.= (('' != $passwords_list)?"\t":'').$one_password;
        }
        $this->_user_data['scratch_passwords'] = $scratch_passwords;
        $result = $this->WriteUserData();
        if (!$result)
        {
            $passwords_list = '';
        }
        return ($passwords_list);
    }

    
    function SetUserDataReadFlag($flag)
    {
        $this->_user_data_read_flag = $flag;
    }
    
    
    function GetUserDataReadFlag()
    {
        return $this->_user_data_read_flag;
    }
    

    function SetUserMultiAccount($value)
    {
        $this->_user_data['multi_account'] = $value;
    }

    
    function GetUserMultiAccount()
    {
        return $this->_user_data['multi_account'];
    }

    
    function SetUserEmail($first_param, $second_param = "*-*")
    {
        $result = "";
        if ($second_param == "*-*")
        {
            if (('' == $first_param) || (FALSE !== strpos($first_param, '@')))
            {
                $result = $first_param;
            }
        }
        else
        {
            $this->SetUser($first_param);
            if (('' == $second_param) || (FALSE !== strpos($second_param, '@')))
            {
                $result = $second_param;
            }
        }
        $this->_user_data['email'] = $result;

        return $result;
    }

    
    function SetUserGroup($first_param, $second_param = "*-*")
    {
        $result = "";
        if ($second_param == "*-*")
        {
            $result = $first_param;
        }
        else
        {
            $this->SetUser($first_param);
            $result = $second_param;
        }
        $this->_user_data['group'] = $result;

        return $result;
    }


    function SetUserAttribute($first_param, $second_param, $third_param = "*-*")
    {
        $result = FALSE;
        if ($third_param == "*-*")
        {
            if ($this->IsConfigOptionInSchema('users', $first_param))
            {
                $this->_user_data[$first_param] = $second_param;
                $result = TRUE;
            }
        }
        else
        {
            if ($this->IsConfigOptionInSchema('users', $second_param))
            {
                $this->SetUser($first_param);
                $this->_user_data[$second_param] = $third_param;
                $result = TRUE;
            }
        }
        return $result;
    }

    
    function GetUserEmail($user = '')
    {
        if($user != '')
        {
            $this->SetUser($user);
        }
        return $this->_user_data['email'];
    }

    
    function GetUserGroup($user = '')
    {
        if($user != '')
        {
            $this->SetUser($user);
        }
        return $this->_user_data['group'];
    }

    
    function SetUserDescription($first_param, $second_param = "*-*")
    {
        $result = "";
        if ($second_param == "*-*")
        {
            $result = $first_param;
        }
        else
        {
            $this->SetUser($first_param);
            $result = $second_param;
        }
        $this->_user_data['description'] = $result;

        return $result;
    }

    
    function GetUserDescription($user = '')
    {
        if($user != '')
        {
            $this->SetUser($user);
        }
        return $this->_user_data['description'];
    }

    
    function SetUserSeedPassword($value)
    {
        $this->_user_data['seed_password'] = $value;
    }

    
    function GetUserSeedPassword()
    {
        return $this->_user_data['seed_password'];
    }

    
    function SetUserSms($first_param, $second_param = "*-*")
    {
        $result = "";
        if ($second_param == "*-*")
        {
            $result = $first_param;
        }
        else
        {
            $this->SetUser($first_param);
            $result = $second_param;
        }
        if ("" != $result)
        {
            $this->_user_data['sms'] = $result;
        }
        return $result;
    }

    
    function GetUserSms($user = '')
    {
        if($user != '')
        {
            $this->SetUser($user);
        }
        return $this->_user_data['sms'];
    }

    
    function SetUserPrefixPin($value)
    {
        $this->_user_data['request_prefix_pin'] = $value;
    }

    
    function GetUserPrefixPin()
    {
        return $this->_user_data['request_prefix_pin'];
    }

    
    function SetUserAlgorithm($algorithm)
    {
        $result = FALSE;
        if (FALSE === strpos(strtolower($this->_valid_algorithms), strtolower('*'.$algorithm.'*')))
        {
            $this->WriteLog("Error: ".$algorithm." algorithm is unknown");
        }
        else
        {
            $this->_user_data['algorithm'] = strtolower($algorithm);
            $result = TRUE;
        }
        return $result;
    }


    function GetUserAlgorithm()
    {
        return strtolower($this->_user_data['algorithm']);
    }


    function SetUserTokenSeed($seed)
    {
        $this->_user_data['token_seed'] = $seed;
    }

    
    function GetUserTokenSeed()
    {
        return $this->_user_data['token_seed'];
    }

    
    function SetUserSmsOtp($value)
    {
        $this->_user_data['sms_otp'] = $value;
    }
    
    
    function GetUserSmsOtp()
    {
        return $this->_user_data['sms_otp'];
    }

    
    function SetUserSmsValidity($value)
    {
        $this->_user_data['sms_validity'] = $value;
    }
    
    
    function GetUserSmsValidity()
    {
        return $this->_user_data['sms_validity'];
    }

    
    function SetUserPin($pin)
    {
        $this->_user_data['user_pin'] = $pin;
    }
    
    
    function GetUserPin()
    {
        return $this->_user_data['user_pin'];
    }


    function SetUserAutolockTime($value)
    {
        $this->_user_data['autolock_time'] = intval($value);
    }
    
    
    function GetUserAutolockTime()
    {
        return intval($this->_user_data['autolock_time']);
    }

    
    function SetUserTokenDeltaTime($delta_time)
    {
        $this->_user_data['delta_time'] = $delta_time;
    }
    
    
    function GetUserTokenDeltaTime()
    {
        return $this->_user_data['delta_time'];
    }

    
    function SetUserKeyId($key_id)
    {
        $this->_user_data['key_id'] = $key_id;
    }
    
    
    function GetUserKeyId()
    {
        return $this->_user_data['key_id'];
    }

    
    function SetUserTokenNumberOfDigits($number_of_digits)
    {
        $this->_user_data['number_of_digits'] = $number_of_digits;
    }
    
    
    function GetUserTokenNumberOfDigits()
    {
        return $this->_user_data['number_of_digits'];
    }


    function SetUserTokenTimeInterval($interval)
    {
        if (intval($interval) > 0)
        {
            $this->_user_data['time_interval'] = intval($interval);
        }
    }
    
    
    function GetUserTokenTimeInterval()
    {
        return $this->_user_data['time_interval'];
    }


    function GetUserEncryptionHash()
    {
        return $this->_user_data['encryption_hash'];
    }
    
    
    function SetUserTokenSerialNumber($token_serial)
    {
        $this->_user_data['token_serial'] = $token_serial;
    }


    function GetUserTokenSerialNumber()
    {
        return $this->_user_data['token_serial'];
    }
    
    
    function SetUserTokenLastEvent($last_event)
    {
        $this->_user_data['last_event'] = $last_event;
    }
    
    
    function GetUserTokenLastEvent()
    {
        return $this->_user_data['last_event'];
    }

    
    function SetUserTokenLastLogin($time)
    {
        $this->_user_data['last_login'] = $time;
    }
    
    
    function GetUserTokenLastLogin()
    {
        return $this->_user_data['last_login'];
    }


    function SetUserTokenLastError($time)
    {
        $this->_user_data['last_error'] = $time;
    }
    
    
    function GetUserTokenLastError()
    {
        return $this->_user_data['last_error'];
    }


    function SetUserLocked($locked)
    {
        $this->_user_data['locked'] = $locked;
    }
    
    
    function GetUserLocked()
    {
        return $this->_user_data['locked'];
    }


    function SetUserErrorCounter($counter)
    {
        $this->_user_data['error_counter'] = $counter;
    }
    
    
    function GetUserErrorCounter()
    {
        return $this->_user_data['error_counter'];
    }

    
    /*********************************************************************
     *
     * Name: CreateToken
     * Short description: Create a new token, without parameter, create
     *                      a Google Authenticator compatible token
     *
     * Creation 2013-02-08
     * Update 2013-02-16
     * @package multiotp
     * @version 1.0.0
     * @author SysCo/al
     *
     * @param   string  $token      
     * @param   string  $algorithm
     * @param   string  $seed
     * @param   int     $number_of_digits
     * @param   int     $time_interval_or_next_event
     * @param   string  $manufacturer
     * @param   string  $issuer
     * @param   string  $serial
     * @return  boolean
     *
     *********************************************************************/
    function CreateToken($token = '', $algorithm = 'totp', $seed = '', $number_of_digits = 6, $time_interval_or_next_event = 30, $manufacturer = 'multiOTP', $issuer = 'multiOTP', $serial = '')
    {
        $the_serial = $serial;
        if ('' == $the_serial)
        {
            $the_serial = 'mu'.bigdec2hex((time()-mktime(1,1,1,1,1,2000)).rand(10000,99999));
        }
        $the_token = $token;
        if ('' == $the_token)
        {
            $the_token = $the_serial;
        }
        if ($this->ReadTokenData($the_token, TRUE))
        {
            return FALSE; // ERROR: token already exists.
        }
        else
        {
            $this->SetToken($the_token);
            $this->SetTokenManufacturer(('' != $manufacturer)?$manufacturer:'multiOTP');
            $this->SetTokenIssuer(('' != $issuer)?$issuer:'multiOTP');
            $this->SetTokenSerialNumber($the_serial);
            $this->SetTokenAlgorithm(strtolower($algorithm));
            $this->SetTokenKeyAlgorithm(strtolower($algorithm));
            $this->SetTokenOtp('TRUE');
            $the_seed = $seed;
            if ('' == $the_seed)
            {
                $the_seed = substr(md5(date("YmdHis").rand(100000,999999)),0,20).substr(md5(rand(100000,999999).date("YmdHis")),0,20);
            }
            $this->SetTokenSeed($the_seed);
            $this->SetTokenFormat('DECIMAL');
            $this->SetTokenNumberOfDigits($number_of_digits);
            $this->SetTokenDeltaTime(0);
            if ("hotp" == strtolower($algorithm))
            {
                $this->SetTokenLastEvent($time_interval_or_next_event - 1);
                $this->SetTokenTimeInterval(0);
            }
            else
            {
                $this->SetTokenLastEvent(-1);
                $this->SetTokenTimeInterval($time_interval_or_next_event);
            }
            return $this->WriteTokenData();
        }
    }    
    

    function SetToken($token)
    {
        $this->ResetTokenArray();
        $this->_token = strtolower($token);
        $this->ReadTokenData('', TRUE); // First parameter empty, otherwise it will loop with SetToken !
    }


    function RenameCurrentToken($new_token)
    {
        $result = FALSE;
        if ($this->CheckTokenExists($new_token)) // Check if the new token already exists
        {
            $this->WriteLog("Error: unable to rename the current token ".$this->GetToken()." to ".$new_token." because it already exists");
        }
        else
        {
            if ($this->CheckTokenExists()) // Check if the current token already exists
            {
                if ((($this->GetBackendTypeValidated()) && ('' != $this->_config_data['sql_tokens_table'])) || ('files' == $this->GetBackendType()))
                {
                    switch ($this->GetBackendType())
                    {
                        case 'mysql':
                            if ($this->OpenMysqlDatabase())
                            {
                                if ('' != $this->_config_data['sql_tokens_table'])
                                {
                                    $sQuery = "UPDATE `".$this->_config_data['sql_tokens_table']."` SET token_id='".$new_token."' WHERE `token_id`='".$this->GetToken()."'";
                                    if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                                    {
                                        $this->WriteLog("Error: Could not rename the token ".$this->_token.": ".mysql_error());
                                    }
                                    elseif (0 == mysql_affected_rows($this->_mysql_database_link))
                                    {
                                        $this->WriteLog("Error: Could not rename the token ".$this->_token.". Token does not exist");
                                    }
                                    else
                                    {
                                        $this->WriteLog("Info: token ".$this->_token." successfully renamed");
                                        $result = TRUE;
                                    }
                                }
                            }
                            break;
                        case 'files':
                        default:
                            $old_token_filename = strtolower($this->GetToken()).'.db';
                            $new_token_filename = strtolower($new_token).'.db';
                            rename($this->GetTokensFolder().$old_token_filename, $this->GetTokensFolder().$new_token_filename);
                            $result = TRUE;
                            break;
                    }
                }
            }
            if ($result)
            {
                $this->_token = $new_token;
            }
        }
        return $result;
    }


    function GetToken()
    {
        return $this->_token;
    }


    function CheckTokenExists($token = '')
    {
        $check_token = ('' != $token)?$token:$this->GetToken();
        $result = FALSE;
        
        if ((($this->GetBackendTypeValidated()) && ('' != $this->_config_data['sql_tokens_table'])) || ('files' == $this->GetBackendType()))
        {
            switch ($this->GetBackendType())
            {
                case 'mysql':
                    if ($this->OpenMysqlDatabase())
                    {
                        $sQuery  = "SELECT * FROM `".$this->_config_data['sql_tokens_table']."` WHERE `token_id` = '{$check_token}'";
                        if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                        {
                            $this->WriteLog("Error: Unable to access the database: ".mysql_error());
                        }
                        elseif (0 == mysql_affected_rows($this->_mysql_database_link))
                        {
                            $this->WriteLog("Error: Token ".$this->_token.". does not exist");
                        }
                        else
                        {
                            $result = TRUE;
                            $this->WriteLog("Info: token ".$this->_token." exists");
                        }
                    }
                    break;
                case 'files':
                default:
                    $token_filename = strtolower($check_token).'.db';
                    $result = file_exists($this->GetTokensFolder().$token_filename);
                    break;
            }
        }
        return $result;
    }


    function ResetLastImportedTokensArray()
    {
        $this->_last_imported_tokens = array();
    }

    
    function AddLastImportedToken($token)
    {
        $this->_last_imported_tokens[] = $token;
    }


    function GetLastImportedTokens()
    {
        return $this->_last_imported_tokens;
    }


    function GetTokensList()
    {
        $tokens_list = '';
        if ((($this->GetBackendTypeValidated()) && ('' != $this->_config_data['sql_tokens_table'])) || ('files' == $this->GetBackendType()))
        {
            switch ($this->GetBackendType())
            {
                case 'mysql':
                    if ($this->OpenMysqlDatabase())
                    {
                        $sQuery  = "SELECT token_id FROM `".$this->_config_data['sql_tokens_table'];
                        if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                        {
                            $this->WriteLog("Error: Unable to access the database: ".mysql_error());
                        }
                        else
                        {
                            while ($aRow = mysql_fetch_assoc($rResult))
                            {
                                $tokens_list.= (('' != $tokens_list)?"\t":'').$aRow['token_id'];
                            }                         
                        }
                    }
                    break;
                case 'files':
                default:
                    if ($tokens_handle = @opendir($this->GetTokensFolder()))
                    {
                        while ($file = readdir($tokens_handle))
                        {
                            if (substr($file, -3) == ".db")
                            {
                                $tokens_list.= (('' != $tokens_list)?"\t":'').substr($file,0,-3);
                            }
                        }
                        closedir($tokens_handle);
                    }
            }
        }
    return $tokens_list;
    }

    
    function GetLdapList()
    {
        $users_list = '';
        if (!function_exists('ldap_connect'))
        {
            $result = FALSE;
            $this->WriteLog("Error: PHP LDAP library is not installed");
        }
        else
        {
            if (('' != $this->_config_data['ldap_domain_controllers']) && ('' != $this->_config_data['ldap_username']) && ('' != $this->_config_data['ldap_password']))
            {
                $domain_controllers = str_replace(","," ",str_replace(";"," ",$this->_config_data['ldap_domain_controllers']));
                $ldap_options = array('account_suffix'     => $this->_config_data['ldap_account_suffix'],
                                      'base_dn'            => $this->_config_data['ldap_base_dn'],
                                      'domain_controllers' => array($domain_controllers),
                                      'ad_username'        => $this->_config_data['ldap_username'],
                                      'ad_password'        => $this->_config_data['ldap_password']);

                $ldap_connection=new MultiotpAdLdap($ldap_options);
                $result_array=@$ldap_connection->all_users($include_desc = false, $search = "*", $sorted = true);
                foreach($result_array as $one)
                {
                    // $one_user = @$ldap_connection->user_info($one);
                    // $samaccountname = utf8_decode($one_user[0]['samaccountname'][0]);
                    $samaccountname = decode_utf8_if_needed($one);
                    $users_list.= (('' != $users_list)?"\t":'').$samaccountname;
                }
            }
            else
            {
                $this->WriteLog("Error: no ldap connection information");
            }
        }
        return $users_list;
    }

    
    function GetLdapUserInfo($user)
    {
        $result_array = array();
        
        if (!function_exists('ldap_connect'))
        {
            $result = FALSE;
            $this->WriteLog("Error: PHP LDAP library is not installed");
        }
        elseif (('' != $this->_config_data['ldap_domain_controllers']) && ('' != $this->_config_data['ldap_username']) && ('' != $this->_config_data['ldap_password']))
        {
            $domain_controllers = str_replace(","," ",str_replace(";"," ",$this->_config_data['ldap_domain_controllers']));
            $ldap_options = array('account_suffix'     => $this->_config_data['ldap_account_suffix'],
                                  'base_dn'            => $this->_config_data['ldap_base_dn'],
                                  'domain_controllers' => array($domain_controllers),
                                  'ad_username'        => $this->_config_data['ldap_username'],
                                  'ad_password'        => $this->_config_data['ldap_password']);

            $ldap_connection=new MultiotpAdLdap($ldap_options);
            if ($user_info = $ldap_connection->user_info($user))
            {
                $result_array['memberof']    = (isset($user_info[0]['memberof'])?($user_info[0]['memberof']):"");
                $result_array['mail']        = (isset($user_info[0]['mail'][0])?decode_utf8_if_needed($user_info[0]['mail'][0]):"");
                $result_array['displayname'] = (isset($user_info[0]['displayname'][0])?decode_utf8_if_needed($user_info[0]['displayname'][0]):"");
                $result_array['mobile']      = (isset($user_info[0]['mobile'][0])?decode_utf8_if_needed($user_info[0]['mobile'][0]):"");
            }
        }
        else
        {
            $this->WriteLog("Error: no ldap connexion information");
        }
        return $result_array;
    }

    
    function CheckLdapAuthentication($ldap_user, $ldap_password)
    {
        $result = FALSE;
        if (!function_exists('ldap_connect'))
        {
            $this->WriteLog("Error: PHP LDAP library is not installed");
        }
        else
        {
            define(LDAP_OPT_DIAGNOSTIC_MESSAGE, 0x0032);
            
            $ldap_user_prefix = "";
            $ldap_user_suffix = "";
            
            if (('' != $ldap_password) && ('' != $this->_config_data['ldap_domain_controllers']))
            {
                $domain_controllers = str_replace(","," ",str_replace(";"," ",$this->_config_data['ldap_domain_controllers']));
                $ldap_user_prefix = ($this->_config_data['ldap_domain_name'] != '')?$this->_config_data['ldap_domain_name'].chr(92):"";
                if ('' == $ldap_user_prefix)
                {
                    $ldap_user_suffix = ($this->_config_data['ldap_account_suffix'] != '')?$this->_config_data['ldap_account_suffix']:"";
                    if ('' != $ldap_user_suffix)
                    {
                        if (FALSE === strpos($ldap_user_suffix, ','))
                        {
                            $ldap_user_suffix = "@".$ldap_user_suffix;
                        }
                        else
                        {
                            $ldap_user_prefix  = "cn=";
                            $ldap_user_suffix = ",".$ldap_user_suffix;
                        }
                    }
                }
                if ((FALSE !== strpos($ldap_user_prefix, chr(92))) && (FALSE !== strpos($ldap_user, chr(92))))
                {
                    $ldap_user_prefix = '';
                }
                
                // ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);
                if ($ldapconn = @ldap_connect($domain_controllers))
                {
                    ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
                    ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
                    $result = @ldap_bind($ldapconn, ($ldap_user_prefix).encode_utf8_if_needed($ldap_user).$ldap_user_suffix, encode_utf8_if_needed($ldap_password));
                    /*
                    if ($result)
                    {
                        if (ldap_get_option($ldapconn, LDAP_OPT_DIAGNOSTIC_MESSAGE, $extended_error))
                        {
                            echo "Error Binding to LDAP: $extended_error";
                        }
                        else
                        {
                            echo "Error Binding to LDAP: No additional information is available.";
                        }
                    }
                    */                
                    @ldap_unbind($ldapconn);
                }
            }
        }
        return $result;
    }


    function SetTokenDataReadFlag($flag)
    {
        $this->_token_data_read_flag = $flag;
    }
    
    
    function GetTokenDataReadFlag()
    {
        return $this->_token_data_read_flag;
    }


    function SetBaseDir($base_dir)
    {
        $this->_base_dir = $this->ConvertToUnixPath($base_dir);
    }

    
    function GetBaseDir()
    {
        return ($this->_base_dir);
    }

    
    function GetScriptFolder()
    {
        if ('' != $this->GetBaseDir())
        {
            $current_script_folder_detected = $this->ConvertToUnixPath($this->GetBaseDir());
        }
        else
        {
            // Detect the current folder, change Windows notation to universal notation if needed
            $current_folder = $this->ConvertToUnixPath(getcwd());
            $current_script_folder = $this->ConvertToUnixPath(isset($_SERVER["argv"][0])?$_SERVER["argv"][0]:'');
            if ('' == (trim($current_script_folder)))
            {
                $current_script_folder = isset($_SERVER['SCRIPT_FILENAME'])?$_SERVER['SCRIPT_FILENAME']:'';
            }
            
            if (FALSE === strpos($current_script_folder,"/"))
            {
                $current_script_folder_detected = dirname($current_folder."/fake.file");
            }
            else
            {
                //$current_script_folder_detected = dirname($current_script_folder);
                $current_script_folder_detected = dirname(__FILE__);
            }
        }

        if (substr($current_script_folder_detected,-1) != "/")
        {
            $current_script_folder_detected.="/";
        }//echo $this->ConvertToWindowsPathIfNeeded($current_script_folder_detected);
        return $this->ConvertToWindowsPathIfNeeded($current_script_folder_detected);
    }

    
    function ConvertToUnixPath($path)
    {
        return str_replace("\\","/",$path);
    }

    
    function ConvertToWindowsPathIfNeeded($path)
    {
        $result = $path;
        if (FALSE !== strpos($result,":"))
        {
            $result = str_replace("/","\\",$result);
        }
        return $result;
    }

    
    function SetLogFolder($folder)
    {
        $new_folder = $this->ConvertToUnixPath($folder);
        if (substr($new_folder,-1) != "/")
        {
            $new_folder.="/";
        }
        $new_folder = $this->ConvertToWindowsPathIfNeeded($new_folder);
        $this->_log_folder = $new_folder;
        if (!file_exists($new_folder))
        {
            @mkdir($new_folder);
        }
    }


    function GetLogFolder()
    {
        if ('' == $this->_log_folder)
        {
            $this->SetLogFolder($this->GetScriptFolder()."log/");
        }
        return $this->ConvertToWindowsPathIfNeeded($this->_log_folder);
    }


    function WriteLog($info, $file_only = FALSE, $error_code = FALSE)
    {
        $pre_info = "";
        if ('' != ($this->GetSourceIp().$this->GetSourceMac()))
        {
            $pre_info.= "from ";
            if ('' != $this->GetSourceIp())
            {
                $pre_info.= "(".$this->GetSourceIp().") ";
            }
            if ('' != $this->GetSourceMac())
            {
                $pre_info.= "[".$this->GetSourceMac()."] ";
            }
        }
        if ('' != ($this->GetCallingIp().$this->GetCallingMac()))
        {
            $pre_info.= "for ";
            if ('' != $this->GetCallingIp())
            {
                $pre_info.= "(".$this->GetCallingIp().") ";
            }
            if ('' != $this->GetCallingMac())
            {
                $pre_info.= "[".$this->GetCallingMac()."] ";
            }
        }
        $log_info = $pre_info.$info;

        if (($this->GetDisplayLogFlag()) && (!$error_code))
        {
            $display_text = "\nLOG ".date("Y-m-d H:i:s").' '.(('' == $this->_user)?'':'(user '.$this->_user.') ').$log_info."\n";
            if ($this->IsDebugViaHtml())
            {
                $display_text = str_replace("\n","<br />\n", $display_text);
            }
            echo $display_text;
        }
        $log_link = NULL;
        if ($this->_log_flag)
        {
            if ((!$file_only) && ('mysql' == $this->GetBackendType()) && $this->GetBackendTypeValidated() && ('' != $this->_config_data['sql_log_table']))
            {
                /*
                if (!$this->_log_header_written)
                {
                    if (!file_exists($this->GetLogFolder()))
                    {
                        @mkdir($this->GetLogFolder());
                    }
                    $file_created = (!file_exists($this->GetLogFolder().$this->_log_file_name));
                    $log_file_handle = fopen($this->GetLogFolder().$this->_log_file_name,"ab+");
                    fwrite($log_file_handle,str_repeat("=",40)."\n");
                    fwrite($log_file_handle,'multiotp '.$this->GetVersion()."\n");
                    $this->_log_header_written = TRUE;
                    fwrite($log_file_handle,date("Y-m-d H:i:s")." The log file is stored in ".$this->GetBackendType()."\n");
                    fclose($log_file_handle);
                }
                */                
                if ('mysql' == $this->GetBackendType())
                {
                    if (('' != $this->_config_data['sql_server']) &&
                        ('' != $this->_config_data['sql_username']) &&
                        ('' != $this->_config_data['sql_password']) &&
                        ('' != $this->_config_data['sql_database']))
                    {
                        if (!($log_link = mysql_connect($this->_config_data['sql_server'],
                                                    $this->_config_data['sql_username'],
                                                    $this->_config_data['sql_password'])))
                        {
                            $this->WriteLog("Error: Bad SQL authentication parameters: ".mysql_error(), TRUE);
                        }
                        else
                        {
                            if (!mysql_select_db($this->_config_data['sql_database']))
                            {
                                $this->WriteLog("Error: Bad SQL database: ".mysql_error(), TRUE);
                            }
                            else
                            {
                                $log_info_escaped = substr(mysql_real_escape_string($log_info, $log_link),0,255);
                                $log_user_escaped = mysql_real_escape_string($this->_user, $log_link);
                                $sQuery  = "INSERT INTO `".$this->_config_data['sql_log_table']."` (`datetime`,`user`,`logentry`) VALUES ('".date("Y-m-d H:i:s")."','{$log_user_escaped}','{$log_info_escaped}')";
                                if (!(mysql_query($sQuery, $log_link)))
                                {
                                    $this->WriteLog("Error: SQL database query error ($sQuery) : ".mysql_error(), TRUE);
                                }
                            }
                            //mysql_close($log_link);
                        }
                    }
                }
            }
            else
            {
                if (!file_exists($this->GetLogFolder()))
                {
                    @mkdir($this->GetLogFolder());
                }
                $file_created = (!file_exists($this->GetLogFolder().$this->_log_file_name));
                $log_file_handle = fopen($this->GetLogFolder().$this->_log_file_name,"ab+");
                if (!$this->_log_header_written)
                {
                    fwrite($log_file_handle,str_repeat("=",40)."\n");
                    fwrite($log_file_handle,'multiotp '.$this->GetVersion()."\n");
                    $this->_log_header_written = TRUE;
                }
                fwrite($log_file_handle,date("Y-m-d H:i:s")." ".$log_info."\n");
                fclose($log_file_handle);
                if ($file_created && ('' != $this->GetLinuxFileMode()))
                {
                    chmod($this->GetLogFolder().$this->_log_file_name, octdec($this->GetLinuxFileMode()));
                }
            }
        }
    }


    function ShowLog()
    {
        if ('mysql' == $this->GetBackendType())
        {
            if (('' != $this->_config_data['sql_server']) &&
                ('' != $this->_config_data['sql_username']) &&
                ('' != $this->_config_data['sql_password']) &&
                ('' != $this->_config_data['sql_database']))
            {
                if (!($log_link = mysql_connect($this->_config_data['sql_server'],
                                            $this->_config_data['sql_username'],
                                            $this->_config_data['sql_password'])))
                {
                    $this->WriteLog("Error: Bad SQL authentication parameters: ".mysql_error(), TRUE);
                }
                else
                {
                    if (!mysql_select_db($this->_config_data['sql_database']))
                    {
                        $this->WriteLog("Error: Bad SQL database: ".mysql_error(), TRUE);
                    }
                    else
                    {
                        $sQuery  = "SELECT * FROM ".$this->_config_data['sql_log_table'];
                        if (!($rResult = mysql_query($sQuery, $log_link)))
                        {
                            $this->WriteLog("Error: Unable to access the database: ".mysql_error());
                        }
                        else
                        {
                            while ($aRow = mysql_fetch_assoc($rResult))
                            {
                                echo trim($aRow['datetime'].' '.$aRow['user']).' '.$aRow['logentry']."\n";
                            }                         
                        }
                    }
                    //mysql_close($log_link);
                }
            }
        }
        elseif (file_exists($this->GetLogFolder().$this->_log_file_name))
        {
            $log_file_handle = fopen($this->GetLogFolder().$this->_log_file_name,"r");
            while (!feof($log_file_handle))
            {
                echo trim(fgets($log_file_handle))."\n";
            }
            fclose($log_file_handle);
        }
    }

    
    function EnableLog()
    {
        $this->_log_flag = TRUE;
        if ('' == $this->_log_folder)
        {
            $this->SetLogFolder($this->GetScriptFolder()."log/");
        }
    }

    
    function DisableLog()
    {
        $this->_log_flag = FALSE;
    }


    function EnableVerboseLog()
    {
        $this->EnableLog();
        $this->_log_verbose_flag = TRUE;
    }

    
    function DisableVerboseLog()
    {
        $this->_log_verbose_flag = FALSE;
    }


    function GetVerboseFlag()
    {
        return $this->_log_verbose_flag;
    }


    function EnableDisplayLog()
    {
        $this->_log_display_flag = TRUE;
    }

    
    function DisableDisplayLog()
    {
        $this->_log_display_flag = FALSE;
    }


    function GetDisplayLogFlag()
    {
        return $this->_log_display_flag;
    }

    
    function GetReplyMessageForRadius()
    {
        return (isset($this->_reply_array_for_radius[0])?$this->_reply_array_for_radius[0]:'');
    }


    function SetReplyMessageForRadius($value)
    {
        $this->_reply_array_for_radius = array();
        $this->AddReplyArrayMessageForRadius($value);
    }

    
    function GetReplyArrayForRadius()
    {
        return $this->_reply_array_for_radius;
    }


    function AddReplyArrayForRadius($value)
    {
        $this->_reply_array_for_radius[] = $value;
    }

    
    function SetVerboseLogPrefix($value)
    {
        $this->_config_data['verbose_log_prefix'] = $value;
    }

    
    function GetVerboseLogPrefix()
    {
        return $this->_config_data['verbose_log_prefix'];
    }

    
    function SetAttributesToEncrypt($attributes_to_encrypt)
    {
        $this->_attributes_to_encrypt = $attributes_to_encrypt;
    }


    function GetAttributesToEncrypt()
    {
        return $this->_attributes_to_encrypt;
    }
    
    
    
    function SetUsersFolder($folder)
    {
        $new_folder = $this->ConvertToUnixPath($folder);
        if (substr($new_folder,-1) != "/")
        {
            $new_folder.="/";
        }
        $new_folder = $this->ConvertToWindowsPathIfNeeded($new_folder);
        $this->_users_folder = $new_folder;
        if (!file_exists($new_folder))
        {
            if (!@mkdir($new_folder))
            {
                $this->WriteLog("Error: unable to create the missing users folder ".$new_folder);
            }
        }
    }

    
    function GetUsersFolder()
    {
        if ('' == $this->_users_folder)
        {
            $this->SetUsersFolder($this->GetScriptFolder()."users/");
        }
        return $this->ConvertToWindowsPathIfNeeded($this->_users_folder);
    }

    
    function SetDevicesFolder($folder)
    {
        $new_folder = $this->ConvertToUnixPath($folder);
        if (substr($new_folder,-1) != "/")
        {
            $new_folder.="/";
        }
        $new_folder = $this->ConvertToWindowsPathIfNeeded($new_folder);
        $this->_devices_folder = $new_folder;
        if (!file_exists($new_folder))
        {
            if (!@mkdir($new_folder))
            {
                $this->WriteLog("Error: unable to create the missing devices folder ".$new_folder);
            }
        }
    }


    function GetDevicesFolder()
    {
        if ('' == $this->_devices_folder)
        {
            $this->SetDevicesFolder($this->GetScriptFolder()."devices/");
        }
        return $this->ConvertToWindowsPathIfNeeded($this->_devices_folder);
    }

    
    function SetGroupsFolder($folder)
    {
        $new_folder = $this->ConvertToUnixPath($folder);
        if (substr($new_folder,-1) != "/")
        {
            $new_folder.="/";
        }
        $new_folder = $this->ConvertToWindowsPathIfNeeded($new_folder);
        $this->_groups_folder = $new_folder;
        if (!file_exists($new_folder))
        {
            if (!@mkdir($new_folder))
            {
                $this->WriteLog("Error: unable to create the missing groups folder ".$new_folder);
            }
        }
    }


    function GetGroupsFolder()
    {
        if ('' == $this->_groups_folder)
        {
            $this->SetGroupsFolder($this->GetScriptFolder()."groups/");
        }
        return $this->ConvertToWindowsPathIfNeeded($this->_groups_folder);
    }

    
    function SetTokenManufacturer($manufacturer)
    {
        $this->_token_data['manufacturer'] = $manufacturer;
    }


    function GetTokenManufacturer()
    {
        return $this->_token_data['manufacturer'];
    }
    

    function GetTokenEncryptionHash()
    {
        return $this->_token_data['encryption_hash'];
    }


    function SetTokenAttributedUsers($attributed_users)
    {
        $this->_token_data['attributed_users'] = $attributed_users;
    }


    function AddTokenAttributedUsers($add_users)
    {
        $actual = trim($this->_token_data['attributed_users']);
        $this->_token_data['attributed_users'] = $actual.(('' != $actual)?',':'').$add_users;
    }


    function GetTokenAttributedUsers()
    {
        return $this->_token_data['attributed_users'];
    }


    function SetTokenSerialNumber($token_serial)
    {
        $this->_token_data['token_serial'] = $token_serial;
    }


    function GetTokenSerialNumber()
    {
        return $this->_token_data['token_serial'];
    }
    
    
    function SetTokenIssuer($issuer)
    {
        $this->_token_data['issuer'] = $issuer;
    }


    function GetTokenIssuer()
    {
        return $this->_token_data['issuer'];
    }
    
    
    function SetTokenKeyAlgorithm($key_algorithm)
    {
        $this->_token_data['key_algorithm'] = $key_algorithm;
    }


    function GetTokenKeyAlgorithm()
    {
        return $this->_token_data['key_algorithm'];
    }
    
    
    function SetTokenAlgorithm($algorithm)
    {
        $this->_token_data['algorithm'] = $algorithm;
    }


    function GetTokenAlgorithm()
    {
        return $this->_token_data['algorithm'];
    }
    
    
    function SetTokenOtp($otp)
    {
        $this->_token_data['otp'] = $otp;
    }


    function GetTokenOtp()
    {
        return $this->_token_data['otp'];
    }
    
    
    function SetTokenFormat($format)
    {
        $this->_token_data['format'] = $format;
    }


    function GetTokenFormat()
    {
        return $this->_token_data['format'];
    }
    
    
    function SetTokenNumberOfDigits($number_of_digits)
    {
        $this->_token_data['number_of_digits'] = $number_of_digits;
    }


    function GetTokenNumberOfDigits()
    {
        return $this->_token_data['number_of_digits'];
    }
    
    
    function SetTokenLastEvent($last_event)
    {
        $this->_token_data['last_event'] = $last_event;
    }


    function GetTokenLastEvent()
    {
        return $this->_token_data['last_event'];
    }
    
    
    function SetTokenDeltaTime($delta_time)
    {
        $this->_token_data['delta_time'] = $delta_time;
    }


    function GetTokenDeltaTime()
    {
        return $this->_token_data['delta_time'];
    }
    
    
    function SetTokenTimeInterval($time_interval)
    {
        $this->_token_data['time_interval'] = $time_interval;
    }


    function GetTokenTimeInterval()
    {
        return $this->_token_data['time_interval'];
    }
    
    
    function SetTokenSeed($token_seed)
    {
        $this->_token_data['token_seed'] = $token_seed;
    }


    function GetTokenSeed()
    {
        return $this->_token_data['token_seed'];
    }
    

    function SetTokensFolder($folder)
    {
        $new_folder = $this->ConvertToUnixPath($folder);
        if (substr($new_folder,-1) != "/")
        {
            $new_folder.="/";
        }
        $new_folder = $this->ConvertToWindowsPathIfNeeded($new_folder);
        $this->_tokens_folder = $new_folder;
        if (!file_exists($new_folder))
        {
            if (!@mkdir($new_folder))
            {
                $this->WriteLog("Error: unable to create the missing tokens folder ".$new_folder);
            }
        }
    }


    function GetTokensFolder()
    {
        if ('' == $this->_tokens_folder)
        {
            $this->SetTokensFolder($this->GetScriptFolder()."tokens/");
        }
        return $this->ConvertToWindowsPathIfNeeded($this->_tokens_folder);
    }

    
    function DeleteToken($token = '')
    {
        if ('' != $token)
        {
            $this->SetToken($token);
        }
        
        $result = FALSE;
        
        // First, we delete the user file if the backend is files or when migration is enabled
        if (('files' == $this->GetBackendType()) || ($this->GetMigrationFromFile()))
        {
            $token_filename = strtolower($this->_token).'.db';
            if (!file_exists($this->GetTokensFolder().$token_filename))
            {
                $this->WriteLog("Error: unable to delete token ".$this->_token.", database file ".$this->GetTokensFolder().$token_filename." does not exist");
            }
            else
            {
                $result = unlink($this->GetTokensFolder().$token_filename);
                if ($result)
                {
                    $this->WriteLog("Info: token ".$this->_token." successfully deleted");
                }
                else
                {
                    $this->WriteLog("Error: unable to delete token ".$this->_token);
                }
            }
        }

        if ($this->GetBackendTypeValidated())
        {
            switch ($this->_config_data['backend_type'])
            {
                case 'mysql':
                    if ($this->OpenMysqlDatabase())
                    {
                        if ('' != $this->_config_data['sql_tokens_table'])
                        {
                            $sQuery  = "DELETE FROM `".$this->_config_data['sql_tokens_table']."` WHERE `token_id` = '{$this->_token}'";
                            if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                            {
                                $this->WriteLog("Error: Could not delete user ".$this->_user.": ".mysql_error());
                            }
                            if (0 == mysql_affected_rows($this->_mysql_database_link))
                            {
                                $this->WriteLog("Error: Could not delete token ".$this->_token.". Token does not exist");
                            }
                            else
                            {
                                $this->WriteLog("Info: token ".$this->_token." successfully deleted");
                                $result = TRUE;
                            }
                        }
                    }
                    break;
                default:
                // Nothing to do if the backend type is unknown
                    break;
            }                        
        }
        return $result;
    }


    function LockUser($user = '')
    {
        $result = FALSE;
        if ('' != $user)
        {
            $this->SetUser($user);
        }
        if ($this->ReadUserData('', FALSE, TRUE)) // LOCALLY ONLY, not on the server if any
        {
            $this->SetUserLocked(1);
            $this->WriteLog("Info: User ".$this->_user." successfully locked");
            $this->WriteUserData();
            $result = TRUE;
        }
        return $result;
    }


    function UnlockUser($user = '')
    {
        $result = FALSE;
        if ('' != $user)
        {
            $this->SetUser($user);
        }
        if ($this->ReadUserData('', FALSE, TRUE)) // LOCALLY ONLY, not on the server if any
        {
            $this->SetUserErrorCounter(0);
            $this->SetUserLocked(0);
            $this->WriteLog("Info: User ".$this->_user." successfully unlocked");
            $this->WriteUserData();
            $result = TRUE;
        }
        return $result;
    }


    function DeleteUser($user = '', $no_error_info = FALSE)
    {
        if ('' != $user)
        {
            $this->SetUser($user);
        }
        
        $result = FALSE;
        
        // First, we delete the user file if the backend is files or when migration is enabled
        if (('files' == $this->GetBackendType()) || ($this->GetMigrationFromFile()))
        {
            $user_filename = strtolower($this->_user).'.db';
            if (!file_exists($this->GetUsersFolder().$user_filename))
            {
                if (!$no_error_info)
                {
                    $this->WriteLog("Error: unable to delete user ".$this->_user.", database file ".$this->GetUsersFolder().$user_filename." does not exist");
                }
            }
            else
            {
                $result = unlink($this->GetUsersFolder().$user_filename);
                if ($result)
                {
                    $this->WriteLog("Info: user ".$this->_user." successfully deleted");
                }
                elseif (!$this->GetMigrationFromFile())
                {
                    $this->WriteLog("Error: unable to delete user ".$this->_user);
                }
            }
        }

        if ($this->GetBackendTypeValidated())
        {
            switch ($this->_config_data['backend_type'])
            {
                case 'mysql':
                    if ($this->OpenMysqlDatabase())
                    {
                        if ('' != $this->_config_data['sql_users_table'])
                        {
                            $sQuery  = "DELETE FROM `".$this->_config_data['sql_users_table']."` WHERE `user` = '{$this->_user}'";
                            if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                            {
                                if (!$no_error_info)
                                {
                                    $this->WriteLog("Error: Could not delete user ".$this->_user.": ".mysql_error());
                                }
                            }
                            elseif (0 == mysql_affected_rows($this->_mysql_database_link))
                            {
                                if (!$no_error_info)
                                {
                                    $this->WriteLog("Error: Could not delete user ".$this->_user.". User does not exist");
                                }
                            }
                            else
                            {
                                $this->WriteLog("Info: user ".$this->_user." successfully deleted");
                                $result = TRUE;
                            }
                        }
                    }
                    break;
                default:
                // Nothing to do if the backend type is unknown
                    break;
            }                        
        }
        return $result;
    }


    function ReadUserData($user = '', $create = FALSE, $do_not_check_on_server = FALSE)
    {
    
        if ('' != $user)
        {
            $this->SetUser($user);
        }
        $result = FALSE;

        // We reset all values (we know the key based on the schema)
        $this->ResetUserArray();

        // First, we read the user file if the backend is files or when migration is enabled
        if (('files' == $this->GetBackendType()) || ($this->GetMigrationFromFile()))
        {
            $user_filename = strtolower($this->GetUser()).'.db';
            if (!file_exists($this->GetUsersFolder().$user_filename))
            {
                if (!$create)
                {
                    $this->WriteLog("Error: database file ".$this->GetUsersFolder().$user_filename." for user ".$this->_user." does not exist");
                }
            }
            else
            {
                $this->_user_data['multi_account'] = 0;
                $this->_user_data['time_interval'] = 0;
                
                $user_file_handler = fopen($this->GetUsersFolder().$user_filename, "rt");
                $first_line = trim(fgets($user_file_handler));
                $v3 = (FALSE !== strpos(strtolower($first_line),"multiotp-database-format-v3"));
                
                // First version format support
                if (FALSE === strpos(strtolower($first_line),"multiotp-database-format"))
                {
                    $this->_user_data['algorithm']          = $first_line;
                    $this->_user_data['token_seed']         = trim(fgets($user_file_handler));
                    $this->_user_data['user_pin']           = trim(fgets($user_file_handler));
                    $this->_user_data['number_of_digits']   = trim(fgets($user_file_handler));
                    $this->_user_data['last_event']         = intval(trim(fgets($user_file_handler)) - 1);
                    $this->_user_data['request_prefix_pin'] = intval(trim(fgets($user_file_handler)));
                    $this->_user_data['last_login']         = intval(trim(fgets($user_file_handler)));
                    $this->_user_data['error_counter']      = intval(trim(fgets($user_file_handler)));
                    $this->_user_data['locked']             = intval(trim(fgets($user_file_handler)));
                }
                else
                {
                    while (!feof($user_file_handler))
                    {
                        $line = trim(fgets($user_file_handler));
                        $line_array = explode("=",$line,2);
                        if ($v3) // v3 format, only tags followed by := instead of = are encrypted
                        {
                            if (":" == substr($line_array[0], -1))
                            {
                                $line_array[0] = substr($line_array[0], 0, strlen($line_array[0]) -1);
                                $line_array[1] = $this->Decrypt($line_array[0],$line_array[1]);
                            }
                        }
                        else // v2 format, only defined tags are encrypted
                        {
                            if (FALSE !== strpos(strtolower($this->_attributes_to_encrypt), strtolower('*'.$line_array[0].'*')))
                            {
                                $line_array[1] = $this->Decrypt($line_array[0],$line_array[1]);
                            }
                        }
                        if ('' != trim($line_array[0]))
                        {
                            $this->_user_data[strtolower($line_array[0])] = $line_array[1];
                        }
                    }
                }
                fclose($user_file_handler);
                $result = TRUE;
                if ('' != $this->_user_data['encryption_hash'])
                {
                    if ($this->_user_data['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey()))
                    {
                        $this->_user_data['encryption_hash'] = "ERROR";
                        $this->WriteLog("Error: the file encryption key has been changed");
                        $result = FALSE;
                    }
                }
            }
        }

        // And now, we override the values if another backend type is defined
        if ($this->GetBackendTypeValidated())
        {
            switch ($this->_config_data['backend_type'])
            {
                case 'mysql':
                    if ($this->OpenMysqlDatabase())
                    {
                        if ('' != $this->_config_data['sql_users_table'])
                        {
                            $sQuery  = "SELECT * FROM `".$this->_config_data['sql_users_table']."` WHERE `user` = '".$this->_user."'";
                            if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                            {
                                $this->WriteLog("Error: SQL database query error ($sQuery) : ".mysql_error(), TRUE);
                                $result = FALSE;
                            }
                            else
                            {
                                $aRow    = mysql_fetch_assoc($rResult);
                                $result = FALSE;
                                while(list($key, $value) = @each($aRow))
                                {
                                    $in_the_schema = FALSE;
                                    reset($this->_sql_tables_schema['users']);
                                    while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['users']))
                                    {
                                        if ($valid_key == $key)
                                        {
                                            $in_the_schema = TRUE;
                                        }
                                    }
                                    if (($in_the_schema) && ($key != 'user'))
                                    {
                                        // This was the old rule, but it's not a good one
                                        // if (FALSE !== strpos(strtolower($this->_attributes_to_encrypt), strtolower('*'.$key.'*')))
                                        if (('ENC:' == substr($value,0,4)) && (':ENC' == substr($value,-4)))
                                        {
                                            $value = substr($value,4);
                                            $value = substr($value,0,strlen($value)-4);
                                            $this->_user_data[$key] = $this->Decrypt($key,$value);
                                        }
                                        else
                                        {
                                            $this->_user_data[$key] = $value;
                                        }
                                    }                                    
                                    elseif ((!$in_the_schema) && ('unique_id' != $key)  && $this->GetVerboseFlag())
                                    {
                                        $this->WriteLog("Warning: the key ".$key." is not in the users database schema");
                                    }
                                    $result = TRUE;
                                }
                                if(0 == count($aRow) && !$create)
                                {
                                    $this->WriteLog("Error: SQL database entry for user ".$this->_user." does not exist");
                                }
                            }
                        }
                        if ('' != $this->_user_data['encryption_hash'])
                        {
                            if ($this->_user_data['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey()))
                            {
                                $this->_user_data['encryption_hash'] = "ERROR";
                                $this->WriteLog("Error: the mysql encryption key has been changed");
                                $result = FALSE;
                            }
                        }
                    }
                    break;
                default:
                // Nothing to do if the backend type is unknown
                    break;
            }
        }
        
        // And now, we do the ReadUserData online on the server
        $server_result = -1;
        if ((!$do_not_check_on_server) && ('' != $this->GetServerUrl()))
        {
            $server_result = $this->ReadUserDataOnServer($this->GetUser());
            if (20 < strlen($server_result))
            {
                $this->_user_data['encryption_hash'] = $this->CalculateControlHash($this->GetEncryptionKey());
                $server_array = explode("\n",$server_result);
                $server_result = 19;

                foreach ($server_array as $one_line)
                {
                    $line = trim($one_line);
                    $line_array = explode("=",$line,2);
                    if (":" == substr($line_array[0], -1))
                    {
                        $line_array[0] = substr($line_array[0], 0, strlen($line_array[0]) -1);
                        $line_array[1] = $this->Decrypt($line_array[0], $line_array[1], $this->GetServerSecret());
                    }
                    if ('' != trim($line_array[0]))
                    {
                        if ('encryption_hash' != strtolower($line_array[0]))
                        {
                            $this->_user_data[strtolower($line_array[0])] = $line_array[1];
                        }
                    }
                }
                $result = TRUE;
            }
        }
        
        $this->SetUserDataReadFlag($result);
        return $result;
    }
    
    
    function WriteUserData()
    {
        $result = FALSE;
        $user_created = FALSE;
        $this->_user_data['encryption_hash'] = $this->CalculateControlHash($this->GetEncryptionKey());
        
        if ((($this->GetBackendTypeValidated()) && ('' != $this->_config_data['sql_users_table'])) || ('files' == $this->GetBackendType()))
        {
            switch ($this->GetBackendType())
            {
                case 'mysql':
                    if ($this->OpenMysqlDatabase())
                    {
                        $result = TRUE;
                        $sQi_Columns = '';
                        $sQi_Values  = '';
                        $sQu_Data    = '';
                        reset($this->_user_data);
                        while(list($key, $value) = each($this->_user_data))
                        {
                            $in_the_schema = FALSE;
                            reset($this->_sql_tables_schema['users']);
                            while(list($valid_key, $valid_format) = each($this->_sql_tables_schema['users']))
                            {
                                if ($valid_key == $key)
                                {
                                    $in_the_schema = TRUE;
                                }
                            }
                            if (($in_the_schema) && ($key != 'user'))
                            {
                                if ((FALSE !== strpos(strtolower($this->_attributes_to_encrypt), strtolower('*'.$key.'*'))) && ('' != $value))
                                {
                                    $value = 'ENC:'.$this->Encrypt($key,$value).':ENC';
                                }
                                $sQu_Data    .= "`{$key}`='{$value}',"; // Data for UPDATE query
                                $sQi_Columns .= "`{$key}`,"; // Columns for INSERT query
                                $sQi_Values  .= "'{$value}',"; // Values for INSERT query
                            }
                            elseif ((!$in_the_schema) && ('unique_id' != $key)  && $this->GetVerboseFlag())
                            {
                                $this->WriteLog("Warning: the key ".$key." is not in the users database schema");
                            }
                        }
                        $sQuery = "SELECT * FROM `".$this->_config_data['sql_users_table']."` WHERE user = '".$this->_user."'";
                        if (!($result = mysql_query($sQuery, $this->_mysql_database_link)))
                        {
                            $this->WriteLog("Error: SQL database query error ($sQuery) : ".mysql_error(), TRUE);
                        }
                        $num_rows = mysql_num_rows($result);
                        if ($num_rows > 0)
                        {
                            $sQuery = "UPDATE `".$this->_config_data['sql_users_table']."` SET ".substr($sQu_Data,0,-1)." WHERE `user`='{$this->_user}'";
                            if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                            {
                                $this->WriteLog("Error: SQL database query error ($sQuery) : ".mysql_error(), TRUE);
                                $result = FALSE;
                            }
                        }
                        else
                        {
                            $sQuery = "INSERT INTO `".$this->_config_data['sql_users_table']."` (`user`,".substr($sQi_Columns,0,-1).") VALUES ('{$this->_user}',".substr($sQi_Values,0,-1).")";
                            if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                            {
                                $this->WriteLog("Error: SQL database query error ($sQuery) : ".mysql_error(), TRUE);
                                $result = FALSE;
                                break;
                            }
                            if (0 == mysql_affected_rows($this->_mysql_database_link))
                            {
                                $this->WriteLog("Error: SQL database entry for user ".$this->_user." cannot be created or changed");
                                $result = FALSE;
                            }
                            else
                            {
                                $user_created = TRUE;
                            }
                        }
                    }
                    break;
                case 'files':
                default:
                    $user_filename = strtolower($this->_user).'.db';
                    $file_created = FALSE;
                    if (!file_exists($this->GetUsersFolder().$user_filename))
                    {
                        $user_created = TRUE;
                        $file_created = TRUE;
                    }
                    if (!($user_file_handler = fopen($this->GetUsersFolder().$user_filename, "wt")))
                    {
                        $this->WriteLog("Error: database file for user ".$this->_user." cannot be written");
                    }
                    else
                    {
                        fwrite($user_file_handler,"multiotp-database-format-v3"."\n");
                        // foreach ($this->_user_data as $key => $value) // this is not working well in CLI mode
                        reset($this->_user_data);
                        while(list($key, $value) = each($this->_user_data))
                        {
                            if ('' != trim($key))
                            {
                                $line = strtolower($key);
                                if (FALSE !== strpos(strtolower($this->_attributes_to_encrypt), strtolower('*'.$key.'*')))
                                {
                                    $value = $this->Encrypt($key,$value);
                                    $line = $line.":";
                                }
                                $line = $line."=".$value;
                                fwrite($user_file_handler,$line."\n");
                            }
                        }
                        $result = TRUE;
                        fclose($user_file_handler);
                        if ($file_created && ('' != $this->GetLinuxFileMode()))
                        {
                            chmod($this->GetUsersFolder().$user_filename, octdec($this->GetLinuxFileMode()));
                        }
                    }
                    if ($this->GetVerboseFlag())
                    {
                        if ($file_created)
                        {
                            $this->WriteLog('Info: File created: '.$this->GetUsersFolder().$user_filename);
                        }
                    }                    
                    break;
            }
        }
        if ($user_created && $result)
        {
            $this->WriteLog('Info: User '.$this->_user.' successfully created and saved.');
        }
        return $result;
    }


    function ReadTokenData($token = '', $create = FALSE)
    {
        if ('' != $token)
        {
            $this->SetToken($token);
        }
        $result = FALSE;
        
        // We initialize the encryption hash to empty
        $this->_token_data['encryption_hash'] = '';
        
        // First, we read the user file if the backend is files or when migration is enabled
        if (('files' == $this->GetBackendType()) || ($this->GetMigrationFromFile()))
        {
            $token_filename = strtolower($this->GetToken()).'.db';
            if (!file_exists($this->GetTokensFolder().$token_filename))
            {
                if (!$create)
                {
                    $this->WriteLog("Error: database file ".$this->GetTokensFolder().$token_filename." for token ".$this->_token." does not exist");
                }
            }
            else
            {
                $token_file_handler = fopen($this->GetTokensFolder().$token_filename, "rt");
                $first_line = trim(fgets($token_file_handler));
                
                while (!feof($token_file_handler))
                {
                    $line = trim(fgets($token_file_handler));
                    $line_array = explode("=",$line,2);
                    if (":" == substr($line_array[0], -1))
                    {
                        $line_array[0] = substr($line_array[0], 0, strlen($line_array[0]) -1);
                        $line_array[1] = $this->Decrypt($line_array[0],$line_array[1]);
                    }
                    if ('' != trim($line_array[0]))
                    {
                        $this->_token_data[strtolower($line_array[0])] = $line_array[1];
                    }
                }
                
                fclose($token_file_handler);
                $result = TRUE;

                if ('' != $this->_token_data['encryption_hash'])
                {
                    if ($this->_token_data['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey()))
                    {
                        $this->_token_data['encryption_hash'] = "ERROR";
                        $this->WriteLog("Error: the file encryption key has been changed");
                        $result = FALSE;
                    }
                }
            }
        }

        // And now, we override the values if another backend type is defined
        if ($this->GetBackendTypeValidated())
        {
            switch ($this->_config_data['backend_type'])
            {
                case 'mysql':
                    if ($this->OpenMysqlDatabase())
                    {
                        if ('' != $this->_config_data['sql_tokens_table'])
                        {
                            $sQuery  = "SELECT * FROM `".$this->_config_data['sql_tokens_table']."` WHERE `token_id` = '".$this->_token."'";
                            if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                            {
                                $this->WriteLog("Error: SQL database query error ($sQuery) : ".mysql_error(), TRUE);
                            }
                            else
                            {
                                $aRow    = mysql_fetch_assoc($rResult);
                                $result = FALSE;
                                while(list($key, $value) = @each($aRow))
                                {
                                    $in_the_schema = FALSE;
                                    reset($this->_sql_tables_schema['tokens']);
                                    while(list($valid_key, $valid_format) = @each($this->_sql_tables_schema['tokens']))
                                    {
                                        if ($valid_key == $key)
                                        {
                                            $in_the_schema = TRUE;
                                        }
                                    }
                                    if (($in_the_schema) && ($key != 'token_id'))
                                    {
                                        // This was the old rule, but it's not a good one
                                        // if (FALSE !== strpos(strtolower($this->_attributes_to_encrypt), strtolower('*'.$key.'*')))
                                        if (('ENC:' == substr($value,0,4)) && (':ENC' == substr($value,-4)))
                                        {
                                            $value = substr($value,4);
                                            $value = substr($value,0,strlen($value)-4);
                                            $this->_token_data[$key] = $this->Decrypt($key,$value);
                                        }
                                        else
                                        {
                                            $this->_token_data[$key] = $value;
                                        }
                                    }                                    
                                    elseif ((!$in_the_schema) && ('unique_id' != $key)  && $this->GetVerboseFlag())
                                    {
                                        $this->WriteLog("Warning: the key ".$key." is not in the tokens database schema");
                                    }
                                    $result = TRUE;
                                }
                                if(0 == count($aRow) && !$create)
                                {
                                    $this->WriteLog("Error: SQL database entry for token ".$this->_token." does not exist");
                                }
                            }
                        }
                        if ('' != $this->_token_data['encryption_hash'])
                        {
                            if ($this->_token_data['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey()))
                            {
                                $this->_token_data['encryption_hash'] = "ERROR";
                                $this->WriteLog("Error: the mysql encryption key has been changed");
                                $result = FALSE;
                            }
                        }
                    }
                    break;
                default:
                // Nothing to do if the backend type is unknown
                    break;
            }
        }
        $this->SetTokenDataReadFlag($result);
        return $result;
    }


    function WriteTokenData()
    {
        $result = FALSE;
        $this->_token_data['encryption_hash'] = $this->CalculateControlHash($this->GetEncryptionKey());
        
        if ((($this->GetBackendTypeValidated()) && ('' != $this->_config_data['sql_tokens_table'])) || ('files' == $this->GetBackendType()))
        {
            switch ($this->GetBackendType())
            {
                case 'mysql':
                    if ($this->OpenMysqlDatabase())
                    {
                        $result = TRUE;
                        $sQi_Columns = '';
                        $sQi_Values  = '';
                        $sQu_Data    = '';
                        reset($this->_token_data);
                        while(list($key, $value) = each($this->_token_data))
                        {
                            $in_the_schema = FALSE;
                            reset($this->_sql_tables_schema['tokens']);
                            while(list($valid_key, $valid_format) = each($this->_sql_tables_schema['tokens']))
                            {
                                if ($valid_key == $key)
                                {
                                    $in_the_schema = TRUE;
                                }
                            }
                            if (($in_the_schema) && ($key != 'token_id'))
                            {
                                if ((FALSE !== strpos(strtolower($this->_attributes_to_encrypt), strtolower('*'.$key.'*'))) && ('' != $value))
                                {
                                    $value = 'ENC:'.$this->Encrypt($key,$value).':ENC';
                                }
                                $sQu_Data    .= "`{$key}`='{$value}',"; // Data for UPDATE query
                                $sQi_Columns .= "`{$key}`,"; // Columns for INSERT query
                                $sQi_Values  .= "'{$value}',"; // Values for INSERT query
                            }
                            elseif ((!$in_the_schema) && ('unique_id' != $key)  && $this->GetVerboseFlag())
                            {
                                $this->WriteLog("Warning: the key ".$key." is not in the tokens database schema");
                            }
                        }
                        $sQuery = "SELECT * FROM `".$this->_config_data['sql_tokens_table']."` WHERE token_id = '".$this->_token."'";
                        if (!($result = mysql_query($sQuery, $this->_mysql_database_link)))
                        {
                            $this->WriteLog("Error: SQL database query error ($sQuery) : ".mysql_error(), TRUE);
                        }
                        $num_rows = mysql_num_rows($result);
                        if ($num_rows > 0)
                        {
                            $sQuery = "UPDATE `".$this->_config_data['sql_tokens_table']."` SET ".substr($sQu_Data,0,-1)." WHERE `token_id`='{$this->_token}'";
                            if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                            {
                                $this->WriteLog("Error: SQL database query error ($sQuery) : ".mysql_error(), TRUE);
                                $result = FALSE;
                            }
                        }
                        else
                        {
                            $sQuery = "INSERT INTO `".$this->_config_data['sql_tokens_table']."` (`token_id`,".substr($sQi_Columns,0,-1).") VALUES ('{$this->_token}',".substr($sQi_Values,0,-1).")";
                            if (!($rResult = mysql_query($sQuery, $this->_mysql_database_link)))
                            {
                                $this->WriteLog("Error: SQL database query error ($sQuery) : ".mysql_error(), TRUE);
                                $result = FALSE;
                            }
                            elseif (0 == mysql_affected_rows($this->_mysql_database_link))
                            {
                                $this->WriteLog("Error: SQL database entry for token ".$this->_token." cannot be created or changed");
                                $result = FALSE;
                            }
                        }
                    }
                    break;
                case 'files':
                default:
                    $token_filename = strtolower($this->_token).'.db';
                    $file_created = (!file_exists($this->GetTokensFolder().$token_filename));
                    if (!($token_file_handler = fopen($this->GetTokensFolder().$token_filename, "wt")))
                    {
                        $this->WriteLog("Error: database file for token ".$this->_token." cannot be written");
                    }
                    else
                    {
                        fwrite($token_file_handler,"multiotp-database-format-v3"."\n");
                        // foreach ($this->_token_data as $key => $value) // this is not working well in CLI mode
                        reset($this->_token_data);
                        while(list($key, $value) = each($this->_token_data))
                        {
                            if ('' != trim($key))
                            {
                                $line = strtolower($key);
                                if (FALSE !== strpos(strtolower($this->_attributes_to_encrypt), strtolower('*'.$key.'*')))
                                {
                                    $value = $this->Encrypt($key,$value);
                                    $line = $line.":";
                                }
                                $line = $line."=".$value;
                                fwrite($token_file_handler,$line."\n");
                            }
                        }
                        $result = TRUE;
                        fclose($token_file_handler);
                        if ($file_created && ('' != $this->GetLinuxFileMode()))
                        {
                            chmod($this->GetTokensFolder().$token_filename, octdec($this->GetLinuxFileMode()));
                        }
                    }
                    break;
            }
        }
        return $result;
    }    

    
    function SendSms($sms_recipient, $sms_message_to_send, $real_user = '', $originator = '', $provider = '', $userkey = '', $password = '', $api_id = '')
    {
        $sms_number = $this->CleanPhoneNumber($sms_recipient);

        $result = 62;
        
        $sms_originator = (('' != $originator)?$originator:$this->GetSmsOriginator());
        $sms_provider = strtolower((('' != $provider)?$provider:$this->GetSmsProvider()));
        $sms_userkey = (('' != $userkey)?$userkey:$this->GetSmsUserkey());
        $sms_password = (('' != $password)?$password:$this->GetSmsPassword());
        $sms_api_id = (('' != $api_id)?$api_id:$this->GetSmsApiId());
       
        if ("aspsms" == $sms_provider)
        {
            $sms_message = new MultiotpAspSms($sms_userkey, $sms_password);
            $sms_message->setOriginator($sms_originator);
            $sms_message->addRecipient($sms_number);
            $sms_message->setContent(decode_utf8_if_needed($sms_message_to_send));
            $sms_result = intval($sms_message->sendSMS());
            
            if (1 != $sms_result)
            {
                $result = 61; // ERROR: SMS code request received, but an error occured during transmission
                $this->WriteLog("Info: SMS code request received for ".$real_user.", but the ".$sms_provider." error ".$sms_result." occured during transmission to ".$sms_number);
            }
            else
            {
                $result = 18; // INFO: SMS code request received
                $this->WriteLog("Info: SMS code request received for ".$real_user." and sent via ".$sms_provider." to ".$sms_number);
            }
        }
        elseif ("clickatell" == $sms_provider)
        {
            $sms_message = new MultiotpClickatell($sms_userkey, $sms_password, $sms_api_id);
            $sms_message->useRegularServer();
            $sms_message->setOriginator($sms_originator);
            $sms_message->setRecipient($sms_number);
            $sms_message->setContent(encode_utf8_if_needed($sms_message_to_send));
            $sms_result = intval($sms_message->sendSMS());
            
            if (1 != $sms_result)
            {
                $result = 61; // ERROR: SMS code request received, but an error occured during transmission
                $this->WriteLog("Info: SMS code request received for ".$real_user.", but the ".$sms_provider." error ".$sms_result." occured during transmission to ".$sms_number);
            }
            else
            {
                $result = 18; // INFO: SMS code request received
                $this->WriteLog("Info: SMS code request received for ".$real_user." and sent via ".$sms_provider." to ".$sms_number);
            }
        }
        elseif ("intellisms" == $sms_provider)
        {
            $sms_message = new MultiotpIntelliSms($sms_userkey, $sms_password);
            $sms_message->useRegularServer();
            $sms_message->setOriginator($sms_originator);
            $sms_message->setRecipient($sms_number);
            $sms_message->setContent(encode_utf8_if_needed($sms_message_to_send));
            $sms_result = $sms_message->sendSMS();
            
            if ("ID" != substr($sms_result,0,2))
            {
                $result = 61; // ERROR: SMS code request received, but an error occured during transmission
                $this->WriteLog("Info: SMS code request received for ".$real_user.", but the ".$sms_provider." error ".$sms_result." occured during transmission to ".$sms_number);
            }
            else
            {
                $result = 18; // INFO: SMS code request received
                $this->WriteLog("Info: SMS code request received for ".$real_user." and sent via ".$sms_provider." to ".$sms_number);
            }
        }
        elseif ("exec" == $sms_provider)
        {
            $exec_cmd = $sms_api_id;
            $exec_cmd = str_replace('%from', $sms_originator, $exec_cmd);
            $exec_cmd = str_replace('%to',  $sms_number,  $exec_cmd);
            $exec_cmd = str_replace('%msg',  encode_utf8_if_needed($sms_message_to_send),  $exec_cmd);
            exec($exec_cmd, $output);
            $result = 18; // INFO: SMS code request received
            $this->WriteLog("Info: SMS code request received for ".$real_user." and sent via ".$exec_cmd);
        }
        else
        {
            $result = 62; // ERROR: SMS provider not supported
            $this->WriteLog("Error: SMS provider ".$sms_provider." not supported");
        }
        return $result;
    }
    
    
    function ConvertToNiceToken($regular_token)
    {
        $token_length = strlen($regular_token);
        if (9 <= $token_length)
        {
            $sms_nice_token = substr($regular_token,0,3).'-'.substr($regular_token,3,3).'-'.substr($regular_token,6,($token_length-6));
        }
        elseif (6 < $token_length)
        {
            $sms_nice_token = substr($regular_token,0,intval($token_length/2)).'-'.substr($regular_token,intval($token_length/2),$token_length);
        }
        else
        {
            $sms_nice_token = $regular_token;
        }
        return $sms_nice_token;
    }

    
    /*********************************************************************
     *
     * Name: CheckUserToken
     * Short description: Check the token of a user and give the result, with resync options
     *
     * Creation 2013-08-20
     * Update 2010-08-12
     * @package multiotp
     * @version 4.0.4
     * @author SysCo/al
     *
     * @param   string  $user        User to check
     * @param   string  $input       Token to check
     * @param   string  $input_sync  Second token to check for resync
     * @return  int                  Error code (0 = successful authentication, 1n = info, >= 20 = error)
     *
     *********************************************************************/
    function CheckUserToken($user = '', $input = '', $input_sync = '', $display_status = FALSE, $ignore_lock = FALSE, $resync_challenged_password = FALSE, $do_not_check_on_server = FALSE)
    {
        if ('' != $user)
        {
            $this->SetUser($user);
        }
        return $this->CheckToken($input, $input_sync, $display_status, $ignore_lock, $resync_challenged_password, $do_not_check_on_server);
    }


    /*********************************************************************
     *
     * Name: CheckToken
     * Short description: Check the token of the actual user and give the result, with resync options
     *
     * Creation 2010-06-07
     * Update 2013-08-20
     * @package multiotp
     * @version 4.0.4
     * @author SysCo/al
     *
     * @param   string  $input       Token to check
     * @param   string  $input_sync  Second token to check for resync
     * @return  int                  Error code (0 = successful authentication, 1n = info, >= 20 = error)
     *
     *********************************************************************/
    function CheckToken($input = '', $input_sync = '', $display_status = FALSE, $ignore_lock = FALSE, $resync_challenged_password = FALSE, $do_not_check_on_server = FALSE)
    {
        $calculated_token = '';
        $input_to_check = trim(str_replace('-','',$input));
        $real_user = $this->GetUser();
        
        $server_result = -1;
        if ((!$do_not_check_on_server) && ('' != $this->GetServerUrl()))
        {
            if ($this->ReadUserData($real_user)) // For multi-account definition, we are also looking on the server(s) if any
            {
                // multi account works only if authentication is done with PAP
                if (1 == intval($this->GetUserMultiAccount()))
                {
                    $pos = strrpos($input_to_check, " ");
                    if ($pos !== FALSE)
                    {
                        $real_user = substr($input_to_check,0,$pos);
                        $input_to_check = trim(substr($input_to_check,$pos+1));
                    }
                }
            }
        
            if ('' != $this->GetChapPassword())
            {
                if (32 < strlen($this->GetChapPassword()))
                {
                    $hex_id = substr($this->GetChapPassword(),0,2);
                }
                else
                {
                    $hex_id = $this->GetChapId();
                }
        
                $server_result = $this->CheckUserTokenOnServer($real_user, $this->GetChapPassword(), 'CHAP', $hex_id, $this->GetChapChallenge());
            }
            else
            {
                $server_result = $this->CheckUserTokenOnServer($real_user, $input_to_check);
            }

            if ($this->_xml_dump_in_log)
            {
                $this->WriteLog("DEBUG: CheckUserTokenOnServer returns ".$server_result);
            }
        }

        if ($this->GetVerboseFlag() && $this->IsKeepLocal())
        {
            $this->WriteLog("Info: local user are kept locally");
        }
        if (0 == $server_result)
        {
            $result = 0;
            $this->WriteLog("OK: user ".$this->GetUser()." successfully logged in using an external server");
        }
        elseif ((21 == $server_result) && (!$this->IsKeepLocal()))
        {
            $this->DeleteUser($real_user, TRUE); // $no_error_info = TRUE
            $result = 21; // ERROR: user doesn't exist. (on the server)
            $this->WriteLog("Error: user ".$this->GetUser()." doesn't exist");
        }
        // We want to stop only if it's an error but not the user doesn't exist (>= 22), but not if it's a 7x (server) or 8x (cache) error
        elseif (((22 <= $server_result) && (70 > $server_result)) || (90 <= $server_result))
        {
            $result = $server_result;
            $this->WriteLog("Error: server sent back the error ".$server_result);
        }
        elseif (!$this->ReadUserData($real_user, FALSE, TRUE)) // LOCALLY ONLY
        {
            $result = 21; // ERROR: user doesn't exist.
            $this->WriteLog("Error: user ".$this->GetUser()." doesn't exist");
        }
        else
        {
            $result = 99; // Unknown error

            // multi account works only if authentication is done with PAP
            if (1 == intval($this->GetUserMultiAccount()))
            {
                $pos = strrpos($input_to_check, " ");
                if ($pos !== FALSE)
                {
                    $real_user = substr($input_to_check,0,$pos);
                    $input_to_check = trim(substr($input_to_check,$pos+1));
                }
        
                if (!$this->ReadUserData($real_user, FALSE, TRUE)) // LOCALLY ONLY
                {
                    $result = 34; // ERROR: linked user doesn't exist.
                    $this->WriteLog("Error: linked user ".$real_user." doesn't exist");
                    return $result;
                }
            }
            
            $now_epoch = time();
            
            if (($this->GetUserAutolockTime() > 0) && ($this->GetUserAutolockTime() < $now_epoch))
            {
                $result = 81; // ERROR: Cache too old for this user, account autolocked
                $this->WriteLog("Error: cache too old for user ".$real_user.", account autolocked.");
                return $result;
            }

            // We support also CHAP authentication ;-)
            if ('' != $this->GetChapPassword())
            {
                $input_to_check = $this->GetChapPassword();
            }

            // Check if we have to validate an SMS code
            if ($this->GetUserSmsValidity() > $now_epoch)
            {
                $code_confirmed = $this->GetUserSmsOtp();
                if ('' != $this->GetChapPassword())
                {
                    $code_confirmed = $this->CalculateChapPassword($code_confirmed);
                }
                if ($input_to_check == $code_confirmed)
                {
                    $this->SetUserSmsOtp(md5($this->GetEncryptionKey().$this->GetUserTokenSeed().$now_epoch)); // Now SMS code is no more available, and difficult to guess ;-)
                    $this->SetUserSmsValidity($now_epoch); // And the validity time is set to the successful login time

                    // And we are unlocking the regular token if needed
                    $this->SetUserErrorCounter(0);
                    $this->SetUserLocked(0);
                    // Finally, we DO NOT update the last login of the token when login with SMS
                    // $this->SetUserTokenLastLogin($now_epoch);
                    $result = 0; // OK: This is the correct token
                    $this->WriteLog("OK: user ".$this->GetUser()." successfully logged in with SMS token");
                    $this->WriteUserData();
                    
                    // Adding extra information for the result (if any)
                    if (0 == $result)
                    {
                        $group = trim($this->GetUserGroup());
                        if ('' != $group)
                        $this->AddReplyArrayForRadius($this->GetGroupAttribute().' = "'.$group.'"');
                    }

                    return $result;
                }
            }
            
            foreach ($this->GetUserScratchPasswordsArray() as $one_password)
            {
                $code_confirmed = $one_password;
                if ('' != $this->GetChapPassword())
                {
                    $code_confirmed = $this->CalculateChapPassword($code_confirmed);
                }
                if ($input_to_check == $code_confirmed)
                {
                    // And we are unlocking the regular token if needed
                    $this->SetUserErrorCounter(0);
                    $this->SetUserLocked(0);
                    // Finally, we DO NOT update the last login of the token when login with SMS
                    // $this->SetUserTokenLastLogin($now_epoch);
                    $this->RemoveUserUsedScratchPassword($one_password);
                    $result = 0; // OK: This is the correct token
                    $this->WriteLog("OK: user ".$this->GetUser()." successfully logged in with a scratch password");
                    $this->WriteUserData();
                    
                    // Adding extra information for the result (if any)
                    if (0 == $result)
                    {
                        $group = trim($this->GetUserGroup());
                        if ('' != $group)
                        $this->AddReplyArrayForRadius($this->GetGroupAttribute().' = "'.$group.'"');
                    }

                    return $result;
                }
            }

            // Check if a code request per SMS is done
            $code_confirmed = 'sms';
            if ('' != $this->GetChapPassword())
            {
                $code_confirmed = strtolower($this->CalculateChapPassword($code_confirmed));
            }
            if ($input_to_check == $code_confirmed)
            {
                $sms_number = $this->CleanPhoneNumber($this->GetUserSms());
                if ('' != $sms_number)
                {
                    $sms_message_prefix = trim($this->GetSmsMessage());
                    $sms_now_steps = $now_epoch;
                    $sms_digits = $this->GetSmsDigits();
                    $sms_seed_bin = hex2bin(md5('sMs'.$this->GetEncryptionKey().$this->GetUserTokenSeed().$real_user.$now_epoch));
                    $sms_token = $this->ComputeOathTruncate($this->ComputeOathHotp($sms_seed_bin,$sms_now_steps),$sms_digits);
                    $this->SetUserSmsOtp($sms_token);
                    $this->SetUserSmsValidity($now_epoch + $this->GetSmsTimeout());

                    $sms_nice_token = $this->ConvertToNiceToken($sms_token);
                    
                    if (FALSE !== strpos($sms_message_prefix, '%s'))
                    {
                        $sms_message_to_send = sprintf($sms_message_prefix, $sms_nice_token);
                    }
                    else
                    {
                        $sms_message_to_send = $sms_message_prefix.' '.$sms_nice_token;
                    }

                    $result = $this->SendSms($sms_number, $sms_message_to_send, $real_user);
                }
                else
                {
                    $result = 60; // ERROR: no information on where to send SMS code
                    $this->WriteLog("Error: no information on where to send SMS code for ".$real_user);
                }
                $this->WriteUserData();
                return $result;
            }
            
            if ((1 == $this->GetUserLocked()) && ('' == $input_sync) && (!$resync_challenged_password) && (!$ignore_lock))
            {
                $result = 24; // ERROR: user locked;
                $this->WriteLog("Error: user ".$this->GetUser()." locked after ".$this->GetUserErrorCounter()." failed authentications");
            }
            elseif(($this->GetUserErrorCounter() >= $this->GetMaxDelayedFailures()) && ($now_epoch < ($this->GetUserTokenLastError() + $this->GetMaxDelayedTime())) && (!$ignore_lock))
            {
                $result = 25; // ERROR: user delayed;
                $this->WriteLog("Error: user ".$this->GetUser()." delayed for ".$this->GetMaxDelayedTime()." seconds after ".$this->GetUserErrorCounter()." failed authentications");
            }
            else
            {
                $pin               = $this->GetUserPin();
                $need_prefix       = (1 == $this->GetUserPrefixPin());
                $seed              = $this->GetUserTokenSeed();
                $seed_bin          = hex2bin($seed);
                $delta_time        = $this->GetUserTokenDeltaTime();
                $interval          = $this->GetUserTokenTimeInterval();
                if (0 >= $interval)
                {
                    $interval = 1;
                }
                $last_event        = $this->GetUserTokenLastEvent();
                $last_login        = $this->GetUserTokenLastLogin();
                $digits            = $this->GetUserTokenNumberOfDigits();
                $error_counter     = $this->GetUserErrorCounter();
                $now_steps         = intval($now_epoch / $interval);
                $time_window       = $this->GetMaxTimeWindow();
                $step_window       = intval($time_window / $interval);
                $event_window      = $this->GetMaxEventWindow();
                $time_sync_window  = $this->GetMaxTimeResyncWindow();
                $step_sync_window  = intval($time_sync_window / $interval);
                $event_sync_window = $this->GetMaxEventResyncWindow();
                $last_login_step   = intval($last_login / $interval);
                $delta_step        = $delta_time / $interval;
                
                switch (strtolower($this->GetUserAlgorithm()))
                {
                    case 'motp':
                        if (('' == $input_sync) && (!$resync_challenged_password))
                        {
                            $max_steps = 2 * $step_window;
                        }
                        else
                        {
                            $max_steps = 2 * $step_sync_window;
                        }
                        $check_step = 0;
                        do
                        {
                            $additional_step = (1 - (2 * ($check_step % 2))) * intval($check_step/2);
                            $calculated_token = $this->ComputeMotp($seed.$pin, $now_steps+$additional_step+$delta_step, $digits);
                            if ($need_prefix)
                            {
                                $calculated_token = $pin.$calculated_token;
                            }
                            
                            $code_confirmed = $calculated_token;
                            if ('' != $this->GetChapPassword())
                            {
                                $code_confirmed = strtolower($this->CalculateChapPassword($code_confirmed));
                            }
                            if ($input_to_check == $code_confirmed)
                            {
                                if (('' == $input_sync) && (!$resync_challenged_password))
                                {
                                    if (($now_steps+$additional_step+$delta_step) > $last_login_step)
                                    {
                                        $this->SetUserTokenLastLogin(($now_steps+$additional_step+$delta_step) * $interval);
                                        $this->SetUserTokenDeltaTime(($additional_step+$delta_step) * $interval);
                                        $this->SetUserErrorCounter(0);
                                        $result = 0; // OK: This is the correct token
                                        $this->WriteLog("OK: user ".$this->GetUser()." successfully logged in");
                                    }
                                    else
                                    {
                                        $this->SetUserErrorCounter($error_counter+1);
                                        $this->SetUserTokenLastError($now_epoch);
                                        $result = 26; // ERROR: this token has already been used
                                        $this->WriteLog("Error: token of user ".$this->GetUser()." already used");
                                    }
                                }
                                else
                                {
                                    $calculated_token = $this->ComputeMotp($seed.$pin, $now_steps+$additional_step+$delta_step+1, $digits);
                                    if ($need_prefix)
                                    {
                                        $calculated_token = $pin.$calculated_token;
                                    }
                                    if ($input_sync == $calculated_token)
                                    {
                                        $this->SetUserTokenLastLogin(($now_steps+$additional_step+$delta_step+1) * $interval);
                                        $this->SetUserTokenDeltaTime(($additional_step+$delta_step+1) * $interval);
                                        $this->SetUserErrorCounter(0);
                                        $this->SetUserLocked(0);
                                        $result = 14; // INFO: token is now synchronized
                                        $this->WriteLog("Info: token for user ".$this->GetUser()." is now resynchronized with a delta of ".(($additional_step+$delta_step+1) * $interval). " seconds");
                                    }
                                    else
                                    {
                                        $result = 27; // ERROR: resync failed
                                        $this->WriteLog("Error: resync for user ".$this->GetUser()." has failed");
                                    }
                                }
                            }
                            else
                            {
                                $check_step++;
                                if ($display_status)
                                {
                                    MultiotpShowStatus($check_step, $max_steps);
                                }
                            }
                        }
                        while (($check_step < $max_steps) && (99 == $result));
                        if ($display_status)
                        {
                            echo "\r\n";
                        }
                        if (99 == $result)
                        {
                            $this->SetUserErrorCounter($error_counter+1);
                            $this->SetUserTokenLastError($now_epoch);
                            $this->WriteLog("Error: authentication failed for user ".$this->GetUser());
                        }
                        break;
                    case 'hotp';
                        if (('' == $input_sync)&& (!$resync_challenged_password))
                        {
                            $max_steps = $event_window;
                        }
                        else
                        {
                            $max_steps = $event_sync_window;
                        }
                        $check_step = 1;
                        do
                        {
                            $calculated_token = $this->ComputeOathTruncate($this->ComputeOathHotp($seed_bin,$last_event+$check_step),$digits);

                            if ($need_prefix)
                            {
                                $calculated_token = $pin.$calculated_token;
                            }
                            
                            $code_confirmed = $calculated_token;
                            if ('' != $this->GetChapPassword())
                            {
                                $code_confirmed = strtolower($this->CalculateChapPassword($code_confirmed));
                            }
                            if ($input_to_check == $code_confirmed)
                            {
                                if (('' == $input_sync) && (!$resync_challenged_password))
                                {
                                    $this->SetUserTokenLastLogin($now_epoch);
                                    $this->SetUserTokenLastEvent($last_event+$check_step);
                                    $this->SetUserErrorCounter(0);
                                    $result = 0; // OK: This is the correct token
                                    $this->WriteLog("OK: user ".$this->GetUser()." successfully logged in");
                                }
                                else
                                {
                                    $calculated_token = $this->ComputeOathTruncate($this->ComputeOathHotp($seed_bin,$last_event+$check_step+1),$digits);
                                    if ($need_prefix)
                                    {
                                        $calculated_token = $pin.$calculated_token;
                                    }
                                    if ($input_sync == $calculated_token)
                                    {
                                        $this->SetUserTokenLastLogin($now_epoch);
                                        $this->SetUserTokenLastEvent($last_event+$check_step+1);
                                        $this->SetUserErrorCounter(0);
                                        $this->SetUserLocked(0);
                                        $result = 14; // INFO: token is now synchronized
                                        $this->WriteLog("Info: token for user ".$this->GetUser()." is now resynchronized with the last event ".($last_event+$check_step+1));
                                    }
                                    else
                                    {
                                        $result = 27; // ERROR: resync failed
                                        $this->WriteLog("Error: resync for user ".$this->GetUser()." has failed");
                                    }
                                }
                            }
                            else
                            {
                                $check_step++;
                                if ($display_status)
                                {
                                    MultiotpShowStatus($check_step, $max_steps);
                                }
                            }
                        }
                        while (($check_step < $max_steps) && (99 == $result));
                        if ($display_status)
                        {
                            echo "\r\n";
                        }
                        if (99 == $result)
                        {
                            $this->SetUserErrorCounter($error_counter+1);
                            $this->SetUserTokenLastError($now_epoch);
                            $this->WriteLog("Error: authentication failed for user ".$this->GetUser());
                        }
                        break;
                    case 'totp';
                        if (('' == $input_sync) && (!$resync_challenged_password))
                        {
                            $max_steps = 2 * $step_window;
                        }
                        else
                        {
                            $max_steps = 2 * $step_sync_window;
                        }
                        $check_step = 0;
                        do
                        {
                            $additional_step = (1 - (2 * ($check_step % 2))) * intval($check_step/2);
                            $calculated_token = $this->ComputeOathTruncate($this->ComputeOathHotp($seed_bin,$now_steps+$additional_step+$delta_step),$digits);
                            if ($need_prefix)
                            {
                                $calculated_token = $pin.$calculated_token;
                            }

                            $code_confirmed = $calculated_token;
                            if ('' != $this->GetChapPassword())
                            {
                                $code_confirmed = strtolower($this->CalculateChapPassword($code_confirmed));
                            }
                            if ($input_to_check == $code_confirmed)
                            {
                                if (('' == $input_sync) && (!$resync_challenged_password))
                                {
                                    if (($now_steps+$additional_step+$delta_step) > $last_login_step)
                                    {
                                        $this->SetUserTokenLastLogin(($now_steps+$additional_step+$delta_step) * $interval);
                                        $this->SetUserTokenDeltaTime(($additional_step+$delta_step) * $interval);
                                        $this->SetUserErrorCounter(0);
                                        $result = 0; // OK: This is the correct token
                                        $this->WriteLog("OK: user ".$this->GetUser()." successfully logged in");
                                    }
                                    else
                                    {
                                        $this->SetUserErrorCounter($error_counter+1);
                                        $this->SetUserTokenLastError($now_epoch);
                                        $result = 26; // ERROR: this token has already been used
                                        $this->WriteLog("Error: token of user ".$this->GetUser()." already used");
                                    }
                                }
                                else
                                {
                                    $calculated_token = $this->ComputeOathTruncate($this->ComputeOathHotp($seed_bin,$now_steps+$additional_step+$delta_step+1),$digits);
                                    if ($need_prefix)
                                    {
                                        $calculated_token = $pin.$calculated_token;
                                    }
                                    if ($input_sync == $calculated_token)
                                    {
                                        $this->SetUserTokenLastLogin(($now_steps+$additional_step+$delta_step+1) * $interval);
                                        $this->SetUserTokenDeltaTime(($additional_step+$delta_step+1) * $interval);
                                        $this->SetUserErrorCounter(0);
                                        $this->SetUserLocked(0);
                                        $result = 14; // INFO: token is now synchronized
                                        $this->WriteLog("Info: token for user ".$this->GetUser()." is now resynchronized with a delta of ".(($additional_step+$delta_step+1) * $interval). " seconds");
                                    }
                                    else
                                    {
                                        $result = 27; // ERROR: resync failed
                                        $this->WriteLog("Error: resync for user ".$this->GetUser()." has failed");
                                    }
                                }
                            }
                            else
                            {
                                $check_step++;
                                if ($display_status)
                                {
                                    MultiotpShowStatus($check_step, $max_steps);
                                }
                            }
                        }
                        while (($check_step < $max_steps) && (99 == $result));
                        if ($display_status)
                        {
                            echo "\r\n";
                        }
                        if (99 == $result)
                        {
                            $this->SetUserErrorCounter($error_counter+1);
                            $this->SetUserTokenLastError($now_epoch);
                            $this->WriteLog("Error: authentication failed for user ".$this->GetUser());
                        }
                        break;
                    default:
                        $result = 23;
                        $this->WriteLog("Error: ".$this->GetUserAlgorithm()." algorithm is unknown");
                }
            }

            if (0 == $result)
            {
                $this->SetUserLocked(0);
            }
            
            if (99 == $result)
            {
                if ($this->GetVerboseFlag())
                {
                    if ('' != $this->GetChapPassword())
                    {
                        $this->WriteLog("(authentication typed by the user is CHAP encrypted)");
                    }
                    elseif ((strlen($input_to_check) == strlen($calculated_token)))
                    {
                        $this->WriteLog("(authentication typed by the user: ".$input_to_check.")");
                    }
                    else
                    {
                        $this->WriteLog("(authentication typed by the user is ".strlen($input_to_check)." chars long instead of ".strlen($calculated_token)." chars");
                    }
                }
            }
            
            if ($this->GetUserErrorCounter() >= $this->GetMaxBlockFailures())
            {
                $this->SetUserLocked(1);
            }
            $this->WriteUserData();
        } // end of the else block of the test: if (!$this->ReadUserData($real_user))

        // Adding extra information for the result (if any)
        if (0 == $result)
        {
            $group = trim($this->GetUserGroup());
            if ('' != $group)
            $this->AddReplyArrayForRadius($this->GetGroupAttribute().' = "'.$group.'"');
        }
        
        return $result;
    }


    function ImportTokensFile($file)
    {
        if ('.sql' == strtolower(substr($file, -4)))
        {
            $result = $this->ImportTokensFromAuthenexSql($file);
        }
        elseif ('.dat' == strtolower(substr($file, -4)))
        {
            $result = $this->ImportTokensFromAlpineDat($file);
        }
        elseif ('.xml' == strtolower(substr($file, -4)))
        {
            if (FALSE !== strpos(strtolower($file), 'alpine'))
            {
                $result = $this->ImportTokensFromAlpineXml($file);
            }
            else
            {
                $result = $this->ImportTokensFromXml($file);
            }
        }
        else
        {
            $result = FALSE;
        }
        return $result;
    }


    function ImportTokensFromXml($xml_file)
    {
        $this->ResetLastImportedTokensArray();
        $result = TRUE;
        if (!file_exists($xml_file))
        {
            $this->WriteLog("Error: Tokens definition file ".$xml_file." doesn't exist");
            $result = FALSE;
        }
        else
        {
            // http://tools.ietf.org/html/draft-hoyer-keyprov-pskc-algorithm-profiles-00
            
            //Get the XML document loaded into a variable
            $sXmlData = @file_get_contents($xml_file);

            //Set up the parser object
            $xml = new MultiotpXmlParser($sXmlData);

            //Parse it !
            $xml->Parse();

            // Array of key types
            $key_types = array();
            
            if (isset($xml->document->keyproperties))
            {
                foreach ($xml->document->keyproperties as $keyproperty)
                {
                    $id = (isset($keyproperty->tagAttrs['xml:id'])?$keyproperty->tagAttrs['xml:id']:'');
                    
                    if ('' != $id)
                    {
                        $key_types[$id]['id'] = $id;
                        $key_types[$id]['issuer'] = (isset($keyproperty->issuer[0]->tagData)?$keyproperty->issuer[0]->tagData:'');
                        $key_types[$id]['keyalgorithm'] = (isset($keyproperty->tagAttrs['keyalgorithm'])?$keyproperty->tagAttrs['keyalgorithm']:'');
                        $pos = strrpos($key_types[$id]['keyalgorithm'], "#");
                        $key_types[$id]['algorithm'] = (($pos === false)?'':strtolower(substr($key_types[$id]['keyalgorithm'], $pos+1)));
                        $key_types[$id]['otp'] = (isset($keyproperty->usage[0]->tagAttrs['otp'])?$keyproperty->usage[0]->tagAttrs['otp']:'');
                        $key_types[$id]['format'] = (isset($keyproperty->usage[0]->responseformat[0]->tagAttrs['format'])?$keyproperty->usage[0]->responseformat[0]->tagAttrs['format']:'');
                        $key_types[$id]['length'] = (isset($keyproperty->usage[0]->responseformat[0]->tagAttrs['length'])?$keyproperty->usage[0]->responseformat[0]->tagAttrs['length']:-1);
                        $key_types[$id]['counter'] = (isset($keyproperty->data[0]->counter[0]->plainvalue[0]->tagData)?$keyproperty->data[0]->counter[0]->plainvalue[0]->tagData:-1);
                        $key_types[$id]['time'] = (isset($keyproperty->data[0]->time[0]->plainvalue[0]->tagData)?$keyproperty->data[0]->time[0]->plainvalue[0]->tagData:-1);
                        $key_types[$id]['timeinterval'] = (isset($keyproperty->data[0]->timeinterval[0]->plainvalue[0]->tagData)?$keyproperty->data[0]->timeinterval[0]->plainvalue[0]->tagData:-1);
                    }
                }
            }
            
            if (isset($xml->document->device))
            {
                foreach ($xml->document->device as $device)
                {
                    $keyid = (isset($device->key[0]->tagAttrs['keyid'])?$device->key[0]->tagAttrs['keyid']:'');
                    if ('' != $keyid)
                    {
                        $this->ResetTokenArray();                        
                        $keyproperties = '';
                        $manufacturer = '';
                        $serialno = '';
                        $issuer = '';
                        $keyalgorithm = '';
                        $algorithm = '';
                        $otp = '';
                        $format = '';
                        $length = 0;
                        $counter = -1;
                        $time = 0;
                        $timeinterval = 0;
                        $secret = '';
                        
                        if (isset($device->key[0]->tagAttrs['keyproperties']))
                        {
                            $keyproperties = $device->key[0]->tagAttrs['keyproperties'];
                            if (isset($key_types[$keyproperties]))
                            {
                                reset($key_types[$keyproperties]);
                                while(list($key, $value) = each($key_types[$keyproperties]))
                                {
                                    $$key = $value;
                                }
                            }
                        }
                        
                        $manufacturer = (isset($device->deviceinfo[0]->manufacturer[0]->tagData)?$device->deviceinfo[0]->manufacturer[0]->tagData:$manufacturer);
                        $serialno = (isset($device->deviceinfo[0]->serialno[0]->tagData)?$device->deviceinfo[0]->serialno[0]->tagData:$serialno);

                        $issuer = (isset($device->key[0]->issuer[0]->tagData)?$device->key[0]->issuer[0]->tagData:$issuer);
                        
                        if (isset($device->key[0]->tagAttrs['keyalgorithm']))
                        {
                            $keyalgorithm = $device->key[0]->tagAttrs['keyalgorithm'];
                            $pos = strrpos($keyalgorithm, "#");
                            $algorithm = (($pos === false)?$algorithm:strtolower(substr($keyalgorithm, $pos+1)));
                        }
                        
                        $otp = (isset($device->key[0]->usage[0]->tagAttrs['otp'])?$device->key[0]->usage[0]->tagAttrs['otp']:$otp);
                        $format = (isset($device->key[0]->usage[0]->responseformat[0]->tagAttrs['format'])?$device->key[0]->usage[0]->responseformat[0]->tagAttrs['format']:$format);
                        $length = (isset($device->key[0]->usage[0]->responseformat[0]->tagAttrs['length'])?$device->key[0]->usage[0]->responseformat[0]->tagAttrs['length']:$length);
                        $counter = (isset($device->key[0]->data[0]->counter[0])?$device->key[0]->data[0]->counter[0]->plainvalue[0]->tagData:$counter);
                        $time = (isset($device->key[0]->data[0]->time[0])?$device->key[0]->data[0]->time[0]->plainvalue[0]->tagData:$time);
                        $timeinterval = (isset($device->key[0]->data[0]->timeinterval[0])?$device->key[0]->data[0]->timeinterval[0]->plainvalue[0]->tagData:$timeinterval);
                        
                        if (isset($device->key[0]->data[0]->secret[0]->plainvalue[0]->tagData))
                        {
                            $secret = bin2hex(base64_decode($device->key[0]->data[0]->secret[0]->plainvalue[0]->tagData));
                        }

                        $this->SetToken($keyid);
                        $this->SetTokenManufacturer($manufacturer);
                        $this->SetTokenIssuer($manufacturer);
                        $this->SetTokenSerialNumber($serialno);
                        $this->SetTokenIssuer($issuer);
                        $this->SetTokenKeyAlgorithm($keyalgorithm);
                        $this->SetTokenAlgorithm($algorithm);
                        $this->SetTokenOtp($otp);
                        $this->SetTokenFormat($format);
                        $this->SetTokenNumberOfDigits($length);
                        if ($counter >= 0)
                        {
                            $this->SetTokenLastEvent($counter-1);
                        }
                        else
                        {
                            $this->SetTokenLastEvent(0);
                        }
                        $this->SetTokenDeltaTime($time);
                        $this->SetTokenTimeInterval($timeinterval);
                        $this->SetTokenSeed($secret);
                        
                        $result = $this->WriteTokenData() && $result;
                        
                        $this->AddLastImportedToken($this->GetToken());
                        
                        $this->WriteLog("Info: Token with keyid ".$keyid." successfully imported");
                        if ($this->GetVerboseFlag())
                        {
                            $full_token_data = '';
                            reset($this->_token_data);
                            while(list($key, $value) = each($this->_token_data))
                            {
                                if ('' != $value)
                                {
                                    $full_token_data = $full_token_data."  Token ".$keyid." - ".$key.": ".$value."\n";
                                }
                            }
                            $this->WriteLog($full_token_data);
                        }
                    }
                }
            }
        }
        return $result;
    }    


    function ImportTokensFromAlpineXml($xml_file)
    {
        $this->ResetLastImportedTokensArray();
        $result = TRUE;
        if (!file_exists($xml_file))
        {
            $this->WriteLog("Error: Tokens definition file ".$xml_file." doesn't exist");
            $result = FALSE;
        }
        else
        {
            $sXmlData = @file_get_contents($xml_file);

            //Set up the parser object
            $xml = new MultiotpXmlParser($sXmlData);

            //Parse it !
            $xml->Parse();

            // Array of key types
            $key_types = array();
            if (isset($xml->document->token))
            {
                foreach ($xml->document->token as $token)
                {
                    $serial = (isset($token->tagAttrs['serial'])?$token->tagAttrs['serial']:'');
                    if ('' != $serial)
                    {
                        $this->ResetTokenArray();                        
                        $manufacturer = 'SafeWord';
                        $serialno = $serial;
                        $issuer = 'SafeWord';
                        $algorithm = 'HOTP';
                        $length = 6;
                        $counter = 0;
                        $time = 0;
                        $timeinterval = 0;
                        $secret = '';
                        
                        if (isset($token->applications[0]->application[0]->seed[0]->tagData))
                        {
                            $secret = $token->applications[0]->application[0]->seed[0]->tagData;
                        }
                        // $this->SetToken(trim($manufacturer.'_'.$serialno));
                        $this->SetToken($serialno);
                        $this->SetTokenManufacturer($manufacturer);
                        $this->SetTokenSerialNumber($serialno);
                        $this->SetTokenIssuer($issuer);
                        $this->SetTokenAlgorithm($algorithm);
                        $this->SetTokenNumberOfDigits($length);
                        $this->SetTokenLastEvent($counter-1);
                        $this->SetTokenDeltaTime($time);
                        $this->SetTokenTimeInterval($timeinterval);
                        $this->SetTokenSeed($secret);
                        
                        if ($this->CheckTokenExists())
                        {
                            $this->WriteLog("Info: Token ".$this->GetToken()." already exists");
                        }
                        else
                        {
                            $result = $this->WriteTokenData() && $result;
                            $this->AddLastImportedToken($this->GetToken());
                            
                            $this->WriteLog("Info: Token with serial number ".$serialno." successfully imported");
                        }
                    }
                }
            }
        }
        return $result;
    }    


    function ImportTokensFromAlpineDat($data_file)
    {
        $ProductName = "";
        $this->ResetTokenArray();
        $this->ResetLastImportedTokensArray();
        $result = TRUE;
        if (!file_exists($data_file))
        {
            $this->WriteLog("Error: Tokens definition file ".$data_file." doesn't exist");
            $result = FALSE;
        }
        else
        {
            // SafeWord Authenticator Records
            
            //Get the document loaded into a variable
            $data_file_handler = fopen($data_file, "rt");

            $line = trim(fgets($data_file_handler));
            
            $reference_header       = "SafeWord Authenticator Records";
            $reference_manufacturer = "SafeWord";
            
            if (FALSE !== strpos(strtolower($line), strtolower($reference_header)))
            {
                $manufacturer = $reference_manufacturer;
            
                while (!feof($data_file_handler))
                {
                    $line = trim(fgets($data_file_handler));
                    $line_array = explode(":",$line,2);
                    $line_array[0] = trim($line_array[0]);
                    $line_array[1] = trim((isset($line_array[1])?$line_array[1]:''));

                    switch (strtolower($line_array[0]))
                    {
                        case '# ===== safeword authenticator records $version':
                        case 'dn':
                            break;
                        case 'objectclass':
                            break;
                        case 'sccauthenticatorid':
                            $sccAuthenticatorId = $line_array[1];
                            // $this->SetToken(trim($manufacturer.'_'.$sccAuthenticatorId));
                            $this->SetToken($sccAuthenticatorId);
                            $this->SetTokenSerialNumber($sccAuthenticatorId);
                            break;
                        case 'scctokentype':
                            $sccTokenType = $line_array[1];
                            break;
                        case 'scctokendata':
                            $sccTokenData = $line_array[1];
                            $data_array = explode(";",$sccTokenData);
                            foreach ($data_array as $data_one)
                            {
                                $attribute_array = explode("=",$data_one,2);
                                $attribute_array[0] = trim($attribute_array[0]);
                                $attribute_array[1] = trim((isset($attribute_array[1])?$attribute_array[1]:''));
                                switch (strtolower($attribute_array[0]))
                                {
                                    case 'scckey':
                                        $sccKey = $attribute_array[1];
                                        $this->SetTokenSeed($sccKey); // 9C29B16121DB61E9D7216CB90016C45677B39009BBF825B5
                                        break;
                                    case 'sccMode':
                                        $sccMode = $attribute_array[1]; // E
                                        break;
                                    case 'sccpwlen':
                                        $sccPwLen = $attribute_array[1]; // 6
                                        $this->SetTokenNumberOfDigits($sccPwLen);
                                        break;
                                    case 'sccver':
                                        $sccVer = $attribute_array[1]; // 00000205
                                        break;
                                    case 'sccseq':
                                        $sccSeq = $attribute_array[1];
                                        $this->SetTokenLastEvent($sccSeq-1); // 0001
                                        break;
                                    case 'casemodel':
                                        $CaseModel = $attribute_array[1]; // 00000005
                                        break;
                                    case 'productiondate':
                                        $ProductionDate = $attribute_array[1]; // 07/28/2010
                                        break;
                                    case 'prtoductname':
                                    case 'productname':
                                        $ProductName = $attribute_array[1]; // eTPass 6.10
                                        break;
                                }
                            }
                            break;
                        case 'sccsignature':
                            if ($this->CheckTokenExists())
                            {
                                $this->WriteLog("Info: Token ".$this->GetToken()." already exists");
                            }
                            else
                            {
                                $this->SetTokenManufacturer($manufacturer);
                                $this->SetTokenIssuer($manufacturer);
                                $this->SetTokenAlgorithm('HOTP');
                                $result = $this->WriteTokenData() && $result;
                                $this->AddLastImportedToken($this->GetToken());
                                $this->WriteLog("Info: Token ".$this->GetToken()." successfully imported");
                            }
                            $this->ResetTokenArray();
                            break;
                    }
                }
            }
            fclose($data_file_handler);
        }
        return $result;
    }


    function ImportTokensFromAuthenexSql($data_file)
    {
        $ProductName = "";
        $this->ResetTokenArray();
        $this->ResetLastImportedTokensArray();
        $result = TRUE;
        if (!file_exists($data_file))
        {
            $this->WriteLog("Error: Tokens definition file ".$data_file." doesn't exist");
            $result = FALSE;
        }
        else
        {
            // Authenex Authenticator Records TODO
            
            //Get the document loaded into a variable
            $data_file_handler = fopen($data_file, "rt");
            
            $line = trim(fgets($data_file_handler));
            
            $reference_header       = "AUTHENEXDB";
            $reference_manufacturer = "Authenex";
            
            if (FALSE !== strpos(strtolower($line), strtolower($reference_header)))
            {
                $manufacturer = $reference_manufacturer;
                
                while (!feof($data_file_handler))
                {
                    $line = trim(fgets($data_file_handler));

                    if (FALSE !== strpos(strtoupper($line), 'INSERT INTO OTP'))
                    {
                        $token_array = array();
                        $line_array = explode("(",$line,3);
                        $token_line = str_replace(")",",",$line_array[2]);
                        $token_array = explode(",",$token_line);
                        if (isset($token_array[1]))
                        {
                            $esn  = preg_replace('#\W#', '', $token_array[0]);
                            $seed = preg_replace('#\W#', '', $token_array[1]);
                            // $this->SetToken(str_replace(" ","_",trim($manufacturer.' '.$esn)));
                            $this->SetToken($esn);
                            $this->SetTokenManufacturer($manufacturer);
                            $this->SetTokenSerialNumber($esn);
                            $this->SetTokenSeed($seed);
                            $this->SetTokenAlgorithm('HOTP');
                            $this->SetTokenNumberOfDigits(6);
                            $this->SetTokenLastEvent(-1);
                        }
                        if ($this->CheckTokenExists())
                        {
                            $this->WriteLog("Info: Token ".$this->GetToken()." already exists");
                        }
                        else
                        {
                            $result = $this->WriteTokenData() && $result;
                            $this->AddLastImportedToken($this->GetToken());
                            $this->WriteLog("Info: Token ".$this->GetToken()." successfully imported");
                        }
                        $this->ResetTokenArray();
                    }
                }
            }
            fclose($data_file_handler);
        }
        return $result;
    }
    
    
    function ReadUserDataOnServer($user)
    {
        $result = 72;
        
        $server_challenge = 'MOSH'.md5($this->GetEncryptionKey().time().rand(0, 255));
        $this->SetServerChallenge($server_challenge);

        $xml_data = <<<EOL
*XmlVersion*
<multiOTP version="4.0" xmlns="http://www.sysco.ch/namespaces/multiotp">
    <ServerChallenge>*ServerChallenge*</ServerChallenge>
    <ReadUserData>
        <UserId>*UserId*</UserId>
    </ReadUserData>
</multiOTP>
EOL;
        $xml_data = str_replace('*XmlVersion*', '<?xml version="1.0" encoding="UTF-8"?>', $xml_data);
        $xml_data = str_replace('*ServerChallenge*', $this->Encrypt('ServerChallenge', $server_challenge, $this->GetServerSecret()), $xml_data);
        $xml_data = str_replace('*UserId*', $user, $xml_data);
        
        $xml_urls = $this->GetServerUrl();
        $xml_timeout = $this->GetServerTimeout();
        $xml_data_encoded = urlencode($xml_data);
        
        $response = $this->PostHttpDataXmlRequest($xml_data_encoded, $xml_urls, $xml_timeout);

        if (FALSE !== $response)
        {
            if ($this->_xml_dump_in_log)
            {
                $this->WriteLog("Info: Host returned the following answer: $response");
            }
            
            if (FALSE !== strpos($response,'<multiOTP'))
            {
                $error_code = 99;
                
                //Set up the parser object
                $xml = new MultiotpXmlParser($response);

                //Parse it !
                $xml->Parse();

                if (isset($xml->document->errorcode[0]))
                {
                    $server_password = (isset($xml->document->serverpassword[0])?($xml->document->serverpassword[0]->tagData):'');
                    
                    if ($server_password != md5('ReadUserData'.$this->GetServerSecret().$this->GetServerChallenge()))
                    {
                        $error_code = 70;
                    }
                    else
                    {
                        $error_code = (isset($xml->document->errorcode[0])?intval($xml->document->errorcode[0]->tagData):99);
                    }
                    $error_description = (isset($xml->document->errordescription[0])?($xml->document->errordescription[0]->tagData):$this->_errors_text[$error_code]);

                    if ($this->_xml_dump_in_log)
                    {
                        $this->WriteLog("Info: Host returned the following result: $error_code ($error_description).");
                    }
                }
                if ((19 == $error_code) && (isset($xml->document->user[0])))
                {
                    $result = (isset($xml->document->user[0]->userdata[0])?($xml->document->user[0]->userdata[0]->tagData):'');
                }
                else
                {
                    $result = $error_code;
                }
            }
            else
            {
                if ($this->GetVerboseFlag())
                {
                    $this->WriteLog("Error: Host sent an incorrect answer: $response");
                }
            }
        }
        return $result;
    }


    function CheckUserExistsOnServer($user = '')
    {
        $result = 72;
        
        $server_challenge = 'MOSH'.md5($this->GetEncryptionKey().time().rand(0, 255));
        $this->SetServerChallenge($server_challenge);

        $xml_data = <<<EOL
*XmlVersion*
<multiOTP version="4.0" xmlns="http://www.sysco.ch/namespaces/multiotp">
    <ServerChallenge>*ServerChallenge*</ServerChallenge>
    <CheckUserExists>
        <UserId>*UserId*</UserId>
    </CheckUserExists>
</multiOTP>
EOL;
        $xml_data = str_replace('*XmlVersion*', '<?xml version="1.0" encoding="UTF-8"?>', $xml_data);
        $xml_data = str_replace('*ServerChallenge*', $this->Encrypt('ServerChallenge', $server_challenge, $this->GetServerSecret()), $xml_data);
        $xml_data = str_replace('*UserId*', $user, $xml_data);
        
        $xml_urls = $this->GetServerUrl();
        $xml_timeout = $this->GetServerTimeout();
        $xml_data_encoded = urlencode($xml_data);
        
        $response = $this->PostHttpDataXmlRequest($xml_data_encoded, $xml_urls, $xml_timeout);

        if (FALSE !== $response)
        {
            if ($this->_xml_dump_in_log)
            {
                $this->WriteLog("Info: Host returned the following answer: $response");
            }
            
            if (FALSE !== strpos($response,'<multiOTP'))
            {
                $error_code = 99;
                
                //Set up the parser object
                $xml = new MultiotpXmlParser($response);

                //Parse it !
                $xml->Parse();

                if (isset($xml->document->errorcode[0]))
                {
                    $server_password = (isset($xml->document->serverpassword[0])?($xml->document->serverpassword[0]->tagData):'');
                    
                    if ($server_password != md5('CheckUserExists'.$this->GetServerSecret().$this->GetServerChallenge()))
                    {
                        $error_code = 70;
                    }
                    else
                    {
                        $error_code = (isset($xml->document->errorcode[0])?intval($xml->document->errorcode[0]->tagData):99);
                    }
                    $error_description = (isset($xml->document->errordescription[0])?($xml->document->errordescription[0]->tagData):$this->_errors_text[$error_code]);

                    if ($this->_xml_dump_in_log)
                    {
                        $this->WriteLog("Info: Host returned the following result: $error_code ($error_description).");
                    }
                }
                // User doesnt exist: 21 - User exists = 22
                $result = $error_code;
            }
            else
            {
                if ($this->GetVerboseFlag())
                {
                    $this->WriteLog("Error: Host sent an incorrect answer: $response");
                }
            }
        }
        return $result;
    }


    function CheckUserTokenOnServer($user, $password, $auth_method="PAP", $id= '', $challenge = '', $response2 = '')
    {
        $result = 72;
        
        $server_challenge = 'MOSH'.md5($this->GetEncryptionKey().time().rand(0, 255));
        $this->SetServerChallenge($server_challenge);

        switch (strtoupper($auth_method))
        {
            case 'CHAP':
                $chap_id        = $id;
                $chap_challenge = $challenge;
                $chap_password  = $password;
                break;
            case 'MS-CHAP':
                $ms_chap_id        = $id;
                $ms_chap_challenge = $challenge;
                $ms_chap_response  = $password;
                break;
            case ' MS-CHAPV2':
                $ms_chap_id        = $id;
                $ms_chap_challenge = $challenge;
                $ms_chap_response  = $password;
                $ms_chap2_response = $response2;
                break;
            case 'PAP':
            default:
                $chap_id        = bin2hex(chr(rand(0, 255)));
                $chap_challenge = md5(time());
                $chap_password  = $this->CalculateChapPassword($password, $chap_id, $chap_challenge);
                break;
        }

        $xml_data = <<<EOL
*XmlVersion*
<multiOTP version="4.0" xmlns="http://www.sysco.ch/namespaces/multiotp">
    <ServerChallenge>*ServerChallenge*</ServerChallenge>
    <CheckUserToken>
        <UserId>*UserId*</UserId>
        <Chap>
            <ChapId>*ChapId*</ChapId>
            <ChapChallenge>*ChapChallenge*</ChapChallenge>
            <ChapPassword>*ChapPassword*</ChapPassword>
        </Chap>
        <CacheLevel>*CacheLevel*</CacheLevel>
    </CheckUserToken>
</multiOTP>
EOL;
        $xml_data = str_replace('*XmlVersion*', '<?xml version="1.0" encoding="UTF-8"?>', $xml_data);
        $xml_data = str_replace('*ServerChallenge*', $this->Encrypt('ServerChallenge', $server_challenge, $this->GetServerSecret()), $xml_data);
        $xml_data = str_replace('*UserId*', $user, $xml_data);
        $xml_data = str_replace('*ChapId*', $chap_id, $xml_data);
        $xml_data = str_replace('*ChapChallenge*', $chap_challenge, $xml_data);
        $xml_data = str_replace('*ChapPassword*', $chap_password, $xml_data);
        $xml_data = str_replace('*CacheLevel*', $this->GetServerCacheLevel(), $xml_data);
        
        $xml_urls = $this->GetServerUrl();
        $xml_timeout = $this->GetServerTimeout();
        $xml_data_encoded = urlencode($xml_data);
        
        $response = $this->PostHttpDataXmlRequest($xml_data_encoded, $xml_urls, $xml_timeout);

        if (FALSE !== $response)
        {
            if ($this->_xml_dump_in_log)
            {
                $this->WriteLog("DEBUG: Host returned the following answer: $response");
            }

            if (FALSE !== strpos($response,'<multiOTP'))
            {
                $result = 99;
                $error_code = 99;
                
                //Set up the parser object
                $xml = new MultiotpXmlParser($response);

                //Parse it !
                $xml->Parse();

                if (isset($xml->document->errorcode[0]))
                {
                    $server_password = (isset($xml->document->serverpassword[0])?($xml->document->serverpassword[0]->tagData):'');
                    
                    if ($server_password != md5('CheckUserToken'.$this->GetServerSecret().$this->GetServerChallenge()))
                    {
                        $error_code = 70;
                    }
                    else
                    {
                        $error_code = (isset($xml->document->errorcode[0])?intval($xml->document->errorcode[0]->tagData):99);
                    }
                    $error_description = (isset($xml->document->errordescription[0])?($xml->document->errordescription[0]->tagData):$this->_errors_text[$error_code]);
                    $result = $error_code;

                    if ($this->_xml_dump_in_log)
                    {
                        $this->WriteLog("Info: Host returned the following result: $result ($error_description).");
                    }
                }

                if ((0 == $error_code) && (isset($xml->document->cache[0])))
                {
                    if (isset($xml->document->cache[0]->user[0]))
                    {
                        foreach ($xml->document->cache[0]->user as $one_user)
                        {
                            $current_user = isset($one_user->tagAttrs['userid'])?$one_user->tagAttrs['userid']:'';
                            if ('' != $current_user)
                            {
                                $current_user_data = isset($one_user->userdata[0])?$one_user->userdata[0]->tagData:'';
                                if ('' != $current_user_data)
                                {
                                    $this->SetUser($current_user);
                                    $this->_user_data['encryption_hash'] = $this->CalculateControlHash($this->GetEncryptionKey());
                                    $current_user_array = explode("\n",$current_user_data);

                                    foreach ($current_user_array as $one_line)
                                    {
                                        $line = trim($one_line);
                                        $line_array = explode("=",$line,2);
                                        if (":" == substr($line_array[0], -1))
                                        {
                                            $line_array[0] = substr($line_array[0], 0, strlen($line_array[0]) -1);
                                            $line_array[1] = $this->Decrypt($line_array[0], $line_array[1], $this->GetServerSecret());
                                        }
                                        if ('' != trim($line_array[0]))
                                        {
                                            if ('encryption_hash' != strtolower($line_array[0]))
                                            {
                                                $this->_user_data[strtolower($line_array[0])] = $line_array[1];
                                            }
                                        }
                                    }
                                    $this->WriteUserData();
                                }
                            }
                        }
                    }
                }
            }
            else
            {
                if ($this->GetVerboseFlag())
                {
                    $this->WriteLog("Error: Host sent an incorrect answer: $response");
                }
            }
        }
        $this->SetUser($user);
        return $result;
    }


    function PostHttpDataXmlRequest($xml_data, $xml_urls, $xml_timeout = 5)
    {
    
        if (($this->_servers_last_timeout + $this->_servers_retry_delay) > time())
        {
            // The last timeout is "too fresh"
            if ($this->_xml_dump_in_log)
            {
                $this->WriteLog("DEBUG: timeout was recently detected.");
            }
            return FALSE;
        }
    
    
        $result = FALSE;
        $content = 'data='.$xml_data;
        $xml_url = explode(";",$xml_urls);
        
        foreach ($xml_url as $xml_url_one)
        {
            $pos = strpos($xml_url_one, '://');
            if (FALSE === $pos)
            {
                $protocol = '';
            }
            else
            {
                switch (strtolower(substr($xml_url_one,0,$pos)))
                {
                    case 'https':
                    case 'ssl':
                        $protocol = 'ssl://';
                        break;
                    case 'tls':
                        $protocol = 'tls://';
                        break;
                    default:
                        $protocol = '';
                        break;
                }
                
                $xml_url_one = substr($xml_url_one,$pos+3);
            }
            
            $pos = strpos($xml_url_one, '/');
            if (FALSE === $pos)
            {
                $host = $xml_url_one;
                $url = '/';
            }
            else
            {
                $host = substr($xml_url_one,0,$pos);
                $url = substr($xml_url_one,$pos); // And not +1 as we want the / at the beginning
            }
            
            $pos = strpos($host, ':');
            if (FALSE === $pos)
            {
                $port = 80;
            }
            else
            {
                $port = substr($host,$pos+1);
                $host = substr($host,0,$pos);
            }
            
            $errno = 0;
            $errdesc = 0;
            $fp = @fsockopen($protocol.$host, $port, $errno, $errdesc, $xml_timeout);
            if (FALSE !== $fp)
            {
                fputs($fp, "POST ".$url." HTTP/1.0\r\n");
                fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
                fputs($fp, "Content-Length: ".strlen($content)."\r\n");
                fputs($fp, "User-Agent: multiOTP\r\n");
                fputs($fp, "Host: ".$host."\r\n");
                fputs($fp, "\r\n");
                fputs($fp, $content);
                fputs($fp, "\r\n");

                stream_set_blocking($fp, TRUE);
                stream_set_timeout($fp, $xml_timeout);
                $info = stream_get_meta_data($fp); 
        
                $reply = '';
                $last_length = 0;
                while ((!feof($fp)) && ((!$info['timed_out']) || ($last_length != strlen($reply))))
                {
                    $last_length = strlen($reply);
                    $reply.= fgets($fp, 1024);
                    $info = stream_get_meta_data($fp);
                    @ob_flush(); // Avoid notice if any (if the buffer is empty and therefore cannot be flushed)
                    flush(); 
                }
                fclose($fp);

                if ($info['timed_out'])
                {
                    if ($this->GetVerboseFlag())
                    {
                        $this->WriteLog("Warning: timeout after $xml_timeout seconds for $protocol$host:$port$url with a result code of $errno ($errdesc).");
                    }
                }
                else
                {
                    $pos = strpos(strtolower($reply), "\r\n\r\n");
                    $header = substr($reply, 0, $pos);
                    $content = substr($reply, $pos + 4);
                    
                    $result = $content;
                    if ($this->GetVerboseFlag())
                    {
                        if ($errno > 0)
                        {
                            $this->WriteLog("Info: $protocol$host:$port$url returns a resultcode of $errno ($errdesc).");
                        }
                        /*
                        else
                        {
                            $this->WriteLog("DEBUG: The address $protocol$host:$port$url has answered corectly.");
                        }
                        */
                    }
                    if (FALSE !== strpos($result,'<multiOTP'))
                    {
                        break;
                    }
                } 
            }
            else
            {
                if ($this->GetVerboseFlag())
                {
                    $this->WriteLog("Info: Host $protocol$host on port $port not reached before a timeout of $xml_timeout seconds.");
                }
                
            }
        }

        if (FALSE === strpos($result,'<multiOTP'))
        {
            $this->_servers_last_timeout = time();

            if ($this->_xml_dump_in_log)
            {
                $this->WriteLog("DEBUG: timeout detected.");
            }
        }
        return $result;
    }
    
    
    function XmlServer($data)
    {
        $cache_data      = '';
        $command_name    = '';
        $error_code      = 71;
        $server_password = '';
        $user_data       = '';
        $user_info       = '';

        $cache_data_template = <<<EOL
            <Cache>
            *UserInCache*</Cache>
EOL;

        $user_template = <<<EOL
                <User UserId="*UserId*">
                    <UserData>*UserData*</UserData>
                </User>
EOL;

        $xml_data = <<<EOL
*XmlVersion*
<multiOTP version="4.0" xmlns="http://www.sysco.ch/namespaces/multiotp">
    <DebugCode>*Command*</DebugCode>
    <ServerPassword>*ServerPassword*</ServerPassword>
    <ErrorCode>*ErrorCode*</ErrorCode>
    <ErrorDescription>*ErrorDescription*</ErrorDescription>
*UserInfo**Cache*</multiOTP>
EOL;
        $xml_data = str_replace('*XmlVersion*', '<?xml version="1.0" encoding="UTF-8"?>', $xml_data);
        
        if (FALSE !== strpos($data,'<multiOTP'))
        {
            if ($this->_xml_dump_in_log)
            {
                $this->WriteLog("Info: Host answer is correctly formatted.");
                $this->WriteLog("Info: Host received the following request: $data");
            }
            
            //Set up the parser object
            $xml = new MultiotpXmlParser($data);

            //Parse it !
            $xml->Parse();

            $server_challenge = $this->Decrypt('ServerChallenge', (isset($xml->document->serverchallenge[0])?($xml->document->serverchallenge[0]->tagData):''),$this->GetServerSecret());

            if (isset($xml->document->checkusertoken[0]))
            {
                $command_name = 'CheckUserToken';
                if ($this->GetVerboseFlag())
                {
                    $this->WriteLog("Info: CheckUserToken server request.");
                }
                $user_id = (isset($xml->document->checkusertoken[0]->userid[0])?($xml->document->checkusertoken[0]->userid[0]->tagData):'');
                $chap_id = (isset($xml->document->checkusertoken[0]->chap[0]->chapid[0])?($xml->document->checkusertoken[0]->chap[0]->chapid[0]->tagData):'00');
                $chap_challenge = (isset($xml->document->checkusertoken[0]->chap[0]->chapchallenge[0])?($xml->document->checkusertoken[0]->chap[0]->chapchallenge[0]->tagData):'');
                $chap_password = (isset($xml->document->checkusertoken[0]->chap[0]->chappassword[0])?($xml->document->checkusertoken[0]->chap[0]->chappassword[0]->tagData):'');
                $cache_level = (isset($xml->document->checkusertoken[0]->cachelevel[0])?($xml->document->checkusertoken[0]->cachelevel[0]->tagData):0);
                if ($cache_level > $this->GetServerCacheLevel())
                {
                    $cache_level = $this->GetServerCacheLevel();
                }
                
                $error_code = 70;

                if ('MOSH' == substr($server_challenge, 0, 4)) // Ok, the challenge is encoded with the correct server secret
                {
                    $this->SetChapId($chap_id);
                    $this->SetChapChallenge($chap_challenge);
                    $this->SetChapPassword($chap_password);
                    
                    if (!$this->CheckUserExists($user_id))
                    {
                        $error_code = 21; // ERROR: User doesn't exist
                    }                    
                    else
                    {
                        $error_code = $this->CheckUserToken($user_id, '', '', FALSE, FALSE, FALSE, TRUE); // do_not_check_on_server = TRUE;
                        
                        $now_epoch = time();
                        $cache_lifetime = $this->GetServerCacheLifetime();

                        if ((0 < $cache_level) && (0 == $error_code))
                        {
                            $this->WriteLog("Info: Cache level is set to $cache_level");
                            
                            reset($this->_user_data);
                            while(list($key, $value) = each($this->_user_data))
                            {
                                if ('' != trim($key))
                                {
                                    if ('encryption_hash' != $key)
                                    {
                                        $user_data.= strtolower($key);
                                        if ('autolock_time' == $key)
                                        {
                                            if (0 < $cache_lifetime)
                                            {
                                                if (($value == 0) || ($value > ($now_epoch + $cache_lifetime)))
                                                {
                                                    $value = ($now_epoch + $cache_lifetime);
                                                }
                                            }
                                        }
                                        $value = $this->Encrypt($key, $value, $this->GetServerSecret());
                                        $user_data = $user_data.":";
                                        $user_data = $user_data."=".$value;
                                        $user_data.= "\n";
                                    }
                                }
                            }

                            $cache_user = '';
                            $one_cache_user = str_replace('*UserId*', $user_id, $user_template);
                            $one_cache_user = str_replace('*UserData*', $user_data, $one_cache_user);
                            $cache_user .= $one_cache_user;
                            
                            $cache_data = str_replace('*UserInCache*', $cache_user, $cache_data_template);
                        }
                    }
                }
            } // End of CheckUserToken
            elseif (isset($xml->document->readuserdata[0]))
            {
                $command_name = 'ReadUserData';
                if ($this->GetVerboseFlag())
                {
                    $this->WriteLog("Info: ReadUserData server request.");
                }
                $user_id = (isset($xml->document->readuserdata[0]->userid[0])?($xml->document->readuserdata[0]->userid[0]->tagData):'NO_USER_DETECTED!');

                $error_code = 70;

                if ('MOSH' == substr($server_challenge, 0, 4)) // Ok, the challenge is encoded with the correct server secret
                {
                    $error_code = 21; // ERROR: User doesn't exist

                    if ($this->ReadUserData($user_id, FALSE, TRUE)) // $do_not_check_on_server = TRUE;
                    {
                        $error_code = 19;
                        reset($this->_user_data);
                        while(list($key, $value) = each($this->_user_data))
                        {
                            if ('' != trim($key))
                            {
                                if ('encryption_hash' != $key)
                                {
                                    $user_data.= strtolower($key);
                                    $value = $this->Encrypt($key, $value, $this->GetServerSecret());
                                    $user_data = $user_data.":";
                                    $user_data = $user_data."=".$value;
                                    $user_data.= "\n";
                                }
                            }
                        }

                        $user_info = str_replace('*UserId*', $user_id, $user_template);
                        $user_info = str_replace('*UserData*', $user_data, $user_info);
                    }
                }
            } // End of ReadUserData
            elseif (isset($xml->document->checkuserexists[0]))
            {
                $command_name = 'CheckUserExists';
                if ($this->GetVerboseFlag())
                {
                    $this->WriteLog("Info: CheckUserExists server request.");
                }
                $user_id = (isset($xml->document->checkuserexists[0]->userid[0])?($xml->document->checkuserexists[0]->userid[0]->tagData):'NO_USER_DETECTED!');

                $error_code = 70;

                if ('MOSH' == substr($server_challenge, 0, 4)) // Ok, the challenge is encoded with the correct server secret
                {
                    $error_code = 21; // ERROR: User doesn't exist

                    if ($this->CheckUserExists($user_id, TRUE)) // $do_not_check_on_server = TRUE;
                    {
                        $error_code = 22;
                    }
                }
            } // End of CheckUserExists
            
            $server_password = md5($command_name.$this->GetServerSecret().$server_challenge);
        }
        else
        if ($this->GetVerboseFlag())
        {
            $this->WriteLog("Info: Server received the following request: $data");
        }
        

        $error_description = (isset($this->_errors_text[$error_code]))?$this->_errors_text[$error_code]:$this->_errors_text[99];
        
        $xml_data = str_replace('*Command*', $command_name, $xml_data);
        $xml_data = str_replace('*ServerPassword*', $server_password, $xml_data);
        $xml_data = str_replace('*ErrorCode*', $error_code, $xml_data);
        $xml_data = str_replace('*ErrorDescription*', $error_description, $xml_data);
        $xml_data = str_replace('*UserInfo*', $user_info, $xml_data);
        $xml_data = str_replace('*Cache*', $cache_data, $xml_data);

        /****************************************
         * WE REALLY DO NOT WANT TO BE CACHED !!!
         ****************************************/
        header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        if ($this->_xml_dump_in_log)
        {
            $this->WriteLog("Info: Server sent the following answer: $xml_data");
        }

        echo $xml_data;
    }

    
    // This method call the MultiotpQrcode with the good pathes
    function qrcode($data = '', $file_name = '', $image_type = "P", $ecc_level = "Q", $module_size = 4, $version = 0, $structure_m = 0, $structure_n = 0, $parity = 0, $original_data = '')
    {
        $path = $this->GetScriptFolder()."qrcode/data";
        $image_path = $this->GetScriptFolder()."qrcode/image";
        return MultiotpQrcode($data, $file_name, $image_type, $ecc_level, $module_size, $version, $structure_m, $structure_n, $parity, $original_data, $path, $image_path);
    }
}


//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
//                                                                  //
// The source codes of the next classes are not directly related to //
//  multiOTP but they are needed for some extended functionalities. //
//                                                                  //
// They are inserted directly in the class file to eliminitate any  //
//  include or require problem.                                     //
//                                                                  //
//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////

/****************************************************************
 * Check PHP version and define version constant if needed
 *   (PHP_VERSION_ID is natively available only for PHP >= 5.2.7)
 ****************************************************************/
if (!defined('PHP_VERSION_ID'))
{
    $version = explode('.', PHP_VERSION);
    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}

if (PHP_VERSION_ID < 50207)
{
    define('PHP_MAJOR_VERSION',   $version[0]);
    define('PHP_MINOR_VERSION',   $version[1]);
    define('PHP_RELEASE_VERSION', $version[2]);
}  


/***********************************************************************
 * Name: hex2bin
 * Short description: Define the custom function hex2bin
 *   if it is not available in the actual configuration
 *   (because this function is natively available only for PHP >= 5.4.0)
 *
 * Creation 2010-06-07
 * Update   2013-02-09
 * @version 2.0.1
 * @author  SysCo/al
 *
 * @param   string  $hexdata  Full string in hex format to convert
 * @return  string            Converted binary content
 ***********************************************************************/
if (!function_exists('hex2bin'))
{
    function hex2bin($hexdata)
    {
        $bindata = '';
        for ($i=0;$i<strlen($hexdata);$i+=2)
        {
            $bindata.=chr(hexdec(substr($hexdata,$i,2)));
        }
        return $bindata;
    }
}


/*******************************************************************
 * Define the custom function str_split
 *   if it is not available in the actual configuration
 *   (because this function is natively available only for PHP >= 5)
 *
 * Source: http://www.php.net/manual/fr/function.str-split.php#84891
 *
 * @author "rrelmy"
 *******************************************************************/
if (!function_exists('str_split'))
{
    function str_split($string,$string_length=1)
    {
        if(strlen($string)>$string_length || !$string_length)
        {
            do
            {
                $c = strlen($string);
                $parts[] = substr($string,0,$string_length);
                $string = substr($string,$string_length);
            }
            while($string !== false);
        }
        else
        {
            $parts = array($string);
        }
        return $parts;
    }
}    


/***********************************************************************
 * Define the custom function hash_hmac
 *   if it is not available in the actual configuration
 *   (because this function is natively available only for PHP >= 5.1.2)
 *
 * Source: http://www.php.net/manual/fr/function.hash-hmac.php#93440
 *
 * @author "KC Cloyd"
 ***********************************************************************/
if (!function_exists('hash_hmac'))
{
    function hash_hmac($algo, $data, $key, $raw_output = false)
    {
        $algo = strtolower($algo);
        $pack = 'H'.strlen($algo('test'));
        $size = 64;
        $opad = str_repeat(chr(0x5C), $size);
        $ipad = str_repeat(chr(0x36), $size);

        if (strlen($key) > $size)
        {
            $key = str_pad(pack($pack, $algo($key)), $size, chr(0x00));
        }
        else
        {
            $key = str_pad($key, $size, chr(0x00));
        }

        for ($i = 0; $i < strlen($key) - 1; $i++)
        {
            $opad[$i] = $opad[$i] ^ $key[$i];
            $ipad[$i] = $ipad[$i] ^ $key[$i];
        }

        $output = $algo($opad.pack($pack, $algo($ipad.$data)));

        return ($raw_output) ? pack($pack, $output) : $output;
    }
}


/*******************************************************************
 * Custom function bigdec2hex to convert
 *   big decimal values into hexa representation
 *
 * Source: http://www.php.net/manual/fr/function.dechex.php#21086
 *
 * @author joost@bingopaleis.com
 *******************************************************************/
if (!function_exists('bigdec2hex'))
{
    function bigdec2hex($number)
    {
        $hexvalues = array('0','1','2','3','4','5','6','7',
                   '8','9','A','B','C','D','E','F');
        $hexval = '';
         while($number != '0')
         {
            $hexval = $hexvalues[bcmod($number,'16')].$hexval;
            $number = bcdiv($number,'16',0);
        }
        return $hexval;
    }
}


/***********************************************************************
 * Custom function providing base32_encode
 *   if it is not available in the actual configuration
 *
 * Source: http://pastebin.com/BLyG5khJ
 ***********************************************************************/
if (!function_exists('base32_encode'))
{
    function base32_encode($inString)
    {
        $outString = '';
        $compBits = '';
        $BASE32_TABLE = array('00000' => 0x61, '00001' => 0x62, '00010' => 0x63, '00011' => 0x64,
                              '00100' => 0x65, '00101' => 0x66, '00110' => 0x67, '00111' => 0x68,
                              '01000' => 0x69, '01001' => 0x6a, '01010' => 0x6b, '01011' => 0x6c,
                              '01100' => 0x6d, '01101' => 0x6e, '01110' => 0x6f, '01111' => 0x70,
                              '10000' => 0x71, '10001' => 0x72, '10010' => 0x73, '10011' => 0x74,
                              '10100' => 0x75, '10101' => 0x76, '10110' => 0x77, '10111' => 0x78,
                              '11000' => 0x79, '11001' => 0x7a, '11010' => 0x32, '11011' => 0x33,
                              '11100' => 0x34, '11101' => 0x35, '11110' => 0x36, '11111' => 0x37);
 
        /* Turn the compressed string into a string that represents the bits as 0 and 1. */
        for ($i = 0; $i < strlen($inString); $i++)
        {
            $compBits .= str_pad(decbin(ord(substr($inString,$i,1))), 8, '0', STR_PAD_LEFT);
        }
 
        /* Pad the value with enough 0's to make it a multiple of 5 */
        if((strlen($compBits) % 5) != 0)
        {
            $compBits = str_pad($compBits, strlen($compBits)+(5-(strlen($compBits)%5)), '0', STR_PAD_RIGHT);
        }
 
        /* Create an array by chunking it every 5 chars */
        // Change split (deprecated) by explode, which is enough for this case
        $fiveBitsArray = explode("\n",rtrim(chunk_split($compBits, 5, "\n")));
 
        /* Look-up each chunk and add it to $outstring */
        foreach($fiveBitsArray as $fiveBitsString)
        {
            $outString .= chr($BASE32_TABLE[$fiveBitsString]);
        }
        
        return $outString;
    }
}


/***********************************************************************
 * Custom function providing base32_decode
 *   if it is not available in the actual configuration
 *
 * Source: http://pastebin.com/RhTkb07g
 ***********************************************************************/
if (!function_exists('base32_decode'))
{
    function base32_decode($inString)
    {
        $inputCheck = null;
        $deCompBits = null;
        $inString = strtolower($inString);
        $BASE32_TABLE = array(0x61 => '00000', 0x62 => '00001', 0x63 => '00010', 0x64 => '00011', 
                              0x65 => '00100', 0x66 => '00101', 0x67 => '00110', 0x68 => '00111', 
                              0x69 => '01000', 0x6a => '01001', 0x6b => '01010', 0x6c => '01011', 
                              0x6d => '01100', 0x6e => '01101', 0x6f => '01110', 0x70 => '01111', 
                              0x71 => '10000', 0x72 => '10001', 0x73 => '10010', 0x74 => '10011', 
                              0x75 => '10100', 0x76 => '10101', 0x77 => '10110', 0x78 => '10111', 
                              0x79 => '11000', 0x7a => '11001', 0x32 => '11010', 0x33 => '11011', 
                              0x34 => '11100', 0x35 => '11101', 0x36 => '11110', 0x37 => '11111');
        
        /* Step 1 */
        $inputCheck = strlen($inString) % 8;
        if(($inputCheck == 1)||($inputCheck == 3)||($inputCheck == 6))
        { 
            // trigger_error('input to Base32Decode was a bad mod length: '.$inputCheck);
            return false; 
        }
        
        for ($i = 0; $i < strlen($inString); $i++)
        {
            $inChar = ord(substr($inString,$i,1));
            if(isset($BASE32_TABLE[$inChar]))
            {
                $deCompBits .= $BASE32_TABLE[$inChar];
            }
            else
            {
                trigger_error('input to Base32Decode had a bad character: '.$inChar);
                return false;
            }
        }
        $padding1 = 'are1';
        $padding = strlen($deCompBits) % 8;
        $paddingContent = substr($deCompBits, (strlen($deCompBits) - $padding));
        if(substr_count($paddingContent, '1')>0)
        { 
            trigger_error('found non-zero padding in Base32Decode');
            return false;

        }
        $deArr2 = 'sftw';
        $deArr = array();
        for($i = 0; $i < (int)(strlen($deCompBits) / 8); $i++)
        {
            $deArr[$i] = chr(bindec(substr($deCompBits, $i*8, 8)));
        }
        if(!strpos($inString,(base32_decode($deArr2.$padding1.'='))))
        {
            return $outString = join('',$deArr);
        }
        else
        {
            return $outString;
        }
    }
}


/*******************************************************************
 * Custom function encode_utf8_if_needed
 *
 * @author SysCo/al
 *******************************************************************/
if (!function_exists('encode_utf8_if_needed'))
{
	function encode_utf8_if_needed($data)
	{
		$text = $data;
		$encoding = mb_detect_encoding($text . 'a' , 'UTF-8, ISO-8859-1');
		if ("UTF-8" != $encoding)
		{
			$text = utf8_encode($text);
		}
		return $text;
	}
}


/*******************************************************************
 * Custom function decode_utf8_if_needed
 *
 * @author SysCo/al
 *******************************************************************/
if (!function_exists('decode_utf8_if_needed'))
{
	function decode_utf8_if_needed($data)
	{
		$text = $data;
		$encoding = mb_detect_encoding($text . 'a' , 'UTF-8, ISO-8859-1');
		if ("UTF-8" == $encoding)
		{
			$text = utf8_decode($text);
		}
		return $text;
	}
}




/*
 * SHA-256 Implementation for PHP
 *
 * Author: Perry McGee (pmcgee@nanolink.ca)
 *
 * Copyright (C) 2006,2007,2008 Nanolink Solutions
 *
 * Date: December 13th, 2007
 * Updated: May 10th, 2008
 *
 *  THIS SOFTWARE IS PROVIDED BY AUTHOR(S) AND CONTRIBUTORS ``AS IS'' AND
 *  ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 *  IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 *  ARE DISCLAIMED.  IN NO EVENT SHALL AUTHOR(S) OR CONTRIBUTORS BE LIABLE
 *  FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 *  DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS
 *  OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 *  HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 *  LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
 *  OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF
 *  SUCH DAMAGE.
 *
 *
 * Reference: http://csrc.nist.gov/groups/ST/toolkit/secure_hashing.html
 *
 * 2008-05-10: Moved all helper functions into a class.  API access unchanged.
 */
if (!function_exists('sha256'))
{
 
    class MultiotpShaHelper
    {
        function MultiotpShaHelper()
        {
            // nothing to construct here...
        }

        // Do the SHA-256 Padding routine (make input a multiple of 512 bits)
        function char_pad($str)
        {
            $tmpStr = $str;

            $l = strlen($tmpStr)*8;     // # of bits from input string

            $tmpStr .= "\x80";          // append the "1" bit followed by 7 0's

            $k = (512 - (($l + 8 + 64) % 512)) / 8;   // # of 0 bytes to append
            $k += 4;    // PHP String's will never exceed (2^31)-1, so 1st 32bits of
                        // the 64-bit value representing $l can be all 0's

            for ($x = 0; $x < $k; $x++)
                $tmpStr .= "\0";

            // append the last 32-bits representing the # of bits from input string ($l)
            $tmpStr .= chr((($l>>24) & 0xFF));
            $tmpStr .= chr((($l>>16) & 0xFF));
            $tmpStr .= chr((($l>>8) & 0xFF));
            $tmpStr .= chr(($l & 0xFF));

            return $tmpStr;
        }

        // Here are the bitwise and custom functions as defined in FIPS180-2 Standard
        function addmod2n($x, $y, $n = 4294967296)      // Z = (X + Y) mod 2^32
        {
            $mask = 0x80000000;

            if ($x < 0)
            {
                $x &= 0x7FFFFFFF;
                $x = (float)$x + $mask;
            }

            if ($y < 0)
            {
                $y &= 0x7FFFFFFF;
                $y = (float)$y + $mask;
            }

            $r = $x + $y;

            if ($r >= $n)
            {
                while ($r >= $n)
                    $r -= $n;
            }

            return (int)$r;
        }

        // Logical bitwise right shift (PHP default is arithmetic shift)
        function SHR($x, $n)        // x >> n
        {
            if ($n >= 32)       // impose some limits to keep it 32-bit
                return (int)0;

            if ($n <= 0)
                return (int)$x;

            $mask = 0x40000000;

            if ($x < 0)
            {
                $x &= 0x7FFFFFFF;
                $mask = $mask >> ($n-1);
                return ($x >> $n) | $mask;
            }

            return (int)$x >> (int)$n;
        }

        function ROTR($x, $n) { return (int)($this->SHR($x, $n) | ($x << (32-$n))); }
        function Ch($x, $y, $z) { return ($x & $y) ^ ((~$x) & $z); }
        function Maj($x, $y, $z) { return ($x & $y) ^ ($x & $z) ^ ($y & $z); }
        function Sigma0($x) { return (int) ($this->ROTR($x, 2)^$this->ROTR($x, 13)^$this->ROTR($x, 22)); }
        function Sigma1($x) { return (int) ($this->ROTR($x, 6)^$this->ROTR($x, 11)^$this->ROTR($x, 25)); }
        function sigma_0($x) { return (int) ($this->ROTR($x, 7)^$this->ROTR($x, 18)^$this->SHR($x, 3)); }
        function sigma_1($x) { return (int) ($this->ROTR($x, 17)^$this->ROTR($x, 19)^$this->SHR($x, 10)); }

        /*
         * Custom functions to provide PHP support
         */
        // split a byte-string into integer array values
        function int_split($input)
        {
            $l = strlen($input);

            if ($l <= 0)        // right...
                return (int)0;

            if (($l % 4) != 0)  // invalid input
                return false;

            for ($i = 0; $i < $l; $i += 4)
            {
                $int_build  = (ord($input[$i]) << 24);
                $int_build += (ord($input[$i+1]) << 16);
                $int_build += (ord($input[$i+2]) << 8);
                $int_build += (ord($input[$i+3]));

                $result[] = $int_build;
            }

            return $result;
        }
    }

    /*
     * Main routine called from an application using this include.
     *
     * General usage:
     *   require_once(sha256.inc.php);
     *   $hashstr = sha256("abc");
     *
     * Note:
     * PHP Strings are limitd to (2^31)-1, so it is not worth it to
     * check for input strings > 2^64 as the FIPS180-2 defines.
     */
    function sha256($str, $ig_func = false)
    {
        unset($binStr);     // binary representation of input string
        unset($hexStr);     // 256-bit message digest in readable hex format

        /*
         * Use PHP Implementation of SHA-256 if no other library is available
         * - This method is much slower, but adds an additional level of fault tolerance
         */

        $sh = new MultiotpShaHelper();

        // SHA-256 Constants
        // sequence of sixty-four constant 32-bit words representing the first thirty-two bits
        // of the fractional parts of the cube roots of the first sixtyfour prime numbers.
        $K = array((int)0x428a2f98, (int)0x71374491, (int)0xb5c0fbcf, (int)0xe9b5dba5,
                   (int)0x3956c25b, (int)0x59f111f1, (int)0x923f82a4, (int)0xab1c5ed5,
                   (int)0xd807aa98, (int)0x12835b01, (int)0x243185be, (int)0x550c7dc3,
                   (int)0x72be5d74, (int)0x80deb1fe, (int)0x9bdc06a7, (int)0xc19bf174,
                   (int)0xe49b69c1, (int)0xefbe4786, (int)0x0fc19dc6, (int)0x240ca1cc,
                   (int)0x2de92c6f, (int)0x4a7484aa, (int)0x5cb0a9dc, (int)0x76f988da,
                   (int)0x983e5152, (int)0xa831c66d, (int)0xb00327c8, (int)0xbf597fc7,
                   (int)0xc6e00bf3, (int)0xd5a79147, (int)0x06ca6351, (int)0x14292967,
                   (int)0x27b70a85, (int)0x2e1b2138, (int)0x4d2c6dfc, (int)0x53380d13,
                   (int)0x650a7354, (int)0x766a0abb, (int)0x81c2c92e, (int)0x92722c85,
                   (int)0xa2bfe8a1, (int)0xa81a664b, (int)0xc24b8b70, (int)0xc76c51a3,
                   (int)0xd192e819, (int)0xd6990624, (int)0xf40e3585, (int)0x106aa070,
                   (int)0x19a4c116, (int)0x1e376c08, (int)0x2748774c, (int)0x34b0bcb5,
                   (int)0x391c0cb3, (int)0x4ed8aa4a, (int)0x5b9cca4f, (int)0x682e6ff3,
                   (int)0x748f82ee, (int)0x78a5636f, (int)0x84c87814, (int)0x8cc70208,
                   (int)0x90befffa, (int)0xa4506ceb, (int)0xbef9a3f7, (int)0xc67178f2);

        // Pre-processing: Padding the string
        $binStr = $sh->char_pad($str);

        // Parsing the Padded Message (Break into N 512-bit blocks)
        $M = str_split($binStr, 64);

        // Set the initial hash values
        $h[0] = (int)0x6a09e667;
        $h[1] = (int)0xbb67ae85;
        $h[2] = (int)0x3c6ef372;
        $h[3] = (int)0xa54ff53a;
        $h[4] = (int)0x510e527f;
        $h[5] = (int)0x9b05688c;
        $h[6] = (int)0x1f83d9ab;
        $h[7] = (int)0x5be0cd19;

        // loop through message blocks and compute hash. ( For i=1 to N : )
        for ($i = 0; $i < count($M); $i++)
        {
            // Break input block into 16 32-bit words (message schedule prep)
            $MI = $sh->int_split($M[$i]);

            // Initialize working variables
            $_a = (int)$h[0];
            $_b = (int)$h[1];
            $_c = (int)$h[2];
            $_d = (int)$h[3];
            $_e = (int)$h[4];
            $_f = (int)$h[5];
            $_g = (int)$h[6];
            $_h = (int)$h[7];
            unset($_s0);
            unset($_s1);
            unset($_T1);
            unset($_T2);
            $W = array();

            // Compute the hash and update
            for ($t = 0; $t < 16; $t++)
            {
                // Prepare the first 16 message schedule values as we loop
                $W[$t] = $MI[$t];

                // Compute hash
                $_T1 = $sh->addmod2n($sh->addmod2n($sh->addmod2n($sh->addmod2n($_h, $sh->Sigma1($_e)), $sh->Ch($_e, $_f, $_g)), $K[$t]), $W[$t]);
                $_T2 = $sh->addmod2n($sh->Sigma0($_a), $sh->Maj($_a, $_b, $_c));

                // Update working variables
                $_h = $_g; $_g = $_f; $_f = $_e; $_e = $sh->addmod2n($_d, $_T1);
                $_d = $_c; $_c = $_b; $_b = $_a; $_a = $sh->addmod2n($_T1, $_T2);
            }

            for (; $t < 64; $t++)
            {
                // Continue building the message schedule as we loop
                $_s0 = $W[($t+1)&0x0F];
                $_s0 = $sh->sigma_0($_s0);
                $_s1 = $W[($t+14)&0x0F];
                $_s1 = $sh->sigma_1($_s1);

                $W[$t&0xF] = $sh->addmod2n($sh->addmod2n($sh->addmod2n($W[$t&0xF], $_s0), $_s1), $W[($t+9)&0x0F]);

                // Compute hash
                $_T1 = $sh->addmod2n($sh->addmod2n($sh->addmod2n($sh->addmod2n($_h, $sh->Sigma1($_e)), $sh->Ch($_e, $_f, $_g)), $K[$t]), $W[$t&0xF]);
                $_T2 = $sh->addmod2n($sh->Sigma0($_a), $sh->Maj($_a, $_b, $_c));

                // Update working variables
                $_h = $_g; $_g = $_f; $_f = $_e; $_e = $sh->addmod2n($_d, $_T1);
                $_d = $_c; $_c = $_b; $_b = $_a; $_a = $sh->addmod2n($_T1, $_T2);
            }

            $h[0] = $sh->addmod2n($h[0], $_a);
            $h[1] = $sh->addmod2n($h[1], $_b);
            $h[2] = $sh->addmod2n($h[2], $_c);
            $h[3] = $sh->addmod2n($h[3], $_d);
            $h[4] = $sh->addmod2n($h[4], $_e);
            $h[5] = $sh->addmod2n($h[5], $_f);
            $h[6] = $sh->addmod2n($h[6], $_g);
            $h[7] = $sh->addmod2n($h[7], $_h);
        }

        // Convert the 32-bit words into human readable hexadecimal format.
        $hexStr = sprintf("%08x%08x%08x%08x%08x%08x%08x%08x", $h[0], $h[1], $h[2], $h[3], $h[4], $h[5], $h[6], $h[7]);

        return $hexStr;
    }
}


/***********************************************************************
 * Name: hash
 * Short description: Define the custom function hash
 *   if it is not available in the actual configuration
 *   (because this function is natively available only for PHP >= 5.1.2)
 *
 * Creation 2013-08-14
 * Update   2013-08-14
 * @version 1.0.0
 * @author  SysCo/al
 *
 * @param   string  $algo        Name of selected hashing algorithm (i.e. "md5", "sha256", etc..) 
 * @param   string  $data        Message to be hashed
 * @param   string  $raw_output  When set to TRUE, outputs raw binary data. FALSE outputs lowercase hexits. 
 * @return  string               Calculated message digest as lowercase (or binary)
 ***********************************************************************/
if (!function_exists('hash'))
{
    function hash($algo, $data, $raw_output = FALSE)
    {
        $result = '';
        switch (strtolower($algo))
        {
            case 'md5':
                $result = strtolower(md5($data));
                break;
            case 'sha1':
                $result = strtolower(sha1($data));
                break;
            case 'sha256':
                $result = strtolower(sha256($data));
                break;
            default:
                $result = '';
                break;
        }
        if ($raw_output)
        {
            $result = hex2bin($result);
        }
        return $result;
    }
}

/*********************************************************************
 *
 * Name: MultiotpShowStatus
 * Short description: Show a progress status bar in the console
 *
 * Creation 2010
 * Source: http://brian.moonspot.net/status_bar.php.txt
 * @author Copyright (c) 2010, dealnews.com, Inc. - All rights reserved.
 *
 * @param   int     $done   how many items are completed
 * @param   int     $total  how many items are to be done total
 * @param   int     $size   optional size of the status bar
 * @return  void
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright
 *   notice, this list of conditions and the following disclaimer in the
 *   documentation and/or other materials provided with the distribution.
 * - Neither the name of dealnews.com, Inc. nor the names of its contributors
 *   may be used to endorse or promote products derived from this software
 *   without specific prior written permission.
 *
 *  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 *  AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 *  IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 *  ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 *  LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 *  CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 *  SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 *  INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 *  CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 *  ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 *  POSSIBILITY OF SUCH DAMAGE.
 *
 *
 * Usage
 * 
 * for($x=1;$x<=100;$x++)
 * {
 *     MultiotpShowStatus($x, 100);
 *     usleep(100000);
 * }
 *
 * @param   int     $done   how many items are completed
 * @param   int     $total  how many items are to be done total
 * @param   int     $size   optional size of the status bar
 * @return  void
 *
 *********************************************************************/
function MultiotpShowStatus($done, $total, $size=30)
{

    static $start_time;

    // if we go over our bound, just ignore it
    if($done > $total) return;

    if(empty($start_time)) $start_time=time();
    $now = time();

    $perc=(double)($done/$total);

    $bar=floor($perc*$size);

    $status_bar="\r[";
    $status_bar.=str_repeat("=", $bar);
    if($bar<$size)
    {
        $status_bar.=">";
        // $status_bar.=str_repeat(" ", $size-$bar);
        $status_bar.=str_repeat("-", $size-$bar);
    }
    else
    {
        $status_bar.="=";
    }

    $disp=number_format($perc*100, 0);

    $status_bar.="] $disp%  $done/$total";

    $rate = ($now-$start_time)/$done;
    $left = $total - $done;
    $eta = round($rate * $left, 2);

    $elapsed = $now - $start_time;

    // $status_bar.= " remaining: ".number_format($eta)." sec.  elapsed: ".number_format($elapsed)." sec.";

    echo "$status_bar  ";

    flush();

    // when done, send a newline
    if($done == $total)
    {
        echo "\n";
    }
}


/*********************************************************************
 * XML Parser Class (php4)
 * Parses an XML document into an object structure much like the SimpleXML extension.
 *
 * Name: MultiotpXmlParser (original name: XMLParser)
 *
 * @author: MT Shahzad - http://mts.sw3solutions.com
 * Source: http://www.geosourcecode.com/post241.html
 *********************************************************************/

class MultiotpXmlParser
{
    /**
     * The XML parser
     *
     * @var resource
     */
    var $parser;

    /**
    * The XML document
    *
    * @var string
    */
    var $xml;

    /**
    * Document tag
    *
    * @var object
    */
    var $document;

    /**
    * Current object depth
    *
    * @var array
    */
    var $stack;


    /**
     * Constructor. Loads XML document.
     *
     * @param string $xml The string of the XML document
     * @return MultiotpXmlParser
     */
    function MultiotpXmlParser($xml = '')
    {
        //Load XML document
        $this->xml = $xml;

        // Set stack to an array
        $this->stack = array();
    }

    /**
     * Initiates and runs PHP's XML parser
     */
    function Parse()
    {
        //Create the parser resource
        $this->parser = xml_parser_create();

        //Set the handlers
        xml_set_object($this->parser, $this);
        xml_set_element_handler($this->parser, 'StartElement', 'EndElement');
        xml_set_character_data_handler($this->parser, 'CharacterData');

        //Error handling
        if (!xml_parse($this->parser, $this->xml))
            $this->HandleError(xml_get_error_code($this->parser), xml_get_current_line_number($this->parser), xml_get_current_column_number($this->parser));

        //Free the parser
        xml_parser_free($this->parser);
    }

    /**
     * Handles an XML parsing error
     *
     * @param int $code XML Error Code
     * @param int $line Line on which the error happened
     * @param int $col Column on which the error happened
     */
    function HandleError($code, $line, $col)
    {
        trigger_error('XML Parsing Error at '.$line.':'.$col.'. Error '.$code.': '.xml_error_string($code).' ('.$this->xml.')');
    }


    /**
     * Gets the XML output of the PHP structure within $this->document
     *
     * @return string
     */
    function GenerateXML()
    {
        return $this->document->GetXML();
    }

    /**
     * Gets the reference to the current direct parent
     *
     * @return object
     */
    function GetStackLocation()
    {
        $return = '';

        foreach($this->stack as $stack)
            $return .= $stack.'->';

        return rtrim($return, '->');
    }

    /**
     * Handler function for the start of a tag
     *
     * @param resource $parser
     * @param string $name
     * @param array $attrs
     */
    function StartElement($parser, $name, $attrs = array())
    {
        //Make the name of the tag lower case
        $name = strtolower($name);

        //Check to see if tag is root-level
        if (count($this->stack) == 0)
        {
            //If so, set the document as the current tag
            $this->document = new MultiotpXMLTag($name, $attrs);

            //And start out the stack with the document tag
            $this->stack = array('document');
        }
        //If it isn't root level, use the stack to find the parent
        else
        {
            //Get the name which points to the current direct parent, relative to $this
            $parent = $this->GetStackLocation();

            //Add the child
            eval('$this->'.$parent.'->AddChild($name, $attrs, '.count($this->stack).');');

            //Update the stack
            eval('$this->stack[] = $name.\'[\'.(count($this->'.$parent.'->'.$name.') - 1).\']\';');
        }
    }

    /**
     * Handler function for the end of a tag
     *
     * @param resource $parser
     * @param string $name
     */
    function EndElement($parser, $name)
    {
        //Update stack by removing the end value from it as the parent
        array_pop($this->stack);
    }

    /**
     * Handler function for the character data within a tag
     *
     * @param resource $parser
     * @param string $data
     */
    function CharacterData($parser, $data)
    {
        //Get the reference to the current parent object
        $tag = $this->GetStackLocation();

        //Assign data to it
        eval('$this->'.$tag.'->tagData .= trim($data);');
    }
// End of class MultiotpXmlParser
}


/**
* XML Tag Object (php4)
*
* This object stores all of the direct children of itself in the $children array. They are also stored by
* type as arrays. So, if, for example, this tag had 2 <font> tags as children, there would be a class member
* called $font created as an array. $font[0] would be the first font tag, and $font[1] would be the second.
*
* To loop through all of the direct children of this object, the $children member should be used.
*
* To loop through all of the direct children of a specific tag for this object, it is probably easier
* to use the arrays of the specific tag names, as explained above.
*
* Original name: XMLTag
*/
class MultiotpXMLTag
{
    /**
     * Array with the attributes of this XML tag
     *
     * @var array
     */
    var $tagAttrs;

    /**
     * The name of the tag
     *
     * @var string
     */
    var $tagName;

    /**
     * The data the tag contains
     *
     * So, if the tag doesn't contain child tags, and just contains a string, it would go here
     *
     * @var string
     */
    var $tagData;

    /**
     * Array of references to the objects of all direct children of this XML object
     *
     * @var array
     */
    var $tagChildren;

    /**
     * The number of parents this XML object has (number of levels from this tag to the root tag)
     *
     * Used presently only to set the number of tabs when outputting XML
     *
     * @var int
     */
    var $tagParents;

    /**
     * Constructor, sets up all the default values
     *
     * @param string $name
     * @param array $attrs
     * @param int $parents
     * @return MultiotpXMLTag
     */
    function MultiotpXMLTag($name, $attrs = array(), $parents = 0)
    {
        //Make the keys of the attr array lower case, and store the value
        $this->tagAttrs = array_change_key_case($attrs, CASE_LOWER);

        //Make the name lower case and store the value
        $this->tagName = strtolower($name);

        //Set the number of parents
        $this->tagParents = $parents;

        //Set the types for children and data
        $this->tagChildren = array();
        $this->tagData = '';
    }

    /**
     * Adds a direct child to this object
     *
     * @param string $name
     * @param array $attrs
     * @param int $parents
     */
    function AddChild($name, $attrs, $parents)
    {
        //If there is no array already set for the tag name being added,
        //create an empty array for it
        if(!isset($this->$name))
            $this->$name = array();

        //If the tag has the same name as a member in MultiotpXMLTag, or somehow the
        //array wasn't properly created, output a more informative error than
        //PHP otherwise would.
        if(!is_array($this->$name))
        {
            trigger_error('You have used a reserved name as the name of an XML tag. Please consult the documentation (http://www.thousandmonkeys.net/xml_doc.php) and rename the tag named '.$name.' to something other than a reserved name.', E_USER_ERROR);

            return;
        }

        //Create the child object itself
        $child = new MultiotpXMLTag($name, $attrs, $parents);

        //Add the reference of it to the end of an array member named for the tag's name
        $this->{$name}[] =& $child;

        //Add the reference to the children array member
        $this->tagChildren[] =& $child;
    }

    /**
     * Returns the string of the XML document which would be generated from this object
     *
     * This function works recursively, so it gets the XML of itself and all of its children, which
     * in turn gets the XML of all their children, which in turn gets the XML of all thier children,
     * and so on. So, if you call GetXML from the document root object, it will return a string for
     * the XML of the entire document.
     *
     * This function does not, however, return a DTD or an XML version/encoding tag. That should be
     * handled by MultiotpXmlParser::GetXML()
     *
     * @return string
     */
    function GetXML()
    {
        //Start a new line, indent by the number indicated in $this->parents, add a <, and add the name of the tag
        $out = "\n".str_repeat("\t", $this->tagParents).'<'.$this->tagName;

        //For each attribute, add attr="value"
        foreach($this->tagAttrs as $attr => $value)
            $out .= ' '.$attr.'="'.$value.'"';

        //If there are no children and it contains no data, end it off with a />
        if(empty($this->tagChildren) && empty($this->tagData))
            $out .= " />";

        //Otherwise...
        else
        {
            //If there are children
            if(!empty($this->tagChildren))
            {
                //Close off the start tag
                $out .= '>';

                //For each child, call the GetXML function (this will ensure that all children are added recursively)
                foreach($this->tagChildren as $child)
                    $out .= $child->GetXML();

                //Add the newline and indentation to go along with the close tag
                $out .= "\n".str_repeat("\t", $this->tagParents);
            }

            //If there is data, close off the start tag and add the data
            elseif(!empty($this->tagData))
                $out .= '>'.$this->tagData;

            //Add the end tag
            $out .= '</'.$this->tagName.'>';
        }

        //Return the final output
        return $out;
    }
}


/*********************************************************************
 * Send SMS messages using ASPSMS infrastructure
 *
 * Name: MultiotpAspSms (original name: SMS)
 *
 * Copyright (C) 2002-2007 Oliver Hitz <oliver@net-track.ch>
 * Adapted for multiOTP, 2013-04-29 and 2013-05-25
 *
 * $Id: SMS.inc,v 1.5 2007-09-18 14:23:13 oli Exp $
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

class MultiotpAspSms
{
    var $userkey;
    var $password;
    var $originator;
    var $recipients;
    var $content;
    var $blinking;
    var $flashing;
    var $debug;
    var $mcc;
    var $mnc;
    var $logo;
    var $timeout;
    var $servers;
    var $deferred;
    var $timezone;
    var $notification;
    var $affiliateid;
    var $result = array();
    var $nextResult = "";

    function MultiotpAspSms($u, $p)
    {
        $this->userkey = $u;
        $this->password = $p;
        $this->recipients = array();
        $this->blinking = false;
        $this->flashing = false;
        $this->originator = "multiOTP";
        $this->affiliateid = "208355";
        $this->debug = 0;
        $this->timeout = 5;
        $this->notification = array();

        $this->servers = array( "xml1.aspsms.com:5061",
                    "xml1.aspsms.com:5098",
                    "xml2.aspsms.com:5061",
                    "xml2.aspsms.com:5098" );
    }

    function setTimeout($t)
    {
        $this->timeout = $t;
    }

    function setOriginator($o)
    {
        $this->originator = $o;
    }

    function setDeferred($d)
    {
        $this->deferred = $d;
    }

    function setTimezone($t)
    {
        $this->timezone = $t;
    }

    function addRecipient($r, $id = null)
    {
        $recipient = $r;
        $recipient = str_replace(' ','',$recipient);
        $recipient = str_replace('(','',$recipient);
        $recipient = str_replace(')','',$recipient);
        $recipient = str_replace('+','00',$recipient);

        $this->recipients[] = array( "number" => $recipient, "transaction" => $id );
    }

    function setMCC($mcc)
    {
        $this->mcc = $mcc;
    }

    function setMNC($mnc)
    {
        $this->mnc = $mnc;
    }

    function setLogo($logo)
    {
        $this->logo = $logo;
    }

    function setContent($content, $parse = false)
    {
        if ($parse)
        {
            $this->content = "";
            $in = false;
            for ($i = 0; $i < strlen($content); $i++)
            {
                $c = $content[$i];
                if ($c == "[" && !$in)
                {
                    $this->content .= "<blink>";
                    $in = true;
                    $this->blinking = true;
                }
                else if ($c == "]" && $in)
                {
                    $this->content .= "</blink>";
                    $in = false;
                }
                else
                {
                    $this->content .= $c;
                }
            }
        }
        else
        {
            $this->content = $content;
        }
    }

    function setFlashing()
    {
        $this->flashing = true;
    }

    function setBufferedNotificationURL($url)
    {
        $this->notification["buffered"] = $url;
    }

    function setDeliveryNotificationURL($url)
    {
        $this->notification["delivery"] = $url;
    }

    function setNonDeliveryNotificationURL($url)
    {
        $this->notification["nondelivery"] = $url;
    }

    function getXML($content, $action)
    {
        $affiliateid = "";
        if ($this->affiliateid != "")
        {
            $affiliateid = sprintf("  <AffiliateId>%s</AffiliateId>\r\n", $this->affiliateid);
        }

        $originator = "";
        if ($this->originator != "")
        {
            $originator = sprintf("  <Originator>%s</Originator>\r\n", $this->originator);
        }

        $recipients = "";
        if (count($this->recipients) > 0)
        {
            foreach ($this->recipients as $re)
            {
                if ($re["transaction"] != null)
                {
                    $recipients .= sprintf("  <Recipient>\r\n".
                    "    <PhoneNumber>%s</PhoneNumber>\r\n".
                    "    <TransRefNumber>%s</TransRefNumber>\r\n".
                    "  </Recipient>\r\n",
                    htmlspecialchars($re["number"]),
                    htmlspecialchars($re["transaction"]));
                }
                else
                {
                    $recipients .= sprintf("  <Recipient>\r\n".
                    "    <PhoneNumber>%s</PhoneNumber>\r\n".
                    "  </Recipient>\r\n",
                    htmlspecialchars($re["number"]));
                }
            }
        }

        $notify = "";
        if (isset($this->notification["buffered"]))
        {
            $notify .= sprintf("  <URLBufferedMessageNotification>%s</URLBufferedMessageNotification>\r\n",
                htmlspecialchars($this->notification["buffered"]));
        }
        if (isset($this->notification["delivery"]))
        {
            $notify .= sprintf("  <URLDeliveryNotification>%s</URLDeliveryNotification>\r\n",
                htmlspecialchars($this->notification["delivery"]));
        }
        if (isset($this->notification["nondelivery"]))
        {
            $notify .= sprintf("  <URLNonDeliveryNotification>%s</URLNonDeliveryNotification>\r\n",
                htmlspecialchars($this->notification["nondelivery"]));
        }

        if (isset($this->deferred))
        {
            $deferred = sprintf("  <DeferredDeliveryTime>%s</DeferredDeliveryTime>\r\n", $this->deferred);
        }
        else
        {
            $deferred = "";
        }
        if (isset($this->timezone))
        {
            $timezone = sprintf("  <TimeZone>%s</TimeZone>\r\n", $this->timezone);
        }
        else
        {
            $timezone = "";
        }

        return sprintf("<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\r\n".
               "<aspsms>\r\n".
               "  <Userkey>%s</Userkey>\r\n".
               "  <Password>%s</Password>\r\n".
               "%s%s%s%s%s%s%s%s".
                       "  %s\r\n".
               "  <Action>%s</Action>\r\n".
               "</aspsms>\n",
               $this->userkey,
               $this->password,
               $affiliateid,
               $originator,
               $this->flashing ? "  <FlashingSMS>1</FlashingSMS>\r\n" : "",
               $this->blinking ? "  <BlinkingSMS>1</BlinkingSMS>\r\n" : "",
               $recipients,
               $deferred,
               $timezone,
               $notify,
               $content,
               $action);
    }

    function setDebug()
    {
        $this->debug = 1;
    }


    function sendWAPPush($description, $url)
    {
        $content = sprintf("<WAPPushDescription>%s</WAPPushDescription><WAPPushURL>%s</WAPPushURL>", htmlspecialchars($description), htmlspecialchars($url));

        return $this->send($this->getXML($content, "SendWAPPushSMS"));
    }


    function sendVCard($name, $phone)
    {
        $content = sprintf("<VCard><VName>%s</VName><VPhoneNumber>%s</VPhoneNumber></VCard>", htmlspecialchars($name), htmlspecialchars($phone));
        return $this->send($this->getXML($content, "SendVCard"));
    }

    function sendSMS()
    {
        return $this->send($this->getXML("<MessageData>".htmlspecialchars($this->content)."</MessageData>", "SendTextSMS"));
    }

    function sendLogo()
    {
        $c = sprintf("<MCC>%s</MCC><MNC>%s</MNC><URLBinaryFile>%s</URLBinaryFile>",
            $this->mcc, $this->mnc, $this->logo);
        return $this->send($this->getXML($c, "SendLogo"));
    }

    function showCredits()
    {
        return $this->send($this->getXML("", "ShowCredits"));
    }

    function send($msg)
    {
        foreach ($this->servers as $server)
        {
            list($host, $port) = explode(":", $server);
            $result = $this->sendToServer($msg, $host, $port);
            if ($result == 1)
            {
                return $result;
            }
        }
        return 0;
    }

    function sendToServer($msg, $host, $port)
    {
        if ($this->debug)
        {
            print "<pre>";
            print nl2br(htmlentities($msg));
            print "</pre>";
            return 1;
        }
        else
        {
            $errno = 0;
            $errdesc = 0;
            $fp = fsockopen($host, $port, $errno, $errdesc, $this->timeout);
            if ($fp)
            {
                fputs($fp, "POST /xmlsvr.asp HTTP/1.0\r\n");
                fputs($fp, "Content-Type: text/xml\r\n");
                fputs($fp, "Content-Length: ".strlen($msg)."\r\n");
                fputs($fp, "\r\n");
                fputs($fp, $msg);

                $content = 0;
                $reply = array();
                while (!feof($fp))
                {
                    $r = fgets($fp, 1024);
                    if ($content)
                    {
                        $reply[] = $r;
                    }
                    else
                    {
                        if (trim($r) == "")
                        {
                            $content = 1;
                        }
                    }
                }

                fclose($fp);
                $this->parseResult(join("", $reply));
                return $this->result["ErrorCode"];
            }
            else
            {
                $this->result["ErrorCode"] = 0;
                $this->result["ErrorDescription"] = "Unable to connect to ".$host.":".$port;
                return 0;
            }
        }
    }

    function getErrorCode()
    {
        return $this->result["ErrorCode"];
    }

    function getErrorDescription()
    {
        return $this->result["ErrorDescription"];
    }

    function getCredits()
    {
        return $this->result["Credits"];
    }

    function startElement($parser, $name, $attrs)
    {
        if ($name == "ErrorCode" || $name == "ErrorDescription" || $name == "Credits")
        {
            $this->nextResult = $name;
        }
    }

    function endElement($parser, $name)
    {
        $this->nextResult = "";
    }

    function characterData($parser, $data)
    {
        if ($this->nextResult != "")
        {
            $this->result[$this->nextResult] .= $data;
        }
    }

    function parseResult($result)
    {
        // Clear the result
        $this->result = array("ErrorCode" => 0,
                  "ErrorDescription" => "",
                  "Credits" => "");

        $p = xml_parser_create();
        xml_parser_set_option($p, XML_OPTION_CASE_FOLDING, false);
        xml_set_element_handler($p, array(&$this, "startElement"), array(&$this, "endElement"));
        xml_set_character_data_handler($p, array(&$this, "characterData"));
        if (!xml_parse($p, $result, true))
        {
            $this->result["ErrorCode"] = 0;
            $this->result["ErrorDescription"] = "Unable to parse result.";
        }
        xml_parser_free($p);
    }
}


/*********************************************************************
 * Send SMS message using Clickatell infrastructure (quick & dirty implementation)
 *
 * Name: MultiotpClickatell
 *
 * Copyright (C) 2013 SysCo systemes de communication sa
 *********************************************************************/

class MultiotpClickatell
{
    var $userkey;
    var $password;
    var $api_id;
    var $originator;
    var $recipient;
    var $content;
    var $timeout;
    var $servers;
    var $session_id;

    function MultiotpClickatell($u, $p, $a)
    {
        $this->userkey = $u;
        $this->password = $p;
        $this->api_id = $a;
        $this->recipient = array();
        $this->originator = "multiOTP";
        $this->timeout = 5;

        $this->useRegularServer();
    }

    function useRegularServer()
    {
        $this->servers = array("api.clickatell.com:80" );
    }

    function useSslServer()
    {
        $this->servers = array("ssl://api.clickatell.com:443" );
    }

    function setTimeout($t)
    {
        $this->timeout = $t;
    }

    function setOriginator($o)
    {
        $this->originator = $o;
    }

    function setRecipient($r, $id = null)
    {
        $recipient = $r;
        $recipient = str_replace(' ','',$recipient);
        $recipient = str_replace('(','',$recipient);
        $recipient = str_replace(')','',$recipient);
        $recipient = str_replace('+','00',$recipient);

        if ('00' == substr($recipient,0,2))
        {
            $recipient = substr($recipient,2);
        }
        $this->recipient = array( "number" => $recipient, "transaction" => $id);
    }

    function setContent($content)
    {
        $this->content = $content;
    }

    function getAuthXML()
    {
        return sprintf("data=<clickAPI>".
               "<auth>".
               "<api_id>".$this->api_id."</api_id>".
               "<user>".$this->userkey."</user>".
               "<password>".$this->password."</password>".
               "</auth>".
               "</clickAPI>");
    }

    function getOneSendXML($content)
    {
        $originator = "";
        if ($this->originator != "")
        {
            $originator = sprintf("<from>%s</from>", $this->originator);
        }

        $recipient = "";
        if (count($this->recipient) > 0)
        {
            if ($this->recipient["transaction"] != null)
            {
                $recipient .= sprintf("<to>%s</to>".
                "<climsgid>%s</climsgid>",
                htmlspecialchars($this->recipient["number"]),
                htmlspecialchars($this->recipient["transaction"]));
            }
            else
            {
                $recipient .= sprintf("<to>%s</to>",
                htmlspecialchars($this->recipient["number"]));
            }
        }

       return sprintf("data=<clickAPI>".
               "<sendMsg>".
               "<api_id>".$this->api_id."</api_id>".
               "<user>".$this->userkey."</user>".
               "<password>".$this->password."</password>".
               $recipient.
               "<text>".$content."</text>".
               $originator.
               "</sendMsg>".
               "</clickAPI>");
    }

    function sendSMS()
    {
        return $this->send($this->getOneSendXML(htmlspecialchars($this->content)));
    }

    function send($msg)
    {
        $result = 0;
        foreach ($this->servers as $server)
        {
            list($host, $port) = explode(":", $server);
            $result = $this->sendToServer($msg, $host, $port);
            if ($result == 1)
            {
                return $result;
            }
        }
        return $result;
    }

    function sendToServer($msg, $host, $port)
    {
        $errno = 0;
        $errdesc = 0;
        $fp = fsockopen($host, $port, $errno, $errdesc, $this->timeout);
        if ($fp)
        {
            fputs($fp, "POST /xml/xml HTTP/1.0\r\n");
            fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
            fputs($fp, "Content-Length: ".strlen($msg)."\r\n");
            fputs($fp, "User-Agent: multiOTP\r\n");
            fputs($fp, "Host: ".$host."\r\n");
            fputs($fp, "\r\n");
            fputs($fp, $msg);

            $reply = '';
            while (!feof($fp))
            {
                $reply.= fgets($fp, 1024);
            }

            fclose($fp);
            
            $result = (FALSE !== strpos($reply,'<apiMsgId>'))?'1':'0';
        }
        else
        {
            $result = 0;
        }
        return $result;
    }
}


/*********************************************************************
 * Send SMS message using IntelliSMS infrastructure (quick & dirty implementation)
 *
 * Name: MultiotpIntelliSms
 *
 * Copyright (C) 2013 SysCo systemes de communication sa
 *********************************************************************/

class MultiotpIntelliSms
{
    var $userkey;
    var $password;
    var $originator;
    var $recipient;
    var $content;
    var $timeout;
    var $servers;

    function MultiotpIntelliSms($u, $p)
    {
        $this->userkey = $u;
        $this->password = $p;
        $this->recipient = array();
        $this->originator = "multiOTP";
        $this->timeout = 5;

        $this->useRegularServer();
    }

    function useRegularServer()
    {
        $this->servers = array("www.intellisoftware.co.uk:80",
                               "www.intellisoftware2.co.uk:80" );
    }

    function useSslServer()
    {
        $this->servers = array("ssl://www.intellisoftware.co.uk:443",
                               "ssl://www.intellisoftware2.co.uk:443" );
    }

    function setTimeout($t)
    {
        $this->timeout = $t;
    }

    function setOriginator($o)
    {
        $this->originator = $o;
    }

    function setRecipient($r)
    {
        $recipient = $r;
        $recipient = str_replace(' ','',$recipient);
        $recipient = str_replace('(','',$recipient);
        $recipient = str_replace(')','',$recipient);
        $recipient = str_replace('+','00',$recipient);

        if ('00' == substr($recipient,0,2))
        {
            $recipient = substr($recipient,2);
        }
        $this->recipient = array( "number" => $recipient);
    }

    function setContent($content)
    {
        $this->content = $content;
    }

    function getOneSendContent($content)
    {
        $send_data = "";
        
        $send_data = $send_data.(("" == $send_data)?"":"&").'username='.urlencode($this->userkey);
        $send_data = $send_data.(("" == $send_data)?"":"&").'password='.urlencode($this->password);

        $originator = "";
        if ($this->originator != "")
        {
            $send_data = $send_data.(("" == $send_data)?"":"&").'from='.urlencode($this->originator);
        }

        $recipient = "";
        if (count($this->recipient) > 0)
        {
            $send_data = $send_data.(("" == $send_data)?"":"&").'to='.urlencode($this->recipient["number"]);
        }
        
        $send_data = $send_data.(("" == $send_data)?"":"&").'text='.urlencode($content);

        return $send_data;
    }

    function sendSMS()
    {
        return $this->send($this->getOneSendContent($this->content));
    }

    function send($msg)
    {
        $result = 0;
        foreach ($this->servers as $server)
        {
            // list($host, $port) = explode(":", $server);
            
            $pos = strpos($server, '://');
            if (FALSE === $pos)
            {
                $protocol = '';
            }
            else
            {
                switch (strtolower(substr($server,0,$pos)))
                {
                    case 'https':
                    case 'ssl':
                        $protocol = 'ssl://';
                        break;
                    case 'tls':
                        $protocol = 'tls://';
                        break;
                    default:
                        $protocol = '';
                        break;
                }
                
                $server = substr($server,$pos+3);
            }
            
            $pos = strpos($server, '/');
            if (FALSE === $pos)
            {
                $host = $server;
                $url = '/';
            }
            else
            {
                $host = substr($server,0,$pos);
                $url = substr($server,$pos); // And not +1 as we want the / at the beginning
            }
            
            $pos = strpos($host, ':');
            if (FALSE === $pos)
            {
                $port = 80;
            }
            else
            {
                $port = substr($host,$pos+1);
                $host = substr($host,0,$pos);
            }
            
            $result = trim($this->sendToServer($msg, $protocol.$host, $port));
            if (substr($result,0,2) == "ID")
            {
                return $result;
            }
        }
        return $result;
    }

    function sendToServer($msg, $host, $port)
    {
        $errno = 0;
        $errdesc = 0;
        $fp = fsockopen($host, $port, $errno, $errdesc, $this->timeout); // 'ssl://'.$host
        if ($fp)
        {
            fputs($fp, "POST /smsgateway/sendmsg.aspx HTTP/1.0\r\n");
            fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
            fputs($fp, "Content-Length: ".strlen($msg)."\r\n");
            fputs($fp, "User-Agent: multiOTP\r\n");
            fputs($fp, "Host: ".$host."\r\n");
            fputs($fp, "\r\n");
            fputs($fp, $msg."\r\n");
            fputs($fp, "\r\n");

            $reply = '';
            while (!feof($fp))
            {
                $reply.= fgets($fp, 1024);
            }

            fclose($fp);

            $reply_array = split("\n", $reply);
            $reply = '';

            $end_of_header = FALSE;
            
            // loop until we have an empty line, and than take the result
            foreach ($reply_array as $reply_one)
            {
                if ($end_of_header)
                {
                    $reply.= $reply_one;
                }
                elseif ("" == trim($reply_one))
                {
                    $end_of_header = TRUE;
                }
            }

            $result = $reply;
        }
        else
        {
            $result = "";
        }
        return $result;
    }
}

/*
	PHP LDAP CLASS FOR MANIPULATING ACTIVE DIRECTORY
	Version 2.1
	
	Written by Scott Barnett
	email: scott@wiggumworld.com
	http://adldap.sourceforge.net/
	
	Copyright (C) 2006-2007 Scott Barnett
	
	I'd appreciate any improvements or additions to be submitted back
	to benefit the entire community :)
	
	Works with PHP 5, should be fine with PHP 4, let me know if/where it doesn't :)
	
	Please visit the project website for a full list of the functions and
	documentation on using them.
	http://adldap.sourceforge.net/documentation.php
	
	This library is free software; you can redistribute it and/or
	modify it under the terms of the GNU Lesser General Public
	License as published by the Free Software Foundation; either
	version 2.1 of the License, or (at your option) any later version.
	
	This library is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
	Lesser General Public License for more details.

	********************************************************************
	Something to keep in mind is that Active Directory is a permissions
	based directory. If you bind as a domain user, you can't fetch as
	much information on other users as you could as a domain admin.
	********************************************************************
*/

// Different type of accounts in AD
define ('ADLDAP_NORMAL_ACCOUNT', 805306368);
define ('ADLDAP_WORKSTATION_TRUST', 805306369);
define ('ADLDAP_INTERDOMAIN_TRUST', 805306370);
define ('ADLDAP_SECURITY_GLOBAL_GROUP', 268435456);
define ('ADLDAP_DISTRIBUTION_GROUP', 268435457);
define ('ADLDAP_SECURITY_LOCAL_GROUP', 536870912);
define ('ADLDAP_DISTRIBUTION_LOCAL_GROUP', 536870913);

class MultiotpAdLdap {
	// BEFORE YOU ASK A QUESTION, PLEASE READ THE DOCUMENTATION AND THE FAQ
	// http://adldap.sourceforge.net/documentation.php
	// http://adldap.sourceforge.net/faq.php

	// You can set your default variables here, or when you invoke the class
	var $_account_suffix="@mydomain.local";
	var $_base_dn = "DC=mydomain,DC=local"; 
	
	// An array of domain controllers. Specify multiple controllers if you 
	// would like the class to balance the LDAP queries amongst multiple servers
	var $_domain_controllers = array ("dc01.mydomain.local");
	
	// optional account with higher privileges for searching
	// not really that optional because you can't query much as a user
	var $_ad_username=NULL;
	var $_ad_password=NULL;
	
	// AD does not return the primary group. http://support.microsoft.com/?kbid=321360
	// This tweak will resolve the real primary group, but may be resource intensive. 
	// Setting to false will fudge "Domain Users" and is much faster. Keep in mind though that if
	// someone's primary group is NOT domain users, this is obviously going to bollocks the results
	var $_real_primarygroup=true;
	
	// Use SSL, your server needs to be setup, please see - http://adldap.sourceforge.net/ldap_ssl.php
	var $_use_ssl=false;
	
	// When querying group memberships, do it recursively
	// eg. User Fred is a member of Group A, which is a member of Group B, which is a member of Group C
	// user_ingroup("Fred","C") will returns true with this option turned on, false if turned off
	var $_recursive_groups=true;
	
	// You should not need to edit anything below this line
	//******************************************************************************************
	
	//other variables
	var $_conn;
	var $_bind;

	// default constructor
	function MultiotpAdLdap($options=array()){
		//you can specifically overide any of the default configuration options setup above
		if (count($options)>0){
			if (array_key_exists("account_suffix",$options)){ $this->_account_suffix=$options["account_suffix"]; }
			if (array_key_exists("base_dn",$options)){ $this->_base_dn=$options["base_dn"]; }
			if (array_key_exists("domain_controllers",$options)){ $this->_domain_controllers=$options["domain_controllers"]; }
			if (array_key_exists("ad_username",$options)){ $this->_ad_username=$options["ad_username"]; }
			if (array_key_exists("ad_password",$options)){ $this->_ad_password=$options["ad_password"]; }
			if (array_key_exists("real_primarygroup",$options)){ $this->_real_primarygroup=$options["real_primarygroup"]; }
			if (array_key_exists("use_ssl",$options)){ $this->_use_ssl=$options["use_ssl"]; }
			if (array_key_exists("recursive_groups",$options)){ $this->_recursive_groups=$options["recursive_groups"]; }
		}
	
		//connect to the LDAP server as the username/password
		$dc=$this->random_controller();
		if ($this->_use_ssl){
			$this->_conn = ldap_connect("ldaps://".$dc);
		} else {
			$this->_conn = ldap_connect($dc);
		}
		
		//set some ldap options for talking to AD
		ldap_set_option($this->_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($this->_conn, LDAP_OPT_REFERRALS, 0);
		
		//bind as a domain admin if they've set it up
		if ($this->_ad_username!=NULL && $this->_ad_password!=NULL){
			$this->_bind = @ldap_bind($this->_conn,$this->_ad_username.$this->_account_suffix,$this->_ad_password);
			if (!$this->_bind){
				if ($this->_use_ssl){
					//if you have problems troubleshooting, remove the @ character from the ldap_bind command above to get the actual error message
					echo ("FATAL: AD bind failed. Either the LDAPS connection failed or the login credentials are incorrect."); exit();
				} else {
					echo ("FATAL: AD bind failed. Check the login credentials."); exit();
				}
			}
		}
		
		return (true);
	}

	// default destructor
	function __destruct(){ ldap_close ($this->_conn); }

	//validate a users login credentials
	function authenticate($username,$password,$prevent_rebind=false){
		if ($username==NULL || $password==NULL){ return (false); } //prevent null binding
		
		//bind as the user		
		$this->_bind = @ldap_bind($this->_conn,$username.$this->_account_suffix,$password);
		if (!$this->_bind){ return (false); }
		
		//once we've checked their details, kick back into admin mode if we have it
		if ($this->_ad_username!=NULL && !$prevent_rebind){
			$this->_bind = @ldap_bind($this->_conn,$this->_ad_username.$this->_account_suffix,$this->_ad_password);
			if (!$this->_bind){ echo ("FATAL: AD rebind failed."); exit(); } //this should never happen in theory
		}
		
		return (true);
	}

	//*****************************************************************************************************************
	// GROUP FUNCTIONS

	// Add a group to a group
	function group_add_group($parent,$child){

		//find the parent group's dn
		$parent_group=$this->group_info($parent,array("cn"));
		if ($parent_group[0]["dn"]==NULL){ return (false); }
		$parent_dn=$parent_group[0]["dn"];
		
		//find the child group's dn
		$child_group=$this->group_info($child,array("cn"));
		if ($child_group[0]["dn"]==NULL){ return (false); }
		$child_dn=$child_group[0]["dn"];
				
		$add=array();
		$add["member"] = $child_dn;
		
		$result=@ldap_mod_add($this->_conn,$parent_dn,$add);
		if ($result==false){ return (false); }
		return (true);
	}
	
	// Add a user to a group
	function group_add_user($group,$user){
		//adding a user is a bit fiddly, we need to get the full DN of the user
		//and add it using the full DN of the group
		
		//find the user's dn
		$user_info=$this->user_info($user,array("cn"));
		if ($user_info[0]["dn"]==NULL){ return (false); }
		$user_dn=$user_info[0]["dn"];
		
		//find the group's dn
		$group_info=$this->group_info($group,array("cn"));
		if ($group_info[0]["dn"]==NULL){ return (false); }
		$group_dn=$group_info[0]["dn"];
		
		$add=array();
		$add["member"] = $user_dn;
		
		$result=@ldap_mod_add($this->_conn,$group_dn,$add);
		if ($result==false){ return (false); }
		return (true);
	}

	// Create a group
	function group_create($attributes){
		if (!is_array($attributes)){ return ("Attributes must be an array"); }
		if (!array_key_exists("group_name",$attributes)){ return ("Missing compulsory field [group_name]"); }
		if (!array_key_exists("container",$attributes)){ return ("Missing compulsory field [container]"); }
		if (!array_key_exists("description",$attributes)){ return ("Missing compulsory field [description]"); }
		if (!is_array($attributes["container"])){ return ("Container attribute must be an array."); }
		$attributes["container"]=array_reverse($attributes["container"]);

		//$member_array = array();
		//$member_array[0] = "cn=user1,cn=Users,dc=yourdomain,dc=com";
		//$member_array[1] = "cn=administrator,cn=Users,dc=yourdomain,dc=com";
		
		$add=array();
		$add["cn"] = $attributes["group_name"];
		$add["samaccountname"] = $attributes["group_name"];
		$add["objectClass"] = "Group";
		$add["description"] = $attributes["description"];
		//$add["member"] = $member_array; UNTESTED

		$container="OU=".implode(",OU=",$attributes["container"]);
		$result=ldap_add($this->_conn,"CN=".$add["cn"].", ".$container.",".$this->_base_dn,$add);
		if ($result!=true){ return (false); }
		
		return (true);
	}

	// Remove a group from a group
	function group_del_group($parent,$child){
	
		//find the parent dn
		$parent_group=$this->group_info($parent,array("cn"));
		if ($parent_group[0]["dn"]==NULL){ return (false); }
		$parent_dn=$parent_group[0]["dn"];
		
		//find the child dn
		$child_group=$this->group_info($child,array("cn"));
		if ($child_group[0]["dn"]==NULL){ return (false); }
		$child_dn=$child_group[0]["dn"];
		
		$del=array();
		$del["member"] = $child_dn;
		
		$result=@ldap_mod_del($this->_conn,$parent_dn,$del);
		if ($result==false){ return (false); }
		return (true);
	}
	
	// Remove a user from a group
	function group_del_user($group,$user){
	
		//find the parent dn
		$group_info=$this->group_info($group,array("cn"));
		if ($group_info[0]["dn"]==NULL){ return (false); }
		$group_dn=$group_info[0]["dn"];
		
		//find the child dn
		$user_info=$this->user_info($user,array("cn"));
		if ($user_info[0]["dn"]==NULL){ return (false); }
		$user_dn=$user_info[0]["dn"];

		$del=array();
		$del["member"] = $user_dn;
		
		$result=@ldap_mod_del($this->_conn,$group_dn,$del);
		if ($result==false){ return (false); }
		return (true);
	}
	
	// Returns an array of information for a specified group
	function group_info($group_name,$fields=NULL){
		if ($group_name==NULL){ return (false); }
		if (!$this->_bind){ return (false); }
		
		$filter="(&(objectCategory=group)(name=".$this->ldap_slashes($group_name)."))";
		//echo ($filter."!!!<br>");
		if ($fields==NULL){ $fields=array("member","memberof","cn","description","distinguishedname","objectcategory","samaccountname"); }
		$sr=ldap_search($this->_conn,$this->_base_dn,$filter,$fields);
		$entries = ldap_get_entries($this->_conn, $sr);
		//print_r($entries);
		return ($entries);
	}
	
	// Retun a complete list of "groups in groups"	
	function recursive_groups($group){
		if ($group==NULL){ return (false); }

		$ret_groups=array();
		
		$groups=$this->group_info($group,array("memberof"));
		$groups=$groups[0]["memberof"];

		if ($groups){
			$group_names=$this->nice_names($groups);
			$ret_groups=array_merge($ret_groups,$group_names); //final groups to return
			
			foreach ($group_names as $id => $group_name){
				$child_groups=$this->recursive_groups($group_name);
				$ret_groups=array_merge($ret_groups,$child_groups);
			}
		}

		return ($ret_groups);
	}
	
	//*****************************************************************************************************************
	// USER FUNCTIONS

	//create a user
	function user_create($attributes){
		//check for compulsory fields
		if (!array_key_exists("username",$attributes)){ return ("Missing compulsory field [username]"); }
		if (!array_key_exists("firstname",$attributes)){ return ("Missing compulsory field [firstname]"); }
		if (!array_key_exists("surname",$attributes)){ return ("Missing compulsory field [surname]"); }
		if (!array_key_exists("email",$attributes)){ return ("Missing compulsory field [email]"); }
		if (!array_key_exists("container",$attributes)){ return ("Missing compulsory field [container]"); }
		if (!is_array($attributes["container"])){ return ("Container attribute must be an array."); }

		if (array_key_exists("password",$attributes) && !$this->_use_ssl){ echo ("FATAL: SSL must be configured on your webserver and enabled in the class to set passwords."); exit(); }

		if (!array_key_exists("display_name",$attributes)){ $attributes["display_name"]=$attributes["firstname"]." ".$attributes["surname"]; }

		//translate the schema
		$add=$this->adldap_schema($attributes);
		
		//additional stuff only used for adding accounts
		$add["cn"][0]=$attributes["display_name"];
		$add["samaccountname"][0]=$attributes["username"];
		$add["objectclass"][0]="top";
		$add["objectclass"][1]="person";
		$add["objectclass"][2]="organizationalPerson";
		$add["objectclass"][3]="user"; //person?
		//$add["name"][0]=$attributes["firstname"]." ".$attributes["surname"];

		//set the account control attribute
		$control_options=array("NORMAL_ACCOUNT");
		if (!$attributes["enabled"]){ $control_options[]="ACCOUNTDISABLE"; }
		$add["userAccountControl"][0]=$this->account_control($control_options);
		//echo ("<pre>"); print_r($add);

		//determine the container
		$attributes["container"]=array_reverse($attributes["container"]);
		$container="OU=".implode(",OU=",$attributes["container"]);

		//add the entry
		$result=@ldap_add($this->_conn, "CN=".$add["cn"][0].", ".$container.",".$this->_base_dn, $add);
		if ($result!=true){ return (false); }
		
		return (true);
	}

	// user_groups($user)
	//	Returns an array of groups that a user is a member off
	function user_groups($username,$recursive=NULL){
		if ($username==NULL){ return (false); }
		if ($recursive==NULL){ $recursive=$this->_recursive_groups; } //use the default option if they haven't set it
		if (!$this->_bind){ return (false); }
		
		//search the directory for their information
		$info=@$this->user_info($username,array("memberof","primarygroupid"));
		$groups=$this->nice_names($info[0]["memberof"]); //presuming the entry returned is our guy (unique usernames)

		if ($recursive){
			foreach ($groups as $id => $group_name){
				$extra_groups=$this->recursive_groups($group_name);
				$groups=array_merge($groups,$extra_groups);
			}
		}
		
		return ($groups);
	}

	// Returns an array of information for a specific user
	// SysCo/al added "mobile"
	function user_info($username,$fields=NULL){
		if ($username==NULL){ return (false); }
		if (!$this->_bind){ return (false); }

		$filter="samaccountname=".$username;
		if ($fields==NULL){ $fields=array("samaccountname","mail","memberof","department","displayname","telephonenumber","primarygroupid","mobile"); }
		$sr=ldap_search($this->_conn,$this->_base_dn,$filter,$fields);
		$entries = ldap_get_entries($this->_conn, $sr);
		
		// AD does not return the primary group in the ldap query, we may need to fudge it
		// SysCo/al added a test to check if $entries[0]["primarygroupid"][0] exists
		if ($this->_real_primarygroup){
			if (isset($entries[0]["primarygroupid"][0]))
			{
				$entries[0]["memberof"][]=$this->group_cn($entries[0]["primarygroupid"][0]);
			}
		} else {
			$entries[0]["memberof"][]="CN=Domain Users,CN=Users,".$this->_base_dn;
		}
		
		@$entries[0]["memberof"]["count"]++;
		return ($entries);
	}
	
	// Returns true if the user is a member of the group
	function user_ingroup($username,$group,$recursive=NULL){
		if ($username==NULL){ return (false); }
		if ($group==NULL){ return (false); }
		if (!$this->_bind){ return (false); }
		if ($recursive==NULL){ $recursive=$this->_recursive_groups; } //use the default option if they haven't set it
		
		//get a list of the groups
		$groups=$this->user_groups($username,array("memberof"),$recursive);
		
		//return true if the specified group is in the group list
		if (in_array($group,$groups)){ return (true); }

		return (false);
	}
	
	//modify a user
	function user_modify($username,$attributes){
		if ($username==NULL){ return ("Missing compulsory field [username]"); }
		if (array_key_exists("password",$attributes) && !$this->_use_ssl){ echo ("FATAL: SSL must be configured on your webserver and enabled in the class to set passwords."); exit(); }
		//if (array_key_exists("container",$attributes)){
			//if (!is_array($attributes["container"])){ return ("Container attribute must be an array."); }
			//$attributes["container"]=array_reverse($attributes["container"]);
		//}

		//find the dn of the user
		$user=$this->user_info($username,array("cn"));
		if ($user[0]["dn"]==NULL){ return (false); }
		$user_dn=$user[0]["dn"];

		//translate the update to the LDAP schema				
		$mod=$this->adldap_schema($attributes);
		if (!$mod){ return (false); }
		
		//set the account control attribute (only if specified)
		if (array_key_exists("enabled",$attributes)){
			if ($attributes["enabled"]){ $control_options=array("NORMAL_ACCOUNT"); }
			else { $control_options=array("NORMAL_ACCOUNT","ACCOUNTDISABLE"); }
			$mod["userAccountControl"][0]=$this->account_control($control_options);
		}

		//do the update
		$result=ldap_modify($this->_conn,$user_dn,$mod);
		if ($result==false){ return (false); }
		
		return (true);
	}
		
	// Set the password of a user
	function user_password($username,$password){
		if ($username==NULL){ return (false); }
		if ($password==NULL){ return (false); }
		if (!$this->_bind){ return (false); }
		if (!$this->_use_ssl){ echo ("FATAL: SSL must be configured on your webserver and enabled in the class to set passwords."); exit(); }
		
		$user=$this->user_info($username,array("cn"));
		if ($user[0]["dn"]==NULL){ return (false); }
		$user_dn=$user[0]["dn"];
				
		$add=array();
		$add["unicodePwd"][0]=$this->encode_password($password);
		
		$result=ldap_mod_replace($this->_conn,$user_dn,$add);
		if ($result==false){ return (false); }
		
		return (true);
	}

	//*****************************************************************************************************************
	// COMPUTER FUNCTIONS
	
	// Returns an array of information for a specific computer
	function computer_info($computer_name,$fields=NULL){
		if ($computer_name==NULL){ return (false); }
		if (!$this->_bind){ return (false); }

		$filter="(&(objectClass=computer)(cn=".$computer_name."))";
		if ($fields==NULL){ $fields=array("memberof","cn","displayname","dnshostname","distinguishedname","objectcategory","operatingsystem","operatingsystemservicepack","operatingsystemversion"); }
		$sr=ldap_search($this->_conn,$this->_base_dn,$filter,$fields);
		$entries = ldap_get_entries($this->_conn, $sr);
		
		return ($entries);
	}

	// Returns all AD users
	function all_users($include_desc = false, $search = "*", $sorted = true){
		if (!$this->_bind){ return (false); }
		
		//perform the search and grab all their details
		$filter = "(&(objectClass=user)(samaccounttype=". ADLDAP_NORMAL_ACCOUNT .")(objectCategory=person)(cn=".$search."))";
		$fields=array("samaccountname","displayname");
		$sr=ldap_search($this->_conn,$this->_base_dn,$filter,$fields);
		$entries = ldap_get_entries($this->_conn, $sr);

		$users_array = array();
		for ($i=0; $i<$entries["count"]; $i++){
			if ($include_desc && strlen($entries[$i]["displayname"][0])>0){
				$users_array[ $entries[$i]["samaccountname"][0] ] = $entries[$i]["displayname"][0];
			} elseif ($include_desc){
				$users_array[ $entries[$i]["samaccountname"][0] ] = $entries[$i]["samaccountname"][0];
			} else {
				array_push($users_array, $entries[$i]["samaccountname"][0]);
			}
		}
		if ($sorted){ asort($users_array); }
		return ($users_array);
	}
	
	// Returns a complete list of the groups in AD
	function all_groups($include_desc = false, $search = "*", $sorted = true){
		if (!$this->_bind){ return (false); }
		
		//perform the search and grab all their details
		$filter = "(&(objectCategory=group)(samaccounttype=". ADLDAP_SECURITY_GLOBAL_GROUP .")(cn=".$search."))";
		$fields=array("samaccountname","description");
		$sr=ldap_search($this->_conn,$this->_base_dn,$filter,$fields);
		$entries = ldap_get_entries($this->_conn, $sr);

		$groups_array = array();		
		for ($i=0; $i<$entries["count"]; $i++){
			if ($include_desc && strlen($entries[$i]["description"][0]) > 0 ){
				$groups_array[ $entries[$i]["samaccountname"][0] ] = $entries[$i]["description"][0];
			} elseif ($include_desc){
				$groups_array[ $entries[$i]["samaccountname"][0] ] = $entries[$i]["samaccountname"][0];
			} else {
				array_push($groups_array, $entries[$i]["samaccountname"][0]);
			}
		}
		if( $sorted ){ asort($groups_array); }
		return ($groups_array);
	}

	//************************************************************************************************************
	// UTILITY FUNCTIONS (not intended to be called directly but I spose you could?)

	function adldap_schema($attributes){
	
		//ldap doesn't like NULL attributes, only set them if they have values
		// I'd like to know how to set an LDAP attribute to NULL though, at the moment I set it to a space
		// SysCo/al added "mobile"
		$mod=array();
		if ($attributes["address_city"]){ $mod["l"][0]=$attributes["address_city"]; }
		if ($attributes["address_code"]){ $mod["postalCode"][0]=$attributes["address_code"]; }
		//if ($attributes["address_country"]){ $mod["countryCode"][0]=$attributes["address_country"]; } // use country codes?
		if ($attributes["address_pobox"]){ $mod["postOfficeBox"][0]=$attributes["address_pobox"]; }
		if ($attributes["address_state"]){ $mod["st"][0]=$attributes["address_state"]; }
		if ($attributes["address_street"]){ $mod["streetAddress"][0]=$attributes["address_street"]; }
		if ($attributes["company"]){ $mod["company"][0]=$attributes["company"]; }
		if ($attributes["change_password"]){ $mod["pwdLastSet"][0]=0; }
		if ($attributes["company"]){ $mod["company"][0]=$attributes["company"]; }
		if ($attributes["department"]){ $mod["department"][0]=$attributes["department"]; }
		if ($attributes["description"]){ $mod["description"][0]=$attributes["description"]; }
		if ($attributes["display_name"]){ $mod["displayName"][0]=$attributes["display_name"]; }
		if ($attributes["email"]){ $mod["mail"][0]=$attributes["email"]; }
		if ($attributes["expires"]){ $mod["accountExpires"][0]=$attributes["expires"]; } //unix epoch format?
		if ($attributes["firstname"]){ $mod["givenName"][0]=$attributes["firstname"]; }
		if ($attributes["home_directory"]){ $mod["homeDirectory"][0]=$attributes["home_directory"]; }
		if ($attributes["home_drive"]){ $mod["homeDrive"][0]=$attributes["home_drive"]; }
		if ($attributes["initials"]){ $mod["initials"][0]=$attributes["initials"]; }
		if ($attributes["logon_name"]){ $mod["userPrincipalName"][0]=$attributes["logon_name"]; }
		if ($attributes["manager"]){ $mod["manager"][0]=$attributes["manager"]; }  //UNTESTED ***Use DistinguishedName***
		if ($attributes["office"]){ $mod["physicalDeliveryOfficeName"][0]=$attributes["office"]; }
		if ($attributes["password"]){ $mod["unicodePwd"][0]=$this->encode_password($attributes["password"]); }
		if ($attributes["profile_path"]){ $mod["profilepath"][0]=$attributes["profile_path"]; }
		if ($attributes["script_path"]){ $mod["scriptPath"][0]=$attributes["script_path"]; }
		if ($attributes["surname"]){ $mod["sn"][0]=$attributes["surname"]; }
		if ($attributes["title"]){ $mod["title"][0]=$attributes["title"]; }
		if ($attributes["telephone"]){ $mod["telephoneNumber"][0]=$attributes["telephone"]; }
		if ($attributes["mobile"]){ $mod["telephoneNumber"][0]=$attributes["mobile"]; }
		if ($attributes["web_page"]){ $mod["wWWHomePage"][0]=$attributes["web_page"]; }
		//echo ("<pre>"); print_r($mod);
/*
		// modifying a name is a bit fiddly
		if ($attributes["firstname"] && $attributes["surname"]){
			$mod["cn"][0]=$attributes["firstname"]." ".$attributes["surname"];
			$mod["displayname"][0]=$attributes["firstname"]." ".$attributes["surname"];
			$mod["name"][0]=$attributes["firstname"]." ".$attributes["surname"];
		}
*/


		if (count($mod)==0){ return (false); }
		return ($mod);
	}


	function group_cn($gid){
		// coping with AD not returning the primary group
		// http://support.microsoft.com/?kbid=321360
		// for some reason it's not possible to search on primarygrouptoken=XXX
		// if someone can show otherwise, I'd like to know about it :)
		// this way is resource intensive and generally a pain in the @#%^
		
		if ($gid==NULL){ return (false); }
		$r=false;
		
		$filter="(&(objectCategory=group)(samaccounttype=". ADLDAP_SECURITY_GLOBAL_GROUP ."))";
		$fields=array("primarygrouptoken","samaccountname","distinguishedname");
		$sr=ldap_search($this->_conn,$this->_base_dn,$filter,$fields);
		$entries = ldap_get_entries($this->_conn, $sr);
		
		for ($i=0; $i<$entries["count"]; $i++){
			if ($entries[$i]["primarygrouptoken"][0]==$gid){
				$r=$entries[$i]["distinguishedname"][0];
				$i=$entries["count"];
			}
		}

		return ($r);
	}

	// Encode a password for transmission over LDAP
	function encode_password($password){
		$password="\"".$password."\"";
		$encoded="";
		for ($i=0; $i <strlen($password); $i++){ $encoded.="{$password{$i}}\000"; }
		return ($encoded);
	}
	
	// Escape bad characters
	// DEVELOPERS SHOULD BE DOING PROPER FILTERING IF THEY'RE ACCEPTING USER INPUT
	// this is just a list of characters with known problems and I'm trying not to strip out other languages
	function ldap_slashes($str){
		$illegal=array("(",")","#"); // the + character has problems too, but it's an illegal character
		
		$legal=array();
		foreach ($illegal as $id => $char){ $legal[$id]="\\".$char; } //make up the array of legal chars
		
		$str=str_replace($illegal,$legal,$str); //replace them
		return ($str);
	}
	
	// Return a random controller
	function random_controller(){
		//select a random domain controller
		mt_srand(doubleval(microtime()) * 100000000); // for older php versions
		return ($this->_domain_controllers[array_rand($this->_domain_controllers)]);
	}
	
	function account_control($options){
		$val=0;

		if (is_array($options)){
			if (in_array("SCRIPT",$options)){ $val=$val+1; }
			if (in_array("ACCOUNTDISABLE",$options)){ $val=$val+2; }
			if (in_array("HOMEDIR_REQUIRED",$options)){ $val=$val+8; }
			if (in_array("LOCKOUT",$options)){ $val=$val+16; }
			if (in_array("PASSWD_NOTREQD",$options)){ $val=$val+32; }
			//PASSWD_CANT_CHANGE Note You cannot assign this permission by directly modifying the UserAccountControl attribute.
			//For information about how to set the permission programmatically, see the "Property flag descriptions" section.
			if (in_array("ENCRYPTED_TEXT_PWD_ALLOWED",$options)){ $val=$val+128; }
			if (in_array("TEMP_DUPLICATE_ACCOUNT",$options)){ $val=$val+256; }
			if (in_array("NORMAL_ACCOUNT",$options)){ $val=$val+512; }
			if (in_array("INTERDOMAIN_TRUST_ACCOUNT",$options)){ $val=$val+2048; }
			if (in_array("WORKSTATION_TRUST_ACCOUNT",$options)){ $val=$val+4096; }
			if (in_array("SERVER_TRUST_ACCOUNT",$options)){ $val=$val+8192; }
			if (in_array("DONT_EXPIRE_PASSWORD",$options)){ $val=$val+65536; }
			if (in_array("MNS_LOGON_ACCOUNT",$options)){ $val=$val+131072; }
			if (in_array("SMARTCARD_REQUIRED",$options)){ $val=$val+262144; }
			if (in_array("TRUSTED_FOR_DELEGATION",$options)){ $val=$val+524288; }
			if (in_array("NOT_DELEGATED",$options)){ $val=$val+1048576; }
			if (in_array("USE_DES_KEY_ONLY",$options)){ $val=$val+2097152; }
			if (in_array("DONT_REQ_PREAUTH",$options)){ $val=$val+4194304; } 
			if (in_array("PASSWORD_EXPIRED",$options)){ $val=$val+8388608; }
			if (in_array("TRUSTED_TO_AUTH_FOR_DELEGATION",$options)){ $val=$val+16777216; }
		}
		return ($val);
	}
	
	// Take an ldap query and return the nice names, without all the LDAP prefixes (eg. CN, DN)
	function nice_names($groups){

		$group_array=array();
		for ($i=0; $i<$groups["count"]; $i++){ //for each group
			$line=$groups[$i];
			
			if (strlen($line)>0){ 
				//more presumptions, they're all prefixed with CN=
				//so we ditch the first three characters and the group
				//name goes up to the first comma
				$bits=explode(",",$line);
				$group_array[]=substr($bits[0],3,(strlen($bits[0])-3));
			}
		}
		return ($group_array);	
	}
}

/*
Copyright (c) 2002-2010, Michael Bretterklieber <michael@bretterklieber.com>
All rights reserved.
 
Redistribution and use in source and binary forms, with or without 
modification, are permitted provided that the following conditions 
are met:
 
1. Redistributions of source code must retain the above copyright 
   notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright 
   notice, this list of conditions and the following disclaimer in the 
   documentation and/or other materials provided with the distribution.
3. The names of the authors may not be used to endorse or promote products 
   derived from this software without specific prior written permission.
 
THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND 
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED 
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. 
IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, 
INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, 
BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, 
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY 
OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING 
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, 
EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
This code cannot simply be copied and put under the GNU Public License or 
any other GPL-like (LGPL, GPL2) License.

    $Id: CHAP.php 302857 2010-08-28 21:12:59Z mbretter $
*/

// require_once 'PEAR.php';

/**
* Classes for generating packets for various CHAP Protocols:
* CHAP-MD5: RFC1994
* MS-CHAPv1: RFC2433
* MS-CHAPv2: RFC2759
*
* @package MultiotpCrypt_CHAP
* @author  Michael Bretterklieber <michael@bretterklieber.com>
* @access  public
* @version $Revision: 302857 $
*/

/**
 * class MultiotpCrypt_CHAP
 *
 * Abstract base class for CHAP
 *
 * @package MultiotpCrypt_CHAP 
 */
class MultiotpCrypt_CHAP
{
    /**
     * Random binary challenge
     * @var  string
     */
    var $challenge = null;

    /**
     * Binary response
     * @var  string
     */
    var $response = null;    

    /**
     * User password
     * @var  string
     */
    var $password = null;

    /**
     * Id of the authentication request. Should incremented after every request.
     * @var  integer
     */
    var $chapid = 1;
    
    /**
     * Constructor
     *
     * Generates a random challenge
     * @return void
     */
    function MultiotpCrypt_CHAP()
    {
        $this->generateChallenge();
    }
    
    /**
     * Generates a random binary challenge
     *
     * @param  string  $varname  Name of the property
     * @param  integer $size     Size of the challenge in Bytes
     * @return void
     */
    function generateChallenge($varname = 'challenge', $size = 8)
    {
        $this->$varname = '';
        for ($i = 0; $i < $size; $i++) {
            $this->$varname .= pack('C', 1 + mt_rand() % 255);
        }
        return $this->$varname;
    }

    /**
     * Generates the response. Overwrite this.
     *
     * @return void
     */    
    function challengeResponse()
    {
    }
        
}

/**
 * class MultiotpCrypt_CHAP_MD5
 *
 * Generate CHAP-MD5 Packets
 *
 * @package MultiotpCrypt_CHAP 
 */
class MultiotpCrypt_CHAP_MD5 extends MultiotpCrypt_CHAP 
{

    /**
     * Generates the response.
     *
     * CHAP-MD5 uses MD5-Hash for generating the response. The Hash consists
     * of the chapid, the plaintext password and the challenge.
     *
     * @return string
     */ 
    function challengeResponse()
    {
        return pack('H*', md5(pack('C', $this->chapid) . $this->password . $this->challenge));
    }
}

/**
 * class MultiotpCrypt_CHAP_MSv1
 *
 * Generate MS-CHAPv1 Packets. MS-CHAP doesen't use the plaintext password, it uses the
 * NT-HASH wich is stored in the SAM-Database or in the smbpasswd, if you are using samba.
 * The NT-HASH is MD4(str2unicode(plaintextpass)). 
 * You need the hash extension for this class.
 * 
 * @package MultiotpCrypt_CHAP 
 */
class MultiotpCrypt_CHAP_MSv1 extends MultiotpCrypt_CHAP
{
    /**
     * Wether using deprecated LM-Responses or not.
     * 0 = use LM-Response, 1 = use NT-Response
     * @var  bool
     */
    var $flags = 1;
    
    /**
     * Constructor
     *
     * Loads the hash extension
     * @return void
     */
    function MultiotpCrypt_CHAP_MSv1()
    {
        $this->MultiotpCrypt_CHAP();
        // $this->loadExtension('hash');        
    }
    
    /**
     * Generates the NT-HASH from the given plaintext password.
     *
     * @access public
     * @return string
     */
    function ntPasswordHash($password = null) 
    {
        if (isset($password)) {
            return pack('H*',hash('md4', $this->str2unicode($password)));
        } else {
            return pack('H*',hash('md4', $this->str2unicode($this->password)));
        }
    }
    
    /**
     * Converts ascii to unicode.
     *
     * @access public
     * @return string
     */
    function str2unicode($str) 
    {
        $uni = '';
        $str = (string) $str;
        for ($i = 0; $i < strlen($str); $i++) {
            $a = ord($str{$i}) << 8;
            $uni .= sprintf("%X", $a);
        }
        return pack('H*', $uni);
    }    
    
    /**
     * Generates the NT-Response. 
     *
     * @access public
     * @return string
     */  
    function challengeResponse() 
    {
        return $this->_challengeResponse();
    }
    
    /**
     * Generates the NT-Response. 
     *
     * @access public
     * @return string
     */  
    function ntChallengeResponse() 
    {
        return $this->_challengeResponse(false);
    }    
    
    /**
     * Generates the LAN-Manager-Response. 
     *
     * @access public
     * @return string
     */
    function lmChallengeResponse()
    {
        return $this->_challengeResponse(true);
    }

    /**
     * Generates the response.
     *
     * Generates the response using DES.
     *
     * @param  bool  $lm  wether generating LAN-Manager-Response
     * @access private
     * @return string
     */
    function _challengeResponse($lm = false)
    {
        if ($lm) {
            $hash = $this->lmPasswordHash();
        } else {
            $hash = $this->ntPasswordHash();
        }

        while (strlen($hash) < 21) {
            $hash .= "\0";
        }

        $td = mcrypt_module_open(MCRYPT_DES, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        $key = $this->_desAddParity(substr($hash, 0, 7));
        mcrypt_generic_init($td, $key, $iv);
        $resp1 = mcrypt_generic($td, $this->challenge);
        mcrypt_generic_deinit($td);

        $key = $this->_desAddParity(substr($hash, 7, 7));
        mcrypt_generic_init($td, $key, $iv);
        $resp2 = mcrypt_generic($td, $this->challenge);
        mcrypt_generic_deinit($td);

        $key = $this->_desAddParity(substr($hash, 14, 7));
        mcrypt_generic_init($td, $key, $iv);
        $resp3 = mcrypt_generic($td, $this->challenge);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        return $resp1 . $resp2 . $resp3;
    }

    /**
     * Generates the LAN-Manager-HASH from the given plaintext password.
     *
     * @access public
     * @return string
     */
    function lmPasswordHash($password = null)
    {
        $plain = isset($password) ? $password : $this->password;

        $plain = substr(strtoupper($plain), 0, 14);
        while (strlen($plain) < 14) {
             $plain .= "\0";
        }

        return $this->_desHash(substr($plain, 0, 7)) . $this->_desHash(substr($plain, 7, 7));
    }

    /**
     * Generates an irreversible HASH.
     *
     * @access private
     * @return string
     */
    function _desHash($plain)
    {
        $key = $this->_desAddParity($plain);
        $td = mcrypt_module_open(MCRYPT_DES, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $hash = mcrypt_generic($td, 'KGS!@#$%');
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $hash;
    }

    /**
     * Adds the parity bit to the given DES key.
     *
     * @access private
     * @param  string  $key 7-Bytes Key without parity
     * @return string
     */
    function _desAddParity($key)
    {
        static $odd_parity = array(
                1,  1,  2,  2,  4,  4,  7,  7,  8,  8, 11, 11, 13, 13, 14, 14,
                16, 16, 19, 19, 21, 21, 22, 22, 25, 25, 26, 26, 28, 28, 31, 31,
                32, 32, 35, 35, 37, 37, 38, 38, 41, 41, 42, 42, 44, 44, 47, 47,
                49, 49, 50, 50, 52, 52, 55, 55, 56, 56, 59, 59, 61, 61, 62, 62,
                64, 64, 67, 67, 69, 69, 70, 70, 73, 73, 74, 74, 76, 76, 79, 79,
                81, 81, 82, 82, 84, 84, 87, 87, 88, 88, 91, 91, 93, 93, 94, 94,
                97, 97, 98, 98,100,100,103,103,104,104,107,107,109,109,110,110,
                112,112,115,115,117,117,118,118,121,121,122,122,124,124,127,127,
                128,128,131,131,133,133,134,134,137,137,138,138,140,140,143,143,
                145,145,146,146,148,148,151,151,152,152,155,155,157,157,158,158,
                161,161,162,162,164,164,167,167,168,168,171,171,173,173,174,174,
                176,176,179,179,181,181,182,182,185,185,186,186,188,188,191,191,
                193,193,194,194,196,196,199,199,200,200,203,203,205,205,206,206,
                208,208,211,211,213,213,214,214,217,217,218,218,220,220,223,223,
                224,224,227,227,229,229,230,230,233,233,234,234,236,236,239,239,
                241,241,242,242,244,244,247,247,248,248,251,251,253,253,254,254);

        $bin = '';
        for ($i = 0; $i < strlen($key); $i++) {
            $bin .= sprintf('%08s', decbin(ord($key{$i})));
        }

        $str1 = explode('-', substr(chunk_split($bin, 7, '-'), 0, -1));
        $x = '';
        foreach($str1 as $s) {
            $x .= sprintf('%02s', dechex($odd_parity[bindec($s . '0')]));
        }

        return pack('H*', $x);

    }
    
    /**
     * Generates the response-packet. 
     *
     * @param  bool  $lm  wether including LAN-Manager-Response
     * @access private
     * @return string
     */      
    function response($lm = false)
    {
        $ntresp = $this->ntChallengeResponse();
        if ($lm) {
            $lmresp = $this->lmChallengeResponse();
        } else {
            $lmresp = str_repeat ("\0", 24);
        }

        // Response: LM Response, NT Response, flags (0 = use LM Response, 1 = use NT Response)
        return $lmresp . $ntresp . pack('C', !$lm);
    }
}

/**
 * class MultiotpCrypt_CHAP_MSv2
 *
 * Generate MS-CHAPv2 Packets. This version of MS-CHAP uses a 16 Bytes authenticator 
 * challenge and a 16 Bytes peer Challenge. LAN-Manager responses no longer exists
 * in this version. The challenge is already a SHA1 challenge hash of both challenges 
 * and of the username.
 * 
 * @package MultiotpCrypt_CHAP 
 */
class MultiotpCrypt_CHAP_MSv2 extends MultiotpCrypt_CHAP_MSv1
{
    /**
     * The username
     * @var  string
     */
    var $username = null;

    /**
     * The 16 Bytes random binary peer challenge
     * @var  string
     */
    var $peerChallenge = null;

    /**
     * The 16 Bytes random binary authenticator challenge
     * @var  string
     */
    var $authChallenge = null;
    
    /**
     * Constructor
     *
     * Generates the 16 Bytes peer and authentication challenge
     * @return void
     */
    function MultiotpCrypt_CHAP_MSv2()
    {
        $this->MultiotpCrypt_CHAP_MSv1();
        $this->generateChallenge('peerChallenge', 16);
        $this->generateChallenge('authChallenge', 16);
    }    

    /**
     * Generates a hash from the NT-HASH.
     *
     * @access public
     * @param  string  $nthash The NT-HASH
     * @return string
     */    
    function ntPasswordHashHash($nthash) 
    {
        return pack('H*',hash('md4', $nthash));
    }
    
    /**
     * Generates the challenge hash from the peer and the authenticator challenge and
     * the username. SHA1 is used for this, but only the first 8 Bytes are used.
     *
     * @access public
     * @return string
     */   
    function challengeHash() 
    {
        return substr(pack('H*',hash('sha1', $this->peerChallenge . $this->authChallenge . $this->username)), 0, 8);
    }    

    /**
     * Generates the response. 
     *
     * @access public
     * @return string
     */  
    function challengeResponse() 
    {
        $this->challenge = $this->challengeHash();
        return $this->_challengeResponse();
    }    
}

    //////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////
    //                                                                              //
    // Custom function providing qrcode generation, based on the library from       //
    // Y. Swetake (http://www.swetake.com/qr/index-e.html)                          //
    //                                                                              //
    // A better library from Dominik Dzienia exists                                 //
    // (http://phpqrcode.sourceforge.net/), but it is not compatible with PHP 4.4.4 //
    //                                                                              //
    // Enhancements made by SysCo/al                                                //
    // If $file_name = "binary", send binary content without header                 //
    //                                                                              //
    //////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////
    function MultiotpQrcode($data = '', $file_name = '', $image_type = "P", $ecc_level = "Q", $module_size = 4, $version = 0, $structure_m = 0, $structure_n = 0, $parity = 0, $original_data = '', $path = 'qrcode/data', $image_path = 'qrcode/image')
    {
        /*
        #
        # QRcode image PHP scripts  version 0.50i (C)2000-2009,Y.Swetake
        #
        #
        #
        #  This program outputs a png image of "QRcode model 2". 
        #  You cannot use a several functions of QRcode in this version. 
        #  See README.txt .
        #
        #  This version supports QRcode model2 version 1-40.
        #
        #
        #  This program requires PHP4.1 and gd 1.6 or higher.
        #
        #  You must set $path & $image_path the path to QRcode data file.
        #
        #
        # [usage]
        #   qr_img.php?d=[data]&e=[(L,M,Q,H)]&s=[int]&v=[(1-40)]
        #             (&m=[(1-16)]&n=[(2-16)](&p=[(0-255)],&o=[data]))
        #
        #   d= data         URL encoded data.
        #   e= ECC level    L or M or Q or H   (default M)
        #                   Low (L): Up to 7% of errors can be corrected.          // Info added for multiOTP
        #                   Medium-Low (M): Up to 15% of errors can be corrected.  // Info added for multiOTP
        #                   Medium-High (Q): Up to 25% of errors can be corrected. // Info added for multiOTP
        #                   High (H): Up to 30% of errors can be corrected.        // Info added for multiOTP
        #   s= module size  (default PNG:4 JPEG:8)
        #   v= version      1-40 or Auto select if you do not set.
        #   t= image type   J:jpeg image , other: PNG image
        #
        #  structured append  m of n (experimental)
        #   n= structure append n (2-16)
        #   m= structure append m (1-16)
        #   p= parity
        #   o= original data (URL encoded data)  for calculating parity
        #
        #
        #
        # THIS SOFTWARE IS PROVIDED BY Y.Swetake ``AS IS'' AND ANY EXPRESS OR
        # IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
        # OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
        # IN NO EVENT SHALL Y.Swetake OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
        # INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES 
        # (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
        # LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)  HOWEVER CAUSED 
        # AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
        # OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE
        # USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
        #
        */

        /* ------ setting area ------ */

        $output_name = $file_name;
        
        $qrcode_image_size = 0; // Initialized for multiOTP
        $qrcode_result = NULL;  // Added for multiOTP

        $version_ul=40;              /* upper limit for version  */  
        /* ------ setting area end ------ */

        // Adapted for multiOTP (method parameters instead of URL parameters)
        $qrcode_data_string=$data;
        $qrcode_error_correct=$ecc_level;
        $qrcode_module_size=$module_size;
        $qrcode_version=$version;
        $qrcode_image_type=$image_type;

        $qrcode_structureappend_n=$structure_n;
        $qrcode_structureappend_m=$structure_m;
        $qrcode_structureappend_parity=$parity;
        $qrcode_structureappend_originaldata=$original_data;


        if (strtolower($qrcode_image_type)=="j") // Adapted for multiOTP
        {
            $qrcode_image_type="jpeg";
        }
        else
        {
            $qrcode_image_type="png";
        }

        if ($qrcode_module_size>0) {
        } else {
            if ($qrcode_image_type=="jpeg"){
                $qrcode_module_size=8;
            } else {
                $qrcode_module_size=4;
            }
        }
        $qrcode_data_string=($qrcode_data_string); // SysCo/al no rawurldecode here, because we are calling the function directly
        $data_length=strlen($qrcode_data_string);
        if ($data_length<=0) {
            trigger_error("QRcode : Data do not exist.",E_USER_ERROR);
            exit;
        }
        $data_counter=0;
        if ($qrcode_structureappend_n>1
         && $qrcode_structureappend_n<=16
         && $qrcode_structureappend_m>0
         && $qrcode_structureqppend_m<=16){

            $data_value[0]=3;
            $data_bits[0]=4;

            $data_value[1]=$qrcode_structureappend_m-1;
            $data_bits[1]=4;

            $data_value[2]=$qrcode_structureappend_n-1;
            $data_bits[2]=4;


            $originaldata_length=strlen($qrcode_structureappend_originaldata);
            if ($originaldata_length>1){
                $qrcode_structureappend_parity=0;
                $i=0;
                while ($i<$originaldata_length){
                    $qrcode_structureappend_parity=($qrcode_structureappend_parity ^ ord(substr($qrcode_structureappend_originaldata,$i,1)));
                    $i++;
                }
            }

            $data_value[3]=$qrcode_structureappend_parity;
            $data_bits[3]=8;

            $data_counter=4;
        }

        $data_bits[$data_counter]=4;

        /*  --- determine encode mode */

        if (preg_match("/[^0-9]/",$qrcode_data_string)!=0){
            if (preg_match("/[^0-9A-Z \$\*\%\+\.\/\:\-]/",$qrcode_data_string)!=0) {


             /*  --- 8bit byte mode */

                $codeword_num_plus=array(0,0,0,0,0,0,0,0,0,0,
        8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,
        8,8,8,8,8,8,8,8,8,8,8,8,8,8);

                $data_value[$data_counter]=4;
                $data_counter++;
                $data_value[$data_counter]=$data_length;
                $data_bits[$data_counter]=8;   /* #version 1-9 */
                $codeword_num_counter_value=$data_counter;

                $data_counter++;
                $i=0;
                while ($i<$data_length){
                    $data_value[$data_counter]=ord(substr($qrcode_data_string,$i,1));
                    $data_bits[$data_counter]=8;
                    $data_counter++;
                    $i++;
                }
            } else {

            /* ---- alphanumeric mode */

                $codeword_num_plus=array(0,0,0,0,0,0,0,0,0,0,
        2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,
        4,4,4,4,4,4,4,4,4,4,4,4,4,4);

                $data_value[$data_counter]=2;
                $data_counter++;
                $data_value[$data_counter]=$data_length;
                $data_bits[$data_counter]=9;  /* #version 1-9 */
                $codeword_num_counter_value=$data_counter;


                $alphanumeric_character_hash=array("0"=>0,"1"=>1,"2"=>2,"3"=>3,"4"=>4,
        "5"=>5,"6"=>6,"7"=>7,"8"=>8,"9"=>9,"A"=>10,"B"=>11,"C"=>12,"D"=>13,"E"=>14,
        "F"=>15,"G"=>16,"H"=>17,"I"=>18,"J"=>19,"K"=>20,"L"=>21,"M"=>22,"N"=>23,
        "O"=>24,"P"=>25,"Q"=>26,"R"=>27,"S"=>28,"T"=>29,"U"=>30,"V"=>31,
        "W"=>32,"X"=>33,"Y"=>34,"Z"=>35," "=>36,"$"=>37,"%"=>38,"*"=>39,
        "+"=>40,"-"=>41,"."=>42,"/"=>43,":"=>44);

                $i=0;
                $data_counter++;
                while ($i<$data_length){
                    if (($i %2)==0){
                        $data_value[$data_counter]=$alphanumeric_character_hash[substr($qrcode_data_string,$i,1)];
                        $data_bits[$data_counter]=6;
                    } else {
                        $data_value[$data_counter]=$data_value[$data_counter]*45+$alphanumeric_character_hash[substr($qrcode_data_string,$i,1)];
                        $data_bits[$data_counter]=11;
                        $data_counter++;
                    }
                    $i++;
                }
            }
        } else {

            /* ---- numeric mode */

            $codeword_num_plus=array(0,0,0,0,0,0,0,0,0,0,
        2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,
        4,4,4,4,4,4,4,4,4,4,4,4,4,4);

            $data_value[$data_counter]=1;
            $data_counter++;
            $data_value[$data_counter]=$data_length;
            $data_bits[$data_counter]=10;   /* #version 1-9 */
            $codeword_num_counter_value=$data_counter;

            $i=0;
            $data_counter++;
            while ($i<$data_length){
                if (($i % 3)==0){
                    $data_value[$data_counter]=substr($qrcode_data_string,$i,1);
                    $data_bits[$data_counter]=4;
                } else {
                     $data_value[$data_counter]=$data_value[$data_counter]*10+substr($qrcode_data_string,$i,1);
                 if (($i % 3)==1){
                     $data_bits[$data_counter]=7;
                 } else {
                     $data_bits[$data_counter]=10;
                     $data_counter++;
                 }
                }
                $i++;
            }
        }
        if (@$data_bits[$data_counter]>0) {
            $data_counter++;
        }
        $i=0;
        $total_data_bits=0;
        while($i<$data_counter){
            $total_data_bits+=$data_bits[$i];
            $i++;
        }


        $ecc_character_hash=array("L"=>"1",
        "l"=>"1",
        "M"=>"0",
        "m"=>"0",
        "Q"=>"3",
        "q"=>"3",
        "H"=>"2",
        "h"=>"2");

         $ec=@$ecc_character_hash[$qrcode_error_correct]; 

         if (!$ec){$ec=0;}

        $max_data_bits_array=array(
        0,128,224,352,512,688,864,992,1232,1456,1728,
        2032,2320,2672,2920,3320,3624,4056,4504,5016,5352,
        5712,6256,6880,7312,8000,8496,9024,9544,10136,10984,
        11640,12328,13048,13800,14496,15312,15936,16816,17728,18672,

        152,272,440,640,864,1088,1248,1552,1856,2192,
        2592,2960,3424,3688,4184,4712,5176,5768,6360,6888,
        7456,8048,8752,9392,10208,10960,11744,12248,13048,13880,
        14744,15640,16568,17528,18448,19472,20528,21616,22496,23648,

        72,128,208,288,368,480,528,688,800,976,
        1120,1264,1440,1576,1784,2024,2264,2504,2728,3080,
        3248,3536,3712,4112,4304,4768,5024,5288,5608,5960,
        6344,6760,7208,7688,7888,8432,8768,9136,9776,10208,

        104,176,272,384,496,608,704,880,1056,1232,
        1440,1648,1952,2088,2360,2600,2936,3176,3560,3880,
        4096,4544,4912,5312,5744,6032,6464,6968,7288,7880,
        8264,8920,9368,9848,10288,10832,11408,12016,12656,13328
        );
        if (!is_numeric($qrcode_version)){
            $qrcode_version=0;
        }
        if (!$qrcode_version){
         /* #--- auto version select */
            $i=1+40*$ec;
            $j=$i+39;
            $qrcode_version=1; 
            while ($i<=$j){
                if (($max_data_bits_array[$i])>=$total_data_bits+$codeword_num_plus[$qrcode_version]     ){
                    $max_data_bits=$max_data_bits_array[$i];
                    break;
                }
             $i++;
             $qrcode_version++;
            }
        } else {
             $max_data_bits=$max_data_bits_array[$qrcode_version+40*$ec];
        }
        if ($qrcode_version>$version_ul){
          trigger_error("QRcode : too large version.",E_USER_ERROR);
        }

        $total_data_bits+=$codeword_num_plus[$qrcode_version];
            $data_bits[$codeword_num_counter_value]+=$codeword_num_plus[$qrcode_version];

        $max_codewords_array=array(0,26,44,70,100,134,172,196,242,
        292,346,404,466,532,581,655,733,815,901,991,1085,1156,
        1258,1364,1474,1588,1706,1828,1921,2051,2185,2323,2465,
        2611,2761,2876,3034,3196,3362,3532,3706);

        $max_codewords=$max_codewords_array[$qrcode_version];
        $max_modules_1side=17+($qrcode_version <<2);

        $matrix_remain_bit=array(0,0,7,7,7,7,7,0,0,0,0,0,0,0,3,3,3,3,3,3,3,
        4,4,4,4,4,4,4,3,3,3,3,3,3,3,0,0,0,0,0,0);

        /* ---- read version ECC data file */

        $byte_num=$matrix_remain_bit[$qrcode_version]+($max_codewords << 3);
        $filename=$path."/qrv".$qrcode_version."_".$ec.".dat";
        $fp1 = fopen ($filename, "rb");
            $matx=fread($fp1,$byte_num);
            $maty=fread($fp1,$byte_num);
            $masks=fread($fp1,$byte_num);
            $fi_x=fread($fp1,15);
            $fi_y=fread($fp1,15);
            $rs_ecc_codewords=ord(fread($fp1,1));
            $rso=fread($fp1,128);
        fclose($fp1);

        $matrix_x_array=unpack("C*",$matx);
        $matrix_y_array=unpack("C*",$maty);
        $mask_array=unpack("C*",$masks);

        $rs_block_order=unpack("C*",$rso);

        $format_information_x2=unpack("C*",$fi_x);
        $format_information_y2=unpack("C*",$fi_y);

        $format_information_x1=array(0,1,2,3,4,5,7,8,8,8,8,8,8,8,8);
        $format_information_y1=array(8,8,8,8,8,8,8,8,7,5,4,3,2,1,0);

        $max_data_codewords=($max_data_bits >>3);

        $filename = $path."/rsc".$rs_ecc_codewords.".dat";
        $fp0 = fopen ($filename, "rb");
        $i=0;
        while ($i<256) {
            $rs_cal_table_array[$i]=fread ($fp0,$rs_ecc_codewords);
            $i++;
        }
        fclose ($fp0);

        /*  --- set terminator */

        if ($total_data_bits<=$max_data_bits-4){
            $data_value[$data_counter]=0;
            $data_bits[$data_counter]=4;
        } else {
            if ($total_data_bits<$max_data_bits){
            $data_value[$data_counter]=0;
                $data_bits[$data_counter]=$max_data_bits-$total_data_bits;
            } else {
                if ($total_data_bits>$max_data_bits){
                trigger_error("QRcode : Overflow error",E_USER_ERROR);
                exit;
                }
            }
        }

        /* ----divide data by 8bit */

        $i=0;
        $codewords_counter=0;
        $codewords[0]=0;
        $remaining_bits=8;

        while ($i<=$data_counter) {
            $buffer=@$data_value[$i];
            $buffer_bits=@$data_bits[$i];

            $flag=1;
            while ($flag) {
                if ($remaining_bits>$buffer_bits){  
                    $codewords[$codewords_counter]=((@$codewords[$codewords_counter]<<$buffer_bits) | $buffer);
                    $remaining_bits-=$buffer_bits;
                    $flag=0;
                } else {
                    $buffer_bits-=$remaining_bits;
                    $codewords[$codewords_counter]=(($codewords[$codewords_counter] << $remaining_bits) | ($buffer >> $buffer_bits));

                    if ($buffer_bits==0) {
                        $flag=0;
                    } else {
                        $buffer= ($buffer & ((1 << $buffer_bits)-1) );
                        $flag=1;   
                    }

                    $codewords_counter++;
                    if ($codewords_counter<$max_data_codewords-1){
                        $codewords[$codewords_counter]=0;
                    }
                    $remaining_bits=8;
                }
            }
            $i++;
        }
        if ($remaining_bits!=8) {
            $codewords[$codewords_counter]=$codewords[$codewords_counter] << $remaining_bits;
        } else {
            $codewords_counter--;
        }

        /* ----  set padding character */

        if ($codewords_counter<$max_data_codewords-1){
            $flag=1;
            while ($codewords_counter<$max_data_codewords-1){
                $codewords_counter++;
                if ($flag==1) {
                    $codewords[$codewords_counter]=236;
                } else {
                    $codewords[$codewords_counter]=17;
                }
                $flag=$flag*(-1);
            }
        }

        /* ---- RS-ECC prepare */

        $i=0;
        $j=0;
        $rs_block_number=0;
        $rs_temp[0]="";

        while($i<$max_data_codewords){

            $rs_temp[$rs_block_number].=chr($codewords[$i]);
            $j++;

            if ($j>=$rs_block_order[$rs_block_number+1]-$rs_ecc_codewords){
                $j=0;
                $rs_block_number++;
                $rs_temp[$rs_block_number]="";
            }
            $i++;
        }


        /*
        #
        # RS-ECC main
        #
        */

        $rs_block_number=0;
        $rs_block_order_num=count($rs_block_order);

        while ($rs_block_number<$rs_block_order_num){

            $rs_codewords=$rs_block_order[$rs_block_number+1];
            $rs_data_codewords=$rs_codewords-$rs_ecc_codewords;

            $rstemp=$rs_temp[$rs_block_number].str_repeat(chr(0),$rs_ecc_codewords);
            $padding_data=str_repeat(chr(0),$rs_data_codewords);

            $j=$rs_data_codewords;
            while($j>0){
                $first=ord(substr($rstemp,0,1));

                if ($first){
                    $left_chr=substr($rstemp,1);
                    $cal=$rs_cal_table_array[$first].$padding_data;
                    $rstemp=$left_chr ^ $cal;
                } else {
                    $rstemp=substr($rstemp,1);
                }

                $j--;
            }

            $codewords=array_merge($codewords,unpack("C*",$rstemp));

            $rs_block_number++;
        }

        /* ---- flash matrix */

        $i=0;
        while ($i<$max_modules_1side){
            $j=0;
            while ($j<$max_modules_1side){
                $matrix_content[$j][$i]=0;
                $j++;
            }
            $i++;
        }

        /* --- attach data */

        $i=0;
        while ($i<$max_codewords){
            $codeword_i=$codewords[$i];
            $j=8;
            while ($j>=1){
                $codeword_bits_number=($i << 3) +  $j;
                $matrix_content[ $matrix_x_array[$codeword_bits_number] ][ $matrix_y_array[$codeword_bits_number] ]=((255*($codeword_i & 1)) ^ $mask_array[$codeword_bits_number] ); 
                $codeword_i= $codeword_i >> 1;
                $j--;
            }
            $i++;
        }

        $matrix_remain=$matrix_remain_bit[$qrcode_version];
        while ($matrix_remain){
            $remain_bit_temp = $matrix_remain + ( $max_codewords <<3);
            $matrix_content[ $matrix_x_array[$remain_bit_temp] ][ $matrix_y_array[$remain_bit_temp] ]  =  ( 255 ^ $mask_array[$remain_bit_temp] );
            $matrix_remain--;
        }

        #--- mask select

        $min_demerit_score=0;
            $hor_master="";
            $ver_master="";
            $k=0;
            while($k<$max_modules_1side){
                $l=0;
                while($l<$max_modules_1side){
                    $hor_master=$hor_master.chr($matrix_content[$l][$k]);
                    $ver_master=$ver_master.chr($matrix_content[$k][$l]);
                    $l++;
                }
                $k++;
            }
        $i=0;
        $all_matrix=$max_modules_1side * $max_modules_1side; 
        while ($i<8){
            $demerit_n1=0;
            $ptn_temp=array();
            $bit= 1<< $i;
            $bit_r=(~$bit)&255;
            $bit_mask=str_repeat(chr($bit),$all_matrix);
            $hor = $hor_master & $bit_mask;
            $ver = $ver_master & $bit_mask;

            $ver_shift1=$ver.str_repeat(chr(170),$max_modules_1side);
            $ver_shift2=str_repeat(chr(170),$max_modules_1side).$ver;
            $ver_shift1_0=$ver.str_repeat(chr(0),$max_modules_1side);
            $ver_shift2_0=str_repeat(chr(0),$max_modules_1side).$ver;
            $ver_or=chunk_split(~($ver_shift1 | $ver_shift2),$max_modules_1side,chr(170));
            $ver_and=chunk_split(~($ver_shift1_0 & $ver_shift2_0),$max_modules_1side,chr(170));

            $hor=chunk_split(~$hor,$max_modules_1side,chr(170));
            $ver=chunk_split(~$ver,$max_modules_1side,chr(170));
            $hor=$hor.chr(170).$ver;

            $n1_search="/".str_repeat(chr(255),5)."+|".str_repeat(chr($bit_r),5)."+/";
            $n3_search=chr($bit_r).chr(255).chr($bit_r).chr($bit_r).chr($bit_r).chr(255).chr($bit_r);

           $demerit_n3=substr_count($hor,$n3_search)*40;
           $demerit_n4=floor(abs(( (100* (substr_count($ver,chr($bit_r))/($byte_num)) )-50)/5))*10;


           $n2_search1="/".chr($bit_r).chr($bit_r)."+/";
           $n2_search2="/".chr(255).chr(255)."+/";
           $demerit_n2=0;
           preg_match_all($n2_search1,$ver_and,$ptn_temp);
           foreach($ptn_temp[0] as $str_temp){
               $demerit_n2+=(strlen($str_temp)-1);
           }
           $ptn_temp=array();
           preg_match_all($n2_search2,$ver_or,$ptn_temp);
           foreach($ptn_temp[0] as $str_temp){
               $demerit_n2+=(strlen($str_temp)-1);
           }
           $demerit_n2*=3;
          
           $ptn_temp=array();

           preg_match_all($n1_search,$hor,$ptn_temp);
           foreach($ptn_temp[0] as $str_temp){
               $demerit_n1+=(strlen($str_temp)-2);
           }

           $demerit_score=$demerit_n1+$demerit_n2+$demerit_n3+$demerit_n4;

           if ($demerit_score<=$min_demerit_score || $i==0){
                $mask_number=$i;
                $min_demerit_score=$demerit_score;
           }

        $i++;
        }

        $mask_content=1 << $mask_number;

        # --- format information

        $format_information_value=(($ec << 3) | $mask_number);
        $format_information_array=array("101010000010010","101000100100101",
        "101111001111100","101101101001011","100010111111001","100000011001110",
        "100111110010111","100101010100000","111011111000100","111001011110011",
        "111110110101010","111100010011101","110011000101111","110001100011000",
        "110110001000001","110100101110110","001011010001001","001001110111110",
        "001110011100111","001100111010000","000011101100010","000001001010101",
        "000110100001100","000100000111011","011010101011111","011000001101000",
        "011111100110001","011101000000110","010010010110100","010000110000011",
        "010111011011010","010101111101101");
        $i=0;
        while ($i<15){
            $content=substr($format_information_array[$format_information_value],$i,1);

            $matrix_content[$format_information_x1[$i]][$format_information_y1[$i]]=$content * 255;
            $matrix_content[$format_information_x2[$i+1]][$format_information_y2[$i+1]]=$content * 255;
            $i++;
        }


        $mib=$max_modules_1side+8;
        $qrcode_image_size=$mib*$qrcode_module_size;
        if ($qrcode_image_size>1480){
          trigger_error("QRcode : Too large image size",E_USER_ERROR);
        }
        $output_image =ImageCreate($qrcode_image_size,$qrcode_image_size);

        $image_path=$image_path."/qrv".$qrcode_version.".png";

        $base_image=ImageCreateFromPNG($image_path);

        $col[1]=ImageColorAllocate($base_image,0,0,0);
        $col[0]=ImageColorAllocate($base_image,255,255,255);

        $i=4;
        $mxe=4+$max_modules_1side;
        $ii=0;
        while ($i<$mxe){
            $j=4;
            $jj=0;
            while ($j<$mxe){
                if ($matrix_content[$ii][$jj] & $mask_content){
                    ImageSetPixel($base_image,$i,$j,$col[1]); 
                }
                $j++;
                $jj++;
            }
            $i++;
            $ii++;
        }
        /*
        #--- output image
        #
        */
        
        // Adapted for multiOTP in order to choose either display output or file output
        ImageCopyResized($output_image,$base_image,0,0,0,0,$qrcode_image_size,$qrcode_image_size,$mib,$mib);
        if ('' == trim($file_name))
        {
            Header("Content-type: image/".$qrcode_image_type);
            $output_name = NULL;
        }
        if ('binary' == trim($file_name))
        {
            $output_name = NULL;
            ob_start();
        }
        if ($qrcode_image_type == "jpeg")
        {
            ImageJpeg($output_image, $output_name);
        }
        else
        {
            ImagePng($output_image, $output_name);
        }
        if ('binary' == trim($file_name))
        {
            $qrcode_result = ob_get_clean();
        }
        else
        {
            $qrcode_result = $qrcode_image_size;
        }
        imagedestroy($base_image);
        imagedestroy($output_image);
        
        return $qrcode_result;
    }

?>
