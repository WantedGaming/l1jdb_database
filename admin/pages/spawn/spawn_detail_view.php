<?php
require_once __DIR__ . '/../../includes/header.php';

// Get the spawn ID and table from URL parameters
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$table = isset($_GET['table']) ? $_GET['table'] : 'spawnlist';

// Validate table name to prevent SQL injection
$valid_tables = ['spawnlist', 'spawnlist_boss', 'spawnlist_clandungeon', 'spawnlist_indun', 
                'spawnlist_other', 'spawnlist_ruun', 'spawnlist_ub', 'spawnlist_unicorntemple', 
                'spawnlist_worldwar'];

if (!in_array($table, $valid_tables)) {
    echo "<div class='admin-message admin-message-error'>Invalid table name</div>";
    exit;
}

// Initialize spawn data
$spawn = null;
$npc = null;

// If we have a valid ID, fetch the spawn data
if ($id > 0) {
    require_once __DIR__ . '/../../../includes/db.php';
    
    // Get spawn data
    $spawn_query = "SELECT s.*, n.desc_en as npc_name 
                    FROM $table s 
                    LEFT JOIN npc n ON s.npc_templateid = n.npcid 
                    WHERE s.id = ?";
    
    $stmt = $conn->prepare($spawn_query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $spawn = $result->fetch_assoc();
        
        // Get NPC data if template ID exists
        if (!empty($spawn['npc_templateid'])) {
            $npc_query = "SELECT * FROM npc WHERE npcid = ?";
            $stmt = $conn->prepare($npc_query);
            $stmt->bind_param('i', $spawn['npc_templateid']);
            $stmt->execute();
            $npc_result = $stmt->get_result();
            
            if ($npc_result->num_rows > 0) {
                $npc = $npc_result->fetch_assoc();
            }
        }
    } else {
        echo "<div class='admin-message admin-message-error'>Spawn not found</div>";
        exit;
    }
    
    $stmt->close();
}

// Get table display name
$table_display_names = [
    'spawnlist' => 'Regular Spawns',
    'spawnlist_boss' => 'Boss Spawns',
    'spawnlist_clandungeon' => 'Clan Dungeon Spawns',
    'spawnlist_indun' => 'Instance Dungeon Spawns',
    'spawnlist_other' => 'Other Spawns',
    'spawnlist_ruun' => 'Ruun Spawns',
    'spawnlist_ub' => 'Underground Battle Spawns',
    'spawnlist_unicorntemple' => 'Unicorn Temple Spawns',
    'spawnlist_worldwar' => 'World War Spawns'
];

$table_display_name = isset($table_display_names[$table]) ? $table_display_names[$table] : ucfirst(str_replace('_', ' ', $table));
?>

<div class="admin-content-wrapper">
    <div class="page-header">
        <div class="breadcrumb">
            <a href="/l1jdb_database/admin/">Dashboard</a> &raquo; 
            <a href="/l1jdb_database/admin/pages/spawn/">Spawns</a> &raquo; 
            <a href="/l1jdb_database/admin/pages/spawn/spawn_list_view.php?table=<?php echo urlencode($table); ?>">
                <?php echo htmlspecialchars($table_display_name); ?>
            </a> &raquo; 
            <span>Spawn Details</span>
        </div>
        <h1>
            <?php if (!empty($npc['spriteId'])): ?>
                <img src="/l1jdb_database/assets/img/icons/ms<?php echo $npc['spriteId']; ?>.png" 
                     onerror="this.src='/l1jdb_database/assets/img/icons/0.png';" 
                     alt="NPC Sprite" 
                     class="npc-detail-sprite">
            <?php endif; ?>
            <?php echo htmlspecialchars($spawn['name']); ?> 
            <small>(ID: <?php echo $spawn['id']; ?>)</small>
        </h1>
    </div>
    
    <div class="admin-btn-group">
        <a href="/l1jdb_database/admin/pages/spawn/spawn_list_view.php?table=<?php echo urlencode($table); ?>" class="admin-btn admin-btn-secondary">
            Back to List
        </a>
        <!-- Add edit/delete buttons here if needed -->
    </div>
    
    <div class="admin-form">
        <div class="form-tabs">
            <button class="form-tab active" data-tab="general-info">General Info</button>
            <button class="form-tab" data-tab="location-info">Location Details</button>
            <button class="form-tab" data-tab="respawn-info">Respawn Settings</button>
            <button class="form-tab" data-tab="npc-info">
                <?php if (!empty($npc['spriteId'])): ?>
                <img src="/l1jdb_database/assets/img/icons/ms<?php echo $npc['spriteId']; ?>.png" 
                     onerror="this.src='/l1jdb_database/assets/img/icons/0.png';" 
                     alt="NPC Sprite" 
                     class="tab-sprite">
                <?php endif; ?>
                NPC Information
            </button>
        </div>
        
        <!-- General Info Tab -->
        <div class="form-tab-content active" id="general-info">
            <div class="field-group">
                <h3>Basic Information</h3>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Spawn ID:</label>
                        <div class="form-control-static"><?php echo $spawn['id']; ?></div>
                    </div>
                    <div class="form-group">
                        <label>Spawn Name:</label>
                        <div class="form-control-static"><?php echo htmlspecialchars($spawn['name']); ?></div>
                    </div>
                    <div class="form-group">
                        <label>NPC Template ID:</label>
                        <div class="form-control-static"><?php echo $spawn['npc_templateid']; ?></div>
                    </div>
                    <div class="form-group">
                        <label>NPC Name:</label>
                        <div class="form-control-static">
                            <?php if (!empty($npc['spriteId'])): ?>
                                <img src="/l1jdb_database/assets/img/icons/ms<?php echo $npc['spriteId']; ?>.png" 
                                     onerror="this.src='/l1jdb_database/assets/img/icons/0.png';" 
                                     alt="NPC Sprite" 
                                     class="npc-sprite inline-sprite">
                            <?php endif; ?>
                            <?php echo htmlspecialchars($spawn['npc_name'] ?? 'Unknown NPC'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Spawn Count:</label>
                        <div class="form-control-static"><?php echo $spawn['count']; ?></div>
                    </div>
                    <div class="form-group">
                        <label>Group ID:</label>
                        <div class="form-control-static"><?php echo $spawn['group_id']; ?></div>
                    </div>
                </div>
            </div>
            
            <div class="field-group">
                <h3>Spawn Table Information</h3>
                <div class="form-group">
                    <label>Table Name:</label>
                    <div class="form-control-static"><?php echo htmlspecialchars($table); ?></div>
                </div>
                <div class="form-group">
                    <label>Table Type:</label>
                    <div class="form-control-static"><?php echo htmlspecialchars($table_display_name); ?></div>
                </div>
            </div>
        </div>
        
        <!-- Location Info Tab -->
        <div class="form-tab-content" id="location-info">
            <div class="field-group">
                <h3>Map Information</h3>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Map ID:</label>
                        <div class="form-control-static"><?php echo $spawn['mapid']; ?></div>
                    </div>
                    <div class="form-group">
                        <label>Movement Distance:</label>
                        <div class="form-control-static"><?php echo $spawn['movement_distance']; ?></div>
                    </div>
                </div>
            </div>
            
            <div class="field-group">
                <h3>Location Coordinates</h3>
                <div class="form-grid-3">
                    <div class="form-group">
                        <label>X Position:</label>
                        <div class="form-control-static"><?php echo $spawn['locx']; ?></div>
                    </div>
                    <div class="form-group">
                        <label>Y Position:</label>
                        <div class="form-control-static"><?php echo $spawn['locy']; ?></div>
                    </div>
                    <div class="form-group">
                        <label>Heading:</label>
                        <div class="form-control-static"><?php echo $spawn['heading']; ?></div>
                    </div>
                </div>
            </div>
            
            <div class="field-group">
                <h3>Random Spawn Area</h3>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Random X Range:</label>
                        <div class="form-control-static"><?php echo $spawn['randomx']; ?></div>
                    </div>
                    <div class="form-group">
                        <label>Random Y Range:</label>
                        <div class="form-control-static"><?php echo $spawn['randomy']; ?></div>
                    </div>
                </div>
            </div>
            
            <div class="field-group">
                <h3>Area Spawning (Min/Max Coordinates)</h3>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Min X (locx1):</label>
                        <div class="form-control-static"><?php echo $spawn['locx1']; ?></div>
                    </div>
                    <div class="form-group">
                        <label>Min Y (locy1):</label>
                        <div class="form-control-static"><?php echo $spawn['locy1']; ?></div>
                    </div>
                    <div class="form-group">
                        <label>Max X (locx2):</label>
                        <div class="form-control-static"><?php echo $spawn['locx2']; ?></div>
                    </div>
                    <div class="form-group">
                        <label>Max Y (locy2):</label>
                        <div class="form-control-static"><?php echo $spawn['locy2']; ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Respawn Info Tab -->
        <div class="form-tab-content" id="respawn-info">
            <div class="field-group">
                <h3>Respawn Settings</h3>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Min Respawn Delay (seconds):</label>
                        <div class="form-control-static"><?php echo $spawn['min_respawn_delay']; ?></div>
                    </div>
                    <div class="form-group">
                        <label>Max Respawn Delay (seconds):</label>
                        <div class="form-control-static"><?php echo $spawn['max_respawn_delay']; ?></div>
                    </div>
                </div>
            </div>
            
            <div class="field-group">
                <h3>Special Respawn Flags</h3>
                <div class="form-grid-3">
                    <div class="form-group">
                        <label>Respawn Screen:</label>
                        <div class="form-control-static">
                            <?php echo $spawn['respawn_screen'] ? 'Yes' : 'No'; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Rest:</label>
                        <div class="form-control-static">
                            <?php echo $spawn['rest'] ? 'Yes' : 'No'; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Near Spawn:</label>
                        <div class="form-control-static">
                            <?php echo $spawn['near_spawn'] ? 'Yes' : 'No'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- NPC Info Tab -->
        <div class="form-tab-content" id="npc-info">
            <?php if ($npc): ?>
                <div class="field-group">
                <h3>
                    <?php if (!empty($npc['spriteId'])): ?>
                    <img src="/l1jdb_database/assets/img/icons/ms<?php echo $npc['spriteId']; ?>.png" 
                         onerror="this.src='/l1jdb_database/assets/img/icons/0.png';" 
                         alt="NPC Sprite" 
                         class="npc-sprite inline-sprite">
                <?php endif; ?>
                NPC Basic Information
            </h3>
            <div class="form-grid-2">
                        <div class="form-group">
                            <label>NPC ID:</label>
                            <div class="form-control-static"><?php echo $npc['npcid']; ?></div>
                        </div>
                        <div class="form-group">
                            <label>Name:</label>
                            <div class="form-control-static"><?php echo htmlspecialchars($npc['desc_en']); ?></div>
                        </div>
                        <div class="form-group">
                            <label>Level:</label>
                            <div class="form-control-static"><?php echo $npc['level']; ?></div>
                        </div>
                        <div class="form-group">
                            <label>HP:</label>
                            <div class="form-control-static"><?php echo $npc['hp']; ?></div>
                        </div>
                        <div class="form-group">
                            <label>MP:</label>
                            <div class="form-control-static"><?php echo $npc['mp']; ?></div>
                        </div>
                        <div class="form-group">
                            <label>AC:</label>
                            <div class="form-control-static"><?php echo $npc['ac']; ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="field-group">
                    <h3>NPC Combat Stats</h3>
                    <div class="form-grid-3">
                        <div class="form-group">
                            <label>Strength:</label>
                            <div class="form-control-static"><?php echo $npc['str']; ?></div>
                        </div>
                        <div class="form-group">
                            <label>Dexterity:</label>
                            <div class="form-control-static"><?php echo $npc['dex']; ?></div>
                        </div>
                        <div class="form-group">
                            <label>Constitution:</label>
                            <div class="form-control-static"><?php echo $npc['con']; ?></div>
                        </div>
                        <div class="form-group">
                            <label>Wisdom:</label>
                            <div class="form-control-static"><?php echo $npc['wis']; ?></div>
                        </div>
                        <div class="form-group">
                            <label>Intelligence:</label>
                            <div class="form-control-static"><?php echo $npc['intel']; ?></div>
                        </div>
                        <div class="form-group">
                            <label>Charisma:</label>
                            <div class="form-control-static"><?php echo $npc['cha']; ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="field-group">
                    <h3>NPC Type Information</h3>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>NPC Type:</label>
                            <div class="form-control-static"><?php echo $npc['npc_type']; ?></div>
                        </div>
                        <div class="form-group">
                            <label>NPC Class:</label>
                            <div class="form-control-static"><?php echo $npc['classId']; ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="admin-btn-group">
                    <a href="/l1jdb_database/admin/pages/bin/npc_common/npc_detail_view.php?id=<?php echo $npc['npcid']; ?>" class="admin-btn admin-btn-primary">
                        View Full NPC Details
                    </a>
                </div>
            <?php else: ?>
                <div class="admin-message admin-message-warning">
                    NPC information not available. The NPC with ID <?php echo $spawn['npc_templateid']; ?> was not found in the database.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* NPC Sprite Styles */
.npc-detail-sprite {
    width: 48px;  /* Increased from 32px */
    height: 48px;  /* Increased from 32px */
    object-fit: contain;
    vertical-align: middle;
    margin-right: 10px;
    border-radius: 4px;
    background-color: rgba(0, 0, 0, 0.1);
}

.inline-sprite {
    width: 36px;  /* Increased from 24px */
    height: 36px;  /* Increased from 24px */
    margin-right: 8px;
    vertical-align: middle;
}

.tab-sprite {
    width: 28px;  /* Increased from 20px */
    height: 28px;  /* Increased from 20px */
    margin-right: 5px;
    vertical-align: middle;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    const tabs = document.querySelectorAll('.form-tab');
    const tabContents = document.querySelectorAll('.form-tab-content');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Remove active class from all tabs and contents
            tabs.forEach(t => t.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked tab and its content
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
