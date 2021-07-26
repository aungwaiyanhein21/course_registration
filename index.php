<?php
  // Start the session
  require_once('start_session.php');

  // Insert the page header
  $page_title = 'Dashboard';
  require_once('header.php');

  require_once('connect_db.php');

  // Show the navigation menu
  require_once('nav_menu.php');

  // Connect to the database 
  try {
	// Connect to database 
	$file_db = new PDO($connect_str, $connect_username, $connect_password);
		
	// Set errormode to exceptions
  $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if (empty($_SESSION['username'])) {
?>
<script>
    window.location.href = "login.php";
</script>

<?php
    }

    echo "<p>Welcome ".$_SESSION['username']."</p>";


    if ($_SESSION['role'] == "student") {
      echo "<h5>Waiting for Professors to respond</h5>";

      $user_email = $_SESSION['username']."@simons-rock.edu";

      $student_query = "SELECT id FROM Student WHERE email = '$user_email'";
      $student_results = $file_db->query($student_query);
      $student_row = $student_results->fetch(PDO::FETCH_ASSOC);

      $student_id = $student_row['id'];

      $semester = "FALL2019";
      
      //pending courses
      $state = "pending";

      $query = "SELECT c.id, c.num, c.title, c.credits, e.section_letter, e.state
                  FROM Course c, enrolls e 
                  WHERE c.id = e.course_id 
                  AND e.student_id = $student_id
                  AND e.semester = '$semester'
                  AND e.state = '$state'
                  ";
      $results = $file_db->query($query);


      echo "<ol>";

      $pending_courses_arr = array();
      foreach ($results as $row) {
        $title = $row['title'];
        

        $pending_courses_arr[] = $title;
        echo "<li>$title</li>";
        //echo "testing";
        //echo "<p>$row['title']</p>";
      }
      
      echo "</ol>";

      if (count($pending_courses_arr) == 0) {
        echo "<p>No pending courses</p>";
      }

      //enrolled courses
      echo "<h5>Enrolled Courses</h5>";
      $state = "enrolled";
      $query = "SELECT c.id, c.num, c.title, c.credits, e.section_letter, e.state
                  FROM Course c, enrolls e 
                  WHERE c.id = e.course_id 
                  AND e.student_id = $student_id
                  AND e.semester = '$semester'
                  AND e.state = '$state'
                  ";
      $results = $file_db->query($query);


      echo "<ol>";

      $enrolled_courses_arr = array();
      foreach ($results as $row) {
        $title = $row['title'];
        
        $enrolled_courses_arr[] = $title;
        echo "<li>$title</li>";
      }
      
      echo "</ol>";

      
      if (count($enrolled_courses_arr) == 0) {
        echo "<p>No enrolled courses</p>";
      }

      // waitlisted courses
      echo "<h5>Waitlisted Courses</h5>";
      $state = "waitlisted";
      $query = "SELECT c.id, c.num, c.title, c.credits, e.section_letter, e.state
                  FROM Course c, enrolls e 
                  WHERE c.id = e.course_id 
                  AND e.student_id = $student_id
                  AND e.semester = '$semester'
                  AND e.state = '$state'
                  ";
      $results = $file_db->query($query);

      echo "<ol>";

      $waitlisted_courses_arr = array();
      foreach ($results as $row) {
        $title = $row['title'];
        
        $waitlisted_courses_arr[] = $title;
        echo "<li>$title</li>";
      }
      
      echo "</ol>";

      if (count($waitlisted_courses_arr) == 0) {
        echo "<p>No waitlisted courses</p>";
      }


      // Unconfirmed Courses
      echo "<h5>Unconfirmed Courses</h5>";
      $state = "unconfirmed";
      $query = "SELECT c.id, c.num, c.title, c.credits, e.section_letter, e.state
                  FROM Course c, enrolls e 
                  WHERE c.id = e.course_id 
                  AND e.student_id = $student_id
                  AND e.semester = '$semester'
                  AND e.state = '$state'
                  ";
      $results = $file_db->query($query);

      echo "<ol>";

      $unconfirmed_courses_arr = array();
      foreach ($results as $row) {
        $title = $row['title'];
        
        $unconfirmed_courses_arr[] = $title;
        echo "<li>$title</li>";
      }
      
      echo "</ol>";

      
      if (count($unconfirmed_courses_arr) == 0) {
        echo "<p>No unconfirmed courses</p>";
      }
    }
    
    

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
