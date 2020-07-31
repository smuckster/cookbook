<?php

require 'lib.php';

// Delete the recipe from the recipes table
$sql = "DELETE FROM recipes WHERE id = ?";
$results = query_db(array($sql, $_POST['recipeid']));
if(is_array($results) && sizeof($results) == 0) {
    // Delete entries from recipe_categories table
    $sql = "DELETE FROM recipe_categories WHERE recipeid = ?";
    $results = query_db(array($sql, $_POST['recipeid']));
    if(is_array($results) && sizeof($results) == 0) {
        echo "success";
    } else {
        echo "failure";
    }
} else {
    echo "failure";
}