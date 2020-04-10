<?php

session_start();

require 'lib.php';

?>

<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>Cookbook</title>

  <link rel="stylesheet" href="styles.css">

  <!-- Lora font (serif) -->
  <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

  <!-- Slabo font (serif) -->
  <link href="https://fonts.googleapis.com/css2?family=Slabo+27px&display=swap" rel="stylesheet">

  <!-- Source Sans Pro font (sans) -->
  <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;700&display=swap" rel="stylesheet">

  <!-- Crimson Text font (serif) -->
  <link href="https://fonts.googleapis.com/css2?family=Crimson+Text:wght@400;700&display=swap" rel="stylesheet">

</head>

<body>
  <!-- jQuery -->
  <script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>

  <!-- Auto-grow plugin for textareas -->
  <script src="auto-resize-textarea/dist/jquery-auto-resize.js"></script>

  <!-- FontAwesome -->
  <script src="https://kit.fontawesome.com/7cb9082f71.js" crossorigin="anonymous"></script>

  <!-- Project-specific scripts -->
  <script src="scripts.js"></script>

  <!-- Header navigation bar -->
  <div class="header-bar">
    <div class="header-contents">
      <div class="site-name"><a href="index.php"><img class="logo" src="logo.png"></a></div>
      <?php 
        // If user is logged in
        if(isset($_SESSION['username'])) { 
          echo "<div class='user-nav-container'>";
          echo "<div class='user-nav-button'>";
          echo $_SESSION['username']; 
          echo "<i class='fas fa-chevron-right'></i>";
          echo "</div>";

          // Render user menu
          echo "<div class='user-menu'>";
          echo "<a href='logout.php'>Logout</a>";
          echo "</div>";
          echo "</div>";

          // New recipe button, if not already on the new recipe page
          if($_SERVER['PHP_SELF'] != '/editrecipe.php') {
            echo "<a href='editrecipe.php' id='new-recipe-button'>New Recipe</a>";
          }

          // Edit recipe button (if applicable)
          if($_SERVER['PHP_SELF'] == '/recipe.php') {
            $sql = "SELECT * FROM recipes WHERE creatorid = ? AND id = ?";
            $results = query_db(array($sql, $_SESSION['userid'], $_GET['id']));
            if(is_array($results) && sizeof($results) == 1) {
              echo "<a href='editrecipe.php?id=" . $_GET['id'] . "' id='edit-recipe-button'>Edit Recipe</a>";
            }
          }
        } else {
          echo "<div class='user-nav-container'>";
          echo "<a class='user-profile login-button' href='login.php'>Login</a>";
          echo "</div>";
        }
      ?>
    </div>
  </div>
