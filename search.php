<?php
include 'header.php';

// Get the results of the search query
$sql = "SELECT recipes.*, MATCH (name, description, attribution, ingredients, ing_headings, process, process_headings, notes) AGAINST (? IN BOOLEAN MODE) AS score FROM `recipes` WHERE MATCH (name, description, attribution, ingredients, ing_headings, process, process_headings, notes) AGAINST (? IN BOOLEAN MODE)";
$results = search($_GET['q'], $sql);
if(!is_array($results) || sizeof($results) < 1) {
    $results = array();
}

// Render the page
echo "<div class='search-page-container'>";

// Search form
echo "<form id='search-recipes' action='search.php' method='get'>";
echo "<input type='text' name='q' id='search' placeholder='Search' value='" . $_GET['q'] . "' required>";
echo "<div class='submit-search'>";
echo "<i class='fas fa-search'></i>";
echo "</div>";
echo "<input type='submit'>";
echo "</form>";

// Display different messages based on number of results
if(sizeof($results) == 0) {
    echo "<div class='results'>Sorry, your search returned no results.</div>";
} else {
    echo "<div class='results'>" . sizeof($results) . " result";
    if(sizeof($results) > 1) {
        echo "s";
    }
    if(strlen($_GET['q']) < 3) {
        echo "<br>(You may find better results by entering a longer search term.)";
    }
    echo "</div>";
}

echo "<div class='search-results-container'>";
foreach($results as $result) {
    echo "<a class='result-container' href='recipe.php?id=" . $result['id'] . "' data-id='" . $result['id'] . "'>";
    echo "<div class='result-image-container'><img class='result-image' src='img/" . $result['picture'] . "'></div>";
    if(strlen($result['name']) > 55) {
        $name = substr($result['name'], 0, 55);
        $name .= '...';
    } else {
        $name = $result['name'];
    }
    echo "<div class='result-name'>" . $name . "</div>";
    echo "<div class='result-attr'>" . $result['attribution'] . "</div><br>";
    if(strlen($result['description']) > 80) {
        $desc = substr($result['description'], 0, 80);
        $desc .= '...';
    } else {
        $desc = $result['description'];
    }
    echo "<div class='result-desc'>" . $desc . "</div>";
    echo "</a>";
}

echo "</div>";
echo "</div>";

function search($query, $sql) {
    // Trim whitespace from query
    $query = trim($query);

    // Verify that search is not empty
    if(mb_strlen($query) === 0 ) {
        return false;
    }

    $query = limitChars($query);
    $words = filterSearchKeys($query);
    $finalwords = '';
    
    // Add wildcard symbol to the end of each search term and add
    // it to array of final words for the query
    foreach($words as $word) {
        $finalwords .= "$word* ";
    }
    $finalwords = trim($finalwords);

    // Prepare the SQL query
    //$sql = "SELECT recipes.*, MATCH (name, description, attribution, ingredients, ing_headings, process, process_headings, notes) AGAINST (? IN BOOLEAN MODE) AS score FROM `recipes` WHERE MATCH (name, description, attribution, ingredients, ing_headings, process, process_headings, notes) AGAINST (? IN BOOLEAN MODE)";
    $results = query_db(array($sql, $finalwords, $finalwords));
    return $results;
}

// Remove unnecessary words from search term and return remaining words as an array
function filterSearchKeys($query) {
    $query = trim(preg_replace("/(\s+)+/", " ", $query));
    $words = array();

    //$list = array('in', 'it', 'a', 'the', 'of', 'or', 'I', 'you', 'he', 'me', 'us', 'they', 'she', 'to', 'but', 'that', 'this', 'those', 'then');
    $list = array();
    $c = 0;
    foreach(explode(" ", $query) as $key) {
        /*if(in_array($key, $list)) {
            continue;
        }*/
        $words[] = $key;
        if($c >= 15) {
            break;
        }
        $c++;
    }
    return $words;
}

// Limit number of characters in the search
function limitChars($query, $limit = 200) {
    return substr($query, 0, $limit);
}