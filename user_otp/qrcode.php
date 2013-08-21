<?php

include_once("user_otp/db/otpuserdatamapper.php");

OC_Util::checkLoggedIn();

$OtpUserDataMapper = new OtpUserDataMapper();
header("Content-type: image/png");
//var_dump($OtpUserDataMapper->findQrcodeByUser(OCP\User::getUser()));
echo $OtpUserDataMapper->findQrcodeByUser(OCP\User::getUser());
