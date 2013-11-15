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

include_once("user_otp/lib/multiotpdb.php");

define("_AUTH_STANDARD_","0");
define("_AUTH_OTP_OR_STANDARD_","1");
define("_AUTH_OTP_ONLY_","2");
define("_AUTH_TWOFACTOR_","3");
define("_AUTH_DEFAULT_",_AUTH_OTP_OR_STANDARD_);

/**
 * Class for user management with OTP if user exist in otp db
 * act as manager for other backend
 * @package user_otp
 */
class OC_USER_OTP extends OC_User_Backend{
	/**
 	 * @var \OC_User_Backend[] $backends
	 */
	private static $_backends = null;
	
	/**
 	 * @var Multiotp $mOtp
	 */
    private $mOtp;
    
    private $_userBackend = null;

    /**
     * Constructor sets up {@link $firstvar}
     */
    public function __construct(){
		    //OC_Log::write('OC_USER_OTP', __FUNCTION__.'().', OC_Log::DEBUG);
        $this->mOtp =  new MultiOtpDb(OCP\Config::getAppValue(
            'user_otp','EncryptionKey','DefaultCliEncryptionKey')
        );
        if(defined('DEBUG') && DEBUG===true){
            $this->mOtp->EnableVerboseLog();
        }
        $this->mOtp->SetMaxBlockFailures(
            OCP\Config::getAppValue('user_otp','MaxBlockFailures',6)
        );
        
    }
    
    public static function registerBackends($usedBackends){
      //OC_Log::write('OC_USER_OTP', __FUNCTION__.'().', OC_Log::DEBUG);
      if(self::$_backends === null){
        foreach ($usedBackends as $backend){
          OC_Log::write('user_otp', 'instance '.$backend.' backend.', OC_Log::DEBUG);
          self::$_backends[$backend] = new $backend();
        }
      }
    }
		
	/**
	 * @brief delete a user
	 * @param string $uid The username of the user to delete
	 * @return bool
	 *
	 * Deletes a user
	 */
	public function deleteUser( $uid ) {
		return $this->__call("deleteUser",array($uid));
	}
	
	/**
	 * @brief Create a new user
	 * @param $uid The username of the user to create
	 * @param $password The password of the new user
	 * @returns true/false
	 *
	 * Creates a new user. Basic checking of username is done in OC_User
	 * itself, not in its subclasses.
	 */
	public function createUser( $uid, $password ) {
		return $this->__call("createUser",array($uid,$password));
	}

	/**
	 * @brief Get a list of all users
	 * @returns array with all uids
	 *
	 * Get a list of all users.
	 */
	public function getUsers($search = '', $limit = null, $offset = null) {
		return $this->__call("getUsers",array($search,$limit,$offset));
	}

	/**
	 * @brief check if a user exists
	 * @param string $uid the username
	 * @return boolean
	 */
	public function userExists($uid) {
		//OC_Log::write('OC_USER_OTP', __FUNCTION__.'().', OC_Log::DEBUG);
        if($this->mOtp->CheckUserExists($uid)){
			return true;
		}
		$backend = $this->getRealBackend($uid);
		if($backend===null){
			return false;
		}else{
			return true;
		}
	}

	/**
	 * @brief get the user's home directory
	 * @param string $uid the username
	 * @return boolean
	 */
	public function getHome($uid) {
		return $this->__call("getHome",array($uid));
	}

	/**
 	 * @brief get display name of the user
	 * @param string $uid user ID of the user
	 * @return string display name
	 */
	public function getDisplayName($uid) {
		return $this->__call("getDisplayName",array($uid));;
	}
	
	/**
	 * @brief get user real backend
	 * @param string $uid the username
	 * @return backend
	 */
	public function getRealBackend($uid) {
		//OC_Log::write('OC_USER_OTP', __FUNCTION__.'().', OC_Log::DEBUG);
		if($this->_userBackend !== null){
			return $this->_userBackend;
		}

		foreach (self::$_backends as $backend) {
			if ($backend->userExists($uid)) {
				$this->_userBackend=$backend;
				return $this->_userBackend;
			}
		}
		return null;
	}
	
	public function __call($name, $arguments){
		//OC_Log::write('OC_USER_OTP', $name.'().', OC_Log::DEBUG);
		$userBackend=$this->getRealBackend(OCP\User::getUser());
    //var_dump($userBackend);
		if($userBackend===null){
			return false;
		}
		
		$reflectionMethod = new ReflectionMethod(get_class($userBackend),$name);
		return $reflectionMethod->invokeArgs($userBackend,$arguments);
	}
	
	public function __set($name){
		//OC_Log::write('OC_USER_OTP', $name.'().', OC_Log::DEBUG);
		$userBackend=$this->getRealBackend(OCP\User::getUser());
    //var_dump($userBackend);
		if($userBackend===null){
			return false;
		}
		
		if(isset($userBackend->$name)){
			return $userBackend->$name;
		}else{
			return false;
		}
	}

    /**
     * check password function
     * @param string $uid user id
     * @param string $password value of the password
     * @return boolean
     */
    public function checkPassword($uid, $password) {
		OC_Log::write('OC_USER_OTP', __FUNCTION__.'().', OC_Log::DEBUG);
		$userBackend=$this->getRealBackend($uid);
		if ($userBackend===null){
			return false;
		}
		
		//if access is made by remote.php and option is note set to force mtop, keep standard auth methode
		// this for keep working webdav access and sync apps
		if(
			basename($_SERVER['SCRIPT_NAME']) === 'remote.php'
			&& OCP\Config::getAppValue('user_otp','disableOtpOnRemoteScript',true)
		){
			return $userBackend->checkPassword($uid, $password);
		}

        if(!$this->mOtp->CheckUserExists($uid)){
            OC_Log::write('OC_USER_OTP','No OTP for user '.$uid.' use user backend', OC_Log::DEBUG);
            return $userBackend->checkPassword($uid, $password);
        }else{
            $this->mOtp->SetUser($uid);
            $authMethode=OCP\Config::getAppValue('user_otp','authMethod',_AUTH_DEFAULT_);
            OC_Log::write('OC_USER_OTP','used auth method : '.$authMethode, OC_Log::DEBUG);
            switch($authMethode){
                case _AUTH_STANDARD_:
                    return $userBackend->checkPassword($uid, $password);
                    break;
                case _AUTH_OTP_OR_STANDARD_:
                    $result = $userBackend->checkPassword($uid, $password);
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
                  if(!isset($_POST['otpPassword']) || $_POST['otpPassword']===""){
                    return false;
                  }
                  OC_Log::write('OC_USER_OTP','used OTP : '.$_POST['otpPassword'], OC_Log::DEBUG);
                  $result = $this->mOtp->CheckToken($_POST['otpPassword']);
                    if ($result===0){
                      return $userBackend->checkPassword($uid, $password);
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
