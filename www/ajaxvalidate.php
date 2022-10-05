<?php
session_start();

include_once 'dbconnect.php';
date_default_timezone_set("America/Los_Angeles");


$sicard_id = "";
$plate = "";
$name = "Not Found";
$riderid = "Not Found";
$category = "Not Found";

$query = "SELECT * FROM stamps ORDER BY last_modification DESC LIMIT 1";
$response=mysql_query($query);
$row = mysql_fetch_array($response);

$sicard_id = $row['stamp_card_id'];

$query = "SELECT * FROM riders WHERE sicard_id=$sicard_id";
$response=mysql_query($query);

$count=mysql_num_rows($response);

if ($count != 0) {
    $row = mysql_fetch_array($response);
    $name = $row['name'];
    $plate = $row['plate'];
    $riderid = $row['riderid'];
    $category = $row['category'];
}

echo '<tr>';
echo '<td>'.$plate.'</td>';
echo '<td>'.$name.'</td>';
echo '<td>'.$riderid.'</td>';
echo '<td>'.$category.'</td>';
echo '<td>'.$sicard_id.'</td>';
echo '</tr>';

?>
