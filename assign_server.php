<?php # assign_server.php - N. Nasteff

// This page allows ITN students to register their harddrives
// on a server for hosting purposes. They will provide a brief description
// of what they are hosting after selecting an available server from
// the dropdown list

// Set the page title and include the HTML header:

$page_title = 'Register a server';
$page_header = 'Server Registrar'; 
include ('includes/header.html');


// If server registration form has been sent AND session contains a student ID
// (logged in)

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['student_id'])) {

    $server_num = $student_id = $drive_id = $description = FALSE;

    // Retrieve student_id and drive_id from the session array
    
    if (isset($_SESSION['student_id'])){

    $temp_student_id = $_SESSION['student_id'];
}

    else {

        error_string_gen('An error occured.');
    }

    if (isset($_SESSION['drive_id'])){

        $drive_id = $_SESSION['drive_id'];
    }

    else {

        error_string_gen('An error occured.');
    }

    // Trim all the incoming data..

    $trimmed = array_map('trim', $_POST);

    // Filter the description string (users will describe what they are hosting on the server)..

    if (isset($trimmed['description'])){

        $description = htmlspecialchars($trimmed['description']);
        $description = mysqli_real_escape_string($db_conn, $description);

    // Verify server number is valid and if the server is available

    if ((int)$trimmed['server_num'] > 0 && (int)$trimmed['server_num'] < 19){
        
        $temp_server_num = (int)$trimmed['server_num'];
    }

    else {
        
        error_string_gen('Server number mismatch. Hit back and try again.');
    }

    // Verify that the server is unoccupied with a query to the
    // servers database

    $server_query = 'SELECT * FROM servers WHERE server_num='.$temp_server_num.' AND available = 1';
    $result = sql_results($server_query, $db_conn);

    if (mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $server_num = (int)$row['server_num'];
    }

    else {
        error_string_gen('Sorry, looks like this server is taken. Hit back and try again.');
        exit();
    }

    // Check if the user already has a server assigned...

    $check_student_query = 'SELECT * FROM server_assignments WHERE student_id='.$temp_student_id;
    $result = sql_results($check_student_query, $db_conn);

    if (mysqli_num_rows($result) > 0){
        error_string_gen('You can only lease one server at a time!');

    }

    else {
        $student_id = $temp_student_id;
    }

    // If all values are validated...

    if ($drive_id && $server_num && $student_id && $description){

        // Get the end time for the lease

        $lease_end = get_class_time();

        // Build the queries for transaction

        $server_assign_query = "INSERT INTO `server_assignments`(`server_num`, `student_id`, `description`, `lease_time`, `expire_time`) VALUES ($server_num,$student_id,\"$description\",NOW(), '$lease_end')";
        $server_availabiltiy_query = "UPDATE servers SET available = 0 WHERE server_num=$server_num";

        // Begin transaction

        mysqli_begin_transaction($db_conn, MYSQLI_TRANS_START_READ_WRITE);

        $result_a = sql_results($server_assign_query, $db_conn);
        $result_b = sql_results($server_availabiltiy_query, $db_conn);
        
        // If queries are good, commit to the database..

        if ($result_a && $result_b){
        mysqli_commit($db_conn);

        // Generate success message and redirect the student..

        success_string_gen('Your server has been leased. You are being redirected.');
        redirect_user('mydrive.php');
        
        // Get the current time and get the contents of the server_log text file

        $current_time = time();
        $log_file = LOG_DIR . 'server_log';
        $update = file_get_contents($log_file);

        /* The next conditionals are used to format the spacing in the log file, specifically
        the server number, description and student id.*/

        // If the description is longer than 30 characters, truncate it

        if (strlen($description) > 30){

            $description = substr($description, 0, 29);

        }

        // Check the length of the description, and add spaces til the length is 30. 

        for ($i=strlen($description);$i<29;$i++){

            $desc_spacing .= " ";
        }

        $description .= $desc_spacing;
        
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

        if ($server_num < 10) {

            $server_string = "0" . $server_num;
        }

        if ($server_num > 9 ){

            $server_string = $server_num;
        }

        $update .= "$server_string\t$student_string\t$description\t" . date("Y-m-d H:i:s", $current_time) . "\n";

        file_put_contents($log_file, $update);
         
        // Quit the script

        $db_conn->close();

        // Add server number to session array..

        $_SESSION['server_num'] = $server_num;

        exit();
}
        else{
            error_string_gen('There was a problem. Please hit back and try again.');
        }
    }
}
}
    // If the student is not logged in, redirect to the login page

    elseif (!isset($_SESSION['student_id'])){

        $url = BASE_URL . 'home.php';
            ob_end_clean();
            header("Location: $url");
            exit();

        }

// Build the form with html

    elseif (!isset($_SESSION['server_num'])) {
        echo '   
                <p class="blockquote">Select a server from the available list below:<br>
                
                <form id="driveassign" action="';

                 // Set form action to PHP_SELF

                echo htmlentities($_SERVER['PHP_SELF']);

                // Create dropdown for servers available..

                 echo '" method="post">   
                    <div class="col-sm">
                    <select type="number" name="server_num" id="server_num" required="required" oninvalid="this.setCustomValidity(\'You must select a server!\')" class="btn btn-primary dropdown-toggle" style="width:200px" onmousedown="if(this.options.length>8){this.size=8;}"  onchange="this.size=0;this.setCustomValidity(\'\');" onblur="this.size=0;" >';

                // Query the DB for available servers
                
                $server_query = 'SELECT * FROM servers WHERE available=1';
                $result = sql_results($server_query, $db_conn);
                echo '<option value="">Select a server..</option>'; 
                
                // Populate dropdown with available servers from query results..
                
                while ($row = mysqli_fetch_array ($result, MYSQLI_ASSOC)) {
                    echo '<option value="'.$row['server_num'].'">Server '.$row['server_num'].'</option>'; 
                }

                echo '</select>
                </div>     
                <div class="col-sm">
                    <br>
                    <p>Enter a brief description of what\'s on the drive:<br><small>Note: The description can only be 30 characters max.</small><br><br><textarea required="required" name="description" rows="2" cols="30" placeholder="Ubuntu Server 18.04.3 LTS"></textarea> <br></p>
                          </div>

            <center>
            <input type="submit" value="Register" class="btn btn-dark" >
        </center>
    </form>

    </div>';
}


include ('includes/footer.html'); 


?>