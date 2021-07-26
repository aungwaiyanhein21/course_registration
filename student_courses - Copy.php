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
        $semester = get_curr_semester(); 
        
?>

        <h3>Course Planner</h3>

        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="submit" value="get all current courses" name="get_all_current_courses" />
        </form>

        <input type='text' placeholder="search...">
        <!--<select>
            <option></option>
        </select>
        -->
        <button>Search (still developing)</button>
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
            <input type="submit" value="(please click after selecting)filter by subject_area" name="filter_by_subject_area" />
        </form>     
        
        <!--<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="submit" value="filter by MOD 1" name="MOD_1" />
        </form>
        -->

        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="radio" value="MOD 1 Courses"  />
            <input type="submit" value="MOD 1 Courses" name="MOD_1" />
        </form>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="submit" value="MOD 2 Courses" name="MOD_2" />
        </form>

        <br />

        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="submit" value="Writing Intensive Courses" name="writing_intensive" />
        </form>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="submit" value="Cultural Perspective Courses" name="cultural_perspective" />
        </form>           

        <hr>
        
        <p>Scheduled Courses (Still developing)</p>
        <table id="addToScheduleTable" class="table table-bordered">
            <tr>
                <th>Added Course</th>
                <th></th>
            </tr>
        </table>

        <hr>

<?php
        if (isset($_POST['get_all_current_courses']) || isset($_POST['filter_by_subject_area']) || isset($_POST['MOD_1']) || 
            isset($_POST['MOD_2']) || isset($_POST['writing_intensive']) || isset($_POST['cultural_perspective']) ) {

            if (isset($_POST['get_all_current_courses'])) {
                //get all courses from db
                $query = "SELECT c.num, c.title, c.credits, s.letter
                            FROM Course c, Section s 
                            WHERE s.semester = '$semester' AND c.id = s.course_id";
                $results = $file_db->query($query);


                // for checking number of rows
                $query_check = "SELECT count(*)
                                    FROM Course c, Section s 
                                    WHERE s.semester = '$semester' AND c.id = s.course_id";
                $result_check = $file_db->query($query_check);

                echo "<p>All Courses for ".$semester."</p>";
            }
            else if (isset($_POST['filter_by_subject_area'])) {
                $subject_area_id = $_POST['subject_area_select_box'];

                if ($subject_area_id == "") {
                    // Insert the page footer
                    require_once('footer.php');
                    exit();
                }
                
                $query  = "SELECT name FROM Subject_Area WHERE id = $subject_area_id";
                $results = $file_db->query($query);
                
                $subject_area = "";
                foreach ($results as $row) { 
                    $subject_area = $row['name'];
                }

                // get filtered course by subject area
                $query = "SELECT c.num, c.title, c.credits, s.letter
                            FROM Course c, course_subject_area cs, Subject_Area sa, Section s 
                            WHERE cs.subject_area_id = $subject_area_id 
                            AND cs.subject_area_id = sa.id
                            AND cs.course_id = c.id
                            AND s.semester = '$semester' AND c.id = s.course_id
                            ";
                $results = $file_db->query($query);

                // for checking number of rows
                $query_check = "SELECT count(*)
                                    FROM Course c, course_subject_area cs, Subject_Area sa, Section s 
                                    WHERE cs.subject_area_id = $subject_area_id 
                                    AND cs.subject_area_id = sa.id
                                    AND cs.course_id = c.id
                                    AND s.semester = '$semester' AND c.id = s.course_id
                                    ";
                $result_check = $file_db->query($query_check);

                echo "<p>Filtered by Subject Area: ".$subject_area."</p>";
            }
            else if (isset($_POST['MOD_1'])) {
                // get filtered course by mod 1
                $query = "SELECT c.num, c.title, c.credits, sm.section_letter as letter
                            FROM Course c, section_mod sm, Mod_Type mt
                            WHERE sm.course_id = c.id
                            AND sm.mod_type_id = mt.id
                            AND mt.name = 'MOD 1'
                            AND sm.semester = '$semester' AND c.id = sm.course_id
                            ";
                $results = $file_db->query($query);

                // for checking number of rows
                $query_check = "SELECT count(*)
                                    FROM Course c, section_mod sm, Mod_Type mt
                                    WHERE sm.course_id = c.id
                                    AND sm.mod_type_id = mt.id
                                    AND mt.name = 'MOD 1'
                                    AND sm.semester = '$semester' AND c.id = sm.course_id
                                    ";
                $result_check = $file_db->query($query_check);

                echo "<p>MOD 1 Courses</p>";
            }
            else if (isset($_POST['MOD_2'])) {
                // get filtered course by mod 2
                $query = "SELECT c.num, c.title, c.credits, sm.section_letter as letter
                            FROM Course c, section_mod sm, Mod_Type mt
                            WHERE sm.course_id = c.id
                            AND sm.mod_type_id = mt.id
                            AND mt.name = 'MOD 2'
                            AND sm.semester = '$semester' AND c.id = sm.course_id
                            ";
                $results = $file_db->query($query);


                // for checking number of rows
                $query_check = "SELECT count(*)
                                    FROM Course c, section_mod sm, Mod_Type mt
                                    WHERE sm.course_id = c.id
                                    AND sm.mod_type_id = mt.id
                                    AND mt.name = 'MOD 2'
                                    AND sm.semester = '$semester' AND c.id = sm.course_id
                                    ";
                $result_check = $file_db->query($query_check);
                echo "<p>MOD 2 Courses</p>";
            }
            else if (isset($_POST['writing_intensive'])) {
                // get filtered course by writing intensive
                $query = "SELECT c.num, c.title,c.credits, s.letter
                            FROM Course c, section s, requirement_description r
                            WHERE s.course_id = c.id
                            AND r.requirement_id = (SELECT id FROM Requirement WHERE meaning = 'Writing Intensive')
                            AND r.program_predicates_id = (SELECT id FROM Program_Predicates WHERE meaning = 'course number')
                            AND c.id = r.value
                            AND s.semester = '$semester' AND c.id = s.course_id
                            ";
                $results = $file_db->query($query);


                // for checking number of rows
                $query_check = "SELECT count(*)
                                    FROM Course c, section s, requirement_description r
                                    WHERE s.course_id = c.id
                                    AND r.requirement_id = (SELECT id FROM Requirement WHERE meaning = 'Writing Intensive')
                                    AND r.program_predicates_id = (SELECT id FROM Program_Predicates WHERE meaning = 'course number')
                                    AND c.id = r.value
                                    AND s.semester = '$semester' AND c.id = s.course_id
                                    ";
                $result_check = $file_db->query($query_check);

                echo "<p>Writing Intensive Courses (not include all the courses because manually entered)</p>";
            }
            else if (isset($_POST['cultural_perspective'])) {
                // get filtered course by cultural perspective
                $query = "SELECT c.num, c.title, c.credits, s.letter
                            FROM Course c, section s, requirement_description r
                            WHERE s.course_id = c.id
                            AND r.requirement_id = (SELECT id FROM Requirement WHERE meaning = 'Cultural Perspective')
                            AND r.program_predicates_id = (SELECT id FROM Program_Predicates WHERE meaning = 'course number')
                            AND c.id = r.value
                            AND s.semester = '$semester' AND c.id = s.course_id
                            ";
                $results = $file_db->query($query);


                // for checking number of rows
                $query_check = "SELECT count(*)
                                    FROM Course c, section s, requirement_description r
                                    WHERE s.course_id = c.id
                                    AND r.requirement_id = (SELECT id FROM Requirement WHERE meaning = 'Cultural Perspective')
                                    AND r.program_predicates_id = (SELECT id FROM Program_Predicates WHERE meaning = 'course number')
                                    AND c.id = r.value
                                    AND s.semester = '$semester' AND c.id = s.course_id
                                    ";
                $result_check = $file_db->query($query_check);

                echo "<p>Cultural Perspective Courses (not include all the courses because manually entered)</p>";
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
                    <?php
                        $c_num = $row['num'];
                        $s_letter = $row['letter'];

                        $func_str = "add_to_schedule('".$c_num."','".$s_letter."')";

                        echo "<button onclick=\"".$func_str."\">Add To Schedule</button>";
                        //echo "<button onclick='$func_str'>Add To Schedule</button>";
                    ?>
                </td>

                <td>
                    <?php
                       
                        $href_str = 'course_detail.php?num='.$c_num.'&letter='.$s_letter.'&semester='.$semester;
                        //echo $href_str;
                        //echo "<a href='student_courses.php?num='".$c_num."'&s_letter='".$s_letter."><button>More Details</button></a>";
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