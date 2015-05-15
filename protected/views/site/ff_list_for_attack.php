<?php
$this->pageTitle=Yii::app()->name;

?>

		
<style>
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


<div id="section_complete_spreadsheet" class="section" style="">
	<div class="section_title">Shortened spreadsheet for FF attack</div>
	<? $this->widget('application.widgets_custom.Report_ff_list_widget',array('is_ff_attack_form'=>1) ); ?>
</div>

<div id="section_hubs_map" class="section" style="">
	<div class="section_title">Hubs map</div>
	<? $this->widget('application.widgets_custom.Hubs_map_widget',array() ); ?>
</div>

<div style="margin-top:40px;margin-bottom:20px;text-align: right;margin-right:30px;">
	Created by <a href="mailto:sharpensteel@gmail.com">sharpensteel1</a>. For your feedback or if you need sources, please message/e-mail me.
</div>