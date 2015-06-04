<?php
	session_start();
	if(!isset($_SESSION['logged']) || $_SESSION['logged'] != 1) {
		header("Location: login.php?not_logged=1");
		die();
	}
	
	$curuser = $_SESSION['username'];
	if(!isset($_GET['pid'])) {
		die('What am i supposed to edit?');		
	}
	
	include('includes/config.inc.php');
	$pid = $_GET['pid'];
	
	$query = "SELECT * FROM photos WHERE pid=$pid;";
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("1. Internal error. Try again later.");
	}
	$row = mysqli_fetch_assoc($result);
	
	// Only owner can edit photo
	
	if($row['owner'] != $curuser) {
		die("You cannot edit this photo");
	}
	
	$ppath = $row['pic_path'];
	$h = $row['height'];
	$w = $row['width'];
	$maxlength = 1.0*min(500, max($h, $w));
	if($h > $w) {
		$height = intval($maxlength);
		$width = intval($maxlength*$w/$h);
	}
	else {
		$width = intval($maxlength);
		$height = intval($maxlength*$h/$w);
	}
	
	if(isset($_POST['edit_photo']) && $_POST['edit_photo'] == 'Change') {
		$story = (isset($_POST['p_story']))?trim($_POST['p_story']):"";
		$type = (isset($_POST['type']))?trim($_POST['type']):"";
	
		if(empty($story)) {
			$story = $row['story'];
		}
		if(empty($type)) {
			$type = $row['type'];
		}
		$query = 'UPDATE photos SET story="'.mysqli_real_escape_string($db, $story).'", 
		type="'.mysqli_real_escape_string($db, $type).'" WHERE pid='.mysqli_real_escape_string($db, $pid).';';
		$result = mysqli_query($db, $query);
		if(!$result) {
			die("2.INTERNAL ERROR. try again later.");
		}
		header('Location: show_image.php?photo_num='.$pid);
		mysqli_close($db);
		die();
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $title; ?></title>
	<link rel="stylesheet" type="text/css" href="mycss/mystyle.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<style>
		#edit_image {
			width: <?php echo $width."px;"; ?>
			height: <?php echo $height."px;"; ?>
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
	<h3>Edit this photo:</h3>
	<img id="edit_image" src=<?php echo '"'.$ppath.'"'; ?>>
	<form action=<?php echo 'edit_photo.php?pid='.$pid; ?> method="post">
	<label for="p_story">Description of this photo:</label><br>
		<textarea class="story" rows="5" cols="100" name ="p_story" ></textarea>
		<br>
		<label>Privacy:</label><br>
		<label for="type">Private</label>
		<input type="radio" name="type" value="private">
		<label for="type">Public</label>
		<input type="radio" name="type" value="public">
		<label for="type">Friends</label>
		<input type="radio" name="type" value="friends"><br>
		<input type="submit" id="edit_photo_button" name="edit_photo" value="Change">
	</form>

	<div class="row album_op">
		<div class="col-md-12 album_op">
		<table class="albums_table">
		<tr>
		<td class="albums_table"><a href=<?php echo '"show_image.php?photo_num='.$_GET['pid'].'"'; ?> id="albums">Back to photo</a></td>
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
