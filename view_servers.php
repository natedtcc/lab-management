<?php # view_servers.php - N. Nasteff

$page_title = "Admin";
$page_header = "View/Modify Servers";
include('includes/header.html');

if (isset($_SESSION['admin_id'])){

// Define limit on number of results to display from the query (pagination)

    $display = 10;

    // If page numbers have already been assigned..

    if (isset($_GET['p']) && is_numeric($_GET['p'])) {
        $pages = $_GET['p'];
    } 

    // Else, determine number of pages

    else { // Need to determine.
        // Count the number of records:
        $count_query = "SELECT COUNT(server_num) FROM servers";
        $result = @mysqli_query ($db_conn, $count_query);
        $row = @mysqli_fetch_array ($result, MYSQLI_NUM);
        $drive_count = $row[0];
        // Calculate the number of pages...
        if ($drive_count > $display) { // More than 1 page.
            $pages = ceil ($drive_count/$display);
        } else {
            $pages = 1;
        }
    }
    // Determine where in the database to start returning results...
    if (isset($_GET['s']) && is_numeric($_GET['s'])) {
        $start = $_GET['s'];
    } 
    
    else {
        $start = 0;
    }

    echo '<center><h2 class=blockquote>Servers</h2>';

    // Define the query for listing drive assignments
    $server_query = "SELECT * FROM servers ORDER BY server_num ASC LIMIT $start, $display"; 

    // Run the query.
    $result = mysqli_query ($db_conn, $server_query);

    // Table header:
    echo '<table cellpadding="20" class="table-striped" align="center">
        <center><th>Server Number</th><th><center>Type</th><th>Available?</th><th></th>';
        

    // Fetch and print all the records....
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    echo '<tr><td><center>'.$row['server_num'].'</td><td>'.$row['type'].'</td><td><center>'.boolean_format($row['available']).'</td><td><a href="delete_server.php?sid='.$row['server_id'].'"">Delete</a></td>';

        
    }

    echo '</table>';

    // Free sql result and close the connection..

    mysqli_free_result ($result);
    mysqli_close($db_conn);

    // Make the links to other pages, if necessary.
    if ($pages > 1) {
        
        echo '<br /><p>';
        $current_page = ($start/$display) + 1;
        
        // If it's not the first page, make a Previous button:
        if ($current_page != 1) {
            echo '<a href="view_servers.php?s=' . ($start - $display) . '&p=' . $pages . '"><<</a> ';
        }
        
        // Make all the numbered pages:
        for ($i = 1; $i <= $pages; $i++) {
            if ($i != $current_page) {
                echo '<a href="view_servers.php?s=' . (($display * ($i - 1))) . '&p=' . $pages .'">' . $i . '</a> ';
            } else {
                echo $i . ' ';
            }
        } // End of FOR loop.
        
        // If it's not the last page, make a Next button:
        if ($current_page != $pages) {
            echo '<a href="view_servers.php?s=' . ($start + $display) . '&p=' . $pages . '">>></a>';
        }
        
        echo '</p>'; // Close the paragraph.
        
    } 

    // Create add server button and back button..

    echo '<br><button class="btn-dark" type="button" name="add_server" onclick="document.location.href=\'add_server.php\'">Add a server</button><br><br>
    <button class="btn-dark" type="button" name="back" onclick="document.location.href=\'admin.php\'">Back to admin panel</button><br><br>';


    }


    else {

        $url = BASE_URL .'admin_login.php';
        ob_end_clean();
        header("Location: $url");
        exit();
    }
                
    include ('includes/footer.html');
        ?>
        

