<?php
    // Insert the page header
    $page_title = 'Sign Up';
    require_once('header.php');
    // import connection settings
    require_once('connect_db.php'); 
    
    // connecting to database
    try {
        // Connect to database 
        $file_db = new PDO($connect_str, $connect_username, $connect_password);
            
        // Set errormode to exceptions
        $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if (isset($_POST['submit'])) {
            // Grab the profile data from the POST
            $username = trim($_POST['username']);
            $password1 =trim($_POST['password1']);
            $password2 = trim($_POST['password2']);
           
            if (!empty($username) && !empty($password1) && !empty($password2) && ($password1 == $password2)) 
            {
                
                // Make sure someone isn't already registered using this username
                $query = "SELECT count(username) FROM Users WHERE username = '$username'";
                $results = $file_db->query($query);
                if($results->fetchColumn() > 0)			
                {
                    // An account already exists for this username, so display an error message
                    echo '<p class="error">An account already exists for this username. Please use a different name.</p>';
                    $username = "";
                }
                else {
                    // The username is unique, so insert the data into the database
                    //$hashed_password = password_hash($password1, PASSWORD_DEFAULT);

                    $query = "INSERT INTO Users (username, password) VALUES ('$username', '$password1')";
                    $file_db->query($query);
                    
                    
                    //for now
                    $query = "SELECT id FROM Student ORDER BY id DESC";
                    $results = $file_db->query($query);
                    $row = $results->fetch(PDO::FETCH_ASSOC);

                    $int_id = (int)$row['id'];
                    $int_id++;

                    //for prototype
                    $new_student_id = $int_id;
                    $new_student_f_name = $username[0];
                    $new_student_l_name = substr($username,1);
                    $new_student_email = $username."@simons-rock.edu";
                

                    $query = "INSERT INTO Student (id, first_name, last_name, email) VALUES ($new_student_id, '$new_student_f_name', '$new_student_l_name', '$new_student_email')";
                    $file_db->query($query);
                    

                    // Confirm success with the user
                    echo '<p>Your new account has been successfully created. You\'re now ready to <a href="login.php">Login</a>.</p>';
                }
            }
            else {
                echo '<p class="error">You must enter all of the sign-up data, including the desired password twice.</p>';
            }
        }
    }
    catch(PDOException $e) {
        // Print PDOException message
        echo $e->getMessage();
    }
?>

<p>Please enter your username and desired password to sign up for Course registration.</p>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <fieldset>
        <legend>Registration Info</legend>
        <label for="username">Username:</label>
        <input type="text" name="username" value="<?php if (!empty($username)) echo $username; ?>" /><br />

        <label for="password1">Password:</label>
        <input type="password" name="password1" /><br />

        <label for="password2">Password (retype):</label>
        <input type="password" name="password2" /><br />
    </fieldset>
    <input type="submit" value="Sign Up" name="submit" />
</form>

<?php
  // Insert the page footer
  require_once('footer.php');
?>