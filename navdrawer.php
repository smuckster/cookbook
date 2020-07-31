<?php

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    echo "<div class='navdrawer'>";
    echo "<a class='nav-top-link' href='user.php?id=" . $_SESSION['userid'] . "&section=categories'><i class='fas fa-book-open'></i>My Categories</a>";
    echo "<div class='mycats-container'>";
    $sql = "SELECT c.* FROM categories c WHERE creatorid = ?";
    $results = query_db(array($sql, $_SESSION['userid']));
    foreach($results as $result) {
        echo "<a class='nav-sub-link' href='search.php?category=" . $result['id'] . "'>" . $result['name'] . "</a>";
    }
    echo "</div>";

    echo "</div>";
}