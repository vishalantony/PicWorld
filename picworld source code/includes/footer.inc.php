<?php
	ini_set('display_errors', true);
error_reporting(E_ALL ^ E_NOTICE);
?>
<div id="footer">
	<p>
	&copy;
	<?php
	$libName = "PicWorld Inc.";
	$start_year = 2014;
	ini_set('date.timezone', 'Asia/Kolkata');
	$year = date('Y');
	if($start_year == $year)
		echo " $year ";
	else
		echo " {$start_year}-{$year} ";
	echo $libName;
    ?>
	</p>
</div>
