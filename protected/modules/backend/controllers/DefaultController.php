<?php

class DefaultController extends BackendBaseController
{
	//public $layout = '//layouts/yii_column2';
	public function actionIndex()
	{
		$this->render('index');
	}
	
	public function actionParse_all($packet_type_id){
		echo Packet_parser::parse_all($packet_type_id);
	}
}