<?php

include 'header.php';

echo "<div class='newuser-container'>";
echo "<h1>Add a new user</h1>";
echo "<form action='process_new_user.php' method='post'>";
echo "<div class='inline'>";
echo "<label for='firstname'>First Name</label>";
echo "<input type='text' id='firstname' name='firstname' required>";
echo "</div>";
echo "<div class='inline'>";
echo "<label for='lastname'>Last Name</label>";
echo "<input type='text' id='lastname' name='lastname' required>";
echo "</div>";
echo "<label for='username'>Username</label>";
echo "<input type='text' id='username' name='username' required>";
echo "<div class='username-match'><i class='fas fa-check-square'></i><i class='fas fa-times-circle'></i><span></span></div><br>";
echo "<div class='inline'>";
echo "<label for='password'>Password</label>";
echo "<input type='password' id='password' name='password' required>";
echo "</div>";
echo "<div class='inline'>";
echo "<label for='password-verify'>Repeat password</label>";
echo "<input type='password' id='password-verify' name='password-verify' required>";
echo "</div>";
echo "<div class='password-match'>Passwords match?<i class='fas fa-check-square'></i><i class='fas fa-times-circle'></i></div><br>";
echo "<input type='submit' class='button' value='Create account'>";
echo "</form>";
echo "<div id='form-error'></div>";
echo "</div>";