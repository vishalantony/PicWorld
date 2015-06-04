<?php
	session_start();
	
	if(isset($_SESSION['logged']) && $_SESSION['logged']==1) {
		$username = $_SESSION['username'];
		include('includes/config.inc.php');
		
		$query = 'SELECT sex FROM user WHERE emailid="'.mysqli_real_escape_string($db, $username).'";';
		$result = mysqli_query($db, $query);
		if(!$result) {
			die("PicWorld faced some internal error. Please try after sometime.");
		}
		$res = mysqli_fetch_assoc($result);
		mysqli_free_result($result);
		$sex = $res['sex'];
		if($sex == 'M')
			$default_pic = 'pictures/profile_pictures/default_dp_M.jpg';
		else if($sex == 'F') {
			$default_pic = 'pictures/profile_pictures/default_dp_F.png';
		}
		
		//delete previous profile pic
		$query = 'SELECT pro_pic FROM profile_pics WHERE owner="'.mysqli_real_escape_string($db, $username).'";';
		$result = mysqli_query($db, $query);
		if(mysqli_connect_errno()) {
			die("PicWorld faced some internal error. Please try after sometime.");
		}
		$res = mysqli_fetch_assoc($result);
		mysqli_free_result($result);
		if(strcmp($res['pro_pic'], $default_pic) != 0) {
			$del = unlink($res['pro_pic']);
			if(!$del) {
				die("PicWorld faced some internal error. Please try after sometime.");
			}
		}
		
		
		$query = 'UPDATE profile_pics SET pro_pic="'.mysqli_real_escape_string($db, $default_pic).'", set_date = curdate(), height = 200, width = 200 WHERE owner="'.mysqli_real_escape_string($db, $username).'";';
		mysqli_query($db, $query);
		if(mysqli_connect_errno()) {
			die("PicWorld faced some internal error. Please try after sometime.");
		}
		mysqli_close($db);
		header("Location: profile.php");
	}
	else {
		header("Location: index.php");
		die();
	}
?>
