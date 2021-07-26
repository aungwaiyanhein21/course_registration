<?php
    // Start the session
    require_once('start_session.php');

    // Insert the page header
    $page_title = 'Student View';
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

        // for all courses

        //eg. FALL2019 depending on current date
        //$semester = get_curr_semester(); 
        $semester = "FALL2019"; //for now


        // add to schedule courses
        $added_courses = array();
        
?>

        <h3>Course Planner</h3>

        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="submit" value="get all current courses" name="get_all_current_courses" />
        </form>

        </br>

        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <select name="search_filter">
                <option value="">Search type...</option>
                <option value="course_num">Course Number</option>
                <option value="course_title">Course Title</option>
                <option value="professor_name">Professor Name</option>
            </select>
            <input type='text' name="search_txt" placeholder="search...">
            <input type="submit" value="Search" name="search" />
        </form>
        
        </br>

        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <select name="subject_area_select_box">
                <option value="">--Select Subject Area--</option>
<?php
                    $query2 = "SELECT id,name FROM Subject_Area ORDER BY name";
                    $results2 = $file_db->query($query2);
                    foreach ($results2 as $row2) {
                        $subject_area_id = $row2['id'];
                        echo "<option value='$subject_area_id'>".$row2['name']."</option>";
                    }

?>
            </select>
            <input type="submit" value="Apply Changes For Subject_Area" name="filter_by_subject_area" />
        </form>
        </br>   
        
 
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="radio" name="mod" value="MOD_1"  />MOD 1 Courses
            <input type="radio" name="mod" value="MOD_2" />MOD 2 Courses
            <input type="submit" value="Apply Changes For MOD" name="MOD" />
        </form>


        <br />

        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="checkbox" name="W" value="writing_intensive"/>Writing Intensive &nbsp
            <input type="checkbox" name="CP" value="cultural_perspective"/>Cultural Perspective
            <input type="submit" value="Apply Changes For W/CP courses" name="W_CP"/>
        </form>
         
        <!--<hr>-->
        
        <!--<p>Scheduled Courses (Still developing)</p>
        <table id="addToScheduleTable" class="table table-bordered">
            <tr>
                <th>Added Course</th>
                <th></th>
            </tr>
        </table>
        -->

        <hr>

<?php
        if (isset($_POST['add_to_schedule'])) {
            $added_courses[] = $_POST['added_course_id'];
        }

        foreach ($added_courses as $added_course) {
            echo "<p>$added_course</p>";
        }


        if (isset($_POST['get_all_current_courses']) || isset($_POST['filter_by_subject_area']) || isset($_POST['MOD']) || 
            isset($_POST['W_CP']) || isset($_POST['search'])) {

            if (isset($_POST['get_all_current_courses'])) {
                //get all courses from db
                $query = "SELECT c.id, c.num, c.title, c.credits, s.letter
                            FROM Course c, Section s 
                            WHERE s.semester = '$semester' AND c.id = s.course_id
                            ORDER BY c.num
                            ";
                $results = $file_db->query($query);


                // for checking number of rows
                $query_check = "SELECT count(*)
                                    FROM Course c, Section s 
                                    WHERE s.semester = '$semester' AND c.id = s.course_id
                                    ORDER BY c.num
                                    ";
                $result_check = $file_db->query($query_check);

                echo "<p>All Courses for ".$semester."</p>";
            }
            else if (isset($_POST['filter_by_subject_area'])) {
                $subject_area_id = $_POST['subject_area_select_box'];

                if ($subject_area_id == "") {
                    // Insert the page footer
                    require('footer.php');
                    exit();
                }
                
                $query  = "SELECT name FROM Subject_Area WHERE id = $subject_area_id";
                $results = $file_db->query($query);
                
                $subject_area = "";
                foreach ($results as $row) { 
                    $subject_area = $row['name'];
                }

                // get filtered course by subject area
                $query = "SELECT c.id, c.num, c.title, c.credits, s.letter
                            FROM Course c, course_subject_area cs, Subject_Area sa, Section s 
                            WHERE cs.subject_area_id = $subject_area_id 
                            AND cs.subject_area_id = sa.id
                            AND cs.course_id = c.id
                            AND s.semester = '$semester' AND c.id = s.course_id
                            ORDER BY c.num
                            ";
                $results = $file_db->query($query);

                // for checking number of rows
                $query_check = "SELECT count(*)
                                    FROM Course c, course_subject_area cs, Subject_Area sa, Section s 
                                    WHERE cs.subject_area_id = $subject_area_id 
                                    AND cs.subject_area_id = sa.id
                                    AND cs.course_id = c.id
                                    AND s.semester = '$semester' AND c.id = s.course_id
                                    ORDER BY c.num
                                    ";
                $result_check = $file_db->query($query_check);

                echo "<p>Filtered by Subject Area: ".$subject_area."</p>";
            }
            else if (isset($_POST['MOD'])) {

                if (!isset($_POST['mod'])) {
                    echo "<h4>Please choose mod type before submitting</h4>";
                    require('footer.php');
                    exit();
                }

                $mod_type = $_POST['mod'];
               
                if ($mod_type == "MOD_1") {
                    $mod = "MOD 1";
                }
                else if ($mod_type == "MOD_2") {
                    $mod = "MOD 2";
                }
               

                // get filtered course by mod 1
                $query = "SELECT c.id, c.num, c.title, c.credits, sm.section_letter as letter
                            FROM Course c, section_mod sm, Mod_Type mt
                            WHERE sm.course_id = c.id
                            AND sm.mod_type_id = mt.id
                            AND mt.name = '$mod'
                            AND sm.semester = '$semester' AND c.id = sm.course_id
                            ORDER BY c.num
                            ";
                $results = $file_db->query($query);

                // for checking number of rows
                $query_check = "SELECT count(*)
                                    FROM Course c, section_mod sm, Mod_Type mt
                                    WHERE sm.course_id = c.id
                                    AND sm.mod_type_id = mt.id
                                    AND mt.name = 'MOD 1'
                                    AND sm.semester = '$semester' AND c.id = sm.course_id
                                    ORDER BY c.num
                                    ";
                $result_check = $file_db->query($query_check);

                echo "<p>$mod Courses</p>";
            }
            else if (isset($_POST['W_CP'])) {
                
                if (!isset($_POST['W']) && !isset($_POST['CP'])) {
                    echo "<h4>Please choose Writing Intensive or Cultural Perspective or both before submitting</h4>";
                    require('footer.php');
                    exit();
                }

                if (isset($_POST['W']) && isset($_POST['CP'])) {
                    // get filtered course by writing intensive and cultural perspective
                    $query = "SELECT distinct c.id, c.num, c.title,c.credits, s.letter
                                FROM Course c, section s, requirement_description r
                                WHERE s.course_id = c.id
                                AND r.requirement_id in (SELECT id FROM Requirement WHERE meaning = 'Writing Intensive' OR meaning = 'Cultural Perspective')
                                AND r.program_predicates_id = (SELECT id FROM Program_Predicates WHERE meaning = 'course number')
                                AND c.id = r.value
                                AND s.semester = '$semester' AND c.id = s.course_id
                                ORDER BY c.num
                                ";
                    $results = $file_db->query($query);


                    // for checking number of rows
                    $query_check = "SELECT count(*)
                                        FROM Course c, section s, requirement_description r
                                        WHERE s.course_id = c.id
                                        AND r.requirement_id in (SELECT id FROM Requirement WHERE meaning = 'Writing Intensive' OR meaning = 'Cultural Perspective')
                                        AND r.program_predicates_id = (SELECT id FROM Program_Predicates WHERE meaning = 'course number')
                                        AND c.id = r.value
                                        AND s.semester = '$semester' AND c.id = s.course_id
                                        ORDER BY c.num
                                        ";
                    $result_check = $file_db->query($query_check);

                    echo "<p>Both Writing Intensive And Cultural Perspective Courses (not include all the courses because manually entered)</p>";
                }
                else {
                    if (isset($_POST['W'])) {
                        $W_or_CP_str = "Writing Intensive";
                    }
                    else if (isset($_POST['CP'])) {
                        $W_or_CP_str = "Cultural Perspective";
                    }

                    // get filtered course by writing intensive or cultural perspective
                    $query = "SELECT c.id, c.num, c.title,c.credits, s.letter
                                FROM Course c, section s, requirement_description r
                                WHERE s.course_id = c.id
                                AND r.requirement_id = (SELECT id FROM Requirement WHERE meaning = '$W_or_CP_str')
                                AND r.program_predicates_id = (SELECT id FROM Program_Predicates WHERE meaning = 'course number')
                                AND c.id = r.value
                                AND s.semester = '$semester' AND c.id = s.course_id
                                ORDER BY c.num
                                ";
                    $results = $file_db->query($query);


                    // for checking number of rows
                    $query_check = "SELECT count(*)
                                        FROM Course c, section s, requirement_description r
                                        WHERE s.course_id = c.id
                                        AND r.requirement_id = (SELECT id FROM Requirement WHERE meaning = '$W_or_CP_str')
                                        AND r.program_predicates_id = (SELECT id FROM Program_Predicates WHERE meaning = 'course number')
                                        AND c.id = r.value
                                        AND s.semester = '$semester' AND c.id = s.course_id
                                        ORDER BY c.num
                                        ";
                    $result_check = $file_db->query($query_check);

                    echo "<p>$W_or_CP_str Courses (not include all the courses because manually entered)</p>";
                }
                
            }
            else if (isset($_POST['search'])) {
                $search_type = $_POST['search_filter'];

                if ($search_type == "") {
                    echo "<h4>Please select search type before submitting</h4>";

                    // Insert the page footer
                    require_once('footer.php');
                    exit();
                }

                $search_text = trim($_POST['search_txt']);

                if ($search_type == "course_num") {
                    $query = "SELECT c.id, c.num, c.title, c.credits, s.letter
                                FROM Course c, Section s 
                                WHERE c.num LIKE '%$search_text%'
                                AND s.semester = '$semester' 
                                AND c.id = s.course_id
                                ORDER BY c.num
                                ";
                    $results = $file_db->query($query);

                    // for checking number of rows
                    $query_check = "SELECT count(*)
                                    FROM Course c, Section s 
                                    WHERE c.num LIKE '%$search_text%'
                                    AND s.semester = '$semester' 
                                    AND c.id = s.course_id
                                    ORDER BY c.num
                                    ";
                    $result_check = $file_db->query($query_check);
                }
                else if ($search_type == "course_title") {
                    $query = "SELECT c.id, c.num, c.title, c.credits, s.letter
                                FROM Course c, Section s 
                                WHERE c.title LIKE '%$search_text%'
                                AND s.semester = '$semester' 
                                AND c.id = s.course_id
                                ORDER BY c.num
                                ";
                    $results = $file_db->query($query);

                    // for checking number of rows
                    $query_check = "SELECT count(*)
                                    FROM Course c, Section s 
                                    WHERE c.title LIKE '%$search_text%'
                                    AND s.semester = '$semester' 
                                    AND c.id = s.course_id
                                    ORDER BY c.num
                                    ";
                    $result_check = $file_db->query($query_check);
                }
                else if ($search_type == "professor_name") {
                    $query = "SELECT c.id, c.num, t.section_letter as letter, c.title, c.credits
                                FROM teaches t, Course c, Professor p
                                WHERE concat(p.first_name, ' ', p.last_name) LIKE '%$search_text%'
                                AND t.course_id = c.id 
                                AND p.id = t.professor_id 
                                ORDER BY c.num
                                ";
                    $results = $file_db->query($query);

                    // for checking number of rows
                    $query_check = "SELECT count(*)
                                        FROM teaches t, Course c, Professor p
                                        WHERE concat(p.first_name, ' ', p.last_name) LIKE '%$search_text%'
                                        AND t.course_id = c.id 
                                        AND p.id = t.professor_id 
                                        ORDER BY c.num
                                    ";
                    $result_check = $file_db->query($query_check);
                }

                echo "<p>Search Courses By $search_type: $search_text</p>";
            }
            
            // if there is no result 
            if ($result_check->fetchColumn() == 0) {
                echo "<p>No results found!</p>";
                
                // Insert the page footer
                require_once('footer.php');
                exit();
            } 
?>
        <table class="table table-striped">
            <tr>
                <th>Course Number</th>
                <th>Section Letter</th>
                <th>Title</th>
                <th>Credits</th>
                <th colspan='2'></th>
            </tr>
<?php       
            foreach ($results as $row) { 
?>
            <tr>
                <td><?php echo $row['num'] ?></td>
                <td><?php echo $row['letter'] ?></td>
                <td><?php echo $row['title'] ?></td>
                <td><?php echo $row['credits']?></td>
                <td>
                    <form method="post" action="course_schedule.php">
                        <input type="hidden" name="added_course_id" value="<?php echo $row['id'] ?>"/>
                        <input type="hidden" name="added_course_num" value="<?php echo $row['num'] ?>"/>
                        <input type="hidden" name="added_course_letter" value="<?php echo $row['letter'] ?>"/>
                        <input type="submit" value="Add To Schedule" name="add_to_schedule" />
                    </form>
                </td>

                <td>
                    <?php
                        $c_num = $row['num'];
                        $s_letter = $row['letter'];


                        $href_str = 'course_detail.php?num='.$c_num.'&letter='.$s_letter.'&semester='.$semester;
                        
                        echo "<a href='$href_str' target='_blank'><button>More Details</button></a>";
                    ?>
                </td>
            </tr>
<?php
            }
?>
        </table>
<?php
        }
    }
    catch(PDOException $e) {
        // Print PDOException message
        echo $e->getMessage();

    }


    //functions
    // for getting semester based on current data (eg. FALL2019)
    function get_curr_semester() {
        //get current month and year
        $current_month = date('m');
        $current_year = date("Y");

        // arrays of months
        $fall_array = array("06","07","08","09","10","11","12");
        $spring_array = array("01","02","03","04","05");
        
        // check if curr month in fall array or spring array
        if (in_array($current_month,$fall_array)) {
            $semester_string = "FALL".$current_year;
        }
        else if (in_array($current_month,$spring_array)) {
            $semester_string = "SPRING".$current_year;
        }
        return $semester_string;
    }

?>


<?php
  // Insert the page footer
  require_once('footer.php');
?>