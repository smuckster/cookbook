<?php

require 'lib.php';

session_start();

// List of colors to use for category tags
$catcolors = array('#00aa55', '#aa00aa', '#5d995d', '#5a781d', // Greens
                   '#009fd4', '#00a4a6', '#5c97bf', '#3477db', // Blues
                   '#b381b3', '#bf6ee0', '#9370db', '#d25299', // Purples
                   '#808080', '#708090', '#4d6066', '#050709', // Darks
                   '#aa8f00', '#b8860b', '#5a440d', '#483c0c', // Yellows
                   '#d47500', '#ff4500', '#d46a43', '#e65722', // Oranges
                   '#ff0000', '#d24d57', '#f64747', '#b11030', // Reds
);

if(isset($_POST['action']) && $_POST['action'] == 'allcategories') {
    // Get a list of all categories the user can view that aren't already associated
    // with this recipe
    $readonly = 0;
    if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == TRUE) {
        $sql = "SELECT * FROM categories WHERE creatorid = ? OR (creatorid = ? AND private = 0)";
        //$sql = "SELECT DISTINCT c.* FROM categories c LEFT JOIN recipe_categories rc ON c.id = rc.categoryid LEFT JOIN recipes r ON rc.recipeid = r.id WHERE rc.recipeid != ? AND (creatorid = ? OR private = 0)";
        $allcats = query_db(array($sql, $_SESSION['userid'], $_SESSION['userid']));
        $readonly = 1;
    } else {
        $sql = "SELECT * FROM categories WHERE private = 0";
        //$sql = "SELECT DISTINCT c.* FROM categories c LEFT JOIN recipe_categories rc ON c.id = rc.categoryid LEFT JOIN recipes r ON rc.recipeid = r.id WHERE rc.recipeid != ? AND  private = 0";
        $allcats = query_db(array($sql));
    }

    // Add in the color picker
    echo "<div class='cat-color-picker'>";
    echo "<div>Pick a color</div>";
    foreach($catcolors as $color) {
        echo "<div class='color-container'><i class='fas fa-circle' style='color:" . $color . "' onclick='new_tag(this)'></i></div>";
    }
    echo "</div>";

    foreach($allcats as $cat) {
        echo "<div class='cat-list-item' data-id='" . $cat['id'] . "' onclick='add_tag(this)'><i class='fas fa-circle' style='color:" . $cat['color'] . "'></i>" . $cat['name'] . "</div>";
    }
}

if(isset($_POST['action']) && $_POST['action'] == 'recipecategories') {
    // Get a list of this recipe's associated categories
    $sql = "SELECT DISTINCT c.* FROM categories c LEFT JOIN recipe_categories rc ON c.id = rc.categoryid LEFT JOIN recipes r ON rc.recipeid = r.id WHERE rc.recipeid = ? AND c.private != 1";
    $recipecats = query_db(array($sql, $_POST['recipeid']));
    if(is_array($recipecats)) {
        if(sizeof($recipecats) == 0) {
            $recipecats = array();
        }
    } else {
        $recipecats = array();
    }

    foreach($recipecats as $cat) {
        echo "<div class='cat-tag' style='background-color:" . $cat['color'] . "' data-id='" . $cat['id'] . "'>" . $cat['name'];
        if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
            if(isset($_SESSION['userid']) && $cat['creatorid'] == $_SESSION['userid']) {
                echo "<i class='fas fa-times' onclick='delete_tag(this)'></i>";
            }
        }
        echo "</div>";
    }
}

if(isset($_POST['action']) && $_POST['action'] == 'new') {
    // Check if provided category already exists
    $sql = "SELECT * FROM categories WHERE name = ?";
    $results = query_db(array($sql, $_POST['categoryname']));
    if(sizeof($results) == 0) {
        $sql = "INSERT INTO categories (name, color, creatorid, private) VALUES (?, ?, ?, ?)";
        echo var_dump($_POST);
        $results = query_db(array($sql, $_POST['categoryname'], $_POST['color'], $_SESSION['userid'], 0));

        // Get the newly created category's id
        $sql = "SELECT id FROM categories WHERE name = ?";
        $results = query_db(array($sql, $_POST['categoryname']));
        $categoryid = $results[0]['id'];

        // Check if an association already exists for this combination
        // of recipeid and categoryid
        $sql = "SELECT * FROM recipe_categories WHERE recipeid = ? AND categoryid = ?";
        $results = query_db(array($sql, $_POST['recipeid'], $categoryid));
        if(sizeof($results) == 0) {
            // Associate the recipe with the category
            $sql = "INSERT INTO recipe_categories (recipeid, categoryid) VALUES (?, ?)";
            $results = query_db(array($sql, $_POST['recipeid'], $categoryid));
        }
    }
}

if(isset($_POST['action']) && $_POST['action'] == 'add') {
    // Check if an association already exists for this combination
    // of recipeid and categoryid
    $sql = "SELECT * FROM recipe_categories WHERE recipeid = ? AND categoryid = ?";
    $results = query_db(array($sql, $_POST['recipeid'], $_POST['categoryid']));
    //echo var_dump($results);
    if(sizeof($results) == 0) {
        // Associate the recipe with the category
        $sql = "INSERT INTO recipe_categories (recipeid, categoryid) VALUES (?, ?)";
        $results = query_db(array($sql, $_POST['recipeid'], $_POST['categoryid']));
    }
}

if(isset($_POST['action']) && $_POST['action'] == 'delete') {
    // Remove the association between this recipeid and categoryid
    $sql = "DELETE FROM recipe_categories WHERE recipeid = ? AND categoryid = ?";
    $results = query_db(array($sql, $_POST['recipeid'], $_POST['categoryid']));
}