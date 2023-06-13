<?php
session_start();

include_once 'dbconnect.php';
date_default_timezone_set("America/Los_Angeles");

$sicard_id = "";

if( $_GET["timestamp"] ) {
	$timestamp = $_GET["timestamp"];

	$query = "SELECT * FROM stamps WHERE stamp_readout_datetime > '$timestamp' ORDER BY stamp_readout_datetime DESC LIMIT 1";
	$response=mysql_query($query);
	
	$count=mysql_num_rows($response);

	if ($count == 1) {
		$row = mysql_fetch_array($response);
		$sicard_id = $row['stamp_card_id'];
	}

} 

echo '<label for="sicard_id">SAIC Number:</label>';
echo '<input id="saic" type="text" name="sicard_id" placeholder="SAIC Number" value="'.$sicard_id.'" />';
echo '<button class="btn btn-primary btn-block" id="add" type="submit" value="" name="add">ADD TIMING CHIP</button>';

?>
