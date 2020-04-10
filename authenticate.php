<?php

session_start();

require 'lib.php';

if(!isset($_POST['username']) && $_POST['password']) {
    die("Please fill out both the username and password field!");
}

// Save last visited page so it can be used to redirect user
if(isset($_SESSION['lastpage'])) {
    $lastpage = $_SESSION['lastpage'];
} else {
    $lastpage = 'index.php';
}

$_SESSION['message'] = "Lastpage variable is: " . $lastpage;

// Save the submitted username as a session variable
$_SESSION['username'] = htmlspecialchars($_POST['username']);

// Get user info from database
$sql = "SELECT * FROM users WHERE username = ?";
$results = query_db(array($sql, $_POST['username']));
$user = $results[0];

// If the user exists in the database, log them in
if(sizeof($results) > 0) {
    $user = $results[0];
    if(password_verify($_POST['password'], $user['password'])) {
        // User has successfully logged in.
        // Create session data for user.
        session_regenerate_id();
        $_SESSION['loggedin'] = TRUE;
        $_SESSION['loginerror'] = FALSE;
        $_SESSION['firstname'] = $user['firstname'];
        $_SESSION['lastname'] = $user['lastname'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['userid'] = $user['id'];
        $_SESSION['message'] = "Logged in successfully!";
    } else {
        $_SESSION['loginerror'] = TRUE;
        $_SESSION['message'] = "Could not verify password";
    }
} else {
    $_SESSION['loginerror'] = TRUE;
    $_SESSION['message'] = "User does not exist in the database";
}

// Redirect user to the last page they were on or the index page
header("Location: $lastpage");
exit();