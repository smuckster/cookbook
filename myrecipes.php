<?php

include 'header.php';

// Make sure that GET 'id' variable is set
if(!isset($_GET['id'])) {
    $_SESSION['message'] = "That page doesn't exist!";
    header('Location: index.php');
}

// Make sure the user is logged in
if(!isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != true) {
    $_SESSION['message'] = "You must be logged in to view that page.";
    header('Location: index.php');
}

// Make sure user can only access their own 'My Categories' page
if($_GET['id'] != $_SESSION['userid']) {
    $_SESSION['message'] = "You do not have permission to view that page.";
    header('Location: index.php');
}

// Get a list of the user's recipes from the database
$sql = "SELECT * FROM recipes WHERE creatorid = ?";
$results = query_db(array($sql, $_GET['id']));
if(!is_array($results)) {
    $results = array();
}

echo "<div class='myrecipes-container'>";
echo "<h1>My Recipes</h1>";

foreach($results as $result) {
    echo "<a class='result-container' href='recipe.php?id=" . $result['id'] . "' data-id='" . $result['id'] . "'>";
    if($result['picture'] != '' || $result['picture'] != NULL) {
        echo "<div class='result-image-container' style='background-image:url(\"img/" . $result['picture'] . "\")'></div>";
    } else {
        echo "<div class='result-image-container' style='background-image:url(\"img/eggplant.jpeg\")'></div>";
    }
    if(strlen($result['name']) > 55) {
        $name = substr($result['name'], 0, 55);
        $name .= '...';
    } else {
        $name = $result['name'];
    }

    echo "<div class='result-name'>" . $name . "</div>";

    if(strlen($result['attribution']) > 23) {
        $attr = substr($result['attribution'], 0, 23);
        $attr .= '...';
    } else {
        $attr = $result['attribution'];
    }

    echo "<div class='result-attr'>" . $attr . "</div><br>";

    if(strlen($result['description']) > 80) {
        $desc = substr($result['description'], 0, 80);
        $desc .= '...';
    } else {
        $desc = $result['description'];
    }
    echo "<div class='result-desc'>" . $desc . "</div>";

    echo "<div class='result-actions'>";
    echo "<div class='action-edit'><i class='fas fa-pen'></i>EDIT</div>";
    echo "<div class='action-delete'><i class='fas fa-trash-alt'></i>DELETE</div>";
    echo "</div>";
    echo "</a>";
    echo "<div class='delete-recipe'>Are you sure you want to delete this recipe? This action cannot be undone!<br><br><div class='confirm-delete-button' data-id='" . $result['id'] . "'>Yes, delete</div><div class='cancel-delete-button'>No, cancel</div></div>";
}

echo "</div>";