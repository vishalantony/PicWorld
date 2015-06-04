<?php
	
	// change the variable values to change the configuration settings.
	// changing the following variables might affect the running of all the codes.
	// please be sure before changing any values.
	
	$host='localhost';
	$user='root';
	$pass='';
	$dbname='picworld';
	
	$db = mysqli_connect($host, $user, $pass);
	if(mysqli_connect_errno()) {
		echo "Failed to connect to MySQL: ".mysqli_connect_error();
		die("Failed");
	}
?>
