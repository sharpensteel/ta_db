<?php

// requires to setup $GLOBALS['CONFIG']['LOG_PATH']  for my_log())

$glTimeStart = microtime(1);


/** @type for default params in functions; to detect that parameter is exacly OMMITED, and not passed null or something like that */
class MAGIC_UNUSED_VALUE{};



function is_empty_string($str){
	return trim((string)$str) === "";
}

function print_r_to_string($var){
	ob_start();
	print_r($var);
	return ob_get_clean();
}

//function errlog($text){
//	error_log("Error: ".$text);
//}

function my_log($text){	
	if(empty($GLOBALS['CONFIG']) || empty($GLOBALS['CONFIG']['LOG_PATH'])){ // path to log not configured
		error_log("my_log: ".$text."\n");
	}
	else{
		error_log(date("[Y-M-d H:i:s] ").$text."\n", 3, $GLOBALS['CONFIG']['LOG_PATH']);
	}
}

function my_error($text){
	my_log("ERROR: ".$text);
	error_log($text);
}


function vd($var){
	echo "<pre>";
	var_dump($var);
	echo "</pre>";
}

function pre($var){
	return "<pre>".$var."</pre>";
}



function pr($var){
	echo "<pre>";
	if(isset($var))
		print_r($var);
	else
		echo "null";
	echo "</pre>";
}

function valueForLog($value){
	$str = json_encode ($value, defined('JSON_UNESCAPED_UNICODE')?JSON_UNESCAPED_UNICODE:0 );
	if(strlen($str) > 300 ){
		$str = substr( $str, 0, 300 )."...";
	}
	return $str;
}


function randString($length, $charset='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789')
{
    $str = '';
    $count = strlen($charset);
    while ($length--) {
        $str .= $charset[mt_rand(0, $count-1)];
    }
    return $str;
}


function delete_dir($dirPath) {
	$res = 1;
    if (! is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            if(!delete_dir($file)) $res=0;
        } else {
            if(!unlink($file)){
				error_log(__FUNCTION__.": unable to delete ".$file);
				$res=0;
			}
        }
    }
    if(!rmdir($dirPath)) $res=0;
	return $res;
}



/**
 * @param string $url
 * @param array $curl_options_additional; example: array(CURLOPT_POST => true, CURLOPT_HTTPHEADER => array('application/x-www-form-urlencoded') )
 * @return string if ok, or FALSE on error
 */
function curl_get_contents($url, $curl_options_additional=null, $throw_exceptions=false)
{
	//TRACE_CALL(__METHOD__, func_get_args());
	static $ch = 0;
	
	if(!function_exists('curl_init')){
		error_log("curl not enabled!!");
		if($throw_exceptions) throw new Exception ("curl not enabled!!");
		exit;
	}
	
	if(!$ch) $ch = curl_init();
	
	$ok = 1;
	
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, 1);	
		
	curl_setopt($ch, CURLOPT_URL, $url); 
	
	if(!empty($curl_options_additional)){
		foreach ($curl_options_additional as $key => $val){
			curl_setopt($ch, $key, $val); 
		}
	}
		
	
	
	$response = curl_exec($ch);
	
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$header = substr($response, 0, $header_size);
	$body = substr($response, $header_size);

	
	$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);		
	
	$errorStr = "";
	
	if(curl_errno($ch))
	{
		$errorStr .= 'Curl error: ' . curl_error($ch).' ';
		$ok = 0;
	}
	
	if( $status<200 || $status>299 ){
		$errorStr .= 'Response http status code: ' . $status.'. ';
		$ok = 0;
	}
	

	if(!$ok){
		$errorStr = "error in ".__FUNCTION__.":\nurl: ".$url."\nerror:".$errorStr."\n";
		if($throw_exceptions) throw new Exception ($errorStr);
		$errorStr .= "response header: ".$header."\nresponse body: ".$body;
		error_log($errorStr);		

	}
	else{
		my_log(__METHOD__.": response body: ".$body."  url:".$url);
	}

	return $ok ? $body : FALSE;
}



function session_start_if_not(){
	/*global $isSectionStarted;
	if(!isset($isSectionStarted)){
		session_start();
		$isSectionStarted = 0;
	}*/
	
	if(session_id() == ''){
		try{
			$res = @session_start();
			if(!$res){
				error_log(__FUNCTION__.": error in session_start(); reseting session.");
				session_reset(); 
			}
		}
		catch(Exception $e){
			error_log(__FUNCTION__.": ".$e->getMessage()."; reseting session.");
			session_reset();
		}
	}
}

function ensureEndsWithSlash($url)
{
	return rtrim($url," /")."/";
}
	
function ensureNotEndsWithSlash($url)
{
	return rtrim($url," /");
}




function &array_default(&$arr, $index, $default_value=0){
	if(empty($arr)) return $default_value;	
	$res = $default_value;
	if(is_array($arr) && isset($arr[$index])){
		$res = &$arr[$index];
		return $res;
	}
	else if(isset($arr->$index)) {
		$res = &$arr->$index;
		return $res;
	}
	if(is_array($res)) error_log (__FUNCTION__.': returned copy of array! this function can\'t work correct with array of arrays!');
	
	return $default_value;
}

function array_default_not_ref($arr, $index, $default_value=0){		
	if(empty($arr)) return $default_value;	
	if(is_array($arr) && isset($arr[$index])){
		return $arr[$index];
	}
	if(isset($arr->$index)) {
		return $arr->$index;
	}
	return $default_value;
}


function array_first_or_default($arr, $default_value=null){
	$res = $default_value;
	if(is_array($arr) && count($arr)){
		reset($arr);
		$res = current($arr);
	}	
	
	if(is_array($res)) error_log (__FUNCTION__.': returned copy of array! this function can\'t work correct with array of arrays!');
	
	return $res;
}

function array_first_or_default_not_ref($arr, $default_value=null){
	$res = $default_value;
	if(is_array($arr) && count($arr)){
		reset($arr);
		$res = current($arr);
	}	
	return $res;
}







class AutoloadSimple1{	
	private static $rootDirArr = 0;
	private static $isRegistered = 0;
	
	static public function registerRootDirectory($rootDir)
	{
		if(!self::$isRegistered) spl_autoload_register('AutoloadSimple::autoload');
		
		if(empty(self::$rootDirArr)) self::$rootDirArr = array();
		
		array_push(self::$rootDirArr, $rootDir);
	}
	
	static private function autoload($classname)
	{
				
		if( class_exists( $classname, false ))
			return true;
				

		$classparts = explode( '\\', $classname );
		$classfile = '/' . array_pop( $classparts ) . '.php';
		$namespace = implode( '\\', $classparts );

		if(!empty(static::$rootDirArr)){
			foreach(static::$rootDirArr as $rootDir){
				$filename = $rootDir . '/' . $namespace . $classfile;
				
				if( is_readable($filename)){
					include_once $filename;
				}
			}
		}
		
	}
}


function mb_uppercase_first_letter($src){
	return mb_convert_case(mb_substr($src, 0, 1),  MB_CASE_UPPER).mb_substr($src, 1);
}

function removeBOM($str=""){
	if(substr($str, 0,3) == pack("CCC",0xef,0xbb,0xbf)) {
		$str=substr($str, 3);
	}
	return $str;
}


function string_simplify_for_url($str){
	$str = strip_tags($str);
	$str = str_replace( array('<','>','"',"'",'«','»','`','&laquo;','&raquo;',"\n",) , "", $str);
	$str = str_replace( array(' ','&nbsp;',"\r","\t") , "_", $str);
	$str = str_replace( '%' , "проц.", $str);
	$str = preg_replace("/_+/","_",$str);
	$str = preg_replace("/_+/","_",$str);
	
	$str = trim($str," _");
	//$str = urlencode($str);
	return $str;
}


/**
 * appends $url_begin with  "?" or "&" and with $url_parameters
 * @param string $url_begin
 * @param string $url_parameters
 */
function url_append_parameters($url_begin, $url_parameters){
	return $url_begin . (strpos($url_begin,"?")===false?"?":"&") . $url_parameters;
}



/**
* 
* @param array $arr array of records; each record is array('key'=>?, 'parent_key'=>?, 'field_aaa'=>?, 'field_bbb'=>? ....)
* @param string $key_field_name  name of key field in record
* @param string $parent_key_field_name name of key field in record
* @param object $key_value key value of parent record
* @return array associative array with parent record of original records and all childrens of parent record
*/
function get_parent_record_with_childrens($arr, $key_field_name, $parent_key_field_name, $key_value){
   if(empty($key_value)) return array();
   $key_filter_arr = array($key_value => 0);
   $result_rec_arr = array();

   while(count($key_filter_arr)){
	   $children_key_filter_arr = array();
	   foreach($key_filter_arr as $key_filter => $val_unused){
		   foreach($arr as $rec){
			   $val_key_parent = array_default($rec, $parent_key_field_name);
			   $val_key = $rec[$key_field_name];
			   if( $val_key != $key_filter && $val_key_parent != $key_filter ) continue;
			   $result_rec_arr[$val_key] = $rec;
			   if(!empty($val_key_parent) && $val_key_parent != $key_filter){
				   if(empty($result_rec_arr[$val_key_parent])){
					   $children_key_filter_arr[$val_key_parent] = 0;
				   }
			   }

		   }
	   }			
	   $key_filter_arr = $children_key_filter_arr;
   }
   return $result_rec_arr;
}



/**
 * 
 * @param int $n
 * @param string $form1 x1 <письмо>
 * @param string $form2 x2..x4 <письма>
 * @param string $form5 x5..x10 <писем>
 * @return string
 */
function plural_form($n, $form1, $form2, $form5)
{
    $n = abs($n) % 100;
    $n1 = $n % 10;
    if ($n > 10 && $n < 20) return $form5;
    if ($n1 > 1 && $n1 < 5) return $form2;
    if ($n1 == 1) return $form1;
    return $form5;
}




/**
 * Convert PHP date() style dateFormat to the equivalent jQuery UI datepicker string
 */
function dateStringToDatepickerFormat($dateString)
{
	$pattern = array(
		//day
		'd',	//day of the month
		'j',	//3 letter name of the day
		'l',	//full name of the day
		'z',	//day of the year

		//month
		'F',	//Month name full
		'M',	//Month name short
		'n',	//numeric month no leading zeros
		'm',	//numeric month leading zeros

		//year
		'Y', //full numeric year
		'y'	//numeric year: 2 digit
		);
		$replace = array(
		'dd','d','DD','o',
		'MM','M','m','mm',
		'yy','y'
	);
	foreach($pattern as &$p)
	{
		$p = '/'.$p.'/';
	}
	return preg_replace($pattern,$replace,$dateString);
}


/**
 * @param string $date_mysql
 * @param string $format for date() function
 */
function mysql_date_parse_and_format($date_mysql, $format = "d.m.Y")
{
	$dt = strtotime($date_mysql);
	return $date_mysql > 0 ? date("d.m.Y", $dt) : "";
}



function log_finish(){
	my_log("REQUEST: ".(isset($_SERVER["REQUEST_URI"])?$_SERVER["REQUEST_URI"]:"NONE")."  ".$_SERVER['REMOTE_ADDR']." -----------------------");
}


function my_exception_handler($exceptionObj)
{
	static $isInside = 0;
	if($isInside) return;
	$isInside= 1;
	ob_start();
	print_r($exceptionObj);
	$exceptionText = ob_get_clean();  
	error_log("Exception: ".$exceptionText);
	$isInside=0;
}

//throw new Exception('exception test.');


function my_shutdown_handler() //will be called when php script ends.
{
	
	
	$lasterror = error_get_last();
	if($lasterror){
		switch ($lasterror['type'])
		{
			case E_ERROR:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_USER_ERROR:
			case E_RECOVERABLE_ERROR:
			case E_CORE_WARNING:
			case E_COMPILE_WARNING:
			case E_PARSE:
				$error = "[ERROR FORCES SHUTDOWN] lvl:" . $lasterror['type'] . " | msg:" . $lasterror['message'] . " | file:" . $lasterror['file'] . " | ln:" . $lasterror['line'];
				my_error($error);
		}
	}
	if(!empty($GLOBALS['ENABLE_LOG_REQUEST'])){
		log_finish();
	}
}


function enable_log_request(){
	$GLOBALS['ENABLE_LOG_REQUEST'] = 1;

	my_log("REQUEST: ".(isset($_SERVER["REQUEST_URI"])?$_SERVER["REQUEST_URI"]:"NONE")."  ".$_SERVER['REMOTE_ADDR']." +++++++++++++++++++++++");
	
	set_exception_handler('my_exception_handler');
	register_shutdown_function("my_shutdown_handler");
}

function filter_var_sanitize_filename($dirty){
	$dirty = trim($dirty);
	/*$res = "";
	$arr = str_split($str);
	foreach($arr as $char){
		if( preg_match("/(\w|\d|\(|\)|-|[ ,_`~!@#$%^&_=+’.©«»®€™¥£¼½¾])+/u", $char) )
				$res .= $char;
	}
	return $res;*/
	$cnt = preg_match_all("/(\w|\d|\(|\)|-|[ ,_`~!@#$%^&_=+’.©«»®€™¥£¼½¾])+/u", $dirty, $matches);
	if(is_array($matches) && is_array($matches[0]))
		return implode("",$matches[0]);
	return "";
}


function date_string_to_timestamp($str, $format = 'd.m.Y'){
	if(!strlen($str)) return 0;
	$arr = date_parse_from_format($format, $str);
	if(!$arr || $arr['errors']) return 0;
	
	return mktime((int)$arr['hour'],(int)$arr['minute'],(int)$arr['second'],(int)$arr['month'],(int)$arr['day'],(int)$arr['year']);
	
}

/**
 * @param int|string $error_code
 * @param string $lang 'ru'|'en' supported
 * @return string
 */
function get_file_error_description($error_code, $lang='ru'){
	$error_code = (int)$error_code;
	
	
	
	$description_arr = 
	array(
		'en' =>	array( 
			'unknown' => 'Unknown file loading error code',
			0=>"There is no error, the file uploaded with success", 
			1=>"The uploaded file exceeds the upload_max_filesize directive in php.ini", 
			2=>"The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
			3=>"The uploaded file was only partially uploaded", 
			4=>"No file was uploaded", 
			6=>"Missing a temporary folder"
		),
		'ru' => array(
			'unknown' => 'Ошибка при загрузке файла, код неизвестен',
			0=>"Файл удачно загружен", 
			1=>"The uploaded file exceeds the upload_max_filesize directive in php.ini", 
			2=>"The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
			3=>"Файл был загружен не полностью", 
			4=>"Отсутствует файл для загрузки", 
			6=>"Missing a temporary folder"
		)
	); 
	if(!key_exists($lang, $description_arr))
		$lang = 'en';
	
	$d_l_arr = $description_arr[$lang];
	if(!key_exists($error_code,$d_l_arr)) { $error_code = 'unknown'; }
	
	return $d_l_arr[$error_code]." (".$error_code.")";
	
}

function remove_bom($str){
	$str = (string)$str;
	if(substr($str, 0,3) == pack("CCC",0xef,0xbb,0xbf)) {
		$str=substr($str, 3);
	}
	return $str;
}




/**
 * 
 * @param strin $xml_string
 * @param $schema_path use schema validation with schema in file
 * @param $schema_string use schema validation with schema in string
 * @return SimpleXMLElement|string return SimpleXMLElement object if validation ok, or string with error description
 */
function simplexml_load_and_validate($xml_string, $schema_string=false)
{	
	
	libxml_clear_errors();
	libxml_use_internal_errors(true);
	
	$simple_xml_element = simplexml_load_string($xml_string);
		
	$errors = libxml_get_errors();
	$result = $simple_xml_element;
	
	$err = "";

	if(!count($errors)){
		if(!($simple_xml_element instanceof SimpleXMLElement)){ return "simple_xml_element is not SimpleXMLElement"; }
			

		if($schema_string !== false){
			$dom_sxe = dom_import_simplexml($simple_xml_element);
			if($dom_sxe === false) { return "dom_import_simplexml() returns error"; }

			$dom = new DOMDocument('1.0');
			$dom_sxe = $dom->importNode($dom_sxe, true);
			$dom_sxe = $dom->appendChild($dom_sxe);

			if ( !$dom->schemaValidateSource( $schema_string) ) {
				$err = "xml not passes schema validation";
				$errors = libxml_get_errors();
			}		
		}

	}
		
		
	if(count($errors)){
		if(!is_string($err)) $err = "";
		foreach ($errors as $error) {
			$err .= "\n";
			//$err  = $xml[$error->line - 1] . "\n";
			//$err .= str_repeat('-', $error->column) . "^\n";

			switch ($error->level) {
				case LIBXML_ERR_WARNING:
					$err .= "Warning $error->code: ";
					break;
				 case LIBXML_ERR_ERROR:
					$err .= "Error $error->code: ";
					break;
				case LIBXML_ERR_FATAL:
					$err .= "Fatal Error $error->code: ";
					break;
			}

			$err .= trim($error->message) .
					   " at line: $error->line" .
					   " column: $error->column";

			if ($error->file) {
				$err .= " at file: $error->file";
			}

			return $err;
		}
		return $err;
	}


	
	
	libxml_clear_errors();	
	
	return $simple_xml_element;
}




function sxiToArray($sxi){
  $a = array();
  for( $sxi->rewind(); $sxi->valid(); $sxi->next() ) {
    if(!array_key_exists($sxi->key(), $a)){ // array_key_exists() may be slow, change to isset()
      $a[$sxi->key()] = array();
    }
    if($sxi->hasChildren()){
      $a[$sxi->key()][] = sxiToArray($sxi->current());
    }
    else{
      $a[$sxi->key()][] = strval($sxi->current());
    }
  }
  return $a;
}

function load_function__array_column()
{
	if (!function_exists('array_column')){
		require_once __DIR__.'/array_column.php';
	}
		
}

function ob_capture($callback){
	ob_start();
	call_user_func($callback);
	return ob_get_clean();
}



/**
 * Drop-in replacement for pathinfo(), but multibyte-safe, cross-platform-safe, old-version-safe.
 * Works similarly to the one in PHP >= 5.2.0
 * @link http://www.php.net/manual/en/function.pathinfo.php#107461
 * taken from joomla sources
 * @param string $path A filename or path, does not need to exist as a file
 * @param integer|string $options Either a PATHINFO_* constant, or a string name to return only the specified piece, allows 'filename' to work on PHP < 5.2
 * @return string|array
 * @static
 */
function mb_pathinfo($path, $options = null) {
  $ret = array('dirname' => '', 'basename' => '', 'extension' => '', 'filename' => '');
  $m = array();
  preg_match('%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im', $path, $m);
  if(array_key_exists(1, $m)) {
    $ret['dirname'] = $m[1];
  }
  if(array_key_exists(2, $m)) {
    $ret['basename'] = $m[2];
  }
  if(array_key_exists(5, $m)) {
    $ret['extension'] = $m[5];
  }
  if(array_key_exists(3, $m)) {
    $ret['filename'] = $m[3];
  }
  switch($options) {
    case PATHINFO_DIRNAME:
    case 'dirname':
      return $ret['dirname'];
      break;
    case PATHINFO_BASENAME:
    case 'basename':
      return $ret['basename'];
      break;
    case PATHINFO_EXTENSION:
    case 'extension':
      return $ret['extension'];
      break;
    case PATHINFO_FILENAME:
    case 'filename':
      return $ret['filename'];
      break;
    default:
      return $ret;
  }
}

function strftime_ru($format = '%e %Ob %Yг.', $date = false) {
	//setlocale(LC_ALL, 'ru_RU.cp1251');
	if ($date === false) {
		$date = time();
	}
	$months = array('','января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря');
	$format = str_replace("%Ob", $months[date('n', $date)], $format);
	return strftime($format, $date);
}


function transliterate($input){
	//todo: str_ireplace() works faster
	
	$gost = array(
	   "Є"=>"YE","І"=>"I","Ѓ"=>"G","і"=>"i","№"=>"-","є"=>"ye","ѓ"=>"g",
	   "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G","Д"=>"D",
	   "Е"=>"E","Ё"=>"YO","Ж"=>"ZH",
	   "З"=>"Z","�?"=>"I","Й"=>"J","К"=>"K","Л"=>"L",
	   "М"=>"M","Н"=>"N","О"=>"O","П"=>"P","Р"=>"R",
	   "С"=>"S","Т"=>"T","У"=>"U","Ф"=>"F","Х"=>"KX",
	   "Ц"=>"TS","Ч"=>"CH","Ш"=>"SH","Щ"=>"SHCH","Ъ"=>"I",
	   "Ы"=>"Y","Ь"=>"","Э"=>"E","Ю"=>"YU","Я"=>"IA",
	   "а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d",
	   "е"=>"e","ё"=>"yo","ж"=>"zh",
	   "з"=>"z","и"=>"i","й"=>"j","к"=>"k","л"=>"l",
	   "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
	   "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"kx",
	   "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"shch","ъ"=>"i",
	   "ы"=>"y","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ia",
	   " "=>"_","—"=>"_",","=>"_","!"=>"_","@"=>"_",
	   "#"=>"-","$"=>"","%"=>"","^"=>"","&"=>"","*"=>"",
	   "("=>"",")"=>"","+"=>"","="=>"",";"=>"",":"=>"",
	   "'"=>"","\""=>"","~"=>"","`"=>"","?"=>"","/"=>"",
	   "\\"=>"","["=>"","]"=>"","{"=>"","}"=>"","|"=>""
	  );

	return strtr($input, $gost);
}


function get_func_name_from_backtrace($depth = 1){
	$callers=debug_backtrace();
	if(!isset($callers[1])) return "??";
	return $callers[1]['function'];
}

function get_backtrace_string($separator = '\r\n  '){
	$st = debug_backtrace();
	$bt_arr =array();
	$res = '';
	for($i=1; $i<count($st); $i++){
		$frame = $st[$i];
		$str = (string)array_default($frame,'function').'() at '.(string)array_default($frame,'file').':'.(string)array_default($frame,'line');
		if(isset($frame['class'])) $str = (string)$frame['class'].':'.$str;
		$res .= $separator.$str;
	}
	return $res;
}

/**
 * 
 * @param mixed[] $unindexed_arr  array of objects that have field $field_name OR  array of arrays that have key $field_name
 * @param string $field_name delault value is 'id'
 * @param string $key_set_type if not null, do settype() for keys; possibles types: "integer" / "string" / "float" / "boolean" ...
 * @return mixed[] returns new array with references to original records indexed by $field_name
 */
function &make_array_indexed_by_records_field($unindexed_arr, $field_name='id', $key_set_type = null){
	if(!count($unindexed_arr)) {
		$empty_res = array();
		return $empty_res;
	}
	
	$indexed_arr = array();
	reset($unindexed_arr);
	$is_arrays = is_array(current($unindexed_arr));
	
	foreach($unindexed_arr as &$record){
		$key = $is_arrays ? $record[$field_name] : $record->$field_name;
		
		if($key_set_type !== null){ settype($key,$key_set_type);  }
		
		$indexed_arr[$key] = &$record;
	}		

	return $indexed_arr;
}


function get_field_value_recursive_not_ref($root, $default_value, $property_name_1){
	$args = func_get_args();
	$args_count = func_num_args();
	$obj_or_arr = $root;
	for($i = 2; $i<$args_count; $i++){
		$property_name_cur = $args[$i];
		if(is_array($obj_or_arr)){
			if(isset($obj_or_arr[$property_name_cur])){
				$obj_or_arr = $obj_or_arr[$property_name_cur];
				continue;
			}
		}
		else if(is_object($obj_or_arr) && property_exists($obj_or_arr,$property_name_cur)){
			$obj_or_arr = $obj_or_arr[$property_name_cur];
			continue;
		}
		return $default_value;
		
	}
	return $obj_or_arr;
}
