<?php
	session_start();
		
	ini_set('display_errors', true);
	error_reporting(E_ALL ^ E_NOTICE);
	
	if(!isset($_SESSION['logged']) || $_SESSION['logged'] != 1) {
		header("Location: login.php?not_logged=1");
		die();
	}
	
	include('includes/config.inc.php');
	
	$title = "PicWorld";
	$mainHeading = "PhotoShare";
	$username = $_SESSION['username'];
	
	if(!isset($_GET['uid'])) {
		header("Location: profile.php");
		die();
	}
	
	$to_be_friend = trim($_GET['uid']);
	if(empty($to_be_friend)) {
		header("Location: profile.php");
		die();
	}
	
	$query = 'SELECT * FROM user WHERE user_id = '.$to_be_friend.';';
	$result = mysqli_query($db,$query);
	if(!$result) {
		die("Internal error 34");
	}
	$friend = mysqli_fetch_assoc($result);
	
	//check if friend request pending.
	$query = 'SELECT * FROM pending WHERE (sent_by="'.$username.'" and sent_to="'.$friend['emailid'].'")
	or (sent_to="'.$username.'" and sent_by="'.$friend['emailid'].'");';
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("Internal error 35");
	}
	
	// check if already friend
	$query = 'SELECT * FROM friends WHERE (sentby="'.$username.'" and acceptedby="'.$friend['emailid'].'")
	or (acceptedby="'.$username.'" and sentby="'.$friend['emailid'].'");';
	$result1 = mysqli_query($db, $query);
	if(!$result1) {
		die("Internal error 38");
	}
	
	
	if(mysqli_num_rows($result) > 0 || mysqli_num_rows($result1) > 0) {
		header("Location: viewprofile.php?uid=".$to_be_friend);
		die();
	}
	
	
	$query = 'INSERT INTO pending VALUES("'.mysqli_real_escape_string($db, $username).'",
	 "'.mysqli_real_escape_string($db, $friend['emailid']).'", NOW());';
	$result2 = mysqli_query($db, $query);
	if(!$result2) {
		die("Internal error 37");
	}
	
	// notify the person.
	$query = 'SELECT * FROM user WHERE emailid = "'.mysqli_real_escape_string($db, $username).'";';
	$result3 = mysqli_query($db,$query);
	if(!$result3) {
		die("Internal error 36");
	}
	$myself = mysqli_fetch_assoc($result3);
	
	
	
	$query = 'INSERT INTO notifications(emailid, notif_date) VALUES("'.mysqli_real_escape_string($db, $friend['emailid']).'", NOW());';
	//echo $query;
	//echo '<br>';
	//notification: mysqli_real_escape_string($db, $notif)
	
	$result4 = mysqli_query($db, $query);
	if(!$result4) {
		die("Internal error 39");
	}
	
	$last_id = mysqli_insert_id($db);
	
	$notif = '<a id="activity_content" href="viewprofile.php?uid='.$myself['user_id'].'">'.$myself['fname'].' '.$myself['lname'].'</a> 
	sent you a friend request. Click here 
	to <a id="activity_content" href="accept_request.php?uid='.$myself['user_id'].'&notif_id='.$last_id.'">accept.</a>';
	
	//echo $notif;
	//echo '<br>';
	
	$query = 'UPDATE notifications SET notification="'.mysqli_real_escape_string($db, $notif).'"
	where notif_id='.$last_id.';';
	
	//echo $query;
	//echo '<br>';
	
	$result5 = mysqli_query($db, $query);
	if(!$result5) {
		die("Internal error 40");
	}
	
	
	// notified.
	mysqli_close($db);
	header("Location: viewprofile.php?uid=".$to_be_friend);
?>
