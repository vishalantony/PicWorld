<?php
	session_start();
		
	ini_set('display_errors', true);
	error_reporting(E_ALL ^ E_NOTICE);
	
		if(!isset($_SESSION['logged']) || $_SESSION['logged'] != 1) {
			header("Location: login.php?not_logged=1");
			die();
		}
		
		include('includes/config.inc.php');
		$title = "photoShare";
		$mainHeading = "PhotoShare";
	
		$username = $_SESSION['username'];
		$query = 'SELECT * FROM user WHERE emailid="'.$username.'";';
		$result = mysqli_query($db, $query);
		if(mysqli_connect_errno()) {
			die("PicWorld faced some internal error. Please try after sometime.");
		}
		if(mysqli_num_rows($result) == 0) {
			// some error occurred.
			session_destroy();
			header("Location: index.php");
			die();
		}
		mysqli_free_result($result);
		$query = 'DELETE FROM user WHERE emailid="'.$username.'";';
		$result = mysqli_query($db, $query);
		if(!$result) {
			mysqli_close($db);
			header("Location: profile.php?delete_error=1");
			die();
		}
		else {
			mysqli_close($db);
			session_destroy();
			header("Location: index.php");
		}
?>
