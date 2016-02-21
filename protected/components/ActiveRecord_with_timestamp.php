<?php


/**
 * examples of access timestamp values in integer form:
 *	$this->dt_created__tt = time(); // where "dt_created" is timestamp field
 *	$tt = $this->dt_last__tt;       // where "dt_last" is timestamp field
 * 
 */
class AR_timestamp_behavior extends CActiveRecordBehavior
{
	
	const NOT_SET = -1010101010;
	
	/** 
	 * all existing 'timestamp' fields in integer form
	 *  example after init(): array('dt_created' => self::NOT_SET, dt_updated' => self::NOT_SET) 
	 * 
	 * @var mixed[]
	 */
	public $timestamps_tt = array();


	public function attach($owner){
		parent::attach($owner);
		$columns = $owner->getMetaData()->columns;
		foreach($columns as $field_name => $column){
			if($column->dbType !== 'timestamp') continue;
			$this->timestamps_tt[$field_name] = self::NOT_SET;
		}
	}
	
	
	
	public function beforeSave($event){
		foreach($this->timestamps_tt as $field_name => $tt){
			if($tt === self::NOT_SET) continue;
			$this->owner->setAttribute($field_name, date('Y-m-d H-i-s', $tt) );
		}
		return parent::beforeSave($event);
	}
	
	public function afterFind($event){
		foreach(array_keys($this->timestamps_tt) as $field_name){
			if($this->timestamps_tt[$field_name] !== self::NOT_SET) continue;
			$val = $this->owner->getAttribute($field_name);
			if(!isset($val)) { continue; }
			$this->timestamps_tt[$field_name] = (int)self::str_to_tt($val);
		}
		return parent::afterFind($event);
	}
	
	public static function str_to_tt($str){
		if(!strlen($str)) return 0;
		return CDateTimeParser::parse($str, 'yyyy-MM-dd HH:mm:ss');
	}
	
	
	/**
	 * @param string $field_name_tt example: `dt_tt`
	 * @return string|false returns string without `_tt` suffix or FALSE if it's not timestamp field
	 */
	public function field_name_tt_remove($field_name_tt){
		if(substr($field_name_tt, -4)!=='__tt') { return false; }
		
		$field_name = substr($field_name_tt, 0, strlen($field_name_tt)-4);
		if(!key_exists($field_name, $this->timestamps_tt)) { return false; }
		return $field_name;
	}
	/**
	 * Determines whether a property can be read.
	 * A property can be read if the class has a getter method
	 * for the property name. Note, property name is case-insensitive.
	 * @param string $name the property name
	 * @return boolean whether the property can be read
	 * @see canSetProperty
	 */
	public function canGetProperty($name)
	{
		if($this->field_name_tt_remove($name) !== false)
			return true;			
		return parent::canGetProperty($name);
	}

	/**
	 * Determines whether a property can be set.
	 * A property can be written if the class has a setter method
	 * for the property name. Note, property name is case-insensitive.
	 * @param string $name the property name
	 * @return boolean whether the property can be written
	 * @see canGetProperty
	 */
	public function canSetProperty($name)
	{
		if($this->field_name_tt_remove($name) !== false)
			return true;			
		return parent::canSetProperty($name);
	}
	

	/**
	 * PHP getter magic method.
	 * @param string $name property name
	 * @return mixed property value
	 * @see getAttribute
	 */
	public function __get($name)
	{
		$field_name = $this->field_name_tt_remove($name);
		
		if($field_name !== false){
			$value = $this->timestamps_tt[$field_name];
			if($value === self::NOT_SET){
				$value_str = $this->owner->getAttribute($field_name);
				$value = $this->timestamps_tt[$field_name] = self::str_to_tt($value_str);
			}
			return $value;
		}		
		
		return parent::__get($name);
	}
	
	
	/**
	 * PHP setter magic method.
	 * @param string $name property name
	 * @param mixed $value property value
	 */
	public function __set($name,$value)
	{
		$field_name = $this->field_name_tt_remove($name);
		
		if($field_name !== false){
			$this->timestamps_tt[$field_name] = $value;
			return;
		}
		
		parent::__set($name,$value);
	}
	
//	
//	public function attributeNames()
//	{
//		echo "ololo";
//		exit;
//		$attrs = array_flip($this->owner->attributeNames());
//		foreach($this->timestamps_tt as $k => $v){
//			$attrs[$k.'__tt'] = 0;
//		}
//		return array_keys($attrs);
//	}	
//	
//	
//	
//	/**
//	 * Returns the attribute names that are safe to be massively assigned.
//	 * A safe attribute is one that is associated with a validation rule in the current {@link scenario}.
//	 * @return array safe attribute names
//	 */
//	public function getSafeAttributeNames()
//	{
//		echo "ololo";
//		exit;
//		$attrs = array_flip(parent::getSafeAttributeNames());
//		foreach($this->timestamps_tt as $k => $v){
//			$attrs[$k.'__tt'] = 0;
//		}
//		return array_keys($attrs);
//		
//	}
}
