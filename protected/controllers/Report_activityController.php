<?php


class Report_activityController extends Controller{
	public function actionIndex($alliance_id = 101, $interval_days = 30){

		if(isset($_REQUEST['amp;alliance_id'])){
			$alliance_id = $_REQUEST['amp;alliance_id'];
		}
		
		$this->render('report_activity_view',array(
			'alliance_id'=>$alliance_id,
			'interval_days'=>$interval_days,
		));
	}
}
