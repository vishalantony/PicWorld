<?php
	session_start();
	if(!isset($_SESSION['logged']) || $_SESSION['logged'] != 1) {
		header("Location: login.php");
		die();
	}
	if(!isset($_GET['uid'])) {
		header("Location: friends.php");
		die();
	}
	
	include('includes/config.inc.php');
	$title = "PicWorld";
	$mainHeading = "PhotoShare";
	
	$uid = $_GET['uid'];
	$username = $_SESSION['username'];
	
	$query = 'SELECT * FROM user WHERE user_id='.$uid.';';
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("MESSAGE 4: ERROR happened");
	}
	
	if(mysqli_num_rows($result) == 0) {
		header("Location: profile.php");
		die();
	}
	$res = mysqli_fetch_assoc($result);
	
	$query = 'SELECT * from friends where (acceptedby="'.$username.'" and sentby="'.$res['emailid'].'") 
	or (acceptedby="'.$res['emailid'].'" and sentby="'.$username.'");';
	
	//echo $query;
	//die();
	
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("MESSAGE 4: ERROR happened");
	}
	
	if(mysqli_num_rows($result) == 0) {
		header("Location: friends.php");
		die();
	}
	
	$query = 'DELETE from friends where (acceptedby="'.$username.'" and sentby="'.$res['emailid'].'") 
	or (acceptedby="'.$res['emailid'].'" and sentby="'.$username.'");';
	//echo $query;
	//die();
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("MESSAGE 4: ERROR happened");
	}
	header("Location: viewprofile.php?uid=".$uid);
?>
