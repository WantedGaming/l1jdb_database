<?php
// API endpoint to update spawn data
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

// Sanitize and validate form data
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$count = isset($_POST['count']) ? (int)$_POST['count'] : 0;
$npc_templateid = isset($_POST['npc_templateid']) ? (int)$_POST['npc_templateid'] : 0;
$locx = isset($_POST['locx']) ? (int)$_POST['locx'] : 0;
$locy = isset($_POST['locy']) ? (int)$_POST['locy'] : 0;
$mapid = isset($_POST['mapid']) ? (int)$_POST['mapid'] : 0;
$movement_distance = isset($_POST['movement_distance']) ? (int)$_POST['movement_distance'] : 0;
$min_respawn_delay = isset($_POST['min_respawn_delay']) ? (int)$_POST['min_respawn_delay'] : 0;
$max_respawn_delay = isset($_POST['max_respawn_delay']) ? (int)$_POST['max_respawn_delay'] : 0;

// Validation checks
$errors = [];

if (empty($name)) {
    $errors[] = "Name is required";
}

if ($count <= 0) {
    $errors[] = "Count must be greater than 0";
}

if ($npc_templateid <= 0) {
    $errors[] = "NPC Template ID must be greater than 0";
}

if (!empty($errors)) {
    $response['message'] = implode(', ', $errors);
    echo json_encode($response);
    exit;
}

try {
    // Get database connection
    $conn = getDbConnection();
    
    // Prepare the SQL query with proper parameterization
    $sql = "UPDATE $table SET 
            name = ?, 
            count = ?, 
            npc_templateid = ?, 
            locx = ?, 
            locy = ?, 
            mapid = ?, 
            movement_distance = ?, 
            min_respawn_delay = ?, 
            max_respawn_delay = ? 
            WHERE id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('siiiiiiiii', $name, $count, $npc_templateid, $locx, $locy, $mapid, $movement_distance, $min_respawn_delay, $max_respawn_delay, $id);
    
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Spawn updated successfully';
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