<?php #admin.php - N. Nasteff

$page_title = "Admin";
$page_header = "Admin Panel";

include('includes/header.html');

// If an admin is logged in, print out the admin panel..

if ( isset($_SESSION['admin_id']) ){
	
// Begin admin panel..

echo '<p class="blockquote">Here you will find some utilities that utilize database queries to modify
existing records. You can alter drive data (drive number,form factor, model etc), server data (server number, in use etc),drive leases and server leases. Use this page at your own risk!</p>
<br><button id="view_drives" class="btn-dark" style="width: 200px;" onclick="document.location.href=
\'view_drives.php\'">View / Edit hard drives</button>
<br><button id="view_servers" class="btn-dark" style="width: 200px;" onclick="document.location.href=\'view_servers.php\'" padding="10">View / Edit servers</button>
<br><button id="view_students" class="btn-dark" style="width: 200px;" onclick="document.location.href=\'view_students.php\'" padding="10">View / Edit students</button>
<br><button id="view_drive_leases" class="btn-dark" style="width: 200px;"onclick="document.location.href=\'view_drive_leases.php\'">View drive leases</button>
<br><button id="view_server_leases" class="btn-dark" style="width: 200px;" onclick="document.location.href=\'view_server_leases.php\'">View server leases</button><br><br>
<button id="drive_clear" class="btn-dark" style="width: 200px;" onclick="document.location.href=\'drive_clear.php\'">Clear all drive assignments</button><br>
<center><button id="server_clear" class="btn-dark" style="width: 200px;" onclick="document.location.href=\'server_clear.php\'">Clear all server leases</button><br>
<center><button id="student_clear" class="btn-dark" style="width: 200px;" onclick="document.location.href=\'student_clear.php\'">Clear all student data</button><br>';

}

else {
	echo '<h5>You must be logged in to view this page!<br>
	Please click <a href="admin_login.php">here</a> to log in.</h5>';
	
}	
include('includes/footer.html');

?>