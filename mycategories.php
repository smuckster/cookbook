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

// Get list of user's categories from the database
$sql = "SELECT * FROM categories WHERE creatorid = ?";
$results = query_db(array($sql, $_GET['id']));
if(!is_array($results)) {
    $results = array();
}

echo "<div class='mycategories-container'>";
echo "<h1>My Categories</h1>";
echo "<p>These are categories that you have created. You can edit their names by clicking the pencil icons, and you can change each category's privacy by flipping the switch. Public categories can be seen and used by other users on the site; private categories can only be seen and used by you.</p>";
echo "<div class='mycategories-edit button2'><i class='fas fa-pen'></i>Edit Categories</div>";

foreach($results as $cat) {
    echo "<div class='mycat-container' data-id='" . $cat['id'] . "' style='background-color:" . $cat['color'] . "'>";
    echo "<a class='mycat-name' href='search.php?category=" . $cat['id'] . "'>" . $cat['name'] . "</a>";
    echo "<input class='mycat-name-edit' type='text' placeholder='Category name'>";
    echo "<div class='mycat-edit'><i class='fas fa-pen'></i></div>";
    echo "<div class='mycat-settings'><span class='public' style='color:";
    if($cat['private'] == 0) {
        echo "#000";
    } else {
        echo "#999";
    }
    echo "'>PUBLIC</span>";
    echo "<div class='toggle-switch-container' style='background-color:";
    if($cat['private'] == 0) {
        echo "green";
    } else {
        echo "#bfbfbf";
    }
    echo "'><div class='toggle-switch ";
    if($cat['private'] == 0) {
        echo "on";
    } else {
        echo "off";
    }
    echo "'></div></div><span class='private' style='color:";
    if($cat['private'] == 0) {
        echo "#999";
    } else {
        echo "#000";
    }
    echo "'>PRIVATE</span><br>";
    echo "<div class='mycat-delete'>";
    echo "<div class='action-delete'><i class='fas fa-trash-alt'></i>DELETE</div>";
    echo "</div>";
    echo "</div>";
    echo "<div class='delete-category'>Are you sure you want to delete this category? This action cannot be undone!<br><br><div class='confirm-delete-button' data-id='" . $cat['id'] . "'>Yes, delete</div><div class='cancel-delete-button'>No, cancel</div></div>"; 
    echo "</div>";
}