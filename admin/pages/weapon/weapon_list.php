<?php
require_once dirname(dirname(dirname(__DIR__))) . '/includes/config.php';
require_once dirname(dirname(dirname(__DIR__))) . '/admin/includes/header.php';

// Handle success/error messages
$message = '';
$messageType = '';

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'added':
            $message = 'Weapon successfully created!';
            $messageType = 'success';
            break;
        case 'updated':
            $message = 'Weapon successfully updated!';
            $messageType = 'success';
            break;
        case 'deleted':
            $name = $_GET['name'] ?? 'Unknown';
            $message = 'Weapon "' . htmlspecialchars($name) . '" successfully deleted!';
            $messageType = 'success';
            break;
    }
}

if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'database':
            $message = 'Database error: ' . ($_GET['msg'] ?? 'Unknown error');
            $messageType = 'error';
            break;
        case 'delete':
            $message = 'Error deleting weapon: ' . ($_GET['msg'] ?? 'Unknown error');
            $messageType = 'error';
            break;
        default:
            $message = 'An error occurred';
            $messageType = 'error';
            break;
    }
}

// Pagination setup
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 25;
$offset = ($page - 1) * $perPage;

// Filter setup
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$typeFilter = isset($_GET['type']) ? $_GET['type'] : '';
$gradeFilter = isset($_GET['grade']) ? $_GET['grade'] : '';
$materialFilter = isset($_GET['material']) ? $_GET['material'] : '';
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'item_id';
$sortOrder = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';

// Build where clause
$whereConditions = [];
$params = [];

if (!empty($search)) {
    $whereConditions[] = "(w.desc_en LIKE ? OR w.desc_kr LIKE ? OR w.item_id = ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = is_numeric($search) ? $search : 0;
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

// Validate sort column
$allowedSortColumns = ['item_id', 'desc_en', 'type', 'itemGrade', 'weight', 'dmg_small', 'dmg_large'];
if (!in_array($sortBy, $allowedSortColumns)) {
    $sortBy = 'item_id';
}

// Get total count for pagination
$countSql = "SELECT COUNT(*) FROM weapon w $whereClause";
$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$totalItems = $stmt->fetchColumn();
$totalPages = ceil($totalItems / $perPage);

// Get weapons
$sql = "SELECT w.item_id, w.desc_en, w.desc_kr, w.type, w.itemGrade, w.material, 
               w.weight, w.iconId, w.spriteId, w.dmg_small, w.dmg_large,
               w.add_str, w.add_con, w.add_dex, w.add_int, w.add_wis, w.add_cha,
               w.add_hp, w.add_mp, w.safenchant, w.hitmodifier, w.dmgmodifier
        FROM weapon w 
        $whereClause 
        ORDER BY w.$sortBy $sortOrder 
        LIMIT $perPage OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$weapons = $stmt->fetchAll();

// Get filter options
$typeOptions = $pdo->query("SELECT DISTINCT type FROM weapon ORDER BY type")->fetchAll(PDO::FETCH_COLUMN);
$gradeOptions = $pdo->query("SELECT DISTINCT itemGrade FROM weapon ORDER BY 
    CASE itemGrade 
        WHEN 'ONLY' THEN 1
        WHEN 'MYTH' THEN 2
        WHEN 'LEGEND' THEN 3
        WHEN 'HERO' THEN 4
        WHEN 'RARE' THEN 5
        WHEN 'ADVANC' THEN 6
        WHEN 'NORMAL' THEN 7
        ELSE 8
    END")->fetchAll(PDO::FETCH_COLUMN);
$materialOptions = $pdo->query("SELECT DISTINCT material FROM weapon ORDER BY material")->fetchAll(PDO::FETCH_COLUMN);

// Helper functions
function getWeaponTypes() {
    return [
        'SWORD' => 'Sword',
        'DAGGER' => 'Dagger',
        'TOHAND_SWORD' => 'Two-Hand Sword',
        'BOW' => 'Bow',
        'SPEAR' => 'Spear',
        'BLUNT' => 'Blunt',
        'STAFF' => 'Staff',
        'STING' => 'Sting',
        'ARROW' => 'Arrow',
        'GAUNTLET' => 'Gauntlet',
        'CLAW' => 'Claw',
        'EDORYU' => 'Edoryu',
        'SINGLE_BOW' => 'Single Bow',
        'SINGLE_SPEAR' => 'Single Spear',
        'TOHAND_BLUNT' => 'Two-Hand Blunt',
        'TOHAND_STAFF' => 'Two-Hand Staff',
        'KEYRINGK' => 'Keyring',
        'CHAINSWORD' => 'Chain Sword'
    ];
}

function getWeaponGrades() {
    return [
        'NORMAL' => 'Normal',
        'ADVANC' => 'Advanced',
        'RARE' => 'Rare',
        'HERO' => 'Hero',
        'LEGEND' => 'Legend',
        'MYTH' => 'Myth',
        'ONLY' => 'Only'
    ];
}

function getGradeClass($grade) {
    $classes = [
        'ONLY' => 'grade-only',
        'MYTH' => 'grade-myth',
        'LEGEND' => 'grade-legend',
        'HERO' => 'grade-hero',
        'RARE' => 'grade-rare',
        'ADVANC' => 'grade-advanc',
        'NORMAL' => 'grade-normal'
    ];
    return $classes[$grade] ?? 'grade-normal';
}

function formatMaterial($material) {
    // Extract the English part from materials like "IRON(철)"
    if (preg_match('/^([A-Z_]+)\(/', $material, $matches)) {
        return ucfirst(strtolower($matches[1]));
    }
    return ucfirst(strtolower($material));
}
?>

<div class="admin-content-wrapper">
    <!-- Admin Header -->
    <div class="admin-header">
        <h1>Weapon Management</h1>
        <div class="admin-header-actions">
            <a href="weapon_add.php" class="admin-btn admin-btn-primary">
                <span class="admin-icon admin-icon-spawn"></span> Add New Weapon
            </a>
            <a href="/l1jdb_database/admin/" class="admin-btn admin-btn-secondary">
                <span>←</span> Back to Admin
            </a>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="admin-breadcrumb">
        <ul class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="/l1jdb_database/admin/">Admin Dashboard</a></li>
            <li class="breadcrumb-separator">/</li>
            <li class="breadcrumb-item">Weapon Management</li>
        </ul>
    </div>

    <!-- Messages -->
    <?php if ($message): ?>
        <div class="admin-message admin-message-<?= $messageType ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Stats Overview -->
    <div class="admin-stats">
        <div class="stat-card">
            <h3>Total Weapons</h3>
            <div class="stat-number"><?= number_format($totalItems) ?></div>
            <div class="stat-label">Database entries</div>
        </div>
        <div class="stat-card">
            <h3>Weapon Types</h3>
            <div class="stat-number"><?= count($typeOptions) ?></div>
            <div class="stat-label">Different categories</div>
        </div>
        <div class="stat-card">
            <h3>Special Grade</h3>
            <div class="stat-number">
                <?php
                $specialCount = 0;
                foreach ($weapons as $weapon) {
                    if ($weapon['itemGrade'] !== 'NORMAL') $specialCount++;
                }
                echo $specialCount;
                ?>
            </div>
            <div class="stat-label">Non-normal grade</div>
        </div>
        <div class="stat-card">
            <h3>Current Page</h3>
            <div class="stat-number"><?= $page ?> / <?= $totalPages ?></div>
            <div class="stat-label">Page navigation</div>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="admin-filters">
        <h3>Filter & Search Weapons</h3>
        <form method="GET" class="filter-form" id="filterForm">
            <div class="filter-group">
                <label for="search">Search</label>
                <div class="admin-search">
                    <input type="text" id="search" name="search" value="<?= htmlspecialchars($search) ?>" 
                           placeholder="Name, ID, or description..." class="search-input">
                </div>
            </div>
            
            <div class="filter-group">
                <label for="type">Weapon Type</label>
                <select id="type" name="type">
                    <option value="">All Types</option>
                    <?php 
                    $weaponTypes = getWeaponTypes();
                    foreach ($typeOptions as $type): 
                    ?>
                        <option value="<?= htmlspecialchars($type) ?>" <?= $typeFilter === $type ? 'selected' : '' ?>>
                            <?= htmlspecialchars($weaponTypes[$type] ?? $type) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="grade">Item Grade</label>
                <select id="grade" name="grade">
                    <option value="">All Grades</option>
                    <?php 
                    $weaponGrades = getWeaponGrades();
                    foreach ($gradeOptions as $grade): 
                    ?>
                        <option value="<?= htmlspecialchars($grade) ?>" <?= $gradeFilter === $grade ? 'selected' : '' ?>>
                            <?= htmlspecialchars($weaponGrades[$grade] ?? $grade) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="material">Material</label>
                <select id="material" name="material">
                    <option value="">All Materials</option>
                    <?php foreach ($materialOptions as $material): ?>
                        <option value="<?= htmlspecialchars($material) ?>" <?= $materialFilter === $material ? 'selected' : '' ?>>
                            <?= htmlspecialchars(formatMaterial($material)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="sort">Sort By</label>
                <select id="sort" name="sort">
                    <option value="item_id" <?= $sortBy === 'item_id' ? 'selected' : '' ?>>ID</option>
                    <option value="desc_en" <?= $sortBy === 'desc_en' ? 'selected' : '' ?>>Name</option>
                    <option value="type" <?= $sortBy === 'type' ? 'selected' : '' ?>>Type</option>
                    <option value="itemGrade" <?= $sortBy === 'itemGrade' ? 'selected' : '' ?>>Grade</option>
                    <option value="weight" <?= $sortBy === 'weight' ? 'selected' : '' ?>>Weight</option>
                    <option value="dmg_small" <?= $sortBy === 'dmg_small' ? 'selected' : '' ?>>Damage</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="order">Order</label>
                <select id="order" name="order">
                    <option value="asc" <?= $sortOrder === 'ASC' ? 'selected' : '' ?>>Ascending</option>
                    <option value="desc" <?= $sortOrder === 'DESC' ? 'selected' : '' ?>>Descending</option>
                </select>
            </div>
            
            <div class="btn-group">
                <button type="submit" class="admin-btn admin-btn-primary">Apply Filters</button>
                <a href="weapon_list.php" class="admin-btn admin-btn-secondary">Clear All</a>
            </div>
        </form>
    </div>

    <!-- Results Info -->
    <div class="results-info">
        Showing <?= count($weapons) ?> of <?= number_format($totalItems) ?> weapons
        <?php if ($page > 1 || $totalPages > 1): ?>
            (Page <?= $page ?> of <?= $totalPages ?>)
        <?php endif; ?>
        <?php if (!empty($search) || !empty($typeFilter) || !empty($gradeFilter) || !empty($materialFilter)): ?>
            - <strong>Filtered results</strong>
        <?php endif; ?>
    </div>

    <!-- Weapons Table -->
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th class="table-cell-icon">Icon</th>
                    <th class="table-cell-id">
                        <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'item_id', 'order' => $sortBy === 'item_id' && $sortOrder === 'ASC' ? 'desc' : 'asc'])) ?>">
                            ID <?= $sortBy === 'item_id' ? ($sortOrder === 'ASC' ? '↑' : '↓') : '' ?>
                        </a>
                    </th>
                    <th class="table-cell-name">
                        <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'desc_en', 'order' => $sortBy === 'desc_en' && $sortOrder === 'ASC' ? 'desc' : 'asc'])) ?>">
                            Name <?= $sortBy === 'desc_en' ? ($sortOrder === 'ASC' ? '↑' : '↓') : '' ?>
                        </a>
                    </th>
                    <th>Korean Name</th>
                    <th>
                        <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'type', 'order' => $sortBy === 'type' && $sortOrder === 'ASC' ? 'desc' : 'asc'])) ?>">
                            Type <?= $sortBy === 'type' ? ($sortOrder === 'ASC' ? '↑' : '↓') : '' ?>
                        </a>
                    </th>
                    <th>
                        <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'itemGrade', 'order' => $sortBy === 'itemGrade' && $sortOrder === 'ASC' ? 'desc' : 'asc'])) ?>">
                            Grade <?= $sortBy === 'itemGrade' ? ($sortOrder === 'ASC' ? '↑' : '↓') : '' ?>
                        </a>
                    </th>
                    <th>Damage</th>
                    <th>Stats</th>
                    <th class="table-cell-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($weapons)): ?>
                    <tr>
                        <td colspan="9">
                            <div class="admin-empty">
                                <h3>No Weapons Found</h3>
                                <p>No weapons match your current search criteria.</p>
                                <a href="weapon_add.php" class="admin-btn admin-btn-primary">Add First Weapon</a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($weapons as $weapon): ?>
                        <tr class="clickable-row" onclick="window.open('/l1jdb_database/pages/weapons/weapon_detail.php?id=<?= $weapon['item_id'] ?>', '_blank')">
                            <td class="icon-cell">
                                <img src="/l1jdb_database/assets/img/icons/<?= $weapon['iconId'] ?>.png" 
                                     alt="<?= htmlspecialchars($weapon['desc_en']) ?>" 
                                     onerror="this.src='/l1jdb_database/assets/img/placeholders/0.png'"
                                     title="Icon ID: <?= $weapon['iconId'] ?>">
                            </td>
                            <td class="table-cell-id"><?= $weapon['item_id'] ?></td>
                            <td class="table-cell-name">
                                <div>
                                    <?= htmlspecialchars($weapon['desc_en']) ?>
                                    <?php if ($weapon['itemGrade'] !== 'NORMAL'): ?>
                                        <span class="badge badge-<?= $weapon['itemGrade'] === 'ONLY' ? 'danger' : ($weapon['itemGrade'] === 'MYTH' ? 'warning' : 'info') ?>">
                                            <?= getWeaponGrades()[$weapon['itemGrade']] ?? $weapon['itemGrade'] ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-small">
                                <?= !empty($weapon['desc_kr']) ? htmlspecialchars($weapon['desc_kr']) : '<span class="text-muted">-</span>' ?>
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    <?= getWeaponTypes()[$weapon['type']] ?? $weapon['type'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="<?= getGradeClass($weapon['itemGrade']) ?>">
                                    <?= getWeaponGrades()[$weapon['itemGrade']] ?? $weapon['itemGrade'] ?>
                                </span>
                            </td>
                            <td class="table-cell-number">
                                <div class="text-small">
                                    <strong><?= $weapon['dmg_small'] ?></strong> / <strong><?= $weapon['dmg_large'] ?></strong>
                                    <?php if ($weapon['hitmodifier'] != 0): ?>
                                        <br><span class="text-positive">Hit: +<?= $weapon['hitmodifier'] ?></span>
                                    <?php endif; ?>
                                    <?php if ($weapon['dmgmodifier'] != 0): ?>
                                        <br><span class="text-positive">Dmg: +<?= $weapon['dmgmodifier'] ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-small">
                                <?php 
                                $stats = [];
                                if ($weapon['add_str'] != 0) $stats[] = "STR: {$weapon['add_str']}";
                                if ($weapon['add_con'] != 0) $stats[] = "CON: {$weapon['add_con']}";
                                if ($weapon['add_dex'] != 0) $stats[] = "DEX: {$weapon['add_dex']}";
                                if ($weapon['add_int'] != 0) $stats[] = "INT: {$weapon['add_int']}";
                                if ($weapon['add_wis'] != 0) $stats[] = "WIS: {$weapon['add_wis']}";
                                if ($weapon['add_cha'] != 0) $stats[] = "CHA: {$weapon['add_cha']}";
                                if ($weapon['add_hp'] != 0) $stats[] = "HP: {$weapon['add_hp']}";
                                if ($weapon['add_mp'] != 0) $stats[] = "MP: {$weapon['add_mp']}";
                                
                                if (!empty($stats)) {
                                    echo '<div class="text-positive">' . implode('<br>', array_slice($stats, 0, 3)) . '</div>';
                                    if (count($stats) > 3) {
                                        echo '<div class="text-muted">+' . (count($stats) - 3) . ' more</div>';
                                    }
                                } else {
                                    echo '<span class="text-muted">No bonuses</span>';
                                }
                                ?>
                            </td>
                            <td class="table-cell-actions">
                                <div class="btn-group">
                                    <a href="weapon_edit.php?id=<?= $weapon['item_id'] ?>" 
                                       class="admin-btn admin-btn-small admin-btn-secondary" 
                                       title="Edit Weapon"
                                       onclick="event.stopPropagation()">
                                        ✏️
                                    </a>
                                    <a href="weapon_delete.php?id=<?= $weapon['item_id'] ?>" 
                                       class="admin-btn admin-btn-small admin-btn-danger" 
                                       title="Delete Weapon"
                                       onclick="event.stopPropagation(); return confirm('Are you sure you want to delete this weapon?')"
                                       data-weapon-name="<?= htmlspecialchars($weapon['desc_en']) ?>">
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
            <?php
            $baseQuery = $_GET;
            unset($baseQuery['page']);
            $queryString = !empty($baseQuery) ? '&' . http_build_query($baseQuery) : '';
            ?>
            
            <?php if ($page > 1): ?>
                <a href="?page=1<?= $queryString ?>" class="admin-btn-page">« First</a>
                <a href="?page=<?= $page - 1 ?><?= $queryString ?>" class="admin-btn-page">‹ Prev</a>
            <?php endif; ?>

            <div class="pagination-pages">
                <?php
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);
                
                if ($startPage > 1) {
                    echo '<a href="?page=1' . $queryString . '" class="admin-btn-page">1</a>';
                    if ($startPage > 2) {
                        echo '<span class="page-dots">...</span>';
                    }
                }
                
                for ($i = $startPage; $i <= $endPage; $i++):
                ?>
                    <a href="?page=<?= $i ?><?= $queryString ?>" 
                       class="admin-btn-page <?= $i === $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php 
                endfor;
                
                if ($endPage < $totalPages) {
                    if ($endPage < $totalPages - 1) {
                        echo '<span class="page-dots">...</span>';
                    }
                    echo '<a href="?page=' . $totalPages . $queryString . '" class="admin-btn-page">' . $totalPages . '</a>';
                }
                ?>
            </div>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?><?= $queryString ?>" class="admin-btn-page">Next ›</a>
                <a href="?page=<?= $totalPages ?><?= $queryString ?>" class="admin-btn-page">Last »</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter changes (with debounce for search)
    const filterForm = document.getElementById('filterForm');
    const filterSelects = filterForm.querySelectorAll('select');
    const searchInput = filterForm.querySelector('#search');
    
    // Auto-submit on select changes
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            filterForm.submit();
        });
    });
    
    // Debounced search
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (this.value.length >= 2 || this.value.length === 0) {
                filterForm.submit();
            }
        }, 800);
    });
    
    // Enhanced delete confirmation
    document.querySelectorAll('[data-weapon-name]').forEach(deleteBtn => {
        deleteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const weaponName = this.getAttribute('data-weapon-name');
            const confirmMessage = `Are you sure you want to delete "${weaponName}"?\n\nThis action cannot be undone.`;
            
            if (confirm(confirmMessage)) {
                // Create form and submit for proper POST handling
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'weapon_process.php';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'item_id';
                idInput.value = new URL(this.href).searchParams.get('id');
                
                form.appendChild(actionInput);
                form.appendChild(idInput);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});
</script>

<?php require_once '../../includes/footer.php'; ?>
