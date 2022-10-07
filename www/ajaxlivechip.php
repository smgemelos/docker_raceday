<?php
session_start();

include_once 'dbconnect.php';
date_default_timezone_set("America/Los_Angeles");

function formattime($timestr) {

    if ($timestr == "") {
        return " ";
    } else {
        $timearr = explode('.',$timestr);
        $millsec = $timearr[1];
        $sec = strtotime($timearr[0])-strtotime("00:00:00");

        if ($sec >= 36000) {
            return gmdate("G:i:s",$sec).".".$millsec;
        } elseif ($sec >= 60) {
            return gmdate("i:s",$sec).".".$millsec;
        } else {
            return gmdate("s",$sec).".".$millsec;
        }
        
    }
}


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

if (count($response) > 0) {
    $count=mysql_num_rows($response);
} else {
    $count = 0;
}



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


echo '<form method="post">';

    echo '<table class="order-table table">';
        echo '<thead>';
            echo '<tr><th style="font-size:14px;"><center>Name</th>';
            echo '<th style="font-size:14px;"><center>Category</th>';
            echo '<th style="font-size:14px;"><center>Total</th>';
            for ($i = 0; $i < $catStages[$category]; $i++) {
                echo '<th style="font-size:14px;"><center>'.$Stages[$i].' Time</th>';
            }

            echo '</tr>';
        echo '</thead>';

        echo '<tbody >';
                               
            echo '<tr>';
            echo '<td style="font-size:12px;"><a href="correct_time.php?sicard_id='.$rider['sicard_id'].'">'.$rider['name'].'</a></td>';
            echo '<td style="font-size:12px;">'.$rider['category'].'</td>';
            echo '<td style="font-size:12px;">'.$rider['total'].'</td>';
            for ($i = 0; $i < $catStages[$category]; $i++) {
                echo '<td style="font-size:12px;">'.$rider[$Stages[$i]].'</td>';
            }
            echo '</tr>';
                            
        echo '</tbody>';
    echo '</table>';


    if ($saic_returned=='Yes') {
        echo '<button id="btn-chip" name="btn-chip"  style="font-size:12px;" >Chip Returned</button>';
    } else {
        echo '<button id="btn-chip" name="btn-chip"  style="font-size:12px;" >Rider Done?</button>';
    } 



echo '</form>';

echo '<form style="width: 200px;">';

    echo '<table>';
        echo '<thead>';
            echo '<tr><th style="font-size:14px;"><center>Rider</th>';
            echo '<th style="font-size:14px;"><center>Stamps</th>';
            echo '</tr>';
        echo '</thead>';
        echo '<tbody >';
            echo '<tr>';
                echo '<td style="text-align:left;font-size:14px;">';

                    echo '<input id="sicard_id" type="hidden" name="sicard_id" value="'.$sicard_id.'" />';

                    echo '<label for="name">Name: &nbsp; </label>';
                    echo $rider['name']; 
                    echo '</br>';

                    echo '<label for="riderid">CES RiderID: &nbsp; </label>';
                    echo $rider['riderid'];
                    echo '</br>';

                    echo '<label for="plate">Plate Number: &nbsp; </label>';
                    echo $rider['plate'];
                    echo '</br>';

                    echo '<label for="category">Category: &nbsp; </label>';
                    echo $rider['category'];
                    echo '</br>';
                    echo '<label for="stages">Stages: &nbsp; </label>';
                    echo $stages;
                    echo '</br>';

                    echo '<label for="sicard_id">SIcardID: &nbsp; </label>';
                    echo $sicard_id;
                    echo '</br>';
                echo '</td>';
                echo '<td style="text-align:left;font-size:14px;">';


                    if (count($stamps)>0) {
                        while ( $row = mysql_fetch_array($stamps) )
                        {
                            $ts = explode(" ",$row['stamp_punch_datetime']);
                            $ms = str_pad ($row['stamp_punch_ms'], 3,$pad_string = "0",$pad_type = STR_PAD_LEFT);
                            echo 'Beacon: '.$row['stamp_control_code'].' Mode: '.$row['stamp_control_mode'].' Timestamp: '.$ts[1].'.'.$ms.'</br>';
                        }
                                    
                    }

                echo '</td>';
            echo '</tr>';
        echo '</tbody >';
    echo '</table>';
    echo '</br>';
                
echo '</form>';

?>
