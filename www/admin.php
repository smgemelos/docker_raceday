<?php
session_start();
$page="admin";
#include_once 'dbconnect.php';
#include_once 'awsdbconnect.php';


#if(!($localcesdb = mysql_connect("mysql","root","root",true)))
#{
#     die('oops connection problem ! --> '.mysql_error());
#}
#if(!mysql_select_db("ces",$localcesdb))
#{
#     die('oops database selection problem ! --> '.mysql_error());
#}



if(!($racedb = mysql_connect("mysql","root","root",true)))
{
     die('oops connection problem ! --> '.mysql_error());
}
if(!mysql_select_db("lcsportident_events",$racedb))
{
     die('oops database selection problem ! --> '.mysql_error());
}



function floattime($timestr) {

    if ($timestr == "") {
        return " ";
    } else {
        $timearr = explode('.',$timestr);
        $millsec = $timearr[1];
        $sec = strtotime($timearr[0])-strtotime("00:00:00");
        
        return $sec.".".$millsec;  
    }
}


#$query = "SELECT * FROM lcevents";
$query = "SELECT * FROM status WHERE id=1";
$status=mysql_fetch_array(mysql_query($query,$racedb));


$query = "SELECT * FROM activestages";
$activestages=mysql_query($query,$racedb);


if(isset($_POST['btn-loadreg']))
{

	$raceid = $_POST['event'];

	$query = "DELETE FROM riders";
    mysql_query($query,$racedb);
    $query = "ALTER TABLE riders AUTO_INCREMENT = 1";
    mysql_query($query,$racedb);

    $query = "DELETE FROM categories";
    mysql_query($query,$racedb);
    $query = "ALTER TABLE categories AUTO_INCREMENT = 1";
    mysql_query($query,$racedb);

    $query = "DELETE FROM stamps";
    mysql_query($query,$racedb);
    $query = "ALTER TABLE stamps AUTO_INCREMENT = 1";
    mysql_query($query,$racedb);

    $query = "DELETE FROM lccard_link_stamps";
    mysql_query($query,$racedb);
    $query = "ALTER TABLE lccard_link_stamps AUTO_INCREMENT = 1";
    mysql_query($query,$racedb);

    $query = "DELETE FROM lccards";
    mysql_query($query,$racedb);
    $query = "ALTER TABLE lccards AUTO_INCREMENT = 1";
    mysql_query($query,$racedb);

    $query = "DELETE FROM lcevents";
    mysql_query($query,$racedb);
    $query = "ALTER TABLE lcevents AUTO_INCREMENT = 1";
    mysql_query($query,$racedb);

    $query = "DELETE FROM raceresults";
    mysql_query($query,$racedb);
    $query = "ALTER TABLE raceresults AUTO_INCREMENT = 1";
    mysql_query($query,$racedb);

    $query = "DELETE FROM beacons";
    mysql_query($query,$racedb);
    $query = "ALTER TABLE beacons AUTO_INCREMENT = 1";
    mysql_query($query,$racedb);

    $query = "DELETE FROM siacriderid";
    mysql_query($query,$racedb);
    $query = "ALTER TABLE siacriderid AUTO_INCREMENT = 1";
    mysql_query($query,$racedb);


    $query = "INSERT INTO lcevents (id_event,event_name) VALUES ('$raceid','$raceid') ";
	$rider=mysql_query($query,$racedb);


    // Takes raw data from the request
    $url = 'http://api.californiaenduro.com/raceday/v1/race.php?raceid='.$raceid;
    $json = file_get_contents($url);

    // Converts it into a PHP object
    $data = json_decode($json, true);

    $riders = $data['riders'];
    $categories = $data['categories'];

    $validentries = 0;
    #$values = "";

    foreach($riders as $rider) {

        if  ($rider['plate'] != "0") {

            $name = mysql_real_escape_string($rider['name']);
	        $riderid = $rider['riderid'];
	        $raceid = $rider['raceid'];
	        $plate = $rider['plate'];
	        $category = mysql_real_escape_string($rider['category']);
	        $extras = mysql_real_escape_string($rider['extras']);
            
            $values = $values . "( '".$plate."','".$name."','".$category."','".$riderid."',".$raceid.",'".$extras."'),";
            $validentries = $validentries + 1;

        }
    }
    $value = substr($values, 0, -1);

    if ($validentries > 0) {

        $query = "INSERT INTO riders (`plate`,`name`,`category`,`riderid`,`raceid`,`extras`) VALUES  " . $value;

    	mysql_query($query,$racedb);
    }

	$validentries = 0;
	$maxstages = 0;
    $values = "";

    foreach($categories as $category) {

	    $name = mysql_real_escape_string($category['name']);
	    $sortorder = $category['sortorder'];
	    $cat = mysql_real_escape_string($category['cat']);
	    $gender = $category['gender'];
	    $stages = $category['stages'];

	    if ($stages > $maxstages) {
        	$maxstages = $stages;
        }

	    $values = $values . "( '".$raceid."','".$name."','".$sortorder."','".$gender."','".$stages."'),";
	    $validentries = $validentries + 1; 
    }
    $value = substr($values, 0, -1);

    if ($validentries > 0) {

    	$query = "INSERT INTO categories (`raceid`,`name`,`sortorder`,`gender`,`stages`) VALUES " . $value;

    	mysql_query($query,$racedb);
    }

	for ($i = 1; $i <= 12; ++$i) {
		$sStart = "s" . $i . " Start";
		$sFinish = "s" . $i . " Finish";
		$tpStart = $i;
		$tpFinish = 100+$i;

		if ($i <= $maxstages) {
			$query = "INSERT INTO beacons (name,timepoint,beaconid,seconds) VALUES 
			             ('$sStart','$tpStart','$tpStart',0), 
			             ('$sFinish','$tpFinish','$tpFinish',0)";
		} else {
			$query = "INSERT INTO beacons (name,timepoint,beaconid,seconds) VALUES 
			             ('$sStart','$tpStart',0,0),
			             ('$sFinish','$tpFinish',0,0)";
		}
		mysql_query($query,$racedb);
	}


	header("Location: rider_checkin.php");

}



if(isset($_POST['btn-clearresults']))
{

    $query = "DELETE FROM raceresults";
    mysql_query($query,$racedb);
    $query = "ALTER TABLE raceresults AUTO_INCREMENT = 1";
    mysql_query($query,$racedb);

	header("Location: admin.php");
}


if(isset($_POST['btn-clearstamps']))
{

    $query = "DELETE FROM stamps";
    mysql_query($query,$racedb);
    $query = "ALTER TABLE stamps AUTO_INCREMENT = 1";
    mysql_query($query,$racedb);

	header("Location: admin.php");
}



if(isset($_POST['btn-uploadresults']))
{

	
}


?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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

	<link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
	
	<link rel="stylesheet" href="raceresults.css" type="text/css" />

    <script type="text/javascript" src="js/livetiming.js"></script>
    <script src="js/table-filter.js"></script>

<style>

</style>

	<script>
		$(document).ready(function(){
    		$('[data-toggle="popover"]').popover(); 
		});

		function eventselect() {
			var event = document.getElementById('event').value;
			if (event == "") {
				document.getElementById('btn-loadreg').disabled=true;
			} else {
				document.getElementById('btn-loadreg').disabled=false;
			}
		}

		function complete() {
			if (document.getElementById('racecomplete').checked) {
				document.getElementById('btn-uploadresults').disabled=false;
			} else {
				document.getElementById('btn-uploadresults').disabled=true;
			}
		}

		function clearresults() {
			if (document.getElementById('verifyclearresults').checked) {
				document.getElementById('btn-clearresults').disabled=false;
			} else {
				document.getElementById('btn-clearresults').disabled=true;
			}
		}

		function clearstamps() {
			if (document.getElementById('verifyclearstamps').checked) {
				document.getElementById('btn-clearstamps').disabled=false;
			} else {
				document.getElementById('btn-clearstamps').disabled=true;
			}
		}

	</script>

</head>


<body>

	<?php include 'menu.php'; ?>


	<div id="rider">

		<form method="post" >

			<h4>Load Registration Data </h4>

			<label for="event">Race:</label>
				<select id="event" type="text" name="event" onchange="eventselect()">
					<option value=""></option>

					<?php

					$url = 'http://api.californiaenduro.com/raceday/v1/events.php';
					$json = file_get_contents($url);

					// Converts it into a PHP object
					$data = json_decode($json, true);
					$events = $data['events'];

				    foreach($events as $event) {
						echo '<option value="'.$event['id'].'">'.$event['name'].'</option>';
					}
					?>
				</select>

			<button id="btn-loadreg" name="btn-loadreg" class="btn btn-primary" style="font-size:12px;"  disabled>Load Reg Data</button> 
			</br>
			</br>
			</br>


			<h4>Race Management </h4>


			<label for="racetiming">Racetiming App:</label>
			<input id="racetiming" type="checkbox" name="racetiming" onclick="racetimingaction()" 
					<?php 
					if ($status["racetiming"] == "start") {
						echo "checked> ON";
					} elseif ($status["racetiming"] == "restart") {
						echo "checked> ON";
					} else {
						echo "> OFF";
					}

					?>
			</br> 

			<label for="dbbackup">Race Backup:</label>
			<input id="dbbackup" type="checkbox" name="dbbackup" onclick="dbbackupaction()" 
			        <?php 
					if ($status["mysqlbackup"] == "start") {
						echo "checked> ON";
					} elseif ($status["mysqlbackup"] == "restart") {
						echo "checked> ON";
					} else {
						echo "> OFF";
					}?>  
			</br> 

			<label for="ceslive">CES Live App:</label>
			<input id="ceslive" type="checkbox" name="ceslive" onclick="cesliveaction()" 
					<?php 
					if ($status["ceslive"] == "start") {
						echo "checked> ON";
					} elseif ($status["ceslive"] == "restart") {
						echo "checked> ON";
					} else {
						echo "> OFF";
					}

					?>
			</br> 
			</br> 
		

			<label for="verifyclearresults">Delete Results:</label>
			<input id="verifyclearresults" type="checkbox" name="verifyclearresults" onclick="clearresults()" > 
			</br>
			<button id="btn-clearresults" name="btn-clearresults" class="btn btn-primary" style="font-size:12px;" disabled >Delete Race Results in Database</button></br> 
			</br>
			<label for="verifyclearstamps">Delete Stamps:</label>
			<input id="verifyclearstamps" type="checkbox" name="verifyclearstamps" onclick="clearstamps()" > 
			</br>
			<button id="btn-clearstamps" name="btn-clearstamps" class="btn btn-primary" style="font-size:12px;" disabled >Delete Chip Timestamps in Database</button>
			</br>
			</br>
			</br>
			
					
<!--
			</br>
			</br>
			<h4>Upload Race Results to main CES DB</h4>
			<label for="racecomplete">Race Complete:</label>
			<input id="racecomplete" type="checkbox" name="racecomplete" onclick="complete()" > 
			</br>
			<button id="btn-uploadresults" name="btn-uploadresults" class="btn btn-primary" style="font-size:12px;" disabled>Upload Race Results</button> 
			</br>
-->
			<h4><?php echo $_SESSION['dbselect']; ?></h4>
			</br>
			</br>
			<h4>Use Desktop Script for local race results upload and series results calculation.</h4>
			</br>
			cd c:\Users\smg\Dropbox\CES\Workspace\userRegister\raceday\python</br>
			python loadresultsDB_StagePlacement_StageWins.py 20239 local</br>
			python IndStandings_stage_points.py 16 local</br>
			python TeamStandingsdb.py 16 local</br>
			</br>
			<a href="local_ind_series_results.php">Local Individual Series Results</a>
			</br>
			<a href="local_team_series_results.php">Local Team Series Results</a>
			</br>
			</br>
			<a href="print_local_ind_series_results.php">Print Series Podium Top 5</a>


		</form>

	</div>

</body>
</html>