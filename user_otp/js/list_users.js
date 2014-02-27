var OtpUserList = {
	useUndo: true,
	availableGroups: [],
	offset: 30, //The first 30 users are there. No prob, if less in total.
				//hardcoded in settings/users.php

	usersToLoad: 10, //So many users will be loaded when user scrolls down

	/**
	 * @brief Initiate user deletion process in UI
	 * @param string uid the user ID to be deleted
	 *
	 * Does not actually delete the user; it sets them for
	 * deletion when the current page is unloaded, at which point
	 * finishDelete() completes the process. This allows for 'undo'.
	 */
	do_delete: function (uid) {
		if (typeof OtpUserList.deleteUid !== 'undefined') {
			//Already a user in the undo queue
			OtpUserList.finishDelete(null);
		}
		OtpUserList.deleteUid = uid;

		// Set undo flag
		OtpUserList.deleteCanceled = false;

		// Provide user with option to undo
		$('#notification').data('deleteuser', true);
		OC.Notification.showHtml(t('settings', 'deleted') + ' ' + escapeHTML(uid) + '<span class="undo">' + t('settings', 'undo') + '</span>');
	},
	
	finishDelete: function (ready) {

		// Check deletion has not been undone
		if (!OtpUserList.deleteCanceled && OtpUserList.deleteUid) {

			// Delete user via ajax
			$.ajax({
				type: 'POST',
				url: OC.filePath('user_otp', 'ajax', 'personalSettings.php'),
				async: false,
				data: { otp_action: 'delete_otp',uid: UserList.deleteUid },
				success: function (result) {
					if (result.status === 'success') {
						// Remove undo option, & remove user from table
						OC.Notification.hide();
						//$('tr').filterAttr('data-uid', OtpUserList.deleteUid).remove();
						OtpUserList.deleteCanceled = true;
						location.reload();
						if (ready) {
							ready();
						}
					} else {
						OC.dialogs.alert(result.data.message, t('settings', 'Unable to remove user'));
					}
				}
			});
		}
	},

};

$(document).ready(function () {
	
	$('table').on('click', 'td.remove-otp>a', function (event) {
		var row = $(this).parent().parent();
		var uid = $(row).attr('data-uid');
		$.post(
			OC.filePath('user_otp', 'ajax', 'personalSettings.php'),
			{otp_action: 'delete_otp',uid: uid},
			function (result) {
				if (result.status != 'success') {
					OC.Notification.show(t('admin', result.data.message));
					if (UserList.notificationTimeout){
						window.clearTimeout(UserList.notificationTimeout);
					}
					UserList.notificationTimeout = window.setTimeout(
						function(){
							OC.Notification.hide();
							UserList.notificationTimeout = null;
						}, 5000);
				}else{
					location.reload();
				}
			}
		);
		// Call function for handling delete/undo
		//OtpUserList.do_delete(uid);
	});
	
	$('table').on('click', 'td.create-otp>a', function (event) {
		var row = $(this).parent().parent();
		var uid = $(row).attr('data-uid');
		var UserPin = $(row).find("input[name=UserPinInput]").first().val();
		var UserTokenSeed = $(row).find('input[name=UserTokenSeedInput]').first().val();
		//alert(UserPin+ " " +UserTokenSeed);
		$.post(
			OC.filePath('user_otp', 'ajax', 'personalSettings.php'),
			{otp_action: 'create_otp',uid: uid, UserPin: UserPin, UserTokenSeed: UserTokenSeed},
			function (result) {
				if (result.status != 'success') {
					OC.Notification.show(t('admin', result.data.message));
					if (UserList.notificationTimeout){
						window.clearTimeout(UserList.notificationTimeout);
					}
					UserList.notificationTimeout = window.setTimeout(
						function(){
							OC.Notification.hide();
							UserList.notificationTimeout = null;
						}, 5000);
				}else{
					location.reload();
				}
			}
		);
	});
	
	$('table').on('click', 'td.send-email>a', function (event) {
		var row = $(this).parent().parent();
		var uid = $(row).attr('data-uid');
		$.post(
			OC.filePath('user_otp', 'ajax', 'personalSettings.php'),
			{otp_action: 'send_email_otp',uid: uid},
			function (result) {
				if (result.status != 'success') {
					OC.Notification.show(t('admin', result.data.message));
					if (UserList.notificationTimeout){
						window.clearTimeout(UserList.notificationTimeout);
					}
					UserList.notificationTimeout = window.setTimeout(
						function(){
							OC.Notification.hide();
							UserList.notificationTimeout = null;
						}, 5000);
				}else{
					OC.Notification.show(t('admin', result.data.message));
					if (UserList.notificationTimeout){
						window.clearTimeout(UserList.notificationTimeout);
					}
					UserList.notificationTimeout = window.setTimeout(
						function(){
							OC.Notification.hide();
							UserList.notificationTimeout = null;
						}, 5000);
				}
			}
		);
	});
	
	// Handle undo notifications
	OC.Notification.hide();
	$('#notification').on('click', '.undo', function () {
		if ($('#notification').data('deleteuser')) {
			$('tbody tr').filterAttr('data-uid', OtpUserList.deleteUid).show();
			OtpUserList.deleteCanceled = true;
		}
		OC.Notification.hide();
	});
	OtpUserList.useUndo = ('onbeforeunload' in window);
	$(window).bind('beforeunload', function () {
		OtpUserList.finishDelete(null);
	});
	
});
