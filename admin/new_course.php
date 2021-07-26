<?php
    // Start the session
    require_once('start_session.php');

    // Show the navigation menu
    require_once('nav_menu.php');
    
    // Insert the page header
    $page_title = 'Course Submitted';
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

    if (!isset($_POST['submit'])) {
        echo '<p>Error in form submission. Redirecting to previous page.';
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
    $fields = array("Course_Name", "Course_Number", "Credits", "Description");
    foreach($fields as $curr){
        if(empty($_POST[$curr])){
            echo '<p>Error in form submission. Please try again.';
            exit();
        }
    }

    $c->title = $_POST['Course_Name'];
    $c->num = $_POST['Course_Number'];
    $c->credits = $_POST['Credits'];
    $c->desc = $_POST['Description'];
    $c->fee = $_POST['Fee_Type'];

    if(!empty($_POST['Prerequisites'])){
        $c->prereq = explode(",", $_POST['Prerequisites']);
    }

    if(!empty($_POST['Coreq'])){
        $c->coreq = explode(",", $_POST['Coreq']);
    }

    $c->id = $c->getCourseId($file_db);
    $c->addToDB($file_db);