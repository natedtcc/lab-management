<?php # logout.php = N. Nasteff

// This script will log the user out, destroy the session, and clear the cookie.
// Then it will redirect the user back to the site index
// NOTE - THE LINK TO THIS WILL ONLY BE AVAILABLE IF A USER IS ACTUALLY LOGGED IN!

$page_header = "Logout";
$page_title = "Thanks~";
include ('includes/header.html');

// If username session variable exists, log out and redirect the user..

if (isset($_SESSION['student_id']) || isset($_SESSION['admin_id'])) {

	$_SESSION = array(); // Destroy the variables.
	session_destroy(); // Destroy the session itself.
	setcookie (session_name(), '', time()-3600); // Destroy the cookie.
	$url = BASE_URL . 'home.php'; // Define the URL.
	ob_end_clean(); // Delete the buffer.
	header("Location: $url");
	exit(); // Quit the script.

// Print a customized message:
echo success_string_gen('You are now logged out. You are being redirected.');
}

// If username session variable does not exist, redirect the user..

else {
	$url = BASE_URL . 'home.php';
	ob_end_clean();
	header("Location: $url");
	exit();
}

include ('includes/footer.html');
?>