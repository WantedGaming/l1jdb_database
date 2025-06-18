<?php
// API endpoint to create a new spawn
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

if (!isset($_POST['table'])) {
    $response['message'] = 'Missing required parameters';
    echo json_encode($response);
    exit;
}

$table = $_POST['table'];

// Validate the table name to prevent SQL injection
if (!isValidTable($table)) {
    $response['message'] = 'Invalid table name';
    echo json_encode($response);
    exit;
}

// Sanitize and validate form data
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$count = isset($_POST['count']) ? (int)$_POST['count'] : 0;
$npc_templateid = isset($_POST['npc_templateid']) ? (int)$_POST['npc_templateid'] : 0;
$group_id = isset($_POST['group_id']) ? (int)$_POST['group_id'] : 0;
$locx = isset($_POST['locx']) ? (int)$_POST['locx'] : 0;
$locy = isset($_POST['locy']) ? (int)$_POST['locy'] : 0;
$randomx = isset($_POST['randomx']) ? (int)$_POST['randomx'] : 0;
$randomy = isset($_POST['randomy']) ? (int)$_POST['randomy'] : 0;
$locx1 = isset($_POST['locx1']) ? (int)$_POST['locx1'] : 0;
$locy1 = isset($_POST['locy1']) ? (int)$_POST['locy1'] : 0;
$locx2 = isset($_POST['locx2']) ? (int)$_POST['locx2'] : 0;
$locy2 = isset($_POST['locy2']) ? (int)$_POST['locy2'] : 0;
$heading = isset($_POST['heading']) ? (int)$_POST['heading'] : 0;
$min_respawn_delay = isset($_POST['min_respawn_delay']) ? (int)$_POST['min_respawn_delay'] : 0;
$max_respawn_delay = isset($_POST['max_respawn_delay']) ? (int)$_POST['max_respawn_delay'] : 0;
$mapid = isset($_POST['mapid']) ? (int)$_POST['mapid'] : 0;
$respawn_screen = isset($_POST['respawn_screen']) ? (int)$_POST['respawn_screen'] : 0;
$movement_distance = isset($_POST['movement_distance']) ? (int)$_POST['movement_distance'] : 0;
$rest = isset($_POST['rest']) ? (int)$_POST['rest'] : 0;
$near_spawn = isset($_POST['near_spawn']) ? (int)$_POST['near_spawn'] : 0;

// Validation checks
$errors = [];

if ($id <= 0) {
    $errors[] = "ID must be greater than 0";
}

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
    
    // Check if ID already exists
    $checkSql = "SELECT id FROM $table WHERE id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param('i', $id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        $response['message'] = "Spawn ID $id is already in use";
        $response['id_taken'] = true;
        echo json_encode($response);
        exit;
    }
    
    // Prepare the SQL query with proper parameterization
    $sql = "INSERT INTO $table (id, name, count, npc_templateid, group_id, locx, locy, randomx, randomy, 
            locx1, locy1, locx2, locy2, heading, min_respawn_delay, max_respawn_delay, mapid, 
            respawn_screen, movement_distance, rest, near_spawn) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isiiiiiiiiiiiiiiiiii', $id, $name, $count, $npc_templateid, $group_id, $locx, $locy, 
                     $randomx, $randomy, $locx1, $locy1, $locx2, $locy2, $heading, $min_respawn_delay, 
                     $max_respawn_delay, $mapid, $respawn_screen, $movement_distance, $rest, $near_spawn);
    
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Spawn created successfully';
        $response['id'] = $id;
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