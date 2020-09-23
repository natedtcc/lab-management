<?php # view_students.php - N. Nasteff

/* This page displays all the currently registered students in the database. 
Admins are given the option to filter the users by class number, as well as 
delete a record.*/

$page_title = "Admin";
$page_header = "View Students";
include('includes/header.html');
echo '<br><div class="container" padding="10px">
            <div class="panel panel-default">';

// Define string for filtering by class

$admin_string = 'view_students.php?class=';

// Number of results to display per page..

$display = 10;

// Determine class sort number..

$class_sort = FALSE;

if (isset($_SESSION['admin_id'])){


	if (isset($_GET['class']) && is_numeric($_GET['class'])){
		
		$class_sort = (int)$_GET['class'];
		
	}

	// If page numbers have already been assigned..

	if (isset($_GET['p']) && is_numeric($_GET['p'])) {
	    $pages = $_GET['p'];
	} 

	// Else, determine number of pages

	else { 
	    // Count the number of records. If class filter is applied, limit to appropriate class number.

		if ($class_sort){
			$count_query = "SELECT COUNT(student_id) FROM students WHERE class_num=$class_sort";
		}
		
		else {
			$count_query = "SELECT COUNT(student_id) FROM students";
	    }
		
		$result = @mysqli_query ($db_conn, $count_query);
	    $row = @mysqli_fetch_array ($result, MYSQLI_NUM);
	    $drive_count = $row[0];
	    // Calculate the number of pages...
	    if ($drive_count > $display) { // More than 1 page.
	        $pages = ceil ($drive_count/$display);
	    } else {
	        $pages = 1;
	    }
	} // End of p IF.

	// Determine where in the database to start returning results...
	if (isset($_GET['s']) && is_numeric($_GET['s'])) {
	    $start = $_GET['s'];
	} else {
	    $start = 0;
	}

	// Define the query (checks if results are to be sorted by ITN class)

	if ($class_sort){
		
        $student_query = "SELECT * FROM students WHERE class_num=$class_sort ORDER BY email ASC LIMIT $start, $display"; 
	}

	else {

        $student_query = "SELECT * FROM students ORDER BY email ASC LIMIT $start, $display"; 

	}

	// Execute query     

	$result = mysqli_query ($db_conn, $student_query);


	// Create a dropdown for a class filter (to filter the results by ITN class number)..

	echo 'Filter by class: <select onChange="window.location.href=this.value"><option value="view_students.php">None</option>';

	foreach (ITN_CLASSES as $classnum){
		
		// Populate the dropdown options. If results are sorted by class number,
		// that option gets the selected tag.
		
		if ($classnum == $class_sort){
			echo '<option value="'.$admin_string, $classnum.'" selected>'.$classnum.'</option>';
		}
		else {
			echo '<option value="'.$admin_string, $classnum.'">'.$classnum.'</option>';
		}
	}
	echo '</select>';

	// Table header:
	echo '<br><table cellpadding="10" class="table-striped" align="center">
	      <center><th>Name/Group</th><th><center>Email</th><th>Class Number</th><th>
	      Register Date</th><th><center>Active?</th><th></th>';
	    

	// Fetch and print all the records....
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	  echo '<tr><td>'.$row['name'].'</td><td>'.$row['email'].'</td><td>'.$row['class_num'].'</td><td>'.$row['reg_date'].'</td><td>'.boolean_format($row['active']).'</td><td><a href="delete_student.php?sid='.$row['student_id'].'"">Delete</a>';

	    
	} // End of WHILE loop.

	echo '</table>';

	// Create back button..

	echo '<br><button class="btn-dark" type="button" name="back" onclick="document.location.href=\'admin.php\'">Back to admin panel</button>';


	mysqli_free_result ($result);
	mysqli_close($db_conn);

	// Make the links to other pages, if necessary.
	if ($pages > 1) {
	    
	    echo '<br><p class="blockquote">';
	    $current_page = ($start/$display) + 1;
	    
	    // If it's not the first page, make a Previous button:
	    if ($current_page != 1) {
			
			if ($class_sort){
				echo '<a href="view_students.php?s=' . ($start - $display) . '&p=' . $pages . '&class='. $class_sort . '"><</a> ';
			}
			else {
	        echo '<a href="view_students.php?s=' . ($start - $display) . '&p=' . $pages . '"><</a> ';
			}
	    }
	    
	    // Make all the numbered pages:
	    for ($i = 1; $i <= $pages; $i++) {
	        if ($i != $current_page) {
				if ($class_sort){
				echo '<a href="view_students.php?s=' . (($display * ($i - 1))) . '&p=' . $pages .'&class=' .$class_sort.'">' . $i . ' </a> ';
				}
				else {
	            echo '<a href="view_students.php?s=' . (($display * ($i - 1))) . '&p=' . $pages .'">' . $i . '</a> ';
				}
	        } else {
	            echo $i . ' ';
	        }
	    } // End of FOR loop.
	    
	    // If it's not the last page, make a Next button:
	    if ($current_page != $pages) {
			if ($class_sort){
				echo '<a href="view_students.php?s=' . ($start + $display) . '&p=' . $pages . '&class='.$class_sort.'">></a>';
			}
			else {
	        echo '<a href="view_students.php?s=' . ($start + $display) . '&p=' . $pages . '">></a>';
			}
	    }
	    
	    echo '</p>'; // Close the paragraph.
		// Back button..

	}

}

else {

    $url = BASE_URL .'admin_login.php';
    ob_end_clean();
    header("Location: $url");
    exit();
}
            
include ('includes/footer.html');
	?>
	

