<?php
session_start();

$page="admin";

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script src="js/table-filter.js"></script>

	<!-- Latest compiled and minified CSS
	<script src='https://www.google.com/recaptcha/api.js'></script>
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<link rel="stylesheet" href="raceresults.css" type="text/css" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	-->

	<link rel="stylesheet" href="libraries/bootstrap/3.3.6/css/bootstrap.min.css">
	<script src="libraries/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
	<script src="libraries/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	
	<link rel="stylesheet" href="raceresults.css" type="text/css" />
    <script type="text/javascript" src="js/livetiming.js"></script>

	<script>
		$(document).ready(function(){
    		$('[data-toggle="popover"]').popover(); 
		});



		function eventselect() {
			var event = document.getElementById('event').value;

			if (event == "") {
				document.getElementById('btn-loadreg').disabled=true;
			} else {
				document.getElementById('btn-loadreg').disabled=false;
				
			}
		}


	</script>

</head>


<body style="background-color: #fff;">

	<?php include 'menu.php'; ?>



	<?php

	include_once 'dbconnect.php';

	if(isset($_GET['event']))
	{  
		$raceid = mysql_real_escape_string($_GET['event']);

		$cmd = "python python/loadregDB.py ".$raceid;
		#$output = shell_exec($cmd);
		#echo $output;
		exec($cmd,$output);
		foreach ( $output as $line ) {
			echo $line . "<br/>";
		}
	}

	?>

</body>
</html>