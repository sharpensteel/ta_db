<?php

class WebApplication extends CWebApplication {
    protected function init()
    {
		$this->onException = function($event){

			error_log('WebApplication onException  type:'.get_class($event->exception).', message: '.$event->exception->getMessage().
				"   exception data: ". json_encode($event->exception, defined('JSON_UNESCAPED_UNICODE')?JSON_UNESCAPED_UNICODE:0 ).
				"  referer: ".array_default( $_SERVER,'HTTP_REFERER','')
			);

		};
        //register_shutdown_function(array($this, 'onShutdownHandler'));
        parent::init();
    }

	/**
	 * Getter magic method.
	 * This method is overridden to support accessing application components
	 * like reading module properties.
	 * @param string $name application component or property name
	 * @return mixed the named property value
	 */
	public function __get($name)
	{
		if($name === 'user'){
			my_log(__METHOD__.":  hasComponent:".($this->hasComponent($name)?:0)."; getComponent: ".($this->getComponent($name)?1:0));
		}
		if($this->hasComponent($name))
			return $this->getComponent($name);
		else
			return parent::__get($name);
	}

	public function isAdmin(){
		return array_default($_SESSION, 'is_admin',0);
	}

	public function setIsAdmin($is_admin){
		session_start_if_not();
		$_SESSION['is_admin'] = $is_admin;
	}
}
