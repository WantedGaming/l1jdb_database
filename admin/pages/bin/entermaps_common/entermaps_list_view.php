<?php
require_once __DIR__ . '/../../../includes/auth_check.php';
require_once __DIR__ . '/../../../../includes/db.php';
require_once __DIR__ . '/../../../includes/header.php';

// Pagination settings
$records_per_page = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Get total count for pagination
$count_query = "SELECT COUNT(*) as total FROM bin_entermaps_common";
$count_result = $conn->query($count_query);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Main query with translations
$query = "
    SELECT 
        e.id,
        e.action_name,
        e.number_id,
        e.loc_x,
        e.loc_y,
        e.loc_range,
        e.priority_id,
        e.maxUser,
        e.conditions,
        e.destinations,
        t.text_english as action_name_en
    FROM bin_entermaps_common e
    LEFT JOIN 0_translations t ON e.action_name = t.text_korean
    ORDER BY e.id, e.action_name
    LIMIT $records_per_page OFFSET $offset
";

$result = $conn->query($query);

// Helper function to format coordinates
function formatCoordinates($x, $y, $range = null) {
    if ($range && $range > 0) {
        return "($x, $y) ±{$range}";
    }
    return "($x, $y)";
}
?>

<div class="page-header">
    <nav class="breadcrumb">
        <a href="../../index.php">Dashboard</a> → 
        <a href="../index.php">Binary Tables</a> → 
        <span>Enter Maps List</span>
    </nav>
    <h1>Enter Maps List</h1>
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
                <th>ID</th>
                <th>Action Name</th>
                <th>Number ID</th>
                <th>Location</th>
                <th>Priority</th>
                <th>Max Users</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr onclick="window.location.href='entermaps_detail_view.php?id=<?php echo $row['id']; ?>&action_name=<?php echo urlencode($row['action_name']); ?>'" style="cursor: pointer;">
                        <td class="table-cell-number"><?php echo htmlspecialchars($row['id']); ?></td>
                        <td class="table-cell-name">
                            <?php 
                            // Display English translation if available, otherwise Korean
                            if (!empty($row['action_name_en'])) {
                                echo htmlspecialchars($row['action_name_en']);
                            } else {
                                echo htmlspecialchars($row['action_name']);
                            }
                            ?>
                        </td>
                        <td class="table-cell-id"><?php echo htmlspecialchars($row['number_id']); ?></td>
                        <td class="table-cell-text-value">
                            <?php echo formatCoordinates($row['loc_x'], $row['loc_y'], $row['loc_range']); ?>
                        </td>
                        <td class="table-cell-number"><?php echo htmlspecialchars($row['priority_id']); ?></td>
                        <td class="table-cell-number">
                            <?php 
                            if ($row['maxUser'] == 0) {
                                echo '<span class="text-muted">Unlimited</span>';
                            } else {
                                echo htmlspecialchars($row['maxUser']);
                            }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No enter maps data found</td>
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
