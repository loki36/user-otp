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
		alert('Not working yet');
		exit;
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
	
	do_create: function (uid) {
		alert('Not working yet');
	},
	
	send_email: function (uid) {
		alert('Not working yet');
	},
};

$(document).ready(function () {
	
	$('table').on('click', 'td.remove-otp>a', function (event) {
		var row = $(this).parent().parent();
		var uid = $(row).attr('data-uid');
		//$(row).hide();
		// Call function for handling delete/undo
		OtpUserList.do_delete(uid);
	});
	
	$('table').on('click', 'td.create-otp>a', function (event) {
		var row = $(this).parent().parent();
		var uid = $(row).attr('data-uid');
		//var UserPin = $('input[name=UserPinInput]').val();
		//var UserTokenSeed = $('input[name=UserTokenSeedInput]').val();
		var UserPin = $(row).find("input[name=UserPinInput]").first().val();
		var UserTokenSeed = $(row).find('input[name=UserTokenSeedInput]').first().val();
		alert(UserPin+ " " +UserTokenSeed);
		console.dir($(row));
		//$(row).hide();
		// Call function for handling delete/undo
		OtpUserList.do_create(uid);
	});
	
	$('table').on('click', 'td.send-email>a', function (event) {
		var row = $(this).parent().parent();
		var uid = $(row).attr('data-uid');
		//$(row).hide();
		// Call function for handling delete/undo
		OtpUserList.send_email(uid);
	});
	
});
