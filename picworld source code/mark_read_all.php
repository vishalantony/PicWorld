<?php
	session_start();
		
	ini_set('display_errors', true);
	error_reporting(E_ALL ^ E_NOTICE);
	
	if(!isset($_SESSION['logged']) || $_SESSION['logged'] != 1) {
		header("Location: login.php?not_logged=1");
	}
	
	$title = "photoShare";
	$mainHeading = "PhotoShare";
	
	include('includes/config.inc.php');
	
	$username = $_SESSION['username'];
	
	$query = 'SELECT * FROM notifications WHERE emailid="'.mysqli_real_escape_string($db, $username).'" and read_state=false;';
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("MESSAGE 1: Internal ERROR. Try later.");
	}
	
	while($res = mysqli_fetch_assoc($result)) {
		$query = 'UPDATE notifications SET read_state=true WHERE notif_id='.$res['notif_id'].';';
		$result5 = mysqli_query($db, $query);
		if(!$result5) {
			die("MESSAGE 1: Internal error. try later.");
		}
	}
	
	mysqli_close($db);
	header("Location: notifications.php");
	
?>
