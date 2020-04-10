<?php

include 'header.php';

echo "<div class='login-background'>";
echo "<div class='login'>";
echo "<h1>Cookbook</h1>";
echo "<form action='authenticate.php' method='post'>";
echo "<label for='username'><i class='fas fa-user'></i></label>";
echo "<input type='text' name='username' placeholder='Username' id='username' value='";
if(isset($_SESSION['username'])) {
    echo $_SESSION['username'];
}
echo "' required>";
echo "<label for='password'><i class='fas fa-lock'></i></label>";
echo "<input type='password' name='password' placeholder='Password' required>";

if(isset($_SESSION['loginerror']) && $_SESSION['loginerror'] == TRUE) {
    echo "<p class='login-error'>Your username and/or password was incorrect.</p>";
}

echo "<input type='submit' value='Login'>";
echo "</form>";

echo "<a class='button' href='newuser.php'>Create new account</a>";

echo "</div>";
echo "</div>";