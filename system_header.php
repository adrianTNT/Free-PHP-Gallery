<?php

error_reporting(6135); 

include("settings.php");

$page_load_start = microtime(true);


if (!isset($_SESSION)) {
	session_start();
}

// this is needed to properly handle the special characters in file names, image resize might fail without this
setlocale(LC_CTYPE, "en_US.UTF-8");

$gallery_domain = str_replace("www.", "", $_SERVER['HTTP_HOST']);

// the gallery_url is the url of the gallery script (the folder), relative to site root, e.g /gallery
$gallery_url  = dirname($_SERVER['SCRIPT_NAME']);
$gallery_url = str_replace($_SERVER['DOCUMENT_ROOT'], '', $gallery_url);

error_reporting(E_ALL ^ E_NOTICE);

include("system_functions.php");

// test if this user is admin or not
$is_admin = false;

if($_SESSION['session_admin'] == md5($_SESSION['session_secret'].$settings_secret)){
	$is_admin = true;
}


// $photo_categories_array = array();
// $total_photos_array = array();

// this will be an array in array, first key is cat, second key is image file: 
// $categories_array['cars']['retro-car']
$categories_array = array();

$timer_1 = microtime(true);

/*
// loop over files directory and read the categories
if ($handle = opendir('files')) {
    while (false !== ($folder = readdir($handle))) {
		if(is_dir('files/'.$folder) and $folder!='.' and $folder!='..'){
			
			// define this key in the array, it will be blank, store categories as keys
			$categories_array[$folder] = array();

			// $total_photos_array[$folder] = 0;
			
			$files_in_dir = scandir('files/'.$folder);
			foreach($files_in_dir as $file){
				// if file ends in _thumb.jpg
				if(strpos($file, '_thumb.jpg') === strlen($file)-10){
					// $total_photos_array[$folder]++;
					$base_file_name = substr($file, 0,  strlen($file)-10);
					// insert this file in the array of files
					array_push($categories_array[$folder], $base_file_name);
				}
			}
			
		}
    }
    closedir($handle);
}
*/

// loop over files directory and read the categories
$scandir_array = scandir('files');
foreach($scandir_array as $folder){

	if(is_dir('files/'.$folder) and $folder!='.' and $folder!='..'){
		
		// define this key in the array, it will be blank, store categories as keys
		$categories_array[$folder] = array();

		// $total_photos_array[$folder] = 0;
			
		$files_in_dir = scandir('files/'.$folder);
		foreach($files_in_dir as $file){
			if($file!='.' and $file!='..'){
				
				// if file is not the category thumbnail (thumbnail.jpg) and not _thumb.jpg and not _small.jpg
				if($file != "thumbnail.jpg" and substr($file, strlen($file)-10) != "_small.jpg" and substr($file, strlen($file)-10) != "_thumb.jpg"){
					// $total_photos_array[$folder]++;
					$base_file_name = substr($file, 0,  strlen($file)-4);
					// insert this file in the array of files
					array_push($categories_array[$folder], $base_file_name);
					
					//echo "<br>$base_file_name";
					
				}
			}
		}
		
	}
}



$timer_2 = microtime(true);

// !! if you use wrong sorting parameter it will convert the category string keys into integer
ksort($categories_array);


// test if imagemagick is installed
$imagemagick_installed = false;
@exec("convert -version", $convert_output_array);
if(strpos($convert_output_array[0], "imagemagick")){
	$imagemagick_installed = true;
}


?>