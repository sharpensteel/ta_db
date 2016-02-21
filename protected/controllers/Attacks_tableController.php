<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AttacksTableController
 *
 * @author Moss
 */
class Attacks_tableController extends Controller {
	//put your code here
	public function actionIndex(){
		$model=new Attack('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Attack']))
			$model->attributes=$_GET['Attack'];

		$this->render('attacks_table_index',array(
			'model'=>$model,
		));
	}
}
