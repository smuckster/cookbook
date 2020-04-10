<?php
include 'header.php';

if(isset($_POST['message'])) {
    echo "<div class='message'>" . $_POST['message'] . "</div>";
}

echo "<div class='frontpage-container'>";

if(isset($_SESSION['message'])) {
    //echo "<p>" . $_SESSION['message'] . "</p>";
}

echo "<div class='welcome'>What do you want to cook?</div>";
echo "<form id='search-recipes' action='search.php' method='get'>";
echo "<input type='text' name='q' id='search' placeholder='Search' required>";
echo "<div class='submit-search'>";
echo "<i class='fas fa-search'></i>";
echo "</div>";
echo "<input type='submit'>";
echo "</form>";

//echo "<a href='editrecipe.php' class='new-recipe button'>New Recipe</a>";

echo "</div>";