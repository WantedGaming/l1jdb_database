<?php
require_once __DIR__ . '/../../../includes/auth_check.php';
require_once __DIR__ . '/../../../../includes/db.php';
require_once __DIR__ . '/../../../includes/header.php';

// Pagination settings
$records_per_page = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Get total count for pagination
$count_query = "SELECT COUNT(*) as total FROM bin_catalyst_common";
$count_result = $conn->query($count_query);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Main query with translations
$query = "
    SELECT 
        c.nameId,
        c.nameId_kr,
        c.input,
        c.input_kr,
        c.output,
        c.output_kr,
        c.successProb,
        t1.text_english as nameId_en,
        t2.text_english as input_en,
        t3.text_english as output_en
    FROM bin_catalyst_common c
    LEFT JOIN 0_translations t1 ON c.nameId_kr = t1.text_korean
    LEFT JOIN 0_translations t2 ON c.input_kr = t2.text_korean
    LEFT JOIN 0_translations t3 ON c.output_kr = t3.text_korean
    ORDER BY c.nameId
    LIMIT $records_per_page OFFSET $offset
";

$result = $conn->query($query);
?>

<div class="page-header">
    <nav class="breadcrumb">
        <a href="../../index.php">Dashboard</a> → 
        <a href="../index.php">Binary Tables</a> → 
        <span>Catalyst List</span>
    </nav>
    <h1>Catalyst List</h1>
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
                <th>Name ID</th>
                <th>Name</th>
                <th>Input ID</th>
                <th>Input Item</th>
                <th>Output ID</th>
                <th>Output Item</th>
                <th>Success Prob (%)</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nameId']); ?></td>
                        <td>
                            <?php 
                            // Display English translation if available, otherwise Korean
                            if (!empty($row['nameId_en'])) {
                                echo htmlspecialchars($row['nameId_en']);
                            } else {
                                echo htmlspecialchars($row['nameId_kr']);
                            }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['input']); ?></td>
                        <td>
                            <?php 
                            if (!empty($row['input_en'])) {
                                echo htmlspecialchars($row['input_en']);
                            } else {
                                echo htmlspecialchars($row['input_kr']);
                            }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['output']); ?></td>
                        <td>
                            <?php 
                            if (!empty($row['output_en'])) {
                                echo htmlspecialchars($row['output_en']);
                            } else {
                                echo htmlspecialchars($row['output_kr']);
                            }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['successProb']); ?>%</td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No catalyst data found</td>
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