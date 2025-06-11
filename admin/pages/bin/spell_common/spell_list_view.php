<?php
require_once __DIR__ . '/../../../includes/auth_check.php';
require_once __DIR__ . '/../../../../includes/db.php';
require_once __DIR__ . '/../../../includes/header.php';

// Pagination settings
$records_per_page = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Get total count for pagination
$count_query = "SELECT COUNT(*) as total FROM bin_spell_common";
$count_result = $conn->query($count_query);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Main query
$query = "
    SELECT 
        spell_id,
        spell_category,
        on_icon_id,
        duration
    FROM bin_spell_common
    ORDER BY spell_id
    LIMIT $records_per_page OFFSET $offset
";

$result = $conn->query($query);
?>

<div class="page-header">
    <nav class="breadcrumb">
        <a href="../../index.php">Dashboard</a> → 
        <a href="../index.php">Binary Tables</a> → 
        <span>Spell Common List</span>
    </nav>
    <h1>Spell Common List</h1>
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
                <th>Spell ID</th>
                <th>Spell Category</th>
                <th>Duration</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr onclick="window.location.href='spell_detail_view.php?id=<?php echo $row['spell_id']; ?>'" style="cursor: pointer;">
                        <td>
                            <img src="../../../../assets/img/icons/<?php echo $row['on_icon_id']; ?>.png" 
                                 alt="Icon <?php echo $row['on_icon_id']; ?>" 
                                 width="32" height="32"
                                 onerror="this.src='../../../../assets/img/icons/0.png'">
                        </td>
                        <td><?php echo htmlspecialchars($row['spell_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['spell_category']); ?></td>
                        <td><?php echo number_format($row['duration']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">No spell data found</td>
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
