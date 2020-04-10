<?php

// require 'checkauth.php';
include 'header.php';

$_SESSION['lastpage'] = $_SERVER['REQUEST_URI'];

if(!isset($_GET['id'])) {
    $_SESSION['message'] = "That recipe doesn't exist!";
    header('Location: index.php');
}

//echo var_dump($_SERVER);

// Get recipe info from the database
$sql = "SELECT r.*, u.firstname, u.lastname FROM recipes r LEFT JOIN users u ON r.creatorid = u.id WHERE r.id = ?";
$results = query_db(array($sql, $_GET['id']));
$recipe = $results[0];

// Get the JSON variables into usable associate array format
$ingredients = json_decode($recipe['ingredients'], true);
$ingHeadings = json_decode($recipe['ing_headings'], true);
$process = json_decode($recipe['process'], true);
$procHeadings = json_decode($recipe['process_headings'], true);
$notes = json_decode($recipe['notes'], true);

// If the JSON variables are null, turn them into empty arrays
if(is_null($ingredients)) { $ingredients = array(); }
if(is_null($ingHeadings)) { $ingHeadings = array(); }
if(is_null($process)) { $process = array(); }
if(is_null($procHeadings)) { $procHeadings = array(); }
if(is_null($notes)) { $notes = array(); }

echo "<div class='recipe-container'>";
echo "<h1>" . $recipe['name'] . "</h1>";
echo "<div class='attribution'>" . $recipe['attribution'] . "</div>";
echo "<div class='recipe-heading-container'>";
echo "<div class='recipe-heading'>";
echo "<div class='description'>" . $recipe['description'] . "</div>";
include 'categories.php';
echo "<div class='contributor'>Contributed by " . $recipe['firstname'] . " " . $recipe['lastname'] . "</div>";
echo "</div>";
echo "<div class='recipe-picture'>";
if($recipe['picture'] != '' || $recipe['picture'] != NULL) {
    echo "<img src='img/" . $recipe['picture'] . "'>";
} else {

}
echo "</div></div>";

// Container to hold the ingredients list and steps
echo "<div class='recipe-content-container'>";

// Ingredients list
echo "<div class='ingredients-container'>";
echo "<h2>Ingredients</h2>";
$ing_num = 0;
foreach($ingredients as $ing) {
    // Add a heading if there is one
    if(array_key_exists($ing_num, $ingHeadings)) {
        echo "<div class='ingredient-heading'>" . $ingHeadings[$ing_num] . "</div>";
    }
    $ing_num++;
    echo "<div class='ingredient-row'>";
    echo "<span class='quantity'>" . $ing['quantity'] . "</span>";
    if($ing['unit'] != '') {
        echo "<span class='unit'>" . $ing['unit'] . "</span>";
    }
    echo "<span class='name'>" . $ing['name'] . "</span>";
    echo "</div>";
}
echo "</div>";

// Steps
echo "<div class='steps-container'>";
echo "<h2>Steps</h2>";
$step_num = 0;
foreach($process as $step) {
    // Add a heading if there is one
    if(array_key_exists($step_num, $procHeadings)) {
        echo "<div class='step-heading'>" . $procHeadings[$step_num] . "</div>";
    }
    $step_num++;
    echo "<div class='step-num'>$step_num.</div>";
    echo "<div class='step'>$step</div><br>";
}
echo "</div>";

// Notes
if(!empty($notes)) {
    echo "<div class='notes-container'>";
    echo "<h2>Notes</h2>";
    echo "<ul>";
    foreach($notes as $note) {
        echo "<li class='note'>$note</li>";
    }
    echo "</ul></div>";
}

echo "</div>";
echo "</div>";