<?php

include("system_header.php");

admin_only();


?>
<?php include("header.php");?>
<h1>Admin </h1>
<p class="breadcrumb"><a href="/">home</a> &gt; <a href="<?php echo $gallery_url;?>">gallery</a> &gt; admin</p>

<p>
<strong><a href="<?php echo $gallery_url;?>/admin-categories">Manage photo categories (<?php echo count($categories_array);?>)</a></strong><br />
Create new categories, delete or rename existent ones, etc.
</p>

<p>
<strong><a href="javascript:void(0);" onmouseup="if(confirm('Are you sure do you want to run this script? It can take a few minutes depending on the amount of photos and speed of your server.')){window.location='<?php echo $gallery_url;?>/admin-regenerate-images';}">Regenerate images</a></strong> <br />
Use this feature when you want to generate thumbnails again based on the original file of each image. Each image is stored on server in 3 files (original file.jpg, file_small.jpg and file_thumbnail.jpg).<br />
You can also run this script if you changed the width/height of your images inside settings.php<br />

<span style="color:#EA0000;">If the script never completes or if you are getting a server error, then try increasing PHP script run time limitation or increase PHP memory limitation.</span>
</p>

<?php include("footer.php");?>
