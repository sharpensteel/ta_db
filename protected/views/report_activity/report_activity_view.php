<span style="font-size:12px;font-weight:bold;">Players activity in the last <?=$interval_days?> days:</span><br><br>
<div style="margin-left:20px;background:#ededff;padding:20px;display:inline-block">
<? $this->widget('application.widgets_custom.Report_activity_widget',array('interval_days'=>$interval_days, 'alliance_id'=>$alliance_id)); ?>
</div>
