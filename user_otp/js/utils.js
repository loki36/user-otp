(function() {
    
var saml = document.createElement('script');
saml.type = 'text/javascript';
(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(saml);
})();

$(document).ready(function(){

		var url = document.URL;
		url = url.substring(0, url.lastIndexOf("/"));
        //$('#password').parent().hide();
        $('#password').parent().removeClass("infield groupbottom");
        $('#password').parent().addClass("infield groupmiddle");
        $('#password').attr( "autocomplete", "on" );
        $('#password').parent().after(
            '<p class="infield groupbottom">'+
            '<input id="otpPassword" type="password" placeholder="" value="" name="otpPassword"'+ 'original-title="" autocomplete="off">'+
			'<label class="infield" for="otpPassword" style="opacity: 1;">One Time Password</label>'+
			'<img id="password-icon" class="svg" alt="" src="'+url+'/core/img/actions/password.svg">'+
			'</p>'
		);
        //$('#remember_login').hide();
        //$('#remember_login+label').hide();
        //$('#submit').hide();
        
        var sheet = document.styleSheets[0];
        sheet.insertRule('#otpPassword {padding-left: 1.8em;width: 11.7em !important;}', sheet.cssRules.length);
        sheet.insertRule(
			'#otpPassword+label+img {'+
				'position:absolute; left:1.25em; top:1.1em;'+
				'-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=30)"; filter:alpha(opacity=30); opacity:.3;'+
			'}'
		, sheet.cssRules.length);
		
		$("label").inFieldLabels();
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

