<?php

if(!defined('YII_FRAMEWORK_DIR'))
	define('YII_FRAMEWORK_DIR', 'c:/xampp/yii/framework');


mb_internal_encoding("UTF-8");

/*if(!defined('YII_ENABLE_ERROR_HANDLER'))
	define('YII_ENABLE_ERROR_HANDLER', false);

if(!defined('YII_ENABLE_EXCEPTION_HANDLER'))
	define('YII_ENABLE_EXCEPTION_HANDLER', false);
*/

putenv("TZ=Europe/Moscow");
ini_set('date.timezone','Europe/Moscow');

if(!defined('DEVEL_MODE'))
	define('DEVEL_MODE', true);

$GLOBALS['is_enable_log_request'] = 1;

$GLOBALS['CURL_DONT_VERIFY_SSL'] = 1;


// for windows test only!!! comment this on linux:
$GLOBALS['SSL_CERTIFICATE_PATH'] = __DIR__.'/cacert.pem';

return array(

	'modules'=>array(
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'123123',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),

	),

	'components'=>array(
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=DB_NAME_HERE',
			'username' => 'USERNAME_HERE',
			'password' => 'PASSWORD_HERE',

		),
		'cache'=>array(
			'class'=> (defined('DEVEL_MODE') && constant('DEVEL_MODE') ? 'CDummyCache' : 'system.caching.CFileCache'),
		),
	),

	'params'=>array(
		'admin_secret'=>'SECRET_PASSWORD_HERE',
	),

);