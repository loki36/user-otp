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

OCP\Util::addscript('user_otp', 'adminSettings');

$tmpl = new OCP\Template('user_otp', 'adminSettings');

// configuration tab
$i=0;
$allTab[$i]['name'] = "userotpSettings-1";
$allTab[$i]['label'] = "Configuration";
$allTab[$i]['arrayConf'] = "config";$i++;

$allTab[$i]['name'] = "userotpSettings-2";
$allTab[$i]['label'] = "OTP Configuration";
$allTab[$i]['arrayConf'] = "configOtp";$i++;

// input type process general tab
$i=0;
$config[$i]['name']='forceCreateUsers'; 
$config[$i]['label']='Force user_otp backend to create new users?';
$config[$i]['type']='checkbox';
$config[$i]['default_value']=false; $i++;

$config[$i]['name']='disableBackends'; 
$config[$i]['label']='Disable other backends? (if checked user needs TOTP to connect if is user is in the TOTP db file)';
$config[$i]['type']='checkbox';
$config[$i]['default_value']=false; $i++;

// input type process tab OTP config
$i=0;
$configOtp[$i]['name']='EncryptionKey'; 
$configOtp[$i]['label']='Encryption Key';
$configOtp[$i]['type']='text';
$configOtp[$i]['default_value']='DefaultCliEncryptionKey'; $i++;

$configOtp[$i]['name']='MaxBlockFailures'; 
$configOtp[$i]['label']='Max Block Failures';
$configOtp[$i]['type']='text';
$configOtp[$i]['default_value']='6'; $i++;

$configOtp[$i]['name']='UsersFolder'; 
$configOtp[$i]['label']='Users Folder';
$configOtp[$i]['type']='text';
$configOtp[$i]['default_value']=getcwd()."/apps/user_otp/lib/multiotp/users/"; $i++;

$configOtp[$i]['name']='UserPrefixPin'; 
$configOtp[$i]['label']='User Prefix Pin';
$configOtp[$i]['type']='checkbox';
$configOtp[$i]['default_value']=false; $i++;

$configOtp[$i]['name']='UserAlgorithm'; 
$configOtp[$i]['label']='User Algorithm (TOTP/HOTP)';
$configOtp[$i]['type']='text';
$configOtp[$i]['default_value']='TOTP'; $i++;

$configOtp[$i]['name']='TokenBase32Encode'; 
$configOtp[$i]['label']='Token Base32 Encode (need for Google Authenticator';
$configOtp[$i]['type']='checkbox';
$configOtp[$i]['default_value']=true; $i++;

$configOtp[$i]['name']='UserTokenNumberOfDigits'; 
$configOtp[$i]['label']='User Token Number Of Digits';
$configOtp[$i]['type']='text';
$configOtp[$i]['default_value']='6'; $i++;

$configOtp[$i]['name']='UserTokenTimeIntervalOrLastEvent'; 
$configOtp[$i]['label']='User Token Time Interval Or Last Event';
$configOtp[$i]['type']='text';
$configOtp[$i]['default_value']='30'; $i++;

foreach ($allTab as $tab){
    foreach ($$tab["arrayConf"] as $input){
        switch ($input['type']){
            case "checkbox":
                if(isset($_POST[$input['name']]) || $input['default_value']){
                    OCP\Config::setAppValue('user_otp',$input['name'],true);
                }else{
                    OCP\Config::setAppValue('user_otp',$input['name'],false);
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
