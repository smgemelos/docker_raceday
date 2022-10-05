<nav id="menu" class="navbar navbar-inverse">
	<div class="container-fluid">
	    <ul  class="nav navbar-nav navbar-right">
	    	<li <?php if($page == "home"){echo 'class="active"';} ?> ><a href="index.php">Home</a></li>
	    	<li <?php if($page == "checkin"){echo 'class="active"';} ?> ><a href="rider_checkin.php">Rider Checkin</a></li>
	    	<li <?php if($page == "livetiming"){echo 'class="active"';} ?> ><a href="livetimingreview.php">Race Timing</a></li>
	    	<li <?php if($page == "finalresults"){echo 'class="active"';} ?> ><a href="finalresults.php">Final Results</a></li>
	    	<li <?php if($page == "racestatus"){echo 'class="active"';} ?> ><a href="racestatus.php">Race Status</a></li>
	    	<li class="dropdown">
        			<a class="dropdown-toggle" data-toggle="dropdown" href="#">Live<span class="caret"></span></a>
        			<ul class="dropdown-menu">
          				<li><a href="live/index.php">Live Scroll</a></li>
          				<li><a href="liverace2/index.php">Live Last Racer</a></li>
        			</ul>
      			</li>
	    	<li <?php if( ($page == "admin") OR ($page=="loadregfile") ) {echo 'class="active"';} ?> class="dropdown">
        			<a class="dropdown-toggle" data-toggle="dropdown" href="#">Admin<span class="caret"></span></a>
        			<ul class="dropdown-menu">
          				<li <?php if($page == "admin") {echo 'class="active"';} ?> ><a href="admin.php">Race Admin</a></li>
          				<li <?php if($page == "admin") {echo 'class="active"';} ?> ><a href="admin_activestage.php">Active Stages</a></li>
          				<li <?php if($page == "admin") {echo 'class="active"';} ?> ><a href="admin_beacontime.php">Beacon Setup</a></li>
          				<li <?php if($page == "loadregfile") {echo 'class="active"';} ?> ><a href="loadregfile.php">Load Reg File</a></li>
        			</ul>
      			</li>
	    	

    	</ul>
	</div>
</nav>