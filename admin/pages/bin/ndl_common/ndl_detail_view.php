<?php
require_once __DIR__ . '/../common/detail_header.php';

// Get parameters from URL (composite primary key)
$map_number = isset($_GET['map']) ? (int)$_GET['map'] : 0;
$npc_classId = isset($_GET['class']) ? (int)$_GET['class'] : 0;
$territory_startXY = isset($_GET['start']) ? (int)$_GET['start'] : 0;
$territory_endXY = isset($_GET['end']) ? (int)$_GET['end'] : 0;

if ($map_number <= 0 || $npc_classId <= 0) {
    echo "<div class='alert alert-error'>Invalid NDL parameters provided.</div>";
    require_once __DIR__ . '/../common/detail_footer.php';
    exit;
}

// Main query to get NDL details with translation
$query = "
    SELECT 
        n.*,
        t.text_english as npc_desc_en
    FROM bin_ndl_common n
    LEFT JOIN 0_translations t ON n.npc_desc_kr = t.text_korean
    WHERE n.map_number = ? AND n.npc_classId = ? AND n.territory_startXY = ? AND n.territory_endXY = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param('iiii', $map_number, $npc_classId, $territory_startXY, $territory_endXY);
$stmt->execute();
$result = $stmt->get_result();
$ndl = $result->fetch_assoc();

if (!$ndl) {
    echo "<div class='alert alert-error'>NDL record not found with the provided parameters.</div>";
    require_once __DIR__ . '/../common/detail_footer.php';
    exit;
}

// Helper function to format coordinates
function formatCoordinates($xy) {
    if ($xy == 0) return 'N/A (0)';
    
    // Try to extract X and Y coordinates if they're packed
    // Common packing methods: (X << 16) | Y or similar
    $x = ($xy >> 16) & 0xFFFF;
    $y = $xy & 0xFFFF;
    
    if ($x > 0 && $y > 0) {
        return "X: $x, Y: $y ($xy)";
    }
    
    return $xy;
}

// Helper function to format map number
function formatMapNumber($map_number) {
    // You might want to add map name lookups here if you have a maps table
    return "Map $map_number";
}

// Helper function to format averages
function formatAverage($value, $suffix = '') {
    if ($value == 0) return 'N/A';
    return number_format($value) . $suffix;
}
?>

<div class="page-header">
    <nav class="breadcrumb">
        <a href="../../index.php">Dashboard</a> → 
        <a href="../index.php">Binary Tables</a> → 
        <a href="ndl_list_view.php">NDL Common List</a> → 
        <span>NDL Record</span>
    </nav>
    <h1>NDL Details - Map: <?php echo $map_number; ?>, Class: <?php echo $npc_classId; ?></h1>
</div>

<div class="admin-actions">
    <a href="ndl_list_view.php" class="admin-btn admin-btn-secondary">← Back to List</a>
</div>

<div class="detail-container">
    <!-- Main Content Row -->
    <div class="weapon-detail-row">
        <!-- Column 1: Image Preview (or placeholder) -->
        <div class="weapon-image-col">
            <div class="weapon-image-container">
                <img src="../../../../assets/img/icons/ndl.png" 
                     alt="NDL Icon" 
                     class="weapon-main-image"
                     onerror="this.src='../../../../assets/img/icons/0.png'">
            </div>
            <div class="icon-id-display">
                <span>Map: <?php echo $map_number; ?>, Class: <?php echo $npc_classId; ?></span>
            </div>
        </div>
        
        <!-- Column 2: Basic Information -->
        <div class="weapon-info-col">
            <div class="weapon-basic-info">
                <h2>Basic Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Map Number:</label>
                        <span><?php echo formatMapNumber($ndl['map_number']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>NPC Class ID:</label>
                        <span><?php echo htmlspecialchars($ndl['npc_classId']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Territory Location Desc:</label>
                        <span><?php echo htmlspecialchars($ndl['territory_location_desc']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="detail-section">
        <h2>NPC Description</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Name (Korean):</label>
                <span><?php echo htmlspecialchars($ndl['npc_desc_kr'] ?: 'None'); ?></span>
            </div>
            <div class="detail-item">
                <label>Name (English):</label>
                <span><?php echo htmlspecialchars($ndl['npc_desc_en'] ?: 'Not translated'); ?></span>
            </div>
        </div>
    </div>

    <div class="detail-section">
        <h2>Territory Coordinates</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Territory Start XY:</label>
                <span><?php echo formatCoordinates($ndl['territory_startXY']); ?></span>
            </div>
            <div class="detail-item">
                <label>Territory End XY:</label>
                <span><?php echo formatCoordinates($ndl['territory_endXY']); ?></span>
            </div>
        </div>
    </div>

    <div class="detail-section">
        <h2>Territory Averages</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Average NPC Value:</label>
                <span><?php echo formatAverage($ndl['territory_average_npc_value']); ?></span>
            </div>
            <div class="detail-item">
                <label>Average AC:</label>
                <span><?php echo formatAverage($ndl['territory_average_ac']); ?></span>
            </div>
            <div class="detail-item">
                <label>Average Level:</label>
                <span><?php echo formatAverage($ndl['territory_average_level']); ?></span>
            </div>
            <div class="detail-item">
                <label>Average Wisdom:</label>
                <span><?php echo formatAverage($ndl['territory_average_wis']); ?></span>
            </div>
            <div class="detail-item">
                <label>Average Magic Resistance:</label>
                <span><?php echo formatAverage($ndl['territory_average_mr']); ?></span>
            </div>
            <div class="detail-item">
                <label>Average Magic Barrier:</label>
                <span><?php echo formatAverage($ndl['territory_average_magic_barrier']); ?></span>
            </div>
        </div>
    </div>

    <div class="detail-section">
        <h2>Raw Coordinate Data</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Raw Start XY:</label>
                <span><?php echo htmlspecialchars($ndl['territory_startXY']); ?></span>
            </div>
            <div class="detail-item">
                <label>Raw End XY:</label>
                <span><?php echo htmlspecialchars($ndl['territory_endXY']); ?></span>
            </div>
        </div>
    </div>

    <div class="detail-section">
        <h2>Primary Key Information</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Composite Key:</label>
                <span>Map: <?php echo $ndl['map_number']; ?>, Class: <?php echo $ndl['npc_classId']; ?>, Start: <?php echo $ndl['territory_startXY']; ?>, End: <?php echo $ndl['territory_endXY']; ?></span>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../common/detail_footer.php'; ?>
