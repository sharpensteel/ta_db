<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
/*
?>
<b>C&C Tiberium alliance database</b>
<span style="float:right">latest attack log: <?=$dt_last_attack?></span>
<br><br><br>


<a href="<?=createUrl("attacks_table")?>">combat log</a><br><br>

<a href="<?=createUrl("report_activity/index",array('alliance_id'=>256,'interval_days'=>30))?>">Report of activity in Siege</a><br><br>


<br>
<br>
<span style="font-size:12px;font-weight:bold;">Players activity in the last 30 days:</span><br><br>
<div style="margin-left:20px;background:#ededff;padding:20px;display:inline-block">
<? $this->widget('application.widgets_custom.Report_activity_widget',array('interval_days'=>30, 'alliance_id'=>101)); ?>
</div>
<? */

?>
<b>World 91 Forgotten Fortress attackers list</b>
<span style="float:right">latest update: <?= date("r",query_scalar('select unix_timestamp(dt_last_world_update) from global_data where id=1')) ?></span>
<br><br><br>
<div style="margin-left:20px;background:#F0F0F0;padding:20px;display:inline-block">
	<span style="font-size: 1.2em;">Update your data:</span><br>
	<? $this->widget('application.widgets_custom.Update_player_widget',array() ); ?>
</div>
<br><br><br>
<div style="margin-left:20px;background:#F0F0F0;padding:20px;display:inline-block">
	<? $this->widget('application.widgets_custom.Report_ff_list_widget',array() ); ?>
</div>