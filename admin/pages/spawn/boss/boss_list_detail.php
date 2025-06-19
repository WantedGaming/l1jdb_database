<?php
require_once('../../../includes/header.php');

// Database connection (using the same pattern as API files)
function getDbConnection() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "l1j_remastered";
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8");
    
    return $conn;
}

// Validate input
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<div class="container mt-4"><div class="alert alert-danger">Invalid boss ID!</div>';
    echo '<a href="boss_list_view.php" class="btn btn-primary">Back to Boss List</a></div>';
    require_once('../../../includes/footer.php');
    exit;
}

$id = (int)$_GET['id'];
$db = getDbConnection();

// Get boss data
$query = "SELECT * FROM spawnlist_boss WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo '<div class="container mt-4"><div class="alert alert-warning">Boss not found!</div>';
    echo '<a href="boss_list_view.php" class="btn btn-primary">Back to Boss List</a></div>';
    require_once('../../../includes/footer.php');
    exit;
}

$boss = $result->fetch_assoc();

// Get map name if available (with comprehensive error handling)
$mapName = "Map " . $boss['spawnMapId'];
if (isset($boss['spawnMapId']) && $boss['spawnMapId'] > 0) {
    // Try to get map name, but handle all possible errors gracefully
    $mapQueries = [
        "SELECT name FROM mapids WHERE mapid = ?",
        "SELECT mapname FROM mapids WHERE mapid = ?",
        "SELECT desc_en FROM mapids WHERE mapid = ?",
        "SELECT desc_kr FROM mapids WHERE mapid = ?",
        "SELECT title FROM mapids WHERE mapid = ?",
        "SELECT map_name FROM mapids WHERE mapid = ?"
    ];
    
    foreach ($mapQueries as $mapQuery) {
        try {
            $mapStmt = $db->prepare($mapQuery);
            if ($mapStmt) {
                $mapStmt->bind_param("i", $boss['spawnMapId']);
                if ($mapStmt->execute()) {
                    $mapResult = $mapStmt->get_result();
                    if ($mapResult && $mapResult->num_rows > 0) {
                        $mapData = $mapResult->fetch_assoc();
                        if ($mapData && !empty(reset($mapData))) {
                            $mapName = reset($mapData); // Get first column value
                            $mapStmt->close();
                            break;
                        }
                    }
                }
                $mapStmt->close();
            }
        } catch (Exception $e) {
            // Silently continue to next query or use default
            continue;
        }
    }
}
?>

<div class="admin-content-wrapper">
    <div class="page-header">
        <div class="breadcrumb">
            <a href="/l1jdb_database/admin/">Dashboard</a> &raquo; 
            <a href="boss_list_view.php">Boss Management</a> &raquo; 
            <span>Boss Details</span>
        </div>
        <h1>Boss Details: <?php echo htmlspecialchars($boss['name']); ?></h1>
    </div>
    
    <div class="admin-header-actions">
        <a href="boss_list_view.php" class="admin-btn admin-btn-secondary">Back to Boss List</a>
        <a href="boss_list_edit.php?id=<?php echo $id; ?>" class="admin-btn admin-btn-primary">Edit Boss</a>
        <a href="boss_list_delete.php?id=<?php echo $id; ?>" class="admin-btn admin-btn-danger">Delete Boss</a>
    </div>
    
    <div class="field-group">
        <h3>Basic Information</h3>
        <div class="form-grid-2">
            <div>
                <table class="admin-table">
                    <tr>
                        <th width="30%">ID</th>
                        <td><?php echo htmlspecialchars($boss['id']); ?></td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td><?php echo htmlspecialchars($boss['name']); ?></td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td><?php echo htmlspecialchars($boss['desc_kr']); ?></td>
                    </tr>
                    <tr>
                        <th>NPC ID</th>
                        <td><?php echo htmlspecialchars($boss['npcid']); ?></td>
                    </tr>
                    <tr>
                        <th>Group ID</th>
                        <td><?php echo htmlspecialchars($boss['groupid']); ?></td>
                    </tr>
                    <tr>
                        <th>Spawn Group ID</th>
                        <td><?php echo htmlspecialchars($boss['spawn_group_id']); ?></td>
                    </tr>
                </table>
            </div>
            <div>
                <table class="admin-table">
                    <tr>
                        <th width="30%">Spawn Type</th>
                        <td><?php echo htmlspecialchars($boss['spawnType']); ?></td>
                    </tr>
                    <tr>
                        <th>Is Active</th>
                        <td>
                            <?php if ($boss['isYN'] == 'true'): ?>
                                <span class="badge badge-success">Active</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Mention Type</th>
                        <td><?php echo htmlspecialchars($boss['mentType']); ?></td>
                    </tr>
                    <tr>
                        <th>Mention</th>
                        <td><?php echo htmlspecialchars($boss['ment']); ?></td>
                    </tr>
                    <tr>
                        <th>Alive Duration</th>
                        <td><?php echo htmlspecialchars($boss['aliveSecond']); ?> seconds</td>
                    </tr>
                    <tr>
                        <th>Percent</th>
                        <td><?php echo htmlspecialchars($boss['percent']); ?>%</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="field-group">
        <h3>Spawn Information</h3>
        <div class="form-grid-2">
            <div>
                <table class="admin-table">
                    <tr>
                        <th width="30%">Map</th>
                        <td><?php echo htmlspecialchars($boss['spawnMapId']); ?> (<?php echo htmlspecialchars($mapName); ?>)</td>
                    </tr>
                    <tr>
                        <th>Coordinates</th>
                        <td>X: <?php echo htmlspecialchars($boss['spawnX']); ?>, Y: <?php echo htmlspecialchars($boss['spawnY']); ?></td>
                    </tr>
                    <tr>
                        <th>Heading</th>
                        <td><?php echo htmlspecialchars($boss['heading']); ?></td>
                    </tr>
                    <tr>
                        <th>Movement Distance</th>
                        <td><?php echo htmlspecialchars($boss['movementDistance']); ?></td>
                    </tr>
                </table>
            </div>
            <div>
                <table class="admin-table">
                    <tr>
                        <th width="30%">Spawn Day</th>
                        <td><?php echo htmlspecialchars($boss['spawnDay'] ?: 'Any day'); ?></td>
                    </tr>
                    <tr>
                        <th>Spawn Time</th>
                        <td><?php echo htmlspecialchars($boss['spawnTime'] ?: 'Any time'); ?></td>
                    </tr>
                    <tr>
                        <th>Random Minutes</th>
                        <td><?php echo htmlspecialchars($boss['rndMinut']); ?></td>
                    </tr>
                    <tr>
                        <th>Random Range</th>
                        <td><?php echo htmlspecialchars($boss['rndRange']); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Map location visualization (if coordinates are available) -->
    <?php if ($boss['spawnX'] != 0 && $boss['spawnY'] != 0): ?>
    <div class="field-group">
        <h3>Map Location</h3>
        <div class="preview-container">
            <div id="bossMap" style="width: 100%; height: 400px; position: relative; border: 1px solid var(--secondary); border-radius: 8px; background: var(--secondary);">
                <div style="position: absolute; left: calc(<?php echo min(max($boss['spawnX'], 0), 380); ?>px); top: calc(<?php echo min(max($boss['spawnY'], 0), 380); ?>px); transform: translate(-50%, -50%);">
                    <div style="width: 20px; height: 20px; background-color: var(--accent); border-radius: 50%; position: relative; box-shadow: 0 2px 8px rgba(253, 127, 68, 0.5);">
                        <span style="position: absolute; top: -30px; left: 50%; transform: translateX(-50%); white-space: nowrap; background: rgba(0,0,0,0.8); color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                            <?php echo htmlspecialchars($boss['name']); ?>
                        </span>
                    </div>
                </div>
                <div style="position: absolute; bottom: 10px; right: 15px; background: rgba(0,0,0,0.7); color: white; padding: 8px 12px; border-radius: 6px; font-size: 12px;">
                    X: <?php echo htmlspecialchars($boss['spawnX']); ?>, Y: <?php echo htmlspecialchars($boss['spawnY']); ?>
                </div>
            </div>
            <div class="text-muted" style="margin-top: 0.5rem; font-size: 0.85rem; opacity: 0.7;">
                <small>Note: This is a simplified representation. Actual in-game location may vary.</small>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
$db->close();
require_once('../../../includes/footer.php');
?>