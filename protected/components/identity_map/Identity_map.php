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
		
		if( !isset($inst->map[$class_name])) {
			$inst->map[$class_name] = array();
		}
		
		$record_arr = &$inst->map[$class_name];
		
		$record_key = (string)$record->primaryKey;
		if(isset($record_arr[$record_key]) && $record_arr[$record_key] !== $record){
			error_log(__METHOD__.": record of type $class_name with key=$record_key already in map but not equal to saved record!");
			return;
		}
		$record_arr[$record->primaryKey] = $record;
	}
	
	/**
	 * called in the end of record->refresh() 
	 * 
	 * @param IMActiveRecord $record
	 */
	public static function replace_record_in_map($record)
	{
		$inst = self::get_instance();
		$class_name = get_class($record);
		
		if( !isset($inst->map[$class_name])) {
			$inst->map[$class_name] = array();
		}
		
		$record_arr = &$inst->map[$class_name];
		
		$record_key = (string)$record->primaryKey;
		$record_arr[$record->primaryKey] = $record;
	}
	
	/**
	 * @param IMActiveRecord $record
	 */
	public static function on_record_deleted($record)
	{
		$inst = self::get_instance();
		$class_name = get_class($record);
		
		if( !isset($inst->map[$class_name])) return;
		
		$record_arr = &$inst->map[$class_name];
		$record_key = (string)$record->primaryKey;		
		
		$record->_set_is_deleted(true);
		
		if(isset($record_arr[$record_key])){
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
		
		if(isset($inst->map[$class_name])){
			$record_arr = $inst->map[$class_name];
			if( isset($record_arr[$record_key]) )
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
		
		if( !isset($inst->map[$class_name])) return null;
		$record_arr = $inst->map[$class_name];
				
		if( !isset($record_arr[$record_key_value]) ) return null;
		
		return $record_arr[$record_key_value];
		
	}
	
	static public function remove_from_map($class_name, $key_value_filter = null){
		$inst = self::get_instance();
		$key_value_filter = (string)$key_value_filter;
		
		if( !isset($inst->map[$class_name])) return;
		$record_arr = &$inst->map[$class_name];
		
		if($key_value_filter != '')
		{
			if(isset($record_arr[$key_value_filter])){
				unset($record_arr[$key_value_filter]);				
			}
		}
		else{
			array_splice($record_arr, 0);
		}
	}
	
}
