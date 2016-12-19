<?php

include("system_header.php");

admin_only();



// change category cover
if(isset($_GET['photo_to_make_cover']) and $_GET['photo_to_make_cover']!='' and isset($_GET['category_box']) and $_GET['category_box']!=''){
	// verify key to make cover
	if($_GET['key']!=md5($_SESSION['session_secret'].$_GET['photo_to_make_cover'])){

		// delete old cover
		@unlink("files/".$_GET['category_box']."/thumbnail.jpg");
		copy("files/".$_GET['category_box']."/".$_GET['photo_to_make_cover']."_thumb.jpg", "files/".$_GET['category_box']."/thumbnail.jpg");
		exit;
		
	}
}


// delete a photo ?!
if(isset($_GET['photo_to_delete']) and $_GET['photo_to_delete']!='' and isset($_GET['category_box']) and $_GET['category_box']!=''){
	
	
	// !!! do not trim the value of photo_to_delete, some might contain funny characters on margins
	
	// verify key to delete photo
	if($_GET['key']==md5("dlt".$_SESSION['session_secret']."img")){
		
		unlink("files/".$_GET['category_box']."/".$_GET['photo_to_delete'].".jpg");
		unlink("files/".$_GET['category_box']."/".$_GET['photo_to_delete']."_small.jpg");
		unlink("files/".$_GET['category_box']."/".$_GET['photo_to_delete']."_thumb.jpg");
		
		exit;
		
	}
}

// veify key to edit photo
if($_GET['key']!=md5($_SESSION['session_secret'].$_GET['original_category_box'])){
	echo '<span class="photo_edit_error"><strong>Key error</strong></span>';
	exit;
}


// do not trim() the original title, otherwise we will not find the original file to rename

// convert the new title_box to a file name safe string, will also trim it
$_GET['title_box'] = string_to_file_name($_GET['title_box']);


if($_GET['original_category_box']=='' or $_GET['category_box']=='' or $_GET['original_title_box']=='' or $_GET['title_box']==''){
	echo '<span class="photo_edit_error"><strong>Missing data (title or category)</strong></span>';
	exit;
}


if($_GET['original_category_box']!=$_GET['category_box'] or $_GET['original_title_box']!=$_GET['title_box']){
	
	// we checked that folder or name is different, so now only check if destination file alrady exists
	if(file_exists("files/".$_GET['category_box']."/".$_GET['title_box'].".jpg")){
		$return_data_array['status'] = "error";
		$return_data_array['status_message'] = "Cannot rename photo, ".$_GET['title_box']." already exists";
		$return_data_array['form_id'] = $_GET['form_id'];
		
		echo json_encode($return_data_array);
		exit;
	}
	
	// move photo, thumb and source
	rename("files/".$_GET['original_category_box']."/".$_GET['original_title_box'].".jpg", "files/".$_GET['category_box']."/".$_GET['title_box'].".jpg");
	rename("files/".$_GET['original_category_box']."/".$_GET['original_title_box']."_small.jpg", "files/".$_GET['category_box']."/".$_GET['title_box']."_small.jpg");
	rename("files/".$_GET['original_category_box']."/".$_GET['original_title_box']."_thumb.jpg", "files/".$_GET['category_box']."/".$_GET['title_box']."_thumb.jpg");
	
	
	// echo '<span class="photo_edit_confirmation"><strong>Data saved</strong></span>';
	
	$return_data_array['status'] = "ok";
	$return_data_array['status_message'] = "Data saved";
	$return_data_array['form_id'] = $_GET['form_id'];
	// return the new title, becuase we removed special chars, 
	// it might be different than what was initially sent to this script
	$return_data_array['title_box'] = $_GET['title_box'];
	
	
	echo json_encode($return_data_array);
	exit;

} else {
	
	$return_data_array['status'] = "error";
	$return_data_array['status_message'] = "No editing needed";
	$return_data_array['form_id'] = $_GET['form_id'];
	
	echo json_encode($return_data_array);
	exit;
}

?>

