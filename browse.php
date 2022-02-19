<?php

include 'header.php';

// Retrieve all recipes
$sql = "SELECT r.*, u.firstname, u.lastname 
        FROM recipes r
        LEFT JOIN users u ON r.creatorid = u.id
        ORDER BY r.name ASC";
$recipes = query_db(array($sql));

// Retrieve all categories
$sql = "SELECT * FROM categories";

echo "<div class='browsepage-container'>";
echo "<h1>Browse</h1>";

foreach($recipes as $recipe) {
    if($recipe['picture'] != '' || $recipe['picture'] != NULL) {
        echo "<a class='recipe-tile' href='recipe.php?id=" . $recipe['id'] . "'><div class='recent-img-container'><div class='recent-img' style='background:url(\"img/" . $recipe['picture'] . "\")'>";
    } else {
        echo "<a class='recipe-tile' href='recipe.php?id=" . $recipe['id'] . "'><div class='recent-img-container'><div class='recent-img' style='background:url(\"img/eggplant.jpeg\")'>";
    }
    echo "</div></div><div class='recent-recipe-title'>" . $recipe['name'] . "</div><div class='recent-recipe-contributor'>Contributed by " . $recipe['firstname'] . " " . $recipe['lastname'] . "</div></a>";
}

echo "</div>";