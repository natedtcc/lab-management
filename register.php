<?php # register.php - N. Nasteff

// This page is used to register students on the website.
// This allows the students to register their leased hard
// drives.

$page_title = 'Register';
$page_header = 'Registration';

include ('includes/header.html');

// Include the script for sending verification emails

include ('includes/mailscript.php');

// Handle the registration form via POST...

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	// Trim all the incoming data:
	$trimmed = array_map('trim', $_POST);

	// Assume invalid values:
	$name =  $email = $class_num = $password = FALSE;
	
	// Check for a first name:
	if (preg_match ('/^[A-Z \'.-]{2,20}$/i', $trimmed['name'])) {
		$name = mysqli_real_escape_string ($db_conn, $trimmed['name']);
	} else {
		echo error_string_gen('Please enter your name!');
	}
	
	// Check for an email address:
	$temp_email = email_validate($trimmed['email'], $db_conn);

	// Check for a password and match against the confirmed password:
	$password = password_validate($trimmed['password1'], $trimmed['password2'], $db_conn);

	// Validate class number against ITN_CLASSES array, defined in
	// config.php

	$class_num = class_validate($trimmed['class_num'], ITN_CLASSES);

    // Create SQL query to compare current email with emails already registered

    $verify_query = 'SELECT * FROM `students` WHERE email="'.$temp_email.'"';
    $result = mysqli_query($db_conn, $verify_query);
    
    // Compare already registered emails against new email. If duplicate is found, return error.
	
    if (mysqli_num_rows($result) == 0 ){
		
        $email = $temp_email;
    }

    else {
		
		error_string_gen('That email address has already been registered.');
		
	}

	// If all user entered data is valid...
	
	if ($name && $email && $class_num && $password) {

			// Generate unique 8 character md5 hash to create
			// a random activation code for the user's activation
			// email (this is included in the register query)

			$hash = hash_gen();

			// Add the user to the database...

			$register_query = "INSERT INTO students (name, email, class_num, password, reg_date, hash) VALUES ('$name', '$email', '$class_num',  SHA1('$password'), NOW(), '$hash' )";

			$result = sql_results($register_query, $db_conn);

			// If the query was successful...

			if (mysqli_affected_rows($db_conn) == 1) {

				// While site is not live, send all verification emails to self

				if (!_LIVE_){

					$email = 'nnasteff@gmail.com';
				
				}

				// Generate PHPMailer object using custom function

				$mail = send_auth_email($email, $hash);

				// Send the email

				if (!$mail->send()){
					error_string_gen('Email error! Contact the admin. Error:');
					echo "{$mail->ErrorInfo}";
				}

				else{

				success_string_gen('Thank you for registering!<br>A verification email has been sent to:');
				echo '</h5><h5>' . $email . '<br><br>You are being redirected.';
				redirect_user('home.php');
			}
				
				exit();

			}

			// If the query did was unsuccessful...

			else {
				echo error_string_gen('You could not be registered due to a system error. We apologize for any inconvenience.');
			}


	} 

	// If validation fails...

	else {
		error_string_gen('An error occured. Please try again.');
	}

	mysqli_close($db_conn);

}

?>

<!-- Build the form for user input -->

<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">
	<fieldset>
	
	<center><p><b>Name / Group<br></b> <input type="text" required="required" name="name" style="width: 150px;" maxlength="30" value="<?php if (isset($trimmed['name'])) echo $trimmed['name']; ?>" /><br><small>If you are part of a group project, enter your group name.</small></p>

	<p><b>Email Address:<br></b> <input type="text" required="required" name="email" style="width: 150px;" maxlength="60" value="<?php if (isset($trimmed['email'])) echo $trimmed['email']; ?>" /> </p>
	<p><b>Class:</b><br><select name="class_num" id="class_num" oninvalid="this.setCustomValidity('Please select a class number.')" onchange="this.setCustomValidity('')" class="btn btn-primary dropdown-toggle" style="width:100px" required="required">
		

<?php
        
        // Populate class number options for select dropdown..

        foreach (ITN_CLASSES as $class_num){
            echo '<option value="'.$class_num.'">ITN'.$class_num.'</option>';
        }
        ?>
    </select></p>
		
	<p><b>Password:</b><br> <input type="password" required="required" name="password1" style="width: 150px;"value="<?php if (isset($trimmed['password1'])) echo $trimmed['password1']; ?>" /> <br><small>Use only letters, numbers, and the underscore. Must be between 4 and 20 characters long.</small></p>

	<p><b>Confirm Password:<br></b> <input type="password" required="required" name="password2" style="width: 150px;" maxlength="20" value="<?php if (isset($trimmed['password2'])) echo $trimmed['password2']; ?>" /></p>
	</fieldset>
	
	<div align="center"><input class="btn btn-dark" type="submit" name="submit" value="Register" /></div>

</form>

<?php include ('includes/footer.html'); ?>
