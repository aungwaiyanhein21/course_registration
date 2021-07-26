<?php
    // Start the session
    require_once('start_session.php');

    // Insert the page header
    $page_title = 'Section Submitted';
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

    if($_POST['Location'] == "SELECT_BUILDING" || $_POST['Course_Type'] == "SELECT_COURSE" || $_POST['Taught_By'] == "SELECT_PROFESSOR"){
        echo "<p>Error in form submission. Redirecting to previous page.";
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

    $s = new Section();

    $s->letter = "C";
    $s->semester = "FALL2019";
    $s->building_id = $_POST['Location'];
    $s->max_enroll = $_POST['Max_Students'];
    $s->professor_id = $_POST['Taught_By'];
    $s->course_id = $_POST['Course_Type'];
    $s->room_number = $_POST['Room_Number'];

    $times = explode(",", $_POST['Timeslot_Ids']);
    foreach( $times as $t ){
        array_push($s->timeslot_ids, trim($t));
    }

    if($_POST['Mod_Type'] != "None"){
        $s->mod_type = $_POST['Mod_Type'];
    }

    $s->insert($file_db);
