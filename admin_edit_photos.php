<?php

include("system_header.php");

admin_only();


$category_title = trim(strip_tags($_GET['cat_url_string']));

$category_display_title = str_replace('-', ' ', $category_title);
$category_display_title = trim($category_display_title, '-');

if(!is_dir('files/'.$category_title)){
	include("404.php");
	exit;
}


?>
<?php include("header.php");?>

<script type="text/javascript"><!--

function change_edit_photo_form(form_id){
	// show submit button
	document.getElementById('button_'+form_id).style.display=''; 
	// hide the div that holds messages like "data saved"
	document.getElementById('form_info_div_'+form_id).style.display='none';
}

function submit_edit_photo_form(form_id){
	
	title_box = document.getElementById('title_box_'+form_id).value;
	original_title_box = document.getElementById('original_title_box_'+form_id).value;
	category_box = document.getElementById('category_box_'+form_id).value
	
	// hide submit button
	document.getElementById('button_'+form_id).style.display='none'; 
	
	run_script('<?php echo $gallery_url;?>/query_photo_edit.php?key=<?php echo md5($_SESSION['session_secret'].$category_title);?>&form_id='+form_id+'&original_category_box=<?php echo addslashes($category_title);?>&category_box='+category_box+'&original_title_box='+original_title_box+'&title_box='+title_box, 'submited_edit_photo_form(run_script_return)');
	
	// do not actually submit the form, we sent the data above
	return false;
}

function submited_edit_photo_form(run_script_return){
	
	// create an object with the data from the photo edit script
	return_object = JSON.parse(run_script_return);
	
	// show the info div
	document.getElementById('form_info_div_'+return_object.form_id).style.display = "";
	
	// if our photo edit script returned an error, then show it in an alert
	if(return_object.status != "ok"){
		// print the error in the info div
		document.getElementById('form_info_div_'+return_object.form_id).innerHTML = '<span class="red_text"><strong>'+return_object.status_message+'</strong></span>';
		document.getElementById('title_box_'+return_object.form_id).focus();
	}
	
	if(return_object.status == "ok"){
		
		// print a "data saved" message under form fields
		document.getElementById('form_info_div_'+return_object.form_id).innerHTML = '<span class="green_text"><strong>'+return_object.status_message+'</strong></span>';
		
		// write the newly received title box on this form's title_box and original_title_box
		// because this data might be different than what we sent (it filtered special characters)
		document.getElementById('original_title_box_'+return_object.form_id).value=return_object.title_box;
		document.getElementById('title_box_'+return_object.form_id).value=return_object.title_box;
		
		// show the div that shows info like "data saved"
		document.getElementById('form_info_div_'+return_object.form_id).style.display='';
		
		// if saved category is different than original category, then hide this photo from the list
		if(category_box != '<?php echo addslashes($category_title);?>'){
			document.getElementById('form_'+return_object.form_id).style.display = 'none';
		}
		
		document.getElementById('title_box_'+return_object.form_id).focus();
		
	}
}

function make_cover(form_id){
	
	<?php $photo_counter = 0; // make all buttons text "cover", then only one will say "made cover" ?>
	<?php foreach($categories_array[$category_title] as $photo_file){ ?>
	document.getElementById('make_cover_button_<?php echo $photo_counter;?>').innerText = 'cover';
	<?php $photo_counter++;?>
	<?php } ?>
	
	// make cover from current title in that form !!!
	title_box = document.getElementById('title_box_'+form_id).value;
	
	run_script('<?php echo $gallery_url;?>/query_photo_edit.php?key=<?php echo md5($_SESSION['session_secret'].$category_title);?>&photo_to_make_cover='+title_box+'&category_box=<?php echo addslashes($category_title);?>', 'document.getElementById(\'make_cover_button_'+form_id+'\').innerText = \'made cover\'');

	
}

function confirm_delete_photo(form_id){
	
	title_box = document.getElementById('title_box_'+form_id).value;
	
	if(confirm('Delete '+title_box+' ?')){
		
		run_script('<?php echo $gallery_url;?>/query_photo_edit.php?key=<?php echo md5("dlt".$_SESSION['session_secret']."img");?>&photo_to_delete='+title_box+'&category_box=<?php echo addslashes($category_title);?>', 'document.getElementById(\'form_'+form_id+'\').style.display = \'none\'');
		
	}
	
	
	
}

--></script>


<h1>Edit photos in <?php echo htmlentities($category_display_title , ENT_QUOTES, "UTF-8");?></h1>

<p class="breadcrumb"><a href="/">home</a> &gt; <a href="<?php echo $gallery_url;?>">gallery</a> &gt; <a href="<?php echo $gallery_url;?>/<?php echo rawurlencode($category_title);?>"><?php echo htmlentities($category_display_title , ENT_QUOTES, "UTF-8");?></a> &gt; edit photos</p>




<?php if(count($categories_array[$category_title])<=0){?>
<p>There are no photos under <strong><?php echo htmlentities($category_display_title , ENT_QUOTES, "UTF-8");?></strong>.</p>
<?php } ?>



<?php if(count($categories_array[$category_title])>0){?>

<p>On this page you can edit the title and category of each photo.</p>

<p>Type a new photo name and press Enter or click &quot;Submit&quot;.</p>

    <?php $photo_counter = 0;?>
	<?php foreach($categories_array[$category_title] as $photo_file){ ?>
    
    <form id="form_<?php echo $photo_counter;?>" name="form_<?php echo $photo_counter;?>" method="get" action="" style="display:block; padding:10px; width:100%; height:<?php echo $settings_thumbnail_height;?>px; margin:0px; margin-left:-10px; border-top:1px solid #CCCCCC;  background-image:url('<?php echo $gallery_url."/files/".rawurlencode($category_title)."/".rawurlencode($photo_file)."_thumb.jpg" ;?>'); background-repeat:no-repeat; background-position:10px 10px;" onsubmit="return submit_edit_photo_form(<?php echo $photo_counter;?>);">
    
    	<span style="float:right; display:inline-block; width:150px; text-align:right;">
                        
        	<a href="javascript:void(0);" id="make_cover_button_<?php echo $photo_counter;?>" onmouseup="make_cover(<?php echo $photo_counter;?>);" class="liquid_button" title="make this photo album cover">cover</a>
        	
			<a href="javascript:void(0);" onmouseup="confirm_delete_photo(<?php echo $photo_counter;?>);" class="liquid_button_red" title="delete this photo">delete</a>
        
    	</span>
        
		<span style="display:inline-block; margin-left:<?php echo $settings_thumbnail_width+10;?>px;">
			

			<input name="title_box" id="title_box_<?php echo $photo_counter;?>" type="text" style="width:300px; padding:2px;" value="<?php echo htmlentities($photo_file, ENT_QUOTES, "UTF-8");?>" onchange="change_edit_photo_form(<?php echo $photo_counter;?>);" onkeypress="change_edit_photo_form(<?php echo $photo_counter;?>);" placeholder="photo title" required />
            
            <input name="original_title_box" id="original_title_box_<?php echo $photo_counter;?>"  type="hidden" value="<?php echo htmlentities($photo_file, ENT_QUOTES, "UTF-8");?>" />
            
            <br />
            
            <select name="category_box" id="category_box_<?php echo $photo_counter;?>" style="margin-top:10px; min-width:300px; padding:2px; height:24px; margin-bottom:10px;" onchange="change_edit_photo_form(<?php echo $photo_counter;?>);">
            	<?php foreach($categories_array as $loop_category_title=>$loop_category_images){?>
                <option value="<?php echo htmlentities($loop_category_title, ENT_QUOTES, "UTF-8");?>" <?php if($loop_category_title==$category_title){?> selected="selected" style="background-color:#DDEEFF;" <?php } ?> ><?php echo htmlentities($loop_category_title, ENT_QUOTES, "UTF-8");?></option>
                <?php } ?>
			</select>
            
			<br />
			
            <input type="submit" name="Submit" id="button_<?php echo $photo_counter;?>" value="Save" style="float:left; margin-right:10px; display:none;" class="button_110" />
            
            <span id="form_info_div_<?php echo $photo_counter;?>" style="float:left;"></span>
            
		</span>
      
      
    </form>
    
  

    
    <?php $photo_counter++;?>
    <?php } ?>

<?php } // if there are photos ?>





<?php include("footer.php");?>