<?php
	session_start();
	if(isset($_SESSION['logged']) && $_SESSION['logged'] == 1) {
		session_destroy();
		header("Location: index.php");
	}
	else {
		header("Location: login.php");
	}
	
?>
