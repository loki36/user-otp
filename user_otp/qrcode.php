<?php

include_once("user_otp/db/otpuserdatamapper.php");

OC_Util::checkLoggedIn();

$OtpUserDataMapper = new OtpUserDataMapper();
header("Content-type: image/png");
echo $OtpUserDataMapper->findQrcodeByUser(OCP\User::getUser());
//var_dump($OtpUserDataMapper->findQrcodeByUser(OCP\User::getUser()));
