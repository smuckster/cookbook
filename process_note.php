<?php

require 'lib.php';

session_start();

if(isset($_POST['action']) && $_POST['action'] == 'add') {
    /* Get a list of comments that already exist in the database for this recipe
    $sql = "SELECT id FROM notes WHERE recipeid = ?";
    $results = query_db(array($sql, $_POST['recipeid']));*/

    $sql = "INSERT INTO notes (userid, recipeid, note) VALUES (?, ?, ?)";
    $results = query_db(array($sql, $_SESSION['userid'], $_POST['recipeid'], $_POST['note']));
    if($results == null) {
        echo "<div class='r-note'><blockquote class='note-text'>" . $_POST['note'] . "<br><br><div class='note-attr'><b>" . $_SESSION['firstname'] . " " . $_SESSION['lastname'] . "</b></div></blockquote></div>";
    } else {
        var_dump($results);
    }
}

if(isset($_POST['action']) && $_POST['action'] == 'delete') {
    $sql = "DELETE FROM notes WHERE id = ?";
    $results = query_db(array($sql, $_POST['noteid']));

    if($results == null) {
        echo "success";
    }
}