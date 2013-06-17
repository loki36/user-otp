<?php
OCP\Util::addscript('user_otp', 'personalSettings');

$mOtp =  new multiotp(
    OCP\Config::getAppValue('user_otp','EncryptionKey','DefaultCliEncryptionKey')
);
$mOtp->EnableVerboseLog();
$mOtp->SetUsersFolder(
    OCP\Config::getAppValue(
        'user_otp','UsersFolder',
        getcwd()."/apps/user_otp/lib/multiotp/users/"
    )
);

$tmpl = new OCP\Template('user_otp', 'personalSettings');

if($mOtp->CheckUserExists(OCP\User::getUser())){
    $tmpl->assign('UserExists',true);

    $mOtp->SetUser(OCP\User::getUser());
    $img = OCP\Config::getAppValue(
        'user_otp',
        'UsersFolder',
        getcwd()."apps/user_otp/lib/multiotp/users/"
    ).OCP\User::getUser().".png";
    $UserTokenQrCode =  $mOtp->GetUserTokenQrCode(OCP\User::getUser(),'',$img);

    $img="../../apps/user_otp/lib/multiotp/users/".OCP\User::getUser().".png";

    $tmpl->assign('UserTokenUrlLink',$mOtp->GetUserTokenUrlLink());
    $tmpl->assign('UserTokenQrCode',$img);
    if(OCP\Config::getAppValue('user_otp','TokenBase32Encode',true)){
        $tmpl->assign('UserTokenSeed',base32_encode(hex2bin($mOtp->GetUserTokenSeed())));
    }else{
        $tmpl->assign('UserTokenSeed',$mOtp->GetUserTokenSeed());    
    }
    $tmpl->assign('UserPin',$mOtp->GetUserPin());
    $tmpl->assign('UserPrefixPin',$mOtp->GetUserPrefixPin());
    $tmpl->assign('UserLocked',$mOtp->GetUserLocked());
    $tmpl->assign('UserAlgorithm',$mOtp->GetUserAlgorithm());
    $tmpl->assign(
        'UserTokenTimeIntervalOrLastEvent',
        strtolower($mOtp->GetUserAlgorithm())==='htop'?
        $mOtp->GetUserTokenLastEvent():$mOtp->GetUserTokenTimeInterval()
    );
}else{
    $tmpl->assign('UserExists',false);
    $tmpl->assign('UserPrefixPin',OCP\Config::getAppValue('user_otp','UserPrefixPin','0'));
}
return $tmpl->fetchPage();
