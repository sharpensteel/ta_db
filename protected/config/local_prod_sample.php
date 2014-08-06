<?php

mb_internal_encoding("UTF-8");

if(!defined('YII_ENABLE_ERROR_HANDLER'))
	define('YII_ENABLE_ERROR_HANDLER', false);

putenv("TZ=Europe/Moscow");
ini_set('date.timezone','Europe/Moscow');

$GLOBALS['is_enable_log_request'] = 1;



return array(
	
	/*'modules'=>array(
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'xxxxxx',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('10.15.10.108'),//'127.0.0.1','::1'),
		),
		
	),*/
	
	'components'=>array(
		'db'=>array(
			//'connectionString' => 'mysql:host=localhost;dbname=ddddddd',
			'username' => 'uuuuu',	
			'password' => 'xxxx',
		),
		'cache'=>array(
            'class'=> (defined('DEVEL_MODE') && constant('DEVEL_MODE') ? 'CDummyCache' : 'system.caching.CFileCache'),
        ),
	)
);