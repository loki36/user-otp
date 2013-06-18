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

OC::$CLASSPATH['OC_USER_OTP'] = 'user_otp/lib/otp.php';

OCP\App::registerAdmin('user_otp','adminSettings');
OCP\App::registerPersonal('user_otp','personalSettings');

//if(OCP\Config::getAppValue('user_otp','forceCreateUsers')){
//    OCP\Util::connectHook('OC_User','pre_createUser','OC_USER_OTP','deleteBackends');
//}

if(OCP\Config::getAppValue('user_otp','disableBackends')){
    OC_User::clearBackends();
}

OC_User::useBackend('OTP');

?>
