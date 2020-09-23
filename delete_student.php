<?php # delete_student.php - N. Nasteff

/* This page will delete a selected user from the database, as well as delete
any drive or server leases they currently have. It is launched from the admin panel*/

$page_title = "Admin";
$page_header = "Delete Student";
include('includes/header.html');

// Assign false values for verification and student ID

$verified = $student_id = FALSE;

// Assign drive lease id from get..

if (isset($_SESSION['admin_id'])){

if (isset($_GET['sid'])) {
	$student_id = (int)$_GET['sid'];
} 

// If method is post, assign deletion verification and student ID variables

elseif (isset($_POST['verified']) && isset($_POST['sid'])){

	// Set verification flag to TRUE and gather user data

	$verified = TRUE;
	$student_id = (int)$_POST['sid'];
	
}

// If these values are not received, quit the script.

else {

	error_string_gen("You have reached this page in error. Please go back and try again.");
	exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && $verified){

	// Get the student's drive_id via SQL..

	$drive_query = "SELECT drive_id FROM students WHERE student_id = $student_id";
	$result = sql_results($drive_query, $db_conn);
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$drive_id = $row['drive_id'];
	
	// Check the server assignments to see if the student has a server currently assigned..

	$server_query = "SELECT server_num FROM server_assignments WHERE student_id=$student_id";
	$result = sql_results($server_query, $db_conn);

	if (mysqli_num_rows($result) == 1){
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$server_num = (int)$row['server_num'];
	}

	else {
		$server_num = 0;
	}

	// Create queries for deleting and updating records on the database..
	
	$assignment_query = "DELETE FROM drive_assignments WHERE student_id=$student_id";
	$student_query = "DELETE FROM students WHERE student_id=$student_id";
	$server_query = "DELETE FROM server_assignments WHERE student_id=$student_id";
	$update_query = "UPDATE servers SET available = 1 WHERE server_num = $server_num";
	$update_query2 = "UPDATE drives SET in_use = 0 WHERE drive_id = $drive_id";
	
	mysqli_begin_transaction($db_conn, MYSQLI_TRANS_START_READ_WRITE);
	
	$valid = sql_results($assignment_query, $db_conn);
	$valid2 = sql_results($server_query, $db_conn);
	$valid3 = sql_results($update_query, $db_conn);
	$valid4 = sql_results($student_query, $db_conn);
	$valid5 = sql_results($update_query2, $db_conn);
	
	if ($valid && $valid2 && $valid3 && $valid4 && $valid5){
		mysqli_commit($db_conn);
		success_string_gen('Record deleted successfully.</h3><br><h5>You are being redirected.');
		redirect_user('view_students.php');
             
		// Quit the script
		$db_conn->close();
		exit();
}
	else{
		error_string_gen('Sorry, an error occured.');
		exit();
		}
}
	// Gather user data from database..

	$lease_query = "SELECT * from students WHERE student_id=$student_id";
	$result = mysqli_query($db_conn, $lease_query) or trigger_error("Query: $lease_query\n<br />MySQL Error: " . mysqli_error($db_conn));
	$user_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
	
	// Table header

	echo '<br><table cellpadding="10" class="table-striped" align="center">
	      <center><th>Name/Group</th><th><center>Email</th><th>Class Number</th><th>
	      Register Date</th><th><center>Active?</th>';
	    

	echo '<tr><td>'.$user_data['name'].'</td><td>'.$user_data['email'].'</		td><td>'.$user_data['class_num'].'</td><td>'.$user_data['reg_date']		.'</td><td>'.boolean_format($user_data['active']).'</td></table>';

	
	echo '<br><h5>Are you sure you want to delete this record?</h5>';
	
	// Create form and yes/no buttons..
	
	echo '<br><form action="delete_student.php" method="post">
	<br><input class="btn-dark" name="verified" type="submit" value="Yes">
	<button class="btn-dark" type="button" name="back" onclick="document.location.href=\'view_students.php\'">No</button>
	<input type="hidden" name="sid" value="'.$student_id.'">
	<input type="hidden" name="did" value"'.$user_data['drive_id'].'"></form>';
}

// If user is not admin / admin not logged in..

else {

    $url = BASE_URL .'admin_login.php';
    ob_end_clean();
    header("Location: $url");
    exit();
}

?>