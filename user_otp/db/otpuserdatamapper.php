<?php
//namespace \OCA\UserOtp\Db;

class OtpUserDataMapper{ // extends Mapper {
    private $TableName = "*PREFIX*user_otp";

    public function __construct(/*API $api*/) {
      //parent::__construct($api, 'news_feeds'); // tablename is news_feeds
    }
    
    public function getTableName(){
      return $this->TableName;
    }


    public function find($id, $userId){
      $sql = 'SELECT * FROM `' . $this->getTableName() . '` ' .
        'WHERE `id` = ? ' .
        'AND `user_id` = ?';

      // use findOneQuery to throw exceptions when no entry or more than one
      // entries were found
      //~ $row = $this->findOneQuery($sql, array($id, $userId));
      //~ $feed = new Item();
      //~ $feed->fromRow($row);

      //~ return $feed;
    }


    public function findByUser($user){
      $sql = 'SELECT * FROM `' . $this->getTableName() . '` ' .
      'WHERE `user` = ? ';

      $query = \OCP\DB::prepare($sql);
      $result = $query->execute(array($user));
      $row = $result->fetchRow();
      //var_dump($row);
      if(!$row){
        return null;
      }
      $OtpUserData = new OtpUserData();
      //$row['qrcode']=base64_decode($row['qrcode']);
      $OtpUserData->fromRow($row);

      return $OtpUserData;
    }
    
    public function findQrcodeByUser($user){
      $sql = 'SELECT `qrcode` FROM `' . $this->getTableName() . '` ' .
      'WHERE `user` = ? ';

      $query = \OCP\DB::prepare($sql);
      $result = $query->execute(array($user));
      $row = $result->fetchRow();
      //var_dump($row);
      if(!$row){
        return null;
      }
      if(is_resource($row['qrcode'])){
		 return base64_decode(fgets($row['qrcode'])); 
	  }elseif(base64_encode(base64_decode($row['qrcode'])) === $row['qrcode']){
        return base64_decode($row['qrcode']);
      }else{
        return $row['qrcode'];
      }
    }
    
    public function deleteByUser($user){
      $sql = 'DELETE FROM `' . $this->getTableName() . '` ' .
      'WHERE `user` = ? ';

      $query = \OCP\DB::prepare($sql);
      $result = $query->execute(array($user));

      return $result;
    }
    
    public function insert(OtpUserData $OtpUserData){
      $sql = 'INSERT INTO `' . $this->getTableName() . '` ' .
      '(`user`,`request_prefix_pin`,`algorithm`,`token_seed`,`user_pin`,`number_of_digits`,`time_interval`,`last_event`,`last_login`,`error_counter`,`locked`) '.
      'VALUES(?,?,?,?,?,?,?,?,?,?,?) ';
//print_r($OtpUserData);//exit;
      $query = \OCP\DB::prepare($sql);
      $result = $query->execute(array(
        $OtpUserData->getUser(),
        $OtpUserData->getRequestPrefixPin(),
        $OtpUserData->getAlgorithm(),
        $OtpUserData->getTokenSeed(),
        $OtpUserData->getUserPin(),
        $OtpUserData->getNumberOfDigits(),
        $OtpUserData->getTimeInterval(),
        $OtpUserData->getLastEvent(),
        $OtpUserData->getLastLogin(),
        $OtpUserData->getErrorCounter(),
        $OtpUserData->getLocked(),
      ));

      return $result;
    }
    
    public function update(OtpUserData $OtpUserData){
      $sql = 'UPDATE  `' . $this->getTableName() . '` ' .
      'SET `request_prefix_pin` =?,'.
      '`algorithm` =?,'.
      '`token_seed` =?,'.
      '`user_pin` =?,'.
      '`number_of_digits` =?,'.
      '`time_interval` =?,'.
      '`last_event` =?,'.
      '`last_login` =?,'.
      '`error_counter` =?,'.
      '`locked` =?,'.
      '`qrcode` =? '.
      'WHERE `user`=? ';
//print_r($OtpUserData);
      $query = \OCP\DB::prepare($sql);
      $result = $query->execute(array(
        $OtpUserData->getRequestPrefixPin(),
        $OtpUserData->getAlgorithm(),
        $OtpUserData->getTokenSeed(),
        $OtpUserData->getUserPin(),
        $OtpUserData->getNumberOfDigits(),
        $OtpUserData->getTimeInterval(),
        $OtpUserData->getLastEvent(),
        $OtpUserData->getLastLogin(),
        $OtpUserData->getErrorCounter(),
        $OtpUserData->getLocked(),
        //base64_encode($OtpUserData->getQrcode()),
        $OtpUserData->getQrcode(),
        $OtpUserData->getUser(),
      ));
      
      //~ print_r($query);exit;

      return $result;
    }

}
