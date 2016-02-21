<?php


/**
 * 
 * @property integer $items_count
 * @property Pager_render render 
 * 
 */
class Pager extends Class_with_properties{
	
	/** @var $string[] indexed by strings */
	private $_setting_value_arr = array();
	
	
	/** @var string[]  must be set before use!!  array format: [setting_name] => [setting_default_value]  */
	private $_setting_arr = array(
		// TODO: make standard settings names changeable
		'page' => '0', // page index
		'item_index' => '0',
		'page_size' => '10',
	);
	
	/** @var object[]  result of $cache_callback_calc_items_uid_arr(); previously call cache_init() need! */
	private $_items_uid_arr = null;
	
	/** @var integer */
	public $max_setting_value_searialize_size = 100; // limit of size serialization of value, on single setting
	
	/** @var integer */
	private $max_page_size = 30;
	
	/** @var bool */
	private $cache_is_inited = false;
	
	/** @var string  use empty value if not separate per users */
	private $cache_user_uid = "";
	
	/** @var string  will be used as cache */
	private $cache_action_name;
	
	/** string[] */
	private $cache_not_invalidating_setting_names;
	
	/** @var callable */
	private $cache_callback_calc_items_uid_arr;
	
	/** @var Pager_render */
	private $renderer = null;
	
	

	/**
	 * @param integer $page_size_default
	 * @param integer $max_page_size 
	 * @param string[] $setting_default_value_arr  array format: [setting_name] => [setting_default_value]
	 */
	public function __construct($page_size_default, $max_page_size=30, $setting_arr = array()) {
		$this->_setting_arr['page_size'] = (string)$page_size_default;
		$this->max_page_size = (string)$max_page_size;
		
		foreach($setting_arr as $setting_name => $setting_val){
			$this->_setting_arr[(string)$setting_name] = (string)$setting_val;
		}
		
	}

	
	/**
	 * @param string $setting_name
	 * @param string $setting_val
	 */
	public function set_setting_value($setting_name, $setting_val){
		$setting_name = (string)$setting_name;
		$setting_val = (string)$setting_val;
		
		if(!array_key_exists($setting_name,$this->_setting_arr)){
			return;
		}		
		
		if($setting_name === 'page_size'){
			$page_size = (int)$setting_val;
			if($page_size <= 0 || $page_size > $this->max_page_size) return;
		}
			

		$this->_setting_value_arr[$setting_name] = $setting_val;
	}	
	
	/*
	 * @return string
	 */
	public function get_setting_value($setting_name){
		$setting_name = (string)$setting_name;
		if(array_key_exists($setting_name,$this->_setting_value_arr)){			
			return $this->_setting_value_arr[$setting_name];
		}
		if(array_key_exists($setting_name,$this->_setting_arr)){
			return $this->_setting_arr[$setting_name];
		}
		return NULL;
	}
		
	/*
	 * @return string[string]
	 */
	public function get_setting_value_arr(){
		return array_replace($this->_setting_arr, $this->_setting_value_arr);
	}
	
	
	

	public function parse_request_parameters($param_arr){
		foreach($this->_setting_arr as $setting_name => $setting_default_val){
			
			if(!array_key_exists($setting_name,$param_arr)) continue;
			$setting_val = (string)$param_arr[$setting_name];
			if($setting_default_val === $setting_val)continue;
			
			$this->_setting_value_arr[$setting_name] = $setting_val;
		}
	}
	

	////////////////////////////////////////////////////////////////////////////////////////////////////
	// cache functions
	////////////////////////////////////////////////////////////////////////////////////////////////////
	
	/*
 	 * @param string $cache_action_name  example: "product_list"
 	 * @param callable $callback_calc_items_uid_arr  function parameter: Pager object
	 * @param string $cache_user_uid  use empty value if not separate per users
	 * @param string $cache_not_invalidating_setting_names  by default, change current page or current item will not invalidate cache data
	 */
	public function cache_init($cache_action_name, $cache_callback_calc_items_uid_arr, $cache_user_uid="", $cache_not_invalidating_setting_names = array("page", "item_index","page_size"))
	{
		$this->cache_action_name = $cache_action_name;
		$this->cache_user_uid = $cache_user_uid;	
		$this->cache_callback_calc_items_uid_arr = $cache_callback_calc_items_uid_arr;
		$this->cache_not_invalidating_setting_names = $cache_not_invalidating_setting_names;
		$this->cache_is_inited = true;
	}
	
	private function cache_id_generate(){
		if(!$this->cache_is_inited) { throw new Exception(__METHOD__.": cache was not inited!"); return; }
		
		$settings_hash = md5(json_encode($this->_setting_value_arr));
		
		return $this->cache_action_name."_".$this->cache_user_uid."_".$settings_hash;
	}	
	
	public function cache_delete(){
		if(!$this->cache_is_inited) { throw new Exception(__METHOD__.": cache was not inited!"); return; }
		$cache_id = $this->cache_id_generate();
		Yii::app()->cache->delete($cache_id);
		$this->_items_uid_arr = null;
	}
	
	
	public function get_items_uid_arr(){
		if(!$this->cache_is_inited) { throw new Exception(__METHOD__.": cache was not inited!"); return; }
		if($this->_items_uid_arr !== null){
			return $this->_items_uid_arr;
		}
		$cache_id = $this->cache_id_generate();
		
		$this->_items_uid_arr = Yii::app()->cache->get($cache_id);
		
		if(!is_array($this->_items_uid_arr)){
			$items_uid_arr_with_keys = call_user_func($this->cache_callback_calc_items_uid_arr, $this);
			$this->_items_uid_arr = array_values($items_uid_arr_with_keys);
			if(!is_array($this->_items_uid_arr)) throw new Exception(__CLASS__.": cache_callback_calc_items_uid_arr() returns not array!");
			Yii::app()->cache->set($cache_id, $this->_items_uid_arr, (int)array_default($GLOBALS, 'CACHE_SQL_LIFETIME_BY_DEFAULT', 30));
		}
		
		return $this->_items_uid_arr;
	}
	
	
	public function get_items_uid_arr_for_page(){
		$uids = $this->get_items_uid_arr();
		
		$page_size = $this->get_setting_value('page_size');
		$page = $this->get_setting_value('page');
		
		return array_slice($uids, $page_size*$page, $page_size);
	}
	

	public function shift_page_to_current_item(){
		$this->get_items_uid_arr();
		
		$item_index = $this->get_setting_value('item_index');
		$page_size = $this->get_setting_value('page_size');
		
		$page =  floor($item_index / $page_size);
		$this->set_setting_value('page',$page);
	}
	
	public function get_page_count(){
		$this->get_items_uid_arr();
		
		$item_count = count($this->_items_uid_arr);
		$page_size = max(1,$this->get_setting_value('page_size'));
		
		return ceil( $item_count / $page_size);
	}
	


	/**
	 * @return string example: "page=4&page_size=10&like=some%20string"
	 */
	public function util__generate_url_parameters($setting_value_overloaded_arr = null){
		$setting_arr = array();
		
		if($setting_value_overloaded_arr){
			$invalid_arr = array_diff_key($setting_value_overloaded_arr, $this->_setting_arr);
			if(is_array($invalid_arr)){
				foreach($invalid_arr as $invalid_key => $invalid_val){
					error_log(__METHOD__.": invalid setting name '{$invalid_key}'!");
				}
			}
		}
		
		
		foreach($this->_setting_arr as $setting_name => $setting_default_val){
			$setting_val = $setting_default_val;
			if( is_array($setting_value_overloaded_arr) && array_key_exists($setting_name, $setting_value_overloaded_arr) ){
				$setting_val = (string)$setting_value_overloaded_arr[$setting_name];
			}
			else if(array_key_exists($setting_name,$this->_setting_value_arr)){
				$setting_val = $this->_setting_value_arr[$setting_name];
			}
			if($setting_val !== $setting_default_val)
				$setting_arr[$setting_name] = $setting_val;
		}
		$res = http_build_query($setting_arr);
		return $res;
	}
	

	/**
	 * 
	 * @param string $controller_action
	 * @param string[] $setting_value_overloaded_arr example: array([setting name] => [setting value], ... )
	 * @param string[] $get_param_arr 
	 * @return string example: "/catalogue/list/?page=4&lolol=r5drs"
	 */
	public function util__create_url($controller_action, $setting_value_overloaded_arr = array(), $get_param_arr = array()){
		$url_begin = createUrl($controller_action, $get_param_arr);
		$url_params = $this->util__generate_url_parameters($setting_value_overloaded_arr);
		return url_append_parameters( $url_begin, $url_params );
	}
	
	/**
	 * 
	 * @param string $url_begin if set, result urls will contain this as beginning
	 * @return string[]
	 */
	public function util__generate_pages_url_arr($url_begin = null){
		$this->get_items_uid_arr();
		
		$page_count = $this->get_page_count();
		
		$url_arr = array();
		for($page_index = 0; $page_index < $page_count; $page_index++){
			$url_params = $this->util__generate_url_parameters(array('page'=>$page_index));
			$url_arr[$page_index] = url_append_parameters( $url_begin, $url_params);
		}
		return $url_arr;
	}
	
	/**
	 * 
	 * @param string $setting_name
	 * @param array[] $option_arr  example: array( array('value'=>'price', 'text'=>'Цене'), array('value'=>'name', 'text'=>'Наименованию') )
	 * @param string $current_url_begin  = Yii::app()->getBaseUrl()."/".$this_controller->uniqueid."/". $this_controller->action->id;
	 * @param string $select_tag_template 
	 * * @param string $option_tag_template
	 */
	public function util__render_listbox_items_for_setting($setting_name, $option_arr, $current_url_begin,
			$select_template = "<select id='{id}'>{script}{options}</select>",
			$option_template="<option value='{value}' _href='{href}' {is_selected} >{text}</option>")
	{
		//$current_url_begin = Yii::app()->getBaseUrl()."/".$this_controller->uniqueid."/". $this_controller->action->id;
		
		$ctrl_id = uniqid("ym_");
		
		
		ob_start();
		
		
		?>
		<script>
			$(function(){
				var $select = $("#<?= $ctrl_id ?>");
				$select.attr( "old_val", $select.val() )
				$select.change(function(){
					if( $(this).attr('old_val') === $(this).val() ) return;
					location.href = $(this).find('option:selected').attr('_href');
				});
			});
		</script>
		<?php
		
		
		
		$script_raw = ob_get_contents();
		ob_end_clean();
		
		
		$options_raw = "";
		foreach($option_arr as $option){
			$is_selected = ((string)$option['value'] === $this->get_setting_value($setting_name)) ? 'selected' : '';
			$href = url_append_parameters( $current_url_begin,  $this->util__generate_url_parameters( array($setting_name=>$option['value']) ) );
			
			$option_raw = str_replace(
				array('{value}', '{href}', '{is_selected}', '{text}'), 
				array($option['value'], $href, $is_selected, $option['text']), 
				$option_template
			);
			$options_raw .= $option_raw;
		}
		
		$select_raw = str_replace(
			array('{id}', '{script}','{options}'), 
			array($ctrl_id, $script_raw,$options_raw),
			$select_template
		);
		
		echo $select_raw;	
	}
	
	/**
	 * makes comparision "==" for all items uids (from get_items_uid_arr() ) and return index of same uid
	 * 
	 * @param object $item_uid item as result of cache_callback_calc_items_uid_arr()
	 * @param integer $name index of uid or FALSE if not found
	 */
	function util__get_item_index_by_uid($item_uid)
	{
		if(!$this->cache_is_inited) { throw new Exception(__METHOD__.": cache was not inited!"); return; }
		
		$item_uid_arr = $this->get_items_uid_arr();
		for($i = count($item_uid_arr)-1; $i>=0; $i--){
			$item_uid_cur = $item_uid_arr[$i];
			if($item_uid_cur == $item_uid){
				return $i;
			}
		}
		return false;
		
	}
	
	/**
	 * @param CActiveRecord $model
	 */
	function util__get_item_arr_for_page($model, $key_field_name = 'id'){
		$model_id_arr = $this->get_items_uid_arr_for_page();
		
		if(!is_array($model_id_arr) || !count($model_id_arr)) return array();
		
		$where = $key_field_name." in (".implode(',',$model_id_arr).")";
		$model_arr_unsorted = $model->findAll( array('condition' => $where) );
		
		$model_arr_by_key = array();
		foreach($model_arr_unsorted as $model){				
			$model_arr_by_key[(int)$model[$key_field_name]] = $model;
		}
		$model_arr = array();
		foreach($model_id_arr as $model_id){
			if( !array_key_exists($model_id, $model_arr_by_key) ) continue;
			$model_arr[] = $model_arr_by_key[$model_id];
		}
		return $model_arr;
	}
	
}