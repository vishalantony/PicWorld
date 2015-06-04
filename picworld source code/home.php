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
	$username = $_SESSION['username'];
	
	$query = 'SELECT distinct activity as activity, activity_date FROM activities WHERE emailid IN
	(SELECT sentby FROM friends WHERE acceptedby="'.mysqli_real_escape_string($db, $username).'")
	or emailid IN
	(SELECT acceptedby FROM friends WHERE sentby="'.mysqli_real_escape_string($db, $username).'")
	or emailid="'.mysqli_real_escape_string($db, $username).'" 
	order by activity_date desc;';
	
	
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("1. Internal error. Try again later.");
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
	
<br>
<br>
<h4>What's the story morning glory?</h4>
<br>
	 
	 <!-- <div class="table-responsive">  -->
	 <!-- class="table" -->
		<table  id="friend_list">
			<?php
				while($row = mysqli_fetch_assoc($result)) {
					echo '<tr>';
					echo '<td id="activity">';
					echo $row['activity'];
					echo '</td>';
					echo '<td>';
					echo $row['activity_date'];
					echo '</td>';
					echo '</tr>';
				}
			?>
		</table>
	
	
	<nav class="navbar navbar-default navbar-fixed-bottom">
	<div class="container-fluid" id="footer">
			<?php include('includes/footer.inc.php'); ?>
	</div>
	</nav>
	
	</div>		<!--  end of bootstrap container-fluid class -->
	 
	 
	 
	</body>
	</html>
