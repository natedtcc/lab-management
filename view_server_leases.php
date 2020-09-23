<?php # view_server_leases.php - N. Nasteff

// This page displays all the current server leases.

$page_title = "Admin";
$page_header = "View Server Leases";
include('includes/header.html');

if (isset($_SESSION['admin_id'])){


echo '<center><h2 class=blockquote>Server Assignments</h2>';

// Define the query for listing drive assignments
$server_query = "SELECT a.email, a.student_id, b.server_lease_id, b.server_num, b.lease_time, b.expire_time, b.description FROM students a 
JOIN server_assignments b ON a.student_id=b.student_id"; 

// Run the query.
$result = sql_results($server_query, $db_conn);

// Table header:
echo '<table cellpadding="20" class="table-striped" align="center">
    <center><th>Leasee</th><th><center>Server Number</th><th><center>Description</th>
    <th><center>Lease Time</th><th><center>Expire Time</th><th></th>';
    

// Fetch and print all the records....
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
echo '<tr align="center"><td><center>'.$row['email'].'</td><td><center>'.$row['server_num'].'</td><td><center>'.$row['description'].'</td><td><left>'.$row['lease_time'].'</td><td><left>'.$row['expire_time'].'</td>'; 
}

echo '</table>';

// Free sql result and close the connection..

mysqli_free_result ($result);
mysqli_close($db_conn);


// Create back button..

echo '<br><button class="btn-dark" type="button" name="back" onclick="document.location.href=\'admin.php\'">Back to admin panel</button><br><br>';

}

// If user is not an admin, redirect..

else {

    $url = BASE_URL .'admin_login.php';
    ob_end_clean();
    header("Location: $url");
    exit();
}
            
include ('includes/footer.html');
    ?>
    
