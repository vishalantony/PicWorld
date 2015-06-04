<?php
	session_start();
	//to list out the users' albums
	if(!isset($_SESSION['logged']) || $_SESSION['logged'] != 1) {
		header("Location: login.php?not_logged=1");
		die();
	}
	
	include('includes/config.inc.php');
	$username = $_SESSION['username'];
	$owner_of_album = $username;
	
	$type = 'public';
	
	if(isset($_GET['uid'])) {
		$uid = trim($_GET['uid']);
		if(!empty($uid)) {
			$query = 'SELECT *, CONCAT(fname, " ", lname) as full_name FROM user WHERE 
			user_id='.mysqli_real_escape_string($db, $uid).';';
			$result = mysqli_query($db, $query);
			if(!$result) {
				die("Internal ERROR. Try again later.");
			}
			if(mysqli_num_rows($result) == 0) {
				header("Location: profile.php");
				die();
			}
			$row5 = mysqli_fetch_assoc($result);
			$owner_of_album = $row5['emailid'];
			
			$query = 'SELECT * from friends where (acceptedby="'.$username.'" and sentby="'.$row5['emailid'].'") 
			or (acceptedby="'.$row5['emailid'].'" and sentby="'.$username.'");';
			$result = mysqli_query($db, $query);
			if(!$result) {
				die("Internal ERROR. Try again later.");
			}
			if(mysqli_num_rows($result) > 0) {
				$type = "friends";
			}
			else if($username == $row5['emailid'])
				$type = 'private';
		}
	}
	else {
		$type = 'private';
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
	
<?php
	if($type == 'private') {
		$query = 'SELECT * FROM album WHERE owner = "'.mysqli_real_escape_string($db, $owner_of_album).'"
		and type in ("public", "private", "friends");';
	}
	else if($type == 'public') {
		$query = 'SELECT * FROM album WHERE owner = "'.mysqli_real_escape_string($db, $owner_of_album).'"
		and type in ("public");';
	}
	else {
		$query = 'SELECT * FROM album WHERE owner = "'.mysqli_real_escape_string($db, $owner_of_album).'"
		and type in ("public", "friends");';
	}

	$result = mysqli_query($db, $query);
	if(!$result) {
		die("PicWorld faced some internal error. Please try after sometime.");
	}
	
	if(mysqli_num_rows($result) == 0) {
		echo '<p>You have no albums to view.</p>';
	}
	else {
		
		$query = 'SELECT *, CONCAT(fname, " ", lname) as full_name FROM user WHERE emailid = "'.mysqli_real_escape_string($db, $owner_of_album).'";';
		$result1 = mysqli_query($db, $query);
		if(!$result1) {
			die("PicWorld faced some internal error. Please try after sometime.");
		}
		$row7 = mysqli_fetch_assoc($result1);
		mysqli_free_result($result1);
		
		
		echo '<h3>'.$row7['full_name'].'\'s Albums</h3>';
		echo '<table>';
		echo '<tr><th id="list_albums">Name</th> <th id="list_albums">Created on:</th></tr>';
		while($row = mysqli_fetch_assoc($result)) {
			echo '<tr>';
			echo '<td id="list_albums"><a class="album_link" href="display_album.php?album_id='.$row['aid'].'">'.$row['name'].'</a></td>';
			echo '<td id="list_albums">'.$row['date_of_creat'].'</td>';
			echo '</tr>';
		}
		echo '</table>';
		mysqli_free_result($result);
	}
	
?>
	
	
	
	<br>
	<br>
	
	<?php 
		if($owner_of_album == $username) {
	?>
	<div class="row album_op">
		<div class="col-md-12 album_op">
		<table class="albums_table">
		<tr>
		<td class="albums_table"><a href="create_album.php" id="albums">Create Album</a></td>
		<td class="albums_table"><a href="friends.php" id="friends">My Friends</a></td>
		</tr>
		</table>
		</div>
	</div>
	<?php
		}
		
		else {
			$query = 'SELECT *, CONCAT(fname, " ", lname) as full_name FROM user WHERE emailid="'.$owner_of_album.'";';
			$result = mysqli_query($db, $query);
			if(!$result) {
				die("MESSAGE 64: Internal ERROR. Try later.");
			}
			if(mysqli_num_rows($result) == 0) {
				//some error.
				header("Location: profile.php");
				die();
			}
			$row6 = mysqli_fetch_assoc($result);
	?>
	<div class="row album_op">
		<div class="col-md-12 album_op">
		<table class="albums_table">
		<tr>
		<td class="albums_table"><a href=<?php echo '"viewprofile.php?uid='.$row6['user_id'].'"'; ?> id="albums">View <?php echo $row6['full_name']."'s profile" ?></a></td>
		</tr>
		</table>
		</div>
	</div>
	<?php
		}
	?>
	<br>
	<br>
	
	
	<!--<div class="row update">
		<div class="col-md-5"></div>
		<div class="col-md-1"><a href="update.php" id="update">Update Profile</a></div>
		<div class="col-md-1"><a href="delete_confirmation.php" id="delete">Delete Profile</a></div>
		<div class="col-md-5"></div>
	</div>-->
	
	
	
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
