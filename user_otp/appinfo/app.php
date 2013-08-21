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

include_once("user_otp/lib/otp.php");

OC::$CLASSPATH['OC_USER_OTP'] = 'user_otp/lib/otp.php';

OCP\App::registerAdmin('user_otp','adminSettings');
OCP\App::registerPersonal('user_otp','personalSettings');

//if(OCP\Config::getAppValue('user_otp','forceCreateUsers')){
//    OCP\Util::connectHook('OC_User','pre_createUser','OC_USER_OTP','deleteBackends');
//}

//if(OCP\Config::getAppValue('user_otp','disableBackends')){
//    OC_User::clearBackends();
//}

// Nothing to do if user is already logged
//if (!OCP\User::isLoggedIn()){
	if(OCP\Config::getAppValue('user_otp','authMethod',_AUTH_DEFAULT_)!==_AUTH_STANDARD_){
		//OC_Log::write('user_otp', 'app load', OC_Log::DEBUG);
		$usedBackends = OC_User::getUsedBackends();
		OC_User::clearBackends();
		OC_USER_OTP::registerBackends($usedBackends);
		OC_User::useBackend('OTP');
		//$otpBackend = new OC_USER_OTP($usedBackends);
		//OC_User::useBackend($otpBackend);
		//~ foreach($usedBackends as $backend){
			//~ OC_User::useBackend($backend);
		//~ }
		//var_dump($otpBackend);exit;
	}

if (!OCP\User::isLoggedIn()){
	if (OCP\Config::getAppValue('user_otp','authMethod',_AUTH_DEFAULT_) === _AUTH_TWOFACTOR_) {
		// Load js code in order to add passcode field into the normal login form
		OCP\Util::addScript('user_otp', 'utils');
	}
}
?>
