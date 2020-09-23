<?php # drive_clearp.php - N. Nasteff

// This page clears all the drive leases that have been assigned to
// the database. Use it at your own risk!


$page_header = 'Clear Drive Assignments'; 
$page_title = 'Admin';
include('includes/header.html');

if (isset($_SESSION['admin_id'])){

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['confirm'] == 'true'){

        $drive_query = "UPDATE drives SET in_use=0 WHERE in_use=1";
        $assignment_query = "DELETE FROM drive_assignments";

        mysqli_begin_transaction($db_conn, MYSQLI_TRANS_START_READ_WRITE);

        $a = sql_results($drive_query, $db_conn);
        $b = sql_results($assignment_query, $db_conn);

        // If queries are good, commit to the database..

        if ($a && $b){
        mysqli_commit($db_conn);

        success_string_gen('All Drive Leases Cleared!</h3><br><h5>You are being redirected.');
        redirect_user('admin.php');

        // Quit the script
        $db_conn->close();
        exit();
        }
    
        else{
            error_string_gen('We\'ve encountered an error. Please try again!');
        }
    }

    // Generate confirmation button

    else {
        echo '<br><h5>Are you sure you want to delete all drive assignment records?</h5>';
        
        // Create form and yes/no buttons..
        
        echo '<br><form action="drive_clear.php" method="post">
        <br><input class="btn-dark" name="verified" type="submit" value="Yes">
        <input type="hidden" name="confirm" value="true">
        <button class="btn-dark" type="button" name="back" onclick="document.location.href=\'admin.php\'">No</button></form>';
        
        }
    }

// If user is not an admin or not logged in..

else {

    $url = BASE_URL .'admin_login.php';
    ob_end_clean();
    header("Location: $url");
    exit();
    }
?>
