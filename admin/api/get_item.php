<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_GET['item_id']) || empty($_GET['item_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Item ID is required']);
    exit;
}

$itemId = (int)$_GET['item_id'];

if ($itemId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid Item ID']);
    exit;
}

try {
    $conn = getDbConnection();
    
    // Check in weapon table first
    $sql = "SELECT item_id, desc_en, iconId, 'weapon' as type FROM weapon WHERE item_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $itemId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'item' => $item
        ]);
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();
    
    // Check in armor table
    $sql = "SELECT item_id, desc_en, iconId, 'armor' as type FROM armor WHERE item_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $itemId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'item' => $item
        ]);
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();
    
    // Check in etcitem table
    $sql = "SELECT item_id, desc_en, iconId, 'item' as type FROM etcitem WHERE item_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $itemId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'item' => $item
        ]);
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();
    
    // Item not found
    echo json_encode([
        'success' => false,
        'message' => 'Item not found'
    ]);
    
    $conn->close();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
