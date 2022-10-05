<?php

if(!mysql_connect("localhost","root","root"))
{
     die('oops connection problem ! --> '.mysql_error());
}
if(!mysql_select_db("ces"))
{
     die('oops database selection problem ! --> '.mysql_error());
}

$page="admin";

$query = "SELECT a.name, a.riderid, a.category, b.sortorder, a.place, a.total, a.r1, a.r2, a.r3, a.r4, a.r5, a.r6, a.r7, a.r8 FROM seriesresults a, category b WHERE a.seriesid=16 AND a.category=b.name ORDER BY b.sortorder, a.place ";
$results=mysql_query($query);


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>California Enduro Series: Indvidual Series Results</title>
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
	<script src="js/excellentexport.js"></script>

</head>



<body>

	<?php include 'menu.php'; ?>
	
	<div id="results">
		<form method="post" action=" <?php echo $_SERVER['PHP_SELF']; ?>" >

	        <a download="registration_<?php echo date("Y_m_d"); ?>.csv" href="#" onclick="return ExcellentExport.csv(this, 'datatable');">Export to CSV</a>
	        <br/>
	        <a href="print_local_ind_series_results.php">Series Podium Top 5</a>
			<center>
			<div align="right">
				Search: <input type="search" class="light-table-filter" data-table="order-table" placeholder=" Filter">
				</br>
				</br>
			</div>

			<table id="datatable" class="order-table table">
				<thead>
					<tr><th><center>Place</th>
						<th><center>Name</th>
						<th><center>RiderID</th>
						<th><center>Category</th>
						<th><center>Total</th>
						<th><center>R1</th>
						<th><center>R2</th>
						<th><center>R3</th>
						<th><center>R4</th>
						<th><center>R5</th>
						<th><center>R6</th>
						<th><center>R7</th>
						<th><center>R8</th></center></tr>
						
				</thead>

				<?php
				$place = 0;
				$category = "PRO MEN";
				while ( $row = mysql_fetch_array($results) )
				{

					echo '<tr>';
					echo '<td>'.$row['place'].'</td>';
					echo '<td>'.$row['name'].'</td>';
					echo '<td>'.$row['riderid'].'</td>';
					echo '<td>'.$row['category'].'</td>';
					echo '<td>'.$row['total'].'</td>';
					echo '<td>'.$row['r1'].'</td>';
					echo '<td>'.$row['r2'].'</td>';
					echo '<td>'.$row['r3'].'</td>';
					echo '<td>'.$row['r4'].'</td>';
					echo '<td>'.$row['r5'].'</td>';
					echo '<td>'.$row['r6'].'</td>';
					echo '<td>'.$row['r7'].'</td>';
					echo '<td>'.$row['r8'].'</td>';
					echo '</tr>';


				}
				
				?>
			</table>

		</form>
	</div>


</body>
</html>