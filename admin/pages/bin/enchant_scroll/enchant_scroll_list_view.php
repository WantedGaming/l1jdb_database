<?php
require_once __DIR__ . '/../../../includes/auth_check.php';
require_once __DIR__ . '/../../../../includes/db.php';
require_once __DIR__ . '/../../../includes/header.php';

// Pagination settings
$records_per_page = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Get total count for pagination
$count_query = "SELECT COUNT(*) as total FROM bin_enchant_scroll_table_common";
$count_result = $conn->query($count_query);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Main query with translations
$query = "
    SELECT 
        e.enchantType,
        e.nameid,
        e.desc_kr,
        e.targetEnchant,
        e.noTargetMaterialList,
        e.target_category,
        e.isBmEnchantScroll,
        e.elementalType,
        e.useBlesscodeScroll,
        t.text_english as desc_en
    FROM bin_enchant_scroll_table_common e
    LEFT JOIN 0_translations t ON e.desc_kr = t.text_korean
    ORDER BY e.enchantType, e.nameid
    LIMIT $records_per_page OFFSET $offset
";

$result = $conn->query($query);

// Helper function to format target category
function formatTargetCategory($category) {
    switch($category) {
        case 'NONE(0)': return 'None';
        case 'WEAPON(1)': return 'Weapon';
        case 'ARMOR(2)': return 'Armor';
        case 'ACCESSORY(3)': return 'Accessory';
        case 'ELEMENT(4)': return 'Element';
        default: return $category;
    }
}

// Helper function to format boolean values
function formatBoolean($value) {
    return $value === 'true' ? 'Yes' : 'No';
}

// Helper function to format elemental type
function formatElementalType($type) {
    switch($type) {
        case 0: return 'None';
        case 1: return 'Fire';
        case 2: return 'Water';
        case 4: return 'Air';
        case 8: return 'Earth';
        default: return "Type $type";
    }
}
?>

<div class="page-header">
    <nav class="breadcrumb">
        <a href="../../index.php">Dashboard</a> → 
        <a href="../index.php">Binary Tables</a> → 
        <span>Enchant Scroll Table List</span>
    </nav>
    <h1>Enchant Scroll Table List</h1>
</div>

<div class="admin-stats">
    <div class="stat-card">
        <h3>Total Records</h3>
        <div class="stat-number"><?php echo number_format($total_records); ?></div>
    </div>
    <div class="stat-card">
        <h3>Current Page</h3>
        <div class="stat-number"><?php echo $page; ?> of <?php echo $total_pages; ?></div>
    </div>
</div>

<div class="table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Enchant Type</th>
                <th>Name ID</th>
                <th>Description</th>
                <th>Target Enchant</th>
                <th>Target Category</th>
                <th>BM Scroll</th>
                <th>Elemental Type</th>
                <th>Use Blesscode</th>
                <th>No Target Materials</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="table-cell-number"><?php echo htmlspecialchars($row['enchantType']); ?></td>
                        <td class="table-cell-id"><?php echo htmlspecialchars($row['nameid']); ?></td>
                        <td class="table-cell-name">
                            <?php 
                            // Display English translation if available, otherwise Korean
                            if (!empty($row['desc_en'])) {
                                echo htmlspecialchars($row['desc_en']);
                            } else {
                                echo htmlspecialchars($row['desc_kr']);
                            }
                            ?>
                        </td>
                        <td class="table-cell-number"><?php echo htmlspecialchars($row['targetEnchant']); ?></td>
                        <td><?php echo formatTargetCategory($row['target_category']); ?></td>
                        <td class="table-cell-boolean">
                            <span class="<?php echo $row['isBmEnchantScroll'] === 'true' ? 'boolean-true' : 'boolean-false'; ?>">
                                <?php echo formatBoolean($row['isBmEnchantScroll']); ?>
                            </span>
                        </td>
                        <td><?php echo formatElementalType($row['elementalType']); ?></td>
                        <td class="table-cell-number"><?php echo htmlspecialchars($row['useBlesscodeScroll']); ?></td>
                        <td>
                            <?php 
                            if (!empty($row['noTargetMaterialList'])) {
                                $materials = explode(',', $row['noTargetMaterialList']);
                                if (count($materials) > 3) {
                                    echo htmlspecialchars(implode(', ', array_slice($materials, 0, 3))) . '... <span class="text-muted">(' . count($materials) . ' total)</span>';
                                } else {
                                    echo htmlspecialchars($row['noTargetMaterialList']);
                                }
                            } else {
                                echo '<span class="table-cell-null-value">None</span>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center">No enchant scroll data found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($total_pages > 1): ?>
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=1" class="admin-btn admin-btn-secondary admin-btn-small">First</a>
        <a href="?page=<?php echo $page - 1; ?>" class="admin-btn admin-btn-secondary admin-btn-small">Previous</a>
    <?php endif; ?>
    
    <?php
    // Show page numbers (with ellipsis for large page counts)
    $start_page = max(1, $page - 2);
    $end_page = min($total_pages, $page + 2);
    
    if ($start_page > 1) {
        echo '<span class="pagination-ellipsis">...</span>';
    }
    
    for ($i = $start_page; $i <= $end_page; $i++):
    ?>
        <a href="?page=<?php echo $i; ?>" 
           class="admin-btn <?php echo $i == $page ? 'admin-btn-primary' : 'admin-btn-secondary'; ?> admin-btn-small">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>
    
    <?php if ($end_page < $total_pages): ?>
        <span class="pagination-ellipsis">...</span>
    <?php endif; ?>
    
    <?php if ($page < $total_pages): ?>
        <a href="?page=<?php echo $page + 1; ?>" class="admin-btn admin-btn-secondary admin-btn-small">Next</a>
        <a href="?page=<?php echo $total_pages; ?>" class="admin-btn admin-btn-secondary admin-btn-small">Last</a>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
