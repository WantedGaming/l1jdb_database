<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Get list of all bin tables
$bin_tables = [
    'bin_armor_element_common' => 'Armor Element',
    'bin_catalyst_common' => 'Catalyst',
    'bin_companion_class_common' => 'Companion Class',
    'bin_companion_skill_common' => 'Companion Skill',
    'bin_craft_common' => 'Craft',
    'bin_einpoint_cost_common' => 'Einpoint Cost',
    'bin_einpoint_normal_prob_common' => 'Einpoint Normal Rate',
    'bin_einpoint_prob_table_common' => 'Einpoint Rate Table',
    'bin_einpoint_stat_common' => 'Einpoint Stat',
    'bin_element_enchant_common' => 'Element Enchant',
    'bin_enchant_scroll_table_common' => 'Enchant Scroll Table',
    'bin_enchant_table_common' => 'Enchant Table ',
    'bin_entermaps_common' => 'Enter Maps',
    'bin_favorbook_common' => 'Favorbook',
    'bin_general_goods_common' => 'General Goods',
    'bin_huntingquest_common' => 'Hunting Quest',
    'bin_item_common' => 'Item',
    'bin_ndl_common' => 'NDL',
    'bin_npc_common' => 'NPC',
    'bin_passivespell_common' => 'Passive Spell',
    'bin_potential_common' => 'Potential',
    'bin_spell_common' => 'Spell',
];

// Get statistics for each table
$table_stats = [];
foreach ($bin_tables as $table => $description) {
    try {
        $count_query = "SELECT COUNT(*) as count FROM $table";
        $count_result = $conn->query($count_query);
        if ($count_result) {
            $count_data = $count_result->fetch_assoc();
            $table_stats[$table] = $count_data['count'];
        } else {
            $table_stats[$table] = 0;
        }
    } catch (Exception $e) {
        $table_stats[$table] = 0;
    }
}
?>

<h1>Binary Data Tables</h1>
    
    <div class="admin-search">
        <input type="text" id="tableSearch" placeholder="Search tables..." autocomplete="off">
    </div>

    <div class="admin-stats">
        <div class="stat-card">
            <h3>Total Tables</h3>
            <div class="stat-number"><?php echo count($bin_tables); ?></div>
        </div>
        <div class="stat-card">
            <h3>Total Records</h3>
            <div class="stat-number"><?php echo number_format(array_sum($table_stats)); ?></div>
        </div>
    </div>

    <div class="bin-grid" id="binGrid">
        <?php foreach ($bin_tables as $table => $description): ?>
            <div class="bin-card" data-table="<?php echo strtolower($table); ?>" data-description="<?php echo strtolower($description); ?>">
                <h3><?php echo htmlspecialchars($description); ?></h3>
                <div class="table-name"><?php echo htmlspecialchars($table); ?></div>
                <div class="record-count">
                    <span>Records:</span>
                    <span class="count"><?php echo number_format($table_stats[$table]); ?></span>
                </div>
                <div class="actions">
                    <a href="view.php?table=<?php echo urlencode($table); ?>" class="admin-btn admin-btn-primary admin-btn-small">View Data</a>
                    <a href="detail.php?table=<?php echo urlencode($table); ?>" class="admin-btn admin-btn-secondary admin-btn-small">Details</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

<script>
document.getElementById('tableSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const cards = document.querySelectorAll('.bin-card');
    
    cards.forEach(card => {
        const tableName = card.getAttribute('data-table');
        const description = card.getAttribute('data-description');
        
        if (tableName.includes(searchTerm) || description.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
