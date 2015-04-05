<?php

class Form_utils{
	
	public static function generate_list_data_from_table_model($table_model, $title_attibute = 'name'){
		$record_arr = $table_model->findAll();	
		//array_splice($record_arr, 0, 0, array('id'=>0, 'name'=>'' ));	
		$list = CHtml::listData($record_arr,'id',$title_attibute);
		if(!isset($list[0])) $list[0] = '';
		return $list;
	}
	
	
	/**
	* @param CActiveForm $form
	* @param CModel $model the data model
	* @param string $attribute the attribute
	* @param array $htmlOptions additional HTML attributes.
	*/
	public static function simple_row_textField_render($form, $model, $attribute, $htmlOptions_arr = array('label' => array(), 'control' => array(), 'error' => array() ) ){
		
		?><div class="form_row">
			<?php echo $form->labelEx($model, $attribute, array_default_not_ref($htmlOptions_arr, 'label', array()) ); ?>
			<?php echo $form->textField($model,$attribute, array_default_not_ref($htmlOptions_arr, 'control', array()) ); ?>
			<?php echo $form->error($model, $attribute, array_default_not_ref($htmlOptions_arr, 'error', array()) ); ?>
		</div><?
	}
	
	/**
	* @param CActiveForm $form
	* @param CModel $model the data model
	* @param string $attribute the attribute
	* @param array $htmlOptions additional HTML attributes.
	*/
	public static function simple_row_fileField_render($form, $model, $attribute, $htmlOptions_arr = array('label' => array(), 'control' => array(), 'error' => array() ) ){
		
		?><div class="form_row">
			<?php echo $form->labelEx($model, $attribute, array_default_not_ref($htmlOptions_arr, 'label', array()) ); ?>
			<?php echo $form->fileField($model,$attribute, array_default_not_ref($htmlOptions_arr, 'control', array()) ); ?>
			<?php echo $form->error($model, $attribute, array_default_not_ref($htmlOptions_arr, 'error', array()) ); ?>
		</div><?
	}

	
/**
	* @param CActiveForm $form
	* @param CModel $model the data model
	* @param string $attribute the attribute
	* @param array $htmlOptions additional HTML attributes. html value 'checked' automaticly set if value of attribute can be casted to nonzero or equal to 'true' string
	*/
	public static function simple_row_checkBox_render($form, $model, $attribute, $htmlOptions_arr = array('label' => array('style' =>'display:inline-block'), 'control' => array(), 'error' => array() ) ){
		
		$is_checked = ((double)$model->$attribute || strtolower($model->$attribute)==='true' );
		$htmlOptions_control_arr = array_default_not_ref($htmlOptions_arr, 'control', array());
		if($is_checked){
			$htmlOptions_control_arr = array_replace_recursive($htmlOptions_control_arr, array('checked'=>'checked'));
		}
		
		?><div class="form_row">
			<?php echo $form->checkBox($model,$attribute, $htmlOptions_control_arr ); ?>
			<?php echo $form->labelEx($model, $attribute, array_default_not_ref($htmlOptions_arr, 'label', array()) ); ?>
			<?php echo $form->error($model, $attribute, array_default_not_ref($htmlOptions_arr, 'error', array()) ); ?>
		</div><?
	}
	
	/**
	* @param CActiveForm $form
	* @param CModel $model the data model
	* @param string $attribute the attribute
	* @param array $htmlOptions additional HTML attributes.
	*/
	public static function simple_row_textArea_render($form, $model, $attribute, $htmlOptions_arr = array('label' => array(), 'control' => array(), 'error' => array() ) ){
		
		?><div class="form_row">
			<?php echo $form->labelEx($model, $attribute, array_default_not_ref($htmlOptions_arr, 'label', array()) ); ?>
			<?php echo $form->textArea($model,$attribute, array_default_not_ref($htmlOptions_arr, 'control', array()) ); ?>
			<?php echo $form->error($model, $attribute, array_default_not_ref($htmlOptions_arr, 'error', array()) ); ?>
		</div><?
	}
	
	/**
	* @param CActiveForm $form
	* @param CModel $model the data model
	* @param string $attribute the attribute
	* @param array $data data for generating the list options (value=>display)
	* @param array $htmlOptions additional HTML attributes.
	*/
	public static function simple_row_dropDownList_render($form, $model,$attribute,$data, $htmlOptions_arr=array('label' => array(), 'control' => array(), 'error' => array() )   )
	{
		?><div class="form_row">
			<?php echo $form->labelEx($model, $attribute, array_default_not_ref($htmlOptions_arr, 'label', array()) ); ?>
			<?php echo $form->dropDownList($model,$attribute, $data, array_default_not_ref($htmlOptions_arr, 'control', array()) ); ?>
			<?php echo $form->error($model, $attribute, array_default_not_ref($htmlOptions_arr, 'error', array()) ); ?>
		</div><?
		
	}
	
	
	/**
	* @param CModel $model the data model
	* @param string $attribute the attribute
	* @param array[] $htmlOptions additional HTML attributes.
	*/
	public static function simple_row_dateField_render($form, $model,$attribute,$htmlOptions_arr=array('label' => array(), 'control' => array(), 'error' => array() )   ){
		
		?><div class="form_row">
			<?php echo $form->labelEx($model, $attribute, array_default_not_ref($htmlOptions_arr, 'label', array()) ); ?>
			<?php echo $form->dateField($model,$attribute,  array_default_not_ref($htmlOptions_arr, 'control', array()) ); ?>
			<?php echo $form->error($model, $attribute, array_default_not_ref($htmlOptions_arr, 'error', array()) ); ?>
		</div><?
		
	}
	

	
	/**
	* @param CActiveForm $form
	* @param CModel $model the data model, inheriting from ActiveRecord_with_timestamp
	 * @param string $attribute  name of the attribute (string attribute, not the `__tt` attribute)
	* @param array $datapicker_options params for jQuery Datepicker; warning: dateFormat in php date format instead of jQuery!!!
	* @param array[] $htmlOptions additional HTML attributes.
	*/
	public static function simple_row_datePicker_timestamp_render($form, $model, $attribute,
			$datapicker_options = array('dateFormatPhp' => 'd.m.Y', 'changeMonth' => 1, 'changeYear' => 1,'yearRangeType' => "c-50:c+50"),
			$htmlOptions_arr = array('label' => array(), 'control' => array("autocomplete" => "off"), 'error' => array() ) )
	{
		$htmlOptions_arr = array_merge( array('label' => array(), 'control' => array(), 'error' => array() ), $htmlOptions_arr );
		
		
		$tt = (int)$model->{$attribute.'__tt'};
		
		
		if(!is_array($datapicker_options)) { $datapicker_options = array(); }
		
		
		$datapicker_options['dateFormatPhp'] = array_default_not_ref($datapicker_options, 'dateFormatPhp', 'd.m.Y');
		
		
		$tt_str = date($datapicker_options['dateFormatPhp'],$tt); // ensure russian timezone lol
		
		
		// correct PHP date() style dateFormat to the equivalent jQuery UI datepicker string
		$datepicker_format = dateStringToDatepickerFormat($datapicker_options['dateFormatPhp']);		
		$datapicker_options['dateFormat'] = $datepicker_format;
		
		
		$control_htmlOptions = array_default_not_ref($htmlOptions_arr, 'control', array());
		CHtml::resolveNameID($model, $attribute, $control_htmlOptions);
		$name_datepicker = $control_htmlOptions['name'];
		$id_datapicker = $control_htmlOptions['id'];
		
		
		$tt_htmlOptions = array();
		$tt_attribute = $attribute.'__tt';
		CHtml::resolveNameID($model, $tt_attribute, $tt_htmlOptions);
		$name_tt = $tt_htmlOptions['name'];
		$id_tt = $tt_htmlOptions['id'];
		
		
		
		?><div class="form_row">
			<?php echo $form->labelEx($model, $attribute, array_default_not_ref($htmlOptions_arr, 'label', array()) ); ?>
			<? $datePicker = $form->widget('zii.widgets.jui.CJuiDatePicker',array(
					//'id'=>$id_input_datepicker,
					'name'=>$name_datepicker,
					'value'=>$tt_str,
					// additional javascript options for the date picker plugin
					'options'=> $datapicker_options,
					'htmlOptions'=> $control_htmlOptions,
			)); ?>
			<input type="hidden" name="<?=$name_tt?>" id="<?=$id_tt?>" value="<?=$tt?>">
			<script>
				$(function(){
					var $dp = $('#<?=$id_datapicker?>');
					$dp.on('change paste keyup', function(){
						var dt = $('#<?=$id_datapicker?>').datepicker( "getDate" );
						var tt = parseInt(dt.getTime()/1000);
						tt -= 0*(new Date()).getTimezoneOffset(); // convert timezone to gmt
						tt += 12*60*60; // middle day GMT
						//console.log(dt,tt);
						$('#<?=$id_tt?>').val(tt);
					});
				});
			</script>
			<? /*<input type="hidden" name="<?=$name_input_hidden?>" id="<?=$id_input_hidden?>" value="<?=$tt?>">			
			<script>
				$(function(){
					
					var date_format = "<?=$datepicker_format?>";//need to check .data() for datapicker created before use this method: $("#<?=$id_input_datepicker?>").datepicker('option','dateFormat');
					
					var $input_datepicker = $("#<?=$id_input_datepicker?>");
					var $input_hidden = $("#<?=$id_input_hidden?>");
					$input_datepicker.change(function(){
						var dt_str = $input_datepicker.val();
						var dt = $.datepicker.parseDate(date_format, dt_str);
						var time_t = parseInt( (dt - new Date('70-1-1')) /1000);
						$input_hidden.val(time_t);
					});
				});
			</script>
			 */ ?>
			<?php echo $form->error($model, $attribute, array_default_not_ref($htmlOptions_arr, 'error', array()) ); ?>
		</div><?
	}
	

	/**
	* @param CActiveForm $form
	* @param CModel $model the data model
	* @param string $attribute the attribute
	* @param array $datapicker_options params for jQuery Datepicker; warning: dateFormat in php date format instead of jQuery!!!
	* @param array[] $htmlOptions additional HTML attributes.
	*/
	public static function simple_row_datePicker_render($form, $model, $attribute,
			$datapicker_options = array('dateFormatPhp' => 'd.m.y', 'changeMonth' => 1, 'changeYear' => 1,'yearRangeType' => "c-50:c+50"),
			$htmlOptions_arr = array('label' => array(), 'control' => array("autocomplete" => "off"), 'error' => array() ) )
	{
		$htmlOptions_arr = array_merge( array('label' => array(), 'control' => array(), 'error' => array() ), $htmlOptions_arr );
		
		$tt = (int)$model->$attribute;
		
		if(!is_array($datapicker_options)) { $datapicker_options = array(); }
		
		$datapicker_options['dateFormatPhp'] = array_default_not_ref($datapicker_options, 'dateFormatPhp', 'd.m.Y');
		
		$dt_str = $model->{$attribute};
		$dt_format = (strpos($dt_str,'-') === false) ? 'd.m.Y' : 'Y-m-d';
		$dt = date_parse_from_format( $dt_format, $dt_str);
		/*vd($attribute);
		vd($dt_format);
		vd($model->{$attribute});
		vd($dt);*/
		$value = $dt['day'].".".$dt['month'].".".$dt['year'];
		
		//$value = date($datapicker_options['dateFormatPhp']);
		/*$old_timezone = date_default_timezone_get();
		date_default_timezone_set('UTC');
		$value = date($datapicker_options['dateFormatPhp'],$tt);
		date_default_timezone_set($old_timezone);*/
		
		
		// correct PHP date() style dateFormat to the equivalent jQuery UI datepicker string
		$datepicker_format = dateStringToDatepickerFormat($datapicker_options['dateFormatPhp']);		
		$datapicker_options['dateFormat'] = $datepicker_format;
		
		//$id_input_datepicker = get_class($model).'_'.$attribute.'_datepicker';
		$name_datepicker = get_class($model).'['.$attribute.']';//get_class($model).'['.$attribute.'_datepicker]';
		//$id_input_hidden = get_class($model).'_'.$attribute;
		//$name_input_hidden = get_class($model).'['.$attribute.']';
		
		?><div class="form_row">
			<?php echo $form->labelEx($model, $attribute, array_default_not_ref($htmlOptions_arr, 'label', array()) ); ?>
			<? $form->widget('zii.widgets.jui.CJuiDatePicker',array(
					//'id'=>$id_input_datepicker,
					'name'=>$name_datepicker,
					'value'=>$value,
					// additional javascript options for the date picker plugin
					'options'=> $datapicker_options,
					'htmlOptions'=> array_default_not_ref($htmlOptions_arr, 'control', array()),
			)); ?>
			<? /*<input type="hidden" name="<?=$name_input_hidden?>" id="<?=$id_input_hidden?>" value="<?=$tt?>">			
			<script>
				$(function(){
					
					var date_format = "<?=$datepicker_format?>";//need to check .data() for datapicker created before use this method: $("#<?=$id_input_datepicker?>").datepicker('option','dateFormat');
					
					var $input_datepicker = $("#<?=$id_input_datepicker?>");
					var $input_hidden = $("#<?=$id_input_hidden?>");
					$input_datepicker.change(function(){
						var dt_str = $input_datepicker.val();
						var dt = $.datepicker.parseDate(date_format, dt_str);
						var time_t = parseInt( (dt - new Date('70-1-1')) /1000);
						$input_hidden.val(time_t);
					});
				});
			</script>
			 */ ?>
			<?php echo $form->error($model, $attribute, array_default_not_ref($htmlOptions_arr, 'error', array()) ); ?>
		</div><?
	}
	
	
	/**
	* @param CModel $model the data model
	* @param string $attribute the attribute
	* @param array $htmlOptions additional HTML attributes.
	*/
	public static function simple_row_staticText_render($model, $attribute, $htmlOptions_arr = array('label' => array(), 'control' => array()), $attribute_value = '{CALCULATE}' ){
		
		$label_html_options = array_default_not_ref($htmlOptions_arr, 'label', array() );
		
		$control_html_options = array_merge( array("class" => "staticText"), array_default_not_ref($htmlOptions_arr, 'control', array() ) );
		
		$label_text = $model->getAttributeLabel($attribute);		
		
		if($attribute_value === '{CALCULATE}')
			$attribute_value = $model->{$attribute};		
		
		?><div class="row">
			<label <? foreach($label_html_options as $key=>$val) echo " $key=\"$val\""; ?> ><?=$label_text?></label>
			<b <? foreach($control_html_options as $key=>$val) echo " $key=\"$val\""; ?> ><?=$attribute_value?></b>
		</div><?
	}
	
	
	
	/**
	* @param CActiveForm $form
	* @param CModel $model the data model
	* @param string $attribute the attribute
	* @param array $editor_options params for tinymce.init({...});
	* @param array[] $htmlOptions additional HTML attributes.
	*/
	public static function simple_row_rich_editor_render($form, $model, $attribute,
			$editor_options = array(),
			$htmlOptions_arr = array('label' => array(), 'control' => array("autocomplete" => "off"), 'error' => array() ) )
	{
		$editor_options = array_merge(
			array(
				'selector' => '#'.get_class($model)."_".$attribute,
				'language' => "ru",
				'plugins' => array(
					"advlist autolink lists link image charmap print preview anchor",
					"searchreplace visualblocks code fullscreen",
					"insertdatetime media table contextmenu paste"
				),
				'toolbar' => "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
			),
			$editor_options
		);
				
		?>
		<div class="row">
			<?php echo $form->labelEx($model, $attribute, array_default_not_ref($htmlOptions_arr, 'label', array()) ); ?>
			<?php echo $form->textArea($model,$attribute,array('rows'=>6, 'cols'=>50)); ?>
			<script src="<?=baseUrl()?>libs/js/tinymce/tinymce.min.js"></script>
			<script type="text/javascript">		
				tinymce.init(<?=json_encode($editor_options,defined('JSON_PRETTY_PRINT')?JSON_PRETTY_PRINT:0 )?>);
			</script>
			<?php echo $form->error($model, $attribute, array_default_not_ref($htmlOptions_arr, 'error', array()) ); ?>
		</div>
		<?
	}
	
	
	
}