<?php

include("system_header.php");

	
$_SESSION['session_admin'] = '';
$_SESSION['session_return_url'] = '';
$_SESSION['session_secret'] = '';
		
header("Location: ".$gallery_url."?message=you are logged out&message_type=success");
exit;

?>