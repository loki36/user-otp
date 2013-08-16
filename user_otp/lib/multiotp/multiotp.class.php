#!/usr/bin/php
<?php

/*********************************************************************
 * multiOTP PHP command line - Strong two-factor authentication PHP class
 * http://www.multiotp.net/
 *
 * Donation are always welcome! Please check http://www.multiotp.net/
 * and you will find the magic button ;-)
 *
 * The multiOTP class is a strong authentication class in pure PHP
 * that supports the following algorithms (mOTP is recommended):
 *  - mOTP (http://motp.sourceforge.net/)
 *  - OATH/HOTP RFC 4226 (http://www.ietf.org/rfc/rfc4226.txt)
 *  - OATH/TOTP HOTPTimeBased RFC 4226 extension
 *  - Google Authenticator (OATH/HOTP or OATH/TOTP based with a base32 seed)
 *
 * This class can be used as is in your own PHP project, but it can also be
 * used easily as an external authentication provider with at least the
 * following RADIUS servers (using the multiotp command line script):
 *  - TekRADIUS, a free Radius server for Windows with MS-SQL backend
 *    (http:/www.tekradius.com/)
 *  - TekRADIUS LT, a free Radius server for Windows with SQLite backend
 *    (http:/www.tekradius.com/)
 *  - FreeRADIUS, a free Radius server implementation for Linux and Windows
 *    and *nix environments (http://freeradius.org/)
 *  - FreeRADIUS for Windows, a free Radius server implementation ported
 *    for Windows (http://sourceforge.net/projects/freeradius/)
 *
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
 * @author: SysCo/al
 * @since CreationDate: 2010-06-08
 * @copyright (c) 2010-2013 by SysCo systemes de communication sa
 * @version $LastChangedRevision: 4.0.0beta5 $
 * @version $LastChangedDate: 2013-05-18 $
 * @version $LastChangedBy: SysCo/al $
 * @link $HeadURL: multiotp.class.php $
 * @link http://www.multiOTP.net/
 * @link developer@sysco.ch
 * Language: PHP 4.4.4 or higher
 *
 *
 * Usage
 *
 *   require_once('multiotp.class.php');
 *   $multiotp = new Multiotp();
 *   $multiotp->SetEncryptionKey('MyPersonalEncryptionKey');
 *   $multiotp->SetUser('user);
 *   $result = $multiotp->CheckToken('token');
 *
 *
 * Examples
 *
 *  Create a new user
 *    require_once('multiotp.class.php');
 *    $multiotp = new Multiotp();
 *    $multiotp->SetEncryptionKey('MyPersonalEncryptionKey');
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
 *    $multiotp = new Multiotp();
 *    $multiotp->SetEncryptionKey('MyPersonalEncryptionKey');
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
 *    $multiotp = new Multiotp();
 *    $multiotp->SetEncryptionKey('MyPersonalEncryptionKey');
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
 *    $multiotp = new Multiotp();
 *    $multiotp->SetEncryptionKey('MyPersonalEncryptionKey');
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
 * 2013-05-14 Henk van der Helm
 *   Henk asked to support also the provider IntelliSMS, which was done! Thanks for his donation!
 *
 * 2013-05-03 Stefan Kügler
 *   Stefan proposed to lower the default max_time_window to 600 secondes, which was done.
 *
 * 2013-03-04 Alan DeKok
 *   Alan proposed in the freeradius mailing-list to put a prefix to be able to handle the
 *   debug info by the freeradius server, which was done.
 *
 * 2012-03-16 Nicolas Goralski
 *   Nicolas proposed an enhancement in order to support PAM
 *     (with the -checkpam option in the command line edition)
 *
 * 2011-05-19 Fabiano Domeniconi
 *   Fabiano found old info in the samples, CheckToken() is not boolean anymore! Samples fixed.
 *
 * 2011-04-24 Steven Roddis
 *   Steven asked for more examples, which was done! Thanks to Steven for his donation ;-)
 *
 * 2010-09-15 Jasper Pol
 *   Jasper has added MySQL backend support
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
 *   2013-05-18 4.0.0beta5 SysCo/al Adding email, sms and seed_password to users attributes
 *                              Adding sms support (aspsms, clickatell, intellisms)
 *                              Adding prefix support for debug mode (in order to send Reply-Message := to Radius)
 *                              Adding a lot of new methods to handle easier the users and the tokens
 *                              General speedup by using available native functions for hash_hmac and others
 *                              Default max_time_window has been lowered to 600 seconds (thanks Stefan for suggestion)
 *                              Integrated Google Authenticator support with integrated base 32 seed handling
 *                              Integrated QRcode generator library (from Y. Swetake)
 *                              General options in an external configuration file
 *   2011-10-25 3.9.2  SysCo/al Some quick fixes after intensive check
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
 * Update 2013-02-09
 * @package multiotp
 * @version 2.0.1
 * @author SysCo/al
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


/*********************************************************************
 * Name: Multiotp
 * MultiOTP PHP class
 *
 * Creation 2010-07-18
 * Update 2013-04-29
 * @package multiotp
 * @version 4.0.0
 * @author SysCo/al
 *********************************************************************/
class Multiotp
{

    var $_version;                  // Current version of the library
    var $_date;                     // Current date of the library
    var $_copyright;                // Copyright message of the library, don't change it !
    var $_website;                  // Website dedicated to this LGPL library, please don't change it !

    var $_valid_algorithms;         // String containing valid algorithms to be used, separated by *, like *mOTP*HOTP*TOTP*
    var $_attributes_to_encrypt;    // Attributes to encrypt in the flat files
    var $_encryption_key;           // Symetric encryption key for the users files and the tokens files
    var $_source_ip;                // Source IP of the request (for a RADIUS request for example)
    var $_errors_text;              // An array containing errors text description
    var $_config_data;              // An array with all the general config related info
    var $_config_folder;            // Folder where the general config file is written
    var $_user;                     // Current user, case insensitive
    var $_user_data;                // An array with all the user related info
    var $_user_data_read_flag;      // Indicate if the user data has been read from the database file
    var $_users_folder;             // Folder where users definition files are stored
    var $_devices_folder;           // Folder where devices definition files are stored
    var $_token;                    // Current token, case insensitive
    var $_token_data;               // An array with all the token related info
    var $_token_data_read_flag;     // Indicate if the token data has been read from the database file
    var $_tokens_folder;            // Folder where tokens definition files are stored
    var $_log_folder;               // Folder where log file is written
    var $_log_file_name;            // Name of the log file
    var $_log_flag;                 // Enable or disable the log
    var $_log_header_written;       // Internal flag to know if the header was already written or not in the log file
    var $_log_verbose_flag;         // Enable or disable the verbose mode for the log
    var $_last_imported_tokens;     // An array containing the names (which are mostly the serials) of the last imported tokens

    // Optional MySQL backend upport
    var $_sql_server;               // The SQL server to use. If empty all information will be written to files
    var $_sql_user;                 // Username for the SQL server.
    var $_sql_passwd;               // Password for the SQL server.
    var $_sql_db;                   // The database to use.
    var $_sql_log_table;            // Table name for log. If empty log will be written to file
    var $_sql_users_table;          // Table name for users. If empty users will be written to files
    var $_sql_tokens_table;         // Table name for tokens. If empty tokens will be written to files


    /*********************************************************************
     * Name: Multiotp
     * Short description: Multiotp class constructor
     *
     * Creation 2010-07-18
     * Update 2013-05-13
     * @package multiotp
     * @version 4.0.0
     * @author SysCo/al
     * @return  void
     *********************************************************************/
    function Multiotp($encryption_key = '')
    {
        $this->_class                    = 'multiOTP';
        $this->_version                  = '4.0.0beta5'; // You should add a suffix for your changes (for example 4.0.0-andy-07)
        $this->_date                     = '2013-05-18'; // You should add a suffix for your changes (for example YYYY-MM-DD / YYY2-M2-D2)
        $this->_copyright                = '(c) 2010-2013 SysCo systemes de communication sa'; // This is a copyright, don't change it !
        $this->_website                  = 'http://www.multiOTP.net'; // Website dedicated to this LGPL library, please don't change it !
        
        $this->_log_header_written       = FALSE; // Flag indicating if the header has already been written in the log file or not
        $this->_valid_algorithms        = '*mOTP*HOTP*TOTP*'; // Supported algorithms, don't change it (unless you have added the handling of a new algorithm ;-)
        $this->_attributes_to_encrypt   = '*user_pin*token_seed*seed_password*sms_password*sms_userkey*sms_api_id*sms_otp*'; // This default list of attributes can be changed using SetAttributesToEncrypt(). Each attribute must be between "*".
        if ('' == $encryption_key)
        {
            $this->_encryption_key = 'MuLtIoTpEnCrYpTiOn'; // This default value should be changed for each project using SetEncryptionKey()
        }
        else
        {
            $this->_encryption_key = $encryption_key;
        }
        $this->_source_ip               = '';
		
        $this->_user                    = ''; // Name of the current user to authenticate
        $this->_user_data_read_flag     = FALSE; // Flag to know if the data concerning the current user has been read
        $this->_users_folder            = ''; // Folders which contain the users flat files
		
        $this->_log_file_name           = 'multiotp.log';
        $this->_log_flag                = FALSE;
        $this->_log_folder              = ''; // Folder which contains the log file
        $this->_log_verbose_flag        = FALSE;

        // MySQL settings (optional)
        $this->_sql_server              = ''; // The SQL server to use. If empty all information will be written to files
        $this->_sql_user                = ''; // Username for the SQL server.
        $this->_sql_passwd              = ''; // Password for the SQL server.
        $this->_sql_db                  = ''; // The database to use.
        $this->_sql_log_table           = 'multiotp_logs'; // Table name for log. If empty log will be written to file
        $this->_sql_users_table         = 'multiotp_users'; // Table name for users. If empty users will be written to files
        $this->_sql_tokens_table        = 'multiotp_tokens'; // Table name for tokens. If empty tokens will be written to files
        $this->_sql_config_table        = 'multiotp_config'; // Table name for config. If empty config will be written to files
        
        // Reset/initialize the user array
        $this->ResetUserArray();
        
        // Reset/initialize the token array
        $this->ResetTokenArray();

        // Reset/initialize the config array
        $this->ResetConfigArray();
        
        // Reset/initialize the errors text array
        $this->ResetErrorsArray();

        $this->ReadConfigData();
    }

    
    /*********************************************************************
     *
     * Name: ShowStatus
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
     *     ShowStatus($x, 100);
     *     usleep(100000);
     * }
     *
     * @param   int     $done   how many items are completed
     * @param   int     $total  how many items are to be done total
     * @param   int     $size   optional size of the status bar
     * @return  void
     *
     *********************************************************************/
    function ShowStatus($done, $total, $size=30)
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
    
    
    function SetConfigFolder($folder, $create = TRUE, $read_config = TRUE)
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
    {
        $config_folder = $this->ConvertToWindowsPathIfNeeded($this->_config_folder);
        if ('' == $config_folder)
        {
            $this->SetConfigFolder($this->GetScriptFolder()."config/", $create_if_not_exist);
        }
        elseif (!file_exists($config_folder))
        {
            if (!@mkdir($config_folder))
            {
                $this->WriteLog("Error: unable to create the missing config folder ".$config_folder);
            }
        }
        return $this->ConvertToWindowsPathIfNeeded($this->_config_folder);
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
       
        $this->_errors_text[41] = "ERROR: Bad MySQL parameters";
        
        $this->_errors_text[50] = "ERROR: QRcode not created";
        $this->_errors_text[51] = "ERROR: UrlLink not created (no provisionable client for this protocol)";

        $this->_errors_text[60] = "ERROR: No information where to send SMS code";
        $this->_errors_text[61] = "ERROR: SMS code request received, but an error occured during transmission";
        $this->_errors_text[62] = "ERROR: SMS provider not supported";
        
        $this->_errors_text[99] = "ERROR: Authentication failed (and other possible unknown errors)";
    }
    
        
    // Reset the config array
    function ResetConfigArray()
    {
        // Maximum number of event gaps accepted for event based algorithm(s) token
        $this->_config_data['max_event_window'] = 100;

        // Maximum number of events accepted to sync event based algorithm(s) token
        $this->_config_data['max_event_resync_window'] = 10000;

        // Maximum time window to be accepted, in seconds (+/-)
        // Initialized to a little bit more than +/- 10 minutes
        // (was 8000 seconds in version 3.x, and Stefan Kügler suggested to put a lower default value)
        $this->_config_data['max_time_window'] = 600;

        // Maximum time window (in seconds) to be accepted for resync (+/-)
        // Initialized to more than +/- one day
        $this->_config_data['max_time_resync_window'] = 90000;

        // Number of consecutive failures before locking and delaying the next request
        $this->_config_data['max_delayed_failures'] = 3;

        // Locking delay in seconds between two trials after "max_delayed_failures" failures
        $this->_config_data['failure_delayed_time'] = 300;

        // Number of consecutive failures before blocking the token. A blocked token needs a resync
        $this->_config_data['max_block_failures'] = 6;

		// Prefix in order to have an Attribute = Value result in the debug mode
        $this->_config_data['verbose_log_prefix'] = "";
		
       // SMS provider
        $this->_config_data['sms_provider'] = '';
        
        // SMS originator/sender
        $this->_config_data['sms_originator'] = 'multiOTP';

        // SMS acccount/username/userkey
        $this->_config_data['sms_userkey'] = '';
        
        // SMS password
        $this->_config_data['sms_password'] = '';
        
        // SMS Api Id (for Clickatell only)
        $this->_config_data['sms_api_id'] = '';
        
        // SMS message prefix
        // $this->_config_data['sms_message_prefix'] = 'Your SMS-Code is:';
        $this->_config_data['sms_message_prefix'] = '%s is your SMS-Code';

        // SMS number of digits
        $this->_config_data['sms_digits'] = 6;

        // SMS timeout before authenticating (in seconds)
        $this->_config_data['sms_timeout'] = 180;

    }


    function ReadConfigData()
    {
        $result = FALSE;
        
        // We initialize the encryption hash to empty
        $this->_config_data['encryption_hash'] = '';
        
        if ('' != $this->_sql_server && '' != $this->_sql_config_table)
        {
            $link = mysql_connect($this->_sql_server, $this->_sql_user, $this->_sql_passwd) or $this->WriteLog(mysql_error());
            mysql_select_db($this->_sql_db);
            $sQuery  = "SELECT * FROM `{$this->_sql_config_table}` ";
            $rResult = mysql_query($sQuery, $link) or $this->WriteLog(mysql_error());
            $aRow    = mysql_fetch_assoc($rResult);
            mysql_close($link);
            while(list($key, $value) = @each($aRow))
            {
                if (FALSE !== strpos(strtolower($this->_attributes_to_encrypt), strtolower('*'.$key.'*')))
                {
                    $this->_config_data[$key] = $this->Decrypt($key,$value);
                }
                else
                {
                    $this->_config_data[$key] = $value;
                }
                $result = TRUE;
            }
        }
        else
        {
            $config_filename = 'multiotp.ini'; // File exists in v3 format only
            if (file_exists($this->GetConfigFolder().$config_filename))
            {
                $config_file_handler = fopen($this->GetConfigFolder().$config_filename, "rt");
                $first_line = trim(fgets($config_file_handler));
                
                while (!feof($config_file_handler))
                {
                    $line = str_replace(chr(10), '', str_replace(chr(13), '', fgets($config_file_handler)));
                    $line_array = explode("=",$line,2);
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
                fclose($config_file_handler);
                $result = TRUE;
            }
        }
        
        if ('' != $this->_config_data['encryption_hash'])
        {
            if ($this->_config_data['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey()))
            {
                $this->_config_data['encryption_hash'] = "ERROR";
                $result = FALSE;
            }
        }
        return $result;
    }


    function WriteConfigData()
    {
        $result = FALSE;
        $this->_config_data['encryption_hash'] = $this->CalculateControlHash($this->GetEncryptionKey());
        
        if ('' != $this->_sql_server && '' != $this->_sql_users_table)
        {
            // Connect to database
            $link = mysql_connect($this->_sql_server, $this->_sql_user, $this->_sql_passwd) or $this->WriteLog(mysql_error());
            mysql_select_db($this->_sql_db);
            reset($this->_config_data);
            $sQi_Columns = '';
            $sQi_Values  = '';
            $sQu_Data    = '';
            while(list($key, $value) = each($this->_config_data))
            {
                if ('' != trim($key))
                {
                    if (FALSE !== strpos(strtolower($this->_attributes_to_encrypt), strtolower('*'.$key.'*')))
                    {
                        $value = $this->Encrypt($key,$value);
                    }
                $sQu_Data    .= "`{$key}`='{$value}',"; // Data for UPDATE query
                $sQi_Columns .= "`{$key}`,"; // Columns for INSERT query
                $sQi_Values  .= "'{$value}',"; // Values for INSERT query
                }
            }
            $sQuery = "UPDATE `{$this->_sql_config_table}` SET ".substr($sQu_Data,0,-1);
            $rResult = mysql_query($sQuery, $link) or $this->WriteLog(mysql_error());
            if (0 == mysql_affected_rows($link))
            {
                $sQuery = "INSERT INTO `{$this->_sql_config_table}` (".substr($sQi_Columns,0,-1).") VALUES (".substr($sQi_Values,0,-1).")";
                $rResult = mysql_query($sQuery, $link) or $this->WriteLog(mysql_error());
                if (0 == mysql_affected_rows($link))
                {
                    $this->WriteLog("Error: SQL database entry for config cannot be created or changed");
                }
                else
                {
                    $result = TRUE;
                }
            }
            else
            {
                $result = TRUE;
            }
            mysql_close($link);
        }
        else
        {
            $config_filename = 'multiotp.ini';
            if (!($config_file_handler = fopen($this->GetConfigFolder(TRUE).$config_filename, "wt")))
            {
                $this->WriteLog("Error: database file for config cannot be written");
            }
            else
            {
                fwrite($config_file_handler,"multiotp-database-format-v3"."\n");
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
            }
        }
        return $result;
    }


    // Reset the user array
    function ResetUserArray()
    {
        // User is a special multi-account user (the real user is in the token, like this: "user[space]token"
        $this->_user_data['multi_account'] = 0;

        // User encryption hash
        $this->_user_data['encryption_hash'] = '';

        // User pin
        $this->_user_data['user_pin'] = '';
        
        // User email
        $this->_user_data['email'] = '';
        
        // User description
        $this->_user_data['description'] = '';
        
        // User seed password, hexadecimal coded
        $this->_user_data['seed_password'] = '';
        
        // User sms number
        $this->_user_data['sms'] = '';
        
        // User sms otp
        $this->_user_data['sms_otp'] = '';
        
        // User sms otp validity
        $this->_user_data['sms_validity'] = 0;
        
        // Algorithm used by the token
        $this->_user_data['algorithm'] = '';
        
        // Time interval in seconds for a time based token
        $this->_user_data['time_interval'] = 0;
        
        // Number of digits returned by the token
        $this->_user_data['number_of_digits'] = 6;
        
        // Request the pin as a prefix of the rturned token value
        $this->_user_data['request_prefix_pin'] = 0;
        
        // Last successful login
        $this->_user_data['last_login'] =  0;
        
        // Last successful event
        $this->_user_data['last_event'] = -1;
        
        // Last error login
        $this->_user_data['last_error'] =  0;
        
        // Delta time in seconds for a time based token
        $this->_user_data['delta_time'] = 0;
        
        // Key identification number, if any
        $this->_user_data['key_id'] = '';

        // Token seed, default set to the RFC test seed, hexadecimal coded
        $this->_user_data['token_seed'] = '3132333435363738393031323334353637383930';

        // Token serial number
        $this->_user_data['token_serial'] = '';

        // Login error counter
        $this->_user_data['error_counter'] = 0;

        // Token locked
        $this->_user_data['locked'] = 0;

        // The user data array is not read actually
        $this->SetUserDataReadFlag(FALSE);
    }

    
    function ResetTokenArray()
    {
        // Token encryption hash
        $this->_token_data['encryption_hash'] = '';
        $this->_token_data['manufacturer'] = 'multiOTP';
        $this->_token_data['token_serial'] = '';
        $this->_token_data['issuer'] = 'multiOTP';
        $this->_token_data['key_algorithm'] = '';
        $this->_token_data['algorithm'] = '';
        $this->_token_data['otp'] = '';
        $this->_token_data['format'] = '';
        
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

    
    function SetSourceIp($value)
    {
        $this->_source_ip = $value;
    }
    

    function GetSourceIp()
    {
        return $this->_source_ip;
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
        $this->_config_data[$attribute] = $value;
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


    /*********************************************************************
     *
     * Name: DefineMySqlConnection
     * Short description: Define the SQL parameters for the MySQL backend
     *
     * Creation 2010-12-18
     * Update 2011-07-06
     * @package multiotp
     * @version 3.2.0
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
        // MySQL settings (optional)
        $this->_sql_server              = $sql_server;
        $this->_sql_user                = $sql_user;
        $this->_sql_passwd              = $sql_passwd;
        $this->_sql_db                  = $sql_db;
        
        // If table names are not defined, we keep the default value defined in the class constructor.
        if (NULL !== $sql_log_table)
        {
            $this->_sql_log_table           = $sql_log_table;
        }
        if (NULL !== $sql_users_table)
        {
            $this->_sql_users_table         = $sql_users_table;
        }
        if (NULL !== $sql_tokens_table)
        {
            $this->_sql_tokens_table        = $sql_tokens_table;
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


    function Encrypt($key, $value)
    {
        $result = '';
        if (strlen($this->_encryption_key) > 0)
        {
            for ($i=0;  $i < strlen($value); $i++)
            {
                $encrypt_char = ord(substr($this->_encryption_key,$i % strlen($this->_encryption_key),1));
                $key_char = ord(substr($key,$i % strlen($key),1));
                $result .= chr($encrypt_char^$key_char^ord(substr($value,$i,1)));
            }
            $result = base64_encode($result);
        }
        else
        {
            $result = $value;
        }
        return $result;
    }
    
    
    function Decrypt($key, $value)
    {
        $result = '';
        if (strlen($this->_encryption_key) > 0)
        {
            $value_to_decrypt = base64_decode($value);
            for ($i=0;  $i < strlen($value_to_decrypt); $i++)
            {
                $encrypt_char = ord(substr($this->_encryption_key,$i % strlen($this->_encryption_key),1));
                $key_char = ord(substr($key,$i % strlen($key),1));
                $result .= chr($encrypt_char^$key_char^ord(substr($value_to_decrypt,$i,1)));
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
    function CreateUser($user, $request_prefix_pin, $algorithm, $seed = '', $pin = '', $number_of_digits = 6, $time_interval_or_next_event = 30, $email = '', $sms = '', $description = '')
    {
        if ($this->ReadUserData($user, TRUE) || ('' == $user))
        {
            return FALSE; // ERROR: user already exists, or user is not set
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
            $this->SetUserSms($sms);
            $this->SetUserDescription($description);
            
            return $this->WriteUserData();
        }
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
    function CreateUserFromToken($user, $token, $email = '', $sms = '', $pin = '', $request_prefix_pin = 0, $description = '')
    {
        if ($this->ReadUserData($user, TRUE) || ('' == $user))
        {
            $result = FALSE;
        }
        elseif (!$this->ReadTokenData($token))
        {
            $result = FALSE;
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
            $this->SetUserSms($sms);
            $this->SetUserDescription($description);
            
            if ($this->WriteUserData())
            {
                $result = TRUE;
            }
            else
            {
                $result = FALSE;
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
    function GetUserTokenQrCode($user = '', $display_name = '', $file_name = 'binary',$ga_encode=true)
    {
        $result = FALSE;
        if ('' != $user)
        {
            $this->SetUser($user);
        }
        if (!function_exists('ImageCreate'))
        {
            $result = FALSE;
        }
        
        elseif ($this->ReadUserData())
        {
            $the_user       = $this->GetUser();
            $q_algorithm    = $this->GetUserAlgorithm();
            $q_period       = $this->GetUserTokenTimeInterval();
            $q_digits       = $this->GetUserTokenNumberOfDigits();
            $q_seed         = $this->GetUserTokenSeed();
            $q_counter      = $this->GetUserTokenLastEvent() + 1;
            $q_display_name = (('' != $display_name)?$display_name:$the_user);
            
            if($ga_encode){
              $q_seed=base32_encode(hex2bin($q_seed));
            }else{
              $q_seed=hex2bin($q_seed);
            }

            switch (strtolower($q_algorithm))
            {
                case 'totp':
                    $result = $this->qrcode('otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?period='.$q_period.'&digits='.$q_digits.'&secret='.$q_seed, $file_name);
                    break;
                case 'hotp':
                    $result = $this->qrcode('otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?counter='.$q_counter.'&digits='.$q_digits.'&secret='.$q_seed, $file_name);
                    break;
                    /*
                case 'motp':
                    $result = $this->qrcode('otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?counter='.$q_counter.'&digits='.$q_digits.'&secret='.base32_encode(hex2bin($q_seed)), $file_name);
                    break;
                */
                default:
                    $result = $this->qrcode('http://www.multiotp.net/no_qrcode_compatible_client_for_this_algorithm', $file_name);
                    $result = FALSE;
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
        if ($this->ReadTokenData())
        {
            $the_token      = $this->GetToken();
            $q_algorithm    = $this->GetTokenAlgorithm();
            $q_period       = $this->GetTokenTimeInterval();
            $q_digits       = $this->GetTokenNumberOfDigits();
            $q_seed         = $this->GetTokenSeed();
            $q_counter      = $this->GetTokenLastEvent() + 1;
            $q_display_name = (('' != $display_name)?$display_name:$the_token);

            switch (strtolower($q_algorithm))
            {
                case 'totp':
                    $result = $this->qrcode('otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?period='.$q_period.'&digits='.$q_digits.'&secret='.base32_encode(hex2bin($q_seed)), $file_name);
                    break;
                case 'hotp':
                    $result = $this->qrcode('otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?counter='.$q_counter.'&digits='.$q_digits.'&secret='.base32_encode(hex2bin($q_seed)), $file_name);
                    break;
                /*
                case 'motp':
                    $result = $this->qrcode('otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?period='.$q_period.'&digits='.$q_digits.'&secret='.base32_encode(hex2bin($q_seed)), $file_name);
                    break;
                */
                default:
                    $result = $this->qrcode('http://www.multiotp.net/no_qrcode_compatible_client_for_this_algorithm', $file_name);
                    $result = FALSE;
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
    function GetUserTokenUrlLink($user = '', $display_name = '',$ga_encode=true)
    {
        $result = FALSE;
        if ('' != $user)
        {
            $this->SetUser($user);
        }

        if ($this->ReadUserData())
        {
            $the_user       = $this->GetUser();
            $q_algorithm    = $this->GetUserAlgorithm();
            $q_period       = $this->GetUserTokenTimeInterval();
            $q_digits       = $this->GetUserTokenNumberOfDigits();
            $q_seed         = $this->GetUserTokenSeed();
            $q_counter      = $this->GetUserTokenLastEvent() + 1;
            $q_display_name = (('' != $display_name)?$display_name:$the_user);
            
            if($ga_encode){
				$q_seed=base32_encode(hex2bin($q_seed));
			}else{
				$q_seed=hex2bin($q_seed);
			}

            switch (strtolower($q_algorithm))
            {
                case 'totp':
                    $result = 'otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?period='.$q_period.'&digits='.$q_digits.'&secret='.$q_seed;
                    break;
                case 'hotp':
                    $result = 'otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?counter='.$q_counter.'&digits='.$q_digits.'&secret='.$q_seed;
                    break;
                    /*
                case 'motp':
                    $result = 'otpauth://'.$q_algorithm.'/'.rawurlencode($q_display_name).'?counter='.$q_counter.'&digits='.$q_digits.'&secret=' . base32_encode(hex2bin($q_seed));
                    break;
                */
                default:
                    // $result = '';
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
        if ($this->ReadUserData($user, TRUE) || ('' == $user))
        {
            $result = FALSE;
        }
        else
        {
            if (!$this->CreateToken())
            {
                $result = FALSE;
            }
            else
            {
                $token = $this->GetToken();
                $result = $this->CreateUserFromToken($user, $token, $email, $sms);
            }
        }
        return $result;
    }


    function SetUser($user)
    {
        $this->ResetUserArray();
        $this->_user = $user;
        $this->ReadUserData('', TRUE); // First parameter empty, otherwise it will loop with SetUser !
   }

    
    function RenameCurrentUser($new_user)
    {
        $result = FALSE;
        if (CheckUserExists($new_user)) // Check if the new user already exists
        {
            $this->WriteLog("Error: unable to rename the current user ".$this->GetUser()." to ".$new_user." because it already exists");
        }
        else
        {
            if (!CheckUserExists()) // Check if the current user already exists
            {
                if ('' != $this->_sql_server && '' != $this->_sql_users_table)
                {
                    $link = mysql_connect($this->_sql_server, $this->_sql_user, $this->_sql_passwd) or $this->WriteLog(mysql_error());
                    mysql_select_db($this->_sql_db);
                    $sQuery = "UPDATE `{$this->_sql_users_table}` SET user='".new_user."' WHERE `user`='".$this->GetUser()."'";
                    $rResult = mysql_query($sQuery, $link) or $this->WriteLog(mysql_error());
                    $aRow    = mysql_fetch_assoc($rResult);
                    mysql_close($link);
                }
                else
                {
                    $old_user_filename = strtolower($this->GetUser()).'.db';
                    $new_user_filename = strtolower($new_user).'.db';
                    rename($this->GetUsersFolder().$old_user_filename, $this->GetUsersFolder().$new_user_filename);
                }
                return $result;
            }
            $this->_user = $user;
            $result = TRUE;
        }
        return $result;
    }


    function GetUser()
    {
        return $this->_user;
    }

    
    function CheckUserExists($user = '')
    {
        $check_user = ('' != $user)?$user:$this->GetUser();
        $result = FALSE;
        
        if ('' != $this->_sql_server && '' != $this->_sql_users_table)
        {
            $link = mysql_connect($this->_sql_server, $this->_sql_user, $this->_sql_passwd) or $this->WriteLog(mysql_error());
            mysql_select_db($this->_sql_db);
            $sQuery  = "SELECT * FROM `{$this->_sql_users_table}` WHERE `user` = '{$check_user}'";
            $rResult = mysql_query($sQuery, $link) or $this->WriteLog(mysql_error());
            $aRow    = mysql_fetch_assoc($rResult);
            mysql_close($link);
            $result = (0 < count($aRow));
        }
        else
        {
            $user_filename = strtolower($check_user).'.db';
            $result = file_exists($this->GetUsersFolder().$user_filename);
        }
        return $result;
    }


    function GetUsersList()
    {
        $users_list = '';
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
        if ("" != $result)
        {
            $this->_user_data['email'] = $result;
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

    
    function SetUserDescription($first_param, $second_param = "*-*")
    {
        $result = "";
        if ($second_param == "*-*")
        {
            if ('' == $first_param)
            {
                $result = $first_param;
            }
        }
        else
        {
            $this->SetUser($first_param);
            $result = $second_param;
        }
        if ("" != $result)
        {
            $this->_user_data['description'] = $result;
        }
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
            if (!$this->CheckTokenExists()) // Check if the current token already exists
            {
                if ('' != $this->_sql_server && '' != $this->_sql_tokens_table)
                {
                    $link = mysql_connect($this->_sql_server, $this->_sql_user, $this->_sql_passwd) or $this->WriteLog(mysql_error());
                    mysql_select_db($this->_sql_db);
                    $sQuery = "UPDATE `{$this->_sql_tokens_table}` SET token_id='".new_token."' WHERE `token_id`='".$this->GetToken()."'";
                    $rResult = mysql_query($sQuery, $link) or $this->WriteLog(mysql_error());
                    $aRow    = mysql_fetch_assoc($rResult);
                    mysql_close($link);
                }
                else
                {
                    $old_token_filename = strtolower($this->GetToken()).'.db';
                    $new_token_filename = strtolower($new_token).'.db';
                    rename($this->GetTokensFolder().$old_token_filename, $this->GetTokensFolder().$new_token_filename);
                }
                return $result;
            }
            $this->_token = $token;
            $result = TRUE;
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
        
        if ('' != $this->_sql_server && '' != $this->_sql_tokens_table)
        {
            $link = mysql_connect($this->_sql_server, $this->_sql_user, $this->_sql_passwd) or $this->WriteLog(mysql_error());
            mysql_select_db($this->_sql_db);
            $sQuery  = "SELECT * FROM `{$this->_sql_tokens_table}` WHERE `token_id` = '{$check_token}'";
            $rResult = mysql_query($sQuery, $link) or $this->WriteLog(mysql_error());
            $aRow    = mysql_fetch_assoc($rResult);
            mysql_close($link);
            $result = (0 < count($aRow));
        }
        else
        {
            $token_filename = strtolower($check_token).'.db';
            $result = file_exists($this->GetTokensFolder().$token_filename);
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
        return $tokens_list;
    }


    function SetTokenDataReadFlag($flag)
    {
        $this->_token_data_read_flag = $flag;
    }
    
    
    function GetTokenDataReadFlag()
    {
        return $this->_token_data_read_flag;
    }
    
    
    function GetScriptFolder()
    {
        // Detect the current folder, change Windows notation to universal notation if needed
        //echo  isset($_SERVER['SCRIPT_FILENAME'])?$_SERVER['SCRIPT_FILENAME']:'';exit;
        $current_folder = $this->ConvertToUnixPath(dirname(__FILE__));
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
            $current_script_folder_detected = dirname(__FILE__);//dirname($current_script_folder);
        }

        if (substr($current_script_folder_detected,-1) != "/")
        {
            $current_script_folder_detected.="/";
        }
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


    function WriteLog($log_info)
    {
        if ($this->_log_flag)
        {
            if ('' != $this->_sql_server && '' != $this->_sql_log_table)
            {
                $log_link = mysql_connect($this->_sql_server, $this->_sql_user, $this->_sql_passwd) or $this->WriteLog(mysql_error());
                mysql_select_db($this->_sql_db);
                $sQuery  = "INSERT INTO `{$this->_sql_log_table}` (`datetime`,`user`,`logentry`) VALUES ('".date("Y-m-d H:i:s")."','{$this->_user}','{$log_info}')";
                mysql_query($sQuery, $log_link);
                mysql_close($log_link);
            }
            else
            {
            if (!file_exists($this->GetLogFolder()))
            {
                    @mkdir($this->_log_flag);
                }
                $log_file_handle = fopen($this->GetLogFolder().$this->_log_file_name,"ab+");
                if (!$this->_log_header_written)
                {
                    fwrite($log_file_handle,str_repeat("=",40)."\n");
                    fwrite($log_file_handle,'multiotp '.$this->GetVersion()."\n");
                    $this->_log_header_written = TRUE;
                }
                fwrite($log_file_handle,date("Y-m-d H:i:s")." ".$log_info."\n");
                fclose($log_file_handle);
            }
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
        
        if ('' != $this->_sql_server && '' != $this->_sql_tokens_table)
        {
            // Connect to database
            $link = mysql_connect($this->_sql_server, $this->_sql_user, $this->_sql_passwd) or $this->WriteLog(mysql_error());
            mysql_select_db($this->_sql_db);
            $sQuery  = "DELETE FROM `{$this->_sql_tokens_table}` WHERE `token` = '{$this->_token}'";
            $rResult = mysql_query($sQuery, $link) or $this->WriteLog("Error: Could not delete token ".$this->_token." ".mysql_error());
            if (0 == mysql_affected_rows($link))
            {
                $this->WriteLog("Error: Could not delete token ".$this->_token.". Token does not exist");
            }
            else
            {
                $this->WriteLog("Information: token ".$this->_token." successfully deleted");
                $result = TRUE;
            }
            mysql_close($link);
        }
        else
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
                    $this->WriteLog("Information: token ".$this->_token." successfully deleted");
                }
                else
                {
                    $this->WriteLog("Error: unable to delete token ".$this->_token);
                }
            }
        }
        return $result;
    }


    function DeleteUser($user = '')
    {
        if ('' != $user)
        {
            $this->SetUser($user);
        }
        
        $result = FALSE;
        
        if ('' != $this->_sql_server && '' != $this->_sql_users_table)
        {
            // Connect to database
            $link = mysql_connect($this->_sql_server, $this->_sql_user, $this->_sql_passwd) or $this->WriteLog(mysql_error());
            mysql_select_db($this->_sql_db);
            $sQuery  = "DELETE FROM `{$this->_sql_users_table}` WHERE `user` = '{$this->_user}'";
            $rResult = mysql_query($sQuery, $link) or $this->WriteLog("Error: Could not delete user ".$this->_user." ".mysql_error());
            if (0 == mysql_affected_rows($link))
            {
                $this->WriteLog("Error: Could not delete user ".$this->_user.". User does not exist");
            }
            else
            {
                $this->WriteLog("Information: user ".$this->_user." successfully deleted");
                $result = TRUE;
            }
            mysql_close($link);
        }
        else
        {
            $user_filename = strtolower($this->_user).'.db';
            if (!file_exists($this->GetUsersFolder().$user_filename))
            {
                $this->WriteLog("Error: unable to delete user ".$this->_user.", database file ".$this->GetUsersFolder().$user_filename." does not exist");
            }
            else
            {
                $result = unlink($this->GetUsersFolder().$user_filename);
                if ($result)
                {
                    $this->WriteLog("Information: user ".$this->_user." successfully deleted");
                }
                else
                {
                    $this->WriteLog("Error: unable to delete user ".$this->_user);
                }
            }
        }
        return $result;
    }


    function ReadUserData($user = '', $create = FALSE)
    {
        if ('' != $user)
        {
            $this->SetUser($user);
        }
        $result = FALSE;
        // We initialize the encryption hash to empty
        $this->_user_data['encryption_hash'] = '';
        
        if ('' != $this->_sql_server && '' != $this->_sql_users_table)
        {
            $link = mysql_connect($this->_sql_server, $this->_sql_user, $this->_sql_passwd) or $this->WriteLog(mysql_error());
            mysql_select_db($this->_sql_db);
            $sQuery  = "SELECT * FROM `{$this->_sql_users_table}` WHERE `user` = '{$this->_user}'";
            $rResult = mysql_query($sQuery, $link) or $this->WriteLog(mysql_error());
            $aRow    = mysql_fetch_assoc($rResult);
            mysql_close($link);
            while(list($key, $value) = @each($aRow))
            {
                if (FALSE !== strpos(strtolower($this->_attributes_to_encrypt), strtolower('*'.$key.'*')))
                {
                    $this->_user_data[$key] = $this->Decrypt($key,$value);
                }
                else
                {
                    $this->_user_data[$key] = $value;
                }
                $result = TRUE;
            }
            if(0 == count($aRow) && !$create)
            {
                $this->WriteLog("Error: SQL database entry for user ".$this->_user." does not exist");
            }
        }
        else
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
            }
        }
        
        if ('' != $this->_user_data['encryption_hash'])
        {
            if ($this->_user_data['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey()))
            {
                $this->_user_data['encryption_hash'] = "ERROR";
                $result = FALSE;
            }
        }

        $this->SetUserDataReadFlag($result);
        return $result;
    }
    
    
    function WriteUserData()
    {
        $result = FALSE;
        $this->_user_data['encryption_hash'] = $this->CalculateControlHash($this->GetEncryptionKey());
        
        if ('' != $this->_sql_server && '' != $this->_sql_users_table)
        {
            // Connect to database
            $link = mysql_connect($this->_sql_server, $this->_sql_user, $this->_sql_passwd) or $this->WriteLog(mysql_error());
            mysql_select_db($this->_sql_db);
            reset($this->_user_data);
            $sQi_Columns = '';
            $sQi_Values  = '';
            $sQu_Data    = '';
            while(list($key, $value) = each($this->_user_data))
            {
                if ('' != trim($key))
                {
                    if (FALSE !== strpos(strtolower($this->_attributes_to_encrypt), strtolower('*'.$key.'*')))
                    {
                        $value = $this->Encrypt($key,$value);
                    }
                $sQu_Data    .= "`{$key}`='{$value}',"; // Data for UPDATE query
                $sQi_Columns .= "`{$key}`,"; // Columns for INSERT query
                $sQi_Values  .= "'{$value}',"; // Values for INSERT query
                }
            }
            $sQuery = "UPDATE `{$this->_sql_users_table}` SET ".substr($sQu_Data,0,-1)." WHERE `user`='{$this->_user}'";
            $rResult = mysql_query($sQuery, $link) or $this->WriteLog(mysql_error());
            if (0 == mysql_affected_rows($link))
            {
                $sQuery = "INSERT INTO `{$this->_sql_users_table}` (`user`,".substr($sQi_Columns,0,-1).") VALUES ('{$this->_user}',".substr($sQi_Values,0,-1).")";
                $rResult = mysql_query($sQuery, $link) or $this->WriteLog(mysql_error());
                if (0 == mysql_affected_rows($link))
                {
                    $this->WriteLog("Error: SQL database entry for user ".$this->_user." cannot be created or changed");
                }
                else
                {
                    $result = TRUE;
                }
            }
            else
            {
                $result = TRUE;
            }
            mysql_close($link);
        }
        else
        {
            $user_filename = strtolower($this->_user).'.db';
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
            }
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
        
        if ('' != $this->_sql_server && '' != $this->_sql_tokens_table)
        {
            $link = mysql_connect($this->_sql_server, $this->_sql_user, $this->_sql_passwd) or $this->WriteLog(mysql_error());
            mysql_select_db($this->_sql_db);
            $sQuery  = "SELECT * FROM `{$this->_sql_tokens_table}` WHERE `token_id` = '{$this->_token}'";
            $rResult = mysql_query($sQuery, $link) or $this->WriteLog(mysql_error());
            $aRow    = mysql_fetch_assoc($rResult);
            mysql_close($link);
            while(list($key, $value) = @each($aRow))
            {
                if (FALSE !== strpos(strtolower($this->_attributes_to_encrypt), strtolower('*'.$key.'*')))
                {
                    $this->_token_data[$key] = $this->Decrypt($key,$value);
                }
                else
                {
                    $this->_token_data[$key] = $value;
                }
                $result = TRUE;
            }
            if(0 == count($aRow) && !$create)
            {
                $this->WriteLog("Error: SQL database entry for token ".$this->_token." does not exist");
            }
        }
        else
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
            }
        }
        
        if ('' != $this->_token_data['encryption_hash'])
        {
            if ($this->_token_data['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey()))
            {
                $this->_token_data['encryption_hash'] = "ERROR";
                $result = FALSE;
            }
        }
        
        $this->SetTokenDataReadFlag($result);
        return $result;
    }
    
    
    function WriteTokenData()
    {
        $result = FALSE;
        $this->_token_data['encryption_hash'] = $this->CalculateControlHash($this->GetEncryptionKey());
        
        if ('' != $this->_sql_server && '' != $this->_sql_tokens_table)
        {
            // Connect to database
            $link = mysql_connect($this->_sql_server, $this->_sql_user, $this->_sql_passwd) or $this->WriteLog(mysql_error());
            mysql_select_db($this->_sql_db);
            reset($this->_token_data);
            $sQi_Columns = '';
            $sQi_Values  = '';
            $sQu_Data    = '';
            while(list($key, $value) = each($this->_token_data))
            {
                if ('' != trim($key))
                {
                    if (FALSE !== strpos(strtolower($this->_attributes_to_encrypt), strtolower('*'.$key.'*')))
                    {
                        $value = $this->Encrypt($key,$value);
                    }
                $sQu_Data    .= "`{$key}`='{$value}',"; // Data for UPDATE query
                $sQi_Columns .= "`{$key}`,"; // Columns for INSERT query
                $sQi_Values  .= "'{$value}',"; // Values for INSERT query
                }
            }
            $sQuery = "UPDATE `{$this->_sql_tokens_table}` SET ".substr($sQu_Data,0,-1)." WHERE `token_id`='{$this->_token}'";
            $rResult = mysql_query($sQuery, $link) or $this->WriteLog(mysql_error());
            if (0 == mysql_affected_rows($link))
            {
                $sQuery = "INSERT INTO `{$this->_sql_tokens_table}` (`token_id`,".substr($sQi_Columns,0,-1).") VALUES ('{$this->_token}',".substr($sQi_Values,0,-1).")";
                $rResult = mysql_query($sQuery, $link) or $this->WriteLog(mysql_error());
                if (0 == mysql_affected_rows($link))
                {
                    $this->WriteLog("Error: SQL database entry for token ".$this->_token." cannot be created or changed");
                }
                else
                {
                    $result = TRUE;
                }
            }
            else
            {
                $result = TRUE;
            }
            mysql_close($link);
        }
        else
        {
            $token_filename = strtolower($this->_token).'.db';
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
            }
        }
        return $result;
    }


    /*********************************************************************
     *
     * Name: CheckToken
     * Short description: Check the token and give the result, with resync options
     *
     * Creation 2010-06-07
     * Update 2010-07-19
     * @package multiotp
     * @version 2.0.0
     * @author SysCo/al
     *
     * @param   string  $input       Token to check
     * @param   string  $input_sync  Second token to check for resync
     * @return  int                  Error code (0 = successful authentication, 1n = info, >= 20 = error)
     *
     *********************************************************************/
    function CheckToken($input = '', $input_sync = '', $display_status = FALSE, $ignore_lock = FALSE)
    {
        $calculated_token = '';
        $input_to_check = trim(str_replace('-','',$input));
        $real_user = $this->GetUser();
      
        if (!$this->ReadUserData($real_user))
        {
            $result = 21; // ERROR: user doesn't exist.
            $this->WriteLog("Error: user ".$this->GetUser()." doesn't exist");
            return $result;
        }
        else
        {
            $result = 99; // Unknown error

            if (1 == intval($this->GetUserMultiAccount()))
            {
                $pos = strrpos($input_to_check, " ");
                if ($pos !== FALSE)
                {
                    $real_user = substr($input_to_check,0,$pos);
                    $input_to_check = substr($input_to_check,$pos+1);
                }
                if (!$this->ReadUserData($real_user))
                {
                    $result = 34; // ERROR: linked user doesn't exist.
                    $this->WriteLog("Error: linked user ".$real_user." doesn't exist");
					return $result;
                }
            }
            
            $now_epoch = time();

            
            // Check if we have to validate an SMS code
            if ($this->GetUserSmsValidity() > $now_epoch)
            {
                if ($input_to_check == $this->GetUserSmsOtp())
                {
                    $this->SetUserSmsOtp(md5($this->GetEncryptionKey().$now_epoch)); // Now SMS code is no more available, and difficult to guess ;-)
                    $this->SetUserSmsValidity($now_epoch); // And the validity time is set to the successful login time

                    // And we are unlocking the regular token if needed
                    $this->SetUserErrorCounter(0);
                    $this->SetUserLocked(0);
                    // Finally, we DO NOT update the last login of the token when login with SMS
                    // $this->SetUserTokenLastLogin($now_epoch);
                    $result = 0; // OK: This is the correct token
                    $this->WriteLog("OK: user ".$this->GetUser()." successfully logged in with SMS token");
                    $this->WriteUserData();
                    return $result;
                }
            }


            if ("sms" == strtolower($input_to_check))
            {
                $sms_recipient = trim($this->GetUserSms());
                if ('' != $sms_recipient)
                {
                    $sms_provider = strtolower($this->GetSmsProvider());
                    $sms_message_prefix = trim($this->GetSmsMessage());
                    $sms_recipient = str_replace(' ','',$sms_recipient);
                    $sms_recipient = str_replace('(','',$sms_recipient);
                    $sms_recipient = str_replace(')','',$sms_recipient);
                    $sms_recipient = str_replace('+','00',$sms_recipient);
                    $sms_now_steps = $now_epoch;
                    $sms_digits = $this->GetSmsDigits();
                    $sms_seed_bin = hex2bin(md5('sMs'.$this->GetEncryptionKey().$real_user));
                    $sms_token = $this->ComputeOathTruncate($this->ComputeOathHotp($sms_seed_bin,$sms_now_steps),$sms_digits);
                    $this->SetUserSmsOtp($sms_token);
                    $this->SetUserSmsValidity($now_epoch + $this->GetSmsTimeout());
                    
                    if (9 <= $sms_digits)
                    {
                        $sms_nice_token = substr($sms_token,0,3).'-'.substr($sms_token,3,3).'-'.substr($sms_token,6,($sms_digits-6));
                    }
                    elseif (6 < $sms_digits)
                    {
                        $sms_nice_token = substr($sms_token,0,intval($sms_digits/2)).'-'.substr($sms_token,intval($sms_digits/2),$sms_digits);
                    }
                    else
                    {
                        $sms_nice_token = $sms_token;
                    }
                    if (FALSE !== strpos($sms_message_prefix, '%s'))
                    {
                        $sms_message_to_send = sprintf($sms_message_prefix, $sms_nice_token);
                    }
                    else
                    {
                        $sms_message_to_send = $sms_message_prefix.' '.$sms_nice_token;
                    }

                    if ("aspsms" == $sms_provider)
                    {
                        $sms_message = new MultiotpAspSms($this->GetSmsUserkey(), $this->GetSmsPassword());
                        $sms_message->setOriginator($this->GetSmsOriginator());
                        $sms_message->addRecipient($sms_recipient);
                        $sms_message->setContent($sms_message_to_send);
                        $sms_result = intval($sms_message->sendSMS());
                        
                        if (1 != $sms_result)
                        {
                            $result = 61; // ERROR: SMS code request received, but an error occured during transmission
                            $this->WriteLog("Info: SMS code request received for ".$real_user.", but the error ".$sms_result." occured during transmission to ".$sms_recipient);
                        }
                        else
                        {
                            $result = 18; // INFO: SMS code request received
                            $this->WriteLog("Info: SMS code request received for ".$real_user." and sent to ".$sms_recipient);
                        }
                    }
                    elseif ("clickatell" == $sms_provider)
                    {
                        $sms_message = new MultiotpClickatell($this->GetSmsUserkey(), $this->GetSmsPassword(), $this->GetSmsApiId());
                        $sms_message->setOriginator($this->GetSmsOriginator());
                        $sms_message->setRecipient($sms_recipient);
                        $sms_message->setContent($sms_message_to_send);
                        $sms_result = intval($sms_message->sendSMS());
                        
                        if (1 != $sms_result)
                        {
                            $result = 61; // ERROR: SMS code request received, but an error occured during transmission
                            $this->WriteLog("Info: SMS code request received for ".$real_user.", but the error ".$sms_result." occured during transmission to ".$sms_recipient);
                        }
                        else
                        {
                            $result = 18; // INFO: SMS code request received
                            $this->WriteLog("Info: SMS code request received for ".$real_user." and sent to ".$sms_recipient);
                        }
                    }
                    elseif ("intellisms" == $sms_provider)
                    {
                        $sms_message = new MultiotpIntelliSms($this->GetSmsUserkey(), $this->GetSmsPassword());
                        $sms_message->setOriginator($this->GetSmsOriginator());
                        $sms_message->setRecipient($sms_recipient);
                        $sms_message->setContent($sms_message_to_send);
                        $sms_result = $sms_message->sendSMS();
                        
                        if ("ID" != substr($sms_result,0,2))
                        {
                            $result = 61; // ERROR: SMS code request received, but an error occured during transmission
                            $this->WriteLog("Info: SMS code request received for ".$real_user.", but the error ".$sms_result." occured during transmission to ".$sms_recipient);
                        }
                        else
                        {
                            $result = 18; // INFO: SMS code request received
                            $this->WriteLog("Info: SMS code request received for ".$real_user." and sent to ".$sms_recipient);
                        }
                    }
                    else
                    {
                        $result = 62; // ERROR: SMS provider not supported
                        $this->WriteLog("Error: SMS provider ".$sms_provider." not supported");
                    }
                }
                else
                {
                    $result = 60; // ERROR: no information where to send SMS code
                    $this->WriteLog("Error: no information where to send SMS code for ".$real_user);
                }
                $this->WriteUserData();
                return $result;
            }
            
            if ((1 == $this->GetUserLocked()) && ('' == $input_sync) && (!$ignore_lock))
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
                        if ('' == $input_sync)
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
                            if ($input_to_check == $calculated_token)
                            {
                                if ('' == $input_sync)
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
                                    $this->ShowStatus($check_step, $max_steps);
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
                        if ('' == $input_sync)
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
                            
                            if ($input_to_check == $calculated_token)
                            {
                                if ('' == $input_sync)
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
                                    $this->ShowStatus($check_step, $max_steps);
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
                        if ('' == $input_sync)
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
                            if ($input_to_check == $calculated_token)
                            {
                                if ('' == $input_sync)
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
                                    $this->ShowStatus($check_step, $max_steps);
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
			
			if ((99 == $result) && (strlen($input_to_check) == strlen($calculated_token)))
			{
				$this->WriteLog("(authentication typed by the user: ".$input_to_check.")");
			}
			
			if ($this->GetUserErrorCounter() >= $this->GetMaxBlockFailures())
			{
				$this->SetUserLocked(1);
			}
			$this->WriteUserData();
        } // end of the else block of the test: if (!$this->ReadUserData($real_user))
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
                        
                        $this->WriteLog("Information: Token with keyid ".$keyid." successfully imported");
                        if ($this->_log_verbose_flag)
                        {
                            reset($this->_token_data);
                            while(list($key, $value) = each($this->_token_data))
                            {
                                if ('' != $value)
                                {
                                    $this->WriteLog("  Token ".$keyid." - ".$key.": ".$value);
                                }
                            }
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
                            $this->WriteLog("Information: Token ".$this->GetToken()." already exists");
                        }
                        else
                        {
                            $result = $this->WriteTokenData() && $result;
                            $this->AddLastImportedToken($this->GetToken());
                            
                            $this->WriteLog("Information: Token with serial number ".$serialno." successfully imported");
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
                                $this->WriteLog("Information: Token ".$this->GetToken()." already exists");
                            }
                            else
                            {
                                $this->SetTokenManufacturer($manufacturer);
                                $this->SetTokenIssuer($manufacturer);
                                $this->SetTokenAlgorithm('HOTP');
                                $result = $this->WriteTokenData() && $result;
                                $this->AddLastImportedToken($this->GetToken());
                                $this->WriteLog("Information: Token ".$this->GetToken()." successfully imported");
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
                            $this->WriteLog("Information: Token ".$this->GetToken()." already exists");
                        }
                        else
                        {
                            $result = $this->WriteTokenData() && $result;
                            $this->AddLastImportedToken($this->GetToken());
                            $this->WriteLog("Information: Token ".$this->GetToken()." successfully imported");
                        }
                        $this->ResetTokenArray();
                    }
                }
            }
            fclose($data_file_handler);
        }
        return $result;
    }


    /******************************************************************************
     * Custom method providing qrcode generation, based on the library from
     * Y. Swetake (http://www.swetake.com/qr/index-e.html)
     *
     * A better library from Dominik Dzienia exists
     * (http://phpqrcode.sourceforge.net/), but it is not compatible with PHP 4.4.4
     *
     * If $file_name = "binary", send binary content without header
     ******************************************************************************/
    function qrcode($data = '', $file_name = '', $image_type = "P", $ecc_level = "Q", $module_size = 4, $version = 0, $structure_m = 0, $structure_n = 0, $parity = 0, $original_data = '')
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
        $path = $this->GetScriptFolder()."/qrcode/data";       // Adapted for multiOTP
        $image_path = $this->GetScriptFolder()."qrcode/image"; // Adapted for multiOTP
        
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
// End of class Multiotp
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
        trigger_error('XML Parsing Error at '.$line.':'.$col.'. Error '.$code.': '.xml_error_string($code));
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
// End of class MultiotpXMLTag
}


/*********************************************************************
 * Send SMS messages using ASPSMS infrastructure
 *
 * Name: MultiotpAspSms (original name: SMS)
 *
 * Copyright (C) 2002-2007 Oliver Hitz <oliver@net-track.ch>
 * Adapted for multiOTP, 2013-04-29
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

    function setAffiliateId($a)
    {
        $this->affiliateid = $a;
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
        $this->recipients[] = array( "number" => $r, "transaction" => $id );
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
// End of class MultiotpAspSms
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

        $this->servers = array( "api.clickatell.com:80" );
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
// End of class MultiotpClickatell
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

        $this->servers = array("www.intellisoftware.co.uk:80",
                               "www.intellisoftware2.co.uk:80" );
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
            list($host, $port) = explode(":", $server);
            $result = trim($this->sendToServer($msg, $host, $port));
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
        $fp = fsockopen($host, $port, $errno, $errdesc, $this->timeout);
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
// End of class MultiotpIntelliSms
}

?>

?>
