<?php

include_once 'dbconnect.php';

$page="checkin";


$query = "SELECT * FROM riders WHERE saic_returned = 'No'";

$query = "SELECT a.name, a.plate, a.riderid, a.category, b.sicard_id, b.saic_returned FROM riders a, siacriderid b WHERE a.riderid=b.riderid AND b.saic_returned = 'No' ORDER BY a.plate";
$rider=mysql_query($query);


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
	        </br>
	        </br>
			<center>
			<table id="datatable" class="order-table table">
				<thead>
					<tr><th><center>Plate</th>
						<th><center>Name</th>
						<th><center>RiderID</th>
						<th><center>Category</th>
						<th><center>SAIC</th>
						<th><center>Chip Returned</th>
					</tr>
				</thead>
				


				<?php

				while ( $row = mysql_fetch_array($rider) )
				{
					echo '<tr>';
					echo '<td>'.$row['plate'].'</td>';
					echo '<td><a href="rider_update.php?riderid='.$row['riderid'].'">'.$row['name'].'</a></td>';
					echo '<td>'.$row['riderid'].'</td>';
					echo '<td>'.$row['category'].'</td>';
					echo '<td>'.$row['sicard_id'].'</td>';
					echo '<td>'.$row['saic_returned'].'</td>';
					echo '</tr>';
				}
				
				?>
			</table>

		</form>
	</div>


</body>
</html>