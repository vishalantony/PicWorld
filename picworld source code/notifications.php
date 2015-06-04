<?php
	session_start();
		
	ini_set('display_errors', true);
	error_reporting(E_ALL ^ E_NOTICE);
	
	if(!isset($_SESSION['logged']) || $_SESSION['logged'] != 1) {
		header("Location: login.php?not_logged=1");
	}
	
	$title = "photoShare";
	$mainHeading = "PhotoShare";
	
	include('includes/config.inc.php');
	
	$username = $_SESSION['username'];
	
	$query = 'SELECT * FROM notifications WHERE emailid="'.mysqli_real_escape_string($db, $username).'" order by notif_date desc;';
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("MESSAGE 1: Internal ERROR. Try later.");
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
	
	<h4>Notifications:</h4>
	
	<?php
		if(mysqli_num_rows($result) == 0) {
			echo "You have no notifications yet.";
		}
		else {
			$query = 'SELECT * FROM user WHERE emailid="'.mysqli_real_escape_string($db, $username).'";';
			$res = mysqli_query($db, $query);
			if(!$res) {
				die("MESSAGE 43: Internal ERROR. Try later.");
			}
			$res1 = mysqli_fetch_assoc($res);
			echo '<p id="mark_read"><a id="activity_content" href="mark_read_all.php?uid='.$res1['user_id'].'">Mark all notifications as read</a></p>';
		}
	?>
	
	<table>
		<?php
		while($row = mysqli_fetch_assoc($result)) {
			echo '<tr>';
			if($row['read_state']) {
				echo '<td id="read_notifs">';
				echo $row['notification'];
				echo '</td>';
				echo '<td id="read_notifs">';
				echo $row['notif_date'];
				echo '</td>';
			}
			else {
				echo '<td id="unread_notifs">';
				echo $row['notification'];
				echo '</td>';
				echo '<td id="unread_notifs">';
				echo $row['notif_date'];
				echo '</td>';
				
			}
			echo '</tr>';
		}
		?>
	</table>
	
	

	<div class="row album_op">
		<div class="col-md-12 album_op">
		<table class="albums_table">
		<tr>
		<td class="albums_table"><a href="albums.php" id="albums">Albums</a></td>
		<td class="albums_table"><a href="friends.php" id="friends">My Friends</a></td>
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
?>
