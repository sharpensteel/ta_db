<?php

include_once __DIR__.'/protected/config/main.php';
include_once __DIR__.'/protected/libs/utils.php';
include_once __DIR__.'/protected/libs/utils_yii.php';


		
if(!empty($GLOBALS['is_enable_log_request'])){
	enable_log_request();
}


// change the following paths if necessary
$yii=YII_FRAMEWORK_DIR.'/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

if(defined('DEVEL_MODE') && constant('DEVEL_MODE')){
	// remove the following lines when in production mode
	defined('YII_DEBUG') or define('YII_DEBUG',true);
	// specify how many levels of call stack should be shown in each log message
	defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);
}



require_once($yii);

/**
 * @param int $errno
 * @param string $errstr
 * @param string $errfile
 * @param int $errline
 * @param array $errcontext
 */
function my_error_handler ( $errno , $errstr, $errfile, $errline, $errcontext){
	if(!error_reporting()) return; // hot handle error at @-prefixed functions
	my_log("ERROR: {$errstr} at {$errfile}:{$errline}");
	if( defined('DEVEL_MODE') && constant('DEVEL_MODE') ){
		// todo: display error
	}
	return false;
}
$GLOBALS['error_handler_prev'] = set_error_handler('my_error_handler');



Yii::createWebApplication($config)->run();
