<?php

include_once("user_otp/lib/multiotp/multiotp.class.php");
include_once("user_otp/db/otpuserdata.php");
include_once("user_otp/db/otpuserdatamapper.php");

class MultiOtpDb extends multiotp{
	
  function ReadUserData($user = '', $create = FALSE){
    if ('' != $user)
    {
      $this->SetUser($user);
    }
    $result = FALSE;
    // We initialize the encryption hash to empty
    $this->_user_data['encryption_hash'] = '';
    
    $OtpUserDataMapper = new OtpUserDataMapper();
    $OtpUserData = $OtpUserDataMapper->findByUser($this->GetUser());
    //var_dump($OtpUserData);
    if($OtpUserData){
      foreach($OtpUserData->getArray() as $key => $value){
        $FormatedKey = strtolower(preg_replace("/(.)([A-Z]{1})/","\\1_\\2",$key));
        $this->_user_data[$FormatedKey] = $value;
        //echo "_user_data[$FormatedKey] = $value<br/>";
      }
      $result = true;
    }
    
    if ('' != $this->_user_data['encryption_hash'])
    {
      if ($this->_user_data['encryption_hash'] != $this->CalculateControlHash($this->GetEncryptionKey()))
      {
        $this->_user_data['encryption_hash'] = "ERROR";
        $result = FALSE;
      }
    }

    $this->SetUserDataReadFlag($result);
    return $result;
  }
	
  function WriteUserData(){
    $OtpUserDataMapper = new OtpUserDataMapper();
    $arguments = array_merge(array("user"=>$this->GetUser()),$this->_user_data);
    $OtpUserData = new OtpUserData($arguments);
    
    // check if user exist
    $OtpUserDataExist = $OtpUserDataMapper->findByUser($this->GetUser());
    if($OtpUserDataExist){ 
      $result = $OtpUserDataMapper->update($OtpUserData);
    }else{
      $result = $OtpUserDataMapper->insert($OtpUserData);
    }
    
    if($result){
      $OtpUserData->setQrcode($this->GetUserTokenQrCode($this->GetUser(),'','binary'));
      //var_dump($this->GetUserTokenQrCode($this->GetUser(),'','binary'));
      $result = $OtpUserDataMapper->update($OtpUserData);
    }
    return $result;
  }
  
  public function CheckUserExists($user = ''){
    $check_user = ('' != $user)?$user:$this->GetUser();
    $OtpUserDataMapper = new OtpUserDataMapper();
    $OtpUserData = $OtpUserDataMapper->findByUser($check_user);
    //var_dump($OtpUserData);
    if($OtpUserData){
      return true;
    }
    return false;
  }
  
  public function DeleteUser($user = '')
  {
    if ('' != $user)
    {
      $this->SetUser($user);
    }
    $OtpUserDataMapper = new OtpUserDataMapper();
    return $OtpUserDataMapper->deleteByUser($this->GetUser());
  }
  
}
