<?php # delete_server.php - N. Nasteff

/* This page will allow admins to remove servers from the database */

$page_title = "Admin";
$page_header = "Delete server";
include('includes/header.html');

// Check for admin login and GET data..

if (isset($_SESSION['admin_id'])){

    $server_id = $verified = FALSE;

    // Check for the get data (server id number) and validate it.

    if (isset($_GET['sid'])){

        $server_id = (int)$_GET['sid'];
    } 

    elseif (isset($_POST['sid']) && isset($_POST['verified'])){

        $server_id = (int)$_POST['sid'];
        $verified = TRUE;
    }
    
    else {
        error_string_gen('Sorry, you entered an invalid server number. Try again.');
        exit();
    }

    // If verified that the admin wants the server deleted...

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $verified && $server_id){

        // Create SQL query for deleting a server from the DB

        $delete_query = "DELETE FROM servers WHERE server_id=$server_id";
        sql_results($delete_query, $db_conn);

        // If the query was successful..

        if (mysqli_affected_rows($db_conn) == 1) {

            success_string_gen('Your server has been deleted. Redirecting you now.');
            redirect_user('view_servers.php');
            exit();
        }

        else {
            error_string_gen('Sorry, and error occured! Please contact the admin.');
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'GET'){


            echo '<br><h5>Are you sure you want to delete this server?</h5>';
            
            // Create form and yes/no buttons..
            
            echo '<br><form action="delete_server.php" method="post">
            <br><input class="btn-dark" name="verified" type="submit" value="Yes">
            <input type="hidden" name="confirm" value="true">
            <input type="hidden" name="sid" value='.$server_id.'">
            <button class="btn-dark" type="button" name="back" onclick="document.location.href=\'view_servers.php\'">No</button></form>';
            
        }
    }

    include ('includes/footer.html');

?>