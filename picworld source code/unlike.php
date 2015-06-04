<?php
	session_start();
	if(!isset($_SESSION['logged']) || $_SESSION['logged'] != 1) {
		header("Location: login.php?not_logged=1");
		die();
	}
	
	$curuser = $_SESSION['username'];
	if(!isset($_GET['pid'])) {
		die('What am i supposed to like?');		
	}
	
	include('includes/config.inc.php');
	$pid = $_GET['pid'];
	
	$query = 'SELECT * FROM photos WHERE pid = '.$pid.';';
	$result = mysqli_query($db, $query);
	if(!$result) {
		//header("Location: profile.php");
		die("Some error while retrieving photo.");
	}
	if(mysqli_num_rows($result) == 0) {
		//header("Location: albums.php");
		die("No such photo. Are you kidding me?");
	}
	
	$row1 = mysqli_fetch_assoc($result);
	
	// now row1 contains all the information about the photo.
	
	// Determining the type of user.
	
	$type = 'public';
	if($row1['owner'] == $curuser) {
		$type = 'private';
	}
	$query = 'SELECT * FROM friends WHERE 
	(acceptedby="'.mysqli_real_escape_string($db, $curuser).'" 	and sentby="'.mysqli_real_escape_string($db, $row1['owner']).'" ) 
	OR 
	(acceptedby="'.mysqli_real_escape_string($db, $row1['owner']).'" and sentby="'.mysqli_real_escape_string($db, $curuser).'");';
	$result = mysqli_query($db, $query);
	if(!$result) {
		//header("Location: profile.php");
		die("Some error happened while finding out who you are.");
	}
	if(mysqli_num_rows($result) > 0) {
		$type = 'friends';
	}
	
	if($type == "public" && $row1['type'] != "public") {
		die("You can't unlike this photo");
	}
	if($type == "friends" && ($row1['type'] != "public" && $row1['type'] != "friends")) {
		die("You can't unlike this photo");
	}
	
	// check if i have already liked this
	$query = 'SELECT * FROM likes WHERE pid='.$pid.' AND liked_by="'.$curuser.'";';
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("2. Internal error. Please try again later.");
	}
	if(mysqli_num_rows($result) == 0) {
		header('Location: show_image.php?photo_num='.$pid);
		die();
	}
	
	$query = 'UPDATE photos SET no_likes=no_likes-1 WHERE pid = '.mysqli_real_escape_string($db, $pid).';';
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("Internal error. Please try again later.");
	}
	$query = 'DELETE FROM likes WHERE pid='.mysqli_real_escape_string($db, $pid).' AND liked_by="'.mysqli_real_escape_string($db, $curuser).'";';
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("Internal error. Please try again later.");
	}
	mysqli_close($db); 
	header('Location: show_image.php?photo_num='.$pid);
?>	
	
