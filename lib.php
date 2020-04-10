<?php

require 'config.php';

/**
 * Queries the database.
 * 
 * Accepts an array of parameters where the first parameter
 * is the SQL query and subsequent parameters are variables
 * to be inserted into the query.
 *
 * @global $pdo The database connection object.
 *
 * @param $args {
 *      $sql The SQL string (with '?' placeholders if needed)
 *      $param1 Optional.
 *      $param2 Optional.
 *      ... The list of parameters to be inserted into SQL statement
 * }
 *
 * @return array A two-dimensional array of the rows retrieved
 **/

function query_db($args) {
    // Use the PDO object defined in config.php
    global $pdo;

    // Verify that the argument passed into the function is an array
    if(!is_array($args)) {
        exit("Argument is not an array.");
    }   

    // Prepare the SQL statement and exit if the provided SQL is invalid
    if(!($sth = $pdo->prepare($args[0]))) {
        exit("SQL query provided is invalid.");
    }   

    // Verify that the number of arguments passed matches the
    // number of placeholders in the query
    if(strlen($args[0]) - strlen(str_replace(str_split('?'), '', $args[0])) != (sizeof($args) - 1)) {
        exit("Number of arguments provided does not match number of placeholders in SQL query.");
    }   

    // Remove the SQL string from the array so remaining values 
    // can be passed into SQL statement
    unset($args[0]);

    // Set the statement handler object to return an associative array
    $sth->setFetchMode(PDO::FETCH_ASSOC);

    //exit(var_dump($sth));

    // If the args array still has any values, use them to prepare the SQL statement
    if(sizeof($args) > 0) {
        $sth->execute(array_values($args));
    } else {
        $sth->execute();
    }

    return $sth->fetchAll();
}
