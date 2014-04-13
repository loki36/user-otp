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

OCP\Util::addscript('user_otp', 'personalSettings');

$mOtp =  new MultiOtpDb(
    OCP\Config::getAppValue('user_otp','EncryptionKey','DefaultCliEncryptionKey')
);
$mOtp->EnableVerboseLog();
//~ $mOtp->SetUsersFolder(
    //~ OCP\Config::getAppValue(
        //~ 'user_otp','UsersFolder',
        //~ getcwd()."/apps/user_otp/3rdparty/multiotp/users/"
    //~ )
//~ );

$tmpl = new OCP\Template('user_otp', 'personalSettings');

$tmpl->assign('disableDeleteOtpForUsers',OCP\Config::getAppValue('user_otp','disableDeleteOtpForUsers','0'));

if($mOtp->CheckUserExists(OCP\User::getUser())){
    $tmpl->assign('UserExists',true);

    $mOtp->SetUser(OCP\User::getUser());
    
    $img=\OCP\Util::linkToRoute('user_otp_qrcode');

    $tmpl->assign('UserTokenUrlLink',$mOtp->GetUserTokenUrlLink());
    $tmpl->assign('UserTokenQrCode',$img);
    //~ if(OCP\Config::getAppValue('user_otp','TokenBase32Encode',true)){
        //~ $tmpl->assign('UserTokenSeed',base32_encode(hex2bin($mOtp->GetUserTokenSeed())));
        //~ $tmpl->assign('TokenBase32Encode',true);
    //~ }else{
        //~ $tmpl->assign('UserTokenSeed',hex2bin($mOtp->GetUserTokenSeed()));    
    //~ }
    $tmpl->assign('UserTokenSeed',base32_encode(hex2bin($mOtp->GetUserTokenSeed()))); 
    $tmpl->assign('UserPin',$mOtp->GetUserPin());
    $tmpl->assign('UserPrefixPin',$mOtp->GetUserPrefixPin());
    $tmpl->assign('UserLocked',$mOtp->GetUserLocked());
    $tmpl->assign('UserAlgorithm',$mOtp->GetUserAlgorithm());
    $tmpl->assign(
        'UserTokenTimeIntervalOrLastEvent',
        strtolower($mOtp->GetUserAlgorithm())==='htop'?
        $mOtp->GetUserTokenLastEvent():$mOtp->GetUserTokenTimeInterval()
    );
}else{
    $tmpl->assign('UserExists',false);
    $tmpl->assign('UserPrefixPin',OCP\Config::getAppValue('user_otp','UserPrefixPin','0'));
}
return $tmpl->fetchPage();
