<?php
/* @var $this ArticleController */
/* @var $model Article */

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#article-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Attacks</h1>

<? /*
<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->
*/ ?>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'article-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'dt',
		array('name'=>'outcome_text','header' =>'outcome','value'=>'(strlen($data->outcome_text)?$data->outcome_text:$data->outcome_id)', 'htmlOptions'=>array('width'=>'180px')),
		//'att_user_id',
		'att_login',
		'def_is_forgotten',
		'def_base_level',
		//'att_base_id',
		//'att_base_name',
		'def_alliance_name',
		'def_login',
		array('name'=>'att_base_name','value'=>'$data->att_base_name." (id:".$data->att_base_id.")"', 'htmlOptions'=>array('width'=>'200px')),
		//'def_base_id',
		//'def_base_name',
		array('name'=>'def_base_name','value'=>'$data->def_base_name." (id:".$data->def_base_id.")"', 'htmlOptions'=>array('width'=>'200px')),
		//'coords',
		'id',
		'attack_log_is_parsed',
		'report_is_parsed',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
