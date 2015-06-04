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
	
	
	
	if(!isset($_GET['uid'])) {
		$display_friend_of = $username;
	}
	else {
		$uid = trim($_GET['uid']);
		if(empty($uid)) {
			$display_friend_of = $username;
		}
		else {
			$query = 'SELECT * FROM user WHERE user_id='.$uid.';';
			$resultx = mysqli_query($db, $query);
			if(!$resultx) {
				die("MESSAGE X; Internal error.");
			}
			$rowx = mysqli_fetch_assoc($resultx);
			$display_friend_of = $rowx['emailid'];
		}
	}
	
	$query = 'SELECT fname, lname FROM user WHERE emailid="'.mysqli_real_escape_string($db, $display_friend_of).'";';
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("1. Internal error. Try later.");
	}
	$row = mysqli_fetch_assoc($result);
	$name = $row['fname'].' '.$row['lname'];
	mysqli_free_result($result);
	
	
	
	$query = 'SELECT sentby from friends where acceptedby = "'.mysqli_real_escape_string($db, $display_friend_of).'";';
	//echo $query;
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("1. Internal error. Try later.");
	}
	$num_friends = mysqli_num_rows($result);
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
	

<h3>Friends of <?php echo "$name : "; ?></h3>
<br>
	 <div class="table-responsive"> 
		<table class="table" id="friend_list">
<?php
	while($row = mysqli_fetch_assoc($result)) {
		$query = 'SELECT * FROM user WHERE emailid = "'.mysqli_real_escape_string($db, $row['sentby']).'";';
		$temp_user = mysqli_query($db, $query);
		if(!$temp_user) {
			die("2. Internal error. Try later.");
		}
		$user_det = mysqli_fetch_assoc($temp_user);
		
		$query = 'SELECT * FROM profile_pics WHERE owner ="'.mysqli_real_escape_string($db, $row['sentby']).'";';
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
		echo '<a id="friend_list_name" href="viewprofile.php?uid='.$user_det['user_id'].'">'.$user_det['fname'].' '.$user_det['lname'].'</a><br><br>';
		echo '<p>Works at: '.(empty($user_det['place_of_work'])?("Unavailable"):$user_det['place_of_work']).'<br>';
		echo 'College: '.(empty($user_det['college'])?("Unavailable"):$user_det['college']).'<br>';
		echo 'Lives in: '.(empty($user_det['location'])?("Unavailable"):$user_det['location']).'<br></p>';
		echo '</td>';
		echo '</tr>';
	}
	
	
	$query = 'SELECT acceptedby from friends where sentby = "'.mysqli_real_escape_string($db, $display_friend_of).'";';
	//echo $query;
	$result = mysqli_query($db, $query);
	if(!$result) {
		die("1. Internal error. Try later.");
	}
	$num_friends = mysqli_num_rows($result);
	while($row = mysqli_fetch_assoc($result)) {
		$query = 'SELECT * FROM user WHERE emailid = "'.mysqli_real_escape_string($db, $row['acceptedby']).'";';
		$temp_user = mysqli_query($db, $query);
		if(!$temp_user) {
			die("2. Internal error. Try later.");
		}
		$user_det = mysqli_fetch_assoc($temp_user);
		
		$query = 'SELECT * FROM profile_pics WHERE owner ="'.mysqli_real_escape_string($db, $row['acceptedby']).'";';
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
		echo '<a id="friend_list_name" href="viewprofile.php?uid='.$user_det['user_id'].'">'.$user_det['fname'].' '.$user_det['lname'].'</a><br><br>';
		echo '<p>Works at: '.(empty($user_det['place_of_work'])?("Unavailable"):$user_det['place_of_work']).'<br>';
		echo 'College: '.(empty($user_det['college'])?("Unavailable"):$user_det['college']).'<br>';
		echo 'Lives in: '.(empty($user_det['location'])?("Unavailable"):$user_det['location']).'<br></p>';
		echo '</td>';
		echo '</tr>';
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
	
