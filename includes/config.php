
<?php # config.php - N. Nasteff

// Define constants and functions here //

// Define LIVE variable (for development or deployment)
define('_LIVE_', TRUE);

// Define admin email address
define('ADMIN_EMAIL', 'dtcc_servers@gmail.com');

// Define Location of MYSQLi config script
define ('MYSQL', 'includes/db_config.php');

// Define base URL for redirects

define ('BASE_URL', 'https://natenasteff.com/lab_management/');
// Define base filedir for includes
define ('BASE_DIR', '/var/www/html/lab_management/');

// Define a directory for the server lease log
define ('LOG_DIR', '/var/log/lab_management/');

// Set default timezone
date_default_timezone_set ('US/Eastern');

// Define array of current ITN class numbers that will be using
// hard drives for their studies

define('ITN_CLASSES', array(150, 170, 200, 252, 253, 254, 255, 290));

function redirect_user($url){
  echo '<script>window.setTimeout(function(){window.location.href = "' 
    . BASE_URL . $url . '";}, 4000);</script>';
}

// Function to display error messages associated with the website

function error_string_gen($error){

  echo '<h5 class="text-danger">' . $error . '</h5><br>';

}


function success_string_gen($string){

  echo '<h5 class="text-success">' . $string . '</h5><br>';

}

// This function builds a query based on a student's email. The query retreives
// all of the student's information that needs to be displayed on their
// homepage (such as drive details and server lease info)

function mydrive_query($email){
  $sql = "SELECT a.email, a.class_num, a.drive_num, c.capacity, c.form_factor,"
  . "c.serial ,b.server_num, b.expire_time, b.description\n"
  . "FROM students a\n"
  . "JOIN server_assignments b ON a.student_id=b.student_id\n"
  . "JOIN drives c ON c.drive_num=a.drive_num\n"
  . "WHERE a.email=\'$email\'";
  return $sql;
}

// Function for generating a random hash for user account activation

function hash_gen(){
  return substr(md5(rand(0,1000)), 0, 8);
}

// Function used for validating strings
// This regexp method filters out anything that
// isnt a letter.

function string_validate($string, $db_conn){

  if (preg_match ('/^[A-Z \'.-]{2,20}$/i', $string)) {
    return mysqli_real_escape_string ($db_conn, $string);
  } 

  else {
    echo error_string_gen('Please enter your name!');
  }
}

// Function for validating an email address. Accepts a string and
// a database connection.

function email_validate($email, $db_conn){

  if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    return mysqli_real_escape_string ($db_conn, $email);
  }

  else {
    error_string_gen('Please enter a valid email!');
  }
}

// This function validates the password when a user is registering.
// It takes 2 password string arguments (for comparison and confirmation)
// as well as a database connection object.

function password_validate($password1, $password2, $db_conn){
  if (preg_match ('/^\w{4,20}$/', $password1) ) {
    if ($password1 == $password2) {
      return mysqli_real_escape_string ($db_conn, $password1);
    } else {
      echo error_string_gen(
        'Your password did not match the confirmed password!');
    }
  } else {
    echo error_string_gen('Please enter a valid password!');
  }
}

// This function is used any time a query is executed. It takes a query
// string and a database connection object as arguments, and returns
// an SQLI result object if the query is successful. Otherwise, it triggers
// a MYSQL error.

function sql_results($query, $db_conn){
  $result =  mysqli_query ($db_conn, $query) or 
    trigger_error("Query: $query\n<br />MySQL Error: ".mysqli_error($db_conn));
  return $result;
}

// This function validates class numbers based on the class array defined
// above.

function class_validate($temp_class_num, $class_array){
  $class_num = null;
  for ($i=0; $i<sizeof($class_array); $i++){
        if ((int)$temp_class_num == $class_array[$i]){
            $class_num = $class_array[$i];
            return $class_num;
            break;
            }   
        }

        if (!$class_num){
            error_string_gen(
              'You entered an invalid class number. Please try again.');
        }
}


// This function formats MySQL boolean values (0 and 1) into strings.

function boolean_format($boolean){

  if ($boolean == "1"){
    return "Yes";
  }

  if ($boolean == "0"){
    return "No";
  }

  if ($boolean == "Yes"){
    return 1;
  }

  if ($boolean == "No"){
    return 0;
  }
}

/* This function calculates the date/time string to be inserted into
   the database once a student leases a server. Leases will only last
   until the end of the class, or in the case of the night class, 
   it will expire at midnight. */

function get_class_time(){

  $current_date = time();

  // Define class end times
  // Add additional day for the end of day

  $morning_class_end = strtotime('10.00.00');
  $afternoon_class_end = strtotime('12.00.00');
  $night_class_end = strtotime('00.00.00 + 1 days'); 
    
  // If it's the morning class (earlier than 10am)

  if ($current_date < $morning_class_end){
    $class_end = date("Y-m-d H:i:s", $morning_class_end);
    return $class_end;
  }

  // If it's the afternoon class (past 10am but before 12pm)

  elseif ($current_date > $morning_class_end 
    && $current_date < $afternoon_class_end){

      $class_end = date("Y-m-d H:i:s", $afternoon_class_end);
      return $class_end;
    }

    // If it's a night class

    else {
      $class_end = date("Y-m-d H:i:s", $night_class_end );
      return $class_end;
    }

}

?>
