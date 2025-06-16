<?php
// API endpoint to get spawn table statistics
header('Content-Type: application/json');

// Include database functions
require_once 'db_functions.php';

// Initialize response array
$response = ['success' => false, 'data' => [], 'message' => ''];

try {
    // Get database connection
    $conn = getDbConnection();
    
    // Get the list of spawn tables
    $spawn_tables = [
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
    
    $table_stats = [];
    $total_records = 0;
    
    // Check each table
    foreach ($spawn_tables as $table) {
        $sql = "SELECT COUNT(*) as count FROM $table";
        $result = $conn->query($sql);
        
        if ($result) {
            $row = $result->fetch_assoc();
            $count = (int)$row['count'];
            $total_records += $count;
            
            $table_stats[] = [
                'table' => $table,
                'count' => $count
            ];
        }
    }
    
    $response['success'] = true;
    $response['data'] = [
        'tables' => $table_stats,
        'total_records' => $total_records
    ];
    
    $conn->close();
} catch (Exception $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
}

echo json_encode($response);
?>