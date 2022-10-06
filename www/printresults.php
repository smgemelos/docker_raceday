<?php

include_once 'dbconnect.php';
date_default_timezone_set("America/Los_Angeles");


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

function timediff($last,$first) {

    if ($last == $first) {
        return " ";
    } else {
        $lastarr = explode('.',$last);
        $firstarr = explode('.',$first);
        $millsec = $lastarr[1] - $firstarr[1];
        $timedelta = strtotime($lastarr[0])-strtotime($firstarr[0]);
        if ($millsec < 0) {
            $millsec = 1000 + $millsec;
            $timedelta = $timedelta - 1;
        }

        if ($millsec < 10){
            $msec = "00".$millsec;
        } elseif ($millsec < 100){
            $msec = "0".$millsec;
        } else {
            $msec = $millsec;
        }

        $sign = "+";
        if ($timedelta < 0){
            #$sign = "-";
            #$timedelta = -$timedelta;
            return "";
        }

        if ($timedelta >= 36000) {
            return $sign.gmdate("G:i:s",$timedelta).".".$msec."";
        } elseif ($timedelta >= 60) {
            return $sign.gmdate("i:s",$timedelta).".".$msec."";
        } else {
            return $sign.gmdate("s",$timedelta).".".$msec."";
        }
        
    }
}


if(isset($_POST['btn-teamresults']))
{

	$cmd = 'python python/Team_RaceDay_Results.py ';

	$output = shell_exec($cmd);

	header("Location: printresults.php");
}





$page="finalresults";

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

#$query = "SELECT a.name, a.plate, a.sicard_id, a.riderid, a.category, b.sortorder, a.ranktotal, a.dnf, a.dq, a.total, a.penalty, a.s1, a.s2, a.s3, a.s4, a.s5, a.s6, a.s7, a.s8, a.s9, a.s10, c.intense FROM raceresults a, categories b, riders c WHERE a.category=b.name AND a.riderid=c.riderid ORDER BY b.sortorder, a.dq, a.dnf, a.ranktotal ";
$query = "SELECT a.name, a.plate, a.sicard_id, a.riderid, a.category, b.sortorder, a.ranktotal, a.dnf, a.dq, a.total, a.ttotal, a.stages, a.penalty, a.s1, a.s2, a.s3, a.s4, a.s5, a.s6, a.s7, a.s8, a.s9, a.s10, a.s11, a.s12 FROM raceresults a, categories b WHERE a.category=b.name ORDER BY b.sortorder, a.dq, a.dnf, a.stages DESC, a.ttotal ";
$results=mysql_query($query);


$query = "SELECT place, teamname, total FROM teamresults";
$teamresults=mysql_query($query);


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

			<button id="btn-teamresults" name="btn-teamresults" class="btn btn-primary" style="font-size:18px;" >Calculate Team Results</button>
			</br>
			</br>

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
						$leadertime = $row['total'];
						$category = $row['category'];
						echo '<tr></tr>';
						echo '<tr><td colspan=5><h4>'.$row['category'].'</h4></tr>';
						echo '<tr><th style="font-size:18px;"><center>Place</cetner></th>';
						echo '<th style="font-size:18px;"><center>Name</cetner></th>';
						echo '<th style="font-size:18px;"><center>Total</cetner></th>';
						echo '<th style="font-size:18px;"><center>Back</cetner></th>';
					}


					$stagescompleted = 0;
					foreach ( $Stages as $value ) {
						if ($row[$value] != "") {
							$stagescompleted++;
						}
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

					if ($place <= 5) {
						echo '<tr>';
						echo '<td style="font-size:18px;">'.$placestr.'</td>';
						echo '<td style="font-size:18px;">'.$row['name'].'</td>';
						echo '<td style="font-size:18px;">'.$row['total'].'</td>';
						echo '<td style="font-size:18px;">'.timediff($row['total'],$leadertime).'</td>';		
						echo '</tr>';
					}


				}


				echo '<tr></tr>';
				echo '<tr><td colspan=4><h4>Team Results</h4></tr>';
				echo '<tr><th style="font-size:18px;"><center>Place</cetner></th>';
				echo '<th style="font-size:18px;"><center>Name</cetner></th>';
				echo '<th style="font-size:18px;"><center>Race Points</cetner></th>';
				echo '</tr>';

				while ( $row = mysql_fetch_array($teamresults) ) 
				{
					if ($row['place'] <= 5) {
						echo '<tr>';
						echo '<td style="font-size:18px;">'.$row['place'].'</td>';
						echo '<td style="font-size:18px;">'.$row['teamname'].'</td>';
						echo '<td style="font-size:18px;">'.$row['total'].'</td>';
						echo '</tr>';
					}
				}
					

				
				?>
			</table>

		</form>
	</div>


</body>
</html>