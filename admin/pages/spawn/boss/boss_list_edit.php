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
    echo '<div class="admin-content-wrapper"><div class="admin-message admin-message-error">Invalid boss ID!</div>';
    echo '<a href="boss_list_view.php" class="admin-btn admin-btn-primary">Back to Boss List</a></div>';
    require_once('../../../includes/footer.php');
    exit;
}

$id = (int)$_GET['id'];
$db = getDbConnection();

$message = "";
$messageType = "";

// Get boss data
$query = "SELECT * FROM spawnlist_boss WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo '<div class="admin-content-wrapper"><div class="admin-message admin-message-warning">Boss not found!</div>';
    echo '<a href="boss_list_view.php" class="admin-btn admin-btn-primary">Back to Boss List</a></div>';
    require_once('../../../includes/footer.php');
    exit;
}

$boss = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $name = trim($_POST['name'] ?? '');
    $desc_kr = trim($_POST['desc_kr'] ?? '');
    $npcid = (int)($_POST['npcid'] ?? 0);
    $spawnDay = trim($_POST['spawnDay'] ?? '');
    $spawnTime = trim($_POST['spawnTime'] ?? '');
    $spawnX = (int)($_POST['spawnX'] ?? 0);
    $spawnY = (int)($_POST['spawnY'] ?? 0);
    $spawnMapId = (int)($_POST['spawnMapId'] ?? 0);
    $rndMinut = (int)($_POST['rndMinut'] ?? 0);
    $rndRange = (int)($_POST['rndRange'] ?? 0);
    $heading = (int)($_POST['heading'] ?? 0);
    $groupid = (int)($_POST['groupid'] ?? 0);
    $spawn_group_id = (int)($_POST['spawn_group_id'] ?? 0);
    $movementDistance = (int)($_POST['movementDistance'] ?? 0);
    $isYN = $_POST['isYN'] ?? 'false';
    $mentType = $_POST['mentType'] ?? 'NONE';
    $ment = trim($_POST['ment'] ?? '');
    $percent = (int)($_POST['percent'] ?? 0);
    $aliveSecond = (int)($_POST['aliveSecond'] ?? 0);
    $spawnType = $_POST['spawnType'] ?? 'NORMAL';
    
    // Validate required fields
    if (empty($name) || $npcid <= 0) {
        $message = "Name and NPC ID are required!";
        $messageType = "error";
    } elseif ($spawnX < -32768 || $spawnX > 32767 || $spawnY < -32768 || $spawnY > 32767) {
        $message = "Coordinates must be between -32768 and 32767!";
        $messageType = "error";
    } elseif ($heading < 0 || $heading > 7) {
        $message = "Heading must be between 0 and 7!";
        $messageType = "error";
    } elseif ($percent < 0 || $percent > 100) {
        $message = "Percent must be between 0 and 100!";
        $messageType = "error";
    } else {
        // Prepare and execute update statement
        $updateQuery = "UPDATE spawnlist_boss SET 
            name = ?, desc_kr = ?, npcid = ?, spawnDay = ?, spawnTime = ?, 
            spawnX = ?, spawnY = ?, spawnMapId = ?, rndMinut = ?, rndRange = ?, 
            heading = ?, groupid = ?, spawn_group_id = ?, movementDistance = ?, 
            isYN = ?, mentType = ?, ment = ?, percent = ?, aliveSecond = ?, spawnType = ? 
            WHERE id = ?";
        
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bind_param(
            "ssissiiiiiiiiissiisi", 
            $name, $desc_kr, $npcid, $spawnDay, $spawnTime, $spawnX, $spawnY, $spawnMapId,
            $rndMinut, $rndRange, $heading, $groupid, $spawn_group_id, $movementDistance,
            $isYN, $mentType, $ment, $percent, $aliveSecond, $spawnType, $id
        );
        
        if ($updateStmt->execute()) {
            $message = "Boss spawn updated successfully!";
            $messageType = "success";
            
            // Refresh boss data
            $stmt->execute();
            $result = $stmt->get_result();
            $boss = $result->fetch_assoc();
        } else {
            $message = "Error updating boss spawn: " . $updateStmt->error;
            $messageType = "error";
        }
    }
}

// Get available maps for dropdown (with comprehensive error handling)
$maps = [];
$mapColumnQueries = [
    "SELECT mapid, name FROM mapids ORDER BY mapid",
    "SELECT mapid, mapname FROM mapids ORDER BY mapid", 
    "SELECT mapid, desc_en FROM mapids ORDER BY mapid",
    "SELECT mapid, desc_kr FROM mapids ORDER BY mapid",
    "SELECT mapid, title FROM mapids ORDER BY mapid",
    "SELECT mapid, map_name FROM mapids ORDER BY mapid",
    "SELECT mapid, mapid as name FROM mapids ORDER BY mapid"
];

foreach ($mapColumnQueries as $query) {
    try {
        $mapsResult = $db->query($query);
        if ($mapsResult && $mapsResult->num_rows > 0) {
            while ($map = $mapsResult->fetch_assoc()) {
                $maps[] = $map;
            }
            break; // Found working query, stop trying
        }
    } catch (Exception $e) {
        // Continue to next query
        continue;
    }
}

// If no maps found, create a simple fallback
if (empty($maps)) {
    // Create some common map entries as fallback
    for ($i = 0; $i <= 10; $i++) {
        $maps[] = ['mapid' => $i, 'name' => "Map $i"];
    }
}
?>

<div class="admin-content-wrapper">
    <div class="page-header">
        <div class="breadcrumb">
            <a href="/l1jdb_database/admin/">Dashboard</a> &raquo; 
            <a href="boss_list_view.php">Boss Management</a> &raquo; 
            <span>Edit Boss</span>
        </div>
        <h1>Edit Boss Spawn: <?php echo htmlspecialchars($boss['name']); ?></h1>
    </div>
    
    <div class="admin-header-actions">
        <a href="boss_list_detail.php?id=<?php echo $id; ?>" class="admin-btn admin-btn-secondary">View Details</a>
        <a href="boss_list_view.php" class="admin-btn admin-btn-primary">Back to Boss List</a>
    </div>
    
    <?php if (!empty($message)): ?>
        <div class="admin-message admin-message-<?php echo $messageType; ?>"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <div class="admin-form">
        <form method="POST" action="">
            <div class="form-tab-content active">
                <div class="field-group">
                    <h3>Basic Information</h3>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="name">Name *</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($boss['name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="desc_kr">Description</label>
                            <input type="text" id="desc_kr" name="desc_kr" value="<?php echo htmlspecialchars($boss['desc_kr']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="npcid">NPC ID *</label>
                            <input type="number" id="npcid" name="npcid" value="<?php echo htmlspecialchars($boss['npcid']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="spawnType">Spawn Type</label>
                            <select id="spawnType" name="spawnType">
                                <option value="NORMAL" <?php echo $boss['spawnType'] == 'NORMAL' ? 'selected' : ''; ?>>NORMAL</option>
                                <option value="DOMINATION_TOWER" <?php echo $boss['spawnType'] == 'DOMINATION_TOWER' ? 'selected' : ''; ?>>DOMINATION_TOWER</option>
                                <option value="DRAGON_RAID" <?php echo $boss['spawnType'] == 'DRAGON_RAID' ? 'selected' : ''; ?>>DRAGON_RAID</option>
                                <option value="POISON_FEILD" <?php echo $boss['spawnType'] == 'POISON_FEILD' ? 'selected' : ''; ?>>POISON_FEILD</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="isYN">Is Active</label>
                            <select id="isYN" name="isYN">
                                <option value="false" <?php echo $boss['isYN'] == 'false' ? 'selected' : ''; ?>>Inactive</option>
                                <option value="true" <?php echo $boss['isYN'] == 'true' ? 'selected' : ''; ?>>Active</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="percent">Spawn Percent</label>
                            <input type="number" id="percent" name="percent" value="<?php echo htmlspecialchars($boss['percent']); ?>" min="0" max="100">
                            <small>Spawn chance percentage (0-100)</small>
                        </div>
                    </div>
                </div>
                
                <div class="field-group">
                    <h3>Location Information</h3>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="spawnMapId">Map ID</label>
                            <select id="spawnMapId" name="spawnMapId">
                                <option value="0">Select Map</option>
                                <?php foreach ($maps as $map): ?>
                                    <?php 
                                    $mapName = isset($map['name']) ? $map['name'] : 
                                              (isset($map['mapname']) ? $map['mapname'] : 
                                              (isset($map['desc_en']) ? $map['desc_en'] : 
                                              (isset($map['desc_kr']) ? $map['desc_kr'] : 
                                              (isset($map['title']) ? $map['title'] : 
                                              (isset($map['map_name']) ? $map['map_name'] : 
                                              "Map " . $map['mapid'])))));
                                    ?>
                                    <option value="<?php echo $map['mapid']; ?>" <?php echo $boss['spawnMapId'] == $map['mapid'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($mapName); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="spawnX">X Coordinate</label>
                            <input type="number" id="spawnX" name="spawnX" value="<?php echo htmlspecialchars($boss['spawnX']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="spawnY">Y Coordinate</label>
                            <input type="number" id="spawnY" name="spawnY" value="<?php echo htmlspecialchars($boss['spawnY']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="heading">Heading</label>
                            <input type="number" id="heading" name="heading" value="<?php echo htmlspecialchars($boss['heading']); ?>" min="0" max="7">
                            <small>Direction the boss will face (0-7)</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="movementDistance">Movement Distance</label>
                            <input type="number" id="movementDistance" name="movementDistance" value="<?php echo htmlspecialchars($boss['movementDistance']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="groupid">Group ID</label>
                            <input type="number" id="groupid" name="groupid" value="<?php echo htmlspecialchars($boss['groupid']); ?>">
                        </div>
                    </div>
                </div>
                
                <div class="field-group">
                    <h3>Timing Information</h3>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="spawnDay">Spawn Day</label>
                            <input type="text" id="spawnDay" name="spawnDay" value="<?php echo htmlspecialchars($boss['spawnDay'] ?? ''); ?>" placeholder="MON,TUE,WED...">
                            <small>Format: MON,TUE,WED,THU,FRI,SAT,SUN or leave empty for any day</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="spawnTime">Spawn Time</label>
                            <input type="text" id="spawnTime" name="spawnTime" value="<?php echo htmlspecialchars($boss['spawnTime'] ?? ''); ?>" placeholder="HH:MM">
                            <small>Format: HH:MM or leave empty for any time</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="rndMinut">Random Minutes</label>
                            <input type="number" id="rndMinut" name="rndMinut" value="<?php echo htmlspecialchars($boss['rndMinut']); ?>">
                            <small>Additional random minutes to add to spawn time</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="rndRange">Random Range</label>
                            <input type="number" id="rndRange" name="rndRange" value="<?php echo htmlspecialchars($boss['rndRange']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="aliveSecond">Alive Duration (seconds)</label>
                            <input type="number" id="aliveSecond" name="aliveSecond" value="<?php echo htmlspecialchars($boss['aliveSecond']); ?>">
                            <small>0 for indefinite</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="spawn_group_id">Spawn Group ID</label>
                            <input type="number" id="spawn_group_id" name="spawn_group_id" value="<?php echo htmlspecialchars($boss['spawn_group_id']); ?>">
                        </div>
                    </div>
                </div>
                
                <div class="field-group">
                    <h3>Notification Settings</h3>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="mentType">Mention Type</label>
                            <select id="mentType" name="mentType">
                                <option value="NONE" <?php echo $boss['mentType'] == 'NONE' ? 'selected' : ''; ?>>NONE</option>
                                <option value="WORLD" <?php echo $boss['mentType'] == 'WORLD' ? 'selected' : ''; ?>>WORLD</option>
                                <option value="MAP" <?php echo $boss['mentType'] == 'MAP' ? 'selected' : ''; ?>>MAP</option>
                                <option value="SCREEN" <?php echo $boss['mentType'] == 'SCREEN' ? 'selected' : ''; ?>>SCREEN</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="ment">Mention Message</label>
                            <input type="text" id="ment" name="ment" value="<?php echo htmlspecialchars($boss['ment']); ?>">
                            <small>Message to display when boss spawns</small>
                        </div>
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 2rem;">
                    <button type="submit" class="admin-btn admin-btn-primary admin-btn-large">Update Boss Spawn</button>
                    <a href="boss_list_detail.php?id=<?php echo $id; ?>" class="admin-btn admin-btn-secondary admin-btn-large" style="margin-left: 1rem;">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
$db->close();
require_once('../../../includes/footer.php');
?>
