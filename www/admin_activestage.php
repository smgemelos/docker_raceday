<?php
session_start();
$page="adminstages";

include_once 'dbconnect.php';
date_default_timezone_set("America/Los_Angeles");




$query = "SELECT max(stages) FROM categories";
$maxstages = mysql_fetch_row(mysql_query($query))[0];


$query = "SELECT * FROM categories ORDER BY sortorder";

$cats=mysql_query($query);


$query = "SELECT * FROM status WHERE id=1";
$status=mysql_fetch_array(mysql_query($query));

if(isset($_POST['racetiming']))
{
    if ($_POST['racetiming'] == "RESTART") {
		$query = "UPDATE status SET racetiming='restart' WHERE  id=1";
		mysql_query($query);
    }
	header("Location: admin_activestage.php");
}



?>





<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>California Enduro Series: Active Categories</title>
	<script src="js/table-filter.js"></script>

	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<link rel="stylesheet" href="raceresults.css" type="text/css" />

	<!-- jQuery library -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>

	<!-- Latest compiled JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>

<body>
	<?php include 'menu.php'; ?>

	<div id="categories" style="width: 900px; background: #f4f7f8; border-radius: 8px; overflow-x:auto; margin: 80px auto; padding: 10px 10px 10px 10px;" >
		<form method="post"   >

			<h4>Update the active stages for each category - then restart the RaceTiming</h4>
			<label for="racetiming">Racetiming App:</label>
			<select id="racetiming" type="text" name="racetiming" onchange="this.form.submit()" >
			<option value="ON" <?php if ($status["racetiming"] == "start") { echo "selected"; } ?> >RUNNING</option>
			<option value="ON" <?php if ($status["racetiming"] == "stop") { echo "selected"; } ?> >STOPPED</option>
			<option value="RESTART" <?php if ($status["racetiming"] == "restart") { echo "selected"; } ?> >RESTART</option>
			</select>
			<br>
			<br>

			
			<table>
			<tr><td></td>
				<td>Category</td>
				<td>Stages</td>
				<?php
				for ($x = 1; $x <= $maxstages; $x+=1) {
					echo '<td>S'.$x.'</td>';
				}
				?>
				<td>Update</td>
			<?php
			$i = 0;
			while ( $row = mysql_fetch_array($cats) )
			{
				echo '<tr>';
				echo '<td><input type="hidden" value="'.$row['id'].'" name="id'.$i.'" style="width: 4em" /></td>';
				echo '<td>'.$row['name'].'</td>';
				echo '<td><input type="text" value="'.$row['stages'].'" name="stages'.$i.'" style="width: 4em; text-align: center;"/></td>';
				for ($x = 1; $x <= $maxstages; $x+=1) {
					echo '<td>';
					if ($x <= $row['stages']) {
						echo '<select type="text" name="s'.$x.$i.'" style="width: 4em" required>';
	 					echo '<option '; 
	 						if($row['s'.$x] == 1){echo("selected");} 
	 					echo ' value="1">ON</option>';
						echo '<option ';
							if($row['s'.$x] == 0){echo("selected");}
						echo ' value="0">OFF</option>';
						echo '</select>';
					}
					echo '</td>';
					

				}


				echo '<td><input type="submit" value="UPDATE" name="update'.$i.'" />';
					if (isset($_POST['update'.$i])) {
						$id = $_POST['id'.$i.''];
						$stages = $_POST['stages'.$i];
						$s1 =  ($_POST['s1'.$i] == '') ? 1 : $_POST['s1'.$i];
						$s2 =  ($_POST['s2'.$i] == '') ? 1 : $_POST['s2'.$i];
						$s3 =  ($_POST['s3'.$i] == '') ? 1 : $_POST['s3'.$i];
						$s4 =  ($_POST['s4'.$i] == '') ? 1 : $_POST['s4'.$i];
						$s5 =  ($_POST['s5'.$i] == '') ? 1 : $_POST['s5'.$i];
						$s6 =  ($_POST['s6'.$i] == '') ? 1 : $_POST['s6'.$i];
						$s7 =  ($_POST['s7'.$i] == '') ? 1 : $_POST['s7'.$i];
						$s8 =  ($_POST['s8'.$i] == '') ? 1 : $_POST['s8'.$i];
						$s9 =  ($_POST['s9'.$i] == '') ? 1 : $_POST['s9'.$i];
						$s10 = ($_POST['s10'.$i] == '') ? 1 : $_POST['s10'.$i];
						$s11 = ($_POST['s11'.$i] == '') ? 1 : $_POST['s11'.$i];
						$s12 = ($_POST['s12'.$i] == '') ? 1 : $_POST['s12'.$i];
						

						$query = "UPDATE categories SET stages = '$stages',
									s1='$s1',s2='$s2',s3='$s3',s4='$s4',
									s5='$s5',s6='$s6',s7='$s7',s8='$s8',
									s9='$s9',s10='$s10',s11='$s11',s12='$s12' 
							     WHERE id='$id'";
						mysql_query($query);
						echo "<meta http-equiv='refresh' content='0'>";
						
					}
				echo '</td>';
				echo '</tr>';

				$i++;
			}


			echo '</table>';
			?>
			</br>
			</br>
		

		</form>
	</div>

</body>
</html>