<?php # assign_drive.php - N. Nasteff

// This page allows ITN students to register their harddrives
// they will be using for their studies during the semester.

// Set the page title and include the HTML header:

$page_title = 'Register your harddrive';
$page_header = 'Drive Registrar';
include ('includes/header.html');

    // Trim all incomming post data

    $trimmed = array_map('trim', $_POST);

    // Assume false validation values

    $drive_id = $student_id = FALSE;


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['student_id'])) {

        // Validate the drive number..

        if ((int)$trimmed['drive_id'] > 0 && (int)$trimmed['drive_id'] < 90 && is_numeric($trimmed['drive_id'])){
            $temp_drive_id = (int)$trimmed['drive_id'];

            // Query the DB to make sure the drive is available (since multiple users can use this site at one time)

            $drive_check = "SELECT * FROM drives WHERE drive_id=$temp_drive_id AND in_use=0";
            $result = sql_results($drive_check, $db_conn);

            if (mysqli_num_rows($result) == 1){

                $drive_id = $temp_drive_id;

            }

            else {
                error_string_gen('Sorry, this drive has already been taken. Please try another.');
            }

        }
        else {
            error_string_gen('Ooops, looks like you messed up your drive number somehow...');
        }
      
    if (!empty($_SESSION['student_id']) && is_numeric($_SESSION['student_id'])){
      $temp_student_id = $_SESSION['student_id'];
    }

    else {
      error_string_gen('Sorry, there was an error.');
    }

    $student_query = "SELECT * FROM drive_assignments WHERE student_id=$temp_student_id";
    $result = sql_results($student_query, $db_conn);

    if (mysqli_num_rows($result) == 0 ){
      $student_id = $temp_student_id;
    }

    else {

      error_string_gen('You can only register one drive!');
      exit();
    }

    // If all fields are verified, build SQL queries

    if ($drive_id && $student_id){
         $insert_query ='INSERT INTO drive_assignments (student_id, drive_id)'.
        ' VALUES ('.$student_id.', '.$drive_id.')';
        $update_query = 'UPDATE drives SET in_use=1 WHERE drive_id='.$drive_id;
        $student_update_query = "UPDATE students SET drive_id=$drive_id WHERE student_id=$student_id";

        // Begin transaction

        mysqli_begin_transaction($db_conn, MYSQLI_TRANS_START_READ_WRITE);

        $result_a = sql_results($insert_query, $db_conn);
        $result_b = sql_results($update_query, $db_conn);
        $result_c = sql_results($student_update_query, $db_conn);
    
        // If queries are good, commit to the database..

        if ($result_a && $result_b && $result_c){
            mysqli_commit($db_conn);

            success_string_gen('Your drive has been assigned.</h3><br><h5>You are being redirected.');
            redirect_user('mydrive.php');

            $drive_query = "SELECT drive_num FROM drives WHERE drive_id = $drive_id";
            $result = sql_results($drive_query, $db_conn);
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $drive_num = (int)$row['drive_num']; 
            
            // Get the current time and get the contents of the server_log text file

        $current_time = time();
        $log_file = LOG_DIR . 'drive_log';
        $update = file_get_contents($log_file);

        /* The next conditionals are used to format the spacing in the log file, specifically
        the server number, description and student id.*/

        // If the description is longer than 30 characters, truncate it

        if ($student_id < 10){

            $student_string = $student_id . "   ";
        }

        if ($student_id > 9 && $student_id < 100) {

            $student_string = $student_id . "  ";

        }

        if ($student_id > 99 && $student_id < 1000) {

            $student_string = $student_id . " ";

        }

        if ($student_id > 999 && $student_id < 9999) {

            $student_string = $student_id;

        }

        if ($drive_num < 10) {

            $drive_string = "0" . $drive_num;
        }

        if ($drive_num > 9 ){

            $drive_string = $drive_num;
        }

        $update .= "$drive_string\t$student_string\t" . date("Y-m-d H:i:s", $current_time) . "\n";

        file_put_contents($log_file, $update);
             
            $db_conn->close();

        // Assign drive number value to the user session 
        // (to prevent additional registrations)

            $_SESSION['drive_id'] = $drive_id;
            exit();
        }

        else {
            error_string_gen('Sorry, an error occured');
        }

    // Additional error message if verification fails
    }
    
    else{
        error_string_gen('Hit back to try again');
    }

}

// Display an error if a user tries to register a second hard drive

if (isset($_SESSION['drive_id'])){

    if ((int)$_SESSION['drive_id'] != 0){

  error_string_gen('You can only register one drive at a time!');
  exit();

    }

}

// If student is not logged in, redirect to the login page..

if (!isset($_SESSION['student_id'])) {

    $url = BASE_URL .'login.php';
            ob_end_clean();
            header("Location: $url");
            exit();
}

else {
  // Build the form for registering a hard drive

  echo '
    <center><p class="lead">Use the dropdown to select a hard drive for your studies here at Delaware Tech.<br></center>
    </div><p><p>
    <form id="driveassign" action="assign_drive.php" method="post">
    <div class="panel-body" align="center">
    <div class="col-sm">
    <select name="drive_id" required="required" oninvalid="this.setCustomValidity(\'Please select a drive number\')" class="btn btn-primary dropdown-toggle" style="width:200px" onmousedown="if(this.options.length>8){this.size=8;}"  onchange="this.size=0;this.setCustomValidity(\'\');" onblur="this.size=0;" ><option value=\'\'>Select a drive..</option>';
             
                       
                       
// Fetch server list from database, then populate a dropdown list with the results
     $sql = "SELECT * FROM drives WHERE in_use=0 AND broken=0"; 
     $result = $db_conn->query($sql);
     while($row = $result->fetch_assoc()){
        echo '<option value="'.$row['drive_id'].'">Drive '.$row['drive_num'].'</option>';
    }
    echo '</div></select></div><br></div></div></div></div><br><br><center>
    <input type="submit" class="btn btn-dark" ></center></form><br><br></div>';

}

include ('includes/footer.html'); 

?>
