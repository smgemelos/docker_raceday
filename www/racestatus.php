<?php

include_once 'dbconnect.php';

$page="racestatus";

$query = "SELECT a.name AS category, count(riderid) AS riderid FROM categories a, riders b WHERE b.category=a.name GROUP BY b.category ORDER BY a.sortorder";
$riders=mysql_query($query);


#$query = "SELECT a.name AS category, count(NULLIF(b.sicard_id,'')) AS chips, count(NULLIF(b.saic_returned,'No')) AS returns FROM categories a, riders b WHERE b.category=a.name GROUP BY b.category ORDER BY a.sortorder";

$query = "SELECT a.category, count(b.riderid) AS chips, count(NULLIF(b.saic_returned,'No')) AS returns FROM riders a, (SELECT * FROM siacriderid GROUP BY riderid) b, categories c WHERE a.riderid=b.riderid AND a.category=c.name GROUP BY a.category ORDER BY c.sortorder";
$chips=mysql_query($query);

$query = "SELECT a.name AS category, count(NULLIF(b.sicard_id,'')) AS total, count(NULLIF(b.dnf,'')) AS dnf, count(NULLIF(b.s1,'')) AS s1, count(NULLIF(b.s2,'')) AS s2, count(NULLIF(b.s3,'')) AS s3, count(NULLIF(b.s4,'')) AS s4, count(NULLIF(b.s5,'')) AS s5, count(NULLIF(b.s6,'')) AS s6, count(NULLIF(b.s7,'')) AS s7, count(NULLIF(b.s8,'')) AS s8, count(NULLIF(b.s9,'')) AS s9, count(NULLIF(b.s10,'')) AS s10 FROM categories a, raceresults b WHERE b.category=a.name  GROUP BY b.category ORDER BY a.sortorder ";
$stages=mysql_query($query);


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

	<div id="results">
		<form method="post" action=" <?php echo $_SERVER['PHP_SELF']; ?>" >
	        <br/>

			<table id="datatable" class="order-table table">
				<thead>
					<tr><th><center>Category</th>
						<th><center>Registered Riders</th>
						<th><center>Chips Assigned</th>
						<th><center>Chips Returned</th>
						<th><center>Stage 1</th>
						<th><center>Stage 2</th>
						<th><center>Stage 3</th>
						<th><center>Stage 4</th>
						<th><center>Stage 5</th>
						<th><center>Stage 6</th>
						<th><center>Stage 7</th>
						<th><center>Stage 8</th>
						<th><center>Stage 9</th>
						<th><center>Stage 10</th>
					</tr>
				</thead>


				<?php

				$i = 0;
				$rider_total = 0;
				$chip_total = 0;
				$return_total = 0;
				$dnf_total = 0;
				$s1_total = 0;
				$s2_total = 0;
				$s3_total = 0;
				$s4_total = 0;
				$s5_total = 0;
				$s6_total = 0;
				$s7_total = 0;
				$s8_total = 0;
				$s9_total = 0;
				$s10_total = 0;


				while ( $rider=mysql_fetch_array($riders) ) {
					$chip = mysql_fetch_array($chips);
					$stage = mysql_fetch_array($stages);

					echo '<tr>';
					echo '<td>'.$rider['category'].'</td>';
					echo '<td>'.$rider['riderid'].'</td>';
					echo '<td>'.$chip['chips'].'</td>';
					echo '<td>'.$chip['returns'].'</td>';
					echo '<td>'.$stage['s1'].'</td>';
					echo '<td>'.$stage['s2'].'</td>';
					echo '<td>'.$stage['s3'].'</td>';
					echo '<td>'.$stage['s4'].'</td>';
					echo '<td>'.$stage['s5'].'</td>';
					echo '<td>'.$stage['s6'].'</td>';
					echo '<td>'.$stage['s7'].'</td>';
					echo '<td>'.$stage['s8'].'</td>';
					echo '<td>'.$stage['s9'].'</td>';
					echo '<td>'.$stage['s10'].'</td>';
					echo '</tr>';

					$rider_total = $rider_total + $rider['riderid'];
					$chip_total = $chip_total + $chip['chips'];
					$return_total = $return_total + $chip['returns'];
					$s1_total = $s1_total + $stage['s1'];
					$s2_total = $s2_total + $stage['s2'];
					$s3_total = $s3_total + $stage['s3'];
					$s4_total = $s4_total + $stage['s4'];
					$s5_total = $s5_total + $stage['s5'];
					$s6_total = $s6_total + $stage['s6'];
					$s7_total = $s7_total + $stage['s7'];
					$s8_total = $s8_total + $stage['s8'];
					$s9_total = $s9_total + $stage['s9'];
					$s10_total = $s10_total + $stage['s10'];
				}

				echo '<tr>';
				echo '<td><b>TOTAL</td>';
				echo '<td><b>'.$rider_total.'</td>';
				echo '<td><b>'.$chip_total.'</td>';
				echo '<td><b>'.$return_total.'</td>';
				echo '<td><b>'.$s1_total.'</td>';
				echo '<td><b>'.$s2_total.'</td>';
				echo '<td><b>'.$s3_total.'</td>';
				echo '<td><b>'.$s4_total.'</td>';
				echo '<td><b>'.$s5_total.'</td>';
				echo '<td><b>'.$s6_total.'</td>';
				echo '<td><b>'.$s7_total.'</td>';
				echo '<td><b>'.$s8_total.'</td>';
				echo '<td><b>'.$s9_total.'</td>';
				echo '<td><b>'.$s10_total.'</td>';
				echo '</tr>';
					

				?>
			</table>

		</form>
	</div>


</body>
</html>