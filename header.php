<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="generator" content="http://www.freephpgallery.com" />

<title><?php if(isset($page_title)){echo $page_title;} else {echo $settings_page_title;}?></title>

<meta name="description" content="<?php if(isset($page_description)){echo $page_description;} else {echo $settings_page_description;}?>"/>

<!-- <link href="<?php echo $gallery_url;?>/style.css" rel="stylesheet" type="text/css" /> -->

<link href="<?php echo $gallery_url;?>/style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="<?php echo $gallery_url;?>/system.js"></script>


<meta name="viewport" content="width=780, initial-scale=1.0, maximum-scale=1.0" />

</head>

<body>

<div class="gallery_wrapper">

		<?php if(isset($_REQUEST['message'])){?>
        	<?php
			$url_message_type = $_REQUEST['message_type'];
			if($url_message_type!="success" and $url_message_type!="error"){
				$url_message_type = "";
			}
			//
			$url_message = urldecode($_GET['message']);
			$url_message = htmlentities($_GET['message'],ENT_QUOTES,"UTF-8");
			?>
            <div class="message_<?php echo $url_message_type;?>">
            <?php echo $url_message;?>
            </div>
		<?php }?>
        
