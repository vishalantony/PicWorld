<?php
	session_start();
	if(!isset($_SESSION['logged']) || $_SESSION['logged'] != 1) {
		header("Location: login.php?not_logged=1");
		die();
	}
	
	$curuser = $_SESSION['username'];
	if(!isset($_GET['photo_num'])) {
		//header("Location: profile.php");
		die("Which photo do you wanna see??");
	}
	
	include('includes/config.inc.php');
	$pid = trim($_GET['photo_num']);
	if(empty($pid)) {
		header("Location: home.php");
		die();
	}
	
	// retrieving all the photo info
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
	
	// now result contains all the information about the photo.
	
	// Determining the type of user.
	
	$type = 'public';
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
	
	
	if($row1['owner'] == $curuser) {
		$type = 'private';
	}
	
	if($type == 'public' && $row1['type'] == 'public') {
		$display = 1;
	}
	else if($type == 'friends' && ($row1['type'] == 'public' || $row1['type'] == 'friends')) {
		$display = 1;
	}
	else if($type == 'private') {
		$display = 1;
	}
	else {
		$display = 0;
	}
	
	if($display == 0) {
		//header("Location: albums.php");
		die("You don't have enough permission see this photo.");
	}
	
	$height = intval($row1['height']);
	$width = intval($row1['width']);
	
	$maxlength = 1.0*min(600, max($height, $width)); //(max of width and height in pixels)
	if($height > $width) {
		$h = intval($maxlength);
		$w = intval($width*$maxlength/$height);
	}
	else {
		$h = intval($height*$maxlength/$width);
		$w = intval($maxlength);
	}
	$pic_path = $row1['pic_path'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $title; ?></title>
	<link rel="stylesheet" type="text/css" href="mycss/mystyle.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<style>
		#displayed_image {
			height: <?php echo $h."px"; ?>;
			width: <?php echo $w."px"; ?>;
		}
		
		#image_desc {
			width: <?php echo $w."px"; ?>;
		}
		
		#image_like {
			width: <?php echo $w."px"; ?>;
		}
		
		div.comments_div {
			width: <?php echo $w."px"; ?>;
		}
		
		#image_comments {
			width: <?php echo $w."px"; ?>;
		}
		textarea.comment_here {
			width: <?php echo ($w-20)."px"; ?>;
		}
		
	</style>
	
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
	
	<br>
	<div class="row image_contents">
		
	<div class="col-md-12 enlarge_image">
	<img id="displayed_image" src=<?php echo '"'.$pic_path.'"'; ?>>
	<br><br>
		<?php
			$query = 'SELECT *, CONCAT(fname, " ", lname) as full_name FROM user WHERE emailid="'.$row1['owner'].'";';	
			$result = mysqli_query($db, $query);
			if(!$result) {
				die("MESSAGE 98: Internal ERROR. Try later.");
			}
			$row7 = mysqli_fetch_assoc($result);
		?>
		<p id="image_desc"> Image by: <a id="image_owner" href=<?php echo '"viewprofile.php?uid='.$row7['user_id'].'"'; ?> >
		<?php echo $row7['full_name']; ?></a></p>
	<p id="image_desc"><?php echo $row1['story']?></p>
	<table id="image_like">
		<tr>
		<?php
			$query = 'SELECT * FROM likes WHERE pid='.mysqli_real_escape_string($db, $pid).' 
			AND liked_by="'.mysqli_real_escape_string($db, $curuser).'";';
			$result = mysqli_query($db, $query);
			if(!$result) {
				die("Internal error. Please try after sometime.");
			}
			if(mysqli_num_rows($result) > 0) {		// already liked
				echo '<td><a href="unlike.php?pid='.$pid.'">dislike('.$row1['no_likes'].')</a></td>';
			}
			else {
				echo '<td><a href="like.php?pid='.$pid.'">like('.$row1['no_likes'].')</a></td>';
			}
		?>
		</tr>
	</table>
	<!-- Comments section -->
	<div class="table-responsive comments_div">
		<table class="table" id="image_comments">
			<?php
				$query = 'SELECT * FROM comments, user WHERE pid='.mysqli_real_escape_string($db, $pid).' AND comments.comment_by=user.emailid order by date_of_comment asc;';
				$result = mysqli_query($db, $query);
				if(!$result) {
					die("coudn't retrieve comments.");
				}
				else {
					$color = array("#FFD6CC", "#F4C9DF"); 
					$start = 0;
					while($row2 = mysqli_fetch_assoc($result)) {
						echo '<tr>';
						echo '<td bgcolor="'.$color[$start].'"><p>';
						echo $row2['fname'].' '.$row2['lname'].' : ';
						echo $row2['comment'];
						echo '<br><br>';
						
						echo $row2['date_of_comment'];
						echo '</p></td>';
						echo '</tr>';
						$start = !$start;
					}
				}
			?>
			<tr>
			<td>
				<form action=<?php echo 'add_comment.php?pid='.$pid; ?> method="post">
					<textarea class="comment_here" rows="5" cols="100" name ="p_comment" ></textarea>
					<input type="submit" id="comment_button" name="comment" value="Comment">
				</form>
			</td>
			</tr>
		</table>
	</div>
	
	</div>
	
	<br>
	<br>
	
	<?php
		$query = 'SELECT * from album WHERE aid='.mysqli_real_escape_string($db, $row1['aid']).';';
		$result = mysqli_query($db, $query);
		if(!$result) {
			die('#3. Internal error. try again later.');
		}
		$row3 = mysqli_fetch_assoc($result);
	?>

	<div class="row album_op">
		<div class="col-md-12 album_op">
		<table class="albums_table">
		<tr>
			<?php
				if($type == 'private') {
				?>
					<td class="albums_table"><a href=<?php echo '"edit_photo.php?pid='.$pid.'"'; ?> id="albums">Edit this photo details</a></td>
			<?php
				}
			?>
		<td class="albums_table"><a href=<?php echo '"display_album.php?album_id='.$row3['aid'].'"'; ?> id="albums">Back to <?php echo $row3['name']; ?></a></td>
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
	mysqli_close($db); 
	mysqli_free_result($result);

?>
