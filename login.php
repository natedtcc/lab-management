<?php # login.php - N. Nasteff


// This page handles the login form for the website

$page_title = 'Login';
$page_header = 'Student Login';
include ('includes/header.html');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$password =	$email = FALSE;

    $trimmed = array_map('trim', $_POST);

	// Validate the email address:
	$email = email_validate($trimmed['email'], $db_conn);
	
	// Validate the password:
	if (!empty($_POST['password'])) {
		$temp_password = mysqli_real_escape_string ($db_conn, $_POST['password']);
	} else {
		error_string_gen('You forgot to enter your password!');
	}

	// Check user credentials...

	$cred_query = "SELECT * FROM students WHERE email='$email' AND password=SHA('$temp_password');";
	$result = sql_results($cred_query, $db_conn);

	if (@mysqli_num_rows($result) == 1){
		$password = $temp_password;
	}

	else {
		error_string_gen('Invalid credentials. Please try again.');
	}
	
	if ($email && $password) { // If everything's OK.

		// Query the database:
		$student_query = "SELECT * FROM students WHERE email='$email' AND password=SHA1('$password') AND active=1";		
		$result = sql_results($student_query, $db_conn);

		
		// If the user is found in the DB...	

		if (@mysqli_num_rows($result) == 1) { 

			// Register the login values (keeps the contents of the cart in the session)

			$_SESSION = $_SESSION + mysqli_fetch_array ($result, MYSQLI_ASSOC); 
			mysqli_free_result($result);
			mysqli_close($db_conn);

			// Set ini settings to autoclear the session after 300 seconds (5min)
			
			ini_set('session.cookie_lifetime', '600');
			ini_set('session.gcmax_lifetime', '600');
							
			// Redirect the user, clear buffer and quit the script
			
			$url = 'mydrive.php';
			ob_end_clean();
			header("Location: $url");
			exit();
				
		} 

		// Username/password mismatch or other invalid login attempt...

		else {
			error_string_gen('You have not yet activated your account.');
		}
		
	}
	
	mysqli_close($db_conn);

} 
?>

<center><p>Your browser must allow cookies in order to log in.</p>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">
	<fieldset><center>
	<b>Email:</b><br><input type="email" required="required" name="email" style="width: 150px;"/><br>
	<b>Password:</b><br><input type="password" required="required" name="password" style="width: 150px;" maxlength="20" />
	<div align="center"><input type="submit" name="submit" value="Login" /></div>
	</fieldset>
</form>

<br><p>Don't have an account? <a href="register.php">Click here to register.</a></p></center></div>

<?php include ('includes/footer.html'); ?>