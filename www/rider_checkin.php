<?php

include_once 'dbconnect.php';

$page="checkin";


$query = "SELECT * FROM riders ORDER BY plate";

#$query = "SELECT a.*, b.sicard_id FROM riders a LEFT JOIN siacriderid b ON a.riderid=b.riderid GROUP BY a.riderid ORDER BY a.plate";
$rider=mysql_query($query);


$query1 = "SELECT * FROM siacriderid";
$res = mysql_query($query1);

$siacriderid = array();
while ( $row = mysql_fetch_array($res) )  {
	#echo "Riderid: ".$row['riderid']."  SIAC: ".$row['sicard_id']."\n";
	#echo "  Array: ".$siacriderid[$row['riderid']][0]."\n";
	if (array_key_exists($row['riderid'],$siacriderid)) {
		array_push($siacriderid[$row['riderid']],$row['sicard_id']);
	} else {
		$siacriderid[$row['riderid']] = array($row['sicard_id']);
	}
}

#var_dump($siacriderid);


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>California Enduro Series: Rider Registration</title>
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
    <script src="js/table-filter.js"></script>
	
</head>



<body>
	<?php include 'menu.php'; ?>

	<div id="results">
		<form method="post" action=" <?php echo $_SERVER['PHP_SELF']; ?>" >
			<a href="download/downloadriderlist.php">Export to CSV</a>
	        </br>
	        </br>
			<center>
			<div align="right">
				Search: <input type="search" class="light-table-filter" data-table="order-table" placeholder=" Filter">
				<button  id="btn-addrider" name="btn-addrider" class="btn btn-primary" style="float: left; font-size:16px;" formaction="rider_add.php"><strong>Add Rider</strong></button> 
				</br>
				</br>
			</div>

			<table id="datatable" class="order-table table">
				<thead>
					<tr><th><center>Plate</th>
						<th><center>Name</th>
						<th><center>RiderID</th>
						<th><center>RaceID</th>
						<th><center>Category</th>
						<th><center>SAIC</th>
						<th><center><a href="rider_missing_saic.php">Chip Returned</a></th>
					</tr>
				</thead>
				


				<?php

				while ( $row = mysql_fetch_array($rider) )
				{
					echo '<tr>';
					echo '<td>'.$row['plate'].'</td>';
					echo '<td><a href="rider_update.php?riderid='.$row['riderid'].'">'.$row['name'].'</a></td>';
					echo '<td>'.$row['riderid'].'</td>';
					echo '<td>'.$row['raceid'].'</td>';
					echo '<td>'.$row['category'].'</td>';
					echo '<td>';

					if (in_array($row['riderid'], array_keys($siacriderid)) ){
						echo implode(", ",$siacriderid[$row['riderid']]);
					} 
					
					echo '</td>';
					echo '<td>'.$row['saic_returned'].'</td>';
					echo '</tr>';
				}
				
				?>
			</table>

		</form>
	</div>


</body>
</html>