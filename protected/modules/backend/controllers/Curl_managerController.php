<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Curl_manager
 *
 * @author Moss
 */
class Curl_managerController extends BackendBaseController{
	//put your code here
	public function actionIndex(){
		//if($_POST)
		$this->render('curl_manager_index');
	}
}
