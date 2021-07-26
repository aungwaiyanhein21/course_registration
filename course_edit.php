<?php

    require_once('start_session.php');

    // Insert the page header
    $page_title = 'Edit Course';
    require_once('header.php');

    // import connection settings
    require_once('connect_db.php');

    //import course class
    require_once("course_class.php");

    // Make sure the user is logged in before going any further.
    if (!isset($_SESSION['user_id'])) {
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
    $c->populateFromDB($file_db, 150); // Intro to CS
?>

<h1>Data</h1>

<?php

    $prereqs = "";
    $first = true;
    foreach($c->prereq as $p){
        if($first){
            $first = false;
        } else {
            $prereqs .= ",";
        }
        $prereqs .= $p;
    }

    $coreqs = "";
    $first = true;
    foreach($c->coreq as $c){
       if($first){
           $first = false;
       } else {
           $coreqs .= ",";
       }
       $coreqs .= $c;
    }

    echo "
    <form method=\"post\" action=\"course_update.php\" id=\"editcourseform\">
        <fieldset>
            <legend>Edit Class</legend>
            <input type=\"hidden\" name=\"id\" value=\"$c->id\" />
            <label for=\"Course_Name\">Course Name:</label>
                <input type=\"text\" name=\"Course_Name\" value=\"$c->title\"/><br />
            <label for=\"Course_Number\">Course Number:</label>
                <input type=\"text\" name=\"Course_Number\" value=\"$c->num\"/><br />
            <label for=\"Credits\">Credits:</label>
                <input type=\"text\" name=\"Credits\" value=\"$c->credits\" /><br />
            <label for=\"Description\">Description</label>
                <textarea form=\"editcourseform\" name=\"Description\"/>$c->desc</textarea><br />
            <label for=\"Prerequisites\">Prerequisites</label>
                <input type=\"text\" name=\"Prerequisites\" value=\"$prereqs\" /> <br />
            <label for=\"Coreq\">Corequisites</label>
                <input type=\"text\" name=\"Coreq\" value=\"$coreqs\" /> <br/>
            <label for=\"Fee_Type\">Fee Type:</label>
                <select name=\"Fee_Type\" form=\"editcourseform\">
                    <option value=\"None\">None</option>
                    <option value=\"Low\">Low</option>
                    <option value=\"Medium\">Medium</option>
                    <option value=\"High\">High</option>
                </select>
        </fieldset>
        <input type=\"submit\" value=\"Submit Edit\" name=\"submit\" />
    </form>
    ";

    require_once('footer.php');
