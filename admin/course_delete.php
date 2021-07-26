<?php

    require_once('start_session.php');
    // Show the navigation menu
    require_once('nav_menu.php');

    // Insert the page header
    $page_title = 'Delete Course';
    require_once('header.php');

    // import connection settings
    require_once('connect_db.php');

    //import course class
    require_once("course_class.php");

    // Make sure the user is logged in before going any further.
    if (!isset($_SESSION['username'])) {
        echo '<p class="login">Please <a href="login.php">log in</a> to access this page.</p>';
        exit();
    }

    try {
        // Connect to database
        $file_db = new PDO($connect_str, $connect_username, $connect_password);

        // Set errormode to exceptions
        $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e){
        echo $e->getMessage();
        exit();
    }

    $c = new Course();
    $c->populateFromDB($file_db, $_POST['id']); // Intro to CS
    $c->deleteFromDB($file_db);
?>

<p>Course deleted successfully</p>

<?php
    require_once('footer.php');
?>