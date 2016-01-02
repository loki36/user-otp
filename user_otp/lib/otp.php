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
         $this->mOtp->SetMaxEventWindow(OCP\Config::getAppValue('user_otp','UserTokenMaxEventWindow',100));
        
    }
    
	public function getSupportedActions() {
		$actions = 0;
		foreach($this->possibleActions AS $action => $methodName) {
			$userBackend=$this->getRealBackend(OCP\User::getUser());
			if($userBackend===null){$userBackend=$this;}
			if(method_exists($userBackend, $methodName)) {
				$actions |= $action;
			}
		}

		return $actions;
	}
    
    public static function registerBackends($usedBackends){
      //OC_Log::write('OC_USER_OTP', __FUNCTION__.'().', OC_Log::DEBUG);
      if(self::$_backends === null){
        foreach ($usedBackends as $backend){
          //OC_Log::write('user_otp', 'instance '.$backend.' backend.', OC_Log::DEBUG);
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
	 * @brief Set password
	 * @param $uid The username
	 * @param $password The new password
	 * @returns true/false
	 *
	 * Change the password of a user
	 */
	public function setPassword( $uid, $password ) {
		return $this->__call("setPassword",array('uid'=>$uid,'password'=>$password));
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
			//little tricky but if user wants create a user uid is not the same as the backend registered!!!
			return $backend->userExists($uid);
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
		OC_Log::write('OC_USER_OTP', $name.'().', OC_Log::DEBUG);
		$userBackend=$this->getRealBackend(OCP\User::getUser());
    //var_dump($userBackend);
		if($userBackend===null){
			//bug fix lost password link
			//print_r($arguments);
			if(isset($arguments['uid'])){
				//print_r($arguments['uid']);
				$userBackend=$this->getRealBackend($arguments['uid']);
			}else{
				return false;
			}
		}
		
		$reflectionMethod = new ReflectionMethod(get_class($userBackend),$name);
		return $reflectionMethod->invokeArgs($userBackend,$arguments);
	}

     /**
     * Check if the source ip is private
     */
     private function is_private_ip($remote_ip) {
        return ! filter_var($remote_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE |  FILTER_FLAG_NO_RES_RANGE);
     }



    /**
     * check password function
     * @param string $uid user id
     * @param string $password value of the password
     * @return boolean
     */
    public function checkPassword($uid, $password) {
		//print_r($_SERVER);
		OC_Log::write('OC_USER_OTP', __FUNCTION__.'().', OC_Log::DEBUG);
		$userBackend=$this->getRealBackend($uid);
		if ($userBackend===null){
			return false;
		}
		
		// enable change password without ipunt OTP
		if(isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO']=="/settings/personal/changepassword"){
			return $userBackend->checkPassword($uid, $password);
		}
		//print_r($_SERVER['PATH_INFO']);exit;

		// if access is made by remote.php and option is note set to force mtop, keep standard auth methode
		// this for keep working webdav access and sync apps
    		// And news api for android new app
    		// And ocsms app, pictures thumbnails, file sharing
                if(
                        ( basename($_SERVER['SCRIPT_NAME']) === 'remote.php' ||
                                ( isset($_SERVER['PATH_INFO']) &&
                                        (
                                                preg_match("#^/apps/news/api/v1-2(.*)$#i", $_SERVER['PATH_INFO']) ||
                                                preg_match("#^/apps/ocsms(.*)$#i", $_SERVER['PATH_INFO']) ||
                                                preg_match("#^/apps/files/api/v1/thumbnail(.*)$#i", $_SERVER['PATH_INFO']) ||
                                                preg_match("#^/apps/files_sharing/api/v1/shares(.*)$#i", $_SERVER['PATH_INFO'])
                                        )
                                )
                        )
                        && OCP\Config::getAppValue('user_otp','disableOtpOnRemoteScript',true)
                )
                {
                        return $userBackend->checkPassword($uid, $password);
                }


         if (OCP\Config::getAppValue('user_otp','bypassOtpOnIntranet',false) && isset($_SERVER['REMOTE_ADDR']) && $this->is_private_ip($_SERVER['REMOTE_ADDR'])) {
             OC_Log::write('OC_USER_OTP','Skipping OTP for user '.$uid.' from private ip '.$_SERVER['REMOTE_ADDR'], OC_Log::WARN);
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
                  if(OCP\Config::getAppValue('user_otp','inputOtpAfterPwd','0')==='1') {
					    $this->mOtp->SetUser($uid);
					    $otpSize = $this->mOtp->GetTokenNumberOfDigits() + (
					      strlen($this->mOtp->GetUserPin())* $this->mOtp->GetUserPrefixPin()
					    );
						$_POST['otpPassword']=substr($password,-$otpSize);
						$password = substr($password,0,strlen($password) - $otpSize);
						//~ var_dump($this->mOtp->GetUserPrefixPin());
						//~ var_dump($otpSize);
						//~ var_dump($password);
						//~ var_dump($_POST['otpPassword']);
				  }
				  //~ var_dump($password);
						//~ var_dump($_POST['otpPassword']);
				  //~ exit;
                  
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
