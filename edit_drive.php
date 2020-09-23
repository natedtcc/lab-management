<?php # edit_drive.php - N. Nasteff

/* This page modifies existing drives. */

$page_title = "Admin";
$page_header = "Edit Drive";
include('includes/header.html');

// Check for admin status..

if (isset($_SESSION['admin_id'])){

// Page resubmission (after user has made changes to a drive)

if ($_SERVER['REQUEST_METHOD'] == 'POST'){

    // Trim all incomming POST data..

    $trimmed = array_map('trim', $_POST);

    // Assign false flags to all variables

    $drive_id = $drive_num = $serial = $capacity = $form_factor = $broken = FALSE;

    // Validate the drive id...

    if ((int)$trimmed['drive_id'] > 0 && (int)$trimmed['drive_id'] < 90 && is_numeric($trimmed['drive_id'])){
        $drive_id = (int)$trimmed['drive_id'];
    }
    else {
        error_string_gen('Invalid drive ID!');
    }

    // Validate the drive number..

    if ((int)$trimmed['drive_num'] > 0 && (int)$trimmed['drive_num'] < 90 && is_numeric($trimmed['drive_num'])){
        $drive_num = (int)$trimmed['drive_num'];
    }
    else {
        error_string_gen('Invalid drive number!');
    }

    // Validate the serial..

    $serial = htmlspecialchars($trimmed['serial']);

    // Validate the form factor..

    if ($trimmed['form_factor'] == "3.5"){
        $form_factor = '3.5';
    }

    if ($trimmed['form_factor'] == "2.5"){
        $form_factor = '2.5';
    }

    else {
        error_string_gen("Invalid form factor! Please enter either 3.5 or 2.5");
    }

    // Validate the capacity..

    if ((int)$trimmed['capacity'] > 0 && (int)$trimmed['capacity'] <= 1000 && is_numeric($trimmed['capacity'])){
        $capacity = (int)$trimmed['capacity'];
    }
    
    else {
        error_string_gen("Invalid capacity! Must be a whole number and cannot be more than 1000!");
    }

    // Validate broken flag..


    $temp_flag = htmlspecialchars($trimmed['broken']);
    $broken = boolean_format($temp_flag);

    // If validation is complete, update the record in the database

    if ($drive_id && $drive_num && $serial && $capacity && $form_factor && $broken == 0 || $broken == 1){

        $sql_drive_string = "UPDATE drives SET drive_num='$drive_num', serial='$serial', capacity=$capacity,
        form_factor=$form_factor, broken=$broken WHERE drive_id=$drive_id";
        
        $valid = sql_results($sql_drive_string, $db_conn);
        
        // If the SQL update is valid..

        if ($valid){
        
        success_string_gen("Drive modified successfully. You are being redirected.");            

        redirect_user('view_drives.php');

        }

        else {

            error_string_gen("Sorry, an error occured! Check the SQL logs (/var/log/apache2/error.log)");
            exit();
        }
    }

    // End of POST if
}

// Create / assign false value to drive_id variable

$drive_id = FALSE;

// Initial page (from view_drives.php GET request)

if ($_SERVER['REQUEST_METHOD'] == 'GET'){

    // Check for a valid dnum value from GET
    
    if (isset($_GET['did'])) {
        $temp_drive_id = (int)$_GET['did'];

        // Validate drive number - drive numbers range from 1-89

        if ($temp_drive_id > 0 && $temp_drive_id <90){
            $drive_id = $temp_drive_id;
        }

        else {
            error_string_gen("Invalid drive number!!");
        }
    }

    // If no drive num is found, display an error...

    else {
        error_string_gen("No drive number found! Hit back and try again.");
    }

    // If drive number is valid...

    if ($drive_id){

	// Query the DB for the drive info..

	$drive_query = "SELECT * from drives WHERE drive_id=$drive_id";
	$result = mysqli_query($db_conn, $drive_query) or trigger_error("Query: $drive_query\n<br />MySQL Error: " . mysqli_error($db_conn));
	$drive_data = mysqli_fetch_array($result, MYSQLI_ASSOC);

	// Build the form for modifying the drive details and
	// create button to submit changes

	echo '<br><br><form action="edit_drive.php" method="post">
	<table class="table" cellpadding="10">
	
	<th>Drive Number</th><th>Serial</th><th>Capacity</th><th>Form Factor</th><th>Broken?</th>

	<tr><td><input type="number" name="drive_num" value="'.$drive_data['drive_num'].'" style="width: 40px;" /></td>
	<td><input type="text" name="serial" value="'.$drive_data['serial'].'"style="width: 150px;" /> </td>
	<td><input type="number" name="capacity" min="250" max="1000" value="'.$drive_data['capacity'].'"style="width: 50px;" /> </td>
    <td><input type="text" name="form_factor" value="'.$drive_data['form_factor'].'"style="width: 50px;"/></td>
    <td><input type="text" name="broken" value="'.boolean_format($drive_data['broken']).'"style="width: 50px;"/></td></tr></table>
	<input type="hidden" name="drive_id" value="'.$drive_id.'"/>
	
	<br><input class="btn-dark" type="submit" name="submit" value="Submit Changes"/>
	<br><br><button class="btn-dark" type="button" name="back" onclick="document.location.href=\'view_drives.php\'">Go back</button></form>';

    }

}

}
?>