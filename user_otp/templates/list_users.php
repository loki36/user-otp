<?php
/**
 * Copyright (c) 2011, Robin Appelman <icewind1991@gmail.com>
 * This file is licensed under the Affero General Public License version 3 or later.
 * See the COPYING-README file.
 */
$allGroups=array();
foreach($_["groups"] as $group) {
	$allGroups[] = $group['name'];
}
$_['subadmingroups'] = $allGroups;
$items = array_flip($_['subadmingroups']);
unset($items['admin']);
$_['subadmingroups'] = array_flip($items);
?>

<script type="text/javascript" src="<?php print_unescaped(OC_Helper::linkToRoute('isadmin'));?>"></script>

<table class="hascontrols grid" data-groups="<?php p(json_encode($allGroups));?>">
	<thead>
		<tr>
			<?php if ($_['enableAvatars']): ?>
			<th id='headerAvatar'></th>
			<?php endif; ?>
			<th id='headerName'><?php p($l->t('Username'))?></th>
			<th id="headerDisplayName"><?php p($l->t( 'Full Name' )); ?></th>
			<th id="headerHasOtp"><?php p($l->t( 'Has OTP' )); ?></th>
			<th id="headerLocked"><?php p($l->t( 'Locked' )); ?></th>
			<th id="headerAlgorithm"><?php p($l->t( 'Algorithm' )); ?></th>
			<?php if($_['PrefixPin']):?>
			<th id="headerUserPin"><?php p($l->t( 'UserPin' )); ?></th>
			<?php endif;?>
			<th id="headerTokenSeed"><?php p($l->t( 'TokenSeed' )); ?></th>
			<th id="headerRemove">Delete OTP</th>
			<th id="headerCreate">Create OTP</th>
			<th id="headerSendEmail">Send Email</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($_["users"] as $user): ?>
		<tr data-uid="<?php p($user["name"]) ?>"
			data-displayName="<?php p($user["displayName"]) ?>">
			<?php if ($_['enableAvatars']): ?>
			<td class="avatar"><div class="avatardiv"></div></td>
			<?php endif; ?>
			<td class="name"><?php p($user["name"]); ?></td>
			<td class="displayName"><span><?php p($user["displayName"]); ?></span>
			</td>
			<td class=""><span><?php p($user["OtpExist"]); ?></span></td>
			<td class=""><span><?php p($user["UserLocked"]); ?></span></td>
			<td class=""><span><?php p($user["UserAlgorithm"]); ?></span></td>
			<?php if($_['PrefixPin']):?>
			<td class="UserPin" style="text-align:center;">
				<?php if($user["OtpExist"]):?>
				  <span><?php p($user["UserPin"]); ?></span>
				<?php else: ?>
				  <input type="text" name="UserPinInput" value="" style="width:35px;">
				<?php endif;?>
			</td>
			<?php endif;?>
			<td class="">
				<?php if($user["OtpExist"]):?>
<!--
					<span id="UserTokenSeed" style="display:inline-block; background-color:red; width:100px;"><?php p($user["UserTokenSeed"]); ?></span>
-->
					 <input type="text" value="<?php p($user["UserTokenSeed"]); ?>" style="width:475px;">
				<?php else: ?>
				  <input type="text" name="UserTokenSeedInput" value="" style="width:475px;">
				<?php endif;?>	
			</td>
			
			<td class="remove-otp">
				<?php if($user["OtpExist"]):?>
					<a href="#" class="action delete" original-title="<?php p($l->t('Delete OTP'))?>">
						<img src="<?php print_unescaped(image_path('core', 'actions/delete.svg')) ?>" class="svg" />
					</a>
				<?php endif;?>
			</td>
			<td class="create-otp">
				<?php if(!$user["OtpExist"]):?>
					<a href="#" class="action add" original-title="<?php p($l->t('Create OTP'))?>">
						<img src="<?php print_unescaped(image_path('core', 'actions/add.svg')) ?>" class="svg" />
					</a>
				<?php endif;?>
			</td>
			<td class="send-email">
				<?php if($user["OtpExist"]):?>
					<a href="#" class="action send-email" original-title="<?php p($l->t('Send email'))?>">
						<img src="<?php print_unescaped(image_path('core', 'actions/mail.svg')) ?>" class="svg" />
					</a>
				<?php endif;?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
