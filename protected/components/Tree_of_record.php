<?php

class Tree_of_record{

	
	private static $table_arr = array();
		
	
	/**
	 * @param string $table_name 
	 * @param string $field_key 
	 * @param string $field_parent_key 
	 * @param string $sql if empty, created sql like: 'select <key>,<parent_key> from <table_name>'; params syntax exemple: 'select ... from ... where somefield1=:somefield1'
	 * @param string[string] $sql_param_arr; exmaple: array('somefield1' => ...)
	 * 
	 * @return mixed[string]  array('<record_key>' =>  array( 'parent'=>..., 'childrens' => ..., 'childrens_all' => ..., 'parents_all' => ..., <key>=>..., <parent_key>=>... ), ...  )
	 */
	static public function get_table($table_name, $field_key = 'id', $field_parent_key ='parent_id', $sql = null, $sql_param_arr = array()){
		if(key_exists($table_name, static::$table_arr)){
			return static::$table_arr[$table_name];
		}
		
		$cache_entry_name = 'Tree_builder__'.$table_name;
		$record_arr = Yii::app()->cache->get($cache_entry_name);
		if($record_arr !== false){
			static::$table_arr[$table_name] = $record_arr;
			return $record_arr;
		}
		
		
		if(!strlen($sql)) $sql = "select `".$field_key."`, `".$field_parent_key."` from `".$table_name."`";
		
		$cmd = Yii::app()->db->createCommand($sql);	
		if(is_array($sql_param_arr)){
			foreach($sql_param_arr as $param_name => $param_value){
				$cmd->bindParam(":".$param_name,$param_value);
			}
		}
		$record_unordered_arr =	$cmd->queryAll();
		
		
		$record_arr = array();
	
		foreach($record_unordered_arr as $record_src){
			$record_arr[$record_src[$field_key]] = array($field_parent_key => $record_src[$field_parent_key], $field_key => $record_src[$field_key],
				'childrens' => array(), 'childrens_all' => array(), 'parent' => null);
			
		}
		
		// set `parent` and `childrens` fields
		foreach ($record_arr as $key => &$record) {
			if(!key_exists($record[$field_parent_key], $record_arr)) continue;
			$parent = &$record_arr[$record[$field_parent_key]];
			$parent['childrens'][$record[$field_key]] = &$record;
			$record['parent'] = &$parent;
			unset($parent);
		}
		unset($record);			
		// set `parents_all` and 'childrens_all'
		foreach ($record_arr as $key => &$record) {
			if($record['parent'] === null) continue;
			$parent = &$record['parent'];
			while($parent !== null){
				$parent['childrens_all'][$record[$field_key]] = &$record;
				$record['parents_all'][$parent[$field_key]] = &$parent;
				$parent = &$parent['parent'];
			}
			unset($parent);
		}
		unset($record);
		
		
		static::$table_arr[$table_name] = $record_arr;
		
		Yii::app()->cache->set($cache_entry_name, $record_arr, (int)array_default($GLOBALS, 'CACHE_SQL_LIFETIME_BY_DEFAULT', 30));
		
		return $record_arr;
		
		
	}
	
}