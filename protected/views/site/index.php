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
<script src="<?=baseUrl()?>libs/jquery-1.11.1.min.js"></script>
		
<style>
	body{
		margin:0;
	}
	
	.section{
		margin-left:20px; margin-top:20px; margin-bottom: 20px;
		background:#F0F0F0;padding:20px;
	}
	.section_title{
		font-size: 1.2em; font-weight: bold; margin-bottom: 20px;
	}
	
</style>

<div style="  background: #C5C5C5;padding: 10px">
<b>World 91 FF run</b>
<span style="float:right">latest update: <?= date("r",query_scalar('select unix_timestamp(dt_last_world_update) from global_data where id=1')) ?></span>
</div>

<br>
<br>
&nbsp;Sections:
<div style="line-height:20px;margin-left:20px;margin-top:5px;margin-bottom:25px;">
	<a href="#section_player_update">Update your OL</a><br>
	<a href="#section_complete_spreadsheet">Complete spreadsheet</a><br>
	<a href="#section_hubs_map">Hubs map</a><br>
</div>


<div id="section_player_update" class="section" style="display:inline-block;">
	<div class="section_title">Update your OL:</div>
	<? $this->widget('application.widgets_custom.Player_update_widget',array() ); ?>
</div>

<div id="section_complete_spreadsheet" class="section" style="">
	<div class="section_title">Complete spreadsheet</div>
	<? $this->widget('application.widgets_custom.Report_ff_list_widget',array() ); ?>
</div>

<div id="section_hubs_map" class="section" style="">
	<div class="section_title">Hubs map</div>
	<? $this->widget('application.widgets_custom.Hubs_map_widget',array() ); ?>
</div>

<div style="margin-top:40px;margin-bottom:20px;text-align: right;margin-right:30px;">
	Created by <a href="mailto:sharpensteel@gmail.com">sharpensteel1</a>. For your feedback or if you need sources, please message/e-mail me.
</div>