<?php
// API endpoint to get spawn data for editing
header('Content-Type: application/json');

// Include database functions only (no HTML output)
require_once 'db_functions.php';

// Initialize response array
$response = [];

// Validate input parameters
if (!isset($_GET['id']) || !isset($_GET['table'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters']);
    exit;
}

$id = (int)$_GET['id'];
$table = $_GET['table'];

// Validate the table name to prevent SQL injection
if (!isValidTable($table)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid table name']);
    exit;
}

try {
    // Get database connection
    $conn = getDbConnection();
    
    // Prepare the SQL query with proper parameterization
    $sql = "SELECT * FROM $table WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Fetch the spawn data
        $spawn = $result->fetch_assoc();
        echo json_encode($spawn);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Spawn not found']);
    }
    
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>