<?php
session_start();
#include_once 'dbconnect.php';
include_once 'dbconnect.php';

$page="adminregfile";




$target_file = "uploads/regfile.csv";
move_uploaded_file($_FILES["regfile"]["tmp_name"], $target_file);


$query = "DELETE FROM riders" ;
mysql_query($query);

$file = fopen($target_file,"r");
fgetcsv($file);

while(!feof($file))
{
    $entry = fgetcsv($file);
    $plate = $entry[0];
    $name = mysql_real_escape_string($entry[1] . " " . $entry[2]);
    $category = mysql_real_escape_string($entry[3]);

    $riderid = $plate;
    $raceid = 1;

	$query = "INSERT INTO riders (plate,name,category,riderid,raceid) VALUES ('$plate','$name','$category','$riderid','$raceid') " ;

	mysql_query($query);
}

fclose($file);

if ($_POST['loadcats']){
	$query = "DELETE FROM categories" ;
	mysql_query($query);

	$query = "SELECT category FROM riders GROUP BY category" ;
	$cats = mysql_query($query);

	while ( $row = mysql_fetch_array($cats) ) {
		$query = "INSERT INTO categories (name) VALUE ('" . $row['category'] ."')" ;
		echo $query;

	}
}






?>
