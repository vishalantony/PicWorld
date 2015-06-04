<?php
	session_start();
	if(!isset($_SESSION['logged']) || $_SESSION['logged'] != 1) {
		header("Location: login.php?not_logged=1");
		die();
	}
	
	$curuser = $_SESSION['username'];
	if(!isset($_GET['aid'])) {
		die('What am i supposed to add the photos to?');		
	}
	
	include('includes/config.inc.php');
	
	$aid = $_GET['aid'];
	$query = 'SELECT * FROM album WHERE aid='.$aid.';';
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("1. Internal error. try again later.");
	}
	$row1 = mysqli_fetch_assoc($result);
	if($row1['owner'] != $curuser) {
		die("You cannot add photos to this album!");
	}
	
	
	$folder = 'pictures/';
	
	if(isset($_POST['upload_pics']) && $_POST['upload_pics'] == 'Upload' && isset($_FILES['photo_array'])) { 
		
		$privacy = (isset($_POST['type']))?trim($_POST['type']):"";
		if(empty($privacy)) {
			$privacy = 'private';
		}
		
		$tmp_names = $_FILES['photo_array']['tmp_name'];
		$up_errors = $_FILES['photo_array']['errors'];
		$num_of_photos = 0;
		
		for($i = 0; $i < count($tmp_names); $i++) {
			if(empty($tmp_names[$i])) continue;
			
			if($up_errors[$i] != UPLOAD_ERR_OK) {
				switch($up_errors[$i]) {
					case UPLOAD_ERR_INI_SIZE: 
					case UPLOAD_ERR_FORM_SIZE: 
					case UPLOAD_ERR_PARTIAL: 
					case UPLOAD_ERR_NO_TMP_DIR:
					case UPLOAD_ERR_CANT_WRITE:
					case UPLOAD_ERR_NO_FILE:
					case UPLOAD_ERR_EXTENSION: die("SOME ERROR occurred while uploading the images"); break;
				}
			}
			
			list($width, $height, $type, $a) = getimagesize($tmp_names[$i]);
			switch($type) {
				case IMAGETYPE_PNG: $image = imagecreatefrompng($tmp_names[$i]); 
									if(!$image) {
										die("1. Image type error!");
									}
									$ext = '.png'; 
									break;
				case IMAGETYPE_JPEG:	$image = imagecreatefromjpeg($tmp_names[$i]);
										if(!$image) {
											die("2. Image type error!");
										}
										$ext = '.jpg'; 
										break;
				default: 	die("3. Image type error!"); break;
			}
			
			$query = 'INSERT INTO photos(owner, aid, upload_date, type, width, height) 
			VALUES("'.mysqli_real_escape_string($db, $curuser).'", '.mysqli_real_escape_string($db, $aid).', 
			now(), "'.mysqli_real_escape_string($db, $privacy).'", '.mysqli_real_escape_string($db, $width).', 
			'.mysqli_real_escape_string($db, $height).')';
			
			$result = mysqli_query($db, $query);
			if(!$result) {
				die("46. PicWorld faced some internal error. Please try after sometime.");
			}
			$last_id = mysqli_insert_id($db);
			$imagename = $last_id.$ext;
			switch($type) {
				case IMAGETYPE_JPEG: 	imagejpeg($image, $folder.$imagename, 100); 
										break;
				case IMAGETYPE_PNG: 	imagepng($image, $folder.$imagename); 
										break;
			}
			$imagename = $folder.$imagename;
			
			$query = 'UPDATE photos SET pic_path="'.mysqli_real_escape_string($db, $imagename).'" 
			WHERE pid='.mysqli_real_escape_string($db, $last_id).';';
			$result = mysqli_query($db, $query);
			if(!$result) {
				die("45. PicWorld faced some internal error. Please try after sometime.");
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
			
			$num_of_photos++;
		}
		
		// retrieve user info
		$query = "SELECT * FROM user WHERE emailid=\"$curuser\"";
		$result = mysqli_query($db, $query);
		if(!$result) {
			die("MESSAGE 67: Internal ERROR. Try later.");
		}
		$res = mysqli_fetch_assoc($result);
		//end of retrieve user info
		
		// add activity
		if($num_of_photos > 0) {
		$activity = '<a id="activity_content" href="viewprofile.php?uid='.$res['user_id'].'">'.$res['fname'].' '.$res['lname'].'</a> 
		added '.$num_of_photos.' photos to the album <a id="activity_content" href="display_album.php?album_id='.$aid.'">'.$row1['name'].'</a>.';
		$query = 'INSERT INTO activities(emailid, activity, activity_date) VALUES("'.$res['emailid'].'", 
		"'.mysqli_real_escape_string($db, $activity).'", NOW());';
		$result = mysqli_query($db, $query);
		if(!$result) {
			die("MESSAGE 97: Some internal ERROR. Please try later.");
		} //
		}
		//added activity
		
		
		
		mysqli_close($db); 
		header("Location: display_album.php?album_id=".$aid);
		die();
	}
	
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
			$query = 'SELECT * FROM notifications WHERE emailid="'.$_SESSION['username'].'" AND read_state=false;';
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
	
	<h3>Select photos to upload:</h3>
	<br>
	<form action=<?php echo '"add_photos.php?aid='.$aid.'"'; ?> method="post" class="role" enctype="multipart/form-data">
		<p><input type="file" name="photo_array[]"></p>
		<p><input type="file" name="photo_array[]"></p>
		<p><input type="file" name="photo_array[]"></p>
		<p><input type="file" name="photo_array[]"></p>
		<p><input type="file" name="photo_array[]"></p>
		
		<label>Privacy:</label><br>
		<label for="type">Private</label>
		<input type="radio" name="type" value="private" checked>
		<label for="type">Public</label>
		<input type="radio" name="type" value="public">
		<label for="type">Friends</label>
		<input type="radio" name="type" value="friends"><br>
		
		
		<input type="submit" name="upload_pics" value="Upload">
	</form>

	<div class="row album_op">
		<div class="col-md-12 album_op">
		<table class="albums_table">
		<tr>
			<td class="albums_table"><a href=<?php echo '"display_album.php?album_id='.$aid.'"'; ?> id="albums">Back to <?php echo $row1['name'] ?></a></td>
			<td class="albums_table"><a href="albums.php" id="albums">Back to albums</a></td>
		</tr>
		</table>
		</div>
	</div>
	<br>
	<br>
	
	
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
	mysqli_free_result($result);
?>
