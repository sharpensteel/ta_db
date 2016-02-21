<?php


/**
 * additional state data: login, is_admin, customer_id
 */
class WebUser extends CWebUser{
	//public $allowAutoLogin=true;



	public function init() {
		parent::init();
	}
	
	/**
	 * @result boolean returns true if ok
	*/
	public function authorization_http_basic__check(){
		
		if(!$this->isGuest){
			return true;
		}
		
		$error_text = 'Вход только для авторизованных пользователей';
		
		if (isset($_SERVER['PHP_AUTH_USER'])) {
						
			$identity=new UserIdentity( array_default($_SERVER,'PHP_AUTH_USER'), array_default($_SERVER,'PHP_AUTH_PW') );
			$identity->authenticate();
			if($identity->errorCode===UserIdentity::ERROR_NONE)
			{
				$duration = 3600; // 1 hour 
				$this->login($identity,$duration);
				return true;
			}
			else{
				$error_text = 'Неверное имя пользователя или пароль';
			}
			
		}
		header('WWW-Authenticate: Basic realm="Tibetium alliance DataBase"');
		header('HTTP/1.0 401 Unauthorized');
		echo $error_text;
		return false;
	}
	

	
	
	
	
	/**
	 * @return int 1|0
	 */
	public function getIs_admin(){ return $this->getState('is_admin'); }

	/** @return string */
	public function getLogin(){ return $this->getState('login'); }
	

	
	public function afterLogin($fromCookie) {
		
		parent::afterLogin($fromCookie);
		
		$user = User::model()->findByAttributes(array( 'login_lowercase' => mb_convert_case($this->id, MB_CASE_LOWER) ));
		
		$this->setState('login',$user->login);
		$this->setState('is_admin',$user->is_admin);
		$this->setState('user_id',$user->id);
		
		//Session_manager::after_login();
	}
	
}