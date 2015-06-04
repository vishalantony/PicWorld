<?php
	session_start();
	
	if(!isset($_SESSION['logged']) || $_SESSION['logged'] != 1) {
		header("Location: login.php?not_logged=1");
		die();
	}
	
	$curuser = $_SESSION['username'];
	
	if(isset($_GET['album_id'])) {
		include('includes/config.inc.php');
		$aid = $_GET['album_id'];
		$query = "SELECT * FROM album WHERE aid = $aid";
		$result = mysqli_query($db, $query);
		if(!$result) {
			die("1. PicWorld faced some internal error. Please try after sometime.");
		}
		
		if(mysqli_num_rows($result) == 0) {			// such an album doesn't exist
			header("Location: profile.php");
			die();
		}
		
		$row = mysqli_fetch_assoc($result);
		
		$type = 'public';
		$query = 'SELECT * from friends where (acceptedby="'.$curuser.'" and sentby="'.$row['owner'].'") 
	or (acceptedby="'.$row['owner'].'" and sentby="'.$curuser.'");';
	//echo $query;
	//die();
		$result = mysqli_query($db, $query);
		if(mysqli_num_rows($result) > 0) {
			$type = 'friends';
		}
		if($curuser == $row['owner']) {
			$type = 'private';
		}
		//echo $row['type'];
		//die();
		if($type == 'public' && $row['type'] != 'public') {
			header('Location: home.php');
			die();
		}
		if($type == 'friends' && ($row['type'] == 'private')) {
			header('Location: home.php');
			die();
		}
		
		
		$album_name = $row['name'];
		$album_desc = $row['description'];
		mysqli_free_result($result);
		
		$type = "public";
		if($curuser == $row['owner']) {
			$type = "private";
		}
		
		$query = 'SELECT * FROM friends WHERE (sentby="'.mysqli_real_escape_string($db, $curuser).'" and acceptedby="'.mysqli_real_escape_string($db, $row['owner']).'")
		or (sentby="'.mysqli_real_escape_string($db, $row['owner']).'" and acceptedby="'.mysqli_real_escape_string($db, $curuser).'");';
		$result = mysqli_query($db, $query);
		if(!$result) {
			die("2. PicWorld faced some internal error. Please try after sometime.");
		}
		
		if(mysqli_num_rows($result) > 0) {			
			$type = "friends";
		}
		
		mysqli_free_result($result);
		
		if($type == "friends") {
			$query = 'SELECT *, thumbs.pid as TPID FROM photos, thumbs WHERE aid = '.$aid.' and photos.pid = thumbs.pid and type in ("friends", "public");';
			$result = mysqli_query($db, $query);
			if(!$result) {
				die("3. PicWorld faced some internal error. Please try after sometime.");
			}
		}
		else if($type == "private") {
			$query = 'SELECT *, thumbs.pid as TPID FROM photos, thumbs WHERE aid = '.$aid.' and photos.pid = thumbs.pid and type in ("friends", "public", "private");';
			$result = mysqli_query($db, $query);
			if(!$result) {
				die("4. PicWorld faced some internal error. Please try after sometime.");
			}
		}
		else{
			$query = 'SELECT *, thumbs.pid as TPID FROM photos, thumbs WHERE aid = '.$aid.' and photos.pid = thumbs.pid and type in ("public")';
			$result = mysqli_query($db, $query);
			if(!$result) {
				die("5. PicWorld faced some internal error. Please try after sometime.");
			}
			
		}
	
		
	}
	else {
		header("Location: albums.php");
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
	
	<h3><?php echo "$album_name"; ?></h3>
	<br>
	<p><?php if(!empty($album_desc)) {?><span id="about_album">About this album: </span><?php echo " $album_desc"; }?></p>
	<div class="row">
	<div class="col-md-12">
	<?php
	
	if(mysqli_num_rows($result) > 0) {
		echo '<div class="table-responsive"> ';
	echo '<table class="table">';
	while($row_pic = mysqli_fetch_assoc($result)) {
		echo '<tr>';
		
		echo '<td id="album_thumbnails"><a href="show_image.php?photo_num='.$row_pic['TPID'].'"><img src="'.$row_pic['thumb_path'].'"></a></td><td id="album_thumbnails">';
		if($row_pic = mysqli_fetch_assoc($result)) {
			
			echo '<a href="show_image.php?photo_num='.$row_pic['TPID'].'"><img src="'.$row_pic['thumb_path'].'"></a>';
		}
		echo '</td></tr>';
	}
	echo '</table>';
	}
	
?>	
	</div>
	</div>
	
	<br>
	<br>

	<div class="row album_op">
		<div class="col-md-12 album_op">
		<table class="albums_table">
		<tr>
			<?php
				if($type == 'private') {	
			?>
		<td class="albums_table"><a href=<?php echo 'add_photos.php?aid='.$aid ?> id="albums">Add Photos to this album</a></td>
			<?php
				}
					
				$query = 'SELECT *, CONCAT(fname, " ", lname) as full_name FROM user WHERE emailid="'.$row['owner'].'";';
				
				$result = mysqli_query($db, $query);			
				if(!$result) {
					die("37. Some internal error.");
				}
				$row9 = mysqli_fetch_assoc($result);
				
			?>
		<td class="albums_table"><a href=<?php echo '"albums.php?uid='.$row9['user_id'].'"' ?> id="albums">Back to <?php echo ($type=='private')?(""):($row9['full_name']."'s"); ?> albums</a></td>
		</tr>
		</table>
		</div>
	</div>
	<br>
	<br>
	
	
	<!--<div class="row update">
		<div class="col-md-5"></div>
		<div class="col-md-1"><a href="update.php" id="update">Update Profile</a></div>
		<div class="col-md-1"><a href="delete_confirmation.php" id="delete">Delete Profile</a></div>
		<div class="col-md-5"></div>
	</div>
	-->
	
	
	<nav class="navbar navbar-default navbar-fixed-bottom">
	<div class="container-fluid" id="footer">
			<?php include('includes/footer.inc.php'); ?>
	</div>
	</nav>
	
	</div>		<!--  end of bootstrap container-fluid class -->
</body>
</html>
<?php
	mysqli_free_result($result);
	mysqli_close($db);	
?>
