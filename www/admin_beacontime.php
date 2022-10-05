<?php
session_start();
$page="admin";

if(!($racedb = mysql_connect("localhost","root","root",true)))
{
     die('oops connection problem ! --> '.mysql_error());
}
if(!mysql_select_db("lcsportident_events",$racedb))
{
     die('oops database selection problem ! --> '.mysql_error());
}



date_default_timezone_set("America/Los_Angeles");

$query = "SELECT max(stages) FROM categories";
$maxstages = mysql_fetch_row(mysql_query($query,$racedb))[0];


$query = "SELECT * FROM beacons";

$beacons=mysql_query($query,$racedb);




?>


<script>

function calcS1() {
	var ts = document.getElementById('t1').value;
	var tf = document.getElementById('t11').value;

	if ((ts != "") && (tf != "")) {
		var time1 = new Date('1970-01-01T' + ts + 'Z').getTime() / 1000;
		var time2 = new Date('1970-01-01T' + tf + 'Z').getTime() / 1000;
	
		timedelta = time2-time1;
		document.getElementById('s1').value = timedelta;
	} else {
		document.getElementById('s1').value = "";
	}

}

</script>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>California Enduro Series: Active Categories</title>
	<script src="js/table-filter.js"></script>

	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<link rel="stylesheet" href="raceresults.css" type="text/css" />

	<!-- jQuery library -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>

	<!-- Latest compiled JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>

<body>
	<?php include 'menu.php'; ?>

	<div id="categories" style="width: 900px; background: #f4f7f8; border-radius: 8px; overflow-x:auto; margin: 80px auto; padding: 10px 10px 10px 10px;" >

		<h3>Do NOT delete the Race Results.</h3>
		<h3>1 - Enter the beacon correcton time IN SECONDS. </h3>
		<h3>2 - THEN stop and restart the RaceTiming cmd line.</h3>
		<h3><br></h3>

		<table id="datatable" class="order-table table">
			<thead>
				<tr>
					<th><center>Beacon Time</th>
					<th><center>Paper Time</th>
					<th><center>Difference</th>
				</tr>
			</thead>
			
			<tr>
				<td><input id="t1" type="text" name="t1" value="" style="width: 10em" onchange="calcS1()"  /></td>
				<td><input id="t11" type="text" name="t11" value="" style="width: 10em" onchange="calcS1()" /></td>
				<td><input id="s1" type="text" name="s1" value=""  style="width: 10em"/></td>
			</tr>
		</table>


		<form method="post"   >
			<center>
			<table>
			<tr><td></td>
				<td>Time Point</td>
				<td>TP ID</td>
				<td>Beacon ID</td>
				<td>Time Adjust (sec)</td>
				<td>Update</td>
			<?php
			$i = 0;
			while ( $row = mysql_fetch_array($beacons) )
			{
				echo '<tr>';
				echo '<td><input type="hidden" value="'.$row['id'].'" name="id'.$i.'" style="width: 2em" /> <input type="hidden" value="'.$row['timepoint'].'" name="tp'.$i.'" style="width: 2em;" /></td>';
				echo '<td>'.$row['name'].'</td>';
			
				echo '<td>'.$row['timepoint'].'</td>';
				echo '<td><input type="text" value="'.$row['beaconid'].'" name="bid'.$i.'" style="width: 8em; text-align: right;" /></td>';
				
				echo '<td><input type="text" value="'.$row['seconds'].'" name="ta'.$i.'" style="width: 10em; text-align: right;" required></td>';


				echo '<td><input type="submit" value="UPDATE" name="update'.$i.'" />';
					if (isset($_POST['update'.$i])) {
						$id  = $_POST['id'.$i];
						$tp = "t".mysql_real_escape_string($_POST['tp'.$i]);
						$bid = mysql_real_escape_string($_POST['bid'.$i]);
						$ta  = mysql_real_escape_string($_POST['ta'.$i]);

						$query = "UPDATE beacons SET beaconid='$bid', seconds='$ta' WHERE id='$id'";
						mysql_query($query);

						$query = "UPDATE raceresults SET ".$tp."='0.0'";
						mysql_query($query);

						echo "<meta http-equiv='refresh' content='0'>";
						
					}
				echo '</td>';
				echo '</tr>';

				$i++;
			}


			echo '</table>';
			?>
			</br>
			</br>
		

		</form>
	</div>

</body>
</html>