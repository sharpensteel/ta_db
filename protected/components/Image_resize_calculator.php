<?php

class Image_resize_calculator{
	
	const AJUST_TYPE__SAME_SIZE = 0; // dest size same size as source image region (or whole source image if region not set)
	const AJUST_TYPE__SPECIFIC_SIZE = 2; // must be set $dest_cx AND $dest_cy
	const AJUST_TYPE__COVER = 3; // source image will cover result image; must be set $dest_cx AND $dest_cy
	const AJUST_TYPE__INSIDE = 4; // source image will be inside by result image; must be set $dest_cx AND $dest_cy


	/** @var integer  one of AJUST_TYPE__<...> constants */
	public $ajust_type = 0;

		
	// selecting source region, for all ajust types
	public $src_region_x = null;
	public $src_region_y = null;
	public $src_region_cx = null;
	public $src_region_cy = null;
	
	
	/* @var integer|null  for AJUST_TYPE__SPECIFIC_SIZE */
	public $dest_cx = null;
	/* @var integer|null  for AJUST_TYPE__SPECIFIC_SIZE */
	public $dest_cy = null;
	
	
	/** @var integer|null  only for AJUST_TYPE__SAME_SIZE */
	public $dest_min_cx = null; 
	
	/** @var integer|null  only for AJUST_TYPE__SAME_SIZE */
	public $dest_min_cy = null;
	
	/** @var integer|null  for AJUST_TYPE__SAME_SIZE  or AJUST_TYPE__COVER or AJUST_TYPE__CONTAIN */
	public $dest_max_cx = null;
	
	/** @var integer|null  for AJUST_TYPE__SAME_SIZE or AJUST_TYPE__COVER or AJUST_TYPE__CONTAIN */
	public $dest_max_cy = null;

	
	
	/** @return string|true return true if ok or error string otherwise */
	public function check_params(){
		$err = "";
				
		switch($this->ajust_type){
			case self::AJUST_TYPE__SAME_SIZE:
				break;
			case self::AJUST_TYPE__SPECIFIC_SIZE:
				if(!$this->dest_cx) $err .= "dest_cx not set;";
				if(!$this->dest_cy) $err .= "dest_cy not set;";
				break;
			case self::AJUST_TYPE__COVER:
			case self::AJUST_TYPE__INSIDE:
				if(!$this->dest_max_cx){
					$err .= "dest_max_cx not set;";
				}
				if(!$this->dest_max_cy){
					$err .= "dest_max_cy not set;";
				}
				break;
			
		}
		
		return $err === "" ? true : $err;
		
	}
	
	/**
	 * @return array|false if succeed, returns array( 'dest_cx'=>? 'dest_cy'=>?, 'src_region_x'=>?, 'src_region_y'=>?, 'src_region_cx'=>?, 'src_region_cy'=>?); or null if error
	 */
	public function calc_final_params($src_cx, $src_cy){		
		$res = array(
			'dest_cx' => 0,
			'dest_cy' => 0,
			'src_region_x' => (int)$this->src_region_x,
			'src_region_y' => (int)$this->src_region_y,
			'src_region_cx' => $this->src_region_cx ? $this->src_region_cx : $src_cx,
			'src_region_cy' => $this->src_region_cy ? $this->src_region_cy : $src_cy,
		);
		if(!$src_cx || !$src_cy) return $res;
		
		$res['src_region_cx'] = min($res['src_region_cx'], $src_cx - $res['src_region_x']);
		$res['src_region_cy'] = min($res['src_region_cy'], $src_cy - $res['src_region_y']);

		
		$err = $this->check_params();
		if($err !== true) { 
			error_log(__FUNCTION__.": ".$err);
			return false;
		}
		
		$dest_cx = $dest_cy = 0;
		switch($this->ajust_type){
			case self::AJUST_TYPE__SAME_SIZE:
				$dest_cx = $res['src_region_cx'];
				$dest_cy = $res['src_region_cy'];
				if($this->dest_min_cx) $dest_cx = max($dest_cx, $this->dest_min_cx);
				if($this->dest_min_cy) $dest_cy = max($dest_cy, $this->dest_min_cy);
				if($this->dest_max_cx) $dest_cx = min($dest_cx, $this->dest_max_cx);
				if($this->dest_max_cy) $dest_cy = min($dest_cy, $this->dest_max_cy);
				
				$src_ratio = $res['src_region_cx'] / $res['src_region_cy'];
				if($dest_cx < $res['src_region_cx'] || $dest_cy < $res['src_region_cy']){ // region goes smaller, correct ratio
					if( $dest_cx > floor($dest_cy * $src_ratio) )
						$dest_cx = floor($dest_cy * $src_ratio);
					else
						$dest_cy = floor($dest_cx / $src_ratio);
				}
				else if($dest_cx > $res['src_region_cx'] || $dest_cy > $res['src_region_cy']){ // region goes larger, correct ratio
					if( $dest_cx < ceil($dest_cy * $src_ratio) )
						$dest_cx = ceil($dest_cy * $src_ratio);
					else
						$dest_cy = ceil($dest_cx / $src_ratio);
				}
				
				$res['src_region_cx'] = $res['dest_cx'] = $dest_cx; 
				$res['src_region_cy'] = $res['dest_cy'] = $dest_cy;
				break;				
			case self::AJUST_TYPE__SPECIFIC_SIZE:
				$res['dest_cx'] = $this->dest_cx;
				$res['dest_cy'] = $this->dest_cy;
				break;			
			case self::AJUST_TYPE__COVER:
				$res['dest_cx'] = $this->dest_max_cx;
				$res['dest_cy'] = $this->dest_max_cy;
				
				$ratio = $res['dest_cx'] / $res['dest_cy'];
				
				$src_cy = $res['src_region_cy'];
				$src_cx = floor($res['src_region_cy'] *  $ratio);
				if($src_cx > $res['src_region_cx']){
					$src_cx = $res['src_region_cx'];
					$src_cy = floor($src_cx / $ratio);
				}
				$res['src_region_x'] -= floor(($src_cx - $res['src_region_cx'])/2);
				$res['src_region_y'] -= floor(($src_cy - $res['src_region_cy'])/2);
				$res['src_region_cx'] = $src_cx;
				$res['src_region_cy'] = $src_cy;												
				break;				
			case self::AJUST_TYPE__INSIDE:				
				$ratio = $res['src_region_cx'] / $res['src_region_cy'];
				
				$dest_cy = $this->dest_max_cy;
				$dest_cx = floor($ratio * $this->dest_max_cy);
				if($dest_cx > $this->dest_max_cx){
					$dest_cx = $this->dest_max_cx;
					$dest_cy = floor($this->dest_max_cx / $ratio);
				}
				
				$res['dest_cx'] = $dest_cx;
				$res['dest_cy'] = $dest_cy;
				break;
			default:
				error_log(__FUNCTION__.": unsupported ajust type ".$this->ajust_type);
				return false;
		}
		
		//print_r($res);
		
		return $res;
	}
	
}