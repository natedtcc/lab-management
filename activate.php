<?php # activate.php - N. Nasteff

// This page handles the email link that the user will go to
// to activate their account. The link contains an MD5 hash
// that is assigned during registration, then sent to the user
// ie '/driveassign/activate.php?h=[HASH GOES HERE]'

$page_title = 'Activation';
$page_header = 'Activation';
include ('includes/header.html');

// Check for a hash

if (isset($_GET['h'])){

	// Assign hash value to variable
	
	$hash = $_GET['h'];

	// DB Query - set the user as active for the specified hash that was generated on account creation

	$activate_query = "UPDATE students SET active=1 WHERE hash='$hash' AND active=0";
	$result = mysqli_query($db_conn, $activate_query) or trigger_error("Query: $activate_query\n<br />MySQL Error: " . mysqli_error($db_conn));

	// If query was a success, generate success message...

	if (mysqli_affected_rows($db_conn) == 1) {
		success_string_gen('Account activated!<br>You may now<br><a href="login.php"><b>log in</a>');
	}

	// If the query failed (incorrect hash), display error..

	else {
		error_string_gen('Sorry, there was an error.');
		exit();
	}
}
// If hash is unset, display error and quit the script

else {
	error_string_gen('You have reached this page in error.<br>Contact the administrator.');
	exit();
}

?>