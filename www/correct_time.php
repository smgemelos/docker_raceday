<?php

include_once 'dbconnect.php';
date_default_timezone_set("UTC");

$riderid = mysql_real_escape_string($_GET['riderid']);

$query = "SELECT * FROM raceresults WHERE riderid='$riderid' ";

$res=mysql_query($query);
$rider=mysql_fetch_array($res);

$query1 = "SELECT * FROM siacriderid WHERE riderid='$riderid'";
$res = mysql_query($query1);
$siacriderid = array();
while ( $row = mysql_fetch_array($res) )  {
	array_push($siacriderid,$row['sicard_id']);
}

#$query = "SELECT * FROM stamps WHERE stamp_card_id='$sicard_id' ";
#$stamps=mysql_query($query);


if(empty($rider)) {
	header("Location: finalresults.php");
} 

$query = "SELECT * FROM categories WHERE name='".$rider["category"]."'";
$res=mysql_query($query);
$category=mysql_fetch_array($res);
$stages = $category['stages'];


function time2sec($time){
	sscanf($time, "%d:%d:%f", $hours, $minutes, $seconds);
	$seconds = ($hours) * 3600 + $minutes * 60 + $seconds;
	return $seconds;
}

if(isset($_POST['btn-submit']))
{     

	$t1 = time2sec(mysql_real_escape_string($_POST['t1']));
	$t101 = time2sec(mysql_real_escape_string($_POST['t101']));
	$s1 = mysql_real_escape_string($_POST['s1']);

	$t2 = time2sec(mysql_real_escape_string($_POST['t2']));
	$t102 = time2sec(mysql_real_escape_string($_POST['t102']));
	$s2 = mysql_real_escape_string($_POST['s2']);

	$t3 = time2sec(mysql_real_escape_string($_POST['t3']));
	$t103 = time2sec(mysql_real_escape_string($_POST['t103']));
	$s3 = mysql_real_escape_string($_POST['s3']);

	$t4 = time2sec(mysql_real_escape_string($_POST['t4']));
	$t104 = time2sec(mysql_real_escape_string($_POST['t104']));
	$s4 = mysql_real_escape_string($_POST['s4']);

	$t5 = time2sec(mysql_real_escape_string($_POST['t5']));
	$t105 = time2sec(mysql_real_escape_string($_POST['t105']));
	$s5 = mysql_real_escape_string($_POST['s5']);

	$t6 = time2sec(mysql_real_escape_string($_POST['t6']));
	$t106 = time2sec(mysql_real_escape_string($_POST['t106']));
	$s6 = mysql_real_escape_string($_POST['s6']);

	$t7 = time2sec(mysql_real_escape_string($_POST['t7']));
	$t107 = time2sec(mysql_real_escape_string($_POST['t107']));
	$s7 = mysql_real_escape_string($_POST['s7']);

	$t8 = time2sec(mysql_real_escape_string($_POST['t8']));
	$t108 = time2sec(mysql_real_escape_string($_POST['t108']));
	$s8 = mysql_real_escape_string($_POST['s8']);

	$t9 = time2sec(mysql_real_escape_string($_POST['t9']));
	$t109 = time2sec(mysql_real_escape_string($_POST['t109']));
	$s9 = mysql_real_escape_string($_POST['s9']);

	$t10 = time2sec(mysql_real_escape_string($_POST['t10']));
	$t110 = time2sec(mysql_real_escape_string($_POST['t110']));
	$s10 = mysql_real_escape_string($_POST['s10']);

	$t11 = time2sec(mysql_real_escape_string($_POST['t11']));
	$t111 = time2sec(mysql_real_escape_string($_POST['t111']));
	$s11 = mysql_real_escape_string($_POST['s11']);

	$t12 = time2sec(mysql_real_escape_string($_POST['t12']));
	$t112 = time2sec(mysql_real_escape_string($_POST['t112']));
	$s12 = mysql_real_escape_string($_POST['s12']);

	$p = mysql_real_escape_string($_POST['penalty']);
	$tp = mysql_real_escape_string($_POST['tpenalty']);

	$total = mysql_real_escape_string($_POST['totaltime']);
	$ttotal = mysql_real_escape_string($_POST['ttotaltime']);
	$ranktotal = mysql_real_escape_string($_POST['ranktotal']);
	$stages = mysql_real_escape_string($_POST['stages']);

	$sicard_id = mysql_real_escape_string($_POST['sicard_id']);

	$dnf = $_POST['dnf']=='Y' ? 'Y' : 'N';

#	if ($_POST['dnf']=='Y') {
#		$dnf = 'Y';
#		$s1 = ($s1 == "") ? "00:00:00" : $s1;
#		$s2 = ($s2 == "") ? "00:00:00" : $s2;
#		$s3 = ($s3 == "") ? "00:00:00" : $s3;
#		$s4 = ($s4 == "") ? "00:00:00" : $s4;
#		$s5 = ($s5 == "") ? "00:00:00" : $s5;
#		$s6 = ($s6 == "") ? "00:00:00" : $s6;
#		$s7 = ($s7 == "") ? "00:00:00" : $s7;
#		$s8 = ($s8 == "") ? "00:00:00" : $s8;
#	} else {
#		$dnf = '';
#		$s1 = ($s1 == "00:00:00") ? '' : $s1;
#		$s2 = ($s2 == "00:00:00") ? '' : $s2;
#		$s3 = ($s3 == "00:00:00") ? '' : $s3;
#		$s4 = ($s4 == "00:00:00") ? '' : $s4;
#		$s5 = ($s5 == "00:00:00") ? '' : $s5;
#		$s6 = ($s6 == "00:00:00") ? '' : $s6;
#		$s7 = ($s7 == "00:00:00") ? '' : $s7;
#		$s8 = ($s8 == "00:00:00") ? '' : $s8;
#	}

	$dq = $_POST['dq']=='Y' ? 'Y' : 'N';
	
	
	$query = "UPDATE raceresults SET ".
	                "t1='$t1',t2='$t2',t3='$t3',t4='$t4',t5='$t5',t6='$t6',".
	                "t7='$t7',t8='$t8',t9='$t9',t10='$t10',t11='$t11',t12='$t12',".
			    "t101='$t101',t102='$t102',t103='$t103',t104='$t104',t105='$t105',t106='$t106',".
			    "t107='$t107',t108='$t108',t109='$t109',t110='$t110',t111='$t111',t112='$t112',".
			    "s1='$s1',s2='$s2',s3='$s3',s4='$s4',s5='$s5',s6='$s6',".
			    "s7='$s7',s8='$s8',s9='$s9',s10='$s10',s11='$s11',s12='$s12', ".
			    "penalty='$p',tpenalty='$tp',total='$total',ttotal='$ttotal',".
			    "stages='$stages',ranktotal='$ranktotal',dnf='$dnf', dq='$dq' ".
			    "WHERE riderid='$riderid' " ;

	$_SESSION['query'] = $query;


	if(mysql_query($query))
 	{
		header("Location: finalresults.php");
 	}
 	else
	{
		?>
        <script>alert('Error while updating - please try again.');</script>
        <?php
	}

}

?>

<script>

function sformat(s) {
      var fm = [
            Math.floor(s / 60 / 60) % 24, // HOURS
            Math.floor(s / 60) % 60, // MINUTES
            (s % 60).toFixed(3) // SECONDS
      ];
      return $.map(fm, function(v, i) { return ((v < 10) ? '0' : '') + v; }).join(':');
}

function calcTotal() {
	var s1 = document.getElementById('s1').value;
	var s2 = document.getElementById('s2').value;
	var s3 = document.getElementById('s3').value;
	var s4 = document.getElementById('s4').value;
	var s5 = document.getElementById('s5').value;
	var s6 = document.getElementById('s6').value;
	var s7 = document.getElementById('s7').value;
	var s8 = document.getElementById('s8').value;
	var s9 = document.getElementById('s9').value;
	var s10 = document.getElementById('s10').value;
	var s11 = document.getElementById('s11').value;
	var s12 = document.getElementById('s12').value;
	var p  = document.getElementById('penalty').value;
	var tp = document.getElementById('tpenalty').value;
	var stages = document.getElementById('stages').value;

	var newstages = 0
	
	var s1time = (s1 == "") ? 0 : new Date('1970-01-01T' + s1 + 'Z').getTime() / 1000;
	var s1rank = (s1 == "") ? 14400 : s1time;
	if (s1 != "") { newstages = newstages + 1; }

	var s2time = (s2 == "") ? 0 : new Date('1970-01-01T' + s2 + 'Z').getTime() / 1000;
	var s2rank = (s2 == "") ? 14400 : s2time;
	if (s2 != "") { newstages = newstages + 1; }

	var s3time = (s3 == "") ? 0 : new Date('1970-01-01T' + s3 + 'Z').getTime() / 1000;
	var s3rank = (s3 == "") ? 14400 : s3time;
	if (s3 != "") { newstages = newstages + 1; }

	var s4time = (s4 == "") ? 0 : new Date('1970-01-01T' + s4 + 'Z').getTime() / 1000;
	var s4rank = (s4 == "") ? 14400 : s4time;
	if (s4 != "") { newstages = newstages + 1; }

	var s5time = (s5 == "") ? 0 : new Date('1970-01-01T' + s5 + 'Z').getTime() / 1000;
	var s5rank = (s5 == "") ? 14400 : s5time;
	if (s5 != "") { newstages = newstages + 1; }

	var s6time = (s6 == "") ? 0 : new Date('1970-01-01T' + s6 + 'Z').getTime() / 1000;
	var s6rank = (s6 == "") ? 14400 : s6time;
	if (s6 != "") { newstages = newstages + 1; }

	var s7time = (s7 == "") ? 0 : new Date('1970-01-01T' + s7 + 'Z').getTime() / 1000;
	var s7rank = (s7 == "") ? 14400 : s7time;
	if (s7 != "") { newstages = newstages + 1; }

	var s8time = (s8 == "") ? 0 : new Date('1970-01-01T' + s8 + 'Z').getTime() / 1000;
	var s8rank = (s8 == "") ? 14400 : s8time;
	if (s8 != "") { newstages = newstages + 1; }

	var s9time = (s9 == "") ? 0 : new Date('1970-01-01T' + s9 + 'Z').getTime() / 1000;
	var s9rank = (s9 == "") ? 14400 : s9time;
	if (s9 != "") { newstages = newstages + 1; }

	var s10time = (s10 == "") ? 0 : new Date('1970-01-01T' + s10 + 'Z').getTime() / 1000;
	var s10rank = (s10 == "") ? 14400 : s10time;
	if (s10 != "") { newstages = newstages + 1; }

	var s11time = (s11 == "") ? 0 : new Date('1970-01-01T' + s11 + 'Z').getTime() / 1000;
	var s11rank = (s11 == "") ? 14400 : s11time;
	if (s11 != "") { newstages = newstages + 1; }

	var s12time = (s12 == "") ? 0 : new Date('1970-01-01T' + s12 + 'Z').getTime() / 1000;
	var s12rank = (s12 == "") ? 14400 : s12time;
	if (s12 != "") { newstages = newstages + 1; }

	var ptime = (p == "") ? 0 : new Date('1970-01-01T' + p + 'Z').getTime() / 1000;
	var prank = (p == "") ? 0 : ptime;


	
	ttotaltime = s1time+s2time+s3time+s4time+s5time+s6time+s7time+s8time+s9time+s10time+s11time+s12time+ptime;
	totaltime = sformat(s1time+s2time+s3time+s4time+s5time+s6time+s7time+s8time+s9time+s10time+s11time+s12time+ptime);
	ranktotal = s1rank+s2rank+s3rank+s4rank+s5rank+s6rank+s7rank+s8rank+s9rank+s10rank+s11rank+s12rank+prank;

	document.getElementById('totaltime').value = totaltime;
	document.getElementById('ranktotal').value = ranktotal;
	document.getElementById('ttotaltime').value = ttotaltime;
	document.getElementById('tpenalty').value = ptime;
	document.getElementById('stages').value = newstages;
}




function calcS1() {
	var ts = document.getElementById('t1').value;
	var tf = document.getElementById('t101').value;

	if ((ts != "") && (tf != "")) {
		var time1 = new Date('1970-01-01T' + ts + 'Z').getTime() / 1000;
		var time2 = new Date('1970-01-01T' + tf + 'Z').getTime() / 1000;
	
		timedelta = sformat(time2-time1);
		document.getElementById('s1').value = timedelta;
	} else {
		document.getElementById('s1').value = "";
	}
	calcTotal()
}

function calcS2() {
	var ts = document.getElementById('t2').value;
	var tf = document.getElementById('t102').value;

	if ((ts != "") && (tf != "")) {
		var time1 = new Date('1970-01-01T' + ts + 'Z').getTime() / 1000;
		var time2 = new Date('1970-01-01T' + tf + 'Z').getTime() / 1000;
	
		timedelta = sformat(time2-time1);
		document.getElementById('s2').value = timedelta;
	} else {
		document.getElementById('s2').value = "";
	}
	calcTotal()
}

function calcS3() {
	var ts = document.getElementById('t3').value;
	var tf = document.getElementById('t103').value;

	if ((ts != "") && (tf != "")) {
		var time1 = new Date('1970-01-01T' + ts + 'Z').getTime() / 1000;
		var time2 = new Date('1970-01-01T' + tf + 'Z').getTime() / 1000;
	
		timedelta = sformat(time2-time1);
		document.getElementById('s3').value = timedelta;
	} else {
		document.getElementById('s3').value = "";
	}
	calcTotal()
}

function calcS4() {
	var ts = document.getElementById('t4').value;
	var tf = document.getElementById('t104').value;

	if ((ts != "") && (tf != "")) {
		var time1 = new Date('1970-01-01T' + ts + 'Z').getTime() / 1000;
		var time2 = new Date('1970-01-01T' + tf + 'Z').getTime() / 1000;
	
		timedelta = sformat(time2-time1);
		document.getElementById('s4').value = timedelta;
	} else {
		document.getElementById('s4').value = "";
	}
	calcTotal()
}

function calcS5() {
	var ts = document.getElementById('t5').value;
	var tf = document.getElementById('t105').value;

	if ((ts != "") && (tf != "")) {
		var time1 = new Date('1970-01-01T' + ts + 'Z').getTime() / 1000;
		var time2 = new Date('1970-01-01T' + tf + 'Z').getTime() / 1000;
	
		timedelta = sformat(time2-time1);
		document.getElementById('s5').value = timedelta;
	} else {
		document.getElementById('s5').value = "";
	}
	calcTotal()
}

function calcS6() {
	var ts = document.getElementById('t6').value;
	var tf = document.getElementById('t106').value;

	if ((ts != "") && (tf != "")) {
		var time1 = new Date('1970-01-01T' + ts + 'Z').getTime() / 1000;
		var time2 = new Date('1970-01-01T' + tf + 'Z').getTime() / 1000;
	
		timedelta = sformat(time2-time1);
		document.getElementById('s6').value = timedelta;
	} else {
		document.getElementById('s6').value = "";
	}
	calcTotal()
}

function calcS7() {
	var ts = document.getElementById('t7').value;
	var tf = document.getElementById('t107').value;

	if ((ts != "") && (tf != "")) {
		var time1 = new Date('1970-01-01T' + ts + 'Z').getTime() / 1000;
		var time2 = new Date('1970-01-01T' + tf + 'Z').getTime() / 1000;
	
		timedelta = sformat(time2-time1);
		document.getElementById('s7').value = timedelta;
	} else {
		document.getElementById('s7').value = "";
	}
	calcTotal()
}

function calcS8() {
	var ts = document.getElementById('t8').value;
	var tf = document.getElementById('t108').value;

	if ((ts != "") && (tf != "")) {
		var time1 = new Date('1970-01-01T' + ts + 'Z').getTime() / 1000;
		var time2 = new Date('1970-01-01T' + tf + 'Z').getTime() / 1000;
	
		timedelta = sformat(time2-time1);
		document.getElementById('s8').value = timedelta;
	} else {
		document.getElementById('s8').value = "";
	}
	calcTotal()
}

function calcS9() {
	var ts = document.getElementById('t9').value;
	var tf = document.getElementById('t109').value;

	if ((ts != "") && (tf != "")) {
		var time1 = new Date('1970-01-01T' + ts + 'Z').getTime() / 1000;
		var time2 = new Date('1970-01-01T' + tf + 'Z').getTime() / 1000;
	
		timedelta = sformat(time2-time1);
		document.getElementById('s9').value = timedelta;
	} else {
		document.getElementById('s9').value = "";
	}
	calcTotal()
}

function calcS10() {
	var ts = document.getElementById('t10').value;
	var tf = document.getElementById('t110').value;

	if ((ts != "") && (tf != "")) {
		var time1 = new Date('1970-01-01T' + ts + 'Z').getTime() / 1000;
		var time2 = new Date('1970-01-01T' + tf + 'Z').getTime() / 1000;
	
		timedelta = sformat(time2-time1);
		document.getElementById('s10').value = timedelta;
	} else {
		document.getElementById('s10').value = "";
	}
	calcTotal()
}

function calcS11() {
	var ts = document.getElementById('t11').value;
	var tf = document.getElementById('t111').value;

	if ((ts != "") && (tf != "")) {
		var time1 = new Date('1970-01-01T' + ts + 'Z').getTime() / 1000;
		var time2 = new Date('1970-01-01T' + tf + 'Z').getTime() / 1000;
	
		timedelta = sformat(time2-time1);
		document.getElementById('s11').value = timedelta;
	} else {
		document.getElementById('s11').value = "";
	}
	calcTotal()
}

function calcS12() {
	var ts = document.getElementById('t12').value;
	var tf = document.getElementById('t112').value;

	if ((ts != "") && (tf != "")) {
		var time1 = new Date('1970-01-01T' + ts + 'Z').getTime() / 1000;
		var time2 = new Date('1970-01-01T' + tf + 'Z').getTime() / 1000;
	
		timedelta = sformat(time2-time1);
		document.getElementById('s12').value = timedelta;
	} else {
		document.getElementById('s12').value = "";
	}
	calcTotal()
}
</script>


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
		<form method="post"   style="width: 800px;">

			<table>
				<thead>
					<tr><th><center>Rider</th>
						<?php

						foreach ($siacriderid as $siacid) {
							echo '<th><center>'.$siacid.'</th>';
						}

						?>
					</tr>
				</thead>
				<tr>
					<td style="text-align:left">

						<input id="sicard_id" type="hidden" name="sicard_id" value="<?php echo $sicard_id; ?>" />

						<label for="name">Name:</label>
						<?php echo $rider['name']; ?>
						</br>

						<label for="riderid">CES RiderID:</label>
						<?php echo $rider['riderid']; ?>
						</br>

						<label for="plate">Plate Number:</label>
						<?php echo $rider['plate']; ?>
						</br>

						<label for="category">Category:</label>
						<?php echo $rider['category']; ?>
						</br>
						<label for="stages">Stages:</label>
						<?php echo $stages; ?>
						</br>
<!--
						<label for="sicard_id">SIcardID:</label>
						<?php echo $sicard_id; ?>
						</br>
-->
					</td>

					<?php

					foreach ($siacriderid as $siacid) {

						echo '<td style="text-align:left">';
						$query = "SELECT * FROM stamps WHERE stamp_card_id='$siacid' AND stamp_control_mode != 7 GROUP BY stamp_control_code, stamp_punch_datetime ORDER BY id_stamp";
						$stamps=mysql_query($query);
						while ( $row = mysql_fetch_array($stamps) ) {
							$ts = explode(" ",$row['stamp_punch_datetime']);
							$ms = str_pad ($row['stamp_punch_ms'], 3,$pad_string = "0",$pad_type = STR_PAD_LEFT);
							echo 'Tp:'.$row["stamp_control_code"];
							#echo ' Mode: '.$row["stamp_control_mode"];
							echo ' Ts: '.$ts[1].'.'.$ms.'</br>';
						}
						echo '</td>';
					}

					?>

				<!--
					<td style="text-align:left">
						<?php

							while ( $row = mysql_fetch_array($stamps) )
							{
								$ts = explode(" ",$row['stamp_punch_datetime']);
								$ms = str_pad ($row['stamp_punch_ms'], 3,$pad_string = "0",$pad_type = STR_PAD_LEFT);
								echo 'Beacon: '.$row['stamp_control_code'].' Mode: '.$row['stamp_control_mode'].' Timestamp: '.$ts[1].'.'.$ms.'</br>';
							}
						?>

					</td>
				-->

			</table>
			</br>
			
			<table id="datatable" class="order-table table">
				<thead>
					<tr><th><center>Stage <?php echo $_SESSION['query'] ?></th>
						<th><center>Start Time</th>
						<th><center>Finish Time</th>
						<th><center>Stage Time</th>
					</tr>
				</thead>
				
				<tr>
					<td>Stage 1</td>
					<td><input id="t1" type="text" name="t1" value="<?php if ($rider['t1']==0) { echo "";} else { $time = explode(".", $rider['t1']); $time[1] = $time[1] ?: '0'; echo date("H:i:s",$time[0]).".".$time[1];} ?>" onchange="calcS1()"  /></td>
					<td><input id="t101" type="text" name="t101" value="<?php if ($rider['t101']==0) { echo "";} else { $time = explode(".", $rider['t101']); echo date("H:i:s",$time[0]).".".$time[1];} ?>" onchange="calcS1()" /></td>
					<td><input id="s1" type="text" name="s1" value="<?php echo $rider['s1']; ?>" /></td>
				</tr>
				<tr>
					<td>Stage 2</td>
					<td><input id="t2" type="text" name="t2" value="<?php if ($rider['t2']==0) { echo "";} else { $time = explode(".", $rider['t2']); $time[1] = $time[1] ?: '0'; echo date("H:i:s",$time[0]).".".$time[1];} ?>" onchange="calcS2()"  /></td>
					<td><input id="t102" type="text" name="t102" value="<?php if ($rider['t102']==0) { echo "";} else { $time = explode(".", $rider['t102']); echo date("H:i:s",$time[0]).".".$time[1];} ?>" onchange="calcS2()" /></td>
					<td><input id="s2" type="text" name="s2" value="<?php echo $rider['s2']; ?>" /></td>
				</tr>
				<tr>
					<td>Stage 3</td>
					<td><input id="t3" type="text" name="t3" value="<?php if ($rider['t3']==0) { echo "";} else { $time = explode(".", $rider['t3']); $time[1] = $time[1] ?: '0'; echo date("H:i:s",$time[0]).".".$time[1];} ?>" onchange="calcS3()"  /></td>
					<td><input id="t103" type="text" name="t103" value="<?php if ($rider['t103']==0) { echo "";} else { $time = explode(".", $rider['t103']); echo date("H:i:s",$time[0]).".".$time[1];} ?>" onchange="calcS3()" /></td>
					<td><input id="s3" type="text" name="s3" value="<?php echo $rider['s3']; ?>" /></td>
				</tr>
				<tr>
					<td>Stage 4</td>
					<td><input id="t4" type="text" name="t4" value="<?php if ($rider['t4']==0) { echo "";} else { $time = explode(".", $rider['t4']); $time[1] = $time[1] ?: '0'; echo date("H:i:s",$time[0]).".".$time[1];} ?>" onchange="calcS4()"  /></td>
					<td><input id="t104" type="text" name="t104" value="<?php if ($rider['t104']==0) { echo "";} else { $time = explode(".", $rider['t104']); echo date("H:i:s",$time[0]).".".$time[1];} ?>" onchange="calcS4()" /></td>
					<td><input id="s4" type="text" name="s4" value="<?php echo $rider['s4']; ?>" /></td>
				</tr>
				<tr>
					<td>Stage 5</td>
					<td><input id="t5" type="text" name="t5" value="<?php if ($rider['t5']==0) { echo "";} else { $time = explode(".", $rider['t5']); $time[1] = $time[1] ?: '0'; echo date("H:i:s",$time[0]).".".$time[1];} ?>" onchange="calcS5()"  /></td>
					<td><input id="t105" type="text" name="t105" value="<?php if ($rider['t105']==0) { echo "";} else { $time = explode(".", $rider['t105']); echo date("H:i:s",$time[0]).".".$time[1];} ?>" onchange="calcS5()" /></td>
					<td><input id="s5" type="text" name="s5" value="<?php echo $rider['s5']; ?>" /></td>
				</tr>
				<tr>
					<td>Stage 6</td>
					<td><input id="t6" type="text" name="t6" value="<?php if ($rider['t6']==0) { echo "";} else { $time = explode(".", $rider['t6']); $time[1] = $time[1] ?: '0'; echo date("H:i:s",$time[0]).".".$time[1];} ?>" onchange="calcS6()"  /></td>
					<td><input id="t106" type="text" name="t106" value="<?php if ($rider['t106']==0) { echo "";} else { $time = explode(".", $rider['t106']); echo date("H:i:s",$time[0]).".".$time[1];} ?>" onchange="calcS6()" /></td>
					<td><input id="s6" type="text" name="s6" value="<?php echo $rider['s6']; ?>" /></td>
				</tr>
				<tr>
					<td>Stage 7</td>
					<td><input id="t7" type="text" name="t7" value="<?php if ($rider['t7']==0) { echo "";} else { $time = explode(".", $rider['t7']); $time[1] = $time[1] ?: '0'; echo date("H:i:s",$time[0]).".".$time[1];} ?>" onchange="calcS7()"  /></td>
					<td><input id="t107" type="text" name="t107" value="<?php if ($rider['t107']==0) { echo "";} else { $time = explode(".", $rider['t107']); echo date("H:i:s",$time[0]).".".$time[1];} ?>" onchange="calcS7()" /></td>
					<td><input id="s7" type="text" name="s7" value="<?php echo $rider['s7']; ?>" /></td>
				</tr>
				<tr>
					<td>Stage 8</td>
					<td><input id="t8" type="text" name="t8" value="<?php if ($rider['t8']==0) { echo "";} else { $time = explode(".", $rider['t8']); $time[1] = $time[1] ?: '0'; echo date("H:i:s",$time[0]).".".$time[1];} ?>" onchange="calcS8()"  /></td>
					<td><input id="t108" type="text" name="t108" value="<?php if ($rider['t108']==0) { echo "";} else { $time = explode(".", $rider['t108']); echo date("H:i:s",$time[0]).".".$time[1];} ?>" onchange="calcS8()" /></td>
					<td><input id="s8" type="text" name="s8" value="<?php echo $rider['s8']; ?>" /></td>
				</tr>
				<tr>
					<td>Stage 9</td>
					<td><input id="t9" type="text" name="t9" value="<?php if ($rider['t9']==0) { echo "";} else { $time = explode(".", $rider['t9']); $time[1] = $time[1] ?: '0'; echo date("H:i:s",$time[0]).".".$time[1];} ?>" onchange="calcS9()"  /></td>
					<td><input id="t109" type="text" name="t109" value="<?php if ($rider['t109']==0) { echo "";} else { $time = explode(".", $rider['t109']); echo date("H:i:s",$time[0]).".".$time[1];} ?>" onchange="calcS9()" /></td>
					<td><input id="s9" type="text" name="s9" value="<?php echo $rider['s9']; ?>" /></td>
				</tr>
				<tr>
					<td>Stage 10</td>
					<td><input id="t10" type="text" name="t10" value="<?php if ($rider['t10']==0) { echo "";} else { $time = explode(".", $rider['t10']); $time[1] = $time[1] ?: '0'; echo date("H:i:s",$time[0]).".".$time[1];} ?>" onchange="calcS10()"  /></td>
					<td><input id="t110" type="text" name="t110" value="<?php if ($rider['t110']==0) { echo "";} else { $time = explode(".", $rider['t110']); echo date("H:i:s",$time[0]).".".$time[1];} ?>" onchange="calcS10()" /></td>
					<td><input id="s10" type="text" name="s10" value="<?php echo $rider['s10']; ?>" /></td>
				</tr>
				<tr>
					<td>Stage 11</td>
					<td><input id="t11" type="text" name="t11" value="<?php if ($rider['t11']==0) { echo "";} else { $time = explode(".", $rider['t11']); $time[1] = $time[1] ?: '0'; echo date("H:i:s",$time[0]).".".$time[1];} ?>" onchange="calcS11()"  /></td>
					<td><input id="t111" type="text" name="t111" value="<?php if ($rider['t111']==0) { echo "";} else { $time = explode(".", $rider['t111']); echo date("H:i:s",$time[0]).".".$time[1];} ?>" onchange="calcS11()" /></td>
					<td><input id="s11" type="text" name="s11" value="<?php echo $rider['s11']; ?>" /></td>
				</tr>
				<tr>
					<td>Stage 12</td>
					<td><input id="t12" type="text" name="t12" value="<?php if ($rider['t12']==0) { echo "";} else { $time = explode(".", $rider['t12']); $time[1] = $time[1] ?: '0'; echo date("H:i:s",$time[0]).".".$time[1];} ?>" onchange="calcS12()"  /></td>
					<td><input id="t112" type="text" name="t112" value="<?php if ($rider['t112']==0) { echo "";} else { $time = explode(".", $rider['t112']); echo date("H:i:s",$time[0]).".".$time[1];} ?>" onchange="calcS12()" /></td>
					<td><input id="s12" type="text" name="s12" value="<?php echo $rider['s12']; ?>" /></td>
				</tr>
				<tr>
					<td colspan="3">Penalty Time (HH:MM:SS)</td>
					<td><input id="penalty" type="text" name="penalty" value="<?php echo $rider['penalty']; ?>" onchange="calcTotal()" /></td>
				</tr>
				<tr>
					<td colspan="3">Total Time</td>
					<td><input id="totaltime" type="text" name="totaltime" value="<?php echo $rider['total']; ?>" /></td>
				</tr>
				<tr>
					<td colspan="3">Stages Completed</td>
					<td><input id="stages" type="text" name="stages" value="<?php echo $rider['stages']; ?>" /></td>
				</tr>
				<tr>
					<td colspan="3">RankTotal</td>
					<td><input id="ranktotal" type="text" name="ranktotal" value="<?php echo $rider['ranktotal']; ?>" /></td>
				</tr>
				<tr>
					<td colspan="3">ttotal</td>
					<td><input id="ttotaltime" type="text" name="ttotaltime" value="<?php echo $rider['ttotal']; ?>" /></td>
				</tr>
				<tr>
					<td colspan="3">tpenalty</td>
					<td><input id="tpenalty" type="text" name="tpenalty" value="<?php echo $rider['tpenalty']; ?>" /></td>
				</tr>
				<tr>
				<tr>
					<td colspan="3">DNF</td>
					<td><input id="dnf" type="checkbox" name="dnf" value="Y" <?php if ($rider['dnf']=='Y') {echo 'CHECKED';} ?> /></td>
				</tr>
				<tr>
					<td colspan="3">DQ</td>
					<td><input id="dq" type="checkbox" name="dq" value="Y" <?php if ($rider['dq']=='Y') {echo 'CHECKED';} ?>  /></td>
				</tr>
			</table>

			</br>
			
			<button type="submit" class="btn btn-primary btn-block" name="btn-submit">SUBMIT</button>
			</br>


		</form>
	</div>

</body>
</html>