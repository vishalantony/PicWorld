<?php 
	session_start();
	if(!isset($_SESSION['logged']) || $_SESSION['logged'] != 1) {
		header("Location: login.php?not_logged=1");
		die();
	}
	$username = $_SESSION['username'];
	
	include('includes/config.inc.php');
	
	if(isset($_POST['add_album']) && $_POST['add_album'] == 'Create Album') {
		$albumname = isset($_POST['album_name'])?trim($_POST['album_name']):"";
		$albumdesc = isset($_POST['album_desc'])?trim($_POST['album_desc']):"";
		
		if(empty($albumname)) {
			header("Location: create_album.php?name_empty=1");
			die();
		}
		
		$query = 'INSERT INTO album(date_of_creat, name, owner, description) 
		VALUES(NOW(), "'.mysqli_real_escape_string($db, $albumname).'", "'.mysqli_real_escape_string($db, $username).'", "'.mysqli_real_escape_string($db, $albumdesc).'");';
		$result = mysqli_query($db, $query);
		if(!$result) {
			die("Picworld experienced some internal error. Please try again after sometime.");
		}
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
	
	<h3>Create An Album:</h3>
	<br>
	
	<form action="create_album.php" method="post">
		<div class="table-responsive"> 
		<table class="table">
			<tr>
				<td class="album_details"><label for="album_name">Enter Album Name</label></td>
				<td class="album_details"><input type="text" name="album_name" id="album_name" value=""></td>
				<td class="album_details">
				<?php
					if(isset($_GET['name_empty']) && $_GET['name_empty'] == 1) {
						echo '<span class="warning">';
						echo "*Album name is necessary.";	
						echo '</span><br>';	
					}
				?>
				</td>
			</tr>
			<tr>
				<td class="album_details"><label for="album_name">Enter Album Description</label></td>
				<td class="album_details"><textarea class="album_desc" rows="6" cols="60" name ="album_desc" id="album_desc" ></textarea></td>
				<td class="album_details"></td>
			</tr>
			<tr>
			<td class="album_details"></td>
			<td class="album_details"><input type="submit" id="add_album" name="add_album" value="Create Album"></td>
			<td class="album_details"></td>
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
