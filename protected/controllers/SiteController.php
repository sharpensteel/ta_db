<?php

class SiteController extends Controller
{
	public $layout='//layouts/main_layout';
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		$params = array();
		$params['dt_last_attack'] = query_scalar('SELECT dt FROM attack ORDER BY id DESC LIMIT 1');
		$this->render('index', $params);
		
	}
	

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$this->layout='//layouts/yii_column2';
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
	
	public function actionPlayer_update(){
		$model = new Player_form();
		try{
			if(isset($_REQUEST['Player_form']))
			{

				if(isset($_REQUEST['Player_form']['id'])){
					setcookie('up_id', $_REQUEST['Player_form']['id'], time()+60*60*24*1000, "/");
				}


				//$model->attributes=$_REQUEST['Player_form'];
				$model->id = $_REQUEST['Player_form']['id'];
				$model->offense_level = $_REQUEST['Player_form']['offense_level'];
				$model->substitution = $_REQUEST['Player_form']['substitution'];

				if(!(int)$model->id){
					throw new Exception('Select your name');
				}
				
				$model->offense_level = (double)str_replace(',', '.', $model->offense_level);
				if(!$model->offense_level){
					throw new Exception('Enter correct offense level');
				}
				
				$model->update();
				
				yii_flash_append('info', 'Information saved');

				/*if($model->validate())
					{

					$res = $model->update();

					if($res !== false){
						yii_flash_append('info', $res);
					}
				}*/

			}		
		}
		catch(Exception $e){
			yii_flash_append('error', $e->getMessage());
		}
		header("Location: ".baseUrl());
	}
	
	// https://ta_local/ta_db/site/Admin_secret_door?secret=asdeksjljk3s1sd4wsda
	// http://146.185.186.182/ta_db/site/Admin_secret_door?secret=asdeksjljk3s1sd4wsda
	public function actionAdmin_secret_door($secret){
		if($secret != 'asdeksjljk3s1sd4wsda'){
			yii_flash_append('error', 'incorrect secret!');
		}
		else{
			session_start_if_not();
			$_SESSION['is_admin'] = 1;
		}
		header("Location: ".baseUrl());
	}
	
	
	public function actionPlayer_update_team($player_id, $team){
		session_start_if_not();
		if(!array_default($_SESSION, 'is_admin',0)){
			echo "only admins can do that."; return;
		}
		query_execute('insert into player_update_history (player_id, team, ip) values (:player_id, :team, :ip)', array('player_id'=>$player_id, 'team'=>$team, 'ip'=>array_default($_SERVER,'REMOTE_ADDR')));
		query_execute('update player set team=:team where id=:player_id', array('player_id'=>$player_id, 'team'=>$team ));
		echo $team;
	}
	
}