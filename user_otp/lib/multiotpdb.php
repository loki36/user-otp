<?php

include_once("user_otp/3rdparty/multiotp/multiotp.class.php");
include_once("user_otp/db/otpuserdata.php");
include_once("user_otp/db/otpuserdatamapper.php");

class MultiOtpDb extends multiotp{
	
  //~ function MultiOtpDb(){
	 //~ parent::__construct();
	 //~ $this->ResetConfigArray();
	 //~ //print_r($this->_config_data);exit;
  //~ }
	
  function ReadUserData($user = '', $create = FALSE, $do_not_check_on_server = FALSE){
    
    if ('' != $user)
    {
      $this->SetUser($user);
    }
    $result = FALSE;
    
    // We reset all values (we know the key based on the schema)
    $this->ResetUserArray();
    
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
    $arguments = array_merge($this->_user_data,array("user"=>$this->GetUser()));
    $OtpUserData = new OtpUserData($arguments);
    //var_dump($arguments);
    // check if user exist
    $OtpUserDataExist = $OtpUserDataMapper->findByUser($this->GetUser());
    //echo $this->GetUser()." 2";exit;
    if($OtpUserDataExist){ 
      $result = $OtpUserDataMapper->update($OtpUserData);
    }else{
      $result = $OtpUserDataMapper->insert($OtpUserData);
    }
   
    if($result && !$OtpUserDataExist){
      //$OtpUserData->setQrcode($this->GetUserTokenQrCode($this->GetUser(),'','binary'));
      $OtpUserData->setQrcode(base64_encode($this->GetUserTokenQrCode($this->GetUser(),'','binary')));
      //var_dump($this->GetUserTokenQrCode($this->GetUser(),'','binary'));
      $result = $OtpUserDataMapper->update($OtpUserData);
    }
    if($result){
      $this->WriteLog('Info: User '.$this->_user.' successfully created and saved.');
    }
    return $result;
  }
  
  public function CheckUserExists($user = '', $no_server_check = FALSE){
    $check_user = ('' != $user)?$user:$this->GetUser();
    $OtpUserDataMapper = new OtpUserDataMapper();
    $OtpUserData = $OtpUserDataMapper->findByUser($check_user);
    //var_dump($OtpUserData);
    if($OtpUserData){
      return true;
    }
    return false;
  }
  
  public function DeleteUser($user = '', $no_error_info = FALSE)
  {
    if ('' != $user)
    {
      $this->SetUser($user);
    }
    $OtpUserDataMapper = new OtpUserDataMapper();
    return $OtpUserDataMapper->deleteByUser($this->GetUser());
  }
  
  function GetUserTokenQrCode($user = '', $display_name = '', $file_name = 'binary')
    {
      $result = FALSE;
      if (!function_exists('ImageCreate'))
      {
          $this->WriteLog("Error: PHP GD library is not installed");
          return $result;
      }
      $data = $this->GetUserTokenUrlLink($user,$display_name);
      if($data){
        $result = $this->qrcode($data, $file_name);
      }

      return $result;
  }
  
}

//~ //overright function because in parent class it return lowercase value not ok for base32
//~ /***********************************************************************
 //~ * Custom function providing base32_encode
 //~ *   if it is not available in the actual configuration
 //~ *
 //~ * Source: http://pastebin.com/BLyG5khJ
 //~ ***********************************************************************/
    //~ function base32_encode($inString)
    //~ {
        //~ $outString = '';
        //~ $compBits = '';
        //~ $BASE32_TABLE = array('00000' => 0x61, '00001' => 0x62, '00010' => 0x63, '00011' => 0x64,
                              //~ '00100' => 0x65, '00101' => 0x66, '00110' => 0x67, '00111' => 0x68,
                              //~ '01000' => 0x69, '01001' => 0x6a, '01010' => 0x6b, '01011' => 0x6c,
                              //~ '01100' => 0x6d, '01101' => 0x6e, '01110' => 0x6f, '01111' => 0x70,
                              //~ '10000' => 0x71, '10001' => 0x72, '10010' => 0x73, '10011' => 0x74,
                              //~ '10100' => 0x75, '10101' => 0x76, '10110' => 0x77, '10111' => 0x78,
                              //~ '11000' => 0x79, '11001' => 0x7a, '11010' => 0x32, '11011' => 0x33,
                              //~ '11100' => 0x34, '11101' => 0x35, '11110' => 0x36, '11111' => 0x37);
 //~ 
        //~ /* Turn the compressed string into a string that represents the bits as 0 and 1. */
        //~ for ($i = 0; $i < strlen($inString); $i++)
        //~ {
            //~ $compBits .= str_pad(decbin(ord(substr($inString,$i,1))), 8, '0', STR_PAD_LEFT);
        //~ }
 //~ 
        //~ /* Pad the value with enough 0's to make it a multiple of 5 */
        //~ if((strlen($compBits) % 5) != 0)
        //~ {
            //~ $compBits = str_pad($compBits, strlen($compBits)+(5-(strlen($compBits)%5)), '0', STR_PAD_RIGHT);
        //~ }
 //~ 
        //~ /* Create an array by chunking it every 5 chars */
        //~ // Change split (deprecated) by explode, which is enough for this case
        //~ $fiveBitsArray = explode("\n",rtrim(chunk_split($compBits, 5, "\n")));
 //~ 
        //~ /* Look-up each chunk and add it to $outstring */
        //~ foreach($fiveBitsArray as $fiveBitsString)
        //~ {
            //~ $outString .= chr($BASE32_TABLE[$fiveBitsString]);
        //~ }
        //~ 
        //~ return strtoupper($outString);
    //~ }

//~ class Base32 {
//~ 
   //~ private static $map = array(
        //~ 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', //  7
        //~ 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', // 15
        //~ 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', // 23
        //~ 'Y', 'Z', '2', '3', '4', '5', '6', '7', // 31
        //~ '='  // padding char
    //~ );
   //~ 
   //~ private static $flippedMap = array(
        //~ 'A'=>'0', 'B'=>'1', 'C'=>'2', 'D'=>'3', 'E'=>'4', 'F'=>'5', 'G'=>'6', 'H'=>'7',
        //~ 'I'=>'8', 'J'=>'9', 'K'=>'10', 'L'=>'11', 'M'=>'12', 'N'=>'13', 'O'=>'14', 'P'=>'15',
        //~ 'Q'=>'16', 'R'=>'17', 'S'=>'18', 'T'=>'19', 'U'=>'20', 'V'=>'21', 'W'=>'22', 'X'=>'23',
        //~ 'Y'=>'24', 'Z'=>'25', '2'=>'26', '3'=>'27', '4'=>'28', '5'=>'29', '6'=>'30', '7'=>'31'
    //~ );
   //~ 
    //~ /**
     //~ *    Use padding false when encoding for urls
     //~ *
     //~ * @return base32 encoded string
     //~ * @author Bryan Ruiz
     //~ **/
    //~ public static function encode($input, $padding = true) {
        //~ if(empty($input)) return "";
        //~ $input = str_split($input);
        //~ $binaryString = "";
        //~ for($i = 0; $i < count($input); $i++) {
            //~ $binaryString .= str_pad(base_convert(ord($input[$i]), 10, 2), 8, '0', STR_PAD_LEFT);
        //~ }
        //~ $fiveBitBinaryArray = str_split($binaryString, 5);
        //~ $base32 = "";
        //~ $i=0;
        //~ while($i < count($fiveBitBinaryArray)) {   
            //~ $base32 .= self::$map[base_convert(str_pad($fiveBitBinaryArray[$i], 5,'0'), 2, 10)];
            //~ $i++;
        //~ }
        //~ if($padding && ($x = strlen($binaryString) % 40) != 0) {
            //~ if($x == 8) $base32 .= str_repeat(self::$map[32], 6);
            //~ else if($x == 16) $base32 .= str_repeat(self::$map[32], 4);
            //~ else if($x == 24) $base32 .= str_repeat(self::$map[32], 3);
            //~ else if($x == 32) $base32 .= self::$map[32];
        //~ }
        //~ return $base32;
    //~ }
   //~ 
    //~ public static function decode($input) {
        //~ if(empty($input)) return;
        //~ $paddingCharCount = substr_count($input, self::$map[32]);
        //~ $allowedValues = array(6,4,3,1,0);
        //~ if(!in_array($paddingCharCount, $allowedValues)) return false;
        //~ for($i=0; $i<4; $i++){
            //~ if($paddingCharCount == $allowedValues[$i] &&
                //~ substr($input, -($allowedValues[$i])) != str_repeat(self::$map[32], $allowedValues[$i])) return false;
        //~ }
        //~ $input = str_replace('=','', $input);
        //~ $input = str_split($input);
        //~ $binaryString = "";
        //~ for($i=0; $i < count($input); $i = $i+8) {
            //~ $x = "";
            //~ if(!in_array($input[$i], self::$map)) return false;
            //~ for($j=0; $j < 8; $j++) {
                //~ $x .= str_pad(base_convert(@self::$flippedMap[@$input[$i + $j]], 10, 2), 5, '0', STR_PAD_LEFT);
            //~ }
            //~ $eightBits = str_split($x, 8);
            //~ for($z = 0; $z < count($eightBits); $z++) {
                //~ $binaryString .= ( ($y = chr(base_convert($eightBits[$z], 2, 10))) || ord($y) == 48 ) ? $y:"";
            //~ }
        //~ }
        //~ return $binaryString;
    //~ }
//~ }
