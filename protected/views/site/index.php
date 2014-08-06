<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;

?>
<b>C&C Tiberium alliance database</b>
<span style="float:right">latest attack log: <?=$dt_last_attack?></span>
<br><br>


<a href="<?=createUrl("attacks_table")?>">attacks table</a><br><br>

<br>
<span style="text-decoration:underline">Players activity by last 30 days:</span><br><br>
<? $this->widget('application.widgets_custom.Report_activity_widget',array('interval_days'=>30));
