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

$db = getDbConnection();

$message = "";
$messageType = "";

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
        // Prepare and execute insert statement
        $query = "INSERT INTO spawnlist_boss (
            name, desc_kr, npcid, spawnDay, spawnTime, spawnX, spawnY, spawnMapId, 
            rndMinut, rndRange, heading, groupid, spawn_group_id, movementDistance, 
            isYN, mentType, ment, percent, aliveSecond, spawnType
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($query);
        $stmt->bind_param(
            "ssissiiiiiiiiissiii", 
            $name, $desc_kr, $npcid, $spawnDay, $spawnTime, $spawnX, $spawnY, $spawnMapId,
            $rndMinut, $rndRange, $heading, $groupid, $spawn_group_id, $movementDistance,
            $isYN, $mentType, $ment, $percent, $aliveSecond, $spawnType
        );
        
        if ($stmt->execute()) {
            $bossId = $db->insert_id;
            $message = "Boss spawn added successfully!";
            $messageType = "success";
            
            // Redirect to the detail page after successful insertion
            header("Location: boss_list_detail.php?id=$bossId");
            exit;
        } else {
            $message = "Error adding boss spawn: " . $stmt->error;
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
            <span>Add Boss</span>
        </div>
        <h1>Add New Boss Spawn</h1>
    </div>
    
    <div class="admin-header-actions">
        <a href="boss_list_view.php" class="admin-btn admin-btn-secondary">Back to Boss List</a>
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
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="desc_kr">Description</label>
                            <input type="text" id="desc_kr" name="desc_kr">
                        </div>
                        
                        <div class="form-group">
                            <label for="npcid">NPC ID *</label>
                            <input type="number" id="npcid" name="npcid" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="spawnType">Spawn Type</label>
                            <select id="spawnType" name="spawnType">
                                <option value="NORMAL">NORMAL</option>
                                <option value="DOMINATION_TOWER">DOMINATION_TOWER</option>
                                <option value="DRAGON_RAID">DRAGON_RAID</option>
                                <option value="POISON_FEILD">POISON_FEILD</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="isYN">Is Active</label>
                            <select id="isYN" name="isYN">
                                <option value="false">Inactive</option>
                                <option value="true">Active</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="percent">Spawn Percent</label>
                            <input type="number" id="percent" name="percent" value="100" min="0" max="100">
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
                                    <option value="<?php echo $map['mapid']; ?>"><?php echo htmlspecialchars($mapName); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="spawnX">X Coordinate</label>
                            <input type="number" id="spawnX" name="spawnX" value="0">
                        </div>
                        
                        <div class="form-group">
                            <label for="spawnY">Y Coordinate</label>
                            <input type="number" id="spawnY" name="spawnY" value="0">
                        </div>
                        
                        <div class="form-group">
                            <label for="heading">Heading</label>
                            <input type="number" id="heading" name="heading" value="0" min="0" max="7">
                            <small>Direction the boss will face (0-7)</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="movementDistance">Movement Distance</label>
                            <input type="number" id="movementDistance" name="movementDistance" value="0">
                        </div>
                        
                        <div class="form-group">
                            <label for="groupid">Group ID</label>
                            <input type="number" id="groupid" name="groupid" value="0">
                        </div>
                    </div>
                </div>
                
                <div class="field-group">
                    <h3>Timing Information</h3>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="spawnDay">Spawn Day</label>
                            <input type="text" id="spawnDay" name="spawnDay" placeholder="MON,TUE,WED...">
                            <small>Format: MON,TUE,WED,THU,FRI,SAT,SUN or leave empty for any day</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="spawnTime">Spawn Time</label>
                            <input type="text" id="spawnTime" name="spawnTime" placeholder="HH:MM">
                            <small>Format: HH:MM or leave empty for any time</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="rndMinut">Random Minutes</label>
                            <input type="number" id="rndMinut" name="rndMinut" value="0">
                            <small>Additional random minutes to add to spawn time</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="rndRange">Random Range</label>
                            <input type="number" id="rndRange" name="rndRange" value="0">
                        </div>
                        
                        <div class="form-group">
                            <label for="aliveSecond">Alive Duration (seconds)</label>
                            <input type="number" id="aliveSecond" name="aliveSecond" value="0">
                            <small>0 for indefinite</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="spawn_group_id">Spawn Group ID</label>
                            <input type="number" id="spawn_group_id" name="spawn_group_id" value="0">
                        </div>
                    </div>
                </div>
                
                <div class="field-group">
                    <h3>Notification Settings</h3>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="mentType">Mention Type</label>
                            <select id="mentType" name="mentType">
                                <option value="NONE">NONE</option>
                                <option value="WORLD">WORLD</option>
                                <option value="MAP">MAP</option>
                                <option value="SCREEN">SCREEN</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="ment">Mention Message</label>
                            <input type="text" id="ment" name="ment">
                            <small>Message to display when boss spawns</small>
                        </div>
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 2rem;">
                    <button type="submit" class="admin-btn admin-btn-primary admin-btn-large">Add Boss Spawn</button>
                    <a href="boss_list_view.php" class="admin-btn admin-btn-secondary admin-btn-large" style="margin-left: 1rem;">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
$db->close();
require_once('../../../includes/footer.php');
?>
