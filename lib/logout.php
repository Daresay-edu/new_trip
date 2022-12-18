<?php
header("Cache-Control: no-cache, must-revalidate");
	session_start();
	unset($_SESSION['username']);
	unset($_SESSION['role']);
	session_destroy();
    //header('Location: login.php');
	return json_encode("1");
	
?>