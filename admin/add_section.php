<?php

    $CURRENT_SEMESTER = "FALL2019";

    require_once('start_session.php');

    // // Show the navigation menu
    // require_once('nav_menu.php');

    // Insert the page header
    $page_title = 'Add Section';
    require_once('header.php');

    // Make sure the user is logged in before going any further.
    if (!isset($_SESSION['username'])) {
        echo '<p class="login">Please <a href="login.php">log in</a> to access this page.</p>';
        exit();
    }

    // import connection settings
    require_once('connect_db.php');

    //import course class
    require_once("course_class.php");

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

    $options = "";
    $query = "SELECT * FROM COURSE";

    $res = $file_db->query($query);

    foreach( $res as $row ){
        $id = $row['id'];
        $num = $row['num'];
        $title = $row['title'];
        $options .= "<option value='$id'>$num: $title</option>\n";
    }

    $buildings = "";
    $query = "SELECT * FROM building";
    $res = $file_db->query($query);

    foreach($res as $row){
        $id = $row['id'];
        $name = $row['name'];
        $buildings .= "<option value='$id'>$name</option>";
    }

    $profs = "";
    $query = "SELECT * FROM professor";
    $res = $file_db->query($query);

    foreach($res as $row){
        $id = $row['id'];
        $fname = $row['first_name'];
        $lname = $row['last_name'];

        $profs .= "<option value='$id'>$fname $lname</option>\n";
    }

    $timeslots = "";
    $query = "SELECT * FROM Time_Slot";
    $res = $file_db->query($query);

    foreach($res as $row){
        $id = $row['id'];
        $start = $row['time_start'];
        $end = $row['time_end'];
        $day = $row['day'];

        $timeslots .= "<option value ='$id'>$day $start $end</option>\n";
    }

    echo "
    <form method='POST' action='submit_section.php' id='addsectionform'>
        <div class='mb-3'>
            <label class='form-label' for='Course_Type'>Course Type</label>
            <select class='form-control' name='Course_Type' form='addsectionform'>
                <option value='SELECT_COURSE'>[SELECT COURSE]</option>
                $options
            </select>
        </div>
        <div class='mb-3'>
            <label class='form-label' for='Location'>Location</label>
            <select class='form-control' name='Location' form='addsectionform'>
                <option value='SELECT_BUILDING'>[SELECT BUILDING]</option>
                $buildings
            </select>
        </div>
        <div class='mb-3'>
            <label class='form-label' for='Taught_By'>Taught By</label>
            <select class='form-control' name='Taught_By' form='addsectionform'>
                <option value='SELECT_PROFESSOR'>[SELECT PROFESSOR]</option>
                $profs
            </select>
        </div>
        <div class='mb-3'>
            <label class='form-label' for='Room_Number'>Room Number</label>   
            <input class='form-control' type='text' name='Room_Number' />
        </div>
        <div class='mb-3'>
            <label class='form-label' for='Max_Students'>Max Students</label>    
            <input class='form-control' type='number' name='Max_Students' /> 
        </div>
        <div class='mb-3'>
            <label class='form-label' for='Timeslot_Ids'>Time Slots</label>   
            <select class='form-control' multiple name='Timeslot_Ids[]' form='addsectionform'>
                $timeslots
            </select>\t**Press down the ctrl(on window)/command(on mac) key to select multiple
        </div>
        <div class='mb-3'>
            <label class='form-label' for='Mod_Type'>Mod Type</label>
            <select class='form-control' name='Mod_Type' form='addsectionform'>
                <option value='None'>None</option>
                <option value='1'>Mod 1</option>
                <option value='2'>Mod 2</option>
            </select>
        </div>
        <input class='btn btn-primary' type='submit' value='Submit' name='submit' />
    </form>
       
    ";


//     <fieldset>
//     <legend>Add Section</legend>
//     <label for='Course_Type'>Course Type</label>
//         <select name='Course_Type' form='addsectionform'>
//             <option value='SELECT_COURSE'>[SELECT COURSE]</option>
//             $options
//         </select> <br />
//     <label for='Location'>Location</label>
//         <select name='Location' form='addsectionform'>
//             <option value='SELECT_BUILDING'>[SELECT BUILDING]</option>
//             $buildings
//         </select> <br />
//     <label for='Taught_By'>Taught By</label>
//         <select name='Taught_By' form='addsectionform'>
//             <option value='SELECT_PROFESSOR'>[SELECT PROFESSOR]</option>
//             $profs
//         </select> <br />                       
//     <label for='Room_Number'>Room Number</label>   
//         <input type='text' name='Room_Number' /> <br/>      
//     <label for='Max_Students'>Max Students</label>    
//         <input type='number' name='Max_Students' /><br />       
//     <label for='Timeslot_Ids'>Time Slots</label>   
//         <select multiple name='Timeslot_Ids[]' form='addsectionform'>
//             $timeslots
//         </select>\t**Press down the ctrl key to select multiple<br/>
//     <label for='Mod_Type'>Mod Type</label>
//         <select name='Mod_Type' form='addsectionform'>
//             <option value='None'>None</option>
//             <option value='1'>Mod 1</option>
//             <option value='2'>Mod 2</option>
//         </select>
// </fieldset>
// <input type='submit' value='Submit' name='submit' />
// </form>