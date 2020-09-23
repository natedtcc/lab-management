<?php # home.php - N. Nasteff

// Default landing page for the drive management system. This page contains
// a brief paragraph and some links for students to log in.

$page_title = "ITN Server & Drive Registrar";
$page_header = "Welcome";

include('includes/header.html');

// Display welcome paragraph

echo '
<p class="blockquote"> 
	This site was designed to keep track of student hard drive leases, as well as server leases for the IT
	department at Delaware Tech. Use the links below to register a hard drive, or lease a server for the class.</p>';

	// Display links to log in / register for an account
	// If the student is logged in, hide the links to register / log in..

	if (empty($_SESSION)){

	echo '<h4><a href="login.php">Log in</a> to manage your account.</h4><p class="blockquote">
	<br><br><small>NOTE: You must <a href="register.php">register</a> before leasing a drive or server.
	</small></p>';
	}

	elseif (isset($_SESSION['student_id'])){

		echo '<h4><a href="mydrive.php">Click here</a> to access your account page.</h4>';

	}

	elseif (isset($_SESSION['admin_id'])){

		echo '<h4><a href="admin.php">Click here</a> to access the admin page.</h4>';

	}


?>


<?php include('includes/footer.html'); ?>