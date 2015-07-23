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
$allTab[$i]['arrayConf'] = "mainConfig";$i++;

$allTab[$i]['name'] = "userotpSettings-2";
$allTab[$i]['label'] = "OTP Configuration";
$allTab[$i]['arrayConf'] = "mainConfigOtp";$i++;

// input type process general tab
$i=0;
//$mainConfig[$i]['name']='forceCreateUsers'; 
//$mainConfig[$i]['label']='Force user_otp backend to create new users?';
//$mainConfig[$i]['type']='checkbox';
//$mainConfig[$i]['default_value']=false; $i++;


$mainConfig[$i]['name']='authMethod'; 
$mainConfig[$i]['label']='Select authentication method';
$mainConfig[$i]['type']='radio';
$mainConfig[$i]['default_value']=_AUTH_DEFAULT_;
$mainConfig[$i]['values']['_AUTH_STANDARD_']['value']=_AUTH_STANDARD_;  
$mainConfig[$i]['values']['_AUTH_STANDARD_']['label']="Standard authentication";
if(!$encription_app){
$mainConfig[$i]['values']['_AUTH_OTP_OR_STANDARD_']['value']=_AUTH_OTP_OR_STANDARD_;  
$mainConfig[$i]['values']['_AUTH_OTP_OR_STANDARD_']['label']="Standard OR OTP authentication (User can use password OR OTP) ";
$mainConfig[$i]['values']['_AUTH_OTP_ONLY_']['value']=_AUTH_OTP_ONLY_;  
$mainConfig[$i]['values']['_AUTH_OTP_ONLY_']['label']="Replace password by OTP (User needs OTP to connect, if user is in the OTP db file) ";
}else{
  $mainConfig[$i]['label'].=' [Some authentication method are disabled due to file_encrytpion app is enabled]';
}
$mainConfig[$i]['values']['_AUTH_TWOFACTOR_']['value']=_AUTH_TWOFACTOR_;  
$mainConfig[$i]['values']['_AUTH_TWOFACTOR_']['label']="Two-factor authentication (User needs password AND OTP to connect, if user is in the OTP db file) ";
$i++;

//$mainConfig[$i]['name']='disableBackends'; 
//$mainConfig[$i]['label']='Disable other backends? (if checked user needs TOTP to connect if is user is in the TOTP db file)';
//$mainConfig[$i]['type']='checkbox';
//$mainConfig[$i]['default_value']=false; $i++;

// input type process tab OTP config
$i=0;
$mainConfigOtp[$i]['name']='EncryptionKey'; 
$mainConfigOtp[$i]['label']='Encryption Key (if left blank, it will be generated automatically)';
$mainConfigOtp[$i]['type']='text';
$VALID_CHAR = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghiklmnopqrstuvwxyz";
$mainConfigOtp[$i]['default_value']=generateRandomString(16,32,2,$VALID_CHAR); $i++;

$mainConfigOtp[$i]['name']='MaxBlockFailures'; 
$mainConfigOtp[$i]['label']='Max try before a temporary block';
$mainConfigOtp[$i]['type']='text';
$mainConfigOtp[$i]['default_value']='6'; $i++;

//~ $mainConfigOtp[$i]['name']='UsersFolder'; 
//~ $mainConfigOtp[$i]['label']='Users Folder';
//~ $mainConfigOtp[$i]['type']='text';
//~ $mainConfigOtp[$i]['default_value']=getcwd()."/apps/user_otp/lib/multiotp/users/"; $i++;

$mainConfigOtp[$i]['name']='UserPrefixPin'; 
$mainConfigOtp[$i]['label']='User Prefix Pin (add a 4 digit fix prefix before token)';
$mainConfigOtp[$i]['type']='checkbox';
$mainConfigOtp[$i]['default_value']=false; $i++;

$mainConfigOtp[$i]['name']='UserAlgorithm'; 
$mainConfigOtp[$i]['label']='User Algorithm (TOTP/HOTP)';
$mainConfigOtp[$i]['type']='select';
$mainConfigOtp[$i]['default_value']='TOTP'; 
$mainConfigOtp[$i]['values']['TOTP']['value']='TOTP';  
$mainConfigOtp[$i]['values']['TOTP']['label']="TOTP";
$mainConfigOtp[$i]['values']['HOTP']['value']='HOTP';  
$mainConfigOtp[$i]['values']['HOTP']['label']="HOTP";$i++;

//~ $mainConfigOtp[$i]['name']='TokenBase32Encode'; 
//~ $mainConfigOtp[$i]['label']='Token Base32 Encode (need for Google Authenticator)';
//~ $mainConfigOtp[$i]['type']='checkbox';
//~ $mainConfigOtp[$i]['default_value']=true; $i++;

$mainConfigOtp[$i]['name']='UserTokenNumberOfDigits'; 
$mainConfigOtp[$i]['label']='User Token Number Of Digits (must be 6 in order to works with Google Authenticator)';
$mainConfigOtp[$i]['type']='text';
$mainConfigOtp[$i]['default_value']='6'; $i++;

$mainConfigOtp[$i]['name']='UserTokenTimeIntervalOrLastEvent'; 
$mainConfigOtp[$i]['label']='<br/>User Token Time Interval (time in seconde between two TOTP) (must be 30 in order to works with Google Authenticator)<br/> Or Last Event (number of past HOTP) (If you’ve just re-initialised your Yubikey, then set this to 0) ';
$mainConfigOtp[$i]['type']='text';
$mainConfigOtp[$i]['default_value']='30'; $i++;

$mainConfigOtp[$i]['name']='UserTokenMaxEventWindow'; 
$mainConfigOtp[$i]['label']='User Token Max Event Window (default : 100)';
$mainConfigOtp[$i]['type']='text';
$mainConfigOtp[$i]['default_value']='100'; $i++;

$mainConfigOtp[$i]['name']='disableOtpOnRemoteScript'; 
$mainConfigOtp[$i]['label']='Disable OTP with remote.php (webdav and sync)';
$mainConfigOtp[$i]['type']='checkbox';
$mainConfigOtp[$i]['default_value']=true; $i++;

$mainConfigOtp[$i]['name']='disableDeleteOtpForUsers'; 
$mainConfigOtp[$i]['label']='Disable delete OTP for users (only regenerated)';
$mainConfigOtp[$i]['type']='checkbox';
$mainConfigOtp[$i]['default_value']=false; $i++;

$mainConfigOtp[$i]['name']='inputOtpAfterPwd'; 
$mainConfigOtp[$i]['label']='Used password field only and add OTP after the password';
$mainConfigOtp[$i]['type']='checkbox';
$mainConfigOtp[$i]['default_value']=false; $i++;

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
