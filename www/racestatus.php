<?php

include_once 'dbconnect.php';

$page="racestatus";

$query = "SELECT name FROM categories ORDER BY sortorder";
$categories=mysql_query($query);


$query = "SELECT a.name AS category, count(riderid) AS riderid FROM categories a, riders b WHERE b.category=a.name GROUP BY b.category";
$res=mysql_query($query);

$riders = array();
while ( $row = mysql_fetch_array($res) )  {
	$riders[$row['category']] = $row['riderid'];	
}


$query = "SELECT b.category, count(distinct b.riderid) AS riders, count(a.sicard_id) AS chips, count(a.saic_returned) AS returned FROM siacriderid a, riders b WHERE a.riderid=b.riderid GROUP BY b.category";
$res=mysql_query($query);

$chips = array();
while ( $row = mysql_fetch_array($res) )  {
	$chips[$row['category']] = array($row['chips'],$row['returned'],$row['riders']);	
}


$query = "SELECT a.name AS category, count(NULLIF(b.sicard_id,'')) AS total, count(NULLIF(b.dnf,'')) AS dnf, count(NULLIF(b.s1,'')) AS s1, count(NULLIF(b.s2,'')) AS s2, count(NULLIF(b.s3,'')) AS s3, count(NULLIF(b.s4,'')) AS s4, count(NULLIF(b.s5,'')) AS s5, count(NULLIF(b.s6,'')) AS s6, count(NULLIF(b.s7,'')) AS s7, count(NULLIF(b.s8,'')) AS s8, count(NULLIF(b.s9,'')) AS s9, count(NULLIF(b.s10,'')) AS s10, count(NULLIF(b.s11,'')) AS s11, count(NULLIF(b.s12,'')) AS s12 FROM categories a, raceresults b WHERE b.category=a.name GROUP BY b.category";
$res=mysql_query($query);

$stages = array();
while ( $row = mysql_fetch_array($res) )  {
	$stages[$row['category']] = array('total'=>$row['total'],
									  'dnf'=>$row['dnf'],
									  's1'=>$row['s1'],
									  's2'=>$row['s2'],
									  's3'=>$row['s3'],
									  's4'=>$row['s4'],
									  's5'=>$row['s5'],
									  's6'=>$row['s6'],
									  's7'=>$row['s7'],
									  's8'=>$row['s8'],
									  's9'=>$row['s9'],
									  's10'=>$row['s10'],
									  's11'=>$row['s11'],
									  's12'=>$row['s12']);	
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
						<th><center>Checked in Riders</th>
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
						<th><center>Stage 11</th>
						<th><center>Stage 12</th>
					</tr>
				</thead>


				<?php

				$i = 0;
				$rider_total = 0;
				$checkin_total = 0;
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
				$s11_total = 0;
				$s12_total = 0;

				while ( $category=mysql_fetch_array($categories) ) {

					$cat = $category['name'];

					echo '<tr>';
					echo '<td>'.$cat.'</td>';
					if (array_key_exists($cat,$riders)) {
						echo '<td>'.$riders[$cat].'</td>';
					} else {
						echo '<td>0</td>';
					}
					if (array_key_exists($cat,$chips)) {
						echo '<td>'.$chips[$cat][2].'</td>';
						echo '<td>'.$chips[$cat][0].'</td>';
						echo '<td>'.$chips[$cat][1].'</td>';
					} else {
						echo '<td>0</td>';
						echo '<td>0</td>';
						echo '<td>0</td>';
					}
					if (array_key_exists($cat,$stages)) {
						echo '<td>'.$stages[$cat]['s1'].'</td>';
						echo '<td>'.$stages[$cat]['s2'].'</td>';
						echo '<td>'.$stages[$cat]['s3'].'</td>';
						echo '<td>'.$stages[$cat]['s4'].'</td>';
						echo '<td>'.$stages[$cat]['s5'].'</td>';
						echo '<td>'.$stages[$cat]['s6'].'</td>';
						echo '<td>'.$stages[$cat]['s7'].'</td>';
						echo '<td>'.$stages[$cat]['s8'].'</td>';
						echo '<td>'.$stages[$cat]['s9'].'</td>';
						echo '<td>'.$stages[$cat]['s10'].'</td>';
						echo '<td>'.$stages[$cat]['s11'].'</td>';
						echo '<td>'.$stages[$cat]['s12'].'</td>';
						echo '</tr>';

						$rider_total = $rider_total + $riders[$cat];
						$checkin_total = $checkin_total + $chips[$cat][2];
						$chip_total = $chip_total + $chips[$cat][0];
						$return_total = $return_total + $chips[$cat][1];
						$s1_total = $s1_total + $stages[$cat]['s1'];
						$s2_total = $s2_total + $stages[$cat]['s2'];
						$s3_total = $s3_total + $stages[$cat]['s3'];
						$s4_total = $s4_total + $stages[$cat]['s4'];
						$s5_total = $s5_total + $stages[$cat]['s5'];
						$s6_total = $s6_total + $stages[$cat]['s6'];
						$s7_total = $s7_total + $stages[$cat]['s7'];
						$s8_total = $s8_total + $stages[$cat]['s8'];
						$s9_total = $s9_total + $stages[$cat]['s9'];
						$s10_total = $s10_total + $stages[$cat]['s10'];
						$s11_total = $s11_total + $stages[$cat]['s11'];
						$s12_total = $s12_total + $stages[$cat]['s12'];
					} else {
						echo '<td>0</td>';
						echo '<td>0</td>';
						echo '<td>0</td>';
						echo '<td>0</td>';
						echo '<td>0</td>';
						echo '<td>0</td>';
						echo '<td>0</td>';
						echo '<td>0</td>';
						echo '<td>0</td>';
						echo '<td>0</td>';
						echo '<td>0</td>';
						echo '<td>0</td>';
						echo '</tr>';
					}
				}

				echo '<tr>';
				echo '<td><b>TOTAL</td>';
				echo '<td><b>'.$rider_total.'</td>';
				echo '<td><b>'.$checkin_total.'</td>';
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
				echo '<td><b>'.$s11_total.'</td>';
				echo '<td><b>'.$s12_total.'</td>';
				echo '</tr>';
					

				?>
			</table>

		</form>
	</div>


</body>
</html>