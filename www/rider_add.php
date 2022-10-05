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
	$sicard_id = mysql_real_escape_string($_POST['sicard_id']);
	$battery = $_POST['battery']=="on" ? 'Yes' : 'No';


	if ( ($name == "") || ($riderid == "") || ($raceid == "") || ($plate == "") || ($sicard_id == "") || ($category == "") || ($battery == "No") ) {
		?>
        <script>alert('All fields are required to register.  Please complete the form and resubmit.');</script>
        <?php
	}
	else
	{

	 	$query = "INSERT INTO riders (name,riderid,raceid,plate,category,sicard_id,battery_check,saic_returned) VALUES 
	 			('$name','$riderid','$raceid','$plate','$category','$sicard_id','$battery','No')";

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

	<script>
	    var ajax_saic = function() {

	    	var saic = document.getElementById('saic').value;
	    	var timestamp = "<?php echo $timestamp ?>"

			if (saic == "") {
				if (window.XMLHttpRequest) {
	                // code for IE7+, Firefox, Chrome, Opera, Safari
	                xmlhttp = new XMLHttpRequest();
	            } else {
	                // code for IE6, IE5
	                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	            }
	            xmlhttp.onreadystatechange = function() {
	                if (this.readyState == 4 && this.status == 200) {
	                    document.getElementById("sicard_id").innerHTML = this.responseText;
	                }
	            };
	            

	            xmlhttp.open("GET","ajaxridersaic.php?timestamp="+timestamp,true);
	            xmlhttp.send();
			}
            
        }

        var interval = 1000; //number of milliseconds between refreshes

        setInterval(ajax_saic, interval);
	</script>

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

			<div id="sicard_id">
				<label for="sicard_id">SAIC Number:</label>
				<input id="saic" type="text" name="sicard_id" placeholder="SAIC Number" value="<?php echo $sicard_id; ?>" required />
			</div>
			</br>
			<label for="battery">SAIC Battery Check:</label>
			<input type="checkbox" name="battery" 
				<?php echo ($battery=="Yes" ? 'checked' : '');?> 
				required > 
			</br>
			</br>

			
			<button type="submit" class="btn btn-primary btn-block" name="btn-submit">SUBMIT</button>
			</br>


		</form>
	</div>

</body>
</html>