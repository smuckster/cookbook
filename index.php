<?php
include 'header.php';

$_SESSION['lastpage'] = $_SERVER['REQUEST_URI'];

if(isset($_POST['message'])) {
    echo "<div class='message'>" . $_POST['message'] . "</div>";
}

echo "<div class='frontpage-container'>";

if(isset($_SESSION['message'])) {
    //echo "<p>" . $_SESSION['message'] . "</p>";
}

// Search bar
echo "<div class='welcome'>What do you want to cook?</div>";
echo "<form id='search-recipes' action='search.php' method='get'>";
echo "<input type='text' name='q' id='search' placeholder='Search' required>";
echo "<div class='submit-search'>";
echo "<i class='fas fa-search'></i>";
echo "</div>";
echo "<input type='submit'>";
echo "</form>";

// Browse recipes button
echo "<div class='browse-container'>";
echo "<a href='browse.php'>";
echo "<div class='button browse'>Browse recipes</div>";
echo "</a>";
echo "</div>";

// Recently added recipes
$sql = "SELECT r.*, u.firstname, u.lastname 
        FROM recipes r
        LEFT JOIN users u ON r.creatorid = u.id
        ORDER BY timecreated DESC, timechanged DESC LIMIT 12";
$results = query_db(array($sql));
if(!is_array($results) || sizeof($results) < 1) {
    $results = array();
}

echo "<div class='recent-recipes-heading'>Recently Added Recipes</div><hr style='margin-top:0'>";
echo "<div class='recent-recipes-container'>";
foreach($results as $result) {
    if($result['picture'] != '' || $result['picture'] != NULL) {
        echo "<a class='recent-recipe' href='recipe.php?id=" . $result['id'] . "'><div class='recent-img-container'><div class='recent-img' style='background:url(\"img/" . $result['picture'] . "\")'>";
    } else {
        echo "<a class='recent-recipe' href='recipe.php?id=" . $result['id'] . "'><div class='recent-img-container'><div class='recent-img' style='background:url(\"img/eggplant.jpeg\")'>";
    }
    echo "</div></div><div class='recent-recipe-title'>" . $result['name'] . "</div><div class='recent-recipe-contributor'>Contributed by " . $result['firstname'] . " " . $result['lastname'] . "</div></a>";
}
echo "</div>";

echo "</div>";