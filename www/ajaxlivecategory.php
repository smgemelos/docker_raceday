<?php
session_start();

include_once 'dbconnect.php';
date_default_timezone_set("America/Los_Angeles");

$sicard_id = "";
$name = "Not Found";
$category = "Not Found";

$Stages = ['s1','s2','s3','s4','s5','s6','s7','s8'];

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


$query = "SELECT * FROM stamps ORDER BY last_modification DESC LIMIT 1";
$response=mysql_query($query);
$row = mysql_fetch_array($response);

$sicard_id = $row['stamp_card_id'];

$query = "SELECT * FROM riders WHERE sicard_id=$sicard_id";
$response=mysql_query($query);

$count=mysql_num_rows($response);

if ($count != 0) {
    $row = mysql_fetch_array($response);
    $riderid = $row['riderid'];
    $category = $row['category'];

    $query = "SELECT * FROM raceresults WHERE category='$category' ORDER BY ranktotal ASC";
    $catresults=mysql_query($query);
}

echo '<thead>';
echo '<tr><th><center>Name</th>';
echo '<th><center>Category</th>';
echo '<th><center>Total</th>';
    for ($i = 0; $i < $catStages[$category]; $i++) {
        echo '<th><center>'.$Stages[$i].' Time</th>';
    }

echo '</tr>';
echo '</thead>';


echo '<tbody>';

while ( $row = mysql_fetch_array($catresults) )
{
    if ($row['riderid'] == $riderid) {
        echo '<tr style="background:yellow;">';
    } else {
        echo '<tr>';
    }
        
    echo '<td>'.$row['name'].'</td>';
    echo '<td>'.$row['category'].'</td>';
    echo '<td>'.$row['total'].'</td>';
    for ($i = 0; $i < $catStages[$category]; $i++) {
        echo '<td>'.$row[$Stages[$i]].'</td>';
    }
    echo '</tr>';
}

echo '</tbody>';

?>
