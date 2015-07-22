<?php

class Packet_parserController extends Controller{
	public $layout='//layouts/main_layout';


	public function actionUpload(){
		
		
		$model = new Packet_parser_form();
		if(isset($_POST['Packet_parser_form']))
		{
			$model->attributes=$_POST['Packet_parser_form'];
			
			if($model->validate()){
				$res = Packet_parser::upload_form($model);
				
				if($res !== false){
					yii_flash_append('info', $res);
					$model->packets_json = '';
				}
			}
		}
			
		$this->render('packet_parser_upload_form', array('model' => $model) );
	}
	
	
}