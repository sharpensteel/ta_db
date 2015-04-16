<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */

class UserIdentity extends CUserIdentity
{
	
	private $_id;
	private $_login;
	private $_is_admin;
			
	const CRYPT_SALT = 'sdl5EswaalE5SD#@';
	
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		
		$user_record = User::model()->findByAttributes(array('login_lowercase' => mb_convert_case($this->id, MB_CASE_LOWER) ));
		if(!$user_record)
			$this->errorCode=self::ERROR_USERNAME_INVALID;
		else if($user_record->password != self::encrypt($this->password))
			$this->errorCode=self::ERROR_PASSWORD_INVALID;
		else{
			$this->_id = $user_record->id;
			$this->errorCode=self::ERROR_NONE;
		}
		
		return !$this->errorCode;
	}
	
	/**
	 * @param string $password_non_encrypted
	 * @return string 
	 */	
	static public function encrypt($password_non_encrypted){
		return $password_non_encrypted; //crypt($password_non_encrypted,self::CRYPT_SALT.$password_non_encrypted);
	}

	

}