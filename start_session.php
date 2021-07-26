<?php
  session_start();

  // If the session vars aren't set, try to set them with a cookie
  if (!isset($_SESSION['username'])) {
    if (isset($_COOKIE['username']) && isset($_COOKIE['role'])) {
      $_SESSION['username'] = $_COOKIE['username'];
      $_SESSION['role'] = $_COOKIE['role'];
    }
  }
?>
