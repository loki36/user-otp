<?php

$this->create('user_otp_qrcode','qrcode.php')->actionInclude('user_otp/qrcode.php');
$this->create('user_otp_list_users','list_users')->actionInclude('user_otp/list_users.php');
