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

  <meta name="viewport" content= "width=device-width, initial-scale=1.0"> 

  <!-- Lora font (serif) -->
  <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

  <!-- Slabo font (serif) -->
  <link href="https://fonts.googleapis.com/css2?family=Slabo+27px&display=swap" rel="stylesheet">

  <!-- Source Sans Pro font (sans) -->
  <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;700&display=swap" rel="stylesheet">

  <!-- Crimson Text font (serif) -->
  <link href="https://fonts.googleapis.com/css2?family=Crimson+Text:wght@400;700&display=swap" rel="stylesheet">

  <!-- Slick JS Carousel CSS -->
  <link rel="stylesheet" type="text/css" href="slick/slick.css">
  <link rel="stylesheet" type="text/css" href="slick/slick-theme.css">

</head>

<!-- If user is logged in, attach a special class to the body -->
<?php
  if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    echo "<body class='authenticated";
  } else {
    echo "<body class='unauthenticated";
  }
  if($_SERVER['PHP_SELF'] == '/index.php') {
    echo " index";
  }
  echo "'>";
?>
  <!-- jQuery -->
  <script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>

  <!-- Auto-grow plugin for textareas -->
  <script src="auto-resize-textarea/dist/jquery-auto-resize.js"></script>

  <!-- Slick JS Carousel plugin -->
  <script type="text/javascript" src="slick/slick.js"></script>

  <!-- FontAwesome -->
  <script src="https://kit.fontawesome.com/7cb9082f71.js" crossorigin="anonymous"></script>

  <!-- Project-specific scripts -->
  <script src="scripts.js"></script>

  <!-- Header navigation bar -->
  <div class="header-bar">
    <div class="header-contents">
      <div class="site-name"><a href="index.php"><img class="logo" src="eggplantlogo.png"></a></div>

      <!-- Search bar -->
      <?php
        // If we're on the correct page, show the search bar
        if($_SERVER['PHP_SELF'] != '/index.php' && ($_SERVER['PHP_SELF'] != '/search.php' || isset($_GET['category']))) {
          echo "<div class='nav-search-container'>";
          echo "<form id='nav-search-recipes' action='search.php' method='get'>";
          echo "<input type='text' name='q' id='search' placeholder='Search for recipes' required>";
          echo "<div class='submit-search'>";
          echo "<i class='fas fa-search'></i>";
          echo "</div>";
          echo "<input type='submit'>";
          echo "</form>";
          echo "</div>";
        }
      ?>
      <div class='user-header-buttons'>
      <?php 
        // If user is logged in
        if(isset($_SESSION['username'])) { 
          echo "<div class='user-nav-container'>";
          echo "<div class='user-nav-button'>";
          echo strtoupper(substr($_SESSION['firstname'], 0, 1)) . strtoupper(substr($_SESSION['lastname'], 0, 1)); 
          echo "</div>";
          echo "<i class='fas fa-chevron-right'></i>";

          // Render user menu
          echo "<div class='user-menu'>";
          echo "<a href='mycategories.php?id=" . $_SESSION['userid'] . "'><i class='fas fa-shapes'></i>MY CATEGORIES</a>";
          echo "<a href='myrecipes.php?id=" . $_SESSION['userid'] . "'><i class='fas fa-book-open'></i>MY RECIPES</a>";
          echo "<a href='logout.php'><i class='fas fa-sign-out-alt'></i>LOGOUT</a>";
          echo "</div>";
          echo "</div>";

          // New recipe button, if not already on the new recipe page
          if($_SERVER['PHP_SELF'] != '/editrecipe.php') {
            echo "<a href='editrecipe.php' class='header-button' id='new-recipe-button'><i class='fas fa-plus'></i>NEW RECIPE</a>";
          }

          // Edit recipe button (if applicable)
          if($_SERVER['PHP_SELF'] == '/recipe.php') {
            $sql = "SELECT * FROM recipes WHERE creatorid = ? AND id = ?";
            $results = query_db(array($sql, $_SESSION['userid'], $_GET['id']));
            if(is_array($results) && sizeof($results) == 1) {
              echo "<a href='editrecipe.php?id=" . $_GET['id'] . "' class='header-button' id='edit-recipe-button'><i class='fas fa-pen'></i>EDIT RECIPE</a>";
            }
          }
        } else {
          echo "<div class='user-nav-container'>";
          echo "<a class='user-profile login-button' href='login.php'>Login</a>";
          echo "</div>";
        }
      ?>
      </div>
      <div class='hamb-menu'><i class='fas fa-bars'></i></div>
      <?php
        echo "<div class='hamb-menu-dropdown'>";
        if(isset($_SESSION['username'])) {
          // Search button
          echo "<a href='search.php'><i class='fas fa-search'></i>Search</a>";

          // New recipe button, if not already on the new recipe page
          if($_SERVER['PHP_SELF'] != '/editrecipe.php') {
            echo "<a href='editrecipe.php'><i class='fas fa-plus-circle'></i>New Recipe</a>";
          }

          // Edit recipe button (if applicable)
          if($_SERVER['PHP_SELF'] == '/recipe.php') {
            $sql = "SELECT * FROM recipes WHERE creatorid = ? AND id = ?";
            $results = query_db(array($sql, $_SESSION['userid'], $_GET['id']));
            if(is_array($results) && sizeof($results) == 1) {
              echo "<a href='editrecipe.php?id=" . $_GET['id'] . "'><i class='fas fa-pen'></i>Edit Recipe</a>";
            }
          }
          echo "<a href='logout.php'><i class='fas fa-sign-out-alt'></i>Logout</a>";
        } else {
          echo "<a href='login.php'><i class='fas fa-sign-in-alt'></i>Login</a>";
        }
        echo "</div>";
      ?>
    </div>
  </div>

  <?php
  //include 'navdrawer.php';
  ?>