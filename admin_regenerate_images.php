<?php

include("system_header.php");

admin_only();

$regenerate_thumbnails_log = '';
$total_files_generated = 0;

foreach($categories_array as $category_title=>$images_array){
	
	$working_directory = "files/".$category_title;
	
	// remove thumbnail of category
	@unlink($working_directory."/thumbnail.jpg");
	
	foreach($categories_array[$category_title] as $image_file){
		
		$regenerate_thumbnails_log .= "\nLoading ".$working_directory."/".$image_file.".jpg";
		
		// scale down the "display" image
		@unlink($working_directory."/".$image_file."_small.jpg");
		if($imagemagick_installed){
			resize_in_limits($working_directory."/".$image_file.".jpg", $working_directory."/".$image_file."_small.jpg", $settings_photo_width, $settings_photo_height);
		} else {
			gd_resize_in_limits($working_directory."/".$image_file.".jpg", $working_directory."/".$image_file."_small.jpg", $settings_photo_width, $settings_photo_height);
		}
		
		// save the thumb image
		@unlink($working_directory."/".$image_file."_thumb.jpg");
		if($imagemagick_installed){
			crop_image($working_directory."/".$image_file.".jpg", $working_directory."/".$image_file."_thumb.jpg", $settings_thumbnail_width, $settings_thumbnail_height);
		} else {
			gd_crop_image($working_directory."/".$image_file.".jpg", $working_directory."/".$image_file."_thumb.jpg", $settings_thumbnail_width, $settings_thumbnail_height);
		}
		
		// make this image category thumbnail unless one already exists
		if(!file_exists($working_directory."/thumbnail.jpg")){
			copy($working_directory."/".$image_file."_thumb.jpg", $working_directory."/thumbnail.jpg");
		}
		
		$total_files_generated++;
		
	}
}

$regenerate_thumbnails_log = trim($regenerate_thumbnails_log);


// header("Location: ".$gallery_url."/admin-categories?message=category added&message_type=success");
// exit;


?>
<?php include("header.php");?>
<h1>Regenerate tumbnails</h1>
<p class="breadcrumb"><a href="/">home</a> &gt; <a href="<?php echo $gallery_url;?>">gallery</a> &gt; <a href="<?php echo $gallery_url;?>/admin">admin</a> &gt; regenerate thumbnails</p>

<h2>Log</h2>
<p><?php echo nl2br(htmlentities($regenerate_thumbnails_log, ENT_QUOTES, "UTF-8"));?></p>
<p style="color:#EA0000;">Generated thumbnails and display images for <?php echo $total_files_generated;?> images.</p>

<?php include("footer.php");?>