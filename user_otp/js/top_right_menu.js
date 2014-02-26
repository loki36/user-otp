$(document).ready(function(){

   //$('#expanddiv li:last-child').append('toto');
   var items = document.querySelectorAll("#expanddiv li");
   var users = items[items.length-4];
   //alert(users);
   //users.append('toto');
   var elm = users; //document.getElementById("name");
	var newElm = document.createElement("li");
	newElm.innerHTML = "<a href='index.php/apps/user_otp/list_users'>OTP Users</a>";
	//alert(document.location.href);
	elm.parentNode.insertBefore(newElm, elm.nextSibling);
        
});
