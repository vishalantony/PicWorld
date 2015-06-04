<?php
session_start();
	ini_set('display_errors', true);
	error_reporting(E_ALL ^ E_NOTICE);
	$title = "PicWorld";
	$mainHeading = "PhotoShare";
	if(isset($_SESSION['logged']) && $_SESSION['logged'] == 1) {
		header("Location: profile.php");
		die();
	}
	
	if(isset($_POST['login']) && $_POST['login'] == 'Login') {
		
		include('includes/config.inc.php');
		
		$username = (isset($_POST['login_username']))?trim($_POST['login_username']):"";
		$passwd = (isset($_POST['login_passwd']))?$_POST['login_passwd']:"";
		
		$query = 'SELECT * FROM user WHERE emailid="'.mysqli_real_escape_string($db, $username).'" and user_passwd=PASSWORD("'.mysqli_real_escape_string($db, $passwd).'");';
		$result = mysqli_query($db, $query);
		
		if(mysqli_connect_errno()) {
			die("PicWorld faced some internal error. Please try after sometime.");
		}
		
		if(mysqli_num_rows($result) > 0) {
			$_SESSION['username'] = $username;
			$_SESSION['logged'] = 1;
			header("Location: profile.php");
		}
		else {
			header("Location: login.php?no_match=1");
		}
		mysqli_free_result($result);
		mysqli_close($db);
		if($_SESSION['logged'] == 1) {
			die();
		}
		//echo $query;
	}
	
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
	<div class="col-md-1"><span id="picworldHome">PicWorld</span></div>
	<div class="col-md-10"></div>
	
	</div> <!-- end of navigation div -->
	
	<div class="row" id="success_message">
		
		<div class="col-md-4">
			
		</div>
		
		<div class="col-md-4" id="login_body">
			<?php
				if(isset($_GET['reg_success']) && $_GET['reg_success'] == 1) {
					echo '<p>You were successfully registered!</p>';
				}
				
				if(isset($_GET['not_logged']) && $_GET['not_logged'] == 1) {
					echo '<p>Please login to continue!</p>';
				}
			?>
		</div>
		
		<div class="col-md-4">
			
		</div>
		
	</div>
	
	<div class="row" id="login_mainbody">
	
		<div class="col-md-4">
			
		</div>
		
		<div class="col-md-4" id="login_body">
			<form action="login.php" method="post">
				<div class="row">
				<div class="col-md-6" id="login_labels"><label for="login_username">Email:</label></div>
				<div class="col-md-6"><input type="email" id="login_username" name ="login_username" size="25pc"></div>
				</div>
				
				<div class="row" id="login_pwd_div">
				<div class="col-md-6" id="login_labels"><label for="login_passwd">Password:</label></div>
				<div class="col-md-6"><input type="password" id="login_passwd" name="login_passwd" size="25pc"></div>
				</div>
				<?php
					if($_GET['no_match'] == 1) {
						echo '<br><span class="warning">';
						echo "*No matching user.";	
						echo '</span>';	
					}
				?>
				
				<input type="submit" name="login" id="login_submitButton" value="Login">
			</form>
		</div>
		
		<div class="col-md-4">
			
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
