<?php
//namespace \OCA\UserOtp\Db;

class OtpUserData {

    private $data = array(
      "Id"=>"",
      "User"=>"",
      "RequestPrefixPin"=>"",
      "Algorithm"=>"",
      "TokenSeed"=>"",
      "UserPin"=>"",
      "NumberOfDigits"=>"",
      "TimeInterval"=>"",
      "LastEvent"=>"",
      "LastLogin"=>"",
      "ErrorCounter"=>"",
      "Locked"=>"",
      "Qrcode"=>"",
    );
    

    public function __construct($fromRow=null){
        if($fromRow){
           $this->fromRow($fromRow);
        }
    }

    public function fromRow($row){
        foreach($row as $key =>$value){
          $DbKey = preg_replace("/ /","",ucwords(preg_replace("/_/"," ",$key)));
          //echo "$DbKey =>$value\n";
          if(isset($this->data[$DbKey])){
            //$this->data[$DbKey] = $value;
            $setter = "set".$DbKey;
            $this->$setter($value);
          }
        }
    }
    
    public function getArray(){
      return $this->data;
    }
    
    private function get($key){
        if(!isset($this->data[$key])){
          throw new Exception('property '.$key.' not exist');
        }
        return $this->data[$key];
    }
    
    private function set($key,$value){
        if(!isset($this->data[$key])){
          throw new Exception('property '.$key.' not exist');
        }
        return $this->data[$key]=$value;
    }

    public function __call($name, $arguments){
      $GetSet = substr($name,0,3);
      switch($GetSet){
        case "get":
        case "set": //echo substr($name,3)."\n";
          array_unshift($arguments, substr($name,3));
          //var_dump($GetSet,$arguments);
          return call_user_func_array(array($this,$GetSet),$arguments);
          break;
        default:
          throw new Exception('Methode '.$name.' unknow');
      }
    }
}
