<?php
	//Destroy session for logout
	session_start();
	session_destroy();
	header("location: login.php")
?>