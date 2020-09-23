<?php #mydrive.php - N. Nasteff


/* This is the landing page once a student logs in to their drive
  account. If the user has not registered a drive, it will display
  links to do so. If they have, they will be presented with some
  information about the drive they are currently leasing (serial,
  capacity, form factor etc). This page also generates the link to
  allow students to lease a server. When a student leases a server,
  their server number / hosted contents will appear here.*/

  $page_header = "My drive";
  $page_title = "ITN Drive Management";

  include('includes/header.html');

  // Assume false value for server number..

  $server_num = $server_desc =FALSE;

  // Make sure the student is logged in...

  if (isset($_SESSION['student_id'])){

  	if (is_numeric($_SESSION['student_id'])){

  		$student_id = $_SESSION['student_id'];
  	}

  	// Query the database for a server assignment based on the student ID..

  	$server_query = "SELECT * FROM server_assignments WHERE student_id=$student_id";
	$result = sql_results($server_query, $db_conn);

	if (mysqli_num_rows($result) == 1){
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$server_num = $row['server_num'];
		$server_desc = $row['description'];
	}


  	// If the student has already registered a drive...

  	if ((int)$_SESSION['drive_id'] != 0){

  		// Assign drive number and server number to variables..

		$drive_id = $_SESSION['drive_id'];

		// If student doesn't have a server leased, assing string value to server_num for
		// display in the user's homepage

		if (!$server_num){

			$server_num = 'None';
		}

		// Query the DB for the drive contents/specs..
		
		$drive_query = "SELECT * FROM drives WHERE drive_id=$drive_id";
		$result = sql_results($drive_query, $db_conn);
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

		// Print results to a table..

		echo '<center><div class="table_border"><table cellpadding="50"><b></h3>
				<tr><td class="cell_color"><h1>#'.$row['drive_num'].'</h1></td><td class="cell_color"><b>Serial:</b><br>'.$row['serial'].'<br><b>Capacity:</b><br>'.$row['capacity'].
				'GB<br><b>Form Factor</b><br>'.$row['form_factor'].'"<br><b>
				Server:</b><br>'.$server_num.'<br>'.$server_desc.'</td></tr></table></div></b>';

		// If the student hasn't leased a server, create a link to do so...

		if ($server_num == 'None'){
			echo '<br><button class="btn btn-dark" onclick="window.location.href = \'assign_server.php\';">Lease a server</button>';

		}

		if (is_numeric($server_num)){
			echo '<br>Your server lease will expire by the end of class.';
		}

  	}

  	else {

  		echo '<h4>Click the link below to register your hard drive.</h4><br> <button class="btn btn-dark" onclick="window.location.href = \'assign_drive.php\';">Register a drive</button><br>';

  	}

  }

  // If the user is not logged in, redirect them to the login page..

  else {

  	$url = BASE_URL . 'login.php';
			ob_end_clean();
			header("Location: $url");
			exit();
  }

  include('includes/footer.html');


?>