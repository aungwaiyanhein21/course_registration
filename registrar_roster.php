<?php
    // Start the session
    require_once('start_session.php');

    // Insert the page header
    $page_title = 'Registrar Roster';
    require_once('header.php');

    // import connection settings
    //require_once('connect_db.php'); 

    // Make sure the user is logged in before going any further.
    if (!isset($_SESSION['user_id'])) {
        echo '<p class="login">Please <a href="login.php">log in</a> to access this page.</p>';
        exit();
    }

     // Show the navigation menu
    require_once('nav_menu.php');

    // connecting to database
    try {
        // Connect to database 
        //$file_db = new PDO($connect_str, $connect_username, $connect_password);
            
        // Set errormode to exceptions
        //$file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        
        //for testing
        
?>

<script>
    var testing = "test";
</script>

<h3>Courses with students enrolled</h3>
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
</ul>

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