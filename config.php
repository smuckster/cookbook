<?php

/** A generic configuration file for a project that 
 * requires a connection with a database. This configuration
 * assumes the use of PDO.
 *
 * For documentation about using PDO: https://phpdelusions.net/pdo
 */

// Set database credentials
$dbhost     = 'localhost';
$dbname     = 'cookbook';
$dbuser     = 'cookbook';
$dbpassword = 'DevilsF00dPepperc*rn';
$dbcharset  = 'utf8mb4';
 
$dsn = "mysql:host=$dbhost;dbname=$dbname;charset=$dbcharset";
$options = [ 
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Create database connection
try {
    $pdo = new PDO($dsn, $dbuser, $dbpassword, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Debug settings
error_reporting(E_ALL);
ini_set('display_errors', 1);
