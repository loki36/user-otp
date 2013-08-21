$(document).ready(function(){
    $("#otp_submit_action").click( function(){        
        // Serialize the data
        var post = $( "#otp_personal_form" ).serialize();
        //$('#passwordchanged').hide();
        //$('#passworderror').hide();
        // Ajax foo
        //alert(post);
        $.post( OC.filePath('user_otp', 'ajax', 'personalSettings.php'), post, function(data){
            if( data.status == "success" ){
//                $('#passwordchanged').show();
                   $( "#otp_personal_form" ).submit();
            }else{
              alert("Error : " + data.data.message);
                //$('#passworderror').html( data.data.message );
                //$('#passworderror').show();
            }
        });
        return false;
    }); 
});
