<?php
#session_start();
include_once 'dbconnect.php';

$riderid = mysql_real_escape_string($_GET['riderid']);

date_default_timezone_set("America/Los_Angeles");
$timestamp = date("Y-m-d H:i:s",time());

$query5 = "SELECT * FROM riders WHERE riderid='$riderid' ";

#$query5 = "SELECT a.*, b.sicard_id FROM riders a LEFT JOIN siacriderid b ON a.riderid=b.riderid AND a.riderid='$riderid'";


$res=mysql_query($query5);
$rider=mysql_fetch_array($res);

if(empty($rider)) {
	header("Location: rider_checkin.php");
} 
else {
	$id = $rider['id'];
	$name = $rider['name'];
	$plate = $rider['plate'];
	$category = $rider['category'];

	$extras = json_decode($rider['extras'],true);

	$saic_returned = $rider['saic_returned'];
	$emcontact = $rider['emcontact'];
	$emphone = $rider['emphone'];
	$raceid = $rider['raceid'];
}

$query = "SELECT * FROM categories";
$categories=mysql_query($query);

if(isset($_POST['btn-submit']))
{     
	$id = mysql_real_escape_string($_POST['id']);
	$name = mysql_real_escape_string($_POST['name']);
	$riderid = mysql_real_escape_string($_POST['riderid']);
	$plate = mysql_real_escape_string($_POST['plate']);
	$category = $_POST['category'];
	$sicard_id = mysql_real_escape_string($_POST['sicard_id']);
	$saic_returned = 'No';
	
	

	if ( ($name == "") || ($riderid == "") || ($plate == "") || ($category == "") ) {
		?>
        <script>alert('All fields are required to register.');</script>
        <?php
	}
	else
	{

	 	$query = "UPDATE riders SET name='$name', riderid='$riderid', plate='$plate', category='$category' WHERE id=$id " ;

		if(mysql_query($query))  {

			if ($sicard_id != "") {
				$query = "INSERT INTO siacriderid (sicard_id,riderid,raceid,name) VALUES ('$sicard_id','$riderid','$raceid','$name')";
				mysql_query($query);
			}
	 		
	 		$query = "UPDATE raceresults SET name='$name', plate='$plate', category='$category' WHERE riderid='$riderid' " ;
	 		mysql_query($query);
			header("Location: rider_checkin.php");

		} else {
			?>
	        <script>alert('Error while updating - please try again.');</script>
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

			<input id="id" type="hidden" name="id" value="<?php echo $id; ?>">

			<?php
			#echo $_GET['riderid'];
			#echo $query5;

			?>

			<label for="name">Name:</label>
			<input type="text" name="name" placeholder="Rider Name" value="<?php echo $name; ?>" required />
			</br>

			<label for="riderid">CES RiderID:</label>
			<input type="text" name="riderid" placeholder="CES RiderID" value="<?php echo $riderid; ?>" required />
			</br>

			<label for="plate">Plate Number:</label>
			<input type="text" name="plate" placeholder="Plate Number" value="<?php echo $plate; ?>" required />
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
			</br>

			<?php

				if (count($extras) > 0 ){
				
					foreach (array_keys($extras) as $extra) {
						echo '<label>'.$extra.'</label>';
						echo '<font size="4">'.$extras[$extra].'</font>';
						echo '</br>';
					}
				}
				echo '</br>';
				echo '</br>';
			?>

			
			

			<?php
			$i = 1;
			$query = "SELECT * FROM siacriderid WHERE riderid='".$riderid."'";
			$siacrider=mysql_query($query);
			while ( $row = mysql_fetch_array($siacrider) )
			{
				echo '<label>SIAC Number '.$i.":</label>";
				echo $row['sicard_id'];
				echo '</br>';
				$i += 1;
			}
			
			?>

			<div id="sicard_id">
			<label for="sicard_id">SIAC Number:</label>
			<input id="saic" type="text" name="sicard_id" placeholder="SAIC Number" value="<?php echo $sicard_id; ?>" style="width: 200px;" />
			<button class="btn btn-primary btn-block" id="add" type="submit" value="" name="add">ADD TIMING CHIP</button>
			</div>
			<?php
				if (isset($_POST['add'])) {
					$sicard_id   = $_POST['sicard_id'];
					

					$query = "INSERT INTO siacriderid (sicard_id,riderid,raceid,name) VALUES ('$sicard_id','$riderid','$raceid','$name')";

					$_SESSION['query'] = $query;

					mysql_query($query);
					echo "<meta http-equiv='refresh' content='0'>";
					
				}
			?>	
			</br>
			</br>
			</br>

			
			<button type="submit" class="btn btn-primary btn-block" name="btn-submit">SUBMIT</button>
			</br>


		</form>

		<?php

		#if ($sicard_id != ""){



		#	echo '<form method="post" action="rider_replace_saic.php">';
		#	echo '<H4>If timing chip is lost or damaged:</H4></br>';

		#	echo '<input id="oldsaic" type="hidden" name="oldsaic" value="'.$sicard_id.'"  />';
		#	echo '<input id="name" type="hidden" name="name" value="'.$name.'" required />';
		#	echo '<input id="riderid" type="hidden" name="riderid" value="'.$riderid.'" />';
		#	echo '<input id="plate "type="hidden" name="plate" value="'.$plate.'"  />';
		
		#	echo '<button type="submit" class="btn btn-primary btn-block" name="btn-newchip">Assign New Timing Chip</button>';

		#    echo '</form>';

		#}

		

		?>

	</div>

</body>
</html>