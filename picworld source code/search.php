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
	
	if(isset($_GET['search']) && $_GET['search'] == "search") {
		$search_text = trim($_GET['search_text']);
		if(empty($search_text)) {
			header("Location: profile.php");
			die();
		}
		$tokens = explode(' ', $search_text);
		$search_for = implode('%', $tokens);
		$query = 'SELECT * FROM user WHERE CONCAT(fname, lname) like "'.mysqli_real_escape_string($db, $search_for).'" ';
		foreach($tokens as $token) {
			$query = $query.' OR fname like "'.mysqli_real_escape_string($db, $token).'%" OR lname like "'.mysqli_real_escape_string($db, $token).'%" ';
		}
		$query = $query.';';
		$result = mysqli_query($db, $query);
		if(!$result) {
			die("MESSAGE 1: Internal error. Try later.");
		}
		
	}
	else {
		header("Location: home.php");
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
	
<br>
<br>
<h4>Your search resulted in:</h4>
<br>
	 
	 
	 
	 <div class="table-responsive"> 
		<table class="table" id="friend_list">
<?php

	$num = mysqli_num_rows($result);
	
	while($row = mysqli_fetch_assoc($result)) {
		
		$query = 'SELECT * FROM profile_pics WHERE owner ="'.mysqli_real_escape_string($db, $row['emailid']).'";';
		$temp_image = mysqli_query($db, $query);
		if(!$temp_image) {
			die("3. Internal error. Try later.");
		}
		$image_det = mysqli_fetch_assoc($temp_image);
		
		$height = $image_det['height'];
		$width = $image_det['width'];
		
		if($height > $width) {
			$width = intval($width*120.0/$height);
			$height = 120;
		}
		else {
			$height = intval(120.0*$height/$width);
			$width = 120;
		}
		
		
		echo '<tr id="friend_list">';
		echo '<td id="friend_list">';
		echo '<img src="'.$image_det['pro_pic'].'" height="'.$height.'" width="'.$width.'">';
		echo '</td>';
		echo '<td id="friend_list">';
		echo '<a id="friend_list_name" href="viewprofile.php?uid='.$row['user_id'].'">'.$row['fname'].' '.$row['lname'].'</a><br><br>';
		echo '<p>Works at: '.(empty($row['place_of_work'])?("Unavailable"):$row['place_of_work']).'<br>';
		echo 'College: '.(empty($row['college'])?("Unavailable"):$row['college']).'<br>';
		echo 'Lives in: '.(empty($row['location'])?("Unavailable"):$row['location']).'<br></p>';
		echo '</td>';
		echo '</tr>';
	}
	
	if($num == 0) {
		echo "Your search did not give any results.";
	}
?>
	</table>
	</div>
	 
	 
	
	
	<nav class="navbar navbar-default navbar-fixed-bottom">
	<div class="container-fluid" id="footer">
			<?php include('includes/footer.inc.php'); ?>
	</div>
	</nav>
	
	</div>		<!--  end of bootstrap container-fluid class -->
	 
	 
	 
	</body>
	</html>

