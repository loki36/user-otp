<?php
/**
 * ownCloud - One Time Password plugin
 *
 * @package user_otp
 * @author Frank Bongrand
 * @copyright 2013 Frank Bongrand fbongrand@free.fr
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU AFFERO GENERAL PUBLIC
 * License along with this library. If not, see <http://www.gnu.org/licenses/>.
 * Displays <a href="http://opensource.org/licenses/AGPL-3.0">GNU AFFERO GENERAL PUBLIC LICENSE</a>
 * @license http://opensource.org/licenses/AGPL-3.0 GNU AFFERO GENERAL PUBLIC LICENSE
 *
 */

include_once("user_otp/lib/multiotp/multiotp.class.php");

define("_AUTH_STANDARD_","0");
define("_AUTH_OTP_OR_STANDARD_","1");
define("_AUTH_OTP_ONLY_","2");
define("_AUTH_TWOFACTOR_","3");
define("_AUTH_DEFAULT_",_AUTH_OTP_OR_STANDARD_);

/**
 * Class for user management with OTP if exist else in a SQL Database (e.g. MySQL, SQLite)
 * @package user_otp
 */
class OC_User_OTP extends OC_User_Database{
    private $mOtp;

    /**
     * Constructor sets up {@link $firstvar}
     */
    public function __construct(){
        $this->mOtp =  new multiotp(OCP\Config::getAppValue(
            'user_otp','EncryptionKey','DefaultCliEncryptionKey')
        );
        $this->mOtp->SetUsersFolder(
            OCP\Config::getAppValue(
                'user_otp','UsersFolder',getcwd()."/apps/user_otp/lib/multiotp/users/"
            )
        );
        if(DEBUG===1){
            $this->mOtp->EnableVerboseLog();
        }
        $this->mOtp->SetMaxBlockFailures(
            OCP\Config::getAppValue('user_otp','MaxBlockFailures',6)
        );
    }

    /**
     * check password function
     * @param string $uid user id
     * @param string $password value of the password
     * @return boolean
     */
    public function checkPassword($uid, $password) {
//    $tmp = $this->userExists($uid);
//    var_dump($tmp);
//    echo $uid.'toto'.$this->mOtp->GetUsersFolder();
//    var_dump($_POST);
//    exit;
        //if(!$this->userExists($uid)){
        if(!$this->mOtp->CheckUserExists($uid)){
            return parent::checkPassword($uid, $password);
        }else{
            $this->mOtp->SetUser($uid);
            $authMethode=OCP\Config::getAppValue('user_otp','authMethod',_AUTH_DEFAULT_);
            switch($authMethode){
                case _AUTH_STANDARD_:
                    return parent::checkPassword($uid, $password);
                    break;
                case _AUTH_OTP_OR_STANDARD_:
                    $result = parent::checkPassword($uid, $password);
                    if($result){
                        return $result;
                    }
                    // break; no break beacause we try with OTP
                case _AUTH_OTP_ONLY_:
                    $result = $this->mOtp->CheckToken($password);
                    if ($result===0){
                        return $uid;
                    }else{
                        if(isset($this->mOtp->_errors_text[$result])){
                            echo $this->mOtp->_errors_text[$result];
                        }
                    }
                    return false;
                break;
                case _AUTH_TWOFACTOR_:
                  $result = $this->mOtp->CheckToken($_POST['otpPassword']);
                    if ($result===0){
                      return parent::checkPassword($uid, $password);
                    }else{
                        if(isset($this->mOtp->_errors_text[$result])){
                            echo $this->mOtp->_errors_text[$result];
                        }
                    }
                    return false;
                    break;
            }
        }
    }
    
}
?>
