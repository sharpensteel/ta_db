<?php

require_once __DIR__.'/utils.php';


/**
 * @param string $key examples: "info", "error"
 * @param mixed $value
 * @param string $separator
 */
function yii_flash_append($key, $value, $separator='<br>')
{
	$value_old = Yii::app()->user->getFlash($key);
	if(!is_empty_string($value_old))
		$value = $value_old.$separator.$value;
	Yii::app()->user->setFlash($key, $value);
}


function error_log_CactiveRecord($model, $method_name=null, $line=null)
{
	$errs = $model->getErrors();	
	$errs_str = json_encode($errs,JSON_UNESCAPED_UNICODE);
	$text = 'error at model '. get_class($model).'!';
	if(!is_empty_string($method_name)){
		$text .= " at {$method_name}:{$line}";
	}	
	
	$text .= " error(s): {$errs_str}";
	error_log($text);
	return $text;
}


/**
 * shorthand for Yii::app()->createUrl()
 * Creates a relative URL based on the given controller and action information.
 * @param string $route the URL route. This should be in the format of 'ControllerID/ActionID'.
 * @param array $params additional GET parameters (name=>value). Both the name and value will be URL-encoded.
 * @param string $ampersand the token separating name-value pairs in the URL.
 * @param string $urlFormat ( CUrlManager::PATH_FORMAT | CUrlManager::GET_FORMAT )
 * @return string the constructed URL
 */
function createUrl($route,$params=array(),$ampersand='&',$urlFormat=CUrlManager::PATH_FORMAT )
{
	$um = Yii::app()->getUrlManager();
	$old_url_format = $um->getUrlFormat();
	
	if($old_url_format !== $urlFormat)
		$um->setUrlFormat($urlFormat);
	
	/*$params_encoded = array();
	foreach($params as $key => $val){
		$params_encoded[urlencode($key)] = urlencode($val);
	}*/	
		
	$res =  $um->createUrl($route, $params, $ampersand);
	
	if($old_url_format !== $urlFormat)
		$um->setUrlFormat($old_url_format);
	return $res;
}


/**
 * shorthand for Yii::app()->request->baseUrl(), makes "/" at end
 */
function baseUrl($absolute=false){
	$base = trim(Yii::app()->request->getBaseUrl($absolute));
	if(substr($base, -1) !== '/')
		$base .= '/';
	return $base;
}




function trace_sql($func_name, $sql, $param_arr, $result=null){
	if(empty($GLOBALS['CONFIG']) || empty($GLOBALS['CONFIG']['ENABLE_TRACE_SQL'])) return;
	
	$result_str = '';
	if(func_num_args()>=4){
		$result_str = "  result: ";
		if(!is_array($result) || !count($result)){
			$result_str .= json_encode($result,JSON_UNESCAPED_UNICODE);
		}
		else{
			$elem = reset($result);
			if(is_array($elem)){
				$result_str .= count($result)." records, {";
				$left = count($result);
				while(strlen($result_str)<100 && $left){ 	
					$left--;
					$result_str .= valueForLog($elem).($left?", ":"");
					$elem = next($result);
				}
				$result_str .= ($left?", ...":'')."}";
			}
			else{
				$result_str .= valueForLog($result);
			}
		}
		
	}
	
	my_log($func_name.": sql: ". json_encode($sql,JSON_UNESCAPED_UNICODE)."  params: ".  valueForLog($param_arr).$result_str);
	
}

/**
 * 
 * @param string $sql example: "select * from table1 where field=:field"
 * @param string[] $param_arr example: array("id"=>492)
 * @return mixed[][]
 */
function query_arr($sql, $param_arr = null){
	
	
	$cmd = Yii::app()->db->createCommand($sql);	
	
	if(is_array($param_arr)){
		foreach($param_arr as $param_name => $param_value){
			$cmd->bindParam(":".$param_name,$param_value);
		}
	}
	$result = $cmd->queryAll();
	trace_sql(__FUNCTION__, $sql, $param_arr, $result);
	return $result;
}


/**
 * 
 * @param string $sql  example: "select field2 from table1 where field1=:field1"
 * @param string[] $param_arr example: array("id"=>492)
 * @return mixed the value of the first column in the first row of the query result. False is returned if there is no value.
 */
function query_scalar($sql, $param_arr = null){
	
	$cmd = Yii::app()->db->createCommand($sql);
	
	if(is_array($param_arr)){
		foreach($param_arr as $param_name => $param_value){
			$cmd->bindParam(":".$param_name,$param_value);
		}
	}
	$result = $cmd->queryScalar();
	trace_sql(__FUNCTION__, $sql, $param_arr, $result);
	return $result;
}

/**
 * 
 * @param string $sql example: "delete from table1 where field1=:field1"
 * @param string[] $param_arr example: array("id"=>492)
 * @return integer 	number of rows affected by the execution.
 */
function query_execute($sql, $param_arr = null){
	$res = 0;
	try
	{
		$cmd = Yii::app()->db->createCommand($sql);
		if(is_array($param_arr)){
			foreach($param_arr as $param_name => $param_value){
				$cmd->bindValue(":".$param_name,$param_value);
			}
		}
		$res = $cmd->execute();
	}
	catch(CDbException $e){		
		
		if( defined('DEVEL_MODE') && constant('DEVEL_MODE') ){
			//echo "rethrow";
			throw $e;
		}
		else{
			error_log(__FUNCTION__.": ".$e->getMessage()."; sql: `".substr($sql,0,300)."`; params:".valueForLog($param_arr));
			return $res;
		}
	}
	trace_sql(__FUNCTION__, $sql, $param_arr, $res);
	return $res;
}
	

/**
 * 
 * @param string $sql  example: "select articleId from category where id = :id"
 * @param mixed[] $sqlParams  example: "select articleId from category where id = :id"
 * @param type $funcOnRowFetch  called on each row; parameter: $row
 * @param type $pdoFetchMode
 * @param type $fetchClassName NOT WORKING NOW!!!  (for $pdoFetchMode = PDO::FETCH_CLASS)
 * @return int nonzero if ok
 */
function queryIterator( $sql, $sqlParams = Array(), $funcOnRowFetch = null, $pdoFetchMode = PDO::FETCH_ASSOC, $fetchClassName = null)
{
	
	$exErrors = 0;
	
	// todo: normal error catching
	
	$cmd = Yii::app()->db->createCommand($sql);
			
	if(is_array($sqlParams)){
		foreach($sqlParams as $param_name => $param_value){
			$cmd->bindValue($param_name,$param_value);
		}
	}
	
	$dataReader = $cmd->query();
	
	$dataReader->setFetchMode($pdoFetchMode);

	
	$cnt = 0;
	//while (($row = $dataReader->read()) !== false){
	foreach($dataReader as $row){
		//$cnt++;
		//if($cnt>3000) break;

		if(!empty($funcOnRowFetch)){				
			$funcOnRowFetch($row);
			$cnt++;
		}
	 }
	 

	trace_sql(__FUNCTION__, $sql, $sqlParams,$cnt.' values iterated');
	
//	
//	global $db;
//	if(empty($db)) dbConnect();
//	
//	try{
//		$STH = $db->prepare($sql);
//
//		$paramPairArr = array();
//		if($sqlParams)
//		foreach($sqlParams as $paramName => $paramValue){
//			$paramPair = array($paramName,$paramValue);
//			$STH->bindParam($paramPair[0],$paramPair[1]);
//			$paramPairArr[] = $paramPair;
//		}
//
//		$STH->execute();
//
//		$STH->setFetchMode($pdoFetchMode);
//
//		while($row = $STH->fetch()) {
//			
//			if(!empty($funcOnRowFetch)){				
//				$funcOnRowFetch($row);
//			}
//			
//		}
//	}
//	catch(PDOException $e) {
//
//		/*ob_start();
//		print_r($sqlParams);
//		$sqlParamsDump = ob_get_clean();*/
//		$sqlParamsDump = json_encode($sqlParams);
//
//		myError($e->getMessage()."\n\tSQL: ".$sql."\n\tparams:".$sqlParamsDump."\r\n");
//		$exErrors = 1;
//	}
//	if(!$exErrors && isset($GLOBALS["enableLogSql"]) && $GLOBALS["enableLogSql"]){
//		/*ob_start();
//		print_r($sqlParams);
//		$sqlParamsDump = ob_get_clean();*/
//		$sqlParamsDump = json_encode($sqlParams);
//		myLog("SQL: ".$sql." params:".$sqlParamsDump."\r\n");
//	}
	return !$exErrors;
}


/**
 * returns reference! example of use: $arr_keyed = &active_record_array_to_associative($arr,'id')
 * 
 * @param type $arr array of ActiveRecord records
 * @param type $key_name
 * @return mixed[] makes array where records accessible by key; all keys casted to string!
 */
function &active_record_array_to_associative(&$arr, $key_name = 'id'){
	$result = array();
	foreach($arr as &$record){
		$result[(string)$record->$key_name] = &$record;
	}
	return $result;
}


/**
 * 
 * @param mixed[] $record_mem_arr   array can be not indexed by key
 * @param string $table_name
 * @param string $key_field_name
 * @param string[] $field_name_arr fields to syncronize
 * @param string $sql_select 
 */
function array_syncronize_with_table($record_mem_arr, $table_name, $key_field_name, $field_name_arr = array(), $sql_select = null, $disable_deleting = false )
{
	my_log(__FUNCTION__.": table_name=$table_name}");
	
	//query_execute("delete from gem_filter where coalesce(name,'')=''");
			
	$field_name_arr_with_key = $field_name_arr;
	if(array_search($key_field_name, $field_name_arr_with_key) === false){
		$field_name_arr_with_key[] = $key_field_name;
	}	
	
	if(!strlen($sql_select)){
		$sql_select_fieldnames = "";
		foreach($field_name_arr_with_key as $field_name){
			if(strlen($sql_select_fieldnames)) $sql_select_fieldnames .= ", ";
			$sql_select_fieldnames .= "`$field_name`";
		}
		$sql_select = "select $sql_select_fieldnames from $table_name";
	}
	
	$record_db_arr_unordered = query_arr($sql_select);
	$record_db_arr = array();
	foreach($record_db_arr_unordered as $record_db){
		$key = (string)$record_db[$key_field_name];
		if(!isset($key)) { error_log(__FUNCTION__.": warning: empty key! table name: {$table_name}, key_field_name:{$key_field_name}"); }
		$record_db_arr[(string)$record_db[$key_field_name]] = &$record_db;
		unset($record_db);
	}
	
	
	

	$insert_values_sql = '';
	$insert_sql = 'insert into `'.$table_name.'` (';
	
	foreach($field_name_arr_with_key as $field_name){
		if(strlen($insert_values_sql)){
			$insert_sql .= ', ';
			$insert_values_sql .= ', ';
		}
		$insert_sql .= "`$field_name`";
		$insert_values_sql .=":{$field_name}";
	}
	$insert_sql .= ') values ('.$insert_values_sql.')';
	
	
	foreach($record_mem_arr as $record_mem){
		$key = (string)$record_mem[$key_field_name];
		if(!isset($record_db_arr[$key]))
		{
			$params = array();
			foreach($field_name_arr_with_key as $field_name){				
				$params[$field_name] = $record_mem[$field_name];
			}
			query_execute($insert_sql,$params);
		}
		else{
			$params = array();		
			$record_db = &$record_db_arr[$key];
			$record_db['record_exists_in_memory'] = 1;
			$update_equating_sql = '';
			foreach($field_name_arr as $field_name){
				$val_mem = (string)$record_mem[$field_name];
				if(!key_exists($field_name,$record_db)){
					error_log(__FUNCTION__.".".__LINE__.": !key_exists  field_name=".$field_name." record_db: ".json_encode($record_db,JSON_UNESCAPED_UNICODE));
				}
				else{
					$val_db = (string)$record_db[$field_name];
				}
				if( $val_mem === $val_db) {	continue; }
				$params[$field_name] = $record_mem[$field_name];
				if(strlen($update_equating_sql)) { $update_equating_sql .= ', '; }
				$update_equating_sql .= "`{$field_name}` = :{$field_name}";
			}
			if(!count($params)) { continue; }
			$params[$key_field_name] = $record_mem[$key_field_name];
			$update_sql = "update `$table_name` set ".$update_equating_sql." where `$key_field_name`=:{$key_field_name}";
			query_execute($update_sql,$params);
			unset($record_db);
		}
	}
	
	if(!$disable_deleting){
		$delete_sql = "delete from `$table_name` where `$key_field_name`=:{$key_field_name}";
		foreach($record_db_arr as $record_db){
			if(isset($record_db['record_exists_in_memory'])) continue;
			query_execute( $delete_sql, array($key_field_name => $record_db[$key_field_name]) );
		}
	}
	
}