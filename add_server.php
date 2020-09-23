<?php # add_server.php - N. Nasteff

/* This page will allow admins to add new servers to the database */

$page_title = "Admin";
$page_header = "Add a server";
include('includes/header.html');

// Check for admin login..

if (isset($_SESSION['admin_id']) && $_SERVER['REQUEST_METHOD'] != 'POST'){

    // Print out the table / form for adding a server..

    echo '<br><br><form action="add_server.php" method="post">
	<table class="table" cellpadding="5">
	
	<th>Server Number</th><th>Type</th>

	<tr><td><input type="number" name="server_num"/></td>
	<td><input type="text" name="type"/> </td></tr></table>
	
	<br><input class="btn-dark" type="submit" name="submit" value="Add server"/>
	<br><br><button class="btn-dark" type="button" name="back" onclick="document.location.href=\'add_server.php\'">Go back</button></form>';


}


// Check for admin login AND post data received..

elseif (isset($_SESSION['admin_id']) && $_SERVER['REQUEST_METHOD'] == 'POST'){

    // Trim all incomming post data

    $trimmed = array_map('trim', $_POST);

    // Set false values for validation..

    $server_num = $type = FALSE;

    // Validate the server number..

    if(isset($_POST['server_num']) && is_numeric((int)$trimmed['server_num'])){

        $server_num = (int)$trimmed['server_num'];

    }

    else {
        error_string_gen('Sorry, you messed up your server number. Try again! Must be numeric.');
    }

    // Validate the server type string..

    if (isset($_POST['type'])){

        $type = string_validate($trimmed['type'], $db_conn);
    }

    else {
        error_string_gen('Sorry, you messed up your type. Try again! Must be only letters.');
    }

    // If everything is validated...

    if ($server_num && $type){

        // Create SQL query for adding a server to the DB

        $insert_query = "INSERT INTO servers (server_num, type) VALUES ($server_num, '$type')";
        sql_results($insert_query, $db_conn);

        // If the query was successful, notify the user, redirect and exit...

        if (mysqli_affected_rows($db_conn) == 1) {

            success_string_gen('Your server has been added. Redirecting you now.');
            redirect_user('view_servers.php');
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

include ('includes/footer.html');
