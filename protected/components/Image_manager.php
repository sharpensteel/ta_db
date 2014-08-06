<?php




/**  utility functions for images */
class Image_manager{
	
	
	
	/**
	 * 
	 * @param string $src_path
	 * @param string $dest_path
	 * @param Image_resize_calculator $image_resize_calculator
	 * @param integer $dest_image_type  one of IMAGETYPE_<...> constants
	 * @return bool  true if ok
	 */	
	static function resize($src_path, $dest_path, $image_resize_calculator, $dest_image_type = IMAGETYPE_PNG, $jpeg_quality = 90, $throw_exceptions_on_errors = false){
		try{
		
			$err = $image_resize_calculator->check_params();
			if($err !== true){ throw new UserException( "wrong resize calculator params: ".$err); }


			$src_size = getimagesize($src_path);

			if(!$src_size){ throw new UserException("getimagesize() returns error! file: ".$src_path); }

			$src_cx = $src_size[0];
			$src_cy = $src_size[1];
			$src_imagetype = $src_size[2];
			$src_mimetype = $src_size['mime'];

			$resize_params = $image_resize_calculator->calc_final_params($src_cx, $src_cy);
			if(!$resize_params){ throw new UserException("Image_resize_calculator::calc_final_params() returns error! file: ".$src_path); }


			switch($src_imagetype) {
				case IMAGETYPE_GIF:
					$src_resource = imagecreatefromgif($src_path); 
					break;
				case IMAGETYPE_JPEG:
				case IMAGETYPE_JPEG2000:
					$src_resource = imagecreatefromjpeg($src_path);
					break;
				case IMAGETYPE_PNG:
					$src_resource = imagecreatefrompng($src_path); 
					break;
				default:
					throw new UserException("unsupported image type: ".$src_mimetype." (".$src_imagetype.") file:".$src_path);
			}

			if(!$src_resource){  throw new UserException("unable to load image file:".$src_path); }



			$dest_resource = imagecreatetruecolor( $resize_params['dest_cx'], $resize_params['dest_cy'] );

			//print_r($resize_params);

			if (!imagecopyresampled( $dest_resource, $src_resource, 0, 0, $resize_params['src_region_x'], $resize_params['src_region_y'],
					$resize_params['dest_cx'], $resize_params['dest_cy'],
					$resize_params['src_region_cx'], $resize_params['src_region_cy'] )
				)
			{
				return false;
			}


			$dest_dir = mb_pathinfo($dest_path,PATHINFO_DIRNAME);

			if( !file_exists($dest_dir) ){
				$res = mkdir($dest_dir,0775,true);
				if(!$res){ throw new UserException("unable to create directory \"{$dest_dir}\" for image {$dest_path}!"); }
			}


			switch($dest_image_type) {
				case IMAGETYPE_GIF:
					if(!imagejpeg($dest_resource,$dest_path)){ throw new UserException("imagepng returns error. source file:".$src_path." dest. file: ".$dest_path); }
					break;
				case IMAGETYPE_JPEG:
				case IMAGETYPE_JPEG2000:
					if(!imagejpeg($dest_resource, $dest_path, $jpeg_quality )){ throw new UserException("imagepng returns error. source file:".$src_path." dest. file: ".$dest_path); }
					break;
				case IMAGETYPE_PNG:
					if(!imagepng($dest_resource,$dest_path)){ throw new UserException("imagepng returns error. source file:".$src_path." dest. file: ".$dest_path); }
					break;
				default:
					throw new UserException("unsupported image type: ".$src_mimetype." (".$src_imagetype.") file:".$src_path);
			}

			if( !chmod($dest_path, 0664) ){ throw new UserException("chmod returns error.  dest. file:".$dest_path); }

		}catch(UserException $e){
			error_log(__METHOD__."(): ".$e->getMessage());
			if($throw_exceptions_on_errors) throw new UserException(__METHOD__."(): ".$e->getMessage());
			return false;
		}
		return true;
	}
	
	
	static public function test1(){
		$image_resize_calculator = new Image_resize_calculator();
		
		$image_resize_calculator->dest_max_cx = 200;
		$image_resize_calculator->dest_max_cy = 200;
		$image_resize_calculator->src_region_x = 100;
		
		$dir = "/home/PlayGamp_Server/server/site/1/";
		$dir_url = $GLOBALS['CONFIG']['SITE_INNER_URL_FULL']."/1/";
		$src_filename = '0.png';		
		
		echo "src image: <img src='{$dir_url}{$src_filename}' ><br><br><br><br>";
		
		echo "AJUST_TYPE__SAME_SIZE image:<br>";
		$dest_filename = '1.png';
		$image_resize_calculator->ajust_type = Image_resize_calculator::AJUST_TYPE__SAME_SIZE;
		var_dump(self::resize($dir.$src_filename, $dir.$dest_filename, $image_resize_calculator, IMAGETYPE_PNG));
		echo "<img src='{$dir_url}{$dest_filename}' ><br><br><br><br>";	
		
		
		echo "AJUST_TYPE__COVER image:<br>";
		$dest_filename = '2.png';		
		$image_resize_calculator->ajust_type = Image_resize_calculator::AJUST_TYPE__COVER;
		var_dump(self::resize($dir.$src_filename, $dir.$dest_filename, $image_resize_calculator, IMAGETYPE_PNG));
		echo "<img src='{$dir_url}{$dest_filename}' ><br><br><br><br>";	
		
		echo "AJUST_TYPE__INSIDE image:<br>";
		$dest_filename = '3.png';
		$image_resize_calculator->ajust_type = Image_resize_calculator::AJUST_TYPE__INSIDE;
		var_dump(self::resize($dir.$src_filename, $dir.$dest_filename, $image_resize_calculator, IMAGETYPE_PNG));
		echo "<img src='{$dir_url}{$dest_filename}' ><br><br><br><br>";	
		
		echo "AJUST_TYPE__SPECIFIC_SIZE image:<br>";
		$dest_filename = '4.png';
		$image_resize_calculator->ajust_type = Image_resize_calculator::AJUST_TYPE__SPECIFIC_SIZE;
		$image_resize_calculator->dest_cx = $image_resize_calculator->dest_max_cx;
		$image_resize_calculator->dest_cy = $image_resize_calculator->dest_max_cy;
		
		var_dump(self::resize($dir.$src_filename, $dir.$dest_filename, $image_resize_calculator, IMAGETYPE_PNG));
		echo "<img src='{$dir_url}{$dest_filename}' ><br><br><br>";	
	}
};