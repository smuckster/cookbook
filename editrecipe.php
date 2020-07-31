<?php

include 'header.php';

// Verify that user is logged in before allowing them access
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    // Determine whether this page is being used to create a new recipe
    // or edit an existing recipe and set variables accordingly
    if(isset($_GET['id']) && $_GET['id'] != '') {
        $sql = "SELECT * FROM recipes WHERE id = ?";
        $results = query_db(array($sql, $_GET['id']));
        $recipe = $results[0];

        // Make sure user is the owner of the recipe they're trying to edit
        if($_SESSION['userid'] != $recipe['creatorid']) {
            $_SESSION['message'] = "You don't have permission to edit this recipe.";
            header('Location: index.php');
            die();
        }
    } else {
        $recipe = array();
    }
} else {
    $_SESSION['message'] = "You need to be logged in to add or edit a recipe!";
    header('Location: index.php');
    die();
}

echo "<div class='new-recipe-container'>";
if(!empty($recipe)) {
    echo "<h1>Editing " . $recipe['name'] . "</h1>";
} else {
    echo "<h1>Add a new recipe</h1>";
}

// Begin the form
echo "<form id='new-recipe-form' method='post' action='' enctype='multipart/form-data'>";

// Name
echo "<label for='name'>Name</label>";
echo "<input type='text' id='name' name='name' placeholder='Recipe name' value='";
if(array_key_exists('name', $recipe)) { echo $recipe['name']; }
echo "' required>";

// Attribution
echo "<label for='attribution'>Attribution</label>";
echo "<input type='text' id='attribution' name='attribution' placeholder='Fuschia Dunlop: Land of Plenty cookbook' value='";
if(array_key_exists('attribution', $recipe)) { echo $recipe['attribution']; }
echo "'>";

// Description
echo "<label for='description'>Description</label>";
echo "<textarea id='description' name='description' placeholder='Describe your recipe' rows='1'>";
if(array_key_exists('description', $recipe)) { echo $recipe['description']; }
echo "</textarea>";

// Yield
echo "<label for='yield'>Yield</label>";
echo "<input type='text' id='yield' name='yield' placeholder='6 servings' value='";
if(array_key_exists('yield', $recipe)) { echo $recipe['yield']; }
echo "'>";

// Time
echo "<label for='time'>Time</label>";
echo "<input type='text' id='time' name='time' placeholder='45 minutes' value='";
if(array_key_exists('time', $recipe)) { echo $recipe['time']; }
echo "'>";

// Ingredients
echo "<div class='ingredients-section'>";
echo "<label for='ingredients'>Ingredients</label>";

if(!empty($recipe)) {
    // Get variables in usable format
    $ingredients = json_decode($recipe['ingredients'], true);
    $ingHeadings = json_decode($recipe['ing_headings'], true);
    if(is_null($ingredients)) { $ingredients = array(); }
    if(is_null($ingHeadings)) { $ingHeadings = array(); }

    $ingNum = 0;

    // Render results
    foreach($ingredients as $ingredient) {
        /*if(array_key_exists($ingNum, $ingHeadings)) {
            echo "<div class='ingredient-heading'>";
            echo "<i class='fas fa-minus-circle' onclick='this.parentNode.remove()'></i>";
            echo "<input type='text' class='ing-heading' data-position='$ingNum' name='ing-headings[$ingNum]' placeholder='Heading' value='" . $ingHeadings[$ingNum] . "'></div>";
        }*/

        echo "<div class='ingredient-container'>";
        echo "<i class='fas fa-minus-circle' onclick='this.parentNode.remove()'></i>";
        echo "<input type='text' class='ing-qty' id='ing-qty-$ingNum' name='ing-qty[]' placeholder='1' maxlength='5' onKeyUp='checkIngredientIfEmpty()' value='" . $ingredient['quantity'] . "'>";
        echo "<input type='text' class='ing-unit' id='ing-unit-$ingNum' name='ing-unit[]' placeholder='cup' onKeyUp='checkIngredientIfEmpty()' value='" . $ingredient['unit'] . "'>";
        echo "<input type='text' class='ing-name' id='ing-name-$ingNum' name='ing-name[]' placeholder='flour' onKeyUp='checkIngredientIfEmpty()' value='" . $ingredient['name'] . "'>";
        echo "<div class='add-row'><a onclick='addIngredientBelow(this)'><i class='fas fa-plus-circle'></i>Add ingredient</a>";
        echo "</div></div>";

        $ingNum++;
    }
} else {
    /*echo "<div class='ingredient-heading'>";
    echo "<i class='fas fa-minus-circle' onclick='this.parentNode.remove()'></i>";
    echo "<input type='text' class='ing-heading' data-position='0' name='ing-headings[0]' placeholder='Heading'></div>";*/
    echo "<div class='ingredient-container'>";
    echo "<i class='fas fa-minus-circle' onclick='this.parentNode.remove()'></i>";
    echo "<input type='text' class='ing-qty' id='ing-qty-1' name='ing-qty[]' placeholder='1' maxlength='5' onKeyUp='checkIngredientIfEmpty()'>";
    echo "<input type='text' class='ing-unit' id='ing-unit-1' name='ing-unit[]' placeholder='cup' onKeyUp='checkIngredientIfEmpty()'>";
    echo "<input type='text' class='ing-name' id='ing-name-1' name='ing-name[]' placeholder='flour' onKeyUp='checkIngredientIfEmpty()'>";
    echo "<div class='add-row'><a onclick='addIngredientBelow(this)'><i class='fas fa-plus-circle'></i>Add ingredient</a>";
    echo "</div></div>";
}

echo "</div>";
echo "<div class='add-ingredient'><a onclick='addIngredientRow()'><i class='fas fa-plus-circle'></i>Add ingredient</a></div>";
//echo "<div class='add-heading'><a onclick='addIngredientHeading()'><i class='fas fa-plus-circle'></i>Add heading</a></div>";

// Process
echo "<div class='process-section'>";
echo "<label for='process'>Steps</label>";

if(!empty($recipe)) {
    // Get variables in a usable format
    $process = json_decode($recipe['process'], true);
    $procHeadings = json_decode($recipe['process_headings'], true);
    if(is_null($process)) { $process = array(); }
    if(is_null($procHeadings)) { $procHeadings = array(); }

    $stepNum = 0;

    foreach($process as $step) {
        /*if(array_key_exists($stepNum, $procHeadings)) {
            echo "<div class='process-heading'>";
            echo "<i class='fas fa-minus-circle' onclick='this.parentNode.remove()'></i>";
            echo "<input type='text' class='proc-heading' data-position='$stepNum' name='proc-headings[$stepNum]' placeholder='Heading' value='" . $procHeadings[$stepNum] . "'></div>";
        }*/

        echo "<div class='process-container'>";
        echo "<i class='fas fa-minus-circle' onclick='this.parentNode.remove()'></i>";
        echo "<textarea class='step' id='step-1' name='steps[]' placeholder='First...' onKeyUp='checkProcessIfEmpty()' rows='1'>" . $step . "</textarea>";
        echo "<div class='add-row'><a onclick='addProcessBelow(this)'><i class='fas fa-plus-circle'></i>Add step</a></div>";
        echo "</div>";

        $stepNum++;
    }

} else {
    /*echo "<div class='process-heading'>";
    echo "<i class='fas fa-minus-circle' onclick='this.parentNode.remove()'></i>";
    echo "<input type='text' class='proc-heading' data-position='0' name='proc-headings[0]' placeholder='Heading'></div>";*/
    echo "<div class='process-container'>";
    echo "<i class='fas fa-minus-circle' onclick='this.parentNode.remove()'></i>";
    echo "<textarea class='step' id='step-1' name='steps[]' placeholder='First...' onKeyUp='checkProcessIfEmpty()' rows='1'></textarea>";
    echo "<div class='add-row'><a onclick='addProcessBelow(this)'><i class='fas fa-plus-circle'></i>Add step</a></div>";
    echo "</div>";
}

echo "</div>";
echo "<div class='add-step'><a onclick='addProcessRow()'><i class='fas fa-plus-circle'></i>Add step</a></div>";
//echo "<div class='add-heading'><a onclick='addProcessHeading()'><i class='fas fa-plus-circle'></i>Add heading</a></div>";

// Notes
echo "<div class='notes-section'>";
echo "<label for='notes'>Notes</label>";
//echo "<div class='notes-container'>";

if(!empty($recipe)) {
    // Get variables in usable format
    $notes = json_decode($recipe['notes'], true);
    if(is_null($notes)) { $notes = array(); }

    $noteNum = 0;

    foreach($notes as $note) {
        echo "<div class='notes-container' data-num='$noteNum'><i class='fas fa-minus-circle' onclick='this.parentNode.remove()'></i><textarea class='note' id='note-$noteNum' name='notes[]' placeholder='Remember to...' onKeyUp='checkNoteIfEmpty();' rows='1'>" . $note . "</textarea></div>";

        $noteNum++;
    }
}
echo "</div>";
echo "<div class='add-note'><a onclick='addNoteRow()'><i class='fas fa-plus-circle'></i>Add note</a></div>";

// Picture
echo "<div class='picture-container'>";
if(!empty($recipe)) { 
    echo "<label for='picture' class='choose-file-2'>Change picture</label>";
} else {
    echo "<label for='picture' class='choose-file'>Upload a picture</label>";
}
echo "<input type='file' name='picture' id='picture' placeholder='Upload a picture'>";
echo "</div>";

// User data
echo "<input type='hidden' name='creatorid' value='" . $_SESSION['userid'] . "'>";

// Is this a new recipe or an existing recipe?
echo "<input type='hidden' name='existingrecipe' value='";
if(isset($_GET['id'])) {
    echo $_GET['id'];
} else {
    echo 'false';
}
echo "'>";

// Submit button
echo "<br><input type='submit' name='submit' class='button' value='";
if(!is_null($recipe)) {
    echo "Save changes'>";
} else {
    echo "Add recipe'>";
}

// Cancel button
if(isset($_GET['id'])) {
    echo "<a class='button2' style='margin-right: 1em' href='recipe.php?id=" . $_GET['id'] . "'>Cancel</a>";
} else {
    echo "<a class='button2' href='index.php'>Cancel</a>";
}

// Show error messages
echo "<div class='alert' id='form-error'></div>";

// End the form
echo "</form>";

// End the container
echo "</div>";