<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Get list of all bin tables
$bin_tables = [
    'bin_catalyst_common' => 'Catalyst',
    'bin_craft_common' => 'Craft',
    'bin_entermaps_common' => 'Enter Maps',
    'bin_item_common' => 'Item',
    'bin_ndl_common' => 'NDL',
    'bin_npc_common' => 'NPC',
    'bin_potential_common' => 'Potential',
    'bin_spell_common' => 'Spell',
    'spawnlist' => 'Spawns',
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
                    <?php if ($table === 'bin_catalyst_common'): ?>
                        <a href="catalyst/catalyst_list_view.php" class="admin-btn admin-btn-primary admin-btn-small">View Data</a>
                    <?php elseif ($table === 'bin_craft_common'): ?>
                        <a href="craft_common/craft_list_view.php" class="admin-btn admin-btn-primary admin-btn-small">View Data</a>
                    <?php elseif ($table === 'bin_enchant_scroll_table_common'): ?>
                        <a href="enchant_scroll_table_common/enchant_scroll_list_view.php" class="admin-btn admin-btn-primary admin-btn-small">View Data</a>
                    <?php elseif ($table === 'bin_entermaps_common'): ?>
                        <a href="entermaps_common/entermaps_list_view.php" class="admin-btn admin-btn-primary admin-btn-small">View Data</a>
                    <?php elseif ($table === 'bin_spell_common'): ?>
                        <a href="spell_common/spell_list_view.php" class="admin-btn admin-btn-primary admin-btn-small">View Data</a>
                    <?php elseif ($table === 'bin_potential_common'): ?>
                        <a href="potential_common/potential_list_view.php" class="admin-btn admin-btn-primary admin-btn-small">View Data</a>
                    <?php elseif ($table === 'bin_npc_common'): ?>
                        <a href="npc_common/npc_list_view.php" class="admin-btn admin-btn-primary admin-btn-small">View Data</a>
                    <?php elseif ($table === 'bin_ndl_common'): ?>
                        <a href="ndl_common/ndl_list_view.php" class="admin-btn admin-btn-primary admin-btn-small">View Data</a>
                    <?php elseif ($table === 'bin_item_common'): ?>
                        <a href="item_common/item_list_view.php" class="admin-btn admin-btn-primary admin-btn-small">View Data</a>
                    <?php elseif ($table === 'spawnlist'): ?>
                        <a href="spawn_common/spawn_list_view.php" class="admin-btn admin-btn-primary admin-btn-small">View Data</a>
                    <?php else: ?>
                        <a href="view.php?table=<?php echo urlencode($table); ?>" class="admin-btn admin-btn-primary admin-btn-small">View Data</a>
                    <?php endif; ?>
                    <a href="detail.php?table=<?php echo urlencode($table); ?>" class="admin-btn admin-btn-secondary admin-btn-small">Details</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
