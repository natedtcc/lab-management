<?php # admin_login.php - N. Nasteff


// This page handles the login form for the website

$page_title = 'Login';
$page_header = 'Admin Login';
include('includes/header.html');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$password =	$email = FALSE;

	// Validate the email address:
	if (!empty($_POST['username']) && ctype_alpha('username')) {
		$username = mysqli_real_escape_string($db_conn, $_POST['username']);
	} else {
		error_string_gen('Try entering your username again.');
	}

	// Validate the password:
	if (!empty($_POST['password'])) {
		$password = mysqli_real_escape_string($db_conn, $_POST['password']);
	} else {
		error_string_gen('You forgot to enter your password!');
	}

	// If username and password are validated..

	if ($username && $password) {

		// Query the database:

		$customer_query = "SELECT * FROM admins WHERE (username='$username' AND"
			.	" pass=SHA1('$password'))";
		$result = mysqli_query($db_conn, $customer_query) or
			trigger_error("Query: $customer_query\n<br />MySQL Error: "
				. mysqli_error($db_conn));

		// If the user is found in the DB...	

		if (@mysqli_num_rows($result) == 1) {

			// Register the login values (keeps the contents of the cart 
			// in the session)

			$_SESSION = $_SESSION + mysqli_fetch_array($result, MYSQLI_ASSOC);
			mysqli_free_result($result);
			mysqli_close($db_conn);

			// Set ini settings to autoclear the session after 300 seconds (5min)

			ini_set('session.cookie_lifetime', '600');
			ini_set('session.gcmax_lifetime', '600');

			// Redirect the user, clear buffer and quit the script

			$url = 'admin.php';
			ob_end_clean();
			header("Location: $url");
			exit();
		}

		// Username/password mismatch or other invalid login attempt...

		else {
			error_string_gen('Incorrect username and/or password.');
		}
	} else {
		error_string_gen('Please try again.');
	}

	mysqli_close($db_conn);
}
?>

<center>
  <p class="blockquote"><small>Your browser must allow cookies in order to log in.</small></p>
  <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">
    <fieldset>
      <center>
        <p><b>Username:</b><br>
          <input type="text" required="required" name="username" style="width: 150px;" maxlength="15" />
        </p>
        <p><b>Password:</b><br>
          <input type="password" required="required" name="password" style="width: 100px;" maxlength="20" />
        </p>
        <input type="submit" class="btn-dark" style="width: 75px;" name="submit" value="Login" />
    </fieldset>
  </form>
  <br>
  <button id="view_servers" class="btn-dark" style="width: 75px;" onclick="document.location.href='home.php'"
    padding="10">Back</button>

  <?php include('includes/footer.html'); ?>