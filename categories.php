<?php

echo "<div class='cats-applied'>";
echo "</div>";
echo "<div class='categories-container'>";

// Only load the input and category list if the user is logged in
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    echo "<input type='text' id='cat-input' placeholder = 'Add category'><i class='fas fa-times' id='cat-input-close'></i>";
    echo "<div class='cat-list'>";
    echo "</div>";
}
echo "</div>";