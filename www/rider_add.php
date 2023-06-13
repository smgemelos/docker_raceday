<?php

include_once 'dbconnect.php';

date_default_timezone_set("America/Los_Angeles");
$timestamp = date("Y-m-d H:i:s",time());

$query = "SELECT * FROM categories";
$categories=mysql_query($query);

$query = "SELECT * FROM lcevents";
$events=mysql_query($query);


if(isset($_POST['btn-submit']))
{     

	$name = mysql_real_escape_string($_POST['name']);
	$riderid = mysql_real_escape_string($_POST['riderid']);
	$raceid = mysql_real_escape_string($_POST['raceid']);
	$plate = mysql_real_escape_string($_POST['plate']);
	$category = $_POST['category'];



	if ( ($name == "") || ($riderid == "") || ($raceid == "") || ($plate == "") || ($category == "")  ) {
		?>
        <script>alert('All fields are required to register.  Please complete the form and resubmit.');</script>
        <?php
	}
	else
	{

	 	$query = "INSERT INTO riders (name,riderid,raceid,plate,category) VALUES 
	 			('$name','$riderid','$raceid','$plate','$category')";

		if(mysql_query($query))
	 	{
			header("Location: rider_checkin.php");
	 	}
	 	else
		{
			?>
	        <script>alert('Error while registering - please try again.');</script>
	        <?php
		}
	}	

}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>California Enduro Series: Rider Registration</title>
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
    <script src="js/table-filter.js"></script>

</head>


<body>
	<?php include 'menu.php'; ?>

	<div id="rider">
		<form method="post">

			<label for="name">Name:</label>
			<input type="text" name="name" placeholder="Name" required />
			</br>

			<label for="riderid">CES RiderID:</label>
			<input type="text" name="riderid" placeholder="CES RiderID" required />
			</br>

			<label for="raceid">Race:</label>
				<select id="raceid" type="text" name="raceid" required>
					<option value=""></option>

					<?php

					while ( $row = mysql_fetch_array($events) )  { 
						echo '<option value="'.$row['id_event'].'">'.$row['event_name'].'</option>';
					}
					?>
				</select>
			</br>

			<label for="plate">Plate Number:</label>
			<input type="text" name="plate" placeholder="Plate Number" required />
			</br>

			<label for="category">Category:</label>
				<select id="category" type="text" name="category" required>
					<option value=""></option>

					<?php

					while ( $row = mysql_fetch_array($categories) )  { 
						echo '<option';
						if($category == $row['name']){
							echo " selected ";
						}
						echo ' value="'.$row['name'].'">'.$row['name'].'</option>';
					}
					?>

				</select>
			</br>

			<button type="submit" class="btn btn-primary btn-block" name="btn-submit">SUBMIT</button>
			</br>


		</form>
	</div>

</body>
</html>