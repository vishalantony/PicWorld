<?php
	session_start();
		
	ini_set('display_errors', true);
	error_reporting(E_ALL ^ E_NOTICE);
	
		if(!isset($_SESSION['logged']) || $_SESSION['logged'] != 1) {
			header("Location: login.php?not_logged=1");
			die();
		}
?>

<!DOCTYPE html>
<html>
	<head></head>
	<body>
		<p>
<?php
	echo "Are you sure you want to delete your profile picture?<br>"
?>
</p>
<p><a href="dp_set_default.php">YES</a></p>
<p><a href="profile.php">NO</a></p>
</body>
</html>

