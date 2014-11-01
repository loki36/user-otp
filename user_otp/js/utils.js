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
            '<input id="otpPassword" type="password" placeholder="One Time Password" value="" name="otpPassword"'+ 'original-title="" autocomplete="off" >'+
			'<label class="infield" for="otpPassword" style="opacity: 1;">One Time Password</label>'+
			'<img id="password-icon" class="svg" alt="" src="'+url+'/core/img/actions/password.svg">'+
			'</p>'
		);

    $("#submit").removeAttr("disabled");

var sheet = document.styleSheets[0]; 	
sheet.insertRule(
  '#otpPassword, input[name="otpPassword-clone"] {'+ 
    'padding-left: 36px !important;'+ 
    'width: 223px !important;'+
  '} ', 
  sheet.cssRules.length
);
sheet.insertRule( 	
  '#otpPassword+label+img {'+ 	
  'position:absolute; left:1.25em; top:1.1em;'+ 	
  '-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=30)"; filter:alpha(opacity=30); opacity:.3;'+ 	
  '}' 	
  , sheet.cssRules.length);

});

