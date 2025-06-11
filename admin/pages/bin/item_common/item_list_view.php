<?php
require_once __DIR__ . '/../../../includes/auth_check.php';
require_once __DIR__ . '/../../../../includes/db.php';
require_once __DIR__ . '/../../../includes/header.php';

// Pagination settings
$records_per_page = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Get total count for pagination
$count_query = "SELECT COUNT(*) as total FROM bin_item_common";
$count_result = $conn->query($count_query);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Main query with translations
$query = "
    SELECT 
        i.name_id,
        i.icon_id,
        i.desc_kr,
        i.material,
        i.item_category,
        i.level_limit_min,
        i.level_limit_max,
        t.text_english as desc_en
    FROM bin_item_common i
    LEFT JOIN 0_translations t ON i.desc_kr = t.text_korean
    ORDER BY i.name_id
    LIMIT $records_per_page OFFSET $offset
";

$result = $conn->query($query);

// Helper function to normalize material text (remove parentheses content)
function normalizeMaterial($material) {
    if (empty($material)) return 'None';
    
    // Remove anything in parentheses and trim
    $normalized = preg_replace('/\([^)]*\)/', '', $material);
    $normalized = trim($normalized);
    
    return ucfirst(strtolower($normalized));
}

// Helper function to format item category
function formatItemCategory($category) {
    if (empty($category)) return 'None';
    
    // Remove anything in parentheses and trim
    $formatted = preg_replace('/\([^)]*\)/', '', $category);
    $formatted = trim($formatted);
    $formatted = str_replace('_', ' ', $formatted);
    
    return ucwords(strtolower($formatted));
}
?>

<div class="page-header">
    <nav class="breadcrumb">
        <a href="../../index.php">Dashboard</a> → 
        <a href="../index.php">Binary Tables</a> → 
        <span>Item Common List</span>
    </nav>
    <h1>Item Common List</h1>
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
                <th>Icon</th>
                <th>Name ID</th>
                <th>Name</th>
                <th>Material</th>
                <th>Category</th>
                <th>Level Range</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr onclick="window.location.href='item_detail_view.php?id=<?php echo $row['name_id']; ?>'" style="cursor: pointer;">
                        <td>
                            <img src="../../../../assets/img/icons/<?php echo $row['icon_id']; ?>.png" 
                                 alt="Icon <?php echo $row['icon_id']; ?>" 
                                 width="64" height="64"
                                 onerror="this.src='../../../../assets/img/icons/0.png'">
                        </td>
                        <td><?php echo htmlspecialchars($row['name_id']); ?></td>
                        <td>
                            <?php 
                            // Display English translation if available, otherwise Korean
                            if (!empty($row['desc_en'])) {
                                echo htmlspecialchars($row['desc_en']);
                            } else {
                                echo htmlspecialchars($row['desc_kr']);
                            }
                            ?>
                        </td>
                        <td><?php echo normalizeMaterial($row['material']); ?></td>
                        <td><?php echo formatItemCategory($row['item_category']); ?></td>
                        <td>
                            <?php 
                            if ($row['level_limit_min'] > 0 || $row['level_limit_max'] > 0) {
                                if ($row['level_limit_min'] == $row['level_limit_max']) {
                                    echo $row['level_limit_min'];
                                } else {
                                    echo $row['level_limit_min'] . '-' . $row['level_limit_max'];
                                }
                            } else {
                                echo 'Any';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No item data found</td>
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
