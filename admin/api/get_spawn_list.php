<?php
// API endpoint to get spawn data with pagination
header('Content-Type: application/json');

// Include database functions
require_once 'db_functions.php';
require_once '../../includes/functions.php';

// Initialize response array
$response = ['success' => false, 'data' => [], 'pagination' => [], 'message' => ''];

// Default parameters
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = isset($_GET['limit']) ? max(10, min(100, (int)$_GET['limit'])) : 20;
$table = isset($_GET['table']) ? $_GET['table'] : 'spawnlist';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Validate the table name
if (!isValidTable($table)) {
    $response['message'] = 'Invalid table name';
    echo json_encode($response);
    exit;
}

try {
    // Get database connection
    $conn = getDbConnection();
    
    // Build the query based on search parameters
    $whereClause = '';
    $params = [];
    $types = '';
    
    if (!empty($search)) {
        $whereClause = " WHERE s.name LIKE ? OR s.id = ? OR s.npc_templateid = ? OR n.desc_en LIKE ?";
        $searchTerm = "%$search%";
        $searchNumeric = is_numeric($search) ? (int)$search : 0;
        $params = [$searchTerm, $searchNumeric, $searchNumeric, $searchTerm];
        $types = 'siss';
    }
    
    // Get total count for pagination
    $countSql = "SELECT COUNT(*) as total FROM $table s LEFT JOIN npc n ON s.npc_templateid = n.npcid $whereClause";
    
    if (!empty($params)) {
        $countStmt = $conn->prepare($countSql);
        $countStmt->bind_param($types, ...$params);
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $totalRows = $countResult->fetch_assoc()['total'];
        $countStmt->close();
    } else {
        $countResult = $conn->query($countSql);
        $totalRows = $countResult->fetch_assoc()['total'];
    }
    
    // Calculate pagination values
    $totalPages = ceil($totalRows / $limit);
    $page = min($page, max(1, $totalPages));
    $offset = ($page - 1) * $limit;
    
    // Get the actual data
    $dataSql = "SELECT s.*, n.desc_en as npc_name 
                FROM $table s 
                LEFT JOIN npc n ON s.npc_templateid = n.npcid 
                $whereClause
                ORDER BY s.id DESC 
                LIMIT ?, ?";
    
    $dataStmt = $conn->prepare($dataSql);
    
    if (!empty($params)) {
        $params[] = $offset;
        $params[] = $limit;
        $types .= 'ii';
        $dataStmt->bind_param($types, ...$params);
    } else {
        $dataStmt->bind_param('ii', $offset, $limit);
    }
    
    $dataStmt->execute();
    $dataResult = $dataStmt->get_result();
    
    $spawns = [];
    $npcIds = [];
    
    // First pass to collect all NPC IDs
    while ($row = $dataResult->fetch_assoc()) {
        $spawns[] = $row;
        if (!empty($row['npc_templateid'])) {
            $npcIds[] = $row['npc_templateid'];
        }
    }
    
    $dataStmt->close();
    
    // Get sprite information for all NPCs in one query
    $spriteData = [];
    if (!empty($npcIds)) {
        try {
            // Get the sprite information
            $npcSql = "SELECT npcid, spriteId FROM npc WHERE npcid IN (" . implode(',', array_map('intval', $npcIds)) . ")";
            $npcResult = $conn->query($npcSql);
            
            while ($npcRow = $npcResult->fetch_assoc()) {
                $spriteId = $npcRow['spriteId'];
                $npcId = $npcRow['npcid'];
                
                // Build paths for PNG and GIF
                $pngPath = '/assets/img/icons/ms' . $spriteId . '.png';
                $gifPath = '/assets/img/icons/ms' . $spriteId . '.gif';
                
                // Check which file exists
                if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/l1jdb_database' . $pngPath)) {
                    $spriteData[$npcId] = '/l1jdb_database' . $pngPath;
                } else if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/l1jdb_database' . $gifPath)) {
                    $spriteData[$npcId] = '/l1jdb_database' . $gifPath;
                } else {
                    $spriteData[$npcId] = '/l1jdb_database/assets/img/icons/0.png'; // Default
                }
            }
        } catch (Exception $e) {
            // Silently fail and use defaults
        }
    }
    
    // Add sprite paths to spawns
    foreach ($spawns as &$spawn) {
        if (!empty($spawn['npc_templateid']) && isset($spriteData[$spawn['npc_templateid']])) {
            $spawn['sprite_path'] = $spriteData[$spawn['npc_templateid']];
        } else {
            $spawn['sprite_path'] = '/l1jdb_database/assets/img/icons/0.png'; // Default image
        }
    }
    
    // Prepare the response
    $response['success'] = true;
    $response['data'] = $spawns;
    $response['pagination'] = [
        'page' => $page,
        'limit' => $limit,
        'total_records' => (int)$totalRows,
        'total_pages' => $totalPages
    ];
    
    $conn->close();
} catch (Exception $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
}

echo json_encode($response);
?>