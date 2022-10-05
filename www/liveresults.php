<?php

include_once 'dbconnect.php';

$page="liveresults";


$query = "SELECT a.name, a.riderid, a.category, b.sortorder, a.ranktotal, a.total, a.s1, a.s2, a.s3, a.s4, a.s5, a.s6, a.s7, a.s8 FROM raceresults a, categories b WHERE a.category=b.name ORDER BY b.sortorder DESC, a.ranktotal ";
$results=mysql_query($query);


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

</head>



<body>

	<?php include 'menu.php'; ?>
	
	<div id="results">
		<form method="post" action=" <?php echo $_SERVER['PHP_SELF']; ?>" >
			<center>
			<div align="right">
				Search: <input type="search" class="light-table-filter" data-table="order-table" placeholder=" Filter">
				</br>
				</br>
			</div>

			<table class="order-table table">
				<thead>
					<tr><th><center>Name</th>
						<th><center>Category</th>
						<th><center>Total</th>
						<th><center>S1 Time</th>
						<th><center>S2 Time</th>
						<th><center>S3 Time</th>
						<th><center>S4 Time</th>
					</tr>
				</thead>

				<?php

				while ( $row = mysql_fetch_array($results) )
				{
					echo '<tr>';
					echo '<td>'.$row['name'].'</td>';
					echo '<td>'.$row['category'].'</td>';
					echo '<td>'.$row['total'].'</td>';
					echo '<td>'.$row['s1'].'</td>';
					echo '<td>'.$row['s2'].'</td>';
					echo '<td>'.$row['s3'].'</td>';
					echo '<td>'.$row['s4'].'</td>';
					echo '</tr>';
				}
				
				?>
			</table>

		</form>
	</div>


</body>
</html>