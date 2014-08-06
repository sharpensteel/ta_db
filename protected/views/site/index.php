<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;

?>
<b>C&C Tiberium alliance database</b>
<span style="float:right">latest attack log: <?=$dt_last_attack?></span>
<br><br><br>


<a href="<?=createUrl("attacks_table")?>">combat log</a><br><br>

<br>
<br>
<span style="font-size:12px;font-weight:bold;">Players activity by last 30 days:</span><br><br>
<div style="margin-left:20px;background:#ededff;padding:20px;display:inline-block">
<? $this->widget('application.widgets_custom.Report_activity_widget',array('interval_days'=>30)); ?>
</div>
