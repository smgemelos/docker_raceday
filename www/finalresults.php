<?php

include_once 'dbconnect.php';

$page="finalresults";

function formattime($timestr) {

    if ($timestr == "") {
        return " ";
    } else {
        $timearr = explode('.',$timestr);
        $millsec = $timearr[1];
        $sec = strtotime($timearr[0])-strtotime("00:00:00");

        if ($sec >= 36000) {
            return gmdate("G:i:s",$sec).".".$millsec;
        } elseif ($sec >= 60) {
            return gmdate("i:s",$sec).".".$millsec;
        } else {
            return gmdate("s",$sec).".".$millsec;
        }
        
    }
}


$catStages = array();
$maxStages = 0;
$Stages = ['s1','s2','s3','s4','s5','s6','s7','s8','s9','s10','s11','s12'];

$query = "SELECT * FROM categories ORDER BY sortorder DESC ";
$categories = mysql_query($query);

while ( $row = mysql_fetch_array($categories) )
{
	$name = $row['name'];
	$stages = $row['stages'];
	if ($stages > $maxStages) {
		$maxStages = $stages;
	}	
	$catStages[$name] = $stages;
}

#$query = "SELECT a.name, a.plate, a.sicard_id, a.riderid, a.category, b.sortorder, a.ranktotal, a.dnf, a.dq, a.total, a.ttotal, a.stages, a.penalty, a.s1, a.s2, a.s3, a.s4, a.s5, a.s6, a.s7, a.s8, a.s9, a.s10 FROM raceresults a, categories b WHERE a.category=b.name ORDER BY b.sortorder, a.dq, a.dnf, a.ranktotal ";
$query = "SELECT a.name, a.plate, a.sicard_id, a.riderid, a.category, b.sortorder, a.ranktotal, a.dnf, a.dq, a.total, a.ttotal, a.stages, a.penalty, a.s1, a.s2, a.s3, a.s4, a.s5, a.s6, a.s7, a.s8, a.s9, a.s10,a.s11,a.s12 FROM raceresults a, categories b WHERE a.category=b.name ORDER BY b.sortorder, a.dq, a.dnf, a.stages DESC, a.ttotal ";
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
	<script src="js/excellentexport.js"></script>

</head>



<body>

	<?php include 'menu.php'; ?>
	
	<div id="results">
		<form method="post" action=" <?php echo $_SERVER['PHP_SELF']; ?>" >
	        <a download="RaceResults_<?php echo date("Y_m_d"); ?>.csv" href="#" onclick="return ExcellentExport.csv(this, 'datatable');">Export to CSV</a>
	        <br/>
	        <br/>
	        <a href="printresults.php">Podium Top 5</a>
			<center>
			<div align="right">
				Search: <input type="search" class="light-table-filter" data-table="order-table" placeholder=" Filter">
				</br>
				</br>
			</div>

			<table id="datatable" class="order-table table">
				<thead>
					<tr><th><center>Place</th>
						<th><center>Plate</th>
						<th><center>Name</th>
						<th><center>RiderID</th>
						<th><center>Category</th>
						<th><center>Total</th>
						<th><center>DSQ</th>
						<th><center>Penalty</th>
						<?php
						for ($i = 0; $i < $maxStages; $i++) {
							echo '<th><center>'.$Stages[$i].' Time</th>';
						}
						?>
				</thead>

				<?php
				$place = 0;
				$category = "PRO MEN";
				while ( $row = mysql_fetch_array($results) )
				{
					$stagescompleted = 0;
					foreach ( $Stages as $value ) {
						if ($row[$value] != "") {
							$stagescompleted++;
						}
					}

					#if ( ($row['s1'] == "") or ($row['s2'] == "") or ($row['s3'] == "") or ($row['s4'] == "") ) {
					#	$total = "DNF";
					#} else {
					#	$total = $row['total'];
					#}

					if ($category == $row['category']) {
						$place = $place + 1;
					} else {
						$place = 1;
						$category = $row['category'];
					}

					$placestr = $place;

					if ( $stagescompleted < $catStages[$row['category']] ) {
						$placestr  = "DNF";
					} elseif ( $row['dnf'] == "Y" ) {
						$placestr  = "DNF";
					} elseif ( $row['dq'] == "Y" ) {
						$placestr = "DSQ";
					} else {
						$placestr = $placestr;
					}

					echo '<tr>';
					echo '<td>'.$placestr.'</td>';
					echo '<td>'.$row['plate'].'</td>';
					echo '<td><a href="correct_time.php?riderid='.$row['riderid'].'">'.$row['name'].'</a></td>';
					echo '<td>'.$row['riderid'].'</td>';
					echo '<td>'.$row['category'].'</td>';
					echo '<td>'.$row['total'].'</td>';
					echo '<td>'.$row['dq'].'</td>';
					echo '<td>'.$row['penalty'].'</td>';
					for ($i = 0; $i < $maxStages; $i++) {
						echo '<td>'.$row[$Stages[$i]].'</td>';
					}
					echo '</tr>';


				}
				
				?>
			</table>

		</form>
	</div>


</body>
</html>