<?php


if(!function_exists("admin_only")){
	function admin_only(){
		global $is_admin, $gallery_url;
		if(!$is_admin){
			$_SESSION['session_return_url'] = $_SERVER['REQUEST_URI'];
			header("Location: ".$gallery_url."/login?message=you need to login to access that page&message_type=error");
			exit;
		}
	}
}


// a function that will truncate a string
// this function will truncate a word if needed
if (!function_exists("truncate_by_letters")) {
	function truncate_by_letters($string,$chars,$append = '...') {
		if(mb_strlen($string,"UTF-8") > $chars) {
			$string = mb_substr($string, 0, $chars,"UTF-8");
			if($trunc_at !== FALSE)
			$string = mb_substr($string, 0, $chars,"UTF-8");
			$string = $string.$append;
		}
		return $string;
	}
}

if(!function_exists("size_convert")){
	function size_convert($size){
		$unit=array('B','KB','MB','GB','TB','PB');
		$string_to_return = round($size/pow(1024,($i=floor(log($size,1024)))),2);
		return number_format($string_to_return,2,".",' ').' '.$unit[$i];
	}
}

// !!! attention, this function uses the function "normalize_characters" too !!!
// formats a string like "ads zxcv eew" to "ads-zxcv-eew";
if(!function_exists("string_to_url")){
	function string_to_url($string){
		//normalize it BEFORE replacing the unacepted chars
		$string = normalize_characters($string);
		//
		$string = preg_replace("/[^A-Za-z0-9]/", "-", $string);
		$string = preg_replace("/[-]+/", '-', $string);
		$string = trim($string,"-");
		$string = strtolower($string);
		return $string;
	}
}


// Converts "Sandra" to "Sandra" and "Bucuresti" to "Bucuresti"
if(!function_exists("normalize_characters")){
	function normalize_characters($string){
		// !!!
		// !!! NOTE that SPECIAL chars like "ă" might convert themselfs into "a" in this file; 
		// in that case, you need to pick UTF-8 when saving this file with NOTEPAD
		// OR in Dreamwever it seems to work ok when saving the file and picking "C (Canonical Decomposition, followed by ...)" under file name
		$string = str_replace(array("ä", "ă", "î", "ş", "ţ", "â", "ő", "ö", "í", "Ă", "Î", "Ş", "Ţ", "Â", "Ő", "Ö", "ĺ", "Á", "á", "É", "é", "Í", "í", "Ñ", "ñ", "Ó", "ó", "Ú", "ú", "Ü", "ü", "¿", "¡", "è", "È", "Ā", "ß", "Ä"), array("a", "a", "i", "s", "t", "a", "o", "o", "i", "A", "I", "S", "T", "A", "O", "O", "I", "A", "a", "E", "e", "I", "i", "N", "n", "O", "o", "U", "u", "U", "u", "?", "i", "e", "E", "A", "ss", "A"), $string);
		return $string;
	}
}


// resizes an image to exactly given sizes by cropping the larger size
if(!function_exists("crop_image")){
	function crop_image($original_file, $destination_file, $new_width = 120, $new_height = 90){

		list($original_width,$original_height) = getimagesize($original_file);

		// first test if difference between old/new width are larger than diff betwern old/new height
		$width_difference = $original_width/$new_width;
		$height_difference = $original_height/$new_height;
		
		// resize width first if width difference < height difference or if differences are the same (old/new image have same proportions);

		// resize width first if width difference is smaller than height difference
		if($width_difference<=$height_difference){
			$resize_first = "width";
		}
		// resize height first if height difference is smaller than width difference
		if($height_difference<$width_difference){
			$resize_first = "height";
		}
		
		if($resize_first == "width"){
			exec("convert ".escapeshellarg($original_file)."  -resize ".$new_width."x -flatten -gravity center -crop ".$new_width."x".$new_height."+0+0 -quality 100 ".escapeshellarg($destination_file));
		} else {
			exec("convert ".escapeshellarg($original_file)."  -resize x".$new_height." -flatten -gravity center -crop ".$new_width."x".$new_height."+0+0 -quality 100 ".escapeshellarg($destination_file));
		}
		
	}
}
// crop_image("ferrari.jpg","ferrari_new.jpg",120,90);



// resize an image so that it doesn't pass given maximum values
if(!function_exists("resize_in_limits")){
	function resize_in_limits($original_file, $destination_file, $max_width = 125, $max_height = 200){
	
		list($original_width,$original_height) = getimagesize($original_file);
		
		// prevent scalling up smaller images ? (get the smaller one...)
		$max_width = min($max_width,$original_width);
		$max_height = min($max_height,$original_height);
		
		// first test if difference between old/new width are larger than diff betwern old/new height
		$width_difference = $original_width/$max_width;
		$height_difference = $original_height/$max_height;
		
		// resize by width if width_difference is smaller than height_difference
		if($width_difference>=$height_difference){
			exec("convert ".escapeshellarg($original_file)."  -resize ".$max_width."x -quality 100 -flatten ".escapeshellarg($destination_file));
		}
		// resize by height if width_difference is smaller than height_difference
		if($width_difference<$height_difference){
			exec("convert ".escapeshellarg($original_file)."  -resize x".$max_height." -quality 100 -flatten ".escapeshellarg($destination_file));
		}
	}
}
// resize_in_limits("ferrari.jpg","ferrari_new.jpg",600,400);




// resize an image into a maximum new width/height
if(!function_exists("gd_resize_in_limits")){
	function gd_resize_in_limits($original_file, $destination_file=NULL, $maximum_width = 550, $maximum_height = 413){
		
		
		
		// get width and height of original image
		list($original_width,$original_height) = getimagesize($original_file);
		
		// do not scale image up ?!
		$maximum_width = min($maximum_width, $original_width);
		$maximum_height = min($maximum_height, $original_height);
		
		
		// calculating new width x height 
		
		// decide if the width or the height is the one that needs to be scalled the most in order to fit in the box
		$width_scale_difference = $original_width/$maximum_width;
		$height_scale_difference = $original_height/$maximum_height;
		
		$resize_by = "width";
		if($height_scale_difference > $width_scale_difference){
			$resize_by = "height";
		}
		
		// resize by width
		if($resize_by == "width"){
			$new_width = $maximum_width;
			$percent = $original_height/$original_width;
			$new_height = $new_width*$percent;
		}
		
		// resize by height
		if($resize_by == "height"){
			$new_height = $maximum_height;
			$percent = $original_width/$original_height;
			$new_width = $new_height*$percent;
		}
		
		if($original_height > $original_width){
			$new_height = $maximum_height;
			$percent = $original_height/$original_width;
			$new_width = $new_height/$percent;
		}
		if($original_width==$original_height){
			$new_width = $maximum_width;
			$new_height = $maximum_height;
		}
		
		$new_width = round($new_width);
		$new_height = round($new_height);
		
		// if original image was smaller then do not scale it up.
		if($original_width<$new_width and $original_height<$new_height){
			$new_width = $original_width;
			$new_height = $original_height;
		}
		
		$smaller_image = imagecreatetruecolor($new_width, $new_height);
		
		// load the image
		if(substr_count(strtolower($original_file), ".jpg") or substr_count(strtolower($original_file), ".jpeg")){
			$original_image = imagecreatefromjpeg($original_file);
		}
		if(substr_count(strtolower($original_file), ".gif")){
			$original_image = imagecreatefromgif($original_file);
		}
		if(substr_count(strtolower($original_file), ".png")){
			$original_image = imagecreatefrompng($original_file);
		}
	
		
		imagecopyresampled($smaller_image,$original_image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
		
		// enable interlace, seems to be have clearer lines (tested with a large thin "x"), file size is smaller too
		// doesn't work on most servers
		// imageinterlace($square_image, true);
		
		// if no destination file was given then display a jpg or png	
		if(!$destination_file){
			//imagepng($smaller_image,NULL,9);
			imagejpeg($smaller_image,NULL,100);
		}
		
		// save the smaller image FILE if destination file given
		if(substr_count(strtolower($destination_file), ".jpg")){
			imagejpeg($smaller_image,$destination_file,100);
		}
		if(substr_count(strtolower($destination_file), ".gif")){
			imagegif($smaller_image,$destination_file);
		}
		if(substr_count(strtolower($destination_file), ".png")){
			imagepng($smaller_image,$destination_file,9);
		}
				
		imagedestroy($original_image);
		imagedestroy($smaller_image);
	
	}
}
// gd_resize_in_limits("ferrari.jpg",NULL,1300,1000);



// a function that crops an image to any given size and centers the content (can also make square thumbs like another function).
if(!function_exists("gd_crop_image")){
	function gd_crop_image($original_file, $destination_file=NULL, $new_width = 50, $new_height = 50){
		
		// get width and height of original image
		list($original_width,$original_height) = getimagesize($original_file);
		
		// get file extension
		$path_parts = pathinfo($original_file);
		$image_extension = $path_parts['extension'];
		
		// load the original image
		if($image_extension == "jpg" or $image_extension == "jpeg"){
			$original_image = imagecreatefromjpeg($original_file);
		}
		if($image_extension == "gif"){
			$original_image = imagecreatefromgif($original_file);
		}
		if($image_extension == "png"){
			$original_image = imagecreatefrompng($original_file);
		}
		
		// - if original width or original height smaller than new ones then resize according to smaller one
		// - detect original width - new width and original height - new height, resize according to smallest result.
		
		// first test if difference between old/new width are larger than diff betwern old/new height
		$width_difference = $original_width/$new_width;
		$height_difference = $original_height/$new_height;
		
		// resize width first if width difference < height difference or if differences are the same (old/new image have same proportions);

		// resize width first if width difference is smaller than height difference
		if($height_width < $height_difference){
			$resize_first = "width";
		}

		// resize height first if height difference is smaller than width difference
		if($height_difference<$width_difference){
			$resize_first = "height";
		}
		
		// new width/height has same proportions as original width/height
		if($height_difference==$width_difference){
			$resize_first = "equal";
		}
		
		// create a new image that will hold the result
		$cropped_image = imagecreatetruecolor($new_width, $new_height);
		// enable interlace, seems to be have clearer lines (tested with a large thin "x")
		// file size is smaller too, doesn't work on most servers
		//imageinterlace($cropped_image, true);
		
		// resize width first, start x and dest x will be 0;
		if($resize_first == "width"){
			// width is directly $new_width and height is temporary, before fited in new size
			$temporary_height = round($original_height/$width_difference);
			// get the proportions/difference between the temporary height and the needed height
			$temporary_difference = ($temporary_height/$new_height);
			if($temporary_difference>0){
				$source_y = $original_height-($original_height/$temporary_difference);
			} else {
				$source_y=0;
			}
			$source_y = round($source_y/2);
			imagecopyresampled($cropped_image,$original_image, 0, 0, 0, $source_y, $new_width, $temporary_height, $original_width, $original_height);
		}		
		
		// resize height first, start x and dest x will be 0;
		if($resize_first == "height"){
			// height is directly $new_height and width is temporary, before fited in new size
			$temporary_width = round($original_width/$height_difference);
			// get the proportions/difference between the temporary width and the needed width
			$temporary_difference = $temporary_width/$new_width;
			
			//echo "$original_width-$original_width/$temporary_difference"; exit;
			if($temporary_difference>0){
				$source_x = $original_width-($original_width/$temporary_difference);
			} else {
				$source_x = 0;
			}
			$source_x = round($source_x/2);
			imagecopyresampled($cropped_image,$original_image, 0, 0, $source_x, 0, $temporary_width, $new_height, $original_width, $original_height);
		}
		
		// meaning that original image has same proportions as new image
		if($resize_first == "equal"){
			imagecopyresampled($cropped_image,$original_image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
		}
		
		// output image in browser no destination file
		if(!$destination_file){		
			//header("Content-type: image/png");
			//imagepng($cropped_image,NULL,9);
			header("Content-type: image/jpeg");
			imagejpeg($cropped_image,NULL,100);
		}
		
		// get file extension of destination file
		if(isset($destination_file) and $destination_file!=""){
			$destination_path_parts = pathinfo($original_file);
			$destination_image_extension = $path_parts['extension'];
		}
		
		if($destination_image_extension == "jpg" or $destination_image_extension == "jpeg"){
			imagejpeg($cropped_image,$destination_file,100);
		}
		if($destination_image_extension == "gif"){
			imagegif($cropped_image,$destination_file);
		}
		if($destination_image_extension == "png"){
			imagepng($cropped_image,$destination_file,9);
		}
		
		imagedestroy($original_image);
		imagedestroy($cropped_image);
		
	}
}
// gd_crop_image("temp/ferrari.jpg",NULL,300,200);


// a function to remove all contents of a directory (including dir)
if (!function_exists("rmdir_r")) {
	function rmdir_r($path){
		if (is_dir($path) && !is_link($path)){
			if ($dh = opendir($path)){
				while (($sf = readdir($dh)) !== false){
					if ($sf == '.' || $sf == '..'){
						continue;
					}
					if(!rmdir_r($path.'/'.$sf)){
						// throw new Exception($path.'/'.$sf.' could not be deleted.');
						// this code ^ fails on some servers, trying print_r instead
						print_r($path.'/'.$sf.' could not be deleted.');
					}
				}
				closedir($dh);
			}
			return rmdir($path);
		}
		return unlink($path);
	}
}

// removes special characters that are unsafe for file names
if(!function_exists("string_to_file_name")){
	function string_to_file_name($string){
		
		$string = strip_tags($string);
		$string = trim($string);
		
		$string = str_replace("#", "", $string);
		$string = str_replace("&", "", $string);
		$string = str_replace("+", "", $string);
		$string = str_replace("/", "", $string);
		$string = str_replace("\\", "", $string);
		$string = str_replace("?", "", $string);
		
		$string = str_replace(":", "", $string);
		$string = str_replace("*", "", $string);
		$string = str_replace("<", "", $string);
		$string = str_replace(">", "", $string);
		// Dreamweaver shows this car as reserved when renaming a folder, so don't allow it here eider
		$string = str_replace("|", "", $string);
		// windows doesn't accept " but accepts '
		$string = str_replace('"', "", $string);
		
		
		// trim it again after removed some characters that might have been on margins after a space
		$string = trim($string);
		
		return $string;
	}
}

?>