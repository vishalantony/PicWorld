<?php
	session_start();
	ini_set('display_errors', true);
	error_reporting(E_ALL ^ E_NOTICE);
	
	if(isset($_SESSION['logged']) && $_SESSION['logged'] == 1) {
		header("Location: profile.php");
		die();
	}
	
	$title = "photoShare";
	$mainHeading = "PhotoShare";
	
	if(isset($_POST['register']) && $_POST['register'] == 'Register') {
		$missing = array();
		
		$fname = (isset($_POST['fname']))?trim($_POST['fname']):"";
		$lname = (isset($_POST['lname']))?trim($_POST['lname']):"NULL";
		$emailbox = (isset($_POST['emailbox']))?trim($_POST['emailbox']):"";
		$regpasswd = (isset($_POST['regpasswd']))?$_POST['regpasswd']:"";
		$confregpasswd = (isset($_POST['confregpasswd']))?$_POST['confregpasswd']:"";
		$regbirthday = (isset($_POST['regbirthday']))?trim($_POST['regbirthday']):"";
		$regsex = (isset($_POST['regsex']))?trim($_POST['regsex']):"";
		
		if(empty($fname)) array_push($missing, "fname");
		if(empty($emailbox)) array_push($missing, "emailbox");
		if(empty($regpasswd)) array_push($missing, "regpasswd");
		if(empty($confregpasswd)) array_push($missing, "confregpasswd");
		if(empty($regbirthday)) array_push($missing, "regbirthday");
		if(empty($regsex)) array_push($missing, "regsex");
		
		
		
		if(!empty($missing)) {
			$missing_values = implode($missing, "=1&");
			header("Location: index.php?$missing_values=1");
			die();
		}
		
		if(strcmp($regpasswd, $confregpasswd) != 0) {
			header("Location: index.php?passwd_mismatch=1");
			die();
		}
			
		include('includes/config.inc.php');
			
			
		//check whether the email is already registered.
			
		$query = 'SELECT * FROM user WHERE emailid = "'.mysqli_real_escape_string($db, $emailbox).'";';
		$result = mysqli_query($db, $query);
		
		if(!$result) {
			die("PicWorld faced some internal error. Please try after sometime.");
		}
		
		if(mysqli_num_rows($result) > 0) {
			header("Location: index.php?existing_user=1");
			die();
		}
		mysqli_free_result($result);
			
		$datetocheck = explode("-", $regbirthday);
		if(!checkdate($datetocheck[1], $datetocheck[2], $datetocheck[0])) {
			header("Location: index.php?invalid_date=1");
			die();
		}
				
		$query = 'INSERT INTO user(fname, lname, emailid, user_passwd, bday, sex) VALUES("'.mysqli_real_escape_string($db, $fname).'", ';
		$query = $query.'"'.mysqli_real_escape_string($db, $lname).'", ';
		$query = $query.'"'.mysqli_real_escape_string($db, $emailbox).'",';
		$query = $query.'PASSWORD("'.mysqli_real_escape_string($db, $regpasswd).'"),';
		$query = $query.'"'.mysqli_real_escape_string($db, $regbirthday).'",';
		$query = $query.'"'.mysqli_real_escape_string($db, $regsex[0]).'");';
			
		$result = mysqli_query($db, $query);
		if(!$result) {
			die("PicWorld faced some internal error. Please try after sometime.");
		}
		
		// SET PROFILE PIC
		if($regsex[0] == 'M')
			$default_pic = 'pictures/profile_pictures/default_dp_M.jpg';
		else if($regsex[0] == 'F') {
			$default_pic = 'pictures/profile_pictures/default_dp_F.png';
		}
		$query = 'INSERT INTO profile_pics(owner, pro_pic, set_date) VALUES ("'.mysqli_real_escape_string($db, $emailbox).'", "'.mysqli_real_escape_string($db, $default_pic).'", curdate());';
		$result = mysqli_query($db, $query);
		if(!$result) {
			die("PicWorld faced some internal error. Please try after sometime.");
		}
		
		$query = 'INSERT INTO album(date_of_creat, name, owner)  VALUES("'.mysqli_real_escape_string($db, date('Y-m-d')).'", "Profile Pictures", "'.mysqli_real_escape_string($db, $emailbox).'");';
		$result = mysqli_query($db, $query);
		if(!$result) {
			die("PicWorld faced some internal error. Please try after sometime.");
		}
		
		mysqli_close($db);
		header("Location: login.php?reg_success=1");
	}
	else {
		header("Location: index.php");
	}		
?>
