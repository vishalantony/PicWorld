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
		die("You can't comment on this photo");
	}
	if($type == "friends" && ($row1['type'] != "public" && $row1['type'] != "friends")) {
		die("You can't comment on this photo");
	}
	
	if(!isset($_POST['comment']) || $_POST['comment'] != 'Comment') {
		mysqli_close($db); 
		mysqli_free_result($result);
		header('Location: show_image.php?photo_num='.$pid);	
		die();
	}
	
	$comment = (isset($_POST['p_comment']))?trim($_POST['p_comment']):"";
	
	if(empty($comment)) {
		mysqli_close($db); 
		mysqli_free_result($result);
		header('Location: show_image.php?photo_num='.$pid);	
		die();
	}
	
	
	$query = 'INSERT INTO comments(pid, comment, comment_by, date_of_comment) VALUES('.$pid.', "'.mysqli_real_escape_string($db, $comment).'",
	"'.$curuser.'", NOW());';
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("5. Some internal error occured. Please try later.");
	}
	
	
	//retrieve user info
	$query = 'SELECT * FROM user WHERE emailid = "'.$curuser.'";';
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("MESSAGE 99. Internal ERROR. Try later.");
	}
	$res = mysqli_fetch_assoc($result);
	
	//add activity
		$activity = '<a id="activity_content" href="viewprofile.php?uid='.$res['user_id'].'">'.$res['fname'].' '.$res['lname'].'</a> 
		commented on a <a id="activity_content" href="show_image.php?photo_num='.$pid.'">photo</a>.';
		$query = 'INSERT INTO activities(emailid, activity, activity_date) VALUES("'.$res['emailid'].'", 
		"'.mysqli_real_escape_string($db, $activity).'", NOW());';
		$result = mysqli_query($db, $query);
		if(!$result) {
			die("MESSAGE 98. Internal ERROR. Try again later.");
		}
		//added activity
		
		
	//notify the owner of photo
	if($curuser != $row1['owner']) {
	$notif = '<a id="activity_content" href="viewprofile.php?uid='.$res['user_id'].'">'.$res['fname'].' '.$res['lname'].'</a> 
		commented on your <a id="activity_content" href="show_image.php?photo_num='.$pid.'">photo</a>.';
	$query = 'INSERT INTO notifications(emailid, notification, notif_date) VALUES(
	"'.$row1['owner'].'", 
	"'.mysqli_real_escape_string($db, $notif).'",
	NOW()
	);';
	$result5 = mysqli_query($db, $query);
	if(!$result5) {
		die("Message 7: Internal error.");
	}
	}
	// end of notify
	
	
	
	// notify others who commented before
	$who_all_to_not_notify = array($res['emailid'], $row1['owner']);
	$query = 'SELECT DISTINCT(comment_by) as comment_by FROM comments WHERE pid = '.$pid.';';
	$resultx = mysqli_query($db, $query);
	if(!$resultx) {
		die("Internal error. Try later.");
	}
	while($rowx = mysqli_fetch_assoc($resultx)) {
		if(!in_array($rowx['comment_by'], $who_all_to_not_notify)) {
			$query = 'SELECT * FROM user WHERE emailid="'.$rowx['comment_by'].'";';
			$result3 = mysqli_query($db, $query);
			if(!$result3) {
				die("34. Internal error. Try later.");
			}
			$rowy = mysqli_fetch_assoc($result3);
			
			$notif = '<a id="activity_content" href="viewprofile.php?uid='.$res['user_id'].'">'.$res['fname'].' '.$res['lname'].'</a> 
			commented on a <a id="activity_content" href="show_image.php?photo_num='.$pid.'">photo</a> you commented on.';
			
			$query = 'INSERT INTO notifications(emailid, notification, notif_date) VALUES(
			"'.$rowy['emailid'].'", 
			"'.mysqli_real_escape_string($db, $notif).'",
			NOW()
			);';
			$result7 = mysqli_query($db, $query);
			if(!$result7) {
				die("Message 10: Internal error.");
			}
		}
	}
	
	
	
	mysqli_close($db);
	header('Location: show_image.php?photo_num='.$pid);	
	
?>
