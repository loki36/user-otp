(function() {

var saml = document.createElement('script');
saml.type = 'text/javascript';
(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(saml);
})();

$(document).ready(function(){

		var url = document.URL;
		url = url.substring(0, url.lastIndexOf("/"));
        $('#password').parent().removeClass("infield groupbottom");
        $('#password').parent().addClass("infield groupmiddle");
        $('#password').attr( "autocomplete", "on" );
        $('#password').parent().after(
            '<p class="infield groupbottom">'+
            '<input id="otpPassword" type="password" placeholder="" value="" name="otpPassword"'+ 'original-title="" autocomplete="off" style="padding-left:36px; width=223px">'+
			'<label class="infield" for="otpPassword" style="opacity: 1;">One Time Password</label>'+
			'<img id="password-icon" class="svg" alt="" src="'+url+'/core/img/actions/password.svg">'+
			'</p>'
		);

    $("#submit").removeAttr("disabled");
		//~ $('#otpPassword').change( function() {
				//~ if ($(this).val() !== "") {
					//~ alert('test');
					//~ $('#otpPassword+label').show();
				//~ }
				//~ else {
					//~ $('#otpPassword+label').hide();
				//~ }
			//~ });
		//$('input#otpPassword').keyup(checkShowCredentials);
		//~ var setShowPassword = function(input, label) {
			//~ input.showPassword().keyup();
		//~ };
		//~ setShowPassword($('#otpPassword'), $('label[for=otpPassword]'));

});
