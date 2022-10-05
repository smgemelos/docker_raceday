<?php
session_start();
#include_once 'dbconnect.php';
include_once 'dbconnect.php';

$page="adminregfile";

$query = "SELECT * FROM categories ORDER BY sortorder";

$cats=mysql_query($query);


if(isset($_POST['btn-regfile']))
{


	$query = "DELETE FROM riders" ;
	mysql_query($query);
	$query =  "ALTER TABLE riders AUTO_INCREMENT = 1";
	mysql_query($query);

	$query = "DELETE FROM lccards";
	mysql_query($query);
	$query = "ALTER TABLE lccards AUTO_INCREMENT = 1" ;
	mysql_query($query);

	$query = "DELETE FROM lccard_link_stamp";
	mysql_query($query);
	$query = "ALTER TABLE lccard_link_stamp AUTO_INCREMENT = 1";
	mysql_query($query);

	$query = "DELETE FROM stamps";
	mysql_query($query);
	$query = "ALTER TABLE stamps AUTO_INCREMENT = 1";
	mysql_query($query);

	$query = "DELETE FROM raceresults";
	mysql_query($query);
	$query = "ALTER TABLE raceresults AUTO_INCREMENT = 1";
	mysql_query($query);

	$query = "UPDATE lcevents SET id_event=1,event_name='1'";
	mysql_query($query);



	$target_file = "uploads/regfile.csv";
	move_uploaded_file($_FILES["regfile"]["tmp_name"], $target_file);

	$file = fopen($target_file,"r");

	fgetcsv($file);

	while(! feof($file))
	{
	    $entry = fgetcsv($file);
	    $plate = $entry[0];
	    $name = mysql_real_escape_string(ucfirst(trim($entry[1])) . " " . ucfirst(trim($entry[2])));
	    $category = mysql_real_escape_string(strtoupper(trim($entry[3])));
	    $emcontact = mysql_real_escape_string(strtoupper(trim($entry[4])));
	    $emphone = mysql_real_escape_string(strtoupper(trim($entry[5])));

	    $riderid = $plate;
	    $raceid = 1;

	    if ($name != " ") {
	    	$query = "INSERT INTO riders (plate,name,category,riderid,raceid,emcontact,emphone) VALUES ('$plate','$name','$category','$riderid','$raceid','$emcontact','$emphone') " ;

			mysql_query($query);
	    }

		
	}

	fclose($file);

	if ($_POST['loadcats']){
		$query = "DELETE FROM categories" ;
		mysql_query($query);

		$query = "SELECT category FROM riders GROUP BY category" ;
		$cats = mysql_query($query);

		while ( $row = mysql_fetch_array($cats) ) {
			$query = "INSERT INTO categories (name) VALUE ('" . $row['category'] ."')" ;
			mysql_query($query);

		}
	}


	header("Location: loadregfile.php");
}


if(isset($_POST['btn-catfile']))
{
	$query = "DELETE FROM categories" ;
	mysql_query($query);
	$query =  "ALTER TABLE categories AUTO_INCREMENT = 1";
	mysql_query($query);

	$target_file = "uploads/catfile.csv";
	move_uploaded_file($_FILES["catfile"]["tmp_name"], $target_file);

	$file = fopen($target_file,"r");

	fgetcsv($file);

	while(! feof($file))
	{
	    $entry = fgetcsv($file);
	    $category = mysql_real_escape_string(strtoupper(trim($entry[0])));
	    $sortorder = $entry[1];
	    $catlevel = mysql_real_escape_string((trim($entry[2])));
	    $gender = mysql_real_escape_string((trim($entry[3])));
	    $stages = $entry[4];
	    $starttime = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $entry[5])));
	    $raceid = 1;

		$query = "INSERT INTO categories (name,sortorder,cat,gender,stages,starttime) VALUES ('$category','$sortorder','$catlevel','$gender','$stages','$starttime') " ;

		mysql_query($query);
	}

	fclose($file);

	header("Location: loadregfile.php");
}




?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script src="js/table-filter.js"></script>

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
		$(document).ready(function(){
    		$('[data-toggle="popover"]').popover(); 
		});



		function eventselect() {
			var event = document.getElementById('event').value;

			if (event == "") {
				document.getElementById('btn-loadreg').disabled=true;
			} else {
				document.getElementById('btn-loadreg').disabled=false;
				
			}
		}


		function regfileselect() {
			var regfile = document.getElementById('regfile').value;

			if (regfile == "") {
				document.getElementById('btn-regfile').disabled=true;
			} else {
				document.getElementById('btn-regfile').disabled=false;
				
			}
		}

		function catfileselect() {
			var catfile = document.getElementById('catfile').value;

			if (catfile == "") {
				document.getElementById('btn-catfile').disabled=true;
			} else {
				document.getElementById('btn-catfile').disabled=false;
				
			}
		}


		function complete() {

			if (document.getElementById('racecomplete').checked) {

				document.getElementById('btn-uploadresults').disabled=false;
			} else {
				document.getElementById('btn-uploadresults').disabled=true;
			}
		}


	</script>

</head>


<body>

	<?php include 'menu.php'; ?>


	<div id="rider">

		<form method="post" enctype="multipart/form-data" >			

			<h4>Load Reg File</h4>

			Columns should be:</br>
			Bib, First Name, Last Name, Category - columns following are ignored</br>
			</br>
			The first row is assumed to be a header, and will be ignored.</br>
			</br>

    		<input type="file" name="regfile" id="regfile" onchange="regfileselect()" accept=".csv"></br>
    		<label for="loadcats" style="width:220px;">Load categories from Reg List:</label>
			<input id="loadcats" type="checkbox" name="loadcats" > 
			</br>
    		<button id="btn-regfile" name="btn-regfile" class="btn btn-primary" style="font-size:12px;"  disabled>Load Reg File</button> 

			</br>
			</br>
			</br>

			<h4>Load Category File</h4>

			Columns should be:</br>
			Category Name, Sort Order, Cat Level (Pro, Expert, etc), Gender, Stages, StartTime (MM/DD/YYYY HH:MM:SS) - columns following are ignored</br>
			</br>
			The first row is assumed to be a header, and will be ignored.</br>
			</br>

    		<input type="file" name="catfile" id="catfile" onchange="catfileselect()" accept=".csv"></br>
    		<button id="btn-catfile" name="btn-catfile" class="btn btn-primary" style="font-size:12px;"  disabled>Load Category File</button> 

			</br>
			</br>
			</br>
			

		</form>


	</div>

	<div id="categories" style="width: 900px; background: #f4f7f8; border-radius: 8px; overflow-x:auto; margin: 80px auto; padding: 10px 10px 10px 10px;" >
		<form method="post"   >

			<input id="raceid" type="hidden" name="raceid" value="<?php echo $raceid; ?>">	

			<?php
			echo '<center><h1>'.$race["name"].'</h1></br>';
			echo '<table>';
			echo '<tr><td></td><td>Category</td><td>Cat Type</td><td>Gender</td><td>Stages</td><td>Sort Order</td><td>Start Time</td><td>Update</td><td>Delete</td>';
			$i = 0;
			while ( $row = mysql_fetch_array($cats) )
			{
				echo '<tr>';
				echo '<td><input type="hidden" value="'.$row['id'].'" name="id'.$i.'" style="width: 4em" /></td>';
				echo '<td><input type="text" value="'.$row['name'].'" name="name'.$i.'" style="width: 20em"/></td>';
				#echo '<td><input type="text" value="'.$row['cat'].'"  style="width: 8em"/></td>';
				echo '<td><select type="text" name="cat'.$i.'" style="width: 6em" required>';
 					echo '<option '; 
 						if($row['cat'] == 'Pro'){echo("selected");} 
 					echo ' value="Pro">Pro</option>';
					echo '<option ';
						if($row['cat'] == 'Expert'){echo("selected");}
					echo ' value="Expert">Expert</option>';
					echo '<option ';
						if($row['cat'] == 'Sport'){echo("selected");}
					echo ' value="Sport">Sport</option>';
					echo '<option ';
						if($row['cat'] == 'Beginner'){echo("selected");}
					echo ' value="Beginner">Beginner</option>';
					echo '</select></td>';

				#echo '<td><input type="text" value="'.$row['gender'].'" name="gender'.$i.'" style="width: 4em"/></td>';
				echo '<td><select type="text" name="gender'.$i.'" style="width: 3em" required>';
 					echo '<option '; 
 						if($row['gender'] == 'M'){echo("selected");} 
 					echo ' value="M">M</option>';
					echo '<option ';
						if($row['gender'] == 'F'){echo("selected");}
					echo ' value="F">F</option>';
					echo '</select></td>';

				echo '<td><input pattern="\d{1,2}" type="text" value="'.$row['stages'].'" name="stages'.$i.'" style="width: 3em"/></td>';
				echo '<td><input pattern="\d{1,2}" type="text" value="'.$row['sortorder'].'" name="sortorder'.$i.'" style="width: 3em"/></td>';
				echo '<td><input type="datetime" step="1" value="'.$row['starttime'].'" name="starttime'.$i.'" style="width: 15em"/></td>';

				echo '<td><input type="submit" value="UPDATE" name="update'.$i.'" />';
					if (isset($_POST['update'.$i])) {
						$id = $_POST['id'.$i.''];
						$name   = mysql_real_escape_string($_POST['name'.$i.'']);
						$cat    = mysql_real_escape_string($_POST['cat'.$i.'']);
						$gender = mysql_real_escape_string($_POST['gender'.$i.'']);
						$stages = mysql_real_escape_string($_POST['stages'.$i.'']);
						$sortorder = mysql_real_escape_string($_POST['sortorder'.$i.'']);
						$starttime = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $_POST['starttime'.$i.''])));

						$query = "UPDATE categories SET name='$name',cat='$cat',gender='$gender',".
							     "stages='$stages',sortorder='$sortorder',starttime='$starttime' ".
							     "WHERE id='$id'";
						mysql_query($query);
						echo "<meta http-equiv='refresh' content='0'>";
						
					}
				echo '</td>';


				echo '<td><input id="delete'.$i.'" type="submit" value="DELETE" name="delete'.$i.'" />';
					if (isset($_POST['delete'.$i])) {

						$id = $_POST['id'.$i.''];

						$query = "DELETE FROM categories WHERE id='$id'";
						mysql_query($query);
						echo "<meta http-equiv='refresh' content='0'>";
					}
				echo '</td>';


				echo '</tr>';

				$i++;
			}

			echo '<tr>';
			echo '<td></td>';
			echo '<td><input id="name" type="text" name="name" style="width: 20em"/></td>';
			echo '<td><select type="text" name="cat" style="width: 6em" required>';
				echo '<option value="Pro">Pro</option>';
				echo '<option value="Expert">Expert</option>';
				echo '<option value="Sport">Sport</option>';
				echo '<option value="Beginner">Beginner</option>';
				echo '</select></td>';
			echo '<td><select type="text" name="gender" style="width: 3em" required>';
				echo '<option value="M">M</option>';
				echo '<option value="F">F</option>';
				echo '</select></td>';
			echo '<td><input pattern="\d{1,2}" id="stages" type="text" name="stages" style="width: 3em"/></td>';
			echo '<td><input pattern="\d{1,2}" id="sortorder" type="text" name="sortorder" style="width: 3em"/></td>';
			echo '<td><input type="datetime" name="starttime" style="width: 15em"/></td>';
			echo '<td><input id="add" type="submit" value="ADD" name="add" />';
				if (isset($_POST['add'])) {
					$name   = mysql_real_escape_string($_POST['name']);
					$cat    = mysql_real_escape_string($_POST['cat']);
					$gender = mysql_real_escape_string($_POST['gender']);
					$stages = mysql_real_escape_string($_POST['stages']);
					$sortorder = mysql_real_escape_string($_POST['sortorder']);
					$starttime = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $_POST['starttime'])));
					$raceid = 1;

					$query = "INSERT INTO categories (raceid,name,cat,gender,stages,sortorder,starttime) VALUES ".
							 "('$raceid','$name','$cat','$gender','$stages','$sortorder','$starttime')";
					mysql_query($query);
					echo "<meta http-equiv='refresh' content='0'>";
					
				}
			echo '</td>';
			echo '<td></td>';


			echo '</tr>';



			echo '</table>';
			?>
			</br>
			</br>

		</form>
	</div>





</body>
</html>