<?php
session_start();
include_once 'dbconnect.php';

$page="home";

$query = "SELECT * FROM lcevents";
$events=mysql_query($query);

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


<body>

	<?php include 'menu.php'; ?>


	<div id="results">

		<form>

			<h4>Load Registration Data </h4>
			1 - Use Admin page to Load Registration data.
			</br>
			
			<h4>Rider Check-In - Assign SIAC Timing Chips</h4>
			1 - Use the tab <b>Rider Checkin</b> to check-in the riders at the race. </br>
			2 - Use the <b>SEARCH</b> field to find the rider - use <b>Name, Number Plate, or RiderID</b> </br>
			3 - Click the link under <b>Name</b> to add the SAIC Number, and confirm Battery Check and Clear Memory</br>
			4 - Click <b>Submit</b> to save the entry and return to Check-In</br>
			5 - Scan the SAIC to validate the rider details on the Live Timing page </br>
			</br> 
			<b>If there is a new rider add to the check-in list. </b></br>
			1 - Click the tab <b>Rider Checkin</b> </br>
			2 - Click the <b>ADD RIDER</b> button.</br>
			3 - Complete the form - assign an SAIC, confrm Battery Check and Clear Memory.</br>
			4 - Click <b>Submit</b> to save the entry and return to Check-In</br>
			5 - Scan the SAIC to validate the rider details on the Live Timing page </br>
			</br> 
			<h4>Save Rider Check-in to USB for Backup </h4>
			1 - Click the tab <b>Rider Checkin</b> </br>
			2 - Click the <b>Export to CSV</b> to save the table to a file.</br>
			3 - Save it to a USB thumb drive.</br>
			</br> 
			<h3>Race Day</h3>
			STEP 1:  Start SIreader Program</br>
			
			STEP 2:  Start "Race Timing" Script on the desktop</br>
			
			STEP 3:  Start "CES Live" script on the desktop - If there is Internet Access </br>
			</br> 
			<h4>Upload Race Results to main CES DB</h4>
			1 - Use Admin page to Upload Race Results.
			</br>
			</br>

		</form>

	</div>

</body>
</html>