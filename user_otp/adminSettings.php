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

OC_Util::checkAdminUser();

OCP\Util::addscript('user_otp', 'adminSettings');

$tmpl = new OCP\Template('user_otp', 'adminSettings');

//check if encription app is enabled :
$sql = 'SELECT * FROM `*PREFIX*appconfig` ' .
       'WHERE `appid` = ? AND configkey= ?';
$query = \OCP\DB::prepare($sql);
$result = $query->execute(array('files_encryption','enabled'));
$row = $result->fetchRow();
if($row && $row["configvalue"]==="yes"){
  $encription_app =true;
}else{
  $encription_app =false;
}

// configuration tab
$i=0;
$allTab[$i]['name'] = "userotpSettings-1";
$allTab[$i]['label'] = "Authenticator method";
$allTab[$i]['arrayConf'] = "config";$i++;

$allTab[$i]['name'] = "userotpSettings-2";
$allTab[$i]['label'] = "OTP Configuration";
$allTab[$i]['arrayConf'] = "configOtp";$i++;

// input type process general tab
$i=0;
//$config[$i]['name']='forceCreateUsers'; 
//$config[$i]['label']='Force user_otp backend to create new users?';
//$config[$i]['type']='checkbox';
//$config[$i]['default_value']=false; $i++;

$config[$i]['name']='authMethod'; 
$config[$i]['label']='Select authentication method';
$config[$i]['type']='radio';
$config[$i]['default_value']=_AUTH_DEFAULT_;
$config[$i]['values']['_AUTH_STANDARD_']['value']=_AUTH_STANDARD_;  
$config[$i]['values']['_AUTH_STANDARD_']['label']="Standard authentication";
if(!$encription_app){
$config[$i]['values']['_AUTH_OTP_OR_STANDARD_']['value']=_AUTH_OTP_OR_STANDARD_;  
$config[$i]['values']['_AUTH_OTP_OR_STANDARD_']['label']="Standard OR OTP authentication (User can use password OR OTP) ";
$config[$i]['values']['_AUTH_OTP_ONLY_']['value']=_AUTH_OTP_ONLY_;  
$config[$i]['values']['_AUTH_OTP_ONLY_']['label']="Replace password by OTP (User needs OTP to connect, if user is in the OTP db file) ";
}else{
  $config[$i]['label'].=' [Some authentication method are disabled due to file_encrytpion app is enabled]';
}
$config[$i]['values']['_AUTH_TWOFACTOR_']['value']=_AUTH_TWOFACTOR_;  
$config[$i]['values']['_AUTH_TWOFACTOR_']['label']="Two-factor authentication (User needs password AND OTP to connect, if user is in the OTP db file) ";
$i++;

//$config[$i]['name']='disableBackends'; 
//$config[$i]['label']='Disable other backends? (if checked user needs TOTP to connect if is user is in the TOTP db file)';
//$config[$i]['type']='checkbox';
//$config[$i]['default_value']=false; $i++;

// input type process tab OTP config
$i=0;
$configOtp[$i]['name']='EncryptionKey'; 
$configOtp[$i]['label']='Encryption Key (if left blank, it will be generated automatically)';
$configOtp[$i]['type']='text';
$VALID_CHAR = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghiklmnopqrstuvwxyz";
$configOtp[$i]['default_value']=generateRandomString(16,32,2,$VALID_CHAR); $i++;

$configOtp[$i]['name']='MaxBlockFailures'; 
$configOtp[$i]['label']='Max try before a temporary block';
$configOtp[$i]['type']='text';
$configOtp[$i]['default_value']='6'; $i++;

//~ $configOtp[$i]['name']='UsersFolder'; 
//~ $configOtp[$i]['label']='Users Folder';
//~ $configOtp[$i]['type']='text';
//~ $configOtp[$i]['default_value']=getcwd()."/apps/user_otp/lib/multiotp/users/"; $i++;

$configOtp[$i]['name']='UserPrefixPin'; 
$configOtp[$i]['label']='User Prefix Pin (add a 4 digit fix prefix before token)';
$configOtp[$i]['type']='checkbox';
$configOtp[$i]['default_value']=false; $i++;

$configOtp[$i]['name']='UserAlgorithm'; 
$configOtp[$i]['label']='User Algorithm (TOTP/HOTP)';
$configOtp[$i]['type']='select';
$configOtp[$i]['default_value']='TOTP'; 
$configOtp[$i]['values']['TOTP']['value']='TOTP';  
$configOtp[$i]['values']['TOTP']['label']="TOTP";
$configOtp[$i]['values']['HOTP']['value']='HOTP';  
$configOtp[$i]['values']['HOTP']['label']="HOTP";$i++;

//~ $configOtp[$i]['name']='TokenBase32Encode'; 
//~ $configOtp[$i]['label']='Token Base32 Encode (need for Google Authenticator)';
//~ $configOtp[$i]['type']='checkbox';
//~ $configOtp[$i]['default_value']=true; $i++;

$configOtp[$i]['name']='UserTokenNumberOfDigits'; 
$configOtp[$i]['label']='User Token Number Of Digits (must be 6 in order to works with Google Authenticator)';
$configOtp[$i]['type']='text';
$configOtp[$i]['default_value']='6'; $i++;

$configOtp[$i]['name']='UserTokenTimeIntervalOrLastEvent'; 
$configOtp[$i]['label']='<br/>User Token Time Interval (time in seconde between two TOTP) (must be 30 in order to works with Google Authenticator)<br/> Or Last Event (number of past HOTP) (If you’ve just re-initialised your Yubikey, then set this to 0) ';
$configOtp[$i]['type']='text';
$configOtp[$i]['default_value']='30'; $i++;

$configOtp[$i]['name']='UserTokenMaxEventWindow'; 
$configOtp[$i]['label']='User Token Max Event Window (default : 100)';
$configOtp[$i]['type']='text';
$configOtp[$i]['default_value']='100'; $i++;

$configOtp[$i]['name']='disableOtpOnRemoteScript'; 
$configOtp[$i]['label']='Disable OTP with remote.php (webdav and sync)';
$configOtp[$i]['type']='checkbox';
$configOtp[$i]['default_value']=true; $i++;

$configOtp[$i]['name']='disableDeleteOtpForUsers'; 
$configOtp[$i]['label']='Disable delete OTP for users (only regenerated)';
$configOtp[$i]['type']='checkbox';
$configOtp[$i]['default_value']=false; $i++;

$configOtp[$i]['name']='inputOtpAfterPwd'; 
$configOtp[$i]['label']='Used password field only and add OTP after the password';
$configOtp[$i]['type']='checkbox';
$configOtp[$i]['default_value']=false; $i++;

$configOtp[$i]['name']='bypassOtpOnIntranet';
$configOtp[$i]['label']='Disable OTP for intranet addresses';
$configOtp[$i]['type']='checkbox';
$configOtp[$i]['default_value']=false; $i++;

foreach ($allTab as $tab){
    foreach ($$tab["arrayConf"] as $input){
        switch ($input['type']){
            case "checkbox":
                if(isset($_POST['authMethod']) || isset($_POST['inputOtpAfterPwd'])){
                    if(isset($_POST[$input['name']])){
						OCP\Config::setAppValue('user_otp',$input['name'],true);
					}else{
						OCP\Config::setAppValue('user_otp',$input['name'],false);
					}
                }                 
                $tmpl->assign(
                    $input['name'],
                    OCP\Config::getAppValue(
                        'user_otp',
                        $input['name'],
                        $input['default_value']
                    )
                );
                break;
            default:
                if ($_POST && isset($_POST[$input['name']]) ) {        
					if($input['name']==="EncryptionKey" && $_POST[$input['name']]==""){
						$_POST[$input['name']]=$input['default_value'];
					}
                    OCP\Config::setAppValue('user_otp',$input['name'],$_POST[$input['name']]);
                }
                $tmpl->assign(
                    $input['name'],
                    OCP\Config::getAppValue(
                        'user_otp',$input['name'],$input['default_value']
                    )
                );
        }
    }
    $tmpl->assign($tab["arrayConf"],$$tab["arrayConf"]);
}
$tmpl->assign('allTab',$allTab);

return $tmpl->fetchPage();
