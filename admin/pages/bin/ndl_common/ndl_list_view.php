<?php
require_once __DIR__ . '/../../../includes/auth_check.php';
require_once __DIR__ . '/../../../../includes/db.php';
require_once __DIR__ . '/../../../includes/header.php';

// Pagination settings
$records_per_page = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Get total count for pagination
$count_query = "SELECT COUNT(*) as total FROM bin_ndl_common";
$count_result = $conn->query($count_query);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Main query with translations
$query = "
    SELECT 
        n.map_number,
        n.npc_classId,
        n.npc_desc_kr,
        n.territory_startXY,
        n.territory_endXY,
        n.territory_location_desc,
        n.territory_average_level,
        n.territory_average_npc_value,
        t.text_english as npc_desc_en
    FROM bin_ndl_common n
    LEFT JOIN 0_translations t ON n.npc_desc_kr = t.text_korean
    ORDER BY n.map_number, n.npc_classId, n.territory_startXY
    LIMIT $records_per_page OFFSET $offset
";

$result = $conn->query($query);

// Helper function to format coordinates
function formatCoordinates($xy) {
    if ($xy == 0) return 'N/A';
    // Assuming XY is packed as (X << 16) | Y or similar format
    return $xy;
}
?>

<div class="page-header">
    <nav class="breadcrumb">
        <a href="../../index.php">Dashboard</a> → 
        <a href="../index.php">Binary Tables</a> → 
        <span>NDL Common List</span>
    </nav>
    <h1>NDL Common List</h1>
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
                <th>Map Number</th>
                <th>NPC Class ID</th>
                <th>NPC Name</th>
                <th>Territory Start</th>
                <th>Territory End</th>
                <th>Avg Level</th>
                <th>Avg Value</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr onclick="window.location.href='ndl_detail_view.php?map=<?php echo $row['map_number']; ?>&class=<?php echo $row['npc_classId']; ?>&start=<?php echo $row['territory_startXY']; ?>&end=<?php echo $row['territory_endXY']; ?>'" style="cursor: pointer;">
                        <td><?php echo htmlspecialchars($row['map_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['npc_classId']); ?></td>
                        <td>
                            <?php 
                            // Display English translation if available, otherwise Korean
                            if (!empty($row['npc_desc_en'])) {
                                echo htmlspecialchars($row['npc_desc_en']);
                            } else {
                                echo htmlspecialchars($row['npc_desc_kr']);
                            }
                            ?>
                        </td>
                        <td><?php echo formatCoordinates($row['territory_startXY']); ?></td>
                        <td><?php echo formatCoordinates($row['territory_endXY']); ?></td>
                        <td><?php echo htmlspecialchars($row['territory_average_level']); ?></td>
                        <td><?php echo number_format($row['territory_average_npc_value']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No NDL data found</td>
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
