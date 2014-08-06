<?php

// общий план:
// 1. Identity Map (уже есть), включая перекрытие instantiate/afterSave/afterDelete/refresh/
// 2. перекрыть findByPk
// 3. построение дерева по массиву записей - это решается кэшированием


class Identity_map
{
	
	/* @var integer[string][string] information about record revisions.
	 * structure: 
	 * array(
	 *     'Table1' => array(
	 *         record1_key => record1,
	 *		   ...
	 *     )
	 *     ... 
	 * ) 
	 */
	public $map = array();
	
	/**
	 * @var Identity_map instance 
	 */
	private static $instance = null;
	
	/** @return Identity_map */
	private static function get_instance()
	{
		if(!self::$instance){
			self::$instance = new Identity_map;
		}
		return self::$instance;
	}
	
	
	/**
	 * @param IMActiveRecord $record
	 */
	public static function on_record_saved($record)
	{
		$inst = self::get_instance();
		$class_name = get_class($record);
		
		if( !key_exists($class_name, $inst->map)) {
			$inst->map[$class_name] = array();
		}
		
		$record_arr = &$inst->map[$class_name];
		
		$record_key = (string)$record->primaryKey;
		if(key_exists($record_key, $record_arr) && $record_arr[$record_key] !== $record){
			error_log(__METHOD__.": record of type $class_name with key=$record_key already in map but not equal to saved record!");
			return;
		}
		$record_arr[$record->primaryKey] = $record;
	}
	
	/**
	 * @param IMActiveRecord $record
	 */
	public static function on_record_deleted($record)
	{
		$inst = self::get_instance();
		$class_name = get_class($record);
		
		if( !key_exists($class_name, $inst->map)) return;
		
		$record_arr = &$inst->map[$class_name];
		$record_key = (string)$record->primaryKey;		
		
		$record->_set_is_deleted(true);
		
		if(key_exists($record_key, $record_arr)){
			unset($record_arr[$record_key]);
		}
	}
	
	/**
	 * 
	 * @param IMActiveRecord $model
	 * @param mixed[] $attributes
	 * @param callable $function_create_instance called when record with same id not exists; params: $attributes
	 * @return IMActiveRecord|null return record if it was loaded/saved early
	 */
	public static function on_record_instantiated($model, $attributes)
	{
		//if( $model->getIsNewRecord() ) return null;
		
		$class_name = get_class($model);
		$record_key = (string)$model->get_primary_key_from_attr($attributes);		
		
		if($record_key === null){
			return call_user_func($function_create_instance, $attributes);
		}
		
		$inst = self::get_instance();
		
		$instance = null;
		
		if(key_exists($class_name, $inst->map)){
			$record_arr = $inst->map[$class_name];
			if( key_exists($record_key, $record_arr) )
				$instance = $record_arr[$record_key];
		}
		else{
			$inst->map[$class_name] = array();
		}
		
		if($instance === null){
			$instance = $model->call_parent_instantiate($attributes);
			if($instance !== null)
				$inst->map[$class_name][$record_key] = $instance;
		}
		
		return $instance;
	}
		
	public static function get_record($class_name, $record_key_value)
	{		
		$inst = self::get_instance();
		$record_key_value = (string)$record_key_value;
		
		if( !key_exists($class_name, $inst->map)) return null;
		$record_arr = $inst->map[$class_name];
				
		if( !key_exists($record_key_value, $record_arr) ) return null;
		
		return $record_arr[$record_key_value];
		
	}
	
}
