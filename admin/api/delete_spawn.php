<?php
// API endpoint to delete spawn data
header('Content-Type: application/json');

// Include database functions
require_once 'db_functions.php';

// Initialize response array
$response = ['success' => false, 'message' => ''];

// Validate input parameters
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

if (!isset($_POST['id']) || !isset($_POST['table'])) {
    $response['message'] = 'Missing required parameters';
    echo json_encode($response);
    exit;
}

$id = (int)$_POST['id'];
$table = $_POST['table'];

// Validate the table name to prevent SQL injection
if (!isValidTable($table)) {
    $response['message'] = 'Invalid table name';
    echo json_encode($response);
    exit;
}

try {
    // Get database connection
    $conn = getDbConnection();
    
    // Prepare the SQL query with proper parameterization
    $sql = "DELETE FROM $table WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Spawn deleted successfully';
    } else {
        $response['message'] = 'Database error: ' . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
}

echo json_encode($response);
?>