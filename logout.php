<?php

session_start();

if(isset($_SERVER['lastpage'])) {
    $lastpage = $_SESSION['lastpage'];
} else {
    $lastpage = 'index.php';
}

session_destroy();

// Return to last visited page
header("Location: $lastpage");