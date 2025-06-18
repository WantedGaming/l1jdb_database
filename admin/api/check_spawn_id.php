<?php
// API endpoint to check if a spawn ID is available
header('Content-Type: application/json');

// Include database functions
require_once 'db_functions.php';

// Initialize response array
$response = ['available' => false, 'message' => ''];

// Validate input parameters
if (!isset($_GET['id']) || !isset($_GET['table'])) {
    $response['message'] = 'Missing required parameters';
    echo json_encode($response);
    exit;
}

$id = (int)$_GET['id'];
$table = $_GET['table'];

// Validate the table name to prevent SQL injection
if (!isValidTable($table)) {
    $response['message'] = 'Invalid table name';
    echo json_encode($response);
    exit;
}

try {
    // Get database connection
    $conn = getDbConnection();
    
    // Check if ID already exists
    $sql = "SELECT id FROM $table WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $response['available'] = true;
        $response['message'] = "ID $id is available";
    } else {
        $response['message'] = "ID $id is already in use";
    }
    
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
}

echo json_encode($response);
?>