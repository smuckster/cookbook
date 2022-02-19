<?php
include 'header.php';

// ------------------------------
// How searching works
// ------------------------------

// The contents of each recipe are contained in four different fulltext
// indexes. Each index is weighted differently according to how high in the
// list of search results it should appear.

// 1. Name
// 2. Ingredients
// 3. Description, Steps
// 4. Ingredient headings, Step headings, Attribution

// Reference: https://stackoverflow.com/questions/547542/how-can-i-manipulate-mysql-fulltext-search-relevance-to-make-one-field-more-val#600915

// Figure out what type of search to perform based on the parameters
if(isset($_GET['category'])) {
    $sql = "SELECT r.*, c.name AS catname, c.color AS catcolor
            FROM recipes r
            LEFT JOIN recipe_categories rc ON r.id = rc.recipeid
            LEFT JOIN categories c ON rc.categoryid = c.id
            WHERE c.id = ?";
    $rec_results = search($_GET['category'], $sql, 1);
    if(!is_array($rec_results) || sizeof($rec_results) < 1) {
        $rec_results = array();
    }
    $cat_results = array();
} else if(isset($_GET['q'])) {
    // Get the results of the search query
    $sql = "SELECT r.*,
            MATCH (r.name) 
                AGAINST (concat(?,'*') IN BOOLEAN MODE) AS L1,
            MATCH (r.ingredients)
                AGAINST (concat(?,'*') IN BOOLEAN MODE) AS L2,
            MATCH (r.description, r.process)
                AGAINST (concat(?,'*') IN BOOLEAN MODE) AS L3,
            MATCH (r.ing_headings, r.process_headings, r.attribution)
                AGAINST (concat(?,'*') IN BOOLEAN MODE) AS L4 
            FROM recipes r
            WHERE MATCH (r.name, r.description, r.attribution, r.ingredients, r.ing_headings, r.process, r.process_headings, r.notes) 
                AGAINST (concat(?,'*') IN BOOLEAN MODE) 
            ORDER BY (L1*2)+(L2*1.5)+(L3)+(L4*0.75) DESC";
    $rec_results = search($_GET['q'], $sql, 5);
    if(!is_array($rec_results) || sizeof($rec_results) < 1) {
        $rec_results = array();
    }

    // Get the categories associated with the search query
    $sql = "SELECT c.*
            FROM categories c
            WHERE c.name LIKE ?";
    $cat_results = search("%" . $_GET['q'] . "%", $sql, 1);
    if(!is_array($cat_results) || sizeof($cat_results) < 1) {
        $cat_results = array();
    }
} else {
    $cat_results = array();
    $rec_results = array();
}

// Render the page
echo "<div class='search-page-container'>";

// If this was a category search, list the category instead of the search bar
if(isset($_GET['category'])) {
    if(sizeof($rec_results) < 1) {
        echo "<div class='results'>There are no recipes in this category!</div>";
    } else {
        echo "<div class='category-result-name' style='background-color:" . $rec_results[0]['catcolor'] . "'>" . $rec_results[0]['catname'] . " Category</div>";
    }
} else if(isset($_GET['q'])) {
    // Search form
    echo "<form id='search-recipes' action='search.php' method='get'>";
    echo "<input type='text' name='q' id='search' placeholder='Search";
    if(isset($_GET['category'])) {
        echo " within category' required>";
    } else {
        echo "' value='" . $_GET['q'] . "' required>";
    }
    echo "<div class='submit-search'>";
    echo "<i class='fas fa-search'></i>";
    echo "</div>";
    echo "<input type='submit'>";
    echo "</form>";
} else {
    // Search form
    echo "<form id='search-recipes' action='search.php' method='get'>";
    echo "<input type='text' name='q' id='search' placeholder='Search";
    if(isset($_GET['category'])) {
        echo " within category' required>";
    } else {
        echo "' value='' required>";
    }
    echo "<div class='submit-search'>";
    echo "<i class='fas fa-search'></i>";
    echo "</div>";
    echo "<input type='submit'>";
    echo "</form>";
}

// Category results
if(sizeof($cat_results) > 0) {
    echo "<div class='cat-results-container'>";
    foreach($cat_results as $result) {
        echo "<div class='cat-tag' style='background-color:" . $result['color'] . "' data-id='" . $result['id'] . "'><a href='search.php?category=" . $result['id'] . "'>" . $result['name'] . "</a></div>";
    }
    echo "</div>";
}

// Display different messages based on number of results
if(sizeof($rec_results) == 0) {
    echo "<div class='results'>Sorry, your search returned no recipes.</div>";
} else {
    echo "<div class='results'>" . sizeof($rec_results) . " result";
    if(sizeof($rec_results) > 1) {
        echo "s";
    }
    echo "</div>";
}

echo "<div class='search-results-container'>";
foreach($rec_results as $result) {
    echo "<a class='result-container' href='recipe.php?id=" . $result['id'] . "' data-id='" . $result['id'] . "'>";
    if($result['picture'] != '' || $result['picture'] != NULL) {
        echo "<div class='result-image-container' style='background-image:url(\"img/" . $result['picture'] . "\")'></div>";
    } else {
        echo "<div class='result-image-container' style='background-image:url(\"img/eggplant.jpeg\")'></div>";
    }
    if(strlen($result['name']) > 55) {
        $name = substr($result['name'], 0, 55);
        $name .= '...';
    } else {
        $name = $result['name'];
    }

    echo "<div class='result-name'>" . $name . "</div>";
    //echo "L1: " . $result['L1'] . "; L2: " . $result['L2'] . "; L3: " . $result['L3'] . "; L4: " . $result['L4'];
    //echo $result['score'];
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

function search($query, $sql, $placeholderNum) {
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
        $finalwords .= "$word ";
    }
    $finalwords = trim($finalwords);

    $searchArray = array($sql);
    for($i = 0; $i < $placeholderNum; $i++) {
        $searchArray[] = $finalwords;
    }

    // Prepare the SQL query
    /*$sql = "SELECT recipes.*,
    MATCH (name, description, attribution, ingredients, ing_headings, process, process_headings, notes) AGAINST ('$finalwords' IN BOOLEAN MODE) AS score
    FROM `recipes`
    WHERE MATCH (name, description, attribution, ingredients, ing_headings, process, process_headings, notes) AGAINST ('$finalwords' IN BOOLEAN MODE)
    ORDER BY score DESC";*/
    $results = query_db($searchArray);
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