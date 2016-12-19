<?php

include("system_header.php");



?>
<?php include("header.php");?>

<h1>Photo Gallery</h1>

<p class="breadcrumb"><a href="/">home</a> &gt; gallery</p>

<?php if(count($categories_array)<=0){?>
<p>There are no photo categories, create one or more categories before uploading photos</p>
<?php } ?>


<?php if(count($categories_array)>0){?>
	
    	
<div>

	<?php foreach($categories_array as $photo_category=>$photos_array){?>
    
    	<?php 
		$category_thumbnail = $gallery_url."/layout/pixel.gif";
		if(file_exists('files/'.$photo_category.'/thumbnail.jpg')){
			$category_thumbnail = $gallery_url.'/'.rawurlencode($photo_category).'.jpg';
		}
		
		$category_url = $gallery_url.'/'.rawurlencode($photo_category);
		?>
        
  		<span class="category_thumbnail_span" style="width:<?php echo $settings_thumbnail_width;?>px; height:<?php echo $settings_thumbnail_height+20;?>px;">
        <a class="category_thumbnail_image" href="<?php echo $category_url;?>" style="width:<?php echo $settings_thumbnail_width;?>px; height:<?php echo $settings_thumbnail_height;?>px; background-image:url('<?php echo $gallery_url;?>/layout/lens_48x48.png');" title="<?php echo htmlentities(ucwords(str_replace('-', ' ', $photo_category)), ENT_QUOTES, "UTF-8");?>">
        <img src="<?php echo $category_thumbnail;?>" width="<?php echo $settings_thumbnail_width;?>" height="<?php echo $settings_thumbnail_height;?>" alt="<?php echo htmlentities(ucwords(str_replace('-', ' ', $photo_category)), ENT_QUOTES, "UTF-8");?>" />
        </a>
        <a class="category_thumbnail_title" href="<?php echo $category_url;?>" title="<?php echo htmlentities(ucwords(str_replace('-', ' ', $photo_category)), ENT_QUOTES, "UTF-8");?>">
        <?php echo htmlentities(str_replace('-',' ', truncate_by_letters($photo_category, 16, '..')), ENT_QUOTES, "UTF-8");?> (<?php echo count($photos_array);?>)
        </a>
        </span>
    

	<?php } ?>

</div><!-- end of categories container -->

<?php } ?>


<?php include("footer.php");?>
