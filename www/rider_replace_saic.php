<?php

include_once 'dbconnect.php';

$oldsaic = mysql_real_escape_string($_POST['oldsaic']);

if(empty($oldsaic)) {
	header("Location: rider_checkin.php");
} 
else {

	$name = mysql_real_escape_string($_POST['name']);
	$riderid = mysql_real_escape_string($_POST['riderid']);
	$plate = mysql_real_escape_string($_POST['plate']);

}



if(isset($_POST['btn-submit']))
{     
	$oldsaic = mysql_real_escape_string($_POST['oldsaic']);
	$newsaic = mysql_real_escape_string($_POST['sicard_id']);
	$riderid = mysql_real_escape_string($_POST['riderid']);

	if ( ($newsaic == "") || ($oldsaic == "") ) {
		?>
        <script>alert('All fields are required.');</script>
        <?php
	}
	else
	{

	 	$query = "UPDATE riders SET sicard_id='$newsaic' WHERE sicard_id='$oldsaic' " ;
	 	mysql_query($query);

	 	$query = "UPDATE stamps SET stamp_card_id='$newsaic' WHERE stamp_card_id='$oldsaic' " ;
	 	mysql_query($query);

	 	$query = "UPDATE raceresults SET sicard_id='$newsaic' WHERE sicard_id='$oldsaic' " ;
	 	mysql_query($query);

	 	header("Location: rider_update.php?riderid=".$riderid);


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
			<input type="text" name="name" value="<?php echo $name; ?>" required />
			</br>

			<label for="riderid">CES RiderID:</label>
			<input type="text" name="riderid" value="<?php echo $riderid; ?>" required />
			</br>

			<label for="plate">Plate:</label>
			<input type="text" name="plate" value="<?php echo $plate; ?>" required />
			</br>

			<label for="oldsaic">Current SAIC:</label>
			<input type="text" name="oldsaic"  value="<?php echo $oldsaic; ?>" required />
			</br>
			</br>
			<h4>New SAIC Number:</h4>
			</br>
			
			<div id="sicard_id">
				<label for="sicard_id">SAIC Number:</label>
				<input id="sicard_id" type="text" name="sicard_id" placeholder="SAIC Number" value="<?php echo $sicard_id; ?>" required />
			</div>
			</br>

			
			<button type="submit" class="btn btn-primary btn-block" name="btn-submit">SUBMIT</button>
			</br>

		</form>

	</div>

</body>
</html>