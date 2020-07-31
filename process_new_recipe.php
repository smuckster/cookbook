<?php

require 'lib.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

//echo var_dump($_POST);
/*echo "\n\nAnd the files variable contains:\n";
echo var_dump($_FILES);*/

// Check if an image was uploaded
$upload_ok = 1;
if($_FILES['picture']['name'] != '' || $_FILES['picture']['name'] != NULL) {

    // Deal with image upload variables and functions first
    $img_dir = '/var/www/cookbook/img/';
    $img_file_path = $img_dir . basename($_FILES['picture']['name']);
    $img_filetype = strtolower(pathinfo($img_file_path, PATHINFO_EXTENSION));
    $duplicate_file_suffix = 1;

    //echo "Uploaded file path: " . $img_file_path;

    // Make sure the image file is an actual image
    if(isset($_POST['submit'])) {
        $check = getimagesize($_FILES['picture']['tmp_name']);
        if($check !== false) {
            $upload_ok = 1;
        } else {
            echo "Please upload an image.\n";
            $upload_ok = 0;
        }
    }
    
    $filename_suffix = 1;

    if(!file_exists($img_file_path)) {
        $final_filename = $_FILES['picture']['name'];
    }
    while(file_exists($img_file_path)) {
        $tmpfn = explode('.', $_FILES['picture']['name']);
        $tmpfn[0] .= $filename_suffix;
        $img_file_path = $img_dir . $tmpfn[0] . '.' . $tmpfn[1];
        $final_filename = $tmpfn[0] . '.' . $tmpfn[1];
        $filename_suffix++;
    }

    // Check file size
    if($_FILES['picture']['size'] > 5000000) {
        echo "Sorry, your image is too big. The maximum filesize is 5 MB.\n";
        $upload_ok = 0;
    }

    // Limit file formats
    if($img_filetype != 'jpg' && $img_filetype != 'png' && $img_filetype != 'jpeg' && $img_filetype != 'gif') {
        echo "Sorry, only JPG, JPEG, PNG, and GIF filetypes allowed.\n";
        $upload_ok = 0;
    }

    // Check if upload_ok is set to 0, and send an error if so
    if($upload_ok == 0) {
        echo "Your file could not be uploaded.\n";
    } else {
        if(move_uploaded_file($_FILES['picture']['tmp_name'], $img_file_path)) {
            //echo "File uploaded just fine";
        } else {
            echo "There was an error uploading your file.";
        }
    }
}

// Process the other form data if the upload went ok
if($upload_ok == 1) {
    // If file wasn't uploaded, deal with the finalfilename variable
    if(!isset($final_filename)) {
        if(isset($_POST['picture'])) {
            $final_filename = $_POST['picture'];
        } else {
            $final_filename = '';
        }
    }

    // -- Ingredients
    $ingredients = array();

    // Delete any entirely empty rows and save the rest in the ingredients array
    if(isset($_POST['ing-name']) && isset($_POST['ing-unit']) && isset($_POST['ing-qty'])) {
        for($i = 0; $i < sizeof($_POST['ing-name']); $i++) {
            if($_POST['ing-name'][$i] == '' && $_POST['ing-unit'][$i] == '' && $_POST['ing-qty'][$i] == '') {
            } else {
                $ingredients[] = array('quantity'   => sanitize($_POST['ing-qty'][$i]), 
                                        'unit'      => sanitize($_POST['ing-unit'][$i]), 
                                        'name'      => sanitize($_POST['ing-name'][$i])); 
            }
        }
    }

    // -- Ingredient headings
    $ingHeadings = array();

    // Delete any entirely empty rows and save the rest in the ingHeadings array
    if(isset($_POST['ing-headings'])) {
        $ingHeadings = array_filter($_POST['ing-headings'], 'sanitize');
    }

    // -- Process
    $process = array();

    // Delete any entirely empty rows and save the rest in the process array
    if(isset($_POST['steps'])) {
        $process = array_filter($_POST['steps'], 'sanitize');
    }

    // -- Process headings
    $processHeadings = array();

    // Delete any entirely empty rows and save the rest in the processHeadings array
    if(isset($_POST['proc-headings'])) {
        $processHeadings = array_filter($_POST['proc-headings'], 'sanitize');
    }

    // -- Notes
    $notes = array();

    // Delete any entirely empty rows and save the rest in the notes array
    if(isset($_POST['notes'])) {
        $notes = array_filter($_POST['notes'], 'sanitize');
    }

    // Convert the arrays to JSON format
    $ingredients_json = json_encode($ingredients);
    $ingHeadings_json = json_encode($ingHeadings);
    $process_json = json_encode($process);
    $processHeadings_json = json_encode($processHeadings);
    $notes_json = json_encode($notes);

    // Check if this is a new recipe or an existing recipe
    if($_POST['existingrecipe'] != 'false') {
        if($final_filename == '' || $final_filename == NULL) {
            // Update recipe in database if no picture was uploaded
            $sql = "UPDATE recipes SET creatorid = ?, name = ?, description = ?, attribution = ?, yield = ?, time = ?, ingredients = ?, ing_headings = ?, process = ?, process_headings = ?, notes = ? WHERE id = ?";
            $results = query_db(array($sql, $_POST['creatorid'], $_POST['name'], $_POST['description'], $_POST['attribution'], $_POST['yield'], $_POST['time'], $ingredients_json, $ingHeadings_json, $process_json, $processHeadings_json, $notes_json, $_POST['existingrecipe']));
        } else {
            // Update recipe in database if a new picture was uploaded
            $sql = "UPDATE recipes SET creatorid = ?, name = ?, description = ?, attribution = ?, yield = ?, time = ?, ingredients = ?, ing_headings = ?, process = ?, process_headings = ?, notes = ?, picture = ? WHERE id = ?";
            $results = query_db(array($sql, $_POST['creatorid'], $_POST['name'], $_POST['description'], $_POST['attribution'], $_POST['yield'], $_POST['time'], $ingredients_json, $ingHeadings_json, $process_json, $processHeadings_json, $notes_json, $final_filename, $_POST['existingrecipe']));
        }
    } else {
        // Insert recipe into database
        $sql = "INSERT INTO recipes (creatorid, name, description, attribution, yield, time, ingredients, ing_headings, process, process_headings, notes, picture) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $results = query_db(array($sql, $_POST['creatorid'], $_POST['name'], $_POST['description'], $_POST['attribution'], $_POST['yield'], $_POST['time'], $ingredients_json, $ingHeadings_json, $process_json, $processHeadings_json, $notes_json, $final_filename));
    }

    if($results == null) {
        //$results = query_db(array("SELECT id FROM recipes WHERE name LIKE '{$_POST['name']}' ORDER BY timechanged, timecreated DESC"));
        $results = query_db(array("SELECT id FROM recipes WHERE name LIKE ? ORDER BY timechanged, timecreated DESC", $_POST['name']));
        echo "id=" . $results[0]['id'];
    } else {
        echo $results;
    }
}

// Array sort function to sanitize user input before 
// adding it to the database
function sanitize($s) {
    return strip_tags($s);
}