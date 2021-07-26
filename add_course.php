<?php
    // Start the session
    require_once('start_session.php');

    // Insert the page header
    $page_title = 'Add Course';
    require_once('header.php');

    // Make sure the user is logged in before going any further.
    if (!isset($_SESSION['user_id'])) {
        echo '<p class="login">Please <a href="login.php">log in</a> to access this page.</p>';
        exit();
    }

    ?>

    <form method="post" action="new_course.php" id="courseform" xmlns="http://www.w3.org/1999/html">
        <fieldset>
            <legend>Add Class</legend>
            <label for="Course_Name">Course Name:</label>
                <input type="text" name="Course_Name" /><br />
            <label for="Course_Number">Course Number:</label>
                <input type="text" name="Course_Number" /><br />
            <label for="Credits">Credits:</label>
                <input type="text" name="Credits" /><br />
            <label for="Description">Description</label>
            <textarea name="Description" form='courseform' placeholder="Type here..."></textarea><br />
            <label for="Prerequisites">Prerequisites</label>
                <input type="text" name="Prerequisites" placeholder="CMPT 100, CMPT 200, etc." /> <br />
            <label for="Coreq">Corequisites</label>
                <input type="text" name="Coreq" placeholder="CMPT 100, CMPT 200, etc." /> <br/>
            <label for="Fee_Type">Fee Type:</label>
                <select name="Fee_Type" form="courseform">
                    <option value="None">None</option>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>
        </fieldset>
        <input type="submit" value="Add Class" name="submit" />
    </form>

<?php

    require_once('footer.php');