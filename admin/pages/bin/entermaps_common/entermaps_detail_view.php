<?php
require_once __DIR__ . '/../../../includes/auth_check.php';
require_once __DIR__ . '/../../../../includes/db.php';
require_once __DIR__ . '/../../../includes/header.php';

// Get parameters from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action_name = isset($_GET['action_name']) ? $_GET['action_name'] : '';

if ($id <= 0 || empty($action_name)) {
    echo "<div class='alert alert-error'>Invalid entry ID or action name provided.</div>";
    require_once __DIR__ . '/../../../includes/footer.php';
    exit;
}

// Main query to get entry details with translation
$query = "
    SELECT 
        e.*,
        t.text_english as action_name_en
    FROM bin_entermaps_common e
    LEFT JOIN 0_translations t ON e.action_name = t.text_korean
    WHERE e.id = ? AND e.action_name = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param('is', $id, $action_name);
$stmt->execute();
$result = $stmt->get_result();
$entry = $result->fetch_assoc();

if (!$entry) {
    echo "<div class='alert alert-error'>Entry with ID $id and action name '$action_name' not found.</div>";
    require_once __DIR__ . '/../../../includes/footer.php';
    exit;
}

// Helper function to format coordinates
function formatCoordinates($x, $y, $range = null) {
    if ($range && $range > 0) {
        return "($x, $y) ±{$range}";
    }
    return "($x, $y)";
}

// Helper function to format max users
function formatMaxUsers($maxUser) {
    return $maxUser == 0 ? 'Unlimited' : number_format($maxUser);
}

// Helper function to format conditions
function formatConditions($conditions) {
    if (empty($conditions)) {
        return 'No conditions specified';
    }
    
    // Try to format as JSON if it looks like JSON
    if (substr(trim($conditions), 0, 1) === '{' || substr(trim($conditions), 0, 1) === '[') {
        $decoded = json_decode($conditions, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return '<pre>' . json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
        }
    }
    
    return nl2br(htmlspecialchars($conditions));
}

// Helper function to format destinations
function formatDestinations($destinations) {
    if (empty($destinations)) {
        return 'No destinations specified';
    }
    
    // Try to format as JSON if it looks like JSON
    if (substr(trim($destinations), 0, 1) === '{' || substr(trim($destinations), 0, 1) === '[') {
        $decoded = json_decode($destinations, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return '<pre>' . json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
        }
    }
    
    return nl2br(htmlspecialchars($destinations));
}
?>

<div class="page-header">
    <nav class="breadcrumb">
        <a href="../../index.php">Dashboard</a> → 
        <a href="../index.php">Binary Tables</a> → 
        <a href="entermaps_list_view.php">Enter Maps List</a> → 
        <span>Entry #<?php echo $id; ?></span>
    </nav>
    <h1>Enter Maps Details - ID: <?php echo $id; ?></h1>
</div>

<div class="admin-actions">
    <a href="entermaps_list_view.php" class="admin-btn admin-btn-secondary">← Back to List</a>
</div>

<div class="detail-container">
    <div class="detail-section">
        <h2>Basic Information</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Entry ID:</label>
                <span><?php echo htmlspecialchars($entry['id']); ?></span>
            </div>
            <div class="detail-item">
                <label>Action Name (Korean):</label>
                <span><?php echo htmlspecialchars($entry['action_name']); ?></span>
            </div>
            <div class="detail-item">
                <label>Action Name (English):</label>
                <span><?php echo htmlspecialchars($entry['action_name_en'] ?: 'Not translated'); ?></span>
            </div>
            <div class="detail-item">
                <label>Number ID:</label>
                <span><?php echo htmlspecialchars($entry['number_id']); ?></span>
            </div>
        </div>
    </div>

    <div class="detail-section">
        <h2>Location & Access</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <label>X Coordinate:</label>
                <span><?php echo number_format($entry['loc_x']); ?></span>
            </div>
            <div class="detail-item">
                <label>Y Coordinate:</label>
                <span><?php echo number_format($entry['loc_y']); ?></span>
            </div>
            <div class="detail-item">
                <label>Location Range:</label>
                <span><?php echo $entry['loc_range']; ?> units</span>
            </div>
            <div class="detail-item">
                <label>Full Location:</label>
                <span><?php echo formatCoordinates($entry['loc_x'], $entry['loc_y'], $entry['loc_range']); ?></span>
            </div>
            <div class="detail-item">
                <label>Priority ID:</label>
                <span><?php echo htmlspecialchars($entry['priority_id']); ?></span>
            </div>
            <div class="detail-item">
                <label>Max Users:</label>
                <span><?php echo formatMaxUsers($entry['maxUser']); ?></span>
            </div>
        </div>
    </div>

    <div class="detail-section">
        <h2>Entry Conditions</h2>
        <div class="detail-item">
            <label>Conditions:</label>
            <div class="detail-text">
                <?php echo formatConditions($entry['conditions']); ?>
            </div>
        </div>
    </div>

    <div class="detail-section">
        <h2>Destinations</h2>
        <div class="detail-item">
            <label>Destination Data:</label>
            <div class="detail-text">
                <?php echo formatDestinations($entry['destinations']); ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
