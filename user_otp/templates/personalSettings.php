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
?>
<form id="otp_personal_form">
    <div id="userotpSettings" class="personalblock">
        <legend><strong>OTP Configuration</strong></legend>
        <?php if($_['UserExists']) { ?>
            <p>
                User Token Seed : <?php p($_['UserTokenSeed']); ?> 
                <?php if($_['UserPrefixPin']){ ?>
                    / User Pin : <?php p($_['UserPin']); ?>
                <?php } ?>
                <?php if($_['UserLocked']){ ?>
                    / <strong>User is locked</strong>
                <?php } ?>
            </p>
            <p>
                User Algorithm : <?php p($_['UserAlgorithm']); ?> 
                / 
                User Token Time Interval Or Last Event : <?php p($_['UserTokenTimeIntervalOrLastEvent']); ?>
            </p>
            <p>
                Token Url Link : <a href="<?php p($_['UserTokenUrlLink']); ?>"><?php p($_['UserTokenUrlLink']); ?></a>
            </p>
            <p>
				With android token apps select base32 before input seed<br/>
        UserTokenQrCode : <img src="<?php p($_['UserTokenQrCode']); ?>">
            </p>
            <?php if(!$_['disableDeleteOtpForUsers']) {?>				
				<input type="hidden" id="otp_action" name="otp_action" value="delete_otp">
				<input id="otp_submit_action" type='button' value='Delete'>
			<?php }else{ ?>
				User Token Seed (if left blank, it will be generated automatically) : <input type="text" name="UserTokenSeed" value="">
                <?php if($_['UserPrefixPin']){ ?>
                    <br/>User Pin (if left blank, it will be generated automatically) :  <input type="text" name="UserPin" value="">
                <?php } ?>
				<input type="hidden" id="otp_action" name="otp_action" value="replace_otp">
				<input id="otp_submit_action" type='button' value='replace'>
			<?php } ?>
        <?php }else{ ?>
            <p>
                User Token Seed (if left blank, it will be generated automatically) : <input type="text" name="UserTokenSeed" value="">
                <?php if($_['UserPrefixPin']){ ?>
                    <br/>User Pin (if left blank, it will be generated automatically) :  <input type="text" name="UserPin" value="">
                <?php } ?>
                <input type="hidden" name="otp_action" value="create_otp">
                <input id="otp_submit_action" type='submit' value='Create'>
            </p>
        <?php } ?>
    </div>
</form>
