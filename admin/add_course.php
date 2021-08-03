<?php
    // Start the session
    require_once('start_session.php');

    // // Show the navigation menu
    // require_once('nav_menu.php');

    // Insert the page header
    $page_title = 'Add Course';
    require_once('header.php');

    // Make sure the user is logged in before going any further.
    if (!isset($_SESSION['username'])) {
        echo '<p class="login">Please <a href="login.php">log in</a> to access this page.</p>';
        exit();
    }

    ?>
    
    <form method="post" action="new_course.php" id="courseform" xmlns="http://www.w3.org/1999/html">
        <div class="mb-3">
            <label class="form-label" for="Course_Name">Course Name:</label>
            <input class="form-control" type="text" name="Course_Name" placeholder="eg.Intro to Databases"/>
        </div>
        <div class="mb-3">
            <label class="form-label" for="Course_Number">Course Number:</label>
            <input class="form-control" type="text" name="Course_Number" placeholder="eg.CMPT 321"/>
        </div>
        <div class="mb-3">
            <label class="form-label" for="Credits">Credits:</label>
            <input class="form-control" type="text" name="Credits" placeholder="eg.4"/>
        </div>
        <div class="mb-3">
            <label class="form-label" for="Description">Description</label>
            <textarea class="form-control" name="Description" form='courseform' placeholder="Type here..."></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label" for="Prerequisites">Prerequisites</label>
            <input class="form-control" type="text" name="Prerequisites" placeholder="CMPT 100, CMPT 200, etc." />
        </div>
        <div class="mb-3">
            <label class="form-label" for="Coreq">Corequisites</label>
            <input class="form-control" type="text" name="Coreq" placeholder="CMPT 100, CMPT 200, etc." /> 
        </div>
        <div class="mb-3">
            <label class="form-label" for="Fee_Type">Fee Type:</label>
            <select class="form-control" name="Fee_Type" form="courseform">
                <option value="None">None</option>
                <option value="Low">Low</option>
                <option value="Medium">Medium</option>
                <option value="High">High</option>
            </select>
        </div>



        <!-- <fieldset>
            <legend>Add Class</legend>
            <label for="Course_Name">Course Name:</label>
                
            <label for="Course_Number">Course Number:</label>
                <input type="text" name="Course_Number" placeholder="eg.CMPT 321"/><br />
            <label for="Credits">Credits:</label>
                <input type="text" name="Credits" placeholder="eg.4"/><br />
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
        </fieldset> -->
        <h4>NOTE: After adding course, please add course section.</h4>
        <input class="btn btn-primary" type="submit" value="Add Class" name="submit" />
    </form>

<?php

    require_once('footer.php');