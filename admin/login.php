<?php
    // Start the session
    session_start();
    
    // import connection settings
    require_once('connect_db.php'); 
    

    // Clear the error message
	$error_msg = "";


    // connecting to database
    try {
        // Connect to database 
        $file_db = new PDO($connect_str, $connect_username, $connect_password);
            
        // Set errormode to exceptions
        $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // If the registrar isn't logged in, try to log them in
		if (!isset($_SESSION['username'])) 
		{
            // Insert the page header
            $page_title = 'Login';
            require('header.php');

            echo "<h3>Welcome to the Registrar's Page. Please login to get started.</h3>";


			if (isset($_POST['submit'])) 
			{
				// Grab the user-entered log-in data
				$user_username = trim($_POST['username']);
                $user_password = trim($_POST['password']);
                //$role = $_POST['specific_role'];

				if (!empty($user_username) && !empty($user_password) && $user_username == 'registrar') {
                    
                    // Look up the username and password in the database
                    // Warning: it is not checking for case sensitive
					$query = "SELECT username FROM Users WHERE username = '$user_username' AND password = '$user_password'";
					$results = $file_db->query($query);
                    $row = $results->fetch(PDO::FETCH_ASSOC);
					
					if($row != NULL)			
					{        
						// The log-in is OK so set the user ID and username session vars (and cookies), and redirect to the home page
                        $_SESSION['username'] = $row['username'];
                        setcookie('username', $row['username'], time() + (60 * 60 * 24 * 30));  // expires in 30 days
?>
                        <script>
                           window.location.href="index.php";
                        </script>

<?php

					}
					else {
						// The username/password are incorrect so set an error message
						$error_msg = 'Login failed! Please recheck the username and password and try again.';
                    }
				}
				else {
					// The username/password weren't entered so set an error message
					$error_msg = 'Sorry, you must enter the registrar username and password to log in.';
				}
			}
		}
    }
    catch(PDOException $e) {
        // Print PDOException message
        echo $e->getMessage();
    }
    


    // If the session var is empty, show any error message and the log-in form; otherwise confirm the log-in
	if (empty($_SESSION['username'])) 
	{
        // echo '<p class="error">' . $error_msg . '</p>';
?>
    <div class="form-container">
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="mb-3">
                <label class="form-label" for="username">Username:</label>
                <input class="form-control" type="text" name="username" value="<?php if (!empty($user_username)) echo $user_username; ?>" /><br />
            </div>
            <div class="mb-3">
                <label class="form-label" for="password">Password:</label>
                <input class="form-control" type="password" name="password" />
            </div>
            <?php
                if (!empty($error_msg)) {
                    echo 
                    "<div class='alert alert-danger' role='alert'>
                        {$error_msg}
                    </div>";
                }
            ?>
            <input class="btn btn-primary" type="submit" value="Login" name="submit" />
        </form>
    </div>


   
    <!-- <p>Don't have an account? Please <a href="signup.php">signup</a></p> -->

<?php
	}
	else {
       echo "You are already logged in as ".$_SESSION['username'].". Please go to index page.";
	}
?>

<?php
  // Insert the page footer
  require_once('footer.php');
?>