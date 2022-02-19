<?php
// Require user to be logged in on every page.
// If user is not logged in, redirect them to the login page
// and save this page's address so they can be returned here
// after logging in.

// session_start();

if(!isset($_SESSION['loggedin'])) {
  ob_start();
  $_SESSION['lastpage'] = $_SERVER['PHP_SELF'];
  header('Location: login.php');
  ob_end_flush();
  die();
}
?>