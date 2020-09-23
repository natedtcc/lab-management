<?php # server_clear.php - N. Nasteff

// This page clears all the current records held in the database regarding
// server leases. Use at your own risk!

$page_header = 'Clear server leases';
$page_title = 'Admin'; 
include('includes/header.html');

// Make sure admin is logged in..

if (isset($_SESSION['admin_id'])){

    // If the user confirms record deletion..
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['confirm'] == 'true'){

        $server_query = "UPDATE servers SET available=1 WHERE available=0";
        $lease_query = "DELETE FROM server_assignments";

        mysqli_begin_transaction($db_conn, MYSQLI_TRANS_START_READ_WRITE);


        $a = sql_results($server_query, $db_conn);
        $b = sql_results($lease_query, $db_conn);

        // If queries are good, commit to the database..

        if ($a && $b){
        
            mysqli_commit($db_conn);

            // Print success string, redirect the admin to the admin panel..

            success_string_gen('All Server Leases Cleared!<br><h5>You are being redirected.');
            redirect_user('admin.php');

            // Quit the script
            $db_conn->close();
            exit();
        }
        else{
            error_string_gen('We\'ve encountered an error. Please try again!');
            }
        }

    // Display the confirmation page..

    else {

        echo '<br><h5>Are you sure you want to delete all server assignment records?</h5>';
        
        // Create form and yes/no buttons..
        
        echo '<br><form action="server_clear.php" method="post">
        <br><input class="btn-dark" name="verified" type="submit" value="Yes">
        <input type="hidden" name="confirm" value="true">
        <button class="btn-dark" type="button" name="back" onclick="document.location.href=\'admin.php\'">No</button></form>';
        
    }

}

else {

    $url = 'admin_login.php';
    ob_end_clean();
    header("Location: $url");
    exit();
}

include ('includes/footer.html');

?>