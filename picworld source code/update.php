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
	
	// updates information here
	
	if(isset($_POST['update']) && $_POST['update'] == 'Update') {
		
		//print_r($_POST);die();
		
		$username = $_SESSION['username'];
		$query = 'SELECT * FROM user WHERE emailid = "'.mysqli_real_escape_string($db, $username).'";';
		$result = mysqli_query($db, $query);
		if(!$result) {
			die("MESSAGE 1: PicWorld faced some internal error. Please try after sometime.");
		}
		if(mysqli_num_rows($result) == 0) {
			// some error happened.
			session_destroy();
			header("Location: index.php");
		}
		$res = mysqli_fetch_assoc($result);
		mysqli_free_result($result);
		
		
		
		$ufname = (isset($_POST['ufname']))?trim($_POST['ufname']):"";
		$ulname = (isset($_POST['ulname']))?trim($_POST['ulname']):"NULL";
		$uemail = (isset($_POST['uemail']))?trim($_POST['uemail']):"";
		$upasswd = (isset($_POST['upasswd']))?$_POST['upasswd']:"";
		$uconfpasswd = (isset($_POST['uconfpasswd']))?$_POST['uconfpasswd']:"";
		$ubday = (isset($_POST['ubday']))?trim($_POST['ubday']):"";
		$usex = (isset($_POST['usex']))?trim($_POST['usex']):"";
		$uworkplace = (isset($_POST['uworkplace']))?trim($_POST['uworkplace']):"";
		$ulocation = (isset($_POST['ulocation']))?trim($_POST['ulocation']):"";
		$ucollege = (isset($_POST['ucollege']))?trim($_POST['ucollege']):"";
		$udesc = (isset($_POST['udesc']))?trim($_POST['udesc']):"";
		
		//ERRORS
		if(!empty($uemail)) {
			$query = 'SELECT * FROM user WHERE emailid = "'.mysqli_real_escape_string($db, $uemail).'";';
			$result = mysqli_query($db, $query);
			$res_check = mysqli_fetch_assoc($result);
			
			if(!$result) {
				die("PicWorld faced some internal error. Please try after sometime.");
			}
			
			if(mysqli_num_rows($result) > 0 && $uemail != $username) {
				header("Location: update.php?user_exists=1");
				die();
			}
			mysqli_free_result($result);
		}
		
		if(!empty($upasswd) && strcmp($upasswd, $uconfpasswd) != 0) {
			header("Location: update.php?password_mismatch=1");
			die();
		}
		
		if(!empty($ubday)) {
			$datetocheck = explode("-", $ubday);
			if(!checkdate($datetocheck[1], $datetocheck[2], $datetocheck[0])) {
				header("Location: update.php?invalid_date=1");
				die();
			}
		}
		
		if($_FILES['upro_pic']['error'] != UPLOAD_ERR_OK) {
			switch($_FILES['upro_pic']['error']) {
				case UPLOAD_ERR_INI_SIZE: header("Location: update.php?large_file=1"); die(); break;
				case UPLOAD_ERR_FORM_SIZE: header("Location: update.php?large_file=1"); die(); break;
				case UPLOAD_ERR_PARTIAL: header("Location: update.php?upload_error=1"); die(); break;
				case UPLOAD_ERR_NO_TMP_DIR: header("Location: update.php?upload_error=1"); die(); break;
				case UPLOAD_ERR_CANT_WRITE: header("Location: update.php?upload_error=1"); die(); break;
				case UPLOAD_ERR_EXTENSION: header("Location: update.php?upload_error=1"); die(); break;
			}
		}
		
		// END OF ERRORS		
		
		
		$ufname = (empty($ufname))?$res['fname']:$ufname;
		$ulname = (empty($ulname))?$res['lname']:$ulname;
		$uemail = (empty($uemail))?$res['emailid']:$uemail;
		$ubday = (empty($ubday))?$res['bday']:$ubday;
		$usex = (empty($usex))?$res['sex']:$usex;		
		$uworkplace = (empty($uworkplace))?$res['place_of_work']:$uworkplace;
		$ulocation = (empty($ulocation))?$res['location']:$ulocation;
		$ucollege = (empty($ucollege))?$res['college']:$ucollege;
		$udesc = (empty($udesc))?$res['description']:$udesc;
		
		
		// TWO cases: when password is changed and when it is not.
		// Case one when it is changed
		

		if(!empty($upasswd)) {
			$query = 'UPDATE user SET fname="'.mysqli_real_escape_string($db, $ufname).'", lname="'.mysqli_real_escape_string($db, $ulname).'", emailid="'.mysqli_real_escape_string($db, $uemail).'", bday="'.mysqli_real_escape_string($db, $ubday).'", sex="'.mysqli_real_escape_string($db, $usex[0]).'", ';
			$query = $query.'place_of_work = "'.mysqli_real_escape_string($db, $uworkplace).'", location = "'.mysqli_real_escape_string($db, $ulocation).'", college="'.mysqli_real_escape_string($db, $ucollege).'", description = "'.mysqli_real_escape_string($db, $udesc).'", ';
			$query = $query.'user_passwd = PASSWORD("'.mysqli_real_escape_string($db, $upasswd).'") where emailid="'.mysqli_real_escape_string($db, $username).'";';
			$result1 = mysqli_query($db, $query);
			if(!$result1) {
				die("MESSAGE 86 Internal error.");
			}
			
			$_SESSION['username'] = $uemail;
			$username =  $uemail;
			
			
		}
		else {
			$query = 'UPDATE user SET fname="'.mysqli_real_escape_string($db, $ufname).'", lname="'.mysqli_real_escape_string($db, $ulname).'", emailid="'.mysqli_real_escape_string($db, $uemail).'", bday="'.mysqli_real_escape_string($db, $ubday).'", sex="'.mysqli_real_escape_string($db, $usex[0]).'", ';
			$query = $query.'place_of_work = "'.mysqli_real_escape_string($db, $uworkplace).'", location = "'.mysqli_real_escape_string($db, $ulocation).'", college="'.mysqli_real_escape_string($db, $ucollege).'", description = "'.mysqli_real_escape_string($db, $udesc).'" ';
			$query = $query.' where emailid="'.mysqli_real_escape_string($db, $username).'";';
			
			$result1 = mysqli_query($db, $query);
			if(!$result1) {
				die("MESSAGE 85 Internal error.");
			}
			
			$_SESSION['username'] = $uemail;
			$username =  $uemail;
			
			
		}
		
		
		
		
		
		
		
		
		/* This is where we deal with the profile picture */
		
		$folder = 'pictures/profile_pictures/';
		$folder_normal_pics = 'pictures/';
		
		if($_FILES['upro_pic']['error'] == UPLOAD_ERR_NO_FILE) {
			$query = 'SELECT * FROM profile_pics WHERE owner = "'.mysqli_real_escape_string($db, $username).'";';
			$result = mysqli_query($db, $query);
			if(!$result) {
				die("MESSAGE 2: PicWorld faced some internal error. Please try after sometime.");
			}
			$res1 = mysqli_fetch_assoc($result);
			$imagename = $res1['pro_pic'];
		}
		
		else {
			list($width, $height, $type, $a) = getimagesize($_FILES['upro_pic']['tmp_name']);
			switch($type) {
				case IMAGETYPE_PNG: $image = imagecreatefrompng($_FILES['upro_pic']['tmp_name']); 
									if(!$image) {
										header("Location: update.php?file_type_error=1");
										die();
									}
									$ext = '.png'; 
									break;
				case IMAGETYPE_JPEG:	$image = imagecreatefromjpeg($_FILES['upro_pic']['tmp_name']);
										if(!$image) {
											header("Location: update.php?file_type_error=1");
											die();
										}
										$ext = '.jpg'; 
										break;
				default: 	header("Location: update.php?file_type_error=1");
							die();
			}
			$imagename = $res['user_id'].$ext;
			
			
			// add pic into Profile Pictures album as well
			$query = 'SELECT aid from album where name="Profile Pictures" and owner="'.mysqli_real_escape_string($db, $username).'" order by aid asc LIMIT 1;';
			$result = mysqli_query($db, $query);
			if(!$result) {
				die("PicWorld faced some internal error. Please try after sometime.");
			}
			if(mysqli_num_rows($result) == 0) {
				//some error occurred.
				$temp_query = 'INSERT INTO album(date_of_creat, name, owner) VALUES(NOW(), "Profile Pictures", "'.mysqli_real_escape_string($db, $username).'");';
				$result = mysqli_query($db, $query);
				
				if(!$result) {
					die("MESSAGE 3: PicWorld faced some internal error. Please try after sometime.");
				}
				
				$query = 'SELECT aid from album where name="Profile Pictures" and owner="'.mysqli_real_escape_string($db, $username).'" order by aid asc LIMIT 1;';
				$result = mysqli_query($db, $query);
				if(!$result) {
					die("MESSAGE 10. PicWorld faced some internal error. Please try after sometime.");
				}
			}
			
			$respp = mysqli_fetch_assoc($result);
			mysqli_free_result($result);
			$query = 'INSERT INTO photos(owner, aid, upload_date, width, height) 
			VALUES("'.mysqli_real_escape_string($db, $username).'", '.mysqli_real_escape_string($db, $respp['aid']).', 
			NOW(), '.mysqli_real_escape_string($db, $width).', '.mysqli_real_escape_string($db, $height).');';
			//echo $query;
			//die();
			
			$result = mysqli_query($db, $query);
			if(!$result) {
				die("MESSAGE 4: PicWorld faced some internal error. Please try after sometime.");
			}
			$last_id = mysqli_insert_id($db);
			$ppimagename = $last_id.$ext;
			
			
			// save the image in the destination
			switch($type) {
				case IMAGETYPE_JPEG: 	imagejpeg($image, $folder.$imagename, 100); 
										imagejpeg($image, $folder_normal_pics.$ppimagename, 100);
										break;
				case IMAGETYPE_PNG: 	imagepng($image, $folder.$imagename); 
										imagepng($image, $folder_normal_pics.$ppimagename);
										break;
			}
			$imagename = $folder.$imagename;
			$ppimagename = $folder_normal_pics.$ppimagename;
			
			$query = 'UPDATE photos SET pic_path="'.mysqli_real_escape_string($db, $ppimagename).'" 
			WHERE pid='.mysqli_real_escape_string($db, $last_id).';';
			$result = mysqli_query($db, $query);
			
			if(!$result) {
				die("MESSAGE 9. PicWorld faced some internal error. Please try after sometime.");
			}
			
			$query = 'UPDATE profile_pics SET pro_pic="'.mysqli_real_escape_string($db, $imagename).'", set_date=NOW(), 
			height = '.mysqli_real_escape_string($db, $height).',
			width = '.mysqli_real_escape_string($db, $width).',
			ppid = '.mysqli_real_escape_string($db, $last_id).'
			WHERE owner= "'.mysqli_real_escape_string($db, $username).'" ;';
			//echo $query;
			//die();
			
			$result = mysqli_query($db, $query);
			
			if(!$result) {
				die("MESSAGE 6: PicWorld faced some internal error. Please try after sometime.");
			}
			
			// dealing with the thumbnails
			$thumbdir = 'pictures/thumbs/';
			if($width > $height) {
				$thumbw = 200;
				$thumbh = $height*200/$width;
			}
			else {
				$thumbh = 200;
				$thumbw = $width*200/$height;
			}
			$thumb = imagecreatetruecolor($thumbw, $thumbh);
			imagecopyresampled($thumb, $image, 0, 0, 0, 0, $thumbw, $thumbh, $width, $height);
			
			$query = 'INSERT INTO thumbs(pid) VALUES('.$last_id.');';
			mysqli_query($db, $query);
			
			$last_thumbid = mysqli_insert_id($db);
			imagejpeg($thumb, $thumbdir.$last_thumbid.'.jpg', 100);
			
			$query = 'UPDATE thumbs SET thumb_path="'.mysqli_real_escape_string($db, $thumbdir.$last_thumbid.'.jpg').'" WHERE thumbid='.mysqli_real_escape_string($db, $last_thumbid).';';
			mysqli_query($db, $query);
			
			imagedestroy($thumb);			
			imagedestroy($image);
		}
		
		
		/* End of the profile picture part */
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		if(mysqli_connect_errno()) {
				die("MESSAGE 15: PicWorld faced some internal error. Please try after sometime.");
		}
		
		
		// add activity
		$query = 'SELECT * FROM user WHERE emailid="'.$username.'";';
		$resultx = mysqli_query($db, $query);
		if(!$resultx) {
			die("MESSAGE 87: Internal error. Try later.");
		}
		$resx = mysqli_fetch_assoc($resultx);
		
		
		$activity = '<a id="activity_content" href="viewprofile.php?uid='.$resx['user_id'].'">'.$resx['fname'].' '.$resx['lname'].'</a> has an updated profile.';
		$query = 'INSERT INTO activities(emailid, activity, activity_date) VALUES("'.$username.'", 
		"'.mysqli_real_escape_string($db, $activity).'", NOW());';
		
		$result = mysqli_query($db, $query);
		if(!$result) {
			die("MESSAGE 97: Some internal ERROR. Please try later.");
		} 
		//added activity
		
		
		mysqli_close($db);
		$_SESSION['username'] = $uemail;
		header("Location: update.php?profile_updated=1");
		die();
		
	}
	
	
	
	// end of updating information
	$username = $_SESSION['username'];
	$query = 'SELECT * FROM user WHERE emailid = "'.mysqli_real_escape_string($db, $username).'";';
	$result = mysqli_query($db, $query);
	
	if(!$result) {
		die("PicWorld faced some internal error. Please try after sometime.");
	}
	
	if(mysqli_num_rows($result) == 0) {
		// some error happened.
		session_destroy();
		header("Location: index.php");
		die();
	}
	$row = mysqli_fetch_assoc($result);
	mysqli_free_result($result);
?>


<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $title; ?></title>
	<link rel="stylesheet" type="text/css" href="mycss/mystyle.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	
	
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<link rel="stylesheet" href="bootstrap/css/bootstrap.css">
	<script src="bootstrap/jquery.min.js"></script>
	<script src="bootstrap/js/bootstrap.js"></script>
	
	<!--
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	-->
	
	
	
</head>

<body>
	<div class="container-fluid">
	
	<div class="row" id="navigation">
	<div class="col-md-1"></div>
	<div class="col-md-3"><span id="picworldHome">PicWorld</span></div>
	<div class="col-md-5"><form action="search.php" method="get">
		<input type="text" name="search_text" id="search_text" placeholder="Search PicWorld">
		<input type="submit" name="search" id="search_button" value="search">
		</form></div>
	<div class="col-md-3">
		
		<table>
			<tr>
			<?php
			$query = 'SELECT * FROM notifications WHERE emailid="'.$_SESSION['username'].'" AND read_state = false;';
			$result9 = mysqli_query($db, $query);
			if(!$result9) {
				die("MESSAGE 45: Internal ERROR. Try later.");
			}
			if(mysqli_num_rows($result9) == 0) {
				?>
			<td class="navglyphtable"><a class="navglyphs" href="notifications.php"><span class="glyphicon glyphicon-bell"></span></a></td>
			<?php
		}
		else {
			?>
			<td class="navglyphtable"><a class="navglyphs" href="notifications.php"><span class="glyphicon glyphicon-flash"></span></a></td>
			<?php
		}
		?>
		<td class="navglyphtable"><a class="navglyphs" href="home.php"><span class="glyphicon glyphicon-home"></span></a></td>
			<td class="navglyphtable"><a class="navglyphs" href="profile.php"><span class="glyphicon glyphicon-user"></span></span></a></td>
			<td class="navglyphtable"><a class="navglyphs" href="logout.php"><span class="glyphicon glyphicon-off"></span> </span></a></td>
			</tr>
		</table>
	</div>
	
	</div> <!-- end of navigation div -->
	
	<h2>Update User Information:</h2>
	<?php 
				if(isset($_GET['profile_updated']) && $_GET['profile_updated'] == 1) {
					echo '<span class="warning">';
					echo "*Your profile has been updated.";	
					echo '</span><br>';	
				}
	?>
	<br>
	
	
	<form action="update.php" method="post" class="role" enctype="multipart/form-data">
		 <div class="table-responsive"> 
		<table class="table">
			
		<tr>
		<td><label for="upro_pic">Set Profile Picture</label></td>
		<td><input  class="update_info"  type="file" id="upro_pic" name="upro_pic"></td>
		<td>
			<?php 
				if(isset($_GET['large_file']) && $_GET['large_file'] == 1) {
					echo '<span class="warning">';
					echo "*The photo you uploaded is too big.";	
					echo '</span><br>';	
				}
				
				if(isset($_GET['upload_error']) && $_GET['upload_error'] == 1) {
					echo '<span class="warning">';
					echo "*There was some error uploading the photo.";	
					echo '</span><br>';	
				}
				
				if(isset($_GET['file_type_error']) && $_GET['file_type_error'] == 1) {
					echo '<span class="warning">';
					echo "*File type not supported.";	
					echo '</span><br>';	
				}
			?>
		</td>
		</tr>
			
			<tr>
		<td><label for="ufname">First Name</label></td>
		<td><input  class="update_info"  type="text" id="ufname" name="ufname" value=<?php echo '"'.$row['fname'].'"'; ?>></td>
		<td></td>
		</tr>
		<tr>
		<td><label for="ulname">Last Name</label></td>
		<td><input  class="update_info"  type="text" id="ulname" name="ulname" value=<?php echo '"'.$row['lname'].'"'; ?>></td>
		<td></td>
		</tr>
		<tr>
		<td><label for="uemail">Email ID</label></td>
		<td><input  class="update_info"   type="text" id="uemail" name="uemail" value=<?php echo '"'.$row['emailid'].'"'; ?>></td>
		<td>
			<?php 
				if(isset($_GET['user_exists']) && $_GET['user_exists'] == 1) {
					echo '<span class="warning">';
					echo "*This email is already registered.";	
					echo '</span><br>';	
				}
			?>
		
		</td>
		
		</tr>
		<tr>
		<td><label for="upasswd">Password</label></td>
		<td><input   class="update_info" type="password" id="upasswd" name="upasswd" value=""></td>
		<td>
		</td>
		</tr>
		<tr>
		<td><label for="uconfpasswd">Confirm Password</label></td>
		<td><input   class="update_info" type="password" id="uconfpasswd" name="uconfpasswd" value="" ></td>
		<td>
		<?php 
				if(isset($_GET['password_mismatch']) && $_GET['password_mismatch'] == 1) {
					echo '<span class="warning">';
					echo "*Passwords don't match.";	
					echo '</span><br>';	
				}
		?>
		</td>
		</tr>
		<tr>
		<td><label for="ubday">Birthday</label></td>
		<td><input  class="update_info" type="date" id="ubday" name="ubday" value=<?php echo '"'.$row['bday'].'"'; ?>></td>
		<td>
		<?php 
				if(isset($_GET['invalid_date']) && $_GET['invalid_date'] == 1) {
					echo '<span class="warning">';
					echo "*Enter proper date.";	
					echo '</span><br>';	
				}
		?>
		</td>
		</tr>
		<tr>
		<td>
			<label for="upasswd">Sex</label></td>
		<td><input type="radio" name="usex" value="MALE" <?php if($row['sex'] == 'M') echo "checked"; ?>>Male
			<input type="radio" name="usex" value="FEMALE" <?php if($row['sex'] == 'F') echo "checked"; ?>>Female</td>
		<td></td>
		</tr>
		
		<tr>
		<td><label for="uworkplace">Where do you work?</label></td>
		<td><input  class="update_info" type="text" id="uworkplace" name="uworkplace" value=<?php echo '"'.$row['place_of_work'].'"'; ?>></td>
		<td></td>
		</tr>
		
		<tr>
		<td><label for="ulocation">Where do you live?</label></td>
		<td><input  class="update_info" type="text" id="ulocation" name="ulocation" value=<?php echo '"'.$row['location'].'"'; ?>></td>
		<td></td>
		</tr>
		
		
		<tr>
		<td><label for="ucollege">College</label></td>
		<td><input  class="update_info" type="text" id="ucollege" name="ucollege" value=<?php echo '"'.$row['college'].'"'; ?>></td>
		<td></td>
		</tr>
		
		<tr>
		<td><label for="udesc">About you</label></td>
		<td><textarea class="update_info" rows="5" cols="200" name ="udesc" ><?php echo $row['description']; ?></textarea></td>
		<td></td>
		</tr>
		
		
		<tr>
		<td></td>
		<td><input type="submit" id="ubutton" name="update" value="Update"></td>
		<td></td>
		</tr>
		
		</table>
		</div>
	</form>
	
	
	<nav class="navbar navbar-default navbar-fixed-bottom">
	<div class="container-fluid" id="footer">
			<?php include('includes/footer.inc.php'); ?>
	</div>
	</nav>
	
	</div>		<!--  end of bootstrap container-fluid class -->
</body>
</html>

<?php
mysqli_close($db);
?>
