<?php
	$host='localhost';
	$user='root';
	$pass='';
	$dbname='picworld';
	
	$title = "photoShare";
	$mainHeading = "PhotoShare";
	
	$db = mysqli_connect($host, $user, $pass, $dbname);
	if(mysqli_connect_errno()) {
		echo "Failed to connect to MySQL: ".mysqli_connect_error();
		die("Failed");
	}
?>
