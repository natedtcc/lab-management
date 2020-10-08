<?php # delete_drive_lease.php - N. Nasteff

/* This page allows the admin to modify an indivual record located in the
drive lease database. It produces a form with inputs based on database values
according to the user's drive_assign_id. */

$page_title = "Admin";
$page_header = "Delete Drive Lease";
include('includes/header.html');

// Assign false values for verification and lease ID

$verified = $drive_lease_id = FALSE;

// Assign drive lease id from get..

if (isset($_SESSION['admin_id'])) {

  if (isset($_GET['dli'])) {
    $drive_lease_id = (int)$_GET['dli'];
  }

  // If method is post, assign deletion verification and lease ID variables

  elseif (isset($_POST['verified']) && isset($_POST['dli'])) {

    $verified = TRUE;
    $drive_lease_id = (int)$_POST['dli'];
    $drive_id = (int)$_POST['drive_id'];
  }

  // If these values are not received, quit the script.

  else {

    error_string_gen("You have reached this page in error. Please go back and "
      . "try again, or contact the admin.");
    exit();
  }


  if ($_SERVER['REQUEST_METHOD'] == 'POST' && $verified) {

    // Gather user data via SQL..

    $delete_query = "DELETE FROM drive_assignments WHERE drive_lease_id=$drive_lease_id";
    $update_query = "UPDATE drives SET in_use = 0 WHERE drive_id=" . $drive_id;
    $student_update = "UPDATE students SET drive_id = 0 WHERE drive_id = $drive_id";

    mysqli_begin_transaction($db_conn, MYSQLI_TRANS_START_READ_WRITE);

    $check_delete = sql_results($delete_query, $db_conn);
    $check_update = sql_results($update_query, $db_conn);
    $check_student = sql_results($student_update, $db_conn);

    if ($check_delete && $check_update && $check_student) {
      mysqli_commit($db_conn);
      success_string_gen('Records deleted successfully.</h3><br><h5>You are being redirected.');
      redirect_user('view_drive_leases.php');

      // Quit the script
      $db_conn->close();
      exit();
    } else {
      error_string_gen('Sorry, and error occured.');
      exit();
    }
  }

  // Gather user data from database..

  $lease_query = "SELECT a.*, b.class_num, b.name, b.email FROM drive_assignments a\n"

    . "JOIN students b ON a.student_id=b.student_id\n WHERE a.drive_lease_id=$drive_lease_id";


  $result = mysqli_query($db_conn, $lease_query) or trigger_error("Query: $lease_query\n<br />MySQL Error: " . mysqli_error($db_conn));
  $user_data = mysqli_fetch_array($result, MYSQLI_ASSOC);

  // Table header

  echo '<br><table cellpadding="20" class="table-striped" align="center">
      <center><th>User/Group</th><th><center>Email</th><th>Drive ID</th><th>
      Class Number</th><th><center>Lease Date</th><th></th><th></th>';


  // Print user details..

  echo '<tr><td>' . $user_data['name'] . '</td><td>' . $user_data['email'] . '</td><td>' . $user_data['drive_id'] . '</td><td>' . $user_data['class_num'] . '</td><td>' . $user_data['lease_date'] . '</td></tr></table>';

  echo '<br><h5>Are you sure you want to delete this record?</h5>';

  // Create form and yes/no buttons..

  echo '<br><form action="delete_drive_lease.php" method="post">
	<br><input class="btn-dark" name="verified" type="submit" value="Yes">
	<input type="hidden" name="dli" value="' . $drive_lease_id . '">
	<input type="hidden" name="drive_id" value="' . $user_data['drive_id'] . '">
	<button class="btn-dark" type="button" name="back" onclick="document.location.href=\'view_drive_leases.php\'">No</button></form>';
} else {

  $url = BASE_URL . 'admin_login.php';
  ob_end_clean();
  header("Location: $url");
  exit();
}