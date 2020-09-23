<?php # add_drive.php - N. Nasteff

/* This page allows the admin to add a new drive to the database. */

$page_title = "Admin";
$page_header = "Add a drive";
include('includes/header.html');

if (isset($_SESSION['admin_id']) && $_SERVER['REQUEST_METHOD'] != 'POST'){

    // Build the form for modifying the drive details and
	// create button to submit changes

	echo '<br><br><form action="add_drive.php" method="post">
	<table class="table" cellpadding="10">
	
	<th>Drive Number</th><th>Serial</th><th>Capacity</th><th>Form Factor</th><th>Broken?</th>

	<tr><td><input type="number" name="drive_num" /></td>
	<td><input type="text" name="serial"/> </td>
	<td><input type="number" name="capacity" min="250" max="1000 /> </td>
    <td><input type="text" name="form_factor/></td>
    <td><input type="text" name="broken"/></td></tr></table>
	<input type="hidden" name="drive_id" value="'.$drive_id.'"/>
	
	<br><input class="btn-dark" type="submit" name="submit" value="Submit Changes"/>
	<br><br><button class="btn-dark" type="button" name="back" onclick="document.location.href=\'view_drives.php\'">Go back</button></form>';

}

elseif (isset($_SESSION['admin_id']) && $_SERVER['REQUEST_METHOD'] == 'POST'){

    // Trim all incomming post data

    $trimmed = array_map('trim', $_POST);

    // Set false values for validation..

    $drive_num = $serial = $capacity = $form_factor = $broken = FALSE;

    // Validate the drive number..

    if(isset($_POST['drive_num']) && is_numeric((int)$trimmed['drive_num'])){

        $server_num = (int)$trimmed['drive_num'];

    }

    else {
        error_string_gen('Please enter a valid drive number.');
    }

    // Validate the server type string..

    if (isset($_POST['serial'])){

        $serial = (int)$trimmed['serial'];

    }

    else {
        error_string_gen('Please enter a valid serial number. Try again!');
    }

    // If everything is validated...

    if ($drive_num && $serial && $capacity && $form_factor && $broken){

        // Create SQL query for adding a server to the DB

        $insert_query = "INSERT INTO drives (drive_num, serial, capacity, form_factor, broken) VALUES ($drive_num, '$serial', $capacity, $form_factor, '$broken')";
        sql_results($insert_query, $db_conn);

        // If the query was successful, notify the user, redirect and exit...

        if (mysqli_affected_rows($db_conn) == 1) {

            success_string_gen('Your drive has been added. Redirecting you now.');
            redirect_user('view_drives.php');
            exit();

    }
        // If there's an SQL error..

        else {
            error_string_gen('Sorry, an error occured. Contact the admin.');
            exit();
        }

    }

}

else {
    error_string_gen("You must be logged in as an admin to view this page!");
    exit();
}

include('includes/footer.html');

?>