<?php
  // Generate the navigation menu
  echo '<hr />';

  // generate nav menu based on whether it is professor, student or registrar


  if (isset($_SESSION['username'])) {
    echo '<a href="index.php">Home</a> &nbsp';
    //echo '<a href="">View Profile</a> &nbsp';
    //echo '<a href="edit_profile.php">Edit Profile</a> &nbsp';

    if (isset($_SESSION['role'])) {
      $role = $_SESSION['role'];
      if ($role == 'student') {
        echo '<a href="student_courses.php">Student Courses</a> &nbsp';
      }
      else if ($role == 'professor') {
        echo '<a href="professor_courses.php">Professor Courses</a> &nbsp';
      }
    }

    echo '<a href="logout.php">LogOut (' . $_SESSION['username'] . ')</a>';
  }
  else {
    echo '<a href="login.php">Login</a> &nbsp';
    echo '<a href="signup.php">Sign Up</a>';
  }
  echo '<hr />';
?>
