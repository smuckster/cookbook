<?php

require 'lib.php';

session_start();

// Make sure username is unique
$sql = "SELECT COUNT(*) AS count FROM users WHERE username = ?";
$results = query_db(array($sql, $_POST['username']));
if($results[0]['count'] > 0) {
    echo "Sorry, the username you entered is already taken! Please enter a unique username.";
} 

// Make sure passwords match
if($_POST['password'] != $_POST['password-verify']) {
    echo "The passwords you entered don't match. Please try entering your passwords again.";
}

// Add new user to database
$sql = "INSERT INTO users (username, password, firstname, lastname) VALUES (?, ?, ?, ?)";
$results = query_db(array($sql, $_POST['username'], password_hash($_POST['password'], PASSWORD_DEFAULT), $_POST['firstname'], $_POST['lastname']));

if(is_array($results) && empty($results)) {
    // Get new user's id number
    $sql = "SELECT id FROM users WHERE username = ?";
    $results = query_db(array($sql, $_POST['username']));
    if(array_key_exists('id', $results[0]) && $results[0]['id'] != '') {
        // Log user in under their new account
        session_regenerate_id();
        $_SESSION['loggedin'] = TRUE;
        $_SESSION['loginerror'] = FALSE;
        $_SESSION['firstname'] = $_POST['firstname'];
        $_SESSION['lastname'] = $_POST['lastname'];
        $_SESSION['username'] = $_POST['username'];
        $_SESSION['userid'] = $results[0]['id'];
    } else {
        $_SESSION['loginerror'] = TRUE;
    }
}

echo "Success";