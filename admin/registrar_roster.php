<?php
    // Start the session
    require_once('start_session.php');

    // Insert the page header
    $page_title = 'Registrar Roster';
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
        $query = "SELECT c.id, c.num, c.title, s.letter
                    FROM Course c, Section s
                    WHERE c.id = s.course_id ORDER BY c.title
                    ";
        $courses = $file_db->query($query);

    }

    catch(PDOException $e) {
        // Print PDOException message
        echo $e->getMessage();
    }

?>


<h2>All Courses</h2>
<?php

    $course_title_check_arr = array();
    foreach ($courses as $row) {
        $title = $row['title'];
        if (!in_array($title, $course_title_check_arr)){
            echo '<hr>';
            //$href_str = 'course_edit.php?id='.$row;
            $cid = $row['id'];
            
            echo "<div class='row align-items-center'>";
                echo 
                "<div class='col'>
                    <h3> $title </h3> 
                </div>";
                echo 
                "<div class='col btn-side-by-side'>
                    <form method=\"post\" action=\"course_edit.php\"> 
                        <input type=\"hidden\" name=\"id\" value=\"$cid\" />
                        <input class='btn btn-primary' type=\"submit\" value=\"Edit\" name=\"submit\" />    
                    </form>;
                    <form method=\"post\" action=\"course_delete.php\"> 
                        <input type=\"hidden\" name=\"id\" value=\"$cid\" />
                        <input class='btn btn-danger' type=\"submit\" value=\"Delete\" name=\"submit\" />    
                    </form>
                </div>";
    
            echo "</div>";
           

            $course_title_check_arr[] = $title;
            echo '<h4>Sections<h4>';
            }
        echo '<h5>'.$row['num'].$row['letter'].'</h5>';

        $course_id = $row['id'];
        $section_letter = $row['letter'];
        $query = "SELECT student_id, state, first_name, last_name
                    FROM enrolls, Student
                    WHERE course_id = $course_id
                    AND section_letter = '$section_letter'
                    AND semester = 'FALL2019'
                    AND enrolls.student_id = Student.id
                    ";

        //$query = "SELECT student_id, state FROM enrolls 
        //    WHERE course_id = $row['id'] AND section_letter = $row['letter'] AND semester = 'FALL2019'";
        $students = $file_db->query($query);
        $enrolled = array();
        $waitlisted = array();
        $und = array();
        foreach ($students as $stud) {
            //echo '<h4>'.$stud['student_id'].'</h4>';
            if ($stud['state'] == 'enrolled')
                $enrolled[] = $stud;
            else if ($stud['state'] == 'waitlisted')
                $waitlisted[] = $stud;
            else if ($stud['state'] == 'unconfirmed')
                $und[] = $stud;
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
            echo 'There are 0 students in an unconfirmed state';
        foreach ($und as $u) {
            echo '<li>'.$u['first_name'].' '.$u['last_name'].', '.$u['student_id'].'</li>';
        }
        echo '</ol>';
        
    }
?>
 <!-- <ul>
    <li>
        <p>CMPT 100 - Introduction to Computer Science</p>
        Enrolled

        <ol>
            <li></li>
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
    <li>
        <p>MATH 211A - Calculus II</p>
        Enrolled
        <ol>
            <li>Ron</li>
        </ol></br>

        WaitListed
        <ol>
            <li>Percy</li>
        </ol></br>

        Undefined
        <ol>
            <li>Alexander</li>
        </ol></br>
    </li>
</ul>  -->

<?php
  // Insert the page footer
  require_once('footer.php');
?>
