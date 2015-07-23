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
		//OC_User::useBackend('OTP');
		//$otpBackend = new OC_USER_OTP($usedBackends);
		//OC_User::useBackend($otpBackend);
		//~ foreach($usedBackends as $backend){
			//~ OC_User::useBackend($backend);
		//~ }
		//var_dump($otpBackend);exit;
	}
//var_dump(OCP\Config::getAppValue('user_otp','inputOtpAfterPwd','0'));
//exit;

if (!OCP\User::isLoggedIn()){
	if (
		OCP\Config::getAppValue('user_otp','authMethod',_AUTH_DEFAULT_) === _AUTH_TWOFACTOR_ 
		&& OCP\Config::getAppValue('user_otp','inputOtpAfterPwd','0')!=='1'
	) {
		// Load js code in order to add passco fix node field into the normal login form
		OCP\Util::addScript('user_otp', 'utils');
	}
}

//OCP\Util::addScript('user_otp', 'top_right_menu');
//var_dump('toto');
?>
<!--
<script type="text/javascript">
$(document).ready(function(){

   //$('#expanddiv li:last-child').append('toto');
   var items = document.querySelectorAll("#expanddiv li");
   var users = items[items.length-4];
   //alert(users);
   //users.append('toto');
   var elm = users; //document.getElementById("name");
	var newElm = document.createElement("li");
	newElm.innerHTML = "<a href='"+<?php echo \OCP\Util::linkToRoute('user_otp_list_users') ?> +"'>OTP Users</a>";
	//alert(document.location.href);
	elm.parentNode.insertBefore(newElm, elm.nextSibling);
        
});
</script>
-->

<?php

$isadmin = OC_User::isAdminUser(OC_User::getUser());
if($isadmin){
\OCP\App::addNavigationEntry(array(

    // the string under which your app will be referenced in owncloud
    'id' => 'user_otp',

    // sorting weight for the navigation. The higher the number, the higher
    // will it be listed in the navigation
    'order' => 74,

    // the route that will be shown on startup
    'href' => \OCP\Util::linkToRoute('user_otp_list_users'),

    // the icon that will be shown in the navigation
    // this file needs to exist in img/example.png
    'icon' => \OCP\Util::imagePath('settings', 'admin.svg'),

    // the title of your application. This will be used in the
    // navigation or on the settings page of your app
    'name' => 'OTP Users'
));
}


?>
