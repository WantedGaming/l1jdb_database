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

// Function to parse item strings and get item details
function parseItemData($itemString, $conn) {
    if (empty($itemString)) return [];
    
    $items = [];
    $lines = explode("\n", trim($itemString));
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        
        // Parse format: Name_ID:1028 Count:5 Slot:1 (example)
        preg_match_all('/(\w+):(\w+)/', $line, $matches, PREG_SET_ORDER);
        
        $item = [];
        foreach ($matches as $match) {
            $key = strtolower($match[1]);
            $value = $match[2];
            $item[$key] = $value;
        }
        
        if (isset($item['name_id'])) {
            // Get item details from bin_item_common
            $itemQuery = "
                SELECT 
                    bi.name_id,
                    bi.icon_id,
                    bi.desc_kr,
                    t.text_english as desc_en
                FROM bin_item_common bi
                LEFT JOIN 0_translations t ON bi.desc_kr = t.text_korean
                WHERE bi.name_id = ?
            ";
            
            $itemStmt = $conn->prepare($itemQuery);
            $itemStmt->bind_param('i', $item['name_id']);
            $itemStmt->execute();
            $itemResult = $itemStmt->get_result();
            $itemData = $itemResult->fetch_assoc();
            
            if ($itemData) {
                $item['item_name'] = !empty($itemData['desc_en']) ? $itemData['desc_en'] : $itemData['desc_kr'];
                $item['icon_id'] = $itemData['icon_id'];
            } else {
                $item['item_name'] = "Unknown Item (ID: {$item['name_id']})";
                $item['icon_id'] = 0;
            }
            
            $items[] = $item;
        }
    }
    
    return $items;
}

// Parse input and output items
$inputItems = parseItemData($craft['inputs_arr_input_item'], $conn);
$optionItems = parseItemData($craft['inputs_arr_option_item'], $conn);
$successItems = parseItemData($craft['outputs_success'], $conn);
$failureItems = parseItemData($craft['outputs_failure'], $conn);

// Helper functions from original file
function formatBoolean($value) {
    return $value === 'true' ? 'Yes' : 'No';
}

function formatAlignment($align) {
    if ($align == 0) return 'Neutral';
    if ($align > 0) return "Lawful ($align)";
    return "Chaotic ($align)";
}

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

function formatGender($gender) {
    switch ($gender) {
        case 0: return 'Any';
        case 1: return 'Male';
        case 2: return 'Female';
        default: return 'Unknown';
    }
}

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

<style>
.craft-visual-container {
    max-width: 1400px;
    margin: 0 auto;
}

.craft-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    border: 1px solid var(--accent);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}

.craft-title {
    color: var(--accent);
    font-size: 2rem;
    margin-bottom: 0.5rem;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.craft-subtitle {
    color: var(--text);
    opacity: 0.8;
    font-size: 1.1rem;
}

.visual-craft-flow {
    display: grid;
    grid-template-columns: 2fr auto 2fr;
    gap: 2rem;
    align-items: center;
    margin: 2rem 0;
    min-height: 400px;
}

.inputs-section, .outputs-section {
    background-color: var(--primary);
    border-radius: 12px;
    padding: 2rem;
    border: 2px solid var(--secondary);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.section-title {
    color: var(--accent);
    font-size: 1.3rem;
    margin-bottom: 1.5rem;
    text-align: center;
    border-bottom: 2px solid var(--accent);
    padding-bottom: 0.5rem;
}

.item-category {
    margin-bottom: 2rem;
}

.category-title {
    color: var(--text);
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 1rem;
    padding: 0.5rem 1rem;
    background-color: var(--secondary);
    border-radius: 6px;
    text-align: center;
}

.items-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.item-card {
    background-color: var(--secondary);
    border: 2px solid var(--primary);
    border-radius: 8px;
    padding: 1rem;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    min-height: 140px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.item-card:hover {
    transform: translateY(-2px);
    border-color: var(--accent);
    box-shadow: 0 4px 12px rgba(253, 127, 68, 0.3);
}

.item-icon {
    width: 48px;
    height: 48px;
    object-fit: contain;
    margin: 0 auto 0.5rem;
    border-radius: 4px;
    background-color: var(--primary);
    padding: 4px;
}

.item-icon-placeholder {
    width: 48px;
    height: 48px;
    background-color: var(--primary);
    border: 1px dashed rgba(255, 255, 255, 0.3);
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.5rem;
    color: var(--text);
    opacity: 0.5;
    font-size: 0.7rem;
}

.item-name {
    color: var(--text);
    font-size: 0.8rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
    line-height: 1.2;
    min-height: 2.4rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.item-count {
    background-color: var(--accent);
    color: white;
    font-size: 0.75rem;
    font-weight: bold;
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
    position: absolute;
    top: -8px;
    right: -8px;
    min-width: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.craft-arrow {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--accent);
    font-size: 3rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
}

.craft-info {
    text-align: center;
    margin-top: 1rem;
    color: var(--text);
    opacity: 0.8;
}

.success-rate {
    background: linear-gradient(45deg, #2ecc71, #27ae60);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: bold;
    margin-bottom: 0.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.empty-category {
    text-align: center;
    color: var(--text);
    opacity: 0.5;
    font-style: italic;
    padding: 2rem;
    background-color: var(--secondary);
    border: 1px dashed rgba(255, 255, 255, 0.3);
    border-radius: 8px;
}

.craft-details-tabs {
    background-color: var(--primary);
    border-radius: 12px;
    margin-top: 2rem;
    overflow: hidden;
    border: 1px solid var(--secondary);
}

.tab-nav {
    display: flex;
    background-color: var(--secondary);
    border-bottom: 1px solid var(--primary);
}

.tab-btn {
    flex: 1;
    padding: 1rem;
    background: none;
    border: none;
    color: var(--text);
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
}

.tab-btn:hover {
    background-color: rgba(253, 127, 68, 0.1);
}

.tab-btn.active {
    background-color: var(--accent);
    color: white;
}

.tab-content {
    padding: 2rem;
    display: none;
}

.tab-content.active {
    display: block;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.detail-item {
    background-color: var(--secondary);
    padding: 1rem;
    border-radius: 6px;
    border: 1px solid var(--primary);
}

.detail-item label {
    display: block;
    color: var(--accent);
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-item span {
    color: var(--text);
    font-size: 1rem;
    word-break: break-word;
}

.detail-text {
    background-color: var(--primary);
    padding: 1rem;
    border-radius: 4px;
    border: 1px solid var(--secondary);
    color: var(--text);
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
    white-space: pre-wrap;
    word-break: break-word;
    margin-top: 0.5rem;
}

@media (max-width: 1024px) {
    .visual-craft-flow {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .craft-arrow {
        transform: rotate(90deg);
        font-size: 2rem;
    }
}

@media (max-width: 768px) {
    .items-grid {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 0.5rem;
    }
    
    .item-card {
        padding: 0.75rem;
        min-height: 120px;
    }
    
    .item-icon, .item-icon-placeholder {
        width: 36px;
        height: 36px;
    }
    
    .craft-header {
        padding: 1.5rem;
    }
    
    .craft-title {
        font-size: 1.5rem;
    }
    
    .detail-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="page-header">
    <nav class="breadcrumb">
        <a href="../../index.php">Dashboard</a> → 
        <a href="../index.php">Binary Tables</a> → 
        <a href="craft_list_view.php">Craft Common List</a> → 
        <span>Visual Craft #<?php echo $craft_id; ?></span>
    </nav>
</div>

<div class="admin-actions">
    <a href="craft_list_view.php" class="admin-btn admin-btn-secondary">← Back to List</a>
    <a href="craft_detail_view.php?id=<?php echo $craft_id; ?>" class="admin-btn admin-btn-primary">View Details</a>
</div>

<div class="craft-visual-container">
    <!-- Craft Header -->
    <div class="craft-header">
        <h1 class="craft-title">
            <?php echo !empty($craft['desc_en']) ? htmlspecialchars($craft['desc_en']) : htmlspecialchars($craft['desc_kr']); ?>
        </h1>
        <p class="craft-subtitle">Craft ID: <?php echo $craft_id; ?> | Success Rate: <?php echo number_format($craft['outputs_success_prob_by_million'] / 10000, 2); ?>%</p>
    </div>

    <!-- Visual Craft Flow -->
    <div class="visual-craft-flow">
        <!-- Inputs Section -->
        <div class="inputs-section">
            <h2 class="section-title">Required Materials</h2>
            
            <?php if (!empty($inputItems)): ?>
            <div class="item-category">
                <h3 class="category-title">Input Items</h3>
                <div class="items-grid">
                    <?php foreach ($inputItems as $item): ?>
                    <div class="item-card" title="<?php echo htmlspecialchars($item['item_name']); ?> (ID: <?php echo $item['name_id']; ?>)">
                        <?php if (!empty($item['icon_id']) && file_exists(__DIR__ . '/../../../../assets/img/icons/' . $item['icon_id'] . '.png')): ?>
                            <img src="../../../../assets/img/icons/<?php echo $item['icon_id']; ?>.png" 
                                 alt="<?php echo htmlspecialchars($item['item_name']); ?>" 
                                 class="item-icon">
                        <?php else: ?>
                            <div class="item-icon-placeholder">No Icon</div>
                        <?php endif; ?>
                        
                        <div class="item-name"><?php echo htmlspecialchars($item['item_name']); ?></div>
                        
                        <?php if (isset($item['count'])): ?>
                            <span class="item-count"><?php echo $item['count']; ?></span>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($optionItems)): ?>
            <div class="item-category">
                <h3 class="category-title">Optional Items</h3>
                <div class="items-grid">
                    <?php foreach ($optionItems as $item): ?>
                    <div class="item-card" title="<?php echo htmlspecialchars($item['item_name']); ?> (ID: <?php echo $item['name_id']; ?>)">
                        <?php if (!empty($item['icon_id']) && file_exists(__DIR__ . '/../../../../assets/img/icons/' . $item['icon_id'] . '.png')): ?>
                            <img src="../../../../assets/img/icons/<?php echo $item['icon_id']; ?>.png" 
                                 alt="<?php echo htmlspecialchars($item['item_name']); ?>" 
                                 class="item-icon">
                        <?php else: ?>
                            <div class="item-icon-placeholder">No Icon</div>
                        <?php endif; ?>
                        
                        <div class="item-name"><?php echo htmlspecialchars($item['item_name']); ?></div>
                        
                        <?php if (isset($item['count'])): ?>
                            <span class="item-count"><?php echo $item['count']; ?></span>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (empty($inputItems) && empty($optionItems)): ?>
                <div class="empty-category">No input items required</div>
            <?php endif; ?>
        </div>

        <!-- Craft Arrow -->
        <div class="craft-arrow">
            <div>⚒️</div>
            <div class="craft-info">
                <div class="success-rate"><?php echo number_format($craft['outputs_success_prob_by_million'] / 10000, 2); ?>%</div>
                <div><?php echo $craft['batch_delay_sec']; ?>s</div>
            </div>
        </div>

        <!-- Outputs Section -->
        <div class="outputs-section">
            <h2 class="section-title">Craft Results</h2>
            
            <?php if (!empty($successItems)): ?>
            <div class="item-category">
                <h3 class="category-title">Success Results</h3>
                <div class="items-grid">
                    <?php foreach ($successItems as $item): ?>
                    <div class="item-card" title="<?php echo htmlspecialchars($item['item_name']); ?> (ID: <?php echo $item['name_id']; ?>)">
                        <?php if (!empty($item['icon_id']) && file_exists(__DIR__ . '/../../../../assets/img/icons/' . $item['icon_id'] . '.png')): ?>
                            <img src="../../../../assets/img/icons/<?php echo $item['icon_id']; ?>.png" 
                                 alt="<?php echo htmlspecialchars($item['item_name']); ?>" 
                                 class="item-icon">
                        <?php else: ?>
                            <div class="item-icon-placeholder">No Icon</div>
                        <?php endif; ?>
                        
                        <div class="item-name"><?php echo htmlspecialchars($item['item_name']); ?></div>
                        
                        <?php if (isset($item['count'])): ?>
                            <span class="item-count"><?php echo $item['count']; ?></span>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($failureItems)): ?>
            <div class="item-category">
                <h3 class="category-title">Failure Results</h3>
                <div class="items-grid">
                    <?php foreach ($failureItems as $item): ?>
                    <div class="item-card" title="<?php echo htmlspecialchars($item['item_name']); ?> (ID: <?php echo $item['name_id']; ?>)">
                        <?php if (!empty($item['icon_id']) && file_exists(__DIR__ . '/../../../../assets/img/icons/' . $item['icon_id'] . '.png')): ?>
                            <img src="../../../../assets/img/icons/<?php echo $item['icon_id']; ?>.png" 
                                 alt="<?php echo htmlspecialchars($item['item_name']); ?>" 
                                 class="item-icon">
                        <?php else: ?>
                            <div class="item-icon-placeholder">No Icon</div>
                        <?php endif; ?>
                        
                        <div class="item-name"><?php echo htmlspecialchars($item['item_name']); ?></div>
                        
                        <?php if (isset($item['count'])): ?>
                            <span class="item-count"><?php echo $item['count']; ?></span>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (empty($successItems) && empty($failureItems)): ?>
                <div class="empty-category">No output items defined</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Detailed Information Tabs -->
    <div class="craft-details-tabs">
        <div class="tab-nav">
            <button class="tab-btn active" onclick="switchTab(event, 'requirements')">Requirements</button>
            <button class="tab-btn" onclick="switchTab(event, 'configuration')">Configuration</button>
            <button class="tab-btn" onclick="switchTab(event, 'timing')">Success & Timing</button>
        </div>

        <div id="requirements" class="tab-content active">
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
        </div>

        <div id="configuration" class="tab-content">
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

        <div id="timing" class="tab-content">
            <div class="detail-grid">
                <div class="detail-item">
                    <label>Success Probability:</label>
                    <span><?php echo number_format($craft['outputs_success_prob_by_million'] / 10000, 2); ?>%</span>
                </div>
                <div class="detail-item">
                    <label>Batch Delay:</label>
                    <span><?php echo $craft['batch_delay_sec']; ?> seconds</span>
                </div>
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

            <?php if (!empty($craft['period_list'])): ?>
            <div class="detail-item">
                <label>Period List:</label>
                <div class="detail-text"><?php echo nl2br(htmlspecialchars($craft['period_list'])); ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function switchTab(evt, tabName) {
    var i, tabcontent, tabbtn;
    
    // Hide all tab content
    tabcontent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].classList.remove("active");
    }
    
    // Remove active class from all tab buttons
    tabbtn = document.getElementsByClassName("tab-btn");
    for (i = 0; i < tabbtn.length; i++) {
        tabbtn[i].classList.remove("active");
    }
    
    // Show the selected tab and mark button as active
    document.getElementById(tabName).classList.add("active");
    evt.currentTarget.classList.add("active");
}

// Add click handlers for item cards to show detailed info
document.addEventListener('DOMContentLoaded', function() {
    const itemCards = document.querySelectorAll('.item-card');
    
    itemCards.forEach(card => {
        card.addEventListener('click', function() {
            const title = this.getAttribute('title');
            if (title) {
                // Create a simple modal-like alert with item info
                const itemInfo = title;
                alert(itemInfo); // You can replace this with a more sophisticated modal
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
