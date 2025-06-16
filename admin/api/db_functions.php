<?php
// API utility functions for database operations
// This file should contain only functions, no HTML output

// Database connection
function getDbConnection() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "l1j_remastered";
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Function to sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Function to validate numeric input
function validateNumeric($input, $fieldName, $min = null, $max = null) {
    if (!is_numeric($input)) {
        return "$fieldName must be a number.";
    }
    
    if ($min !== null && $input < $min) {
        return "$fieldName must be at least $min.";
    }
    
    if ($max !== null && $input > $max) {
        return "$fieldName must not exceed $max.";
    }
    
    return null; // No error
}

// Function to validate table names against allowlist
function isValidTable($table) {
    $allowed_tables = [
        'spawnlist',
        'spawnlist_boss',
        'spawnlist_clandungeon',
        'spawnlist_indun',
        'spawnlist_other',
        'spawnlist_ruun',
        'spawnlist_ub',
        'spawnlist_unicorntemple',
        'spawnlist_worldwar'
    ];
    
    return in_array($table, $allowed_tables);
}
?>