<?php


class Player_updateController {
	
	public function actionUpdate(){
		$model = new Packet_parser_form();
		if(isset($_POST['Packet_parser_form']))
		{
			$model->attributes=$_POST['Packet_parser_form'];
			
			if($model->validate()){
				$res = Packet_parser::upload($model);
				
				if($res !== false){
					yii_flash_append('info', $res);
					$model->packets_json = '';
				}
			}
		}
			
		
	}
}
