<?php
  // Start the session
  require_once('start_session.php');

  // Insert the page header
  $page_title = 'Dashboard';
  require_once('header.php');

  require_once('connect_db.php');

  // Show the navigation menu
  require_once('nav_menu.php');

  // Connect to the database 
  try {
	// Connect to database 
	$file_db = new PDO($connect_str, $connect_username, $connect_password);
		
	// Set errormode to exceptions
    $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if (empty($_SESSION['username'])) {
?>
<script>
    window.location.href = "login.php";
</script>

<?php
    }

    echo "<p>Welcome ".$_SESSION['username']."</p>";
    echo "<p> View the roster at the registrar_roster page</p>";

  }
  catch(PDOException $e) {
    // Print PDOException message
    echo $e->getMessage();
  }
?>

<?php
  // Insert the page footer
  require_once('footer.php');
?>