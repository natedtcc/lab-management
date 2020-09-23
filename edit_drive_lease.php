<?php # edit_drive_lease.php - N. Nasteff

/* This page allows the admin to modify an indivual record located in the
drive lease database. It produces a form with inputs based on database values
according to the user's drive_assign_id. */

$page_title = "Admin";
$page_header = "Edit Drive Lease";
include('includes/header.html');

$drive_lease_id = FALSE;

// Validate the drive lease id..

// Drive lease value is generated after clicking edit on the view_drive_leases.php page

if (isset($_SESSION['admin_id'])){

if (isset($_GET['dli'])) { // From view_users.php
	$drive_lease_id = (int)$_GET['dli'];
} 

elseif (isset($_POST['drive_lease_id'])) { // Form submission.
	$drive_lease_id = (int)$_POST['drive_lease_id'];
} 
else{

	error_string_gen('You have reached this page in error. Please try again.');
	exit();
}
	
	
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	
	// Trim all incomming post data..
	
    $trimmed = array_map('trim', $_POST);
	
	// Assume false values for all validations
	
    $username = $drive_num = $email = $class_num = FALSE;
	
	// Assign former drive number to variable to update the database..
	
	$prev_drive_num = (int)$trimmed['prev_drive_num'];
	
	
	// Validate the drive number..

        if ((int)$trimmed['drive_num'] > 0 && (int)$trimmed['drive_num'] < 90 && is_numeric($trimmed['drive_num'])){
            $drive_num = (int)$trimmed['drive_num'];
        }
		
		// If drive number fails validation...
		
        else {
            echo '<h5><center>Invalid drive number!</h5><br>';
        }
    
        // Validate user's name / group...

        if (ctype_alpha($trimmed['username'])){
            $username = mysqli_real_escape_string($db_conn, $trimmed['username']);
        }
		
		// If name/group fails validation..
		
        else {
            echo '<h5><center>Incorrect name! Use only letters.</h5><br>';
        }
    

		// Validate email address..

        if (filter_var($trimmed['email'], FILTER_VALIDATE_EMAIL) ){
            $email = mysqli_real_escape_string($db_conn, $trimmed['email']);
        }

		// If email fails validation..

        else {
            echo '<h5><center>Incorrect email address!</h5><br>';

        }
		
		// Validate class number (compare against ITN_CLASSES array)
    
	    for ($i=0; $i<sizeof(ITN_CLASSES); $i++){
            if ((int)$_POST['class_num'] == ITN_CLASSES[$i]){
                $class_num = ITN_CLASSES[$i];
                break;
            }    
        }

		// If class number fails validation..

        if (!$class_num){
            echo '<h5><center>Incorrect class number!</h5><br>';
            }

    // If all fields are verified, build SQL queries..

    if ($username && $email && $drive_num && $class_num && $drive_lease_id){
		
		// Build string for updating individual DB record..
		
        $update_assign_string = "UPDATE drive_assignments SET name='$username', email='$email', class_num=$class_num,
		drive_num=$drive_num WHERE drive_lease_id=$drive_lease_id";
		
		// Build string for setting the drive as not in use in the drive table...
		
		$update_drive_string = "UPDATE drives SET in_use=0 WHERE drive_num=$prev_drive_num";

        // Begin transaction
		mysqli_begin_transaction($db_conn, MYSQLI_TRANS_START_READ_WRITE);

        $valid = mysqli_query($db_conn, $update_assign_string) or trigger_error("Query: $update_string\n<br />MySQL Error: " . mysqli_error($db_conn));
        $valid_2 = mysqli_query($db_conn, $update_drive_string) or trigger_error("Query: $update_drive_string\n<br />MySQL Error: " . mysqli_error($db_conn));

    // If query is good, commit to the database..

		if ($valid && $valid_2){
			mysqli_commit($db_conn);
			echo '<h3><center>Records updated successfully.</h3><br><h5>You are being redirected.</h5>
			<script>window.setTimeout(function(){window.location.href = "view_drive_leases.php";}, 3000);</script>';
             
			// Quit the script
			$db_conn->close();
			exit();
}
		else{
			echo'<h3>Sorry, an SQL error occured.';
		}

    // Additional error message if verification fails
}
    else{
        echo '<h5><center>An error occured. Go back and try again.</h5>';
    }

}
	


if ($drive_lease_id){

	// Query the DB for the user's drive info..

	$lease_query = "SELECT * from drive_assignments WHERE drive_lease_id=$drive_lease_id";
	$result = mysqli_query($db_conn, $lease_query) or trigger_error("Query: $lease_query\n<br />MySQL Error: " . mysqli_error($db_conn));
	$user_data = mysqli_fetch_array($result, MYSQLI_ASSOC);

	// Build the form for modifying the lease details and
	// create button to submit changes

	echo '<br><br><form action="edit_drive_lease.php" method="post">
	<table class="table" cellpadding="10">
	
	<th>Name/Group</th><th>Email</th><th>Drive Number</th><th>Class Number</th>

	<tr><td><input type="text" name="username" value="'.$user_data['name'].'" /></td>
	<td><input type="email" name="email" value="'.$user_data['email'].'" /> </td>
	<td><input type="number" name="drive_num" min="0" max="90" value="'.$user_data['drive_num'].'" /> </td>
	<td><input type="text" name="class_num" value="'.$user_data['class_num'].'"/></td></tr></table>
	<input type="hidden" name="drive_lease_id" value="'.$drive_lease_id.'"/>
	<input type="hidden" name="prev_drive_num" value="'.$user_data['drive_num'].'"/>
	<br><input class="btn-dark" type="submit" name="submit" value="Submit Changes"/>
	<br><br><button class="btn-dark" type="button" name="back" onclick="document.location.href=\'admin/view_drive_leases.php\'">Go back</button></form>';

}

}



?>