<?php

    // Start the session
    require_once('start_session.php');

    // Insert the page header
    $page_title = 'Course selection';
    require_once('header.php');

    // import connection settings
    require_once('connect_db.php'); 

    // Make sure the user is logged in before going any further.
    if (!isset($_SESSION['username'])) {
        echo '<p class="login">Please <a href="login.php">log in</a> to access this page.</p>';
        exit();
    }
    
    /*if (!isset($_POST['add_to_schedule'])) {
       echo "<p> Access Denied! Please add the courses from <a href='student_courses.php'>student_courses</a> page to access this page </p>";
       exit(); 
    }
    */
    
    
    // connecting to database
    try {
        // Connect to database 
        $file_db = new PDO($connect_str, $connect_username, $connect_password);
            
        // Set errormode to exceptions
        $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e) {
        // Print PDOException message
        echo $e->getMessage();
        exit();
    }



    // Show the navigation menu
    require_once('nav_menu.php');

    if(!isset($_POST['add_to_schedule']) && empty($_SESSION['added_courses'])) {
        echo "<p>Empty courses. Please add the <a href='student_courses.php'>courses</a><p>";
        exit();
    }
    
    // when click 'add to schedule' button, add to session and display added courses
    if (isset($_POST['add_to_schedule'])) {
        
        //unset($_SESSION['added_courses']);
        
        
        if (!isset($_SESSION['added_courses'])) {
            $_SESSION['added_courses'] = array();
        }

        $added_course_id = $_POST['added_course_id'];
        $added_course_num = $_POST['added_course_num'];
        $added_course_letter = $_POST['added_course_letter'];

        $added_course = $added_course_num." ".$added_course_letter;

        
        $is_course_exist_arr = check_element_in_session($added_course_id, $added_course_num, $added_course_letter);
        

        if ($is_course_exist_arr['is_exists'] == 0) {
            $added_course_arr = array();
            $added_course_arr['id'] = $added_course_id;
            $added_course_arr['num'] = $added_course_num;
            $added_course_arr['letter'] = $added_course_letter;
            $added_course_arr['overlapped_courses'] = array("hasn't checked");

            $_SESSION['added_courses'][] = $added_course_arr;
        }
        else {
?>
<script>
            alert("Already in the schedule. Please choose another course.");
</script>

<?php
            echo "<p>Already in the schedule. Please choose another course.</p>";
        }
        
    }

    // unset the specific course from session array
    if (isset($_POST['delete_added_course'])) {
        $course_id_to_be_removed = $_POST['course_id_to_be_removed'];
        $course_num_to_be_removed = $_POST['course_num_to_be_removed'];
        $course_letter_to_be_removed = $_POST['course_letter_to_be_removed'];

        //$course_to_be_removed = $_POST['course_to_be_removed'];


        $course_to_be_deleted_arr = check_element_in_session($course_id_to_be_removed, $course_num_to_be_removed, $course_letter_to_be_removed);

        //$index = array_search($course_to_be_removed, $_SESSION['added_courses']);
        $index = $course_to_be_deleted_arr['index'];
        
        unset($_SESSION['added_courses'][$index]);
        $_SESSION['added_courses'] = array_values($_SESSION['added_courses']); //rearrange the index after removing certain elements

    }

    // check whether there is overlap 
    if (isset($_POST['check_schedule'])) {

        $added_courses_arr = $_SESSION['added_courses']; //eg. [{"id":"1","num":"AFAM 100","letter":"A"},{"id":"21","num":"CMPT 252","letter":"A"}]

        $previous_courses_assoc_arr = array(); //eg. {"AFAM 100 A": [{"M":{"start_time": 1576933800, "end_time": 1576938900}},{"W":{"start_time": 1576933800, "end_time": 1576938900}} ], ...}

        $overlapped_courses_assoc_arr = array();

        foreach ($added_courses_arr as $each_course) {
            $each_course_id = $each_course['id'];
            $each_course_num = $each_course['num'];
            $each_course_letter = $each_course['letter'];

            $each_course_num_letter = $each_course_num." ".$each_course_letter;

        
            $each_course_day_time_arr = array(); //eg. [{"M":{"start_time": 1576933800, "end_time": 1576938900}}, {"W":{"start_time": 1576933800, "end_time": 1576938900}}]
            
            $overlap_courses_arr = array();
            
            //get time info for each course
            $course_time_query = "SELECT t.time_start, t.time_end, t.day
                                    FROM meets_at m, Time_Slot t
                                    WHERE m.course_id = $each_course_id
                                    AND m.section_letter = '$each_course_letter'
                                    AND m.time_slot_id = t.id 
                                    ORDER BY t.time_start,t.time_end 
                                 ";
            $time_results = $file_db->query($course_time_query);

            foreach ($time_results as $time_row) {
                $time_start = strtotime($time_row['time_start']);
                $time_end = strtotime($time_row['time_end']);
                $day = $time_row['day'];

                
                $overlap_arr = check_overlap($time_start, $time_end, $day, $previous_courses_assoc_arr);
                
                if (count($overlap_arr) > 0) {
                    foreach ($overlap_arr as $each_overlap_course) {
                        if (!in_array($each_overlap_course,$overlap_courses_arr)) {
                            $overlap_courses_arr[] = $each_overlap_course;
                        }
                        
                    }
                }
                
                $day_assoc_arr = array(
                    $day => array($time_start, $time_end)
                );
               
                $each_course_day_time_arr[] = $day_assoc_arr;
            }
            
            $previous_courses_assoc_arr[$each_course_num_letter] = $each_course_day_time_arr;

            $overlapped_courses_assoc_arr[$each_course_num_letter] = $overlap_courses_arr;
        }

        
        $overlapped_courses_sample = array();

        foreach ($overlapped_courses_assoc_arr as $each_course_key => $overlap_courses_array){
            $overlapped_courses_sample_arr = array();

            foreach ($overlapped_courses_assoc_arr as $another_each_course_key => $another_overlap_courses_array) {
                if ($each_course_key != $another_each_course_key) {
                    if (in_array($each_course_key, $another_overlap_courses_array)) {
                        if (!in_array($another_each_course_key, $overlapped_courses_sample_arr)) {
                            $overlapped_courses_sample_arr[] = $another_each_course_key;
                        }
                    }
                }
            }

            foreach ($overlap_courses_array as $another_overlap_course) {
                            
                if (!in_array($another_overlap_course, $overlapped_courses_sample_arr)) {
                    $overlapped_courses_sample_arr[] = $another_overlap_course;
                }
            }

            $overlapped_courses_sample[$each_course_key] = $overlapped_courses_sample_arr;
        }

        //testing
        /*
        $start_time = strtotime("14:10:00");
        $end_time = strtotime("15:35:00");

        $another_start_time = strtotime("15:36:00");
        $another_end_time = strtotime("17:10:00");

        if (($another_start_time >= $start_time && $another_start_time <= $end_time) || ($another_end_time >= $start_time && $another_end_time <= $end_time)) {
            echo "overlap";
        }
        else {
            echo "not overlap";
        }
        */
        for ($i=0; $i < count($_SESSION['added_courses']); $i++) {
            $course_full_num = $_SESSION['added_courses'][$i]['num']." ".$_SESSION['added_courses'][$i]['letter'];
            
            $_SESSION['added_courses'][$i]['overlapped_courses'] = $overlapped_courses_sample[$course_full_num];
        }
        //echo json_encode($_SESSION['added_courses']);
    }


    if (isset($_POST['submit_schedule'])) {

        $count_status = 0;
        foreach ($_SESSION['added_courses'] as $added_course_assoc_arr) {
            $added_course_overlap_arr = $added_course_assoc_arr['overlapped_courses'];
        
            $count_status = $count_status + count($added_course_overlap_arr);

        }
        if ($count_status > 0) {
?>
            <script>
                alert("There are still overlapped courses. Please check before you submit!");
            </script>
<?php
        }
        else {
            $user_email = $_SESSION['username']."@simons-rock.edu";

            $student_query = "SELECT id FROM Student WHERE email = '$user_email'";
            $student_results = $file_db->query($student_query);
            $student_row = $student_results->fetch(PDO::FETCH_ASSOC);

            $student_id = $student_row['id'];

            $semester = "FALL2019";
            $state = "pending";

            foreach ($_SESSION['added_courses'] as $courses_to_be_added) {
                $potential_course_id = $courses_to_be_added['id'];
                $potential_section_letter = $courses_to_be_added['letter'];

                $query = "INSERT INTO enrolls (student_id, course_id, section_letter, semester, state) VALUES ($student_id, $potential_course_id, '$potential_section_letter', '$semester', '$state')";
                $file_db->query($query);
            }

?>
            <script>
                alert("Successfully submitted the schedule!");
                window.location.href = "student_courses.php";
            </script>
<?php

            //echo $student_id;
        }
    }

?>
        <a href="student_courses.php"><button>Go back to add courses</button></a>
        <br>
        <table class="table table-striped">
            <tr>
                <th>Course Num</th>
                <th>Course Letter</th>
                <th>Overlapped Courses</th>
                <th></th>
            </tr>
<?php       
            foreach ($_SESSION['added_courses'] as $course_arr) { 
?>
            <tr>
                <td><?php echo $course_arr['num']; ?></td>
                <td><?php echo $course_arr['letter']; ?></td>
                <td>
                    <?php echo json_encode($course_arr['overlapped_courses']); ?>
                </td>
                <td>
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <input type="hidden" name="course_id_to_be_removed" value="<?php echo $course_arr['id']; ?>" />
                        <input type="hidden" name="course_num_to_be_removed" value="<?php echo $course_arr['num']; ?>" />
                        <input type="hidden" name="course_letter_to_be_removed" value="<?php echo $course_arr['letter']; ?>" />
                        <input type="submit" value="Remove Added Course" name="delete_added_course" />
                    </form>
                </td>
            </tr>
<?php
            }
?>

        </table>

        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="submit" value="Check" name="check_schedule">
        </form>
        <br>

        <h6>Before submitting, please make sure to click "Check" button and check that all the overlap course lists are empty (eg. [])</h6>
        <h6></h6>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="submit" value="Submit the schedule" name="submit_schedule">
        </form>

<?php
    function check_element_in_session($course_id, $course_num, $course_letter) {
        $check_arr = array();
        $is_exists = 0;
        $index = -1;
        foreach ($_SESSION['added_courses'] as $each_arr) {
            $index ++;
            if ($each_arr['id'] == $course_id && $each_arr['num'] == $course_num && $each_arr['letter'] == $course_letter) {
                $is_exists = 1;
                break;
            }
        }
        $check_arr['is_exists'] = $is_exists;
        $check_arr['index'] = $index;
        return $check_arr;
    }

    function check_overlap($time_start, $time_end, $day, $previous_courses_assoc_arr) {
        $overlap_count_assoc_arr = array();

        foreach ($previous_courses_assoc_arr as $course => $day_time_arr) {
            $course_key = $course;

            $count_overlap = 0;
            foreach ($day_time_arr as $day_time_assoc_arr) {
                foreach ($day_time_assoc_arr as $prev_day => $time_arr) {
                    if ($day == $prev_day) {
                        $prev_time_start = $time_arr[0];
                        $prev_time_end = $time_arr[1];

                        if (($time_start >= $prev_time_start && $time_start <= $prev_time_end) || ($time_end >= $prev_time_start && $time_end <= $prev_time_end)) {
                            $count_overlap ++;
                        }
                    }
                }
            }

            $overlap_count_assoc_arr[$course_key] = $count_overlap;
        }


        $overlapped_courses_arr = array();
        foreach ($overlap_count_assoc_arr as $overlap_course => $overlap_counter) {
            if ($overlap_counter > 0) {
                $overlapped_courses_arr[] = $overlap_course;
            }
        }

        return $overlapped_courses_arr;
    }

?>


<?php
  // Insert the page footer
  require_once('footer.php');
?>