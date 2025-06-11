<?php
require_once __DIR__ . '/../../../includes/auth_check.php';
require_once __DIR__ . '/../../../../includes/db.php';
require_once __DIR__ . '/../../../includes/header.php';

// Get craft_id from URL
$craft_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($craft_id <= 0) {
    echo "<div class='alert alert-error'>Invalid craft ID provided.</div>";
    require_once __DIR__ . '/../../../includes/footer.php';
    exit;
}

// Main query to get craft details with translation
$query = "
    SELECT 
        c.*,
        t.text_english as desc_en
    FROM bin_craft_common c
    LEFT JOIN 0_translations t ON c.desc_kr = t.text_korean
    WHERE c.craft_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $craft_id);
$stmt->execute();
$result = $stmt->get_result();
$craft = $result->fetch_assoc();

if (!$craft) {
    echo "<div class='alert alert-error'>Craft with ID $craft_id not found.</div>";
    require_once __DIR__ . '/../../../includes/footer.php';
    exit;
}

// Helper function to format boolean values
function formatBoolean($value) {
    return $value === 'true' ? 'Yes' : 'No';
}

// Helper function to format alignment values
function formatAlignment($align) {
    if ($align == 0) return 'Neutral';
    if ($align > 0) return "Lawful ($align)";
    return "Chaotic ($align)";
}

// Helper function to format class requirements
function formatClassRequirements($classes) {
    if ($classes == 0) return 'All Classes';
    
    $class_names = [];
    if ($classes & 1) $class_names[] = 'Prince';
    if ($classes & 2) $class_names[] = 'Knight';
    if ($classes & 4) $class_names[] = 'Elf';
    if ($classes & 8) $class_names[] = 'Wizard';
    if ($classes & 16) $class_names[] = 'Dark Elf';
    if ($classes & 32) $class_names[] = 'Dragon Knight';
    if ($classes & 64) $class_names[] = 'Illusionist';
    if ($classes & 128) $class_names[] = 'Warrior';
    if ($classes & 256) $class_names[] = 'Fencer';
    if ($classes & 512) $class_names[] = 'Lancer';
    
    return !empty($class_names) ? implode(', ', $class_names) : 'Unknown';
}

// Helper function to format gender requirements
function formatGender($gender) {
    switch ($gender) {
        case 0: return 'Any';
        case 1: return 'Male';
        case 2: return 'Female';
        default: return 'Unknown';
    }
}

// Helper function to format success count type
function formatSuccessCountType($type) {
    switch ($type) {
        case 'World(0)': return 'World';
        case 'Account(1)': return 'Account';
        case 'Character(2)': return 'Character';
        case 'AllServers(3)': return 'All Servers';
        default: return $type;
    }
}
?>

<div class="page-header">
    <nav class="breadcrumb">
        <a href="../../index.php">Dashboard</a> → 
        <a href="../index.php">Binary Tables</a> → 
        <a href="craft_list_view.php">Craft Common List</a> → 
        <span>Craft #<?php echo $craft_id; ?></span>
    </nav>
    <h1>Craft Details - ID: <?php echo $craft_id; ?></h1>
</div>

<div class="admin-actions">
    <a href="craft_list_view.php" class="admin-btn admin-btn-secondary">← Back to List</a>
</div>

<div class="detail-container">
    <div class="detail-section">
        <h2>Basic Information</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Craft ID:</label>
                <span><?php echo htmlspecialchars($craft['craft_id']); ?></span>
            </div>
            <div class="detail-item">
                <label>Description ID:</label>
                <span><?php echo htmlspecialchars($craft['desc_id']); ?></span>
            </div>
            <div class="detail-item">
                <label>Description (Korean):</label>
                <span><?php echo htmlspecialchars($craft['desc_kr']); ?></span>
            </div>
            <div class="detail-item">
                <label>Description (English):</label>
                <span><?php echo htmlspecialchars($craft['desc_en'] ?: 'Not translated'); ?></span>
            </div>
        </div>
    </div>

    <div class="detail-section">
        <h2>Requirements</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Level Range:</label>
                <span><?php echo $craft['min_level']; ?> - <?php echo $craft['max_level']; ?></span>
            </div>
            <div class="detail-item">
                <label>Required Gender:</label>
                <span><?php echo formatGender($craft['required_gender']); ?></span>
            </div>
            <div class="detail-item">
                <label>Alignment Range:</label>
                <span><?php echo formatAlignment($craft['min_align']); ?> to <?php echo formatAlignment($craft['max_align']); ?></span>
            </div>
            <div class="detail-item">
                <label>Karma Range:</label>
                <span><?php echo number_format($craft['min_karma']); ?> - <?php echo number_format($craft['max_karma']); ?></span>
            </div>
            <div class="detail-item">
                <label>Required Classes:</label>
                <span><?php echo formatClassRequirements($craft['required_classes']); ?></span>
            </div>
        </div>
        
        <?php if (!empty($craft['required_quests'])): ?>
        <div class="detail-item">
            <label>Required Quests:</label>
            <div class="detail-text"><?php echo nl2br(htmlspecialchars($craft['required_quests'])); ?></div>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($craft['required_sprites'])): ?>
        <div class="detail-item">
            <label>Required Sprites:</label>
            <div class="detail-text"><?php echo nl2br(htmlspecialchars($craft['required_sprites'])); ?></div>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($craft['required_items'])): ?>
        <div class="detail-item">
            <label>Required Items:</label>
            <div class="detail-text"><?php echo nl2br(htmlspecialchars($craft['required_items'])); ?></div>
        </div>
        <?php endif; ?>
    </div>

    <div class="detail-section">
        <h2>Craft Configuration</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Max Count:</label>
                <span><?php echo number_format($craft['max_count']); ?></span>
            </div>
            <div class="detail-item">
                <label>Show in List:</label>
                <span><?php echo formatBoolean($craft['is_show']); ?></span>
            </div>
            <div class="detail-item">
                <label>PC Cafe Only:</label>
                <span><?php echo formatBoolean($craft['PCCafeOnly']); ?></span>
            </div>
            <div class="detail-item">
                <label>BM Prob Open:</label>
                <span><?php echo formatBoolean($craft['bmProbOpen']); ?></span>
            </div>
            <div class="detail-item">
                <label>Except NPC:</label>
                <span><?php echo formatBoolean($craft['except_npc']); ?></span>
            </div>
        </div>
    </div>

    <div class="detail-section">
        <h2>Input & Output Items</h2>
        
        <?php if (!empty($craft['inputs_arr_input_item'])): ?>
        <div class="detail-item">
            <label>Input Items:</label>
            <div class="detail-text"><?php echo nl2br(htmlspecialchars($craft['inputs_arr_input_item'])); ?></div>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($craft['inputs_arr_option_item'])): ?>
        <div class="detail-item">
            <label>Option Items:</label>
            <div class="detail-text"><?php echo nl2br(htmlspecialchars($craft['inputs_arr_option_item'])); ?></div>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($craft['outputs_success'])): ?>
        <div class="detail-item">
            <label>Success Outputs:</label>
            <div class="detail-text"><?php echo nl2br(htmlspecialchars($craft['outputs_success'])); ?></div>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($craft['outputs_failure'])): ?>
        <div class="detail-item">
            <label>Failure Outputs:</label>
            <div class="detail-text"><?php echo nl2br(htmlspecialchars($craft['outputs_failure'])); ?></div>
        </div>
        <?php endif; ?>
    </div>

    <div class="detail-section">
        <h2>Success & Timing</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Success Probability:</label>
                <span><?php echo number_format($craft['outputs_success_prob_by_million'] / 10000, 2); ?>%</span>
            </div>
            <div class="detail-item">
                <label>Batch Delay:</label>
                <span><?php echo $craft['batch_delay_sec']; ?> seconds</span>
            </div>
        </div>
        
        <?php if (!empty($craft['period_list'])): ?>
        <div class="detail-item">
            <label>Period List:</label>
            <div class="detail-text"><?php echo nl2br(htmlspecialchars($craft['period_list'])); ?></div>
        </div>
        <?php endif; ?>
    </div>

    <div class="detail-section">
        <h2>Success Counting</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Current Success Count:</label>
                <span><?php echo number_format($craft['cur_successcount']); ?></span>
            </div>
            <div class="detail-item">
                <label>Max Success Count:</label>
                <span><?php echo number_format($craft['max_successcount']); ?></span>
            </div>
            <div class="detail-item">
                <label>Success Count Type:</label>
                <span><?php echo formatSuccessCountType($craft['SuccessCountType']); ?></span>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
