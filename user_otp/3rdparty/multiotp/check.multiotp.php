<?php
/**
 * @file  check.multiotp.php
 * @brief Check the implementation of some multiOTP functionnalities
 *
 * multiOTP - Strong two-factor authentication PHP class package
 *
 * PHP 4.4.4 or higher is supported.
 *
 * @author    Andre Liechti, SysCo systemes de communication sa, <developer@sysco.ch>
 * @version   4.0.4
 * @date      2013-08-20
 * @since     2013-07-10
 * @copyright (c) 2010-2013 by SysCo systemes de communication sa
 * @copyright GNU Lesser General Public License
 *
 *//*
 * 
 * Description
 *
 *   check.multiotp.php is a file implementing the Multiotp class
 *   in order to check the compliance with RFC4226. It must be
 *   placed in the same directory as the multiotp.class.php file.
 *
 *   WARNING! DO NOT FORGET TO REMOVE this test file from your disk
 *            when you go in production !
 *
 *
 * Usage
 *  
 *   The file must be placed in the same directory as multiotp.class.php
 *
 *
 * External file needed
 *
 *   multiotp.class.php
 *
 *
 * External file created
 *
 *   Multiotp class will create some internals folders and files
 *
 *
 * Licence
 *
 *   Copyright (c) 2010-2013, SysCo systèmes de communication sa
 *   SysCo (tm) is a trademark of SysCo systèmes de communication sa
 *   (http://www.sysco.ch/)
 *   All rights reserved.
 *
 *   This file is part of the multiOTP project.
 *
 *   This script is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU Lesser General Public
 *   License as published by the Free Software Foundation; either
 *   version 3 of the License, or (at your option) any later version.
 *
 *   This script is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *   Lesser General Public License for more details.
 *
 *   You should have received a copy of the GNU Lesser General Public
 *   License along with multiOTP PHP class.
 *   If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * Change Log
 *
 *   2013-07-10 4.0.4  SysCo/al Initial release
 ***************************************************************/

$first_time = time();

require_once('multiotp.class.php');

// Tests counter
$tests     = 0;

// Successes counter
$successes = 0;

$browser_mode = isset($_SERVER["HTTP_USER_AGENT"]);

// $crlf will skip a line in command line mode and also in browser mode
$crlf = $browser_mode?"<br />\n":"\n";
$b_on  = $browser_mode?'<b>':'';
$b_off = $browser_mode?'</b>':'';

// Declare and initialize the Multiotp class if not done by an other file including this one
If (!isset($multiotp))
{
	$multiotp = new Multiotp('DefaultCliEncryptionKey');
}
$multiotp->SetMaxEventResyncWindow(500); // 500 is enough and quicker for the check
$multiotp->EnableVerboseLog(); // Could be helpful at the beginning

// Write the configuration information in the configuration file
$multiotp->WriteConfigData();


//====================================================================
// Display header and version information
echo $crlf;
echo $b_on.$multiotp->GetClassName()." HOTP implementation check".$b_off.$crlf;
echo "(RFC 4226, http://www.ietf.org/rfc/rfc4226.txt)".$crlf;
echo "-----------------------------------------------".$crlf;
echo $crlf;
echo $multiotp->GetFullVersionInfo();
echo ", running with PHP version ".phpversion().$crlf;
echo $crlf;
echo "Valid algorithms: ".str_replace("\t",", ",$multiotp->GetAlgorithmsList()).$crlf;
echo $crlf;
echo "<hr />";
echo $crlf;


//====================================================================
// Delete the user test_user if it exists
echo "<i>";
echo "Deleting the test_user".$crlf;
if (!$multiotp->DeleteUser('test_user'))
{
    echo "- INFO: User test_user doesn't exist yet".$crlf;
}
else
{
    echo "- INFO: User test_user successfully deleted".$crlf;
}
echo "</i>";
echo $crlf;


//====================================================================
// Delete the token test_token if it exists
echo "<i>";
echo "Deleting the test_token".$crlf;
if (!$multiotp->DeleteToken('test_token'))
{
    echo "- INFO: Token test_token doesn't exist yet".$crlf;
}
else
{
    echo "- INFO: Token test_token successfully deleted".$crlf;
}
echo "</i>";
echo $crlf;


//====================================================================
// TEST: Creating token test_token with the RFC test values HOTP token
$tests++;
echo $b_on."Creating token test_token with the RFC test values HOTP token".$b_off.$crlf;
if ($multiotp->CreateToken('test_token', 'HOTP', '3132333435363738393031323334353637383930', 6, -1))
{
    echo "- OK! Token test_token sucessfully created".$crlf;
    $successes++;
}
else
{
    echo "- KO! Creation of test_token token failed".$crlf;
}
echo $crlf;


//====================================================================
// TEST: Creating user test_user with the HOTP RFC test token test_token created before
$tests++;
echo $b_on."Creating user test_user with the HOTP RFC test token test_token created before".$b_off.$crlf;
if (!$multiotp->CreateUserFromToken('test_user', 'test_token'))
{
    echo "- KO! Token test_token doesn't exist".$crlf;
}
else
{
    echo "- OK! User test_user sucessfully created with token test_token".$crlf;
    $successes++;
}
echo $crlf;


//====================================================================
// TEST: Authenticating test_user with the first token of the RFC test values
$tests++;
echo $b_on."Authenticating test_user with the first token of the RFC test values".$b_off.$crlf;
$multiotp->SetUser('test_user');
if (0 == ($error = $multiotp->CheckToken('755224')))
{
    echo "- OK! Token of the user test_user successfully accepted".$crlf;
    $successes++;
}
else
{
    echo "- KO! Error authenticating the user test_user with the first token".$crlf;
}
echo $crlf;


//====================================================================
// TEST: Testing the replay rejection
$tests++;
echo $b_on."Testing the replay rejection".$b_off.$crlf;
$multiotp->SetUser('test_user');
if (0 != ($error = $multiotp->CheckToken('755224')))
{
    echo "- OK! Token of the user test_user successfully REJECTED (replay)".$crlf;
    $successes++;
}
else
{
    echo "- KO! Replayed token *WRONGLY* accepted".$crlf;
}
echo $crlf;


//====================================================================
// TEST: Resynchronizing the key
$tests++;
echo $b_on."Resynchronizing the key".$b_off.$crlf;
$multiotp->SetUser('test_user');
if (14 == ($error = $multiotp->CheckToken('338314', '254676', (!$browser_mode))))
{
    echo "- OK! Token of the user test_user successfully resynchronized".$crlf;
    $successes++;
}
else
{
    echo "- KO! Token of the user test_user NOT resynchronized".$crlf;
}
echo $crlf;


//====================================================================
// TEST: Testing a false resynchronisation (in the past, may take some time)
$tests++;
echo $b_on."Testing a false resynchronisation (in the past, may take some time)".$b_off.$crlf;
$multiotp->SetUser('test_user');
$start_time = time();
if (14 != ($error = $multiotp->CheckToken('287082', '359152', (!$browser_mode))))
{
    echo "- OK! Token of test_user successfully NOT resynchronized (in the past), in less than ".(1+time()-$start_time)." second(s) ".$crlf;
    $successes++;
}
else
{
    echo "- KO! Token of user test_user *WRONGLY* resynchronized".$crlf;
}
echo $crlf;


//====================================================================
// TEST: Deleting the test_user2 if it exists
echo "<i>";
echo "Deleting the test_user2".$crlf;
if (!$multiotp->DeleteUser('test_user2'))
{
    echo "- INFO: User test_user2 doesn't exist yet".$crlf;
}
else
{
    echo "- INFO: User test_user2 successfully deleted".$crlf;
}
echo "</i>";
echo $crlf;


//====================================================================
// TEST: Creating user test_user2 with the RFC test values HOTP token and PIN prefix
$tests++;
echo $b_on."Creating user test_user2 with the RFC test values HOTP token and PIN prefix".$b_off.$crlf;
if ($multiotp->CreateUser('test_user2',1,'HOTP','3132333435363738393031323334353637383930','1234',6,0))
{
    echo "- OK! User test_user2 sucessfully created".$crlf;
    $successes++;
}
else
{
    echo "- KO! Creation of user test_user2 failed".$crlf;
}
echo $crlf;


//====================================================================
// TEST: Authenticating test_user2 with the first token of the RFC test values with PIN
$tests++;
echo $b_on."Authenticating test_user2 with the first token of the RFC test values with PIN".$b_off.$crlf;
$multiotp->SetUser('test_user2');
if (0 == ($error = $multiotp->CheckToken('1234755224')))
{
    echo "- OK! Token of the user test_user2 (with prefix PIN) successfully accepted".$crlf;
    $successes++;
}
else
{
    echo "- KO! Error #".$error." authenticating user test_user2 with the first token and PIN prefix".$crlf;
}
echo $crlf;


//====================================================================
// Delete the user fast_user if it exists
echo "<i>";
echo "Deleting the user fast_user".$crlf;
if (!$multiotp->DeleteUser('fast_user'))
{
    echo "- INFO: User fast_user doesn't exist yet".$crlf;
}
else
{
    echo "- INFO: User fast_user successfully deleted".$crlf;
}
echo "</i>";
echo $crlf;


//====================================================================
// Delete the user fast_user_renamed if it exists
echo "<i>";
echo "Deleting the user fast_user_renamed".$crlf;
if (!$multiotp->DeleteUser('fast_user_renamed'))
{
    echo "- INFO: User fast_user_renamed doesn't exist yet".$crlf;
}
else
{
    echo "- INFO: User fast_user_renamed successfully deleted".$crlf;
}
echo "</i>";
echo $crlf;


//====================================================================
// TEST: Creating user fast_user using the one parameter FastCreateUser() function
$tests++;
echo $b_on."Creating user fast_user using the one parameter FastCreateUser() function".$b_off.$crlf;
if ($multiotp->FastCreateUser('fast_user'))
{
    echo "- OK! User fast_user sucessfully created".$crlf;
    $successes++;
}
else
{
    echo "- KO! Creation of user fast_user failed".$crlf;
}
echo $crlf;


//====================================================================
// TEST: Check if user fast_user exists
$tests++;
echo $b_on."Check if the user fast_user exists".$b_off.$crlf;

if ($multiotp->CheckUserExists('fast_user'))
{
    echo "- OK! User fast_user exists".$crlf;
    $successes++;
}
else
{
    echo "- KO! fast_user does not exist".$crlf;
}
echo $crlf;


//====================================================================
// TEST: Rename user fast_user
$tests++;
echo $b_on."Renaming the user fast_user to fast_user_renamed".$b_off.$crlf;

$multiotp->SetUser('fast_user');

if ($multiotp->RenameCurrentUser('fast_user_renamed'))
{
    echo "- OK! User fast_user successfully renamed to fast_user_renamed".$crlf;
    $successes++;
}
else
{
    echo "- KO! RenameCurrentUser functions failed".$crlf;
}
echo $crlf;


//====================================================================
// TEST: Check if user fast_user exists
$tests++;
echo $b_on."Check if the user fast_user does not exist".$b_off.$crlf;

if (!$multiotp->CheckUserExists('fast_user'))
{
    echo "- OK! User fast_user does not exist".$crlf;
    $successes++;
}
else
{
    echo "- KO! fast_user exist".$crlf;
}
echo $crlf;


//====================================================================
// TEST: Creating a QRcode provisioning file for the HOTP RFC test token
$tests++;
echo $b_on."Creating a QRcode provisioning file for the HOTP RFC test token".$b_off.$crlf;
$size_result = $multiotp->qrcode('otpauth://hotp/multiOTP hotp test?counter=0&digits=6&secret='.base32_encode(hex2bin('3132333435363738393031323334353637383930')), $multiotp->GetScriptFolder().'qrcode/qrHOTP.png');
if (0 < $size_result)
{
    echo "- OK! HOTP QRcode successfully created".$crlf;
    $successes++;
}
else
{
    echo "- KO! HOTP QRcode not created".$crlf;
}
echo $crlf;


//====================================================================
// TEST: Creating a QRcode provisioning file for the TOTP RFC test token
$tests++;
echo $b_on."Creating a QRcode provisioning file for the TOTP RFC test token".$b_off.$crlf;
$size_result = $multiotp->qrcode('otpauth://totp/multiOTP totp test?period=30&digits=6&secret='.base32_encode(hex2bin('3132333435363738393031323334353637383930')), $multiotp->GetScriptFolder().'qrcode/qrTOTP.png');
if (0 < $size_result)
{
    echo "- OK! TOTP QRcode successfully created".$crlf;
    $successes++;
}
else
{
    echo "- KO! TOTP QRcode not created".$crlf;
}
echo $crlf;


//====================================================================
// Display the QRcode in the browser using inline images
if ($browser_mode && ($size_result > 0))
{
    echo "Displaying inline image for the test_user (HOTP QRCode Google Auhtenticator token)".$crlf;
    echo "<img src=\"data:image/png;base64,".base64_encode($multiotp->GetUserTokenQrCode('test_user', 'multiOTP test_user token'))."\" alt=\"test_user test token\">".$crlf;
    echo $crlf;

    echo "Displaying inline image for TOTP QRCode Google Auhtenticator token".$crlf;
    $binary_result = $multiotp->qrcode('otpauth://totp/multiOTP totp test?period=30&digits=6&secret='.base32_encode(hex2bin('3132333435363738393031323334353637383930')), "binary");
    echo "<img src=\"data:image/png;base64,".base64_encode($binary_result)."\" alt=\"multiOTP TOTP test token\">".$crlf;
    echo $crlf;
}


//====================================================================
// TEST: Check Base32 functions
$tests++;
echo $b_on."Check Base32 functions".$b_off." (should return 3132333435363738393031323334353637383930)".$crlf;

if ('3132333435363738393031323334353637383930' == bin2hex(base32_decode(base32_encode(hex2bin('3132333435363738393031323334353637383930')))))
{
    echo "- OK! Base32 functions successfully checked".$crlf;
    $successes++;
}
else
{
    echo "- KO! Base32 functions failed".$crlf;
}
echo $crlf;


//====================================================================
// TESTS result
echo $b_on;
if ($successes == $tests)
{
    echo "OK! ALL $tests TESTS HAVE PASSED SUCCESSFULLY !".$crlf;
}
else
{
    echo "KO! ONLY $successes/$tests TESTS HAVE PASSED SUCCESSFULLY !".$crlf;
}
echo $b_off;
echo $crlf;

echo "<hr />";

echo $crlf;
echo $crlf;

//====================================================================
// List of existing tokens (originally tab separated)
echo $b_on."List of existing tokens".$b_off.$crlf;
echo str_replace("\t",", ",$multiotp->GetTokensList()).$crlf;
echo $crlf;


//====================================================================
// List of existing users (originally tab separated)
echo $b_on."List of existing users".$b_off.$crlf;
echo str_replace("\t",", ",$multiotp->GetUsersList()).$crlf;
echo $crlf;

echo "Time spent for the whole script: less than ".(1+time()-$first_time)." second(s)";
echo $crlf;
?>