<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../includes/header.php';

// Handle form submissions
$message = '';
$messageType = '';

// Handle delete operation
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM weapon WHERE item_id = ?");
        $stmt->execute([$_GET['id']]);
        logAdminActivity('DELETE', 'weapon', $_GET['id'], 'Deleted weapon');
        $message = 'Weapon deleted successfully!';
        $messageType = 'success';
    } catch (PDOException $e) {
        $message = 'Error deleting weapon: ' . $e->getMessage();
        $messageType = 'error';
    }
}

// Pagination setup
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Filter setup
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$typeFilter = isset($_GET['type']) ? $_GET['type'] : '';
$gradeFilter = isset($_GET['grade']) ? $_GET['grade'] : '';
$materialFilter = isset($_GET['material']) ? $_GET['material'] : '';

// Build where clause
$whereConditions = [];
$params = [];

if (!empty($search)) {
    $whereConditions[] = "(w.desc_en LIKE ? OR w.desc_kr LIKE ? OR w.item_id = ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = $search;
}

if (!empty($typeFilter)) {
    $whereConditions[] = "w.type = ?";
    $params[] = $typeFilter;
}

if (!empty($gradeFilter)) {
    $whereConditions[] = "w.itemGrade = ?";
    $params[] = $gradeFilter;
}

if (!empty($materialFilter)) {
    $whereConditions[] = "w.material = ?";
    $params[] = $materialFilter;
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Get total count for pagination
$countSql = "SELECT COUNT(*) FROM weapon w $whereClause";
$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$totalItems = $stmt->fetchColumn();
$totalPages = ceil($totalItems / $perPage);

// Get weapons with optional bin item data
$sql = "SELECT w.*, 
               bc.desc_kr as bin_desc_kr,
               bc.desc_id as bin_desc_id,
               bc.real_desc as bin_real_desc
        FROM weapon w 
        LEFT JOIN bin_item_common bc ON w.item_name_id = bc.name_id
        $whereClause 
        ORDER BY w.item_id ASC 
        LIMIT $perPage OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$weapons = $stmt->fetchAll();

// Get filter options
$typeOptions = $pdo->query("SELECT DISTINCT type FROM weapon ORDER BY type")->fetchAll(PDO::FETCH_COLUMN);
$gradeOptions = $pdo->query("SELECT DISTINCT itemGrade FROM weapon ORDER BY itemGrade")->fetchAll(PDO::FETCH_COLUMN);
$materialOptions = $pdo->query("SELECT DISTINCT material FROM weapon ORDER BY material")->fetchAll(PDO::FETCH_COLUMN);
?>
        <!-- Admin Header -->
        <div class="admin-header">
            <h1>Weapon Management</h1>
            <div class="admin-header-actions">
                <a href="<?php echo SITE_URL; ?>/admin/pages/weapon/weapon_add.php" class="admin-btn admin-btn-primary">
                    ➕ Add New Weapon
                </a>
                <a href="/l1jdb_database/admin/" class="admin-btn admin-btn-secondary">
                    ← Back to Admin
                </a>
            </div>
        </div>

        <!-- Breadcrumb -->
        <nav class="admin-breadcrumb">
            <ul class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="/l1jdb_database/admin/">Admin</a></li>
                <li class="breadcrumb-separator">›</li>
                <li class="breadcrumb-item">Weapon Management</li>
            </ul>
        </nav>

        <?php if ($message): ?>
            <div class="admin-message admin-message-<?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="admin-stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($totalItems); ?></div>
                <div class="stat-label">Total Weapons</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count(array_filter($weapons, function($w) { return $w['itemGrade'] !== 'NORMAL'; })); ?></div>
                <div class="stat-label">Special Grade Weapons</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count(array_unique(array_column($weapons, 'type'))); ?></div>
                <div class="stat-label">Weapon Types</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="admin-filters">
            <h3>🔍 Filter Weapons</h3>
            <form method="GET" class="filter-form">
                <div class="filter-group">
                    <label for="search">Search</label>
                    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Name, ID, or description...">
                </div>
                <div class="filter-group">
                    <label for="type">Type</label>
                    <select id="type" name="type">
                        <option value="">All Types</option>
                        <?php foreach ($typeOptions as $type): ?>
                            <option value="<?php echo htmlspecialchars($type); ?>" <?php echo $typeFilter === $type ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars(normalizeWeaponType($type)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="grade">Grade</label>
                    <select id="grade" name="grade">
                        <option value="">All Grades</option>
                        <?php foreach ($gradeOptions as $grade): ?>
                            <option value="<?php echo htmlspecialchars($grade); ?>" <?php echo $gradeFilter === $grade ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars(normalizeGrade($grade)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="material">Material</label>
                    <select id="material" name="material">
                        <option value="">All Materials</option>
                        <?php foreach ($materialOptions as $material): ?>
                            <option value="<?php echo htmlspecialchars($material); ?>" <?php echo $materialFilter === $material ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars(normalizeWeaponMaterial($material)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="admin-btn admin-btn-primary">Filter</button>
                <a href="<?php echo SITE_URL; ?>/admin/pages/weapon/weapon_list.php" class="admin-btn admin-btn-secondary">Clear</a>
            </form>
        </div>

        <!-- Results Info -->
        <div class="results-info">
            Showing <?php echo number_format(count($weapons)); ?> of <?php echo number_format($totalItems); ?> weapons
            (Page <?php echo $page; ?> of <?php echo $totalPages; ?>)
        </div>

        <!-- Weapons Table -->
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Icon</th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Korean Name</th>
                        <th>Type</th>
                        <th>Grade</th>
                        <th>Material</th>
                        <th>Damage (S/L)</th>
                        <th>Weight</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($weapons)): ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted">
                                <div class="admin-empty">
                                    <h3>No weapons found</h3>
                                    <p>No weapons match your current filters.</p>
                                    <a href="<?php echo SITE_URL; ?>/admin/pages/weapon/weapon_add.php" class="admin-btn admin-btn-primary">
                                        Add First Weapon
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($weapons as $weapon): ?>
                            <tr>
                                <td class="table-cell-icon">
                                    <img src="<?php echo SITE_URL; ?>/assets/img/icons/<?php echo $weapon['iconId']; ?>.png" 
                                         alt="<?php echo htmlspecialchars($weapon['desc_en']); ?>" 
                                         onerror="this.src='<?php echo SITE_URL; ?>/assets/img/icons/0.png'">
                                </td>
                                <td class="table-cell-id"><?php echo $weapon['item_id']; ?></td>
                                <td class="table-cell-name">
                                    <a href="<?php echo SITE_URL; ?>/pages/weapons/weapon_detail.php?id=<?php echo $weapon['item_id']; ?>" 
                                       class="weapon-link" target="_blank">
                                        <?php echo htmlspecialchars(cleanDescriptionPrefix($weapon['desc_en'])); ?>
                                    </a>
                                    <?php if ($weapon['itemGrade'] !== 'NORMAL'): ?>
                                        <?php echo displayGrade($weapon['itemGrade']); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($weapon['desc_kr'])): ?>
                                        <?php echo htmlspecialchars($weapon['desc_kr']); ?>
                                    <?php elseif (!empty($weapon['bin_desc_kr'])): ?>
                                        <span class="text-muted"><?php echo htmlspecialchars($weapon['bin_desc_kr']); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars(normalizeWeaponType($weapon['type'])); ?></td>
                                <td>
                                    <span class="<?php echo getGradeClass($weapon['itemGrade']); ?>">
                                        <?php echo htmlspecialchars(normalizeGrade($weapon['itemGrade'])); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars(normalizeWeaponMaterial($weapon['material'])); ?></td>
                                <td class="table-cell-number">
                                    <?php echo $weapon['dmg_small']; ?>/<?php echo $weapon['dmg_large']; ?>
                                </td>
                                <td class="table-cell-number"><?php echo ($weapon['weight']); ?></td>
                                <td class="table-cell-actions">
                                    <div class="btn-group">
                                        <a href="<?php echo SITE_URL; ?>/admin/pages/weapon/weapon_edit.php?id=<?php echo $weapon['item_id']; ?>" 
                                           class="admin-btn admin-btn-small admin-btn-secondary" title="Edit">
                                            ✏️
                                        </a>
                                        <a href="<?php echo SITE_URL; ?>/admin/pages/weapon/weapon_list.php?action=delete&id=<?php echo $weapon['item_id']; ?>" 
                                           class="admin-btn admin-btn-small admin-btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this weapon?')" title="Delete">
                                            🗑️
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="admin-pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=1<?php echo !empty($_GET) ? '&' . http_build_query(array_filter($_GET, function($k) { return $k !== 'page'; }, ARRAY_FILTER_USE_KEY)) : ''; ?>" class="page-btn">« First</a>
                    <a href="?page=<?php echo $page - 1; ?><?php echo !empty($_GET) ? '&' . http_build_query(array_filter($_GET, function($k) { return $k !== 'page'; }, ARRAY_FILTER_USE_KEY)) : ''; ?>" class="page-btn">‹ Prev</a>
                <?php endif; ?>

                <?php
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);
                
                for ($i = $startPage; $i <= $endPage; $i++):
                ?>
                    <a href="?page=<?php echo $i; ?><?php echo !empty($_GET) ? '&' . http_build_query(array_filter($_GET, function($k) { return $k !== 'page'; }, ARRAY_FILTER_USE_KEY)) : ''; ?>" 
                       class="page-btn <?php echo $i === $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?><?php echo !empty($_GET) ? '&' . http_build_query(array_filter($_GET, function($k) { return $k !== 'page'; }, ARRAY_FILTER_USE_KEY)) : ''; ?>" class="page-btn">Next ›</a>
                    <a href="?page=<?php echo $totalPages; ?><?php echo !empty($_GET) ? '&' . http_build_query(array_filter($_GET, function($k) { return $k !== 'page'; }, ARRAY_FILTER_USE_KEY)) : ''; ?>" class="page-btn">Last »</a>
                <?php endif; ?>

                <div class="page-info">
                    Page <?php echo $page; ?> of <?php echo $totalPages; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
// Auto-submit form on filter changes
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.querySelector('.filter-form');
    const filterSelects = filterForm.querySelectorAll('select');
    
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            filterForm.submit();
        });
    });
    
    // Handle search input with debounce
    const searchInput = document.querySelector('#search');
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (this.value.length >= 3 || this.value.length === 0) {
                filterForm.submit();
            }
        }, 500);
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
