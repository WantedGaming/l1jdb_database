<?php
require_once __DIR__ . '/../common/detail_header.php';

// Get name_id from URL
$name_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($name_id <= 0) {
    echo "<div class='alert alert-error'>Invalid item ID provided.</div>";
    require_once __DIR__ . '/../common/detail_footer.php';
    exit;
}

// Main query to get item details with translation
$query = "
    SELECT 
        i.*,
        t.text_english as desc_en
    FROM bin_item_common i
    LEFT JOIN 0_translations t ON i.desc_kr = t.text_korean
    WHERE i.name_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $name_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    echo "<div class='alert alert-error'>Item with ID $name_id not found.</div>";
    require_once __DIR__ . '/../common/detail_footer.php';
    exit;
}

// Helper function to format boolean values
function formatBoolean($value) {
    return $value === 'true' ? 'Yes' : 'No';
}

// Helper function to normalize enum text (remove parentheses content)
function normalizeEnumText($text) {
    if (empty($text) || $text === 'NONE(0)' || $text === 'NONE(-1)') return 'None';
    
    // Remove anything in parentheses and trim
    $normalized = preg_replace('/\([^)]*\)/', '', $text);
    $normalized = trim($normalized);
    $normalized = str_replace('_', ' ', $normalized);
    
    return ucwords(strtolower($normalized));
}

// Helper function to format class permissions
function formatClassPermissions($item) {
    $permissions = [];
    if ($item['prince_permit'] === 'true') $permissions[] = 'Prince';
    if ($item['knight_permit'] === 'true') $permissions[] = 'Knight';
    if ($item['elf_permit'] === 'true') $permissions[] = 'Elf';
    if ($item['magician_permit'] === 'true') $permissions[] = 'Magician';
    if ($item['darkelf_permit'] === 'true') $permissions[] = 'Dark Elf';
    if ($item['dragonknight_permit'] === 'true') $permissions[] = 'Dragon Knight';
    if ($item['illusionist_permit'] === 'true') $permissions[] = 'Illusionist';
    if ($item['warrior_permit'] === 'true') $permissions[] = 'Warrior';
    if ($item['fencer_permit'] === 'true') $permissions[] = 'Fencer';
    if ($item['lancer_permit'] === 'true') $permissions[] = 'Lancer';
    
    return !empty($permissions) ? implode(', ', $permissions) : 'None';
}

// Helper function to format weight
function formatWeight($weight, $isReal = false) {
    if ($weight == 0) return 'None';
    
    if ($isReal) {
        return number_format($weight / 1000, 2) . ' kg';
    } else {
        return number_format($weight / 1000, 2) . ' kg (per 1000 items)';
    }
}

// Helper function to format damage
function formatDamage($large, $small) {
    if ($large == 0 && $small == 0) return 'None';
    if ($large == $small) return $large;
    return "$small-$large";
}

// Helper function to format level range
function formatLevelRange($min, $max) {
    if ($min == 0 && $max == 0) return 'Any Level';
    if ($min == $max) return "Level $min";
    return "Level $min-$max";
}

// Helper function to format complex text fields
function formatComplexText($text) {
    if (empty($text)) return 'None';
    
    // Try to parse and format the text
    $lines = explode("\n", $text);
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
        <a href="item_list_view.php">Item Common List</a> → 
        <span>Item #<?php echo $name_id; ?></span>
    </nav>
    <h1>Item Details - ID: <?php echo $name_id; ?></h1>
</div>

<div class="admin-actions">
    <a href="item_list_view.php" class="admin-btn admin-btn-secondary">← Back to List</a>
</div>

<div class="detail-container">
    <!-- Main Content Row -->
    <div class="detail-content">
        <div class="flex flex-wrap">
            <!-- Column 1: Image Preview Card -->
            <div class="weapon-image-card">
                <div class="weapon-image-container">
                    <?php if ($item['icon_id'] > 0): ?>
                        <img src="../../../../assets/img/icons/<?php echo $item['icon_id']; ?>.png" 
                             alt="Item Icon" 
                             class="weapon-main-image"
                             onerror="this.src='../../../../assets/img/icons/0.png'">
                    <?php else: ?>
                        <img src="../../../../assets/img/icons/0.png" 
                             alt="No Icon" 
                             class="weapon-main-image">
                    <?php endif; ?>
                </div>
                <div class="icon-id-display mt-1 text-center">
                    <span>Icon ID: <?php echo htmlspecialchars($item['icon_id']); ?></span>
                </div>
            </div>
            
            <!-- Column 2: Basic Information -->
            <div class="weapon-info-wrap">
                <div class="weapon-basic-info">
                    <h2>Basic Information</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Name ID:</label>
                            <span><?php echo htmlspecialchars($item['name_id']); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Sprite ID:</label>
                            <span><?php echo htmlspecialchars($item['sprite_id']); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Description ID:</label>
                            <span><?php echo htmlspecialchars($item['desc_id']); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Real Description:</label>
                            <span><?php echo htmlspecialchars($item['real_desc'] ?: 'None'); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Material:</label>
                            <span><?php echo normalizeEnumText($item['material']); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Item Category:</label>
                            <span><?php echo normalizeEnumText($item['item_category']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <div class="detail-container">
        <div class="detail-content">
            <h2 class="section-title">Name & Description</h2>
            <div class="detail-grid">
                <div class="detail-item">
                    <label>Name (Korean):</label>
                    <span><?php echo htmlspecialchars($item['desc_kr'] ?: 'None'); ?></span>
                </div>
                <div class="detail-item">
                    <label>Name (English):</label>
                    <span><?php echo htmlspecialchars($item['desc_en'] ?: 'Not translated'); ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="detail-container">
        <div class="detail-content">
            <h2 class="section-title">Item Properties</h2>
            <div class="detail-grid">
                <div class="detail-item">
                    <label>Body Part:</label>
                    <span><?php echo normalizeEnumText($item['body_part']); ?></span>
                </div>
                <div class="detail-item">
                    <label>Level Range:</label>
                    <span><?php echo formatLevelRange($item['level_limit_min'], $item['level_limit_max']); ?></span>
                </div>
                <div class="detail-item">
                    <label>Weight (1000ea):</label>
                    <span><?php echo formatWeight($item['weight_1000ea']); ?></span>
                </div>
                <div class="detail-item">
                    <label>Real Weight:</label>
                    <span><?php echo formatWeight($item['real_weight'], true); ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="detail-container">
        <div class="detail-content">
            <h2 class="section-title">Class Permissions</h2>
            <div class="detail-grid">
                <div class="detail-item">
                    <label>Allowed Classes:</label>
                    <span><?php echo formatClassPermissions($item); ?></span>
                </div>
            </div>
            
            <div class="class-grid mt-2">
                <div class="class-item <?php echo $item['prince_permit'] === 'true' ? 'allowed' : 'restricted' ?>">
                    <span>Prince</span>
                    <span class="status"><?php echo $item['prince_permit'] === 'true' ? '✓' : '✗' ?></span>
                </div>
                <div class="class-item <?php echo $item['knight_permit'] === 'true' ? 'allowed' : 'restricted' ?>">
                    <span>Knight</span>
                    <span class="status"><?php echo $item['knight_permit'] === 'true' ? '✓' : '✗' ?></span>
                </div>
                <div class="class-item <?php echo $item['elf_permit'] === 'true' ? 'allowed' : 'restricted' ?>">
                    <span>Elf</span>
                    <span class="status"><?php echo $item['elf_permit'] === 'true' ? '✓' : '✗' ?></span>
                </div>
                <div class="class-item <?php echo $item['magician_permit'] === 'true' ? 'allowed' : 'restricted' ?>">
                    <span>Magician</span>
                    <span class="status"><?php echo $item['magician_permit'] === 'true' ? '✓' : '✗' ?></span>
                </div>
                <div class="class-item <?php echo $item['darkelf_permit'] === 'true' ? 'allowed' : 'restricted' ?>">
                    <span>Dark Elf</span>
                    <span class="status"><?php echo $item['darkelf_permit'] === 'true' ? '✓' : '✗' ?></span>
                </div>
                <div class="class-item <?php echo $item['dragonknight_permit'] === 'true' ? 'allowed' : 'restricted' ?>">
                    <span>Dragon Knight</span>
                    <span class="status"><?php echo $item['dragonknight_permit'] === 'true' ? '✓' : '✗' ?></span>
                </div>
                <div class="class-item <?php echo $item['illusionist_permit'] === 'true' ? 'allowed' : 'restricted' ?>">
                    <span>Illusionist</span>
                    <span class="status"><?php echo $item['illusionist_permit'] === 'true' ? '✓' : '✗' ?></span>
                </div>
                <div class="class-item <?php echo $item['warrior_permit'] === 'true' ? 'allowed' : 'restricted' ?>">
                    <span>Warrior</span>
                    <span class="status"><?php echo $item['warrior_permit'] === 'true' ? '✓' : '✗' ?></span>
                </div>
                <div class="class-item <?php echo $item['fencer_permit'] === 'true' ? 'allowed' : 'restricted' ?>">
                    <span>Fencer</span>
                    <span class="status"><?php echo $item['fencer_permit'] === 'true' ? '✓' : '✗' ?></span>
                </div>
                <div class="class-item <?php echo $item['lancer_permit'] === 'true' ? 'allowed' : 'restricted' ?>">
                    <span>Lancer</span>
                    <span class="status"><?php echo $item['lancer_permit'] === 'true' ? '✓' : '✗' ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="detail-container">
        <div class="detail-content">
            <h2 class="section-title">Combat Stats</h2>
            <div class="detail-grid">
                <div class="detail-item">
                    <label>AC:</label>
                    <span><?php echo htmlspecialchars($item['ac']); ?></span>
                </div>
                <div class="detail-item">
                    <label>Weapon Type:</label>
                    <span><?php echo normalizeEnumText($item['extended_weapon_type']); ?></span>
                </div>
                <div class="detail-item">
                    <label>Damage:</label>
                    <span><?php echo formatDamage($item['large_damage'], $item['small_damage']); ?></span>
                </div>
                <div class="detail-item">
                    <label>Hit Bonus:</label>
                    <span><?php echo htmlspecialchars($item['hit_bonus']); ?></span>
                </div>
                <div class="detail-item">
                    <label>Damage Bonus:</label>
                    <span><?php echo htmlspecialchars($item['damage_bonus']); ?></span>
                </div>
                <div class="detail-item">
                    <label>Spell Range:</label>
                    <span><?php echo htmlspecialchars($item['spell_range']); ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="detail-container">
        <div class="detail-content">
            <h2 class="section-title">Enchanting & Enhancement</h2>
            <div class="detail-grid">
                <div class="detail-item">
                    <label>Max Enchant:</label>
                    <span><?php echo htmlspecialchars($item['max_enchant']); ?></span>
                </div>
                <div class="detail-item">
                    <label>Enchant Type:</label>
                    <span><?php echo htmlspecialchars($item['enchant_type']); ?></span>
                </div>
                <div class="detail-item">
                    <label>Can Set Mage Enchant:</label>
                    <span><?php echo formatBoolean($item['can_set_mage_enchant']); ?></span>
                </div>
                <div class="detail-item">
                    <label>Element Enchant Table:</label>
                    <span><?php echo htmlspecialchars($item['element_enchant_table']); ?></span>
                </div>
                <div class="detail-item">
                    <label>Accessory Enchant Table:</label>
                    <span><?php echo htmlspecialchars($item['accessory_enchant_table']); ?></span>
                </div>
                <div class="detail-item">
                    <label>Forced Elemental Enchant:</label>
                    <span><?php echo htmlspecialchars($item['forced_elemental_enchant']); ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="detail-container">
        <div class="detail-content">
            <h2 class="section-title">Special Properties</h2>
            <div class="advanced-grid">
                <div class="advanced-item">
                    <label>Cost:</label>
                    <span><?php echo number_format($item['cost']); ?></span>
                </div>
                <div class="advanced-item">
                    <label>Merge:</label>
                    <span><?php echo formatBoolean($item['merge']); ?></span>
                </div>
                <div class="advanced-item">
                    <label>Is Elven:</label>
                    <span><?php echo formatBoolean($item['is_elven']); ?></span>
                </div>
                <div class="advanced-item">
                    <label>Energy Lost:</label>
                    <span><?php echo formatBoolean($item['energy_lost']); ?></span>
                </div>
                <div class="advanced-item">
                    <label>PSS Event Item:</label>
                    <span><?php echo formatBoolean($item['pss_event_item']); ?></span>
                </div>
                <div class="advanced-item">
                    <label>Market Searching Item:</label>
                    <span><?php echo formatBoolean($item['market_searching_item']); ?></span>
                </div>
                <div class="advanced-item">
                    <label>PSS Heal Item:</label>
                    <span><?php echo formatBoolean($item['pss_heal_item']); ?></span>
                </div>
                <div class="advanced-item">
                    <label>Interaction Type:</label>
                    <span><?php echo htmlspecialchars($item['interaction_type']); ?></span>
                </div>
                <div class="advanced-item">
                    <label>Probability:</label>
                    <span><?php echo htmlspecialchars($item['prob']); ?></span>
                </div>
                <div class="advanced-item">
                    <label>BM Prob Open:</label>
                    <span><?php echo htmlspecialchars($item['bm_prob_open']); ?></span>
                </div>
                <div class="advanced-item">
                    <label>Use Interval:</label>
                    <span><?php echo number_format($item['useInterval']); ?></span>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($item['equip_bonus_list'])): ?>
    <div class="detail-container">
        <div class="detail-content">
            <h2 class="section-title">Equipment Bonuses</h2>
            <div class="detail-item">
                <label>Bonus List:</label>
                <div class="description-text"><?php echo formatComplexText($item['equip_bonus_list']); ?></div>
            </div>
            
            <div class="detail-item mt-2">
                <label>Raw Bonus Data:</label>
                <div class="description-text" style="font-family: monospace;"><?php echo htmlspecialchars($item['equip_bonus_list']); ?></div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($item['armor_series_info'])): ?>
    <div class="detail-container">
        <div class="detail-content">
            <h2 class="section-title">Armor Series Information</h2>
            <div class="detail-item">
                <label>Series Info:</label>
                <div class="description-text"><?php echo formatComplexText($item['armor_series_info']); ?></div>
            </div>
            
            <div class="detail-item mt-2">
                <label>Raw Series Data:</label>
                <div class="description-text" style="font-family: monospace;"><?php echo htmlspecialchars($item['armor_series_info']); ?></div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($item['lucky_bag_reward_list'])): ?>
    <div class="detail-container">
        <div class="detail-content">
            <h2 class="section-title">Lucky Bag Rewards</h2>
            <div class="detail-item">
                <label>Reward List:</label>
                <div class="description-text"><?php echo formatComplexText($item['lucky_bag_reward_list']); ?></div>
            </div>
            
            <div class="detail-item mt-2">
                <label>Raw Reward Data:</label>
                <div class="description-text" style="font-family: monospace;"><?php echo htmlspecialchars($item['lucky_bag_reward_list']); ?></div>
            </div>
        </div>
    </div>
    <?php endif; ?>

<?php require_once __DIR__ . '/../common/detail_footer.php'; ?>
