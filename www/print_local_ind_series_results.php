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

$query = "SELECT a.name, a.riderid, a.category, b.sortorder, a.place, a.total, a.r1, a.r2, a.r3, a.r4, a.r5, a.r6, a.r7, a.r8 FROM seriesresults a, category b WHERE a.seriesid=14 AND a.category=b.name ORDER BY b.sortorder, a.place ";
$results=mysql_query($query);

$query = "SELECT a.teamname, a.place, a.total, a.r1, a.r2, a.r3, a.r4, a.r5, a.r6, a.r7, a.r8 FROM teamresults a  WHERE series=14 ORDER BY a.place LIMIT 5";
$teamresults=mysql_query($query);



$page="admin";



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
	<script src="js/excellentexport.js"></script>

</head>



<body>

	<?php include 'menu.php'; ?>
	
	<div id="results">
		<form method="post" action=" <?php echo $_SERVER['PHP_SELF']; ?>" >
	        <a download="RaceResults_<?php echo date("Y_m_d"); ?>.csv" href="#" onclick="return ExcellentExport.csv(this, 'datatable');">Export to CSV</a>
	        <br/>
			<center>
			<div align="right">
				Search: <input type="search" class="light-table-filter" data-table="order-table" placeholder=" Filter">
				</br>
				</br>
			</div>

			<table id="datatable" class="order-table table">

				<?php
				$place = 0;
				$category = "PRO MEN";
				while ( $row = mysql_fetch_array($results) )
				{

					if ($category == $row['category']) {
						$place = $place + 1;
					} else {
						$place = 1;
						$category = $row['category'];
					}


					if ($place == 1) {
						$category = $row['category'];
						echo '<tr></tr>';
						echo '<tr><td colspan=11><h4>'.$row['category'].'</h4></tr>';
						echo '<tr><th style="font-size:18px;"><center>Place</cetner></th>';
						echo '<th style="font-size:18px;"><center>Name</cetner></th>';
						echo '<th style="font-size:18px;"><center>Total</cetner></th>';
						echo '<th style="font-size:18px;"><center>R1</cetner></th>';
						echo '<th style="font-size:18px;"><center>R2</cetner></th>';
						echo '<th style="font-size:18px;"><center>R3</cetner></th>';
						echo '<th style="font-size:18px;"><center>R4</cetner></th>';
						echo '<th style="font-size:18px;"><center>R5</cetner></th>';
						echo '<th style="font-size:18px;"><center>R6</cetner></th>';
						echo '<th style="font-size:18px;"><center>R7</cetner></th>';
						echo '<th style="font-size:18px;"><center>R8</cetner></th></tr>';

					}

					if ($place <= 5) {
						echo '<tr>';
						echo '<td style="font-size:18px;">'.$row['place'].'</td>';
						echo '<td style="font-size:18px;">'.$row['name'].'</td>';
						echo '<td style="font-size:18px;">'.$row['total'].'</td>';
						echo '<td style="font-size:18px;">'.$row['r1'].'</td>';
						echo '<td style="font-size:18px;">'.$row['r2'].'</td>';
						echo '<td style="font-size:18px;">'.$row['r3'].'</td>';
						echo '<td style="font-size:18px;">'.$row['r4'].'</td>';
						echo '<td style="font-size:18px;">'.$row['r5'].'</td>';
						echo '<td style="font-size:18px;">'.$row['r6'].'</td>';
						echo '<td style="font-size:18px;">'.$row['r7'].'</td>';
						echo '<td style="font-size:18px;">'.$row['r8'].'</td>';
						echo '</tr>';
					}


				}

				echo '<tr><td colspan=11><h4>CES Team Competition</h4></tr>';
				echo '<tr><th style="font-size:18px;"><center>Place</cetner></th>';
				echo '<th style="font-size:18px;"><center>Team</cetner></th>';
				echo '<th style="font-size:18px;"><center>Total</cetner></th>';
				echo '<th style="font-size:18px;"><center>R1</cetner></th>';
				echo '<th style="font-size:18px;"><center>R2</cetner></th>';
				echo '<th style="font-size:18px;"><center>R3</cetner></th>';
				echo '<th style="font-size:18px;"><center>R4</cetner></th>';
				echo '<th style="font-size:18px;"><center>R5</cetner></th>';
				echo '<th style="font-size:18px;"><center>R6</cetner></th>';
				echo '<th style="font-size:18px;"><center>R7</cetner></th>';
				echo '<th style="font-size:18px;"><center>R8</cetner></th></tr>';

				while ( $row = mysql_fetch_array($teamresults) ) {
					echo '<tr>';
					echo '<td style="font-size:18px;">'.$row['place'].'</td>';
					echo '<td style="font-size:18px;">'.$row['teamname'].'</td>';
					echo '<td style="font-size:18px;">'.$row['total'].'</td>';
					echo '<td style="font-size:18px;">'.$row['r1'].'</td>';
					echo '<td style="font-size:18px;">'.$row['r2'].'</td>';
					echo '<td style="font-size:18px;">'.$row['r3'].'</td>';
					echo '<td style="font-size:18px;">'.$row['r4'].'</td>';
					echo '<td style="font-size:18px;">'.$row['r5'].'</td>';
					echo '<td style="font-size:18px;">'.$row['r6'].'</td>';
					echo '<td style="font-size:18px;">'.$row['r7'].'</td>';
					echo '<td style="font-size:18px;">'.$row['r8'].'</td>';
					echo '</tr>';
				}

				
				?>
			</table>

		</form>
	</div>


</body>
</html>