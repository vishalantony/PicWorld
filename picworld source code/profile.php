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
	$query = 'SELECT * FROM user WHERE emailid = "'.mysqli_real_escape_string($db, $username).'";';
	$result = mysqli_query($db, $query);
	
	if(!$result) {
		die("PicWorld faced some internal error. Please try after sometime.");
	}
		
	if(mysqli_num_rows($result) == 0) {
		// some error happened.
		session_destroy();
		header("Location: index.php");
	}
	$row = mysqli_fetch_assoc($result);
	mysqli_free_result($result);
	
	
	// fetch profile photo
	
	$query = 'SELECT * FROM profile_pics WHERE owner = "'.mysqli_real_escape_string($db, $username).'";';
	$result = mysqli_query($db, $query);
	
	if(!$result) {
		die("PicWorld faced some internal error. Please try after sometime.");
	}
	
	if(mysqli_num_rows($result) == 0) {
		// some error happened.
		session_destroy();
		header("Location: index.php");
	}
	$row1 = mysqli_fetch_assoc($result);
	mysqli_free_result($result);
	
	//end of fetching profile photo
	
	
	$default_pic = $row1['pro_pic'];
	$default_dps = array('pictures/profile_pictures/default_dp_F.png', 'pictures/profile_pictures/default_dp_M.jpg');
	
	$private = array('fname', 'lname', 'emailid', 'bday', 'sex', 'place_of_work', 'location', 'college', 'description');
	$public = array('fname', 'lname', 'sex', 'place_of_work', 'location', 'college', 'description', 'pro_pic');
	$display_as = array('fname'=>'First Name', 'lname'=>'Last Name', 'emailid'=>'Email ID', 'bday'=>'Date of Birth', 'sex'=>'Gender', 'place_of_work'=>'Workplace', 'location'=>'Location', 'college'=>'College', 'description'=>'Description');
	
	
?>


<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $title; ?></title>
	<link rel="stylesheet" type="text/css" href="mycss/mystyle.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<style>
		
		<?php 
			// calculating the image width and height
			$h = intval($row1['height']);
			$w = intval($row1['width']);
			
			if($h > $w) {
				$w = intval(200.0*$w/$h);
				$h = 200;
			}
			else {
				$h = intval(200.0*$h/$w);
				$w = 200;
			}
		?>
		
		
		#profile_pic {
			height: <?php echo "$h"."px;"; ?>
			width: <?php echo "$w"."px;"; ?>
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
	<div class="col-md-5">
		<form action="search.php" method="get">
		<input type="text" name="search_text" id="search_text" placeholder="Search PicWorld">
		<input type="submit" name="search" id="search_button" value="search">
		</form>
	</div>
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
	
	
	<div class="row" id="error_message">
		<?php
			if(isset($_GET['delete_error']) && $_GET['delete_error'] == 1) {
				echo '<span class="warning">';
				echo "*Some error occurred while deleting your account. Try again later.";	
				echo '</span><br>';	
			}
		?>
	</div>
	
	
	<div class="row" id="dp_row">
	
	<div class="col-md-3"></div>
	<div class="col-md-6" id="display_dp">
	
	<img id="profile_pic" src=<?php echo $row1['pro_pic']; ?> alt="Profile Picture">
	<?php
	if(!in_array($default_pic, $default_dps))
		echo '<br><a id="set_default_dp" href="delete_dp_conf.php">Remove profile picture</a>';
	?>
	</div>
	<div class="col-md-3"></div>
	
	</div>
	
	<br>
	
	<?php
		foreach($row as $key=>$value) {
			if(in_array($key, $private)) {
				echo 	'<div class="row"><div class="col-md-5"></div>
						<div class="col-md-1 key">'.$display_as[$key].'</div>';
				echo	'<div class="col-md-4 value">'.$value.'</div><div class="col-md-2"></div></div>';
			}
		}
	
	?>
	
	<br>
	<br>

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
	
	<div class="row update">
		<div class="col-md-5"></div>
		<div class="col-md-1"><a href="update.php" id="update">Update Profile</a></div>
		<div class="col-md-1"><a href="delete_confirmation.php" id="delete">Delete Profile</a></div>
		<div class="col-md-5"></div>
	</div>
	
	
	
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
