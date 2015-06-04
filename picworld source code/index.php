<?php
session_start();
	ini_set('display_errors', true);
error_reporting(E_ALL ^ E_NOTICE);

	if(isset($_SESSION['logged']) && $_SESSION['logged'] == 1) {
		header("Location: profile.php");
		die();
	}
	$title = "PicWorld";
	$mainHeading = "PicWorld";

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $title; ?></title>
	<link rel="stylesheet" type="text/css" href="mycss/mystyle.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<link rel="stylesheet" href="bootstrap/css/bootstrap.css">
	<script src="bootstrap/jquery.min.js"></script>
	<script src="bootstrap/js/bootstrap.js"></script>
</head>

<body>
	<div class="container-fluid">
	
	<div class="row" id="navigation">
	<div class="col-md-1"></div>
	<div class="col-md-3"><span id="picworldHome">PicWorld</span></div>
	<div class="col-md-3"></div>
	<div class="col-md-5">
	<form action="login.php" method="post">
		<label for="username">Email:</label>
		<input type="email" id="username" name="login_username">
		<label for="passwd">Password:</label>
		<input type="password" id="passwd" name="login_passwd">
		
		<input type="submit" id="submitButton" value="Login" name="login">
	</form>
	</div>
	
	</div> <!-- end of navigation div -->
	
	<div class="row" id="mainbody">
	
		<div class="col-md-4">
			<?php
			
			?>
		</div>
		
		<div class="col-md-8" id="registerBlock">
			<h2>Register:</h2>
			<form action="register.php" method="POST">
				
				<input type="text" id="fname" name="fname" placeholder="First Name" size="30pc">
				<input type="text" id="lname" name="lname" placeholder="Last Name" size="30pc">
				<?php
					if(isset($_GET['fname']) && $_GET['fname'] == 1) {
					echo '<br><span class="warning">';
					echo "*First name is mandatory";	
					echo '</span>';				
					}
				?>
				
				<input type="email" id="emailbox" name="emailbox" placeholder="Your email" size="70pc">
				<?php
					if(isset($_GET['emailbox']) && $_GET['emailbox'] == 1) {
					echo '<span class="warning">';
					echo "*Email is mandatory";	
					echo '</span>';				
					}
					
					if(isset($_GET['existing_user']) && $_GET['existing_user'] == 1) {
					echo '<span class="warning">';
					echo "*User already exists";	
					echo '</span>';				
					}
				?>
				<input type="password" id="regpasswd" name="regpasswd" placeholder="Enter your password" size="70pc">
				<?php
					if(isset($_GET['regpasswd']) && $_GET['regpasswd'] == 1) {
					echo '<span class="warning">';
					echo "*Enter your password";	
					echo '</span>';				
					}
				?>
				<input type="password" id="confregpasswd" name="confregpasswd" placeholder="Re-enter your password" size="70pc">
				<?php
					if(isset($_GET['confregpasswd']) && $_GET['confregpasswd'] == 1) {
					echo '<span class="warning">';
					echo "*Confirm your password";	
					echo '</span><br>';				
					}
				?>
				
				<?php
					if(isset($_GET['passwd_mismatch']) && $_GET['passwd_mismatch'] == 1) {
					echo '<span class="warning">';
					echo "*Passwords mismatch";	
					echo '</span><br>';				
					}
				?>
				<input type="date" id="regbirthday" name="regbirthday" placeholder="yyyy-mm-dd">
				<input type="radio" name="regsex" value="MALE" checked>Male
				<input type="radio" name="regsex" id="regsexop"  value="FEMALE">Female
				<br>
				<?php
					if(isset($_GET['regbirthday']) && $_GET['regbirthday'] == 1) {
					echo '<span class="warning">';
					echo "*Enter your birthdate";	
					echo '</span>';				
					}
					
					if(isset($_GET['invalid_date']) && $_GET['invalid_date'] == 1) {
					echo '<span class="warning">';
					echo "*Enter a valid date";	
					echo '</span>';				
					}
					
					if(isset($_GET['regsex']) && $_GET['regsex'] == 1) {
					echo '<span class="warning">';
					echo "*Enter your sex";	
					echo '</span>';				
					}
				?>
				<input type="submit" id="regbutton" name="register" value="Register">
			</form>
		</div>
	
	</div>
	
	
	
	<nav class="navbar navbar-default navbar-fixed-bottom">
	<div class="container-fluid" id="footer">
			<?php include('includes/footer.inc.php'); ?>
	</div>
	</nav>
	
	</div>		<!--  end of bootstrap container-fluid class -->
</body>
</html>
