<?php

$page="livetiming";

include_once 'dbconnect.php';

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

    $query = "SELECT * FROM raceresults WHERE riderid='$riderid' ";
    $response=mysql_query($query);
    $rider = mysql_fetch_array($response);

    $query = "SELECT * FROM raceresults WHERE category='$category' ORDER BY ranktotal ASC";
    $catresults=mysql_query($query);
}



?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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

        <script>
        var ajax_category = function() {
         
            if (window.XMLHttpRequest) {
                // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else {
                // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("livecategory").innerHTML = this.responseText;
                }
            };

            xmlhttp.open("GET","ajaxlivecategory.php",true);
            xmlhttp.send();
            
        }

        var ajax_rider = function() {
         
            if (window.XMLHttpRequest) {
                // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else {
                // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("liverider").innerHTML = this.responseText;
                }
            };

            xmlhttp.open("GET","ajaxliverider.php",true);
            xmlhttp.send();
            
        }

        var interval = 1000; //number of milliseconds between refreshes

        setInterval(ajax_category, interval);
        setInterval(ajax_rider, interval);
        </script>



    </head>

    <body>

    	<?php include 'menu.php'; ?>

		<div id="results">

            <form>

                <table class="order-table table" id="liverider">
                    <thead>
                        <tr><th><center>Name</th>
                            <th><center>Category</th>
                            <th><center>Total</th>
                            <?php
                            for ($i = 0; $i < $catStages[$category]; $i++) {
                                echo '<th><center>'.$Stages[$i].' Time</th>';
                            }
                            ?>
                        </tr>
                    </thead>

                    <tbody >
                        <?php
                            echo '<tr>';
                            echo '<td><a href="correct_time.php?sicard_id='.$sicard_id.'">'.$rider['name'].'</a></td>';
                            echo '<td>'.$rider['category'].'</td>';
                            echo '<td>'.$rider['total'].'</td>';
                            for ($i = 0; $i < $catStages[$category]; $i++) {
                                echo '<td>'.$rider[$Stages[$i]].'</td>';
                            }
                            echo '</tr>';
                        ?>
                    </tbody>
                </table>
            </form>

            <form>

                <table class="order-table table" id="livecategory">
                    <thead>
                        <tr><th><center>Name</th>
                            <th><center>Category</th>
                            <th><center>Total</th>
                            <?php
                            for ($i = 0; $i < $catStages[$category]; $i++) {
                                echo '<th><center>'.$Stages[$i].' Time</th>';
                            }
                            ?>
                        </tr>
                    </thead>

                    <tbody >
                        <?php
                        while ( $row = mysql_fetch_array($catresults) )
                        {
                            if ($row['riderid'] == $riderid) {
                                echo '<tr style="background:yellow;">';
                            } else {
                                
                            }
                                
                            echo '<td>'.$row['name'].'</td>';
                            echo '<td>'.$row['category'].'</td>';
                            echo '<td>'.$row['total'].'</td>';
                            for ($i = 0; $i < $catStages[$category]; $i++) {
                                echo '<td>'.$row[$Stages[$i]].'</td>';
                            }
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </form>


        </div>

    </body>
</html>
