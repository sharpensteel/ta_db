<?php
/* @var $this Packet_parserController */
/* @var $packet_type_arr Packet_type[]  */
/* @var $form CActiveForm */
?>


Send JSON packets for parsing and storing<br><br>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'category-form',
	'htmlOptions' => array(
        'enctype' => 'multipart/form-data',
    ),	
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,	
)); ?>


	
	<? Form_utils::simple_row_dropDownList_render($form, $model, 'packet_type_id', Form_utils::generate_list_data_from_table_model(PacketType::model())); ?>
	<br>
	<? Form_utils::simple_row_textArea_render($form, $model, 'packets_json', array('control'=>array('style'=>'width:90%;min-height:100px;'))); ?>
	<br>
	
	<input type="submit">
<?php $this->endWidget(); ?>
	<br>

</div><!-- form -->