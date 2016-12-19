<?php

include("system_header.php");

$category_title = trim(strip_tags($_GET['cat_url_string']));

$category_display_title = str_replace('_', ' ', $category_title);
$category_display_title = str_replace('-', ' ', $category_title);
$category_display_title = trim($category_display_title, '-');

if(!is_dir('files/'.urldecode($category_title))){
	include("404.php");
	exit;
}



// test the files to be uploaded, before inserting in DB
$upload_errors = '';
// note that INTERNET EXPLORER (8) detects certain type as "image/pjpeg" !!!
$acceptable_file_types = array("image/jpeg","image/pjpeg","image/gif","image/png","image/bmp");
// max photo size in kb (10MB = 10000);
$max_photo_size = 10000; 


$working_directory = "files/".$category_title;


// test uploads for size and format
if($is_admin and isset($_FILES["photo_box"]) and $_FILES["photo_box"]["name"]!=''){
		
	if(!in_array($_FILES["photo_box"]["type"],$acceptable_file_types)){
		$upload_errors .= "Photo should be jpg, gif or png; ";
	}
	if($_FILES["photo_box"]["size"]/1024 > $max_photo_size){
		$upload_errors .= "Photo is too large (".size_convert($_FILES["photo_box"]["size"])."), maximum size is ".($max_photo_size/1000)."MB; ";
	}
	
	
	// final will be a string like "landscape-with-mountain"
	$photo_name = pathinfo($_FILES["photo_box"]["name"], PATHINFO_FILENAME);
	$photo_name = strtolower($photo_name);
	$photo_name = string_to_file_name($photo_name);

	// make sure the photo file is not duplicate
	if(file_exists($working_directory."/".$photo_name.".jpg")){
		$upload_errors .= "File (".$photo_name.".jpg) already exists in this category (".$category_display_title."); ";
	}
	
	// upload the file and re-size it
	if($upload_errors==''){
		
		// append this prefix, otherwise they will have same name
		$temporary_file = "temporary_".$_FILES["photo_box"]["name"];
		
		// move original file into a  working directory
		move_uploaded_file($_FILES["photo_box"]["tmp_name"], $working_directory."/".$temporary_file);
		
		// save and scale down source image
		if($imagemagick_installed){
			resize_in_limits($working_directory."/".$temporary_file, $working_directory."/".$photo_name.".jpg", 2000, 2000);
		} else {
			gd_resize_in_limits($working_directory."/".$temporary_file, $working_directory."/".$photo_name.".jpg", 2000, 2000);
		}
		
		// !!! delete original file here, later the uploaded file and main image can have same name, avoid removing main file
		unlink($working_directory."/".$temporary_file);
		
		// save and scale down the "display" image
		if($imagemagick_installed){
			resize_in_limits($working_directory."/".$photo_name.".jpg", $working_directory."/".$photo_name."_small.jpg", $settings_photo_width, $settings_photo_height);
		} else {
			gd_resize_in_limits($working_directory."/".$photo_name.".jpg", $working_directory."/".$photo_name."_small.jpg", $settings_photo_width, $settings_photo_height);
		}
		
		// save and CROP the thumb image
		if($imagemagick_installed){
			crop_image($working_directory."/".$photo_name.".jpg", $working_directory."/".$photo_name."_thumb.jpg", $settings_thumbnail_width, $settings_thumbnail_height);
		} else {
			gd_crop_image($working_directory."/".$photo_name.".jpg", $working_directory."/".$photo_name."_thumb.jpg", $settings_thumbnail_width, $settings_thumbnail_height);
		}
		
		// if category has no thumb, then copy this one
		if(!file_exists("files/".$category_title."/thumbnail.jpg")){
			copy($working_directory."/".$photo_name."_thumb.jpg", "files/".$category_title."/thumbnail.jpg");
		}
		
		// refresh page
		header("Location: ?message=Uploaded photo: ".$category_title."/".$photo_name.".jpg&message_type=success");
		exit;
		
	} else {
		header("Location: ?message=".$upload_errors."&message_type=error");
		exit;
	}
	
}




$page_title = ucwords($category_display_title);
$page_description = ucwords($category_display_title)." | Photo Gallery";

?>
<?php include("header.php");?>


<script type="text/javascript"><!--

// selected photo for upload
function selected_photo_file(){
	
	if(document.getElementById('photo_box').value != ''){
		file_extension = document.getElementById('photo_box').value.split('.').pop().toLowerCase();
		if(file_extension != 'jpg' && file_extension != 'jpeg' && file_extension != 'gif' && file_extension != 'png' && file_extension != 'bmp'){
			alert("Sorry, "+file_extension+" files cannot be uploaded, accepted formats are: jpg, gif or png");
			return false;
		} else {
			document.getElementById('photo_form').style.display = 'none';
			document.getElementById('loading_info_div').style.display = 'inline-block';
			document.getElementById('photo_form').submit();
		}
	}
	
}




function show_large_gallery(){
	document.getElementById('large_gallery').style.display = '';
	document.getElementById('thumbnails_strip').style.display = '';
	gallery_is_visible = true;
	// hide scroll bars
	document.body.style.overflowX = 'hidden';
	document.body.style.overflowY = 'hidden';
	
}

function hide_large_gallery(){
	document.getElementById('large_gallery').style.display = 'none';
	document.getElementById('thumbnails_strip').style.display = 'none';
	gallery_is_visible = false;
	// allow scroll bars
	document.body.style.overflowX = '';
	document.body.style.overflowY = '';
	
}



var images_array = new Array();
var images_url_array = new Array();

<?php foreach($categories_array[$category_title] as $photo_file){ ?>
images_array.push('<?php echo addslashes($photo_file);?>');
images_url_array.push('<?php echo rawurlencode($photo_file);?>');
<?php } ?>



document.onkeyup = KeyCheck;       
function KeyCheck(e){
	var KeyID = (window.event) ? event.keyCode : e.keyCode;
	
	// left key pressed
	if(KeyID == 37 && gallery_is_visible){ 
		switch_large_image(active_large_image-1);
	}

	// right key pressed
	if(KeyID == 39 && gallery_is_visible){ 
		switch_large_image(active_large_image+1);
	}
	
	// ESC key pressed
	if(KeyID == 27 && gallery_is_visible){ 
		hide_large_gallery();
	}
}


// active image will represent the KEY not the value, it will probably go to 0 to 5
active_large_image = 0;


//
gallery_is_visible = false;
function switch_large_image(image_key){
	// if requested image is larger than last image, then show first image (loop trough images on keypress)
	if(image_key>=images_array.length) {
		image_key = 0;
	}
	// if requested image is smaller than first image, then show last image (loop trough images on keypress)
	if(image_key<0) {
		image_key = images_array.length-1;
	}
	// disable border from current image
	document.getElementById('strip_image_'+active_large_image).style.borderColor = '#282828';
	// set border on new active image
	document.getElementById('strip_image_'+image_key).style.borderColor = '#0099FF';
	
	// change the overlay title on the image
	document.getElementById('large_image_title').innerHTML = images_array[image_key].replace(/-/g," ");
	
	// switch the large image
	document.getElementById('large_image').src = '<?php echo $gallery_url;?>/<?php echo rawurlencode($category_title);?>/'+images_url_array[image_key]+'_small.jpg';
	
	// show the large image and strip line
	show_large_gallery();
	// mark this image as active
	active_large_image = image_key;
	
	
	// alert(window.innerWidth+'x'+window.innerHeight);
	
	// alert(document.getElementById('thumbnails_container').offsetWidth);
	
	center_thumbnails_container();
	
}

// will also be fired on window resize
function center_thumbnails_container(){
	
	thumbnails_width = document.getElementById('thumbnails_container').offsetWidth;
	
	// active_image_offset = document.getElementById('strip_image_'+images_array[active_large_image]).offsetLeft;
	active_image_offset = active_large_image*90;
	
	// the limit on left side, (maximum position of the thumbnails)
	thumbnails_left_limit = 0;
	
	// the limit on right side, (minimum position of the thumbnails)
	thumbnails_right_limit = window.innerWidth-thumbnails_width-20;
	
	new_thumbnails_position = Math.round((window.innerWidth/2)-active_image_offset)-59;
	
	// limit the left margin position
	if(new_thumbnails_position>thumbnails_left_limit){
		new_thumbnails_position = thumbnails_left_limit;
	}
	
	// limit the right margin position
	if(new_thumbnails_position<thumbnails_right_limit){
		new_thumbnails_position = thumbnails_right_limit;
	}
	
	// if window is larger than thumbs strip, then position at default value (all above calculations canceled)
	if(window.innerWidth>=thumbnails_width){
		new_thumbnails_position = 0;
	}

	
	document.getElementById('thumbnails_container').style.marginLeft = new_thumbnails_position+'px';
	
	// console.log('Left margin:	'+thumbnails_left_limit);
	// console.log('Right margin:	'+thumbnails_right_limit);
	// console.log('New position:	'+new_thumbnails_position);
	
	// limit the height of the image to fit in that area, not needed for most computers, useful for cell phones
	document.getElementById('large_image').style.maxWidth = window.innerWidth-12+'px';
	document.getElementById('large_image').style.maxHeight = window.innerHeight-document.getElementById('thumbnails_strip').offsetHeight-12+'px';
	
}


//--></script>




<h1 style="text-transform:capitalize;"><?php echo htmlentities($category_display_title, ENT_QUOTES, "UTF-8");?></h1>

<p class="breadcrumb"><a href="/">home</a> &gt; <a href="<?php echo $gallery_url;?>">gallery</a> &gt; <?php echo htmlentities($category_display_title , ENT_QUOTES, "UTF-8");?></p>

<?php if(count($categories_array[$category_title])<=0){?>
<p>There are no photos under <strong><?php echo htmlentities($category_display_title , ENT_QUOTES, "UTF-8");?></strong>.</p>
<?php } ?>


<?php if($is_admin){?>

    <form name="photo_form" id="photo_form" enctype="multipart/form-data" method="post" action="" style="display:none; border:1px solid #CCC; padding:10px; background-color:#F5F5F5; margin-bottom:10px;">
    
        <span id="photo_box_span">
        <input type="file" name="photo_box" id="photo_box" accept="image/*" onChange="selected_photo_file();" style="border:none;"/>
        </span>
        
       
        <img src="<?php echo $gallery_url;?>/layout/delete_16x16.gif" width="16" height="16" style="float:right; cursor:pointer;" onmouseup="document.getElementById('photo_form').style.display='none'; document.getElementById('photo_upload_button').style.display='';" alt="close upload form" title="close upload form" />
    
    </form>
    
    <span id="loading_info_div" style="background-image:url('<?php echo $gallery_url;?>/layout/loading_20x20.gif'); background-repeat:no-repeat; padding-left:24px; padding-top:3px; padding-bottom:2px; margin-top:10px; color:#EA0000; display:none;">Please wait, photo is uploading</span>
    
    <!--
    <a href="<?php echo $gallery_url;?>/upload?category_title=<?php echo urlencode($category_title);?>" class="liquid_button">Upload photos</a>
	-->
    
    <a id="photo_upload_button" href="JavaScript:void(0);" onmouseup="document.getElementById('photo_form').style.display=''; document.getElementById('photo_upload_button').style.display='none';" class="liquid_button" style="padding-left:10px; padding-right:10px; margin-right:5px;">Upload photos</a>
    
    <a id="photos_edit_button" href="<?php echo $gallery_url;?>/<?php echo rawurlencode($category_title);?>/edit-photos" class="liquid_button" style="padding-left:10px; padding-right:10px;">Edit photos</a>
    
    
<?php } // if is admin ?>



<?php if(count($categories_array[$category_title])>0){?>
	<div style="display:block; margin-top:10px;">
    <?php $photo_counter = 0;?>
	<?php foreach($categories_array[$category_title] as $photo_file){ ?>
    <img src="<?php echo $gallery_url."/".rawurlencode($category_title)."/".rawurlencode($photo_file)."_thumb.jpg" ;?>" style="padding:5px; border:1px solid #CCC; margin-top:5px; margin-right:5px; cursor:pointer;" onclick="switch_large_image(<?php echo $photo_counter;?>);" alt="<?php echo htmlentities(ucwords(str_replace('-', ' ', $photo_file)));?>" title="<?php echo htmlentities($photo_file, ENT_QUOTES, "UTF-8");?>" />
    <?php $photo_counter++;?>
    <?php } ?>
    </div>
<?php } // if there are photos ?>





<?php // the top: should be minus half of the thumbs height; minus half the padding around thumbs ?>
<table id="large_gallery" width="100%" style="height:100%; position:fixed; top:-<?php echo round($settings_thumbnail_height/4)+10;?>px; left:0px; display:none; text-align:center; margin:auto;" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="100%" height="100%" align="center" valign="middle" bgcolor="#000000" style="overflow:hidden;" onclick="do_with_delay='close_gallery'; setTimeout('if(do_with_delay==\'switch_photo\'){switch_large_image(active_large_image+1)} else { hide_large_gallery();}', 20);">
    
    <input name="" type="button" class="cancel_button_30" style="position:absolute; top:50px; right:10px;" title="close the gallery" />

	<span style="display:inline-block; width:auto; white-space:nowrap;">
    
        <img id="large_image" src="" style="cursor:pointer; padding:5px; background-color:#FFFFFF; border:1px solid #CCCCCC; margin:auto; position:relative; bottom:-14px;" onclick="setTimeout('do_with_delay=\'switch_photo\'', 10);" alt="" /> 
        
        <?php // this overlay title has a smaller width (fitting image border) by placing the transparent background ~10px to the right then moving the complete title to the left ;?>
                
        <span id="large_image_title" style="color:#FFFFFF; display:block; width:100%; line-height:1em; padding-top:9px; height:20px; position:relative; top:-21px;left:-6px; background-image:url('<?php echo $gallery_url;?>/layout/transparent_bg.png'); background-repeat:no-repeat; background-position:12px 0px; text-transform:capitalize;"></span>
        
	</span>
    
    <?php 
	// the above complicated thing with do_with_delay (close_gallery/switch_photo) will allow to make a difference between clicking the photo 
	// or its parent element; then decide if it should show next photo (photo click) or close gallery (background click)
	?>
    
    </td>
  </tr>
</table>


<!-- thumbnails strip -->
<div id="thumbnails_strip" style="position:fixed; height:<?php echo round(($settings_thumbnail_height/2)+10);?>px; bottom:0px; left:0px; width:100%; background-color:#282828; border-top:1px solid #555555; text-align:center; padding:5px; display:none;">
    
    <span id="thumbnails_container" style="display:inline-block; white-space:nowrap; width:<?php echo count($categories_array[$category_title])*90;?>px; height:<?php echo round(($settings_thumbnail_height/2)+10);?>px; overflow:hidden;">
    <?php $photo_counter = 0;?>
    <?php foreach($categories_array[$category_title] as $photo_file){ ?>

    <img id="strip_image_<?php echo $photo_counter;?>" src="<?php echo $gallery_url;?>/<?php echo rawurlencode($category_title);?>/<?php echo rawurlencode($photo_file);?>_thumb.jpg" border="0" alt="" style="display:inline-block; float:left; cursor:pointer; border:5px solid #282828;" onmouseup="switch_large_image(<?php echo $photo_counter;?>);" width="<?php echo round($settings_thumbnail_width/2);?>" height="<?php echo round($settings_thumbnail_height/2);?>" />
	
    <?php $photo_counter++;?>
	<?php } ?>
    </span>
</div><!-- end of thumbnails_strip -->   

<script type="text/javascript"><!--
window.onresize = function(){ center_thumbnails_container();}
--></script>


<?php include("footer.php");?>
