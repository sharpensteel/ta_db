<?php

class Class_with_properties{
	
	/**
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set ($name , $value ){
		
		$method_name = "set".$name;
		if(method_exists($this, $method_name)){
			$this->$method_name($value, $value);
		}
		
		if (property_exists($this, $name)) {
			$this->$name = $value;
		}
		throw new Exception('property '.$name.' not exists at class '.  get_class($this));
	}
	
	/**
	 * @param string $name
	 */
	public function __get ($name){
		$method_name = "get".$name;
		if(method_exists($this, $method_name)){
			return $this->$method_name();
		}
		
		if (property_exists($this, $name)) {
			return $this->$name;
		}
		throw new Exception('property '.$name.' not exists at class '.  get_class($this));
	}
}
