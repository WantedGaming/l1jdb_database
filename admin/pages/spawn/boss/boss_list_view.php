<?php
require_once('../../../includes/header.php');

// Database connection (using the same pattern as API files)
function getDbConnection() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "l1j_remastered";
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8");
    
    return $conn;
}

$db = getDbConnection();

// Set default sorting with validation
$allowedSorts = ['id', 'name', 'npcid', 'spawnMapId', 'spawnType'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $allowedSorts) ? $_GET['sort'] : 'id';
$order = isset($_GET['order']) && $_GET['order'] === 'DESC' ? 'DESC' : 'ASC';
$filter = isset($_GET['filter']) ? trim($_GET['filter']) : '';

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM spawnlist_boss";
if (!empty($filter)) {
    $countQuery .= " WHERE name LIKE ? OR desc_kr LIKE ?";
    $countStmt = $db->prepare($countQuery);
    $searchTerm = '%' . $filter . '%';
    $countStmt->bind_param("ss", $searchTerm, $searchTerm);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
} else {
    $countResult = $db->query($countQuery);
}
$totalCount = $countResult->fetch_assoc()['total'];

// Close count statement if it was prepared
if (isset($countStmt)) {
    $countStmt->close();
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$totalPages = ceil($totalCount / $perPage);
$offset = ($page - 1) * $perPage;

// Build query
$query = "SELECT * FROM spawnlist_boss";
if (!empty($filter)) {
    $query .= " WHERE name LIKE ? OR desc_kr LIKE ?";
    $query .= " ORDER BY $sort $order LIMIT ?, ?";
    $stmt = $db->prepare($query);
    $searchTerm = '%' . $filter . '%';
    $stmt->bind_param("ssii", $searchTerm, $searchTerm, $offset, $perPage);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    $query .= " ORDER BY $sort $order LIMIT ?, ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("ii", $offset, $perPage);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
}
?>

<div class="admin-content-wrapper">
    <div class="page-header">
        <div class="breadcrumb">
            <a href="/l1jdb_database/admin/">Dashboard</a> &raquo; 
            <a href="/l1jdb_database/admin/pages/spawn/">Spawns</a> &raquo; 
            <span>Boss Management</span>
        </div>
        <h1>Boss Spawn Management</h1>
    </div>
    
    <div class="admin-header-actions">
        <a href="boss_list_add.php" class="admin-btn admin-btn-primary">
            <i class="fa fa-plus"></i> Add New Boss Spawn
        </a>
    </div>
    
    <?php
    // Display success message if a boss was deleted
    if (isset($_GET['deleted']) && $_GET['deleted'] === 'true' && isset($_GET['name'])) {
        echo '<div class="admin-message admin-message-success">Boss "' . htmlspecialchars($_GET['name']) . '" has been deleted successfully!</div>';
    }
    ?>
    
    <!-- Search Form -->
    <div class="admin-filters">
        <h3>Search and Filter</h3>
        <form method="GET" class="filter-form">
            <div class="filter-group">
                <label for="filter">Search by name:</label>
                <input type="text" class="search-input" id="filter" name="filter" placeholder="Search by name..." value="<?php echo htmlspecialchars($filter); ?>">
            </div>
            <div class="filter-group">
                <label for="sort">Sort by:</label>
                <select name="sort" id="sort">
                    <option value="id" <?php echo $sort == 'id' ? 'selected' : ''; ?>>ID</option>
                    <option value="name" <?php echo $sort == 'name' ? 'selected' : ''; ?>>Name</option>
                    <option value="npcid" <?php echo $sort == 'npcid' ? 'selected' : ''; ?>>NPC ID</option>
                    <option value="spawnMapId" <?php echo $sort == 'spawnMapId' ? 'selected' : ''; ?>>Map ID</option>
                    <option value="spawnType" <?php echo $sort == 'spawnType' ? 'selected' : ''; ?>>Type</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="order">Order:</label>
                <select name="order" id="order">
                    <option value="ASC" <?php echo $order == 'ASC' ? 'selected' : ''; ?>>Ascending</option>
                    <option value="DESC" <?php echo $order == 'DESC' ? 'selected' : ''; ?>>Descending</option>
                </select>
            </div>
            <div class="btn-group">
                <button type="submit" class="admin-btn admin-btn-primary">Search</button>
                <?php if (!empty($filter)): ?>
                    <a href="boss_list_view.php" class="admin-btn admin-btn-secondary">Clear</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <!-- Results Count -->
    <div class="results-info">
        Showing <?php echo min($totalCount, $perPage); ?> of <?php echo $totalCount; ?> boss spawns
    </div>
    
    <!-- Boss List Table -->
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th class="table-cell-id">
                        <a href="?sort=id&order=<?php echo $sort == 'id' && $order == 'ASC' ? 'DESC' : 'ASC'; ?>&filter=<?php echo urlencode($filter); ?>">
                            ID <?php echo $sort == 'id' ? ($order == 'ASC' ? '▲' : '▼') : ''; ?>
                        </a>
                    </th>
                    <th class="table-cell-name">
                        <a href="?sort=name&order=<?php echo $sort == 'name' && $order == 'ASC' ? 'DESC' : 'ASC'; ?>&filter=<?php echo urlencode($filter); ?>">
                            Name <?php echo $sort == 'name' ? ($order == 'ASC' ? '▲' : '▼') : ''; ?>
                        </a>
                    </th>
                    <th class="table-cell-id">
                        <a href="?sort=npcid&order=<?php echo $sort == 'npcid' && $order == 'ASC' ? 'DESC' : 'ASC'; ?>&filter=<?php echo urlencode($filter); ?>">
                            NPC ID <?php echo $sort == 'npcid' ? ($order == 'ASC' ? '▲' : '▼') : ''; ?>
                        </a>
                    </th>
                    <th>
                        <a href="?sort=spawnMapId&order=<?php echo $sort == 'spawnMapId' && $order == 'ASC' ? 'DESC' : 'ASC'; ?>&filter=<?php echo urlencode($filter); ?>">
                            Map <?php echo $sort == 'spawnMapId' ? ($order == 'ASC' ? '▲' : '▼') : ''; ?>
                        </a>
                    </th>
                    <th>
                        <a href="?sort=spawnType&order=<?php echo $sort == 'spawnType' && $order == 'ASC' ? 'DESC' : 'ASC'; ?>&filter=<?php echo urlencode($filter); ?>">
                            Type <?php echo $sort == 'spawnType' ? ($order == 'ASC' ? '▲' : '▼') : ''; ?>
                        </a>
                    </th>
                    <th class="table-cell-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="clickable-row" onclick="window.location.href='boss_list_detail.php?id=<?php echo $row['id']; ?>'">
                            <td class="table-cell-id"><?php echo htmlspecialchars($row['id']); ?></td>
                            <td class="table-cell-name"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="table-cell-id"><?php echo htmlspecialchars($row['npcid']); ?></td>
                            <td><?php echo htmlspecialchars($row['spawnMapId']); ?></td>
                            <td><?php echo htmlspecialchars($row['spawnType']); ?></td>
                            <td class="table-cell-actions">
                                <a href="boss_list_detail.php?id=<?php echo $row['id']; ?>" class="admin-btn admin-btn-secondary admin-btn-small" onclick="event.stopPropagation()">Details</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="admin-empty">
                            <h3>No Boss Spawns Found</h3>
                            <p>Try adjusting your search criteria or add a new boss spawn</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="admin-pagination">
            <?php if ($page > 1): ?>
                <a class="admin-btn-page" href="?page=1&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>&filter=<?php echo urlencode($filter); ?>">First</a>
                <a class="admin-btn-page" href="?page=<?php echo $page - 1; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>&filter=<?php echo urlencode($filter); ?>">Previous</a>
            <?php endif; ?>
            
            <div class="pagination-pages">
                <?php
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $startPage + 4);
                if ($endPage - $startPage < 4) {
                    $startPage = max(1, $endPage - 4);
                }
                
                if ($startPage > 1) {
                    echo '<span class="page-dots">...</span>';
                }
                
                for ($i = $startPage; $i <= $endPage; $i++):
                ?>
                    <a href="?page=<?php echo $i; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>&filter=<?php echo urlencode($filter); ?>" class="admin-btn-page <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
                
                <?php if ($endPage < $totalPages) {
                    echo '<span class="page-dots">...</span>';
                } ?>
            </div>
            
            <?php if ($page < $totalPages): ?>
                <a class="admin-btn-page" href="?page=<?php echo $page + 1; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>&filter=<?php echo urlencode($filter); ?>">Next</a>
                <a class="admin-btn-page" href="?page=<?php echo $totalPages; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>&filter=<?php echo urlencode($filter); ?>">Last</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php
$db->close();
require_once('../../../includes/footer.php');
?>