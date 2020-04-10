<?php

require 'lib.php';

$results = query_db(array("SELECT * FROM users WHERE username = ?", $_POST['username']));
if(sizeof($results) > 0) {
    echo "Taken";
} else {
    echo "Free";
}