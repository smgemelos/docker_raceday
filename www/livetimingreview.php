<?php

$page="livetiming";

include_once 'dbconnect.php';


$sicard_id = "";
$name = "Not Found";
$category = "Not Found";

$Stages = ['s1','s2','s3','s4','s5','s6','s7','s8','s9','s10','s11','s12'];

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
$query = "SELECT * FROM siacriderid WHERE sicard_id=$sicard_id";
$response=mysql_query($query);

$count=mysql_num_rows($response);

if ($count != 0) {
    $row = mysql_fetch_array($response);
    $riderid = $row['riderid'];
    #$category = $row['category'];
    $saic_returned = $row['saic_returned'];

    $query = "SELECT * FROM raceresults WHERE riderid='$riderid' ";
    $response=mysql_query($query);
    $rider = mysql_fetch_array($response);
    $category = $rider['category'];

    $query = "SELECT * FROM stamps WHERE stamp_card_id='$sicard_id' AND stamp_control_mode IN (2,3,4,18,19,20) GROUP BY stamp_control_code, stamp_punch_datetime ORDER BY id_stamp";
    $stamps=mysql_query($query);
}


if(isset($_POST['btn-dnf']))
{     
    
    if ($rider['dnf']=='Y') {
        $query = "UPDATE raceresults SET dnf='N' WHERE riderid=$riderid " ;
    } else {
        $query = "UPDATE raceresults SET dnf='Y' WHERE riderid=$riderid " ;
    } 

    mysql_query($query);

}

if(isset($_POST['btn-chip']))
{     
    
    if ($saic_returned=='Yes') {
        $query = "UPDATE siacriderid SET saic_returned='No' WHERE sicard_id=$sicard_id " ;
    } else {
        $query = "UPDATE siacriderid SET saic_returned='Yes' WHERE sicard_id=$sicard_id " ;
    } 

    mysql_query($query);

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
                    document.getElementById("results").innerHTML = this.responseText;
                }
            };

            xmlhttp.open("GET","ajaxlivechip.php",true);
            xmlhttp.send();
            
        }

        var interval = 1000; //number of milliseconds between refreshes

        setInterval(ajax_rider, interval);
        </script>



    </head>

    <body>

    	<?php include 'menu.php'; ?>

		<div id="results">

            <form method="post">

                <table class="order-table table" >
                    <thead>
                        <tr><th style="font-size:14px;"><center>Name</th>
                            <th style="font-size:14px;"><center>Category</th>
                            <th style="font-size:14px;"><center>Total</th>
                            <?php
                            for ($i = 0; $i < $catStages[$category]; $i++) {
                                echo '<th style="font-size:14px;"><center>'.$Stages[$i].' Time</th>';
                            }
                            ?>
                        </tr>
                    </thead>

                    <tbody >
                        <?php
                            echo '<tr>';
                            echo '<td style="font-size:12px;"><a href="correct_time.php?sicard_id='.$rider['sicard_id'].'">'.$rider['name'].'</a></td>';
                            echo '<td style="font-size:12px;">'.$rider['category'].'</td>';
                            echo '<td style="font-size:12px;">'.$rider['total'].'</td>';
                            for ($i = 0; $i < $catStages[$category]; $i++) {
                                echo '<td style="font-size:12px;">'.$rider[$Stages[$i]].'</td>';
                            }
                            echo '</tr>';
                        ?>
                    </tbody>
                </table>


                <?php 
                    if ($saic_returned=='Yes') {
                        echo '<button id="btn-chip" name="btn-chip"  style="font-size:12px;" >Chip Returned</button>';
                    } else {
                        echo '<button id="btn-chip" name="btn-chip"  style="font-size:12px;" >Rider Done?</button>';
                    } 
                ?>
                 




            </form>

            <form method="post" style="width: 500px;">

                <table>
                    <thead>
                        <tr><th style="font-size:14px;"><center>Rider</th>
                            <th style="font-size:14px;"><center>Stamps</th>
                        </tr>
                    </thead>
                    <tr>
                        <td style="text-align:left;font-size:14px;">

                            <input id="sicard_id" type="hidden" name="sicard_id" value="<?php echo $sicard_id; ?>" />

                            <label for="name">Name: &nbsp; </label>
                            <?php echo $rider['name']; ?>
                            </br>

                            <label for="riderid">CES RiderID: &nbsp; </label>
                            <?php echo $rider['riderid']; ?>
                            </br>

                            <label for="plate">Plate Number: &nbsp; </label>
                            <?php echo $rider['plate']; ?>
                            </br>

                            <label for="category">Category: &nbsp; </label>
                            <?php echo $rider['category']; ?>
                            </br>
                            <label for="stages">Stages: &nbsp; </label>
                            <?php echo $stages; ?>
                            </br>

                            <label for="sicard_id">SIcardID: &nbsp; </label>
                            <?php echo $sicard_id; ?>
                            </br>
                        </td>
                        <td style="text-align:left;font-size:14px;">
                            <?php

                                while ( $row = mysql_fetch_array($stamps) )
                                {
                                    $ts = explode(" ",$row['stamp_punch_datetime']);
                                    $ms = str_pad ($row['stamp_punch_ms'], 3,$pad_string = "0",$pad_type = STR_PAD_LEFT);
                                    echo 'Beacon: '.$row['stamp_control_code'].' Mode: '.$row['stamp_control_mode'].' Timestamp: '.$ts[1].'.'.$ms.'</br>';
                                }
                            ?>

                        </td>

                </table>
                </br>

            </form>

        </div>

    </body>
</html>
