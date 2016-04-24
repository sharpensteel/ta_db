<?php


class BackendBaseController extends Controller{
	public $layout = '//layouts/yii_column2';


//	/**
//	 * @return array action filters
//	 */
//	public function filters()
//	{
//		return array(
//			'accessControl', // perform access control for CRUD operations
//			'postOnly + delete', // we only allow deletion via POST request
//		);
//	}

	public function beforeAction($action)
	{
		if(!Yii::app()->isAdmin())
		{
			$err = 'This user is not admin';
			yii_flash_append('error', $err);
			throw new CHttpException(403,$err);
			//yii_flash_append('error', 'Этот пользователь не имеет прав администратора');

		}


		return parent::beforeAction($action);
	}

	public function get_breadcrumbs_homeLink(){
		return '<a href="'. createUrl('backend').'">Dashboard</a>';
	}

}
