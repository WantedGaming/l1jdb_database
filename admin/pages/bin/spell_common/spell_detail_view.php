<?php
require_once __DIR__ . '/../common/detail_header.php';

// Get spell_id from URL
$spell_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($spell_id <= 0) {
    echo "<div class='alert alert-error'>Invalid spell ID provided.</div>";
    require_once __DIR__ . '/../common/detail_footer.php';
    exit;
}

// Main query to get spell details with translation
$query = "
    SELECT 
        s.*,
        t.text_english as tooltip_en
    FROM bin_spell_common s
    LEFT JOIN 0_translations t ON s.tooltip_str_kr = t.text_korean
    WHERE s.spell_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $spell_id);
$stmt->execute();
$result = $stmt->get_result();
$spell = $result->fetch_assoc();

if (!$spell) {
    echo "<div class='alert alert-error'>Spell with ID $spell_id not found.</div>";
    require_once __DIR__ . '/../common/detail_footer.php';
    exit;
}

// Helper function to format spell category
function formatSpellCategory($category) {
    switch ($category) {
        case 'COMPANION_SPELL_BUFF(2)': return 'Companion Spell Buff';
        case 'SPELL_BUFF(1)': return 'Spell Buff';
        case 'SPELL(0)': return 'Spell';
        default: return $category;
    }
}

// Helper function to format duration
function formatDuration($duration) {
    if ($duration == 0) return 'Instant';
    if ($duration < 60) return $duration . ' seconds';
    if ($duration < 3600) return round($duration / 60, 1) . ' minutes';
    return round($duration / 3600, 1) . ' hours';
}

// Helper function to format boolean values
function formatBoolean($value) {
    return $value == 1 ? 'Yes' : 'No';
}

// Helper function to format duration show type
function formatDurationShowType($type) {
    switch ($type) {
        case 0: return 'Normal';
        case 1: return 'Hide Duration';
        case 2: return 'Show Remaining';
        default: return "Type $type";
    }
}
?>

<div class="page-header">
    <nav class="breadcrumb">
        <a href="../../index.php">Dashboard</a> → 
        <a href="../index.php">Binary Tables</a> → 
        <a href="spell_list_view.php">Spell Common List</a> → 
        <span>Spell #<?php echo $spell_id; ?></span>
    </nav>
    <h1>Spell Details - ID: <?php echo $spell_id; ?></h1>
</div>

<div class="admin-actions">
    <a href="spell_list_view.php" class="admin-btn admin-btn-secondary">← Back to List</a>
</div>

<div class="detail-container">
    <!-- Main Content Row -->
    <div class="weapon-detail-row">
        <!-- Column 1: Image Preview -->
        <div class="weapon-image-col">
            <div class="weapon-image-container">
                <?php if ($spell['on_icon_id'] > 0): ?>
                    <img src="../../../../assets/img/icons/<?php echo $spell['on_icon_id']; ?>.png" 
                         alt="Spell Icon" 
                         class="weapon-main-image"
                         onerror="this.src='../../../../assets/img/icons/0.png'">
                <?php else: ?>
                    <img src="../../../../assets/img/icons/0.png" 
                         alt="No Icon" 
                         class="weapon-main-image">
                <?php endif; ?>
            </div>
            <div class="icon-id-display">
                <span>On Icon ID: <?php echo htmlspecialchars($spell['on_icon_id']); ?></span>
                <?php if ($spell['off_icon_id'] > 0): ?>
                    <span>Off Icon ID: <?php echo htmlspecialchars($spell['off_icon_id']); ?></span>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Column 2: Basic Information -->
        <div class="weapon-info-col">
            <div class="weapon-basic-info">
                <h2>Basic Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Spell ID:</label>
                        <span><?php echo htmlspecialchars($spell['spell_id']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Spell Category:</label>
                        <span><?php echo formatSpellCategory($spell['spell_category']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Duration:</label>
                        <span><?php echo formatDuration($spell['duration']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Delay Group ID:</label>
                        <span><?php echo htmlspecialchars($spell['delay_group_id']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Tooltip String ID:</label>
                        <span><?php echo htmlspecialchars($spell['tooltip_str_id']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Extract Item Name ID:</label>
                        <span><?php echo htmlspecialchars($spell['extract_item_name_id']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="detail-section">
        <h2>Tooltip Information</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Tooltip (Korean):</label>
                <span><?php echo htmlspecialchars($spell['tooltip_str_kr'] ?: 'None'); ?></span>
            </div>
            <div class="detail-item">
                <label>Tooltip (English):</label>
                <span><?php echo htmlspecialchars($spell['tooltip_en'] ?: 'Not translated'); ?></span>
            </div>
            <div class="detail-item">
                <label>Extract Item Count:</label>
                <span><?php echo number_format($spell['extract_item_count']); ?></span>
            </div>
        </div>
        
        <?php if (!empty($spell['spell_bonus_list'])): ?>
        <div class="detail-item">
            <label>Spell Bonus List:</label>
            <div class="detail-text"><?php echo nl2br(htmlspecialchars($spell['spell_bonus_list'])); ?></div>
        </div>
        <?php endif; ?>
    </div>

    <div class="detail-section">
        <h2>Companion Settings</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Companion On Icon ID:</label>
                <span>
                    <?php echo htmlspecialchars($spell['companion_on_icon_id']); ?>
                    <?php if ($spell['companion_on_icon_id'] > 0): ?>
                        <br><img src="../../../../assets/img/icons/<?php echo $spell['companion_on_icon_id']; ?>.png" 
                                 alt="Companion On Icon" width="32" height="32"
                                 onerror="this.src='../../../../assets/img/icons/0.png'">
                    <?php endif; ?>
                </span>
            </div>
            <div class="detail-item">
                <label>Companion Off Icon ID:</label>
                <span>
                    <?php echo htmlspecialchars($spell['companion_off_icon_id']); ?>
                    <?php if ($spell['companion_off_icon_id'] > 0): ?>
                        <br><img src="../../../../assets/img/icons/<?php echo $spell['companion_off_icon_id']; ?>.png" 
                                 alt="Companion Off Icon" width="32" height="32"
                                 onerror="this.src='../../../../assets/img/icons/0.png'">
                    <?php endif; ?>
                </span>
            </div>
            <div class="detail-item">
                <label>Icon Priority:</label>
                <span><?php echo htmlspecialchars($spell['companion_icon_priority']); ?></span>
            </div>
            <div class="detail-item">
                <label>Is Good Spell:</label>
                <span><?php echo formatBoolean($spell['companion_is_good']); ?></span>
            </div>
            <div class="detail-item">
                <label>Duration Show Type:</label>
                <span><?php echo formatDurationShowType($spell['companion_duration_show_type']); ?></span>
            </div>
        </div>
    </div>

    <div class="detail-section">
        <h2>Companion Tooltip IDs</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Companion Tooltip String ID:</label>
                <span><?php echo htmlspecialchars($spell['companion_tooltip_str_id']); ?></span>
            </div>
            <div class="detail-item">
                <label>Companion New String ID:</label>
                <span><?php echo htmlspecialchars($spell['companion_new_str_id']); ?></span>
            </div>
            <div class="detail-item">
                <label>Companion End String ID:</label>
                <span><?php echo htmlspecialchars($spell['companion_end_str_id']); ?></span>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../common/detail_footer.php'; ?>
