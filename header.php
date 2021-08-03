<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php
      echo '<title>Course Registration - ' . $page_title . '</title>';
    ?>

  
    <!-- <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"/> -->
    <link rel="stylesheet" href="library/bootstrap/css/bootstrap.min.css" />
    <script src="library/bootstrap/js/bootstrap.bundle.min.js" defer></script>

    <link rel="stylesheet" href="css/style.css" />

    <script src="js/student_view.js"></script>
  </head>
  <body>
    <header>
      <?php
        
        echo '<h1 class="header-title">Course Registration - ' . $page_title . '</h1>';
      
        if (!empty($_SESSION['username'])) {
          // Show the navigation menu
          require_once('nav_menu.php');
        }
      ?>
      
    </header>
    <main>
    <div class="container">
    
    
