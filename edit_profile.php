<?php
    // Start the session
    require_once('start_session.php');

    // Insert the page header
    $page_title = 'Edit Profile';
    require_once('header.php');

    // import connection settings
    require_once('connect_db.php'); 

    // Make sure the user is logged in before going any further.
    if (!isset($_SESSION['username'])) {
        echo '<p class="login">Please <a href="login.php">log in</a> to access this page.</p>';
        exit();
    }

    // Show the navigation menu
    require_once('nav_menu.php');

    try {
        // Create (connect to) SQLite database in file
		$file_db = new PDO($connect_str, $connect_username, $connect_password);
		
		// Set errormode to exceptions
        $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        if (isset($_POST['submit'])) {
            // Grab the profile data from the POST
            $first_name = trim($_POST['firstname']);
            $last_name = trim($_POST['lastname']);
            $email = trim($_POST['email']);
        }
    }
    catch(PDOException $e) {
        // Print PDOException message
        echo $e->getMessage();
    }


?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <fieldset>
        <legend>Personal Information</legend>

        <label for="firstname">First name:</label>
        <input type="text" id="firstname" name="firstname" value="<?php if (!empty($first_name)) echo $first_name; ?>" /><br />

        <label for="lastname">Last name:</label>
        <input type="text" id="lastname" name="lastname" value="<?php if (!empty($last_name)) echo $last_name; ?>" /><br />

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php if (!empty($email)) echo $email; ?>" /><br />

    </fieldset>
    <input type="submit" value="Save Profile" name="submit" />
</form>

<?php
  // Insert the page footer
  require_once('footer.php');
?>