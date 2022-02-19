<?php

// require 'checkauth.php';
include 'header.php';

$_SESSION['lastpage'] = $_SERVER['REQUEST_URI'];

if(!isset($_GET['id'])) {
    $_SESSION['message'] = "That recipe doesn't exist!";
    header('Location: index.php');
}

// Get recipe info from the database
$sql = "SELECT r.*, u.firstname, u.lastname FROM recipes r LEFT JOIN users u ON r.creatorid = u.id WHERE r.id = ?";
$results = query_db(array($sql, $_GET['id']));
$recipe = $results[0];

// Get notes info from the database
$sql = "SELECT n.*, u.firstname, u.lastname FROM notes n LEFT JOIN users u ON n.userid = u.id WHERE recipeid = ? ORDER BY timecreated ASC";
$notes = query_db(array($sql, $_GET['id']));

// Get the JSON variables into usable associate array format
$ingredients = json_decode($recipe['ingredients'], true);
$ingHeadings = json_decode($recipe['ing_headings'], true);
$process = json_decode($recipe['process'], true);
$procHeadings = json_decode($recipe['process_headings'], true);

// If the JSON variables are null, turn them into empty arrays
if(is_null($ingredients)) { $ingredients = array(); }
if(is_null($ingHeadings)) { $ingHeadings = array(); }
if(is_null($process)) { $process = array(); }
if(is_null($procHeadings)) { $procHeadings = array(); }

echo "<div class='recipe-container'>";
echo "<h1>" . htmlentities($recipe['name']) . "</h1>";
echo "<div class='attribution'>" . htmlentities(addLinks($recipe['attribution'])) . "</div>";
if(isset($recipe['yield']) && $recipe['yield'] != '') {
    echo "<div class='yield'><strong>Yield:</strong> " . htmlentities($recipe['yield']) . "</div>";
}
if(isset($recipe['time']) && $recipe['time'] != '') {
    echo "<div class='time'><strong>Time:</strong> " . htmlentities($recipe['time']) . "</div>";
}
echo "<hr>";
echo "<div class='recipe-heading-container'>";

// Categories
echo "<div class='cats-applied'>";
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    echo "<div id='edit-categories'><i class='fas fa-pen'></i><span>Edit Categories</span></div>";
}
echo "</div>";
echo "<div class='categories-container'>";

// Only load the input and category list if the user is logged in
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    echo "<input type='text' id='cat-input' placeholder = 'Add category'><i class='fas fa-times' id='cat-input-close'></i>";
    echo "<div class='cat-list'>";
    echo "</div>";
}
echo "</div>";

echo "<hr>";
echo "<div class='recipe-subheading-container'>";
if($recipe['picture'] != '' || $recipe['picture'] != NULL) {
    echo "<div class='recipe-heading'>";
    echo "<div class='description'>" . addLinks($recipe['description']) . "</div>";
    echo "</div>";
    echo "<div class='recipe-picture'>";
    echo "<img src='img/" . $recipe['picture'] . "'>";
    echo "</div>";
} else {
    echo "<div class='recipe-heading-full-width'>";
    echo "<div class='description'>" . addLinks($recipe['description']) . "</div>";
    echo "</div>";
}
echo "</div>";
echo "<div class='contributor'>Contributed by " . $recipe['firstname'] . " " . $recipe['lastname'] . "</div>";
echo "</div>";

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
    echo "<span class='name'>" . $ing['unit'] . " " . $ing['name'] . "</span>";
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
    echo "<div class='step'>" . addLinks($step) . "</div><br>";
}
echo "</div>";

// Notes
if(!empty($notes)) {
    echo "<div class='notes-container'>";
    echo "<h2>Notes</h2>";
    //echo var_dump($notes);
    //echo "<ul>";
    foreach($notes as $note) {
        echo "<div class='r-note' data-id='" . $note['id'] . "'>";
        echo "<blockquote class='note-text'>" . addLinks($note['note']) . "<br><br>";
        if(isset($_SESSION['userid']) && $_SESSION['userid'] == $note['userid']) {
            echo "<div class='action-delete result-actions'><i class='fas fa-trash-alt'></i>DELETE</div>";
        }
        echo "<div class='note-attr' data-id='" . $note['userid'] . "'><b>" . $note['firstname'] . " " . $note['lastname'] . "</b></div></blockquote></div>";
    }
    //echo "</ul></div>";
    echo "</div>";
}

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    echo "<div class='add-recipe-note-container'>";
    echo "<div class='add-note recipe-note'><a onclick='addRecipeNote(this)'><i class='fas fa-plus-circle'></i>Add a note</a></div>";
    echo "<div class='add-note-textarea-container'>";
    echo "<div>Enter a new note:</div>";
    echo "<textarea class='note-textarea' id='new-note' rows='1'></textarea>";
    echo "<div class='add-note-button'>Save note</div>";
    echo "<div class='cancel-note-button'>Cancel</div>";
    echo "</div>";
    echo "</div>";
}

echo "</div>";
echo "</div>";

// Replace URLs in a string with actual hyperlinks
function addLinks($s) {
    return preg_replace('/https?:\/\/[\w\-\.!~#?&=+\*\'"(),\/]+/','<a href="$0" target="_blank" class="converted-link">$0</a>',$s);
}