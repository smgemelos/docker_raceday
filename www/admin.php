<?php
session_start();
$page="admin";
include_once 'dbconnect.php';
#include_once 'awsdbconnect.php';


#if(!($localcesdb = mysql_connect("mysql","root","root",true)))
#{
#     die('oops connection problem ! --> '.mysql_error());
#}
#if(!mysql_select_db("ces",$localcesdb))
#{
#     die('oops database selection problem ! --> '.mysql_error());
#}



#if(!($racedb = mysql_connect("mysql","root","root",true)))
#{
#     die('oops connection problem ! --> '.mysql_error());
#}
#if(!mysql_select_db("lcsportident_events",$racedb))
#{
#     die('oops database selection problem ! --> '.mysql_error());
#}


function callAPI($method, $url, $data){
   $curl = curl_init();
   switch ($method){
      case "POST":
         curl_setopt($curl, CURLOPT_POST, 1);
         if ($data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
         break;
      case "PUT":
         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
         if ($data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
         break;
      default:
         if ($data)
            $url = sprintf("%s?%s", $url, http_build_query($data));
   }
   // OPTIONS:
   curl_setopt($curl, CURLOPT_URL, $url);
   curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
   ));
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
   // EXECUTE:
   $result = curl_exec($curl);
   if(!$result){die("Connection Failure");}
   curl_close($curl);
   return $result;
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
$status=mysql_fetch_array(mysql_query($query));


$query = "SELECT * FROM activestages";
$activestages=mysql_query($query);


if(isset($_POST['btn-loadreg']))
{

	$raceid = $_POST['event'];

	$query = "DELETE FROM riders";
    mysql_query($query);
    $query = "ALTER TABLE riders AUTO_INCREMENT = 1";
    mysql_query($query);

    $query = "DELETE FROM categories";
    mysql_query($query);
    $query = "ALTER TABLE categories AUTO_INCREMENT = 1";
    mysql_query($query);

    $query = "DELETE FROM stamps";
    mysql_query($query);
    $query = "ALTER TABLE stamps AUTO_INCREMENT = 1";
    mysql_query($query);

    $query = "DELETE FROM lccard_link_stamps";
    mysql_query($query);
    $query = "ALTER TABLE lccard_link_stamps AUTO_INCREMENT = 1";
    mysql_query($query);

    $query = "DELETE FROM lccards";
    mysql_query($query);
    $query = "ALTER TABLE lccards AUTO_INCREMENT = 1";
    mysql_query($query);

    $query = "DELETE FROM lcevents";
    mysql_query($query);
    $query = "ALTER TABLE lcevents AUTO_INCREMENT = 1";
    mysql_query($query);

    $query = "DELETE FROM raceresults";
    mysql_query($query);
    $query = "ALTER TABLE raceresults AUTO_INCREMENT = 1";
    mysql_query($query);

    $query = "DELETE FROM beacons";
    mysql_query($query);
    $query = "ALTER TABLE beacons AUTO_INCREMENT = 1";
    mysql_query($query);

    $query = "DELETE FROM siacriderid";
    mysql_query($query);
    $query = "ALTER TABLE siacriderid AUTO_INCREMENT = 1";
    mysql_query($query);


    $query = "INSERT INTO lcevents (id_event,event_name) VALUES ('$raceid','$raceid') ";
	$rider=mysql_query($query);


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

    	mysql_query($query);
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

    	mysql_query($query);
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
		mysql_query($query);
	}


	#header("Location: rider_checkin.php");

}



if(isset($_POST['btn-clearresults']))
{

    $query = "DELETE FROM raceresults";
    mysql_query($query);
    $query = "ALTER TABLE raceresults AUTO_INCREMENT = 1";
    mysql_query($query);

	header("Location: admin.php");
}


if(isset($_POST['btn-clearstamps']))
{

    $query = "DELETE FROM stamps";
    mysql_query($query);
    $query = "ALTER TABLE stamps AUTO_INCREMENT = 1";
    mysql_query($query);

	header("Location: admin.php");
}


if(isset($_POST['racetiming']))
{
    if ($_POST['racetiming'] == "ON") {
    	if ($_SESSION['racetiming'] == "OFF") {
    		$query = "UPDATE status SET racetiming='restart' WHERE  id=1";
			mysql_query($query);
			$_SESSION['racetiming'] = "ON";
    	}
    }
    if ($_POST['racetiming'] == "OFF") {
    	$query = "UPDATE status SET racetiming='stop' WHERE  id=1";
		mysql_query($query);
		$_SESSION['racetiming'] = "OFF";
    }
	header("Location: admin.php");
}


if(isset($_POST['ceslive']))
{
    if ($_POST['ceslive'] == "ON") {
    	if ($_SESSION['ceslive'] == "OFF") {
    		$query = "UPDATE status SET ceslive='restart' WHERE  id=1";
			mysql_query($query);
			$_SESSION['ceslive'] = "ON";
    	}	
    }
    if ($_POST['ceslive'] == "OFF") {
    	$query = "UPDATE status SET ceslive='stop' WHERE  id=1";
		mysql_query($query);
		$_SESSION['ceslive'] = "OFF";
    }
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
						echo '<option value="'.$event['id'].'">'.$event['name'].$event['id'].'</option>';
					}
					?>
				</select>

			<button id="btn-loadreg" name="btn-loadreg" class="btn btn-primary" style="font-size:12px;"  disabled>Load Reg Data</button> 
			</br>
			</br>
			</br>


			<h4>Race Management </h4>
			</br>


			<label for="racetiming">Racetiming App:</label>
			<select id="racetiming" type="text" name="racetiming" onchange="this.form.submit()" >
					<option value="ON" <?php if ($status["racetiming"] == "start") { echo "selected"; $_SESSION['racetiming'] = "ON";  } ?> >ON</option>
					<option value="OFF" <?php if ($status["racetiming"] == "stop") { echo "selected"; $_SESSION['racetiming'] = "OFF";} ?> >OFF</option>
			</select>

			<label for="ceslive">CES Live App:</label>
			<select id="ceslive" type="text" name="ceslive" onchange="this.form.submit()" >
					<option value="ON" <?php if ($status["ceslive"] == "start") { echo "selected"; $_SESSION['ceslive'] = "ON"; } ?> >ON</option>
					<option value="OFF" <?php if ($status["ceslive"] == "stop") { echo "selected"; $_SESSION['ceslive'] = "OFF"; } ?> >OFF</option>
			</select>
			</br> 
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
			
					

			</br>
			</br>
			<h4>Upload Race Results to main CES DB</h4>
			<?php 
			  echo $_SESSION['uploadResponse']."</br>";
			  $_SESSION['uploadResponse'] = "";
			?>
			<label for="racecomplete">Race Complete:</label>
			<input id="racecomplete" type="checkbox" name="racecomplete" onclick="complete()" > 
			</br>
			<button id="btn-uploadresults" name="btn-uploadresults" class="btn btn-primary" style="font-size:12px;" disabled>Upload Race Results</button> 
			</br>


			<h4><?php echo $_SESSION['dbselect']; ?></h4>
			</br>
			</br>
			<h4>Use Desktop Script for local race results upload and series results calculation.</h4>
			</br>
			cd c:\Users\smg\Dropbox\CES\Workspace\userRegister\raceday\python</br>
			python3 loadresultsDB_StagePlacement_StageWins.py 20239 local</br>
			python3 IndStandings_stage_points.py 16 local</br>
			python3 TeamStandingsdb.py 16 local</br>
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