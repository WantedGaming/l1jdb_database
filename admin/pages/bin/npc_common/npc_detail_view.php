<?php
require_once __DIR__ . '/../../../includes/auth_check.php';
require_once __DIR__ . '/../../../../includes/db.php';
require_once __DIR__ . '/../../../includes/header.php';

// Get class_id from URL
$class_id = isset($_GET['id']) ? (int)$_GET['id'] : -1;

if ($class_id < 0) {
    echo "<div class='alert alert-error'>Invalid class ID provided.</div>";
    require_once __DIR__ . '/../../../includes/footer.php';
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
    require_once __DIR__ . '/../../../includes/footer.php';
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
function formatDropItems($drop_items) {
    if (empty($drop_items)) return 'None';
    
    // Try to parse and format the drop items
    $lines = explode("\n", $drop_items);
    $formatted = [];
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line)) {
            $formatted[] = htmlspecialchars($line);
        }
    }
    
    return implode('<br>', $formatted);
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
    <div class="detail-section">
        <h2>Basic Information</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Class ID:</label>
                <span><?php echo htmlspecialchars($npc['class_id']); ?></span>
            </div>
            <div class="detail-item">
                <label>NPC ID:</label>
                <span><?php echo htmlspecialchars($npc['npc_id']); ?></span>
            </div>
            <div class="detail-item">
                <label>Sprite ID:</label>
                <span><?php echo htmlspecialchars($npc['sprite_id']); ?></span>
            </div>
            <div class="detail-item">
                <label>With Bin Spawn:</label>
                <span><?php echo formatBoolean($npc['with_bin_spawn'] ? 'true' : 'false'); ?></span>
            </div>
            <div class="detail-item">
                <label>Description ID:</label>
                <span><?php echo htmlspecialchars($npc['desc_id']); ?></span>
            </div>
            <div class="detail-item">
                <label>Category:</label>
                <span><?php echo formatCategory($npc['category']); ?></span>
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
            <div class="detail-item">
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
            <div class="detail-item">
                <label>Strength:</label>
                <span><?php echo htmlspecialchars($npc['str']); ?></span>
            </div>
            <div class="detail-item">
                <label>Constitution:</label>
                <span><?php echo htmlspecialchars($npc['con']); ?></span>
            </div>
            <div class="detail-item">
                <label>Dexterity:</label>
                <span><?php echo htmlspecialchars($npc['dex']); ?></span>
            </div>
            <div class="detail-item">
                <label>Wisdom:</label>
                <span><?php echo htmlspecialchars($npc['wis']); ?></span>
            </div>
            <div class="detail-item">
                <label>Intelligence:</label>
                <span><?php echo htmlspecialchars($npc['inti']); ?></span>
            </div>
            <div class="detail-item">
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
                <span><?php echo htmlspecialchars($npc['resistance_fire']); ?></span>
            </div>
            <div class="detail-item">
                <label>Water Resistance:</label>
                <span><?php echo htmlspecialchars($npc['resistance_water']); ?></span>
            </div>
            <div class="detail-item">
                <label>Air Resistance:</label>
                <span><?php echo htmlspecialchars($npc['resistance_air']); ?></span>
            </div>
            <div class="detail-item">
                <label>Earth Resistance:</label>
                <span><?php echo htmlspecialchars($npc['resistance_earth']); ?></span>
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
            <div class="detail-text"><?php echo formatDropItems($npc['drop_items']); ?></div>
        </div>
        
        <div class="detail-item">
            <label>Raw Drop Data:</label>
            <div class="detail-text" style="white-space: pre-wrap; word-wrap: break-word; overflow-wrap: break-word; max-width: 100%; font-family: monospace; padding: 10px; border-radius: 4px;"><?php echo htmlspecialchars($npc['drop_items']); ?></div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
