<?php
    // Start the session
    require_once('start_session.php');

    // Insert the page header
    $page_title = 'Professor Courses';
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

    // connecting to database
    try {
        // Connect to database
        $file_db = new PDO($connect_str, $connect_username, $connect_password);

        // Set errormode to exceptions
        $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "SELECT username FROM Users";
        $results = $file_db->query($query);

        /*foreach ($results as $row) {
            echo '<p>'.$row['username'].'</p>';
        }*/


        #$file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        #echo "Connected successfully";

        // professor responding request to student
        if (isset($_POST['respond_request'])) {
            $status_value = $_POST['status_select_box'];
            //echo $status_value;

            if ($status_value != "") {
                $student_id_update = (int)$_POST['student_id'];
                $course_id_update = (int)$_POST['course_id'];
                $section_letter_update = $_POST['section_letter'];
                $semester_update = $_POST['semester'];

                $update_status = "";
                if ($status_value == "accept_enrollment") {
                    $update_status = "enrolled";
                }
                else if ($status_value == "waitlist") {
                    $update_status = "waitlisted";
                }
                else {
                    $update_status = "unconfirmed";
                }


                $update_query = "UPDATE enrolls
                                    SET state = '$update_status'
                                    WHERE student_id = $student_id_update 
                                    AND course_id = $course_id_update
                                    AND section_letter = '$section_letter_update'
                                    AND semester = '$semester_update'
                                    ";
                $file_db->query($update_query);

?>
                <script>
                    alert("Respond Successfully");
                </script>
                
<?php
               

            }
        }

    

        $prof_user = $_SESSION['username'];
        //$prof_user = "mbarsky";
        $prof_email = $prof_user."@simons-rock.edu";
        //echo '<p>'.$prof_email.'</p>';
        $query_1 = "SELECT C.id, num, section_letter, title FROM teaches T, Course C, Professor P WHERE T.course_id =  C.id AND P.id = T.professor_id AND P.email = \"$prof_email\"";
        //echo '<p>'.$query_1.'</p>';
        $classes = $file_db->query($query_1);
        $semester = 'FALL2019'; //for now
        $num_classes = 0;

        foreach ($classes as $row) {
            ++$num_classes;
            $title = $row['title'];
            $course_id = $row['id'];
            $course_num = $row['num'];
            $section_letter = $row['section_letter'];

            echo '<p>'.$course_num.$section_letter.' '.$title.'</p>';

            $query_2 = "SELECT student_id, state, first_name, last_name
                    FROM enrolls, Student
                    WHERE course_id = $course_id
                    AND section_letter = '$section_letter'
                    AND semester = '$semester'
                    AND enrolls.student_id = Student.id
                    ";
        //$query = "SELECT student_id, state FROM enrolls
        //    WHERE course_id = $row['id'] AND section_letter = $row['letter'] AND semester = 'FALL2019'";
            $students = $file_db->query($query_2);

            $enrolled = array();
            $waitlisted = array();
            $und = array();
            $pending = array();
            foreach ($students as $stud) {
                //echo '<h4>'.$stud['student_id'].'</h4>';
                if ($stud['state'] == 'enrolled')
                    $enrolled[] = $stud;
                else if ($stud['state'] == 'waitlisted')
                    $waitlisted[] = $stud;
                else if ($stud['state'] == 'unconfirmed')
                    $und[] = $stud;
                else if ($stud['state'] == 'pending')
                    $pending[] = $stud;

            }
            echo '<ol>Enrolled: ';
            if (count($enrolled) == 0)
                echo 'There are 0 students enrolled';
            foreach ($enrolled as $e) {
                echo '<li>'.$e['first_name'].' '.$e['last_name'].', '.$e['student_id'].'</li>';
            }
            echo '</ol>';
            
            echo '<ol>Waitlisted: ';
            if (count($waitlisted) == 0)
                echo 'There are 0 students waitlisted';
            foreach ($waitlisted as $w) {
                echo '<li>'.$w['first_name'].' '.$w['last_name'].', '.$w['student_id'].'</li>';
            }
            echo '</ol>';

            echo '<ol>Unconfirmed: ';
            if (count($und) == 0)
                echo 'There are 0 students unconfirmed';
            foreach ($und as $u) {
                echo '<li>'.$u['first_name'].' '.$u['last_name'].', '.$u['student_id'].'</li>';
            }
            echo '</ol>';

            echo '<ol>Students requesting class: ';
            if (count($pending) == 0)
                echo 'There are 0 students requesting class';
            foreach ($pending as $p) {
                $student_id = $p['student_id'];
                echo "<form method='post' action='".$_SERVER['PHP_SELF']."'>";
                    echo '<li>'.$p['first_name'].' '.$p['last_name'].', '.$p['student_id'];
                        echo "&nbsp<select name='status_select_box'>";
                            echo "<option value=''>Select...</option>";
                            echo "<option value='accept_enrollment'>Accept Enrollment</option>";
                            echo "<option value='waitlist'>Put on Waitlist</option>";
                            echo "<option value='unconfirmed'>Unconfirmed</option>";
                        echo "</select>";
                        echo "<input type='hidden' value= '$student_id' name='student_id'>";
                        echo "<input type='hidden' value='$course_id' name='course_id'>";
                        echo "<input type='hidden' value='$section_letter' name='section_letter'>";
                        echo "<input type='hidden' value='$semester' name='semester'>";
                        echo "<input type='submit' value='Respond Request' name='respond_request'>";
                    echo '</li>';
                echo "</form>";
            }
            echo '</ol>';

        }
        if($num_classes == 0){
            echo "You are currently not teaching any classes";
        }

        
?>
<!-- <!h3>Courses that I teach</h3>
<ul>
    <li>
        <p>CMPT 100 - Introduction to Computer Science</p>
        Enrolled
        <ol>
            <li>James</li>
        </ol></br>

        WaitListed
        <ol>
            <li>Tom</li>
        </ol></br>

        Undefined
        <ol>
            <li>Steve</li>
        </ol></br>
    </li>
</ul> -->

<?php
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
