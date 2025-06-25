<?php
require_once dirname(dirname(dirname(__DIR__))) . '/includes/config.php';
require_once dirname(dirname(dirname(__DIR__))) . '/admin/includes/header.php';

// Handle success/error messages
$message = '';
$messageType = '';

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'added':
            $message = 'Monster successfully created!';
            $messageType = 'success';
            break;
        case 'updated':
            $message = 'Monster successfully updated!';
            $messageType = 'success';
            break;
        case 'deleted':
            $name = $_GET['name'] ?? 'Unknown';
            $message = 'Monster "' . htmlspecialchars($name) . '" successfully deleted!';
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
            $message = 'Error deleting monster: ' . ($_GET['msg'] ?? 'Unknown error');
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
$levelFilter = isset($_GET['level']) ? $_GET['level'] : '';
$undeadFilter = isset($_GET['undead']) ? $_GET['undead'] : '';
$agroFilter = isset($_GET['agro']) ? $_GET['agro'] : '';
$implFilter = isset($_GET['impl']) ? $_GET['impl'] : '';
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'npcid';
$sortOrder = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';

// Build where clause
$whereConditions = [];
$params = [];

if (!empty($search)) {
    $whereConditions[] = "(n.desc_en LIKE ? OR n.desc_kr LIKE ? OR n.npcid = ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = is_numeric($search) ? $search : 0;
}

if (!empty($levelFilter)) {
    if ($levelFilter === '1-10') {
        $whereConditions[] = "n.lvl BETWEEN 1 AND 10";
    } elseif ($levelFilter === '11-30') {
        $whereConditions[] = "n.lvl BETWEEN 11 AND 30";
    } elseif ($levelFilter === '31-50') {
        $whereConditions[] = "n.lvl BETWEEN 31 AND 50";
    } elseif ($levelFilter === '51-70') {
        $whereConditions[] = "n.lvl BETWEEN 51 AND 70";
    } elseif ($levelFilter === '71+') {
        $whereConditions[] = "n.lvl >= 71";
    }
}

if (!empty($undeadFilter)) {
    $whereConditions[] = "n.undead = ?";
    $params[] = $undeadFilter;
}

if (!empty($agroFilter)) {
    $whereConditions[] = "n.is_agro = ?";
    $params[] = $agroFilter;
}

if (!empty($implFilter)) {
    $whereConditions[] = "n.impl = ?";
    $params[] = $implFilter;
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Validate sort column
$allowedSortColumns = ['npcid', 'desc_en', 'lvl', 'hp', 'mp', 'ac', 'exp', 'impl'];
if (!in_array($sortBy, $allowedSortColumns)) {
    $sortBy = 'npcid';
}

// Get total count for pagination
$countSql = "SELECT COUNT(*) FROM npc n $whereClause";
$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$totalItems = $stmt->fetchColumn();
$totalPages = ceil($totalItems / $perPage);

// Get monsters
$sql = "SELECT n.npcid, n.desc_en, n.desc_kr, n.lvl, n.hp, n.mp, n.ac, n.exp, 
               n.undead, n.weakAttr, n.is_agro, n.spriteId, n.impl, n.str, n.con, 
               n.dex, n.wis, n.intel, n.mr, n.alignment
        FROM npc n 
        $whereClause 
        ORDER BY n.$sortBy $sortOrder 
        LIMIT $perPage OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$monsters = $stmt->fetchAll();

// Get filter options
$implOptions = $pdo->query("SELECT DISTINCT impl FROM npc WHERE impl != '' ORDER BY impl")->fetchAll(PDO::FETCH_COLUMN);
$undeadOptions = $pdo->query("SELECT DISTINCT undead FROM npc WHERE undead != '' ORDER BY undead")->fetchAll(PDO::FETCH_COLUMN);

// Helper functions
function getUndeadTypes() {
    return [
        'NONE' => 'Normal',
        'UNDEAD' => 'Undead',
        'DEMON' => 'Demon',
        'UNDEAD_BOSS' => 'Undead Boss',
        'DRANIUM' => 'Dranium'
    ];
}

function getImplementations() {
    return [
        'L1Monster' => 'Monster',
        'L1BlackKnight' => 'Black Knight',
        'L1Doppelganger' => 'Doppelganger',
        'L1GuardianTower' => 'Guardian Tower',
        'L1Merchant' => 'Merchant',
        'L1Npc' => 'NPC',
        'L1TeleporterNpc' => 'Teleporter'
    ];
}
?>

<div class="admin-content-wrapper">
    <!-- Admin Header -->
    <div class="admin-header">
        <h1>Monster Management</h1>
        <div class="admin-header-actions">
            <a href="monster_add.php" class="admin-btn admin-btn-primary">
                <span class="admin-icon admin-icon-spawn"></span> Add New Monster
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
            <li class="breadcrumb-item">Monster Management</li>
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
            <h3>Total Monsters</h3>
            <div class="stat-number"><?= number_format($totalItems) ?></div>
            <div class="stat-label">Database entries</div>
        </div>
        <div class="stat-card">
            <h3>Implementation Types</h3>
            <div class="stat-number"><?= count($implOptions) ?></div>
            <div class="stat-label">Different types</div>
        </div>
        <div class="stat-card">
            <h3>Boss Monsters</h3>
            <div class="stat-number">
                <?php
                $bossCount = 0;
                foreach ($monsters as $monster) {
                    if ($monster['undead'] === 'UNDEAD_BOSS' || $monster['lvl'] >= 70) $bossCount++;
                }
                echo $bossCount;
                ?>
            </div>
            <div class="stat-label">High level/boss</div>
        </div>
        <div class="stat-card">
            <h3>Current Page</h3>
            <div class="stat-number"><?= $page ?> / <?= $totalPages ?></div>
            <div class="stat-label">Page navigation</div>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="admin-filters">
        <h3>Filter & Search Monsters</h3>
        <form method="GET" class="filter-form" id="filterForm">
            <div class="filter-group">
                <label for="search">Search</label>
                <div class="admin-search">
                    <input type="text" id="search" name="search" value="<?= htmlspecialchars($search) ?>" 
                           placeholder="Name, ID, or description..." class="search-input">
                </div>
            </div>
            
            <div class="filter-group">
                <label for="level">Level Range</label>
                <select id="level" name="level">
                    <option value="">All Levels</option>
                    <option value="1-10" <?= $levelFilter === '1-10' ? 'selected' : '' ?>>1-10 (Beginner)</option>
                    <option value="11-30" <?= $levelFilter === '11-30' ? 'selected' : '' ?>>11-30 (Low)</option>
                    <option value="31-50" <?= $levelFilter === '31-50' ? 'selected' : '' ?>>31-50 (Mid)</option>
                    <option value="51-70" <?= $levelFilter === '51-70' ? 'selected' : '' ?>>51-70 (High)</option>
                    <option value="71+" <?= $levelFilter === '71+' ? 'selected' : '' ?>>71+ (Boss)</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="undead">Undead Type</label>
                <select id="undead" name="undead">
                    <option value="">All Types</option>
                    <?php 
                    $undeadTypes = getUndeadTypes();
                    foreach ($undeadOptions as $undead): 
                    ?>
                        <option value="<?= htmlspecialchars($undead) ?>" <?= $undeadFilter === $undead ? 'selected' : '' ?>>
                            <?= htmlspecialchars($undeadTypes[$undead] ?? $undead) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="agro">Aggressive</label>
                <select id="agro" name="agro">
                    <option value="">All</option>
                    <option value="true" <?= $agroFilter === 'true' ? 'selected' : '' ?>>Yes</option>
                    <option value="false" <?= $agroFilter === 'false' ? 'selected' : '' ?>>No</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="impl">Implementation</label>
                <select id="impl" name="impl">
                    <option value="">All Types</option>
                    <?php 
                    $implTypes = getImplementations();
                    foreach ($implOptions as $impl): 
                    ?>
                        <option value="<?= htmlspecialchars($impl) ?>" <?= $implFilter === $impl ? 'selected' : '' ?>>
                            <?= htmlspecialchars($implTypes[$impl] ?? $impl) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="sort">Sort By</label>
                <select id="sort" name="sort">
                    <option value="npcid" <?= $sortBy === 'npcid' ? 'selected' : '' ?>>ID</option>
                    <option value="desc_en" <?= $sortBy === 'desc_en' ? 'selected' : '' ?>>Name</option>
                    <option value="lvl" <?= $sortBy === 'lvl' ? 'selected' : '' ?>>Level</option>
                    <option value="hp" <?= $sortBy === 'hp' ? 'selected' : '' ?>>HP</option>
                    <option value="exp" <?= $sortBy === 'exp' ? 'selected' : '' ?>>Experience</option>
                    <option value="impl" <?= $sortBy === 'impl' ? 'selected' : '' ?>>Type</option>
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
                <a href="monster_list.php" class="admin-btn admin-btn-secondary">Clear All</a>
            </div>
        </form>
    </div>

    <!-- Results Info -->
    <div class="results-info">
        Showing <?= count($monsters) ?> of <?= number_format($totalItems) ?> monsters
        <?php if ($page > 1 || $totalPages > 1): ?>
            (Page <?= $page ?> of <?= $totalPages ?>)
        <?php endif; ?>
        <?php if (!empty($search) || !empty($levelFilter) || !empty($undeadFilter) || !empty($agroFilter) || !empty($implFilter)): ?>
            - <strong>Filtered results</strong>
        <?php endif; ?>
    </div>

    <!-- Monsters Table -->
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th class="table-cell-icon">Icon</th>
                    <th class="table-cell-id">
                        <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'npcid', 'order' => $sortBy === 'npcid' && $sortOrder === 'ASC' ? 'desc' : 'asc'])) ?>">
                            ID <?= $sortBy === 'npcid' ? ($sortOrder === 'ASC' ? '↑' : '↓') : '' ?>
                        </a>
                    </th>
                    <th class="table-cell-name">
                        <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'desc_en', 'order' => $sortBy === 'desc_en' && $sortOrder === 'ASC' ? 'desc' : 'asc'])) ?>">
                            Name <?= $sortBy === 'desc_en' ? ($sortOrder === 'ASC' ? '↑' : '↓') : '' ?>
                        </a>
                    </th>
                    <th>Korean Name</th>
                    <th>
                        <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'lvl', 'order' => $sortBy === 'lvl' && $sortOrder === 'ASC' ? 'desc' : 'asc'])) ?>">
                            Level <?= $sortBy === 'lvl' ? ($sortOrder === 'ASC' ? '↑' : '↓') : '' ?>
                        </a>
                    </th>
                    <th>HP/MP</th>
                    <th>Stats</th>
                    <th>Type</th>
                    <th class="table-cell-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($monsters)): ?>
                    <tr>
                        <td colspan="9">
                            <div class="admin-empty">
                                <h3>No Monsters Found</h3>
                                <p>No monsters match your current search criteria.</p>
                                <a href="monster_add.php" class="admin-btn admin-btn-primary">Add First Monster</a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($monsters as $monster): ?>
                        <tr class="clickable-row" onclick="window.open('/l1jdb_database/pages/monsters/monster_detail.php?id=<?= $monster['npcid'] ?>', '_blank')">
                            <td class="icon-cell">
                                <img src="/l1jdb_database/assets/img/icons/ms<?= $monster['spriteId'] ?>.png" 
                                     alt="<?= htmlspecialchars($monster['desc_en']) ?>" 
                                     onerror="this.src='/l1jdb_database/assets/img/icons/ms<?= $monster['spriteId'] ?>.gif'; this.onerror=function(){this.src='/l1jdb_database/assets/img/placeholders/monsters.png';}"
                                     title="Sprite ID: <?= $monster['spriteId'] ?>">
                            </td>
                            <td class="table-cell-id"><?= $monster['npcid'] ?></td>
                            <td class="table-cell-name">
                                <div>
                                    <?= htmlspecialchars($monster['desc_en']) ?>
                                    <?php if ($monster['undead'] !== 'NONE'): ?>
                                        <span class="badge badge-<?= $monster['undead'] === 'UNDEAD_BOSS' ? 'danger' : ($monster['undead'] === 'DEMON' ? 'warning' : 'info') ?>">
                                            <?= getUndeadTypes()[$monster['undead']] ?? $monster['undead'] ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-small">
                                <?= !empty($monster['desc_kr']) ? htmlspecialchars($monster['desc_kr']) : '<span class="text-muted">-</span>' ?>
                            </td>
                            <td class="table-cell-number">
                                <strong><?= $monster['lvl'] ?></strong>
                                <?php if ($monster['is_agro'] == 'true'): ?>
                                    <br><span class="text-negative">Aggressive</span>
                                <?php endif; ?>
                            </td>
                            <td class="table-cell-number">
                                <div class="text-small">
                                    <strong>HP:</strong> <?= number_format($monster['hp']) ?><br>
                                    <strong>MP:</strong> <?= number_format($monster['mp']) ?><br>
                                    <strong>AC:</strong> <?= $monster['ac'] ?>
                                </div>
                            </td>
                            <td class="text-small">
                                <?php 
                                $stats = [];
                                if ($monster['str'] > 0) $stats[] = "STR: {$monster['str']}";
                                if ($monster['con'] > 0) $stats[] = "CON: {$monster['con']}";
                                if ($monster['dex'] > 0) $stats[] = "DEX: {$monster['dex']}";
                                if ($monster['wis'] > 0) $stats[] = "WIS: {$monster['wis']}";
                                if ($monster['intel'] > 0) $stats[] = "INT: {$monster['intel']}";
                                if ($monster['mr'] > 0) $stats[] = "MR: {$monster['mr']}";
                                
                                if (!empty($stats)) {
                                    echo '<div class="text-positive">' . implode('<br>', array_slice($stats, 0, 3)) . '</div>';
                                    if (count($stats) > 3) {
                                        echo '<div class="text-muted">+' . (count($stats) - 3) . ' more</div>';
                                    }
                                } else {
                                    echo '<span class="text-muted">Basic stats</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    <?= getImplementations()[$monster['impl']] ?? $monster['impl'] ?>
                                </span>
                                <?php if ($monster['weakAttr'] !== 'NONE'): ?>
                                    <br><span class="text-small text-muted">Weak: <?= ucfirst(strtolower($monster['weakAttr'])) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="table-cell-actions">
                                <div class="btn-group">
                                    <a href="monster_edit.php?id=<?= $monster['npcid'] ?>" 
                                       class="admin-btn admin-btn-small admin-btn-secondary" 
                                       title="Edit Monster"
                                       onclick="event.stopPropagation()">
                                        ✏️
                                    </a>
                                    <a href="monster_delete.php?id=<?= $monster['npcid'] ?>" 
                                       class="admin-btn admin-btn-small admin-btn-danger" 
                                       title="Delete Monster"
                                       onclick="event.stopPropagation(); return confirm('Are you sure you want to delete this monster?')"
                                       data-monster-name="<?= htmlspecialchars($monster['desc_en']) ?>">
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
    document.querySelectorAll('[data-monster-name]').forEach(deleteBtn => {
        deleteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const monsterName = this.getAttribute('data-monster-name');
            const confirmMessage = `Are you sure you want to delete "${monsterName}"?\n\nThis action cannot be undone.`;
            
            if (confirm(confirmMessage)) {
                // Create form and submit for proper POST handling
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'monster_process.php';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'npcid';
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
