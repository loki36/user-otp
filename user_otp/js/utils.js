(function() {
    
var saml = document.createElement('script');
saml.type = 'text/javascript';
(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(saml);
})();

$(document).ready(function(){

        //$('#password').parent().hide();
        $('#password').parent().removeClass("infield groupbottom");
        $('#password').parent().addClass("infield groupmiddle");
        $('#password').parent().after(
            '<p class="infield groupbottom">'+
            '<input id="otpPassword" type="password" placeholder="" data-typetoggle="#show" value="" name="otpPassword"'+ 'original-title="">'+
'<input type="text" name="password-clone" tabindex="0" autocomplete="off" style="display: none;" original-title="">'+
'<label class="infield" for="otpPassword">One Time Password</label>'+
'<img id="password-icon" class="svg" alt="" src="/core/img/actions/password.svg">'+
'<input id="show" type="checkbox" name="show" original-title="">'+
'<label for="show"></label>'+
'</p>');
        $('#remember_login').hide();
        $('#remember_login+label').hide();
        //$('#submit').hide();

});
