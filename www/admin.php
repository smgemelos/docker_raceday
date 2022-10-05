<?php
session_start();
$page="admin";
#include_once 'dbconnect.php';
#include_once 'awsdbconnect.php';

try {

	if(!($cesdb = mysql_connect("cesdb.californiaenduro.com","cesuser","wvG-Tkd-huo-72S")))
	{
	     #die('oops connection problem ! --> '.mysql_error());
	     throw new Exception("CES DB Connction Problem: ".mysql_error());
	}
	if(!mysql_select_db("ces",$cesdb))
	{
	     #die('oops database selection problem ! --> '.mysql_error());
	     throw new Exception("CES DB Selection Problem: ".mysql_error());
	}

} catch(Exception $e) {
	echo '<br><br><h1 style="color:red;">Message: ' .$e->getMessage().'</h1>';
}



if(!($localcesdb = mysql_connect("mysql","root","root",true)))
{
     die('oops connection problem ! --> '.mysql_error());
}
if(!mysql_select_db("ces",$localcesdb))
{
     die('oops database selection problem ! --> '.mysql_error());
}




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
$query = "SELECT * FROM races WHERE active=1";
$events=mysql_query($query,$cesdb);


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


	$query = "SELECT b.name, b.riderid, b.raceid, b.plate, a.emcontact, a.emphone, b.category FROM rider a, raceregistration b WHERE a.riderid=b.riderid AND b.raceid='$raceid' AND b.status='ACTIVE' ORDER BY b.plate";
	
	# Added this query to pull the Intense info
	#$query = "SELECT b.options AS intense, a.*  FROM (select a.emcontact, a.emphone, b.* FROM rider a, raceregistration b WHERE a.riderid=b.riderid AND b.raceid='$raceid') a LEFT JOIN cesaddonpurch b ON a.riderid=b.riderid WHERE a.raceid='$raceid' AND b.raceid='$raceid' AND a.status='ACTIVE' AND b.cesaddonid=13  ORDER BY a.plate";
	$rider=mysql_query($query,$cesdb);

	while ( $row = mysql_fetch_array($rider) )
	{
		$name = mysql_real_escape_string($row['name']);
        $riderid = $row['riderid'];
        $raceid = $row['raceid'];
        $plate = $row['plate'];
        $intense = $row['intense'];
        $emcontact = $row['emcontact'];
        $emphone = $row['emphone'];
        $category = mysql_real_escape_string($row['category']);

        $query = "INSERT INTO riders (plate,name,category,riderid,raceid,intense,emcontact,emphone) VALUES ('$plate','$name','$category','$riderid','$raceid','$intense','$emcontact','$emphone') " ;
        mysql_query($query,$racedb);
	}


	$query = "SELECT * FROM categories WHERE raceid='$raceid' ORDER BY sortorder";
	$cats=mysql_query($query,$cesdb);

	$maxstages = 0;

	while ( $row = mysql_fetch_array($cats) )
	{
		$name = mysql_real_escape_string($row['name']);
        $sortorder = $row['sortorder'];
        $cat = $row['cat'];
        $gender = $row['gender'];
        $stages = $row['stages'];
        if ($stages > $maxstages) {
        	$maxstages = $stages;
        }
        $starttime = $row['STARTTIME'];

        $query = "INSERT INTO categories (raceid,name,sortorder,cat,gender,stages) VALUES ('$raceid','$name','$sortorder','$cat','$gender','$stages') " ;
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



if(isset($_POST['btn-loadteams']))
{

	$raceid = $_POST['event'];

	$query = "DELETE FROM team";
    mysql_query($query,$racedb);
    $query = "ALTER TABLE team AUTO_INCREMENT = 1";
    mysql_query($query,$racedb);

    $query = "DELETE FROM teammember";
    mysql_query($query,$racedb);
    $query = "ALTER TABLE teammember AUTO_INCREMENT = 1";
    mysql_query($query,$racedb);


	$query = "SELECT * FROM team";
	$rider=mysql_query($query,$cesdb);

	while ( $row = mysql_fetch_array($rider) )
	{
		$teamname = mysql_real_escape_string($row['teamname']);
        $teamid = $row['teamid'];
        $captainid = $row['captainid'];
        $regdate = $row['regdate'];

        $query = "INSERT INTO team (teamid,teamname,captainid,regdate) VALUES ('$teamid','$teamname','$captainid','$regdate') " ;
        mysql_query($query,$racedb);
	}


	$query = "SELECT * FROM teammember";
	$cats=mysql_query($query,$cesdb);

	while ( $row = mysql_fetch_array($cats) )
	{
		
		$riderid = $row['riderid'];
		$ridername = mysql_real_escape_string($row['ridername']);
        $teamid = $row['teamid'];
        $teamname = mysql_real_escape_string($row['teamname']);
        $adddate = $row['adddate'];
        $dropdate = $row['dropdate'];

        $query = "INSERT INTO teammember (riderid,ridername,teamid,teamname,adddate,dropdate) VALUES ('$riderid','$ridername','$teamid','$teamname','$adddate','$dropdate') " ;
        mysql_query($query,$racedb);
	}



	header("Location: rider_checkin.php");
}




if(isset($_POST['btn-uploadresults']))
{

	$_SESSION['dbselect'] = $_POST['localdb'];

	$query = "SELECT id_event FROM lcevents";
	$row = mysql_fetch_array(mysql_query($query,$racedb));
	$raceid = $row['id_event'];

	$_SESSION['raceid'] = $raceid;

	$catStages = array();
	$Stages = ['s1','s2','s3','s4','s5','s6','s7','s8','s9','s10','s11','s12'];

	$query = "SELECT * FROM categories ORDER BY sortorder DESC ";
	$categories = mysql_query($query,$racedb);

	while ( $row = mysql_fetch_array($categories) )
	{
		$name = mysql_real_escape_string($row['name']);
		$stages = $row['stages'];
		$catStages[$name] = $stages;
	}

	$query = "DELETE FROM raceresults WHERE raceid='$raceid' ";
	mysql_query($query,$cesdb);
    $_SESSION['dbselect'] = "aws";    

	$query = "SELECT a.*, b.sortorder FROM raceresults a, categories b WHERE a.category=b.name ORDER BY b.sortorder, a.dq, a.dnf, a.ranktotal ";
	$results=mysql_query($query,$racedb);

    $query = "SELECT DISTINCT(`raceid`) AS raceid from raceresults " ;
    $ids = mysql_query($query,$racedb);
    $count = mysql_num_rows($ids);


    if ($count == 1) 
    {

        $place = 0;
		$currentcat = "PRO MEN";
		while ( $row = mysql_fetch_array($results) )
		{
			$riderid = $row['riderid'];
			$category = mysql_real_escape_string($row['category']);
			$name = mysql_real_escape_string($row['name']);
			$totaltime = $row['totaltime'];
			$penalty = $row['penalty'];
			$s1 = $row['s1'];
			$s2 = $row['s2'];
			$s3 = $row['s3'];
			$s4 = $row['s4'];
			$s5 = $row['s5'];
			$s6 = $row['s6'];
			$s7 = $row['s7'];
			$s8 = $row['s8'];
			$s9 = $row['s9'];
			$s10 = $row['s10'];
			$s11 = $row['s11'];
			$s12 = $row['s12'];
			$t1 = (!in_array($row['t1'],array("",0.0)) and !in_array($row['t101'],array("",0.0)) ? $row['t101'] - $row['t1'] : 0.0);
			$t2 = (!in_array($row['t2'],array("",0.0)) and !in_array($row['t102'],array("",0.0)) ? $row['t102'] - $row['t2'] : 0.0);
			$t3 = (!in_array($row['t3'],array("",0.0)) and !in_array($row['t103'],array("",0.0)) ? $row['t103'] - $row['t3'] : 0.0);
			$t4 = (!in_array($row['t4'],array("",0.0)) and !in_array($row['t104'],array("",0.0)) ? $row['t104'] - $row['t4'] : 0.0);
			$t5 = (!in_array($row['t5'],array("",0.0)) and !in_array($row['t105'],array("",0.0)) ? $row['t105'] - $row['t5'] : 0.0);
			$t6 = (!in_array($row['t6'],array("",0.0)) and !in_array($row['t106'],array("",0.0)) ? $row['t105'] - $row['t6'] : 0.0);
			$t7 = (!in_array($row['t7'],array("",0.0)) and !in_array($row['t107'],array("",0.0)) ? $row['t107'] - $row['t7'] : 0.0);
			$t8 = (!in_array($row['t8'],array("",0.0)) and !in_array($row['t108'],array("",0.0)) ? $row['t108'] - $row['t8'] : 0.0);
			$t9 = (!in_array($row['t9'],array("",0.0)) and !in_array($row['t109'],array("",0.0)) ? $row['t109'] - $row['t9'] : 0.0);
			$t10 = (!in_array($row['t10'],array("",0.0)) and !in_array($row['t110'],array("",0.0)) ? $row['t110'] - $row['t10'] : 0.0);
			$t11 = (!in_array($row['t11'],array("",0.0)) and !in_array($row['t111'],array("",0.0)) ? $row['t111'] - $row['t11'] : 0.0);
			$t12 = (!in_array($row['t12'],array("",0.0)) and !in_array($row['t112'],array("",0.0)) ? $row['t112'] - $row['t12'] : 0.0);
			$ttotal = $row['ttime'];


			$stagescompleted = 0;
			foreach ( $Stages as $value ) {
				if ($row[$value] != "") {
					$stagescompleted++;
				}
			}

			if ($category == $currentcat) {
				$place = $place + 1;
			} else {
				$place = 1;
				$currentcat = $category;
			}

			$_SESSION['raceid'] = $category;

			$placestr = $place;

			if ( $stagescompleted < $catStages[$row['category']] ) {
				$total = "DNF";
				$placestr  = "998";
			} elseif ( $row['dnf'] == "Y" ) {
				$total = "DNF";
				$placestr  = "998";
			} elseif ( $row['dq'] == "Y" ) {
				$total = "DSQ";
				$placestr  = "999";
			} else {
				$total = $row['total'];
				$placestr = $place;
			}

            #print "Category: %s,  Place: %s, Total: %s" % (category,placestr,total)
            

            $query = "INSERT INTO raceresults (`raceid`,`riderid`,`category`,`name`,`place`,`totaltime`,`penalty`,".
                     "`s1`,`s2`,`s3`,`s4`,`s5`,`s6`,`s7`,`s8`,`s9`,`s10`,`s11`,`s12`,".
                     "`t1`,`t2`,`t3`,`t4`,`t5`,`t6`,`t7`,`t8`,`t9`,`t10`,`t11`,`t12`,`ttotal`,`stages`) ".
                     "VALUES ('$raceid','$riderid','$category','$name','$placestr','$total','$penalty',".
                     "'$s1','$s2','$s3','$s4','$s5','$s6','$s7','$s8','$s9','$s10','$s11','$s12',".
                     "'$t1','$t2','$t3','$t4','$t5','$t6','$t7','$t8','$t9','$t10','$t11','$t12',".
                     "'$ttotal','$stagescompleted')";

            
			mysql_query($query,$cesdb);
		    $_SESSION['dbselect'] = "AWS DB Upload Complete";

        }
    }  else  {
    	$_SESSION['raceid'] = "ERROR: ".$count." RaceIDs found in RaceResults table - there should only be 1." ;
    }

	header("Location: admin.php");
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
	
	<link rel="stylesheet" href="raceresults.css" type="text/css" />
    <script type="text/javascript" src="js/livetiming.js"></script>
    <script src="js/table-filter.js"></script>


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

					while ( $row = mysql_fetch_array($events) )  { 
						#echo '<option value="'.$row['id_event'].'">'.$row['event_name'].'</option>';
						echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
					}
					?>
				</select>

			<button id="btn-loadreg" name="btn-loadreg" class="btn btn-primary" style="font-size:12px;"  disabled>Load Reg Data</button> 
			</br>
			</br>
			</br>

			<button id="btn-loadteams" name="btn-loadteams" class="btn btn-primary" style="font-size:12px;" >Load Team Data</button> 
			</br>
			</br>
			</br>

			<h4>Race Management </h4>

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
			

			<h4>Upload Race Results to main CES DB</h4>
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