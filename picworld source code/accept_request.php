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
	
	if(!isset($_GET['uid']) || !isset($_GET['notif_id'])) {
		header("Location: notifications.php");
		die();
	}
	
	$to_be_friend = trim($_GET['uid']);
	$notif_id = trim($_GET['notif_id']);
	if(empty($to_be_friend) || empty($notif_id)) {
		header("Location: notifications.php");
		die();
	}

	$query = 'SELECT * FROM user WHERE user_id='.$to_be_friend.';';
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("MESSAGE 1: Internal error.");
	}
	$row = mysqli_fetch_assoc($result);
	
	$query = 'SELECT * FROM pending WHERE sent_by="'.$row['emailid'].'" AND sent_to="'.$username.'";';
	$result1 = mysqli_query($db, $query);
	if(!$result1) {
		die("MESSAGE 2: Internal error.");
	}
	if(mysqli_num_rows($result1) == 0) {
		header("Location: notifications.php");
		die();
	}
	
	$row1 = mysqli_fetch_assoc($result1);
	
	$query = 'INSERT INTO friends VALUES("'.$row1['sent_by'].'", "'.$row1['sent_to'].'", "'.$row1['requested_date'].'", NOW());';
	$result2 = mysqli_query($db, $query);
	if(!$result2) {
		die("MESSAGE 3: Internal error.");
	}
	
	$query = 'DELETE FROM pending WHERE sent_by = "'.$row1['sent_by'].'" AND sent_to = "'.$row1['sent_to'].'";';
	$result3 = mysqli_query($db, $query);
	if(!$result3) {
		die("MESSAGE 4: Internal error.");
	}
	
	
	$notif = 'You and <a id="activity_content" href="viewprofile.php?uid='.$to_be_friend.'">'.$row['fname'].' '.$row['lname'].'</a> are now friends.';
	$query = 'UPDATE notifications SET notification="'.mysqli_real_escape_string($db, $notif).'"
	where notif_id='.$notif_id.';';
	$result4 = mysqli_query($db, $query);
	if(!$result4) {
		die("Message 5: Internal error.");
	}
	
	
	$query = 'SELECT * FROM user WHERE emailid="'.$username.'";';
	$result6 = mysqli_query($db, $query);
	if(!$result6) {
		die("MESSAGE 6: Internal error.");
	}
	$row2 = mysqli_fetch_assoc($result6);
	
	//notify the user
	$notif = '<a id="activity_content" href="viewprofile.php?uid='.$row2['user_id'].'">'.$row2['fname'].' '.$row2['lname'].'</a> accepted your friend request.';
	$query = 'INSERT INTO notifications(emailid, notification, notif_date) VALUES(
	"'.$row1['sent_by'].'", 
	"'.mysqli_real_escape_string($db, $notif).'",
	NOW()
	);';
	$result5 = mysqli_query($db, $query);
	if(!$result5) {
		die("Message 7: Internal error.");
	}
	
	
	
	//add activity
		$activity = '<a id="activity_content" href="viewprofile.php?uid='.$row['user_id'].'">'.$row['fname'].' '.$row['lname'].'</a> 
		and <a id="activity_content" href="viewprofile.php?uid='.$row2['user_id'].'">'.$row2['fname'].' '.$row2['lname'].'</a> are now friends.';
		
		$query = 'INSERT INTO activities(emailid, activity, activity_date) VALUES("'.$row['emailid'].'", 
		"'.mysqli_real_escape_string($db, $activity).'", NOW());';
		$result6 = mysqli_query($db, $query);
		if(!$result6) {
			die("MESSAGE 98. Internal ERROR. Try again later.");
		}
		
		$query = 'INSERT INTO activities(emailid, activity, activity_date) VALUES("'.$row2['emailid'].'", 
		"'.mysqli_real_escape_string($db, $activity).'", NOW());';
		$result6 = mysqli_query($db, $query);
		if(!$result6) {
			die("MESSAGE 98. Internal ERROR. Try again later.");
		}
		//added activity
	header("Location: notifications.php");
	mysqli_close($db);
?>
