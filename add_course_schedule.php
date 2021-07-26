<?php
    if (isset($_POST['check_overlap'])) {

        // import connection settings
        require_once('connect_db.php'); 


        $course_arr = json_decode($_POST['check_overlap'], true);

        $course_num = $course_arr['new_course']['course_num'];
        $section_letter = $course_arr['new_course']['section_letter'];
        
        try {
            /*
            // Connect to database 
            $file_db = new PDO($connect_str, $connect_username, $connect_password);
                
            // Set errormode to exceptions
            $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


            
            // just for testing: c_id 21 for Discrete Math, c_id 37 for ESL
            //json construction for testing
            
            $course_arr = array();
            $new_courses_arr = array();
            $previous_added_course_lst = array();
            $previous_added_course_arr = array(); //associative array

            $new_courses_arr['course_num'] = "CMPT 252"; //id = 21
            $new_courses_arr['section_letter'] = "A";
            
            $previous_added_course_arr['course_num'] = "ESL 99"; //id = 37
            $previous_added_course_arr['section_letter'] = "A";
            $previous_added_course_lst[] = $previous_added_course_arr;

            $previous_added_course_arr['course_num'] = "ANTH 100"; //id = 4
            $previous_added_course_arr['section_letter'] = "A";
            $previous_added_course_lst[] = $previous_added_course_arr;

            $previous_added_course_arr['course_num'] = "FS 100"; //id = 46
            $previous_added_course_arr['section_letter'] = "C";
            $previous_added_course_lst[] = $previous_added_course_arr;


            $course_arr['new_course'] = $new_courses_arr;
            $course_arr['previous_added_courses'] = $previous_added_course_lst;
            

            //$course_arr = array(
            //    'new_course' -> array('course_num' -> 21, 'section_letter'-> 'A'),
            //    'previous_added_courses' -> array(array('course_num'-> 37, 'section_letter' -> 'A'))
            //);

            

            // json decode it to associative array
            //$course_arr = json_decode($_POST['check_overlap'], true);

            $course_num = $course_arr['new_course']['course_num'];
            $section_letter = $course_arr['new_course']['section_letter'];

            $previous_course_list = $course_arr['previous_added_courses'];

            // get course id using course number
            $query = "SELECT id FROM Course WHERE num = '$course_num'";
            $results = $file_db->query($query);    
            $row = $results->fetch(PDO::FETCH_ASSOC);
            $course_id = $row['id'];


            // get all the time slots for the new course
            $query = "SELECT  m.course_id, t.time_start, t.time_end, t.day
                        FROM meets_at m, Time_Slot t 
                        WHERE m.course_id = $course_id 
                        AND m.section_letter = '$section_letter'
                        AND m.time_slot_id = t.id
                        ";
            $results = $file_db->query($query);   

            //query other previous courses and check whether it falls between new_time_start and new_time_end for both time_start and time_end_columns
            $course_overlap = [];
            
            //echo json_encode($previous_course_list);
            
                

            foreach ($results as $row) {

                $new_course_time_start = $row['time_start'];
                $new_course_time_end = $row['time_end'];
                $new_course_day = $row['day'];


                for ($i=0; $i < count($previous_course_list); $i++) {
                    //$course_overlap = array();                
    
                    $each_obj = $previous_course_list[$i];
    
                    $previous_course_num = $each_obj['course_num'];
                    $previous_section_letter = $each_obj['section_letter'];
    
                    // get course id using course number
                    $course_query = "SELECT id FROM Course WHERE num = '$previous_course_num'";
                    $course_result = $file_db->query($course_query);    
                    $course_row = $course_result->fetch(PDO::FETCH_ASSOC);
                    $previous_course_id = $course_row['id'];
                

                    // Time slot id for ESL : 1, 2, 41, 42
                    //echo "<p>p".$previous_course_id."</p>"; 
                    // checking if the previous course overlap with current add course
                    $prev_course_query = "SELECT count(*)as count,m.course_id
                                            FROM meets_at m, Time_Slot t 
                                            WHERE m.course_id = $previous_course_id 
                                            AND m.section_letter = '$previous_section_letter'
                                            AND m.time_slot_id = t.id
                                            AND t.time_start BETWEEN '$new_course_time_start' AND '$new_course_time_end'
                                            AND t.time_end BETWEEN '$new_course_time_start' AND '$new_course_time_end'
                                            ";
                    $prev_course_results = $file_db->query($prev_course_query);  
                    //$prev_course_row = $prev_course_results->fetch(PDO::FETCH_ASSOC);
                    //$prev_course_count = $prev_course_row['count'];

                

                    foreach ($prev_course_results as $prev_row) {
                        $count_result = $prev_row['count'];
                        $prev_course_id = $prev_row['course_id']; 
                        //echo "<p>".$prev_row['count']."</p>";
                        //echo "<p>".$prev_row['course_id']."</p>";
                        
                        if ($count_result > 0) {
                            //$course_overlap['count'] = $count_result;
                            //$course_overlap['c_id'] = $prev_course_id;
                            $course_overlap[$previous_course_num] = $count_result;
                            //$count_lst[] = $course_overlap;
                        }                        
                    }
                
                    

                    //
                }
            }
            echo json_encode($course_overlap);

            //$query2 = "SELECT FROM 
            //
            //        ";
            */
            /*$course_arr = array();

            $course_arr['course_num'] = $course_num;
            $course_arr['section_letter'] = $section_letter;
            */

            echo $course_num.$section_letter;
        }
        catch(PDOException $e) {
            // Print PDOException message
            echo $e->getMessage();
        }
    
    }
?>