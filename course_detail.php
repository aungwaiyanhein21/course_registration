<?php
    // Start the session
    require_once('start_session.php');

    // Insert the page header
    $page_title = 'Course Detail';
    require_once('header.php');

    // import connection settings
    require_once('connect_db.php');

    // Show the navigation menu
    require_once('nav_menu.php');

    // Make sure the user is logged in before going any further.
    if (!isset($_SESSION['username'])) {
        echo '<p class="login">Please <a href="login.php">log in</a> to access this page.</p>';
        exit();
    }

    if (!isset($_GET['num']) || !isset($_GET['letter']) || !isset($_GET['semester'])) {
        echo '<p>No course selected! Please select the course from <a href="student_courses.php">here</a> and view the course detail </p>';
        exit();
    }

    if (isset($_GET['num']) && isset($_GET['letter']) && isset($_GET['semester'])) {
        // connecting to database
        try {
            // Connect to database
            $file_db = new PDO($connect_str, $connect_username, $connect_password);

            // Set errormode to exceptions
            $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //get from url params
            $course_num = $_GET['num'];
            $section_letter = $_GET['letter'];
            $semester = $_GET['semester'];
            $semester = "FALL2019";


            // get courses from Course table
            $query = "SELECT id, title, credits FROM Course WHERE num = '$course_num'";
            $results = $file_db->query($query);

            foreach ($results as $row) {
                $course_id = $row['id'];
                $course_title = $row['title'];
                $course_credits = $row['credits'];
            }

            // get subject area
            $query = "SELECT  s.name
                        FROM course_subject_area cs, Subject_Area s
                        WHERE cs.course_id = $course_id
                        AND cs.subject_area_id = s.id
                        ";
            $results = $file_db->query($query);

            $subject_area_str = "<h4>Subject Area: ";
            $subject_area_arr = array();
            foreach ($results as $row) {
                $subject_area = $row['name'];

                $subject_area_arr[] = $subject_area;
            }
            $subject_area_str2 = "</h4>";

            $subject_area_full_str = concat_more_than_one_item($subject_area_arr,$subject_area_str,$subject_area_str2);
            //getting description
            $query = "SELECT  descript
                         FROM course_description cd, Description d
                         WHERE cd.course_id = $course_id
                         AND d.id = cd.description_id
                         ";
            $results = $file_db->query($query);
            $description = "";
            foreach ($results as $row) {
              $description = $row['descript'];
            }

            //Getting Fee
            $query = "SELECT  amount
                         FROM course_fee cf, Fee_Type ft
                         WHERE cf.course_id = $course_id
                         AND cf.Fee_Type = ft.type
                         ";
            $results = $file_db->query($query);
            $fee = "None";
            foreach ($results as $row) {
              $fee = $row['amount'];
            }

            //Day, Time, Location
            $query = "SELECT  b.name, t.time_start, t.time_end, t.day, m.room_code
                         FROM meets_at m, Time_Slot t, Building b
                         WHERE m.course_id = $course_id
                         AND m.section_letter = '$section_letter'
                         AND b.id = m.building_id
                         AND t.id = m.time_slot_id
                         ";
            $results = $file_db->query($query);
            $meetings_str = "<h4>Meetings: ";
            $meetings_str2 = "</h4>";
            $meetings_arr = array();
            $day_dic = ["M" => "Monday",
            "T" => "Tuesday","W" => "Wednesday","R" => "Thursday","F" =>"Friday"];
            foreach ($results as $row) {
                $building_name = $row['name'];
                $start_time = $row['time_start'];
                $end_time = $row['time_end'];
                $day = $day_dic[$row['day']];
                $room = trim($row['room_code'], "_");
                $full = $day.' from '.$start_time." to ".$end_time. " in ".$building_name." room: ".$room."\n";
                $meetings_arr[] = $full;
            }
            $meetings_full_str = concat_more_than_one_item($meetings_arr,$meetings_str,$meetings_str2);

            //Enrollement
            $query = "SELECT  max_enroll
                         FROM Section
                         WHERE course_id = $course_id
                         ";
            $results = $file_db->query($query);
            $max_enroll = 0;
            foreach ($results as $row) {
              $max_enroll = $row['max_enroll'];
            }

            //Current Enrollement
            $query = "SELECT  student_id
                         FROM enrolls
                         WHERE course_id = $course_id
                         AND section_letter = '$section_letter'
                         AND semester = '$semester'
                         AND state = 'enrolled'
                         ";
            $results = $file_db->query($query);
            $curr_enroll = 0;
            foreach ($results as $row) {
              $curr_enroll++;
            }

            //WaitListed
            $query = "SELECT  student_id
                         FROM enrolls
                         WHERE course_id = $course_id
                         AND section_letter = '$section_letter'
                         AND semester = '$semester'
                         AND state = 'waitlisted'
                         ";
            $results = $file_db->query($query);
            $waitlist = 0;
            foreach ($results as $row) {
              $waitlist++;
            }

            //Mod type
            $query = "SELECT  mt.Name
                         FROM section_mod sm, Mod_Type mt
                         WHERE sm.course_id = $course_id
                         AND sm.section_letter = '$section_letter'
                         AND sm.semester = '$semester'
                         AND sm.mod_type_id = mt.id
                         ";
            $results = $file_db->query($query);
            $mod = "Full semester";
            foreach ($results as $row) {
              $mod = $row['Name'];
            }

            //Professors
            $query = "SELECT  p.first_name, p.last_name
                         FROM teaches t, Professor p
                         WHERE t.course_id = $course_id
                         AND t.section_letter = '$section_letter'
                         AND t.professor_id = p.id
                         ";
            $results = $file_db->query($query);
            $professors_str = "";
            $temp = 0;
            foreach($results as $row){
                if($temp > 0)
                    $professors_str = $professors_str.' ,';
                $professors_str = $professors_str.$row['first_name'].' '.$row['last_name'];
                $temp += 1;
            }

            echo "<h4>Course Num: ".$course_num."</h4>";
            echo "<h4>Section Letter: ".$section_letter."</h3>";
            echo "<h4>Course Title: ".$course_title."</h4>";
            echo "<h4>Credits: ".$course_credits."</h4>";
            echo "<h4>Semester: ".$semester."</h4>";
            echo $subject_area_full_str;
            echo "<h4> Taught by: ".$professors_str."</h4>";
            echo "<h4>Description: ".$description."</h4>";
            echo "<h4>Fee: ".$fee."</h4>";
            echo $meetings_full_str;
            echo "<h4>Maximum Enrollment: ".$max_enroll."</h4>";
            echo "<h4>Current Enrollment: ".$curr_enroll."</h4>";
            echo "<h4>Waitlisted: ".$waitlist."</h4>";
            echo "<h4>Mod Type: ".$mod."</h4>";
            

           // get description
           /*$query = "SELECT  s.name
                        FROM course_description cd, Description d
                        WHERE cd.course_id = $course_id
                        AND cs.subject_area_id = s.id
                        ";
            $results = $file_db->query($query);
            */






        }
        catch(PDOException $e) {
            // Print PDOException message
            echo $e->getMessage();
        }
    }



    function concat_more_than_one_item($arr,$str1,$str2) {
        if (count($arr) == 1) {
            return $str1.$arr[0].$str2;
        }
        else {
            $str_concat = "";
            for ($i=0; $i < count($arr); $i++) {
                if ($i == (count($arr)-1)) {
                    $str_concat .= $arr[$i];
                }
                else {
                    $str_concat .= $arr[$i].", ";
                }
            }
            return $str1.$str_concat.$str2;
        }

    }
?>

<?php
    // Insert the page footer
    require_once('footer.php');
?>
