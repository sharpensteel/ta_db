<?php


class BackendBaseController extends Controller{
	public $layout = '//layouts/yii_column2';
	
	protected function beforeAction($action) {
		return parent::beforeAction($action);
		
	}
}
