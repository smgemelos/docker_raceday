<?php

include_once 'dbconnect.php';

$page="validcheckin";


$sicard_id = "";
$plate = "";
$name = "Not Found";
$riderid = "Not Found";
$category = "Not Found";



date_default_timezone_set("America/Los_Angeles");

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

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>California Enduro Series: Checkin Validation</title>

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
        var ajax_call = function() {
         
            if (window.XMLHttpRequest) {
                // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else {
                // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("rider").innerHTML = this.responseText;
                }
            };

            xmlhttp.open("GET","ajaxvalidate.php",true);
            xmlhttp.send();
            
        }

        var interval = 500; //number of milliseconds between refreshes

        setInterval(ajax_call, interval);
    </script>


</head>

<body lang="en">

    <?php include 'menu.php'; ?>

    <div id="results">
        <form>
            <table class="order-table table">
                <thead><tr>
                <th><center>Plate</center></th>
                <th><center>Name</center></th>
                <th><center>RiderID</center></th>
                <th><center>Category</center></th>
                <th><center>SIcardID</center></th>
                </tr></thead>
            <tbody id="rider">
            <?php
                echo '<tr>';
                echo '<td>'.$plate.'</td>';
                echo '<td>'.$name.'</td>';
                echo '<td>'.$riderid.'</td>';
                echo '<td>'.$category.'</td>';
                echo '<td>'.$sicard_id.'</td>';
                echo '</tr>';
            ?>
            </tbody>
            </table>
        </form>
    </div>

</body>
</html>