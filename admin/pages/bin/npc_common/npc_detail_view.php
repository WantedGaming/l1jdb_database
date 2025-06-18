<?php
require_once __DIR__ . '/../common/detail_header.php';

// Get class_id from URL
$class_id = isset($_GET['id']) ? (int)$_GET['id'] : -1;

if ($class_id < 0) {
    echo "<div class='alert alert-error'>Invalid class ID provided.</div>";
    require_once __DIR__ . '/../common/detail_footer.php';
    exit;
}

// Main query to get NPC details with translation
$query = "
    SELECT 
        n.*,
        t.text_english as desc_en
    FROM bin_npc_common n
    LEFT JOIN 0_translations t ON n.desc_kr = t.text_korean
    WHERE n.class_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $class_id);
$stmt->execute();
$result = $stmt->get_result();
$npc = $result->fetch_assoc();

if (!$npc) {
    echo "<div class='alert alert-error'>NPC with Class ID $class_id not found.</div>";
    require_once __DIR__ . '/../common/detail_footer.php';
    exit;
}

// Helper function to format boolean values
function formatBoolean($value) {
    return $value === 'true' ? 'Yes' : 'No';
}

// Helper function to format alignment
function formatAlignment($align) {
    if ($align == 0) return 'Neutral (0)';
    if ($align > 0) return "Lawful ($align)";
    return "Chaotic ($align)";
}

// Helper function to format tendency
function formatTendency($tendency) {
    switch ($tendency) {
        case 'AGGRESSIVE(2)': return 'Aggressive';
        case 'PASSIVE(1)': return 'Passive';
        case 'NEUTRAL(0)': return 'Neutral';
        default: return $tendency;
    }
}

// Helper function to format category
function formatCategory($category) {
    $categories = [
        0 => 'General',
        1 => 'Undead',
        2 => 'Beast',
        3 => 'Plant',
        4 => 'Construct',
        5 => 'Elemental',
        6 => 'Dragon',
        7 => 'Demon',
        8 => 'Giant',
        9 => 'Angel'
    ];
    
    return isset($categories[$category]) ? $categories[$category] . " ($category)" : "Category $category";
}

// Helper function to format drop items
function formatDropItems($conn, $drop_items) {
    if (empty($drop_items)) return 'None';
    
    // Try to parse and format the drop items
    $lines = explode("\n", $drop_items);
    $formatted = [];
    $output = '';
    
    // Create a grid layout for drop items
    $output .= '<div class="dropped-by-grid">';
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line)) {
            // Try to extract item information
            // Format expected: {Item Name} - ID: {item_id} - Chance: {percentage}%
            if (preg_match('/(.+?)\s*-\s*ID:\s*(\d+)/', $line, $matches)) {
                $itemName = trim($matches[1]);
                $itemId = (int)$matches[2];
                
                // Get drop chance if available
                $dropChance = '';
                if (preg_match('/Chance:\s*([\d\.]+)%/', $line, $chanceMatches)) {
                    $dropChance = $chanceMatches[1] . '%';
                }
                
                // Find item details and images by checking in weapon, armor, and etcitem tables
                $itemInfo = getItemInfo($conn, $itemId);
                
                if ($itemInfo) {
                    $output .= createItemCard($itemInfo, $dropChance);
                } else {
                    // Fallback for items not found in database
                    $output .= '<div class="monster-card">';
                    $output .= '<div class="monster-info">';
                    $output .= '<div class="monster-image">';
                    $output .= '<img src="../../../../assets/img/placeholders/0.png" alt="' . htmlspecialchars($itemName) . '">';
                    $output .= '</div>';
                    $output .= '<div class="monster-details">';
                    $output .= '<h4>' . htmlspecialchars($itemName) . '</h4>';
                    $output .= '<div class="monster-level">Item ID: ' . $itemId . '</div>';
                    $output .= '</div></div>';
                    if (!empty($dropChance)) {
                        $output .= '<div class="drop-stats">';
                        $output .= '<div class="drop-stat">';
                        $output .= '<span class="drop-stat-label">Chance:</span>';
                        $output .= '<span class="drop-stat-value">' . $dropChance . '</span>';
                        $output .= '</div></div>';
                    }
                    $output .= '</div>';
                }
            } else {
                // For lines that don't match the expected format
                $formatted[] = htmlspecialchars($line);
            }
        }
    }
    
    // Close the grid
    $output .= '</div>';
    
    // Add any unformatted lines as plain text at the bottom if there are any
    if (!empty($formatted)) {
        $output .= '<div class="detail-text unformatted-drops">' . implode('<br>', $formatted) . '</div>';
    }
    
    return $output;
}

// Function to get item information
function getItemInfo($conn, $itemId) {
    // Check weapon table
    $query = "SELECT item_id, desc_en, iconId, 'weapon' as item_type FROM weapon WHERE item_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $itemId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    // Check armor table
    $query = "SELECT item_id, desc_en, iconId, 'armor' as item_type FROM armor WHERE item_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $itemId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    // Check etcitem table
    $query = "SELECT item_id, desc_en, iconId, 'etcitem' as item_type FROM etcitem WHERE item_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $itemId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

// Helper function to create an item card
function createItemCard($item, $dropChance = '') {
    $itemName = cleanDescriptionPrefix($item['desc_en']);
    $itemType = ucfirst($item['item_type']);
    
    // Determine the link to the item detail page
    $itemLink = '#';
    if ($item['item_type'] == 'weapon') {
        $itemLink = '../../../../pages/weapons/weapon_detail.php?id=' . $item['item_id'];
    } elseif ($item['item_type'] == 'armor') {
        $itemLink = '../../../../pages/armor/armor_detail.php?id=' . $item['item_id'];
    } elseif ($item['item_type'] == 'etcitem') {
        $itemLink = '../../../../pages/items/items_detail.php?id=' . $item['item_id'];
    }
    
    $output = '<div class="monster-card">';
    $output .= '<div class="monster-info">';
    $output .= '<div class="monster-image">';
    $output .= '<img src="../../../../assets/img/icons/' . $item['iconId'] . '.png" ';
    $output .= 'alt="' . htmlspecialchars($itemName) . '" ';
    $output .= 'onerror="this.src=\'../../../../assets/img/placeholders/0.png\'">';
    $output .= '</div>';
    $output .= '<div class="monster-details">';
    $output .= '<h4><a href="' . $itemLink . '" class="weapon-link" target="_blank">' . htmlspecialchars($itemName) . '</a></h4>';
    $output .= '<div class="monster-level">' . $itemType . ' (ID: ' . $item['item_id'] . ')</div>';
    $output .= '</div></div>';
    
    if (!empty($dropChance)) {
        $output .= '<div class="drop-stats">';
        $output .= '<div class="drop-stat">';
        $output .= '<span class="drop-stat-label">Chance:</span>';
        $output .= '<span class="drop-stat-value">' . $dropChance . '</span>';
        $output .= '</div></div>';
    }
    
    $output .= '</div>';
    
    return $output;
}

// Helper function to clean description prefix (similar to the one in monster_detail.php)
function cleanDescriptionPrefix($text) {
    // Remove common prefixes like $1, $2, etc.
    return preg_replace('/^\$\d+\s*/', '', $text);
}
?>

<div class="page-header">
    <nav class="breadcrumb">
        <a href="../../index.php">Dashboard</a> → 
        <a href="../index.php">Binary Tables</a> → 
        <a href="npc_list_view.php">NPC Common List</a> → 
        <span>NPC #<?php echo $class_id; ?></span>
    </nav>
    <h1>NPC Details - Class ID: <?php echo $class_id; ?></h1>
</div>

<div class="admin-actions">
    <a href="npc_list_view.php" class="admin-btn admin-btn-secondary">← Back to List</a>
</div>

<div class="detail-container">
    <!-- Main Content Row -->
    <div class="weapon-detail-row">
        <!-- Column 1: Image Preview -->
        <div class="weapon-image-col">
            <div class="weapon-image-container">
                <img src="../../../../assets/img/npc/<?php echo $npc['sprite_id']; ?>.png" 
                     alt="NPC Sprite" 
                     class="weapon-main-image"
                     onerror="this.src='../../../../assets/img/icons/ms<?php echo $npc['sprite_id']; ?>.png'; this.onerror=function(){this.src='../../../../assets/img/icons/ms<?php echo $npc['sprite_id']; ?>.gif'; this.onerror=function(){this.src='../../../../assets/img/npc/unknown.png';}}">
            </div>
            <div class="icon-id-display">
                <span>Sprite ID: <?php echo htmlspecialchars($npc['sprite_id']); ?></span>
            </div>
        </div>
        
        <!-- Column 2: Basic Information -->
        <div class="weapon-info-col">
            <div class="weapon-basic-info">
                <h2>Basic Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Class ID:</label>
                        <span><?php echo htmlspecialchars($npc['class_id']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>NPC ID:</label>
                        <span><?php echo htmlspecialchars($npc['npc_id']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>With Bin Spawn:</label>
                        <span><?php echo formatBoolean($npc['with_bin_spawn'] ? 'true' : 'false'); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Description ID:</label>
                        <span><?php echo htmlspecialchars($npc['desc_id']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Category:</label>
                        <span><?php echo formatCategory($npc['category']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="detail-section">
        <h2>Name & Description</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Name (Korean):</label>
                <span><?php echo htmlspecialchars($npc['desc_kr'] ?: 'None'); ?></span>
            </div>
            <div class="detail-item">
                <label>Name (English):</label>
                <span><?php echo htmlspecialchars($npc['desc_en'] ?: 'Not translated'); ?></span>
            </div>
        </div>
    </div>

    <div class="detail-section">
        <h2>Level & Stats</h2>
        <div class="detail-grid">
            <div class="detail-item stat-element" data-stat-type="level">
                <label>Level:</label>
                <span><?php echo htmlspecialchars($npc['level']); ?></span>
            </div>
            <div class="detail-item">
                <label>HP:</label>
                <span><?php echo number_format($npc['hp']); ?></span>
            </div>
            <div class="detail-item">
                <label>MP:</label>
                <span><?php echo number_format($npc['mp']); ?></span>
            </div>
            <div class="detail-item">
                <label>AC:</label>
                <span><?php echo htmlspecialchars($npc['ac']); ?></span>
            </div>
            <div class="detail-item">
                <label>Alignment:</label>
                <span><?php echo formatAlignment($npc['alignment']); ?></span>
            </div>
            <div class="detail-item">
                <label>Tendency:</label>
                <span><?php echo formatTendency($npc['tendency']); ?></span>
            </div>
        </div>
    </div>

    <div class="detail-section">
        <h2>Attributes</h2>
        <div class="detail-grid">
            <div class="detail-item stat-element" data-stat-type="str">
                <label>Strength:</label>
                <span><?php echo htmlspecialchars($npc['str']); ?></span>
            </div>
            <div class="detail-item stat-element" data-stat-type="con">
                <label>Constitution:</label>
                <span><?php echo htmlspecialchars($npc['con']); ?></span>
            </div>
            <div class="detail-item stat-element" data-stat-type="dex">
                <label>Dexterity:</label>
                <span><?php echo htmlspecialchars($npc['dex']); ?></span>
            </div>
            <div class="detail-item stat-element" data-stat-type="wis">
                <label>Wisdom:</label>
                <span><?php echo htmlspecialchars($npc['wis']); ?></span>
            </div>
            <div class="detail-item stat-element" data-stat-type="int">
                <label>Intelligence:</label>
                <span><?php echo htmlspecialchars($npc['inti']); ?></span>
            </div>
            <div class="detail-item stat-element" data-stat-type="cha">
                <label>Charisma:</label>
                <span><?php echo htmlspecialchars($npc['cha']); ?></span>
            </div>
        </div>
    </div>

    <div class="detail-section">
        <h2>Magic & Resistances</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Magic Resistance:</label>
                <span><?php echo htmlspecialchars($npc['mr']); ?></span>
            </div>
            <div class="detail-item">
                <label>Magic Level:</label>
                <span><?php echo htmlspecialchars($npc['magic_level']); ?></span>
            </div>
            <div class="detail-item">
                <label>Magic Bonus:</label>
                <span><?php echo htmlspecialchars($npc['magic_bonus']); ?></span>
            </div>
            <div class="detail-item">
                <label>Magic Evasion:</label>
                <span><?php echo htmlspecialchars($npc['magic_evasion']); ?></span>
            </div>
            <div class="detail-item">
                <label>Fire Resistance:</label>
                <span class="element-fire"><?php echo htmlspecialchars($npc['resistance_fire']); ?></span>
            </div>
            <div class="detail-item">
                <label>Water Resistance:</label>
                <span class="element-water"><?php echo htmlspecialchars($npc['resistance_water']); ?></span>
            </div>
            <div class="detail-item">
                <label>Air Resistance:</label>
                <span class="element-wind"><?php echo htmlspecialchars($npc['resistance_air']); ?></span>
            </div>
            <div class="detail-item">
                <label>Earth Resistance:</label>
                <span class="element-earth"><?php echo htmlspecialchars($npc['resistance_earth']); ?></span>
            </div>
        </div>
    </div>

    <div class="detail-section">
        <h2>Special Properties</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Big Size:</label>
                <span><?php echo formatBoolean($npc['big']); ?></span>
            </div>
            <div class="detail-item">
                <label>Boss Monster:</label>
                <span><?php echo formatBoolean($npc['is_bossmonster']); ?></span>
            </div>
            <div class="detail-item">
                <label>Can Turn Undead:</label>
                <span><?php echo formatBoolean($npc['can_turnundead']); ?></span>
            </div>
        </div>
    </div>

    <?php if (!empty($npc['drop_items'])): ?>
    <div class="detail-section">
        <h2>Drop Items</h2>
        <div class="detail-item">
            <label>Items:</label>
            <div class="detail-text"><?php echo formatDropItems($conn, $npc['drop_items']); ?></div>
        </div>
        
        <div class="detail-item">
            <label>Raw Drop Data:</label>
            <div class="detail-text" style="white-space: pre-wrap; word-wrap: break-word; overflow-wrap: break-word; max-width: 100%; font-family: monospace; padding: 10px; border-radius: 4px;"><?php echo htmlspecialchars($npc['drop_items']); ?></div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../common/detail_footer.php'; ?>
