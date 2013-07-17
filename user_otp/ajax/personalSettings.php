<?php
/**
 * ownCloud - One Time Password plugin
 *
 * @package user_otp
 * @author Frank Bongrand
 * @copyright 2013 Frank Bongrand fbongrand@free.fr
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU AFFERO GENERAL PUBLIC
 * License along with this library. If not, see <http://www.gnu.org/licenses/>.
 * Displays <a href="http://opensource.org/licenses/AGPL-3.0">GNU AFFERO GENERAL PUBLIC LICENSE</a>
 * @license http://opensource.org/licenses/AGPL-3.0 GNU AFFERO GENERAL PUBLIC LICENSE
 *
 */

include_once("user_otp/lib/utils.php");

$l=OC_L10N::get('settings');

OCP\JSON::checkLoggedIn();
OCP\JSON::checkAppEnabled('user_otp');
OCP\JSON::callCheck();

// Get data
$mOtp =  new multiotp(OCP\Config::getAppValue(
    'user_otp','EncryptionKey','DefaultCliEncryptionKey')
);
$mOtp->EnableVerboseLog();
$mOtp->SetUsersFolder(
    OCP\Config::getAppValue(
        'user_otp',
        'UsersFolder',
        getcwd()."/apps/user_otp/lib/multiotp/users/"
    )
);

if(
   $_POST &&
   $_POST["otp_action"]==="delete_otp" &&
   $mOtp->CheckUserExists(OCP\User::getUser())
){
    if($mOtp->DeleteUser(OCP\User::getUser())){
        OCP\JSON::success(array("data" => array( "message" => $l->t("OTP Changed") )));
    }else{
        OCP\JSON::error(array("data" => array( "message" => $l->t("check apps folder rights") )));
    }
}else if (
    $_POST &&
    $_POST["otp_action"]==="create_otp" &&
    !$mOtp->CheckUserExists(OCP\User::getUser())
){
    // format token seed :
    if($_POST["UserTokenSeed"]===""){
		$GA_VALID_CHAR = "ABCDEFGHIJKLMNOPQRSTUVWXYZ234567";
        $UserTokenSeed=generateRandomString(16,256,8,$GA_VALID_CHAR);
    }else{
		$UserTokenSeed=$_POST["UserTokenSeed"];
	}
    if (OCP\Config::getAppValue('user_otp','TokenBase32Encode',true)){
        $UserTokenSeed=bin2hex(base32_decode($UserTokenSeed));
    }

    $result = $mOtp->CreateUser(
        OCP\User::getUser(),
        (OCP\Config::getAppValue('user_otp','UserPrefixPin','0')?1:0),
        OCP\Config::getAppValue('user_otp','UserAlgorithm','TOTP'),
        $UserTokenSeed,
        $_POST["UserPin"],
        OCP\Config::getAppValue('user_otp','UserTokenNumberOfDigits','6'),
        OCP\Config::getAppValue('user_otp','UserTokenTimeIntervalOrLastEvent','30')
    );
    if($result){
        OCP\JSON::success(array("data" => array( "message" => $l->t("OTP Changed") )));
    }else{
        OCP\JSON::error(array("data" => array( "message" => $l->t("check apps folder rights") )));
    }
}else{
    OCP\JSON::error(array("data" => array( "message" => $l->t("Invalid request") )));
}
