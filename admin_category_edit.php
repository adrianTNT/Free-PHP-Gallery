<?php

include("system_header.php");

admin_only();

$old_category_title = trim(strip_tags($_GET['category_title']));

// category doesn't exist
if(!is_dir("files/".$old_category_title)){
	include("404.php");
	exit;
}

if(isset($_POST['category_title_box']) and $_POST['category_title_box']!=''){
	
	$new_category_title = string_to_file_name($_POST['category_title_box']);
	
	if(is_dir('files/'.$new_category_title)){
		header("Location: ".$gallery_url."/admin-categories?message=cannot rename ".$old_category_title." to ".$new_category_title." because ".$new_category_title." already exists&message_type=error");
		exit;	
	}
	
	rename('files/'.$old_category_title, 'files/'.$new_category_title);
		
	header("Location: ".$gallery_url."/admin-categories?message=category renamed&message_type=success");
	exit;
	
}
?>
<?php include("header.php");?>
<h1>Manage categories</h1>
<p class="breadcrumb"><a href="/">home</a> &gt; <a href="<?php echo $gallery_url;?>">gallery</a> &gt; <a href="<?php echo $gallery_url;?>/admin">admin</a> &gt; <a href="<?php echo $gallery_url;?>/admin-categories">categories</a> &gt; edit</p>

<form id="form1" name="form1" method="post" action="" style="margin:0px;">
        
    <p style="margin-top:0px;">Your URLs will look nicer if you enter category names lowercase and without spaces, like <em><strong>vacation-photos</strong></em></p>
    
    
	<input name="category_title_box" type="text" id="category_title_box" size="20" value="<?php echo htmlentities($old_category_title, ENT_QUOTES, "UTF-8");?>" placeholder="category title" required="required" />
	<br />

	<input name="Submit" type="submit" class="button_190" id="button" value="Save" style="margin-top:10px;" />
</form>

<script type="text/javascript">
document.getElementById('category_title_box').focus();
</script>

<?php include("footer.php");?>
