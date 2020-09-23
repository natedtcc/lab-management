<?php # student_clear.php - N. Nasteff

// This page clears all the student data, along with the drive lease
// and server lease data on the database. Use at your own risk!

$page_title = 'Admin';
$page_header = 'Clear student data';
include('includes/header.html');

// Make sure admin is logged in..

if (isset($_SESSION['admin_id'])){

        
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['confirm'] == 'true'){

        // Build queries to update the database

        $server_query = "UPDATE servers SET available=1 WHERE available=0";
        $drive_query = "UPDATE drives SET in_use=0 WHERE in_use=1";
        $server_lease_query = "DELETE FROM server_assignments";
        $drive_lease_query = "DELETE FROM drive_assignments";
        $student_query = "DELETE FROM students";

        mysqli_begin_transaction($db_conn, MYSQLI_TRANS_START_READ_WRITE);

        $a =sql_results($server_query, $db_conn);
        $b = sql_results($drive_query, $db_conn);
        $c = sql_results($server_lease_query, $db_conn);
        $d = sql_results($drive_lease_query, $db_conn);
        $e = sql_results($student_query, $db_conn);

    // If queries are good, commit to the database..

        if ($a && $b && $c && $d && $e){
        
            mysqli_commit($db_conn);

            // Print success string, redirect the admin to the admin panel..

            success_string_gen('All Server Leases Cleared!</h3><br><h5>You are being redirected.<script>window.setTimeout(function(){window.location.href = "admin.php";}, 3000);</script>');

            // Quit the script
            $db_conn->close();
            exit();
    }
        else{
            error_string_gen('We\'ve encountered an error. Please try again!');
            }
        }

        // Generate confirmation page..

    else {

        echo '<h5>Students will also have to register again.<br>
            Are you sure you want to delete all student records?' 
            . error_string_gen('<b>WARNING: THIS WILL CLEAR ALL DRIVE LEASES AND SERVER LEASES!</b>').'
            </h5>';
        
        // Create form and yes/no buttons..
        
        echo '<br><form action="server_clear.php" method="post">
        <br><input class="btn-dark" name="verified" type="submit" value="Yes">
        <input type="hidden" name="confirm" value="true">
        <button class="btn-dark" type="button" name="back" onclick="document.location.href=\'admin.php\'">No</button></form>';
        

    }

}

// If admin is not logged in, redirect the user..

else {

    $url = BASE_URL .'admin_login.php';
    ob_end_clean();
    header("Location: $url");
    exit();
}

?>