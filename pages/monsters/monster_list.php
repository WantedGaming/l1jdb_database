<?php
require_once dirname(dirname(__DIR__)) . '/includes/config.php';
require_once dirname(dirname(__DIR__)) . '/includes/header.php';

// Call page header with title
getPageHeader('Monsters');

// Render hero section
renderHero('monsters');

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Filters
$searchFilter = isset($_GET['search']) ? trim($_GET['search']) : '';
$levelMinFilter = isset($_GET['level_min']) ? (int)$_GET['level_min'] : '';
$levelMaxFilter = isset($_GET['level_max']) ? (int)$_GET['level_max'] : '';
$undeadFilter = isset($_GET['undead']) ? $_GET['undead'] : '';
$weakAttrFilter = isset($_GET['weak_attr']) ? $_GET['weak_attr'] : '';
$agroFilter = isset($_GET['agro']) ? $_GET['agro'] : '';

// Build WHERE clause - only include valid monster implementations
$whereConditions = ["impl IN ('L1Monster', 'L1BlackKnight', 'L1Doppelganger')"];
$params = [];

if (!empty($searchFilter)) {
    $whereConditions[] = "desc_en LIKE :search";
    $params[':search'] = '%' . $searchFilter . '%';
}

if (!empty($levelMinFilter)) {
    $whereConditions[] = "lvl >= :level_min";
    $params[':level_min'] = $levelMinFilter;
}

if (!empty($levelMaxFilter)) {
    $whereConditions[] = "lvl <= :level_max";
    $params[':level_max'] = $levelMaxFilter;
}

if (!empty($undeadFilter)) {
    $whereConditions[] = "undead = :undead";
    $params[':undead'] = $undeadFilter;
}

if (!empty($weakAttrFilter)) {
    $whereConditions[] = "weakAttr = :weak_attr";
    $params[':weak_attr'] = $weakAttrFilter;
}

if (!empty($agroFilter)) {
    $whereConditions[] = "is_agro = :agro";
    $params[':agro'] = $agroFilter;
}

$whereClause = 'WHERE ' . implode(' AND ', $whereConditions);

// Get total count
$countSql = "SELECT COUNT(*) FROM npc $whereClause";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalItems = $countStmt->fetchColumn();
$totalPages = ceil($totalItems / $limit);

// Get monsters data
$sql = "SELECT npcid, desc_en, lvl, hp, mp, ac, exp, undead, weakAttr, is_agro, spriteId 
        FROM npc 
        $whereClause 
        ORDER BY lvl, npcid 
        LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$monsters = $stmt->fetchAll();

// Get filter options
$undeadSql = "SELECT DISTINCT undead FROM npc WHERE impl IN ('L1Monster', 'L1BlackKnight', 'L1Doppelganger') AND undead != '' ORDER BY undead";
$undeadStmt = $pdo->query($undeadSql);
$undeadTypes = $undeadStmt->fetchAll(PDO::FETCH_COLUMN);

$weakAttrSql = "SELECT DISTINCT weakAttr FROM npc WHERE impl IN ('L1Monster', 'L1BlackKnight', 'L1Doppelganger') AND weakAttr != '' ORDER BY weakAttr";
$weakAttrStmt = $pdo->query($weakAttrSql);
$weakAttrs = $weakAttrStmt->fetchAll(PDO::FETCH_COLUMN);
?>

<main>
    <div class="main">
        <div class="container">
            <!-- Filters -->
            <div class="weapon-filters">
                <form method="GET" class="filter-form">
                    <div class="filter-group">
                        <label for="search">Search:</label>
                        <input type="text" name="search" id="search" placeholder="Search monsters..." value="<?= htmlspecialchars($searchFilter) ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label for="level_min">Min Level:</label>
                        <select name="level_min" id="level_min">
                            <option value="">Any</option>
                            <?php for ($i = 1; $i <= 100; $i += 5): ?>
                                <option value="<?= $i ?>" <?= $levelMinFilter === $i ? 'selected' : '' ?>>
                                    <?= $i ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="level_max">Max Level:</label>
                        <select name="level_max" id="level_max">
                            <option value="">Any</option>
                            <?php for ($i = 5; $i <= 100; $i += 5): ?>
                                <option value="<?= $i ?>" <?= $levelMaxFilter === $i ? 'selected' : '' ?>>
                                    <?= $i ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="undead">Undead Type:</label>
                        <select name="undead" id="undead">
                            <option value="">All Types</option>
                            <?php foreach ($undeadTypes as $undead): ?>
                                <option value="<?= htmlspecialchars($undead) ?>" <?= $undeadFilter === $undead ? 'selected' : '' ?>>
                                    <?= ucfirst(strtolower($undead)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="weak_attr">Weak Attribute:</label>
                        <select name="weak_attr" id="weak_attr">
                            <option value="">All Attributes</option>
                            <?php foreach ($weakAttrs as $attr): ?>
                                <option value="<?= htmlspecialchars($attr) ?>" <?= $weakAttrFilter === $attr ? 'selected' : '' ?>>
                                    <?= ucfirst(strtolower($attr)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="agro">Aggressive:</label>
                        <select name="agro" id="agro">
                            <option value="">All</option>
                            <option value="true" <?= $agroFilter === 'true' ? 'selected' : '' ?>>Yes</option>
                            <option value="false" <?= $agroFilter === 'false' ? 'selected' : '' ?>>No</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="filter-btn">Filter</button>
                    <a href="?" class="clear-btn">Clear</a>
                </form>
            </div>
            
            <!-- Results info -->
            <div class="results-info">
                Showing <?= count($monsters) ?> of <?= $totalItems ?> monsters
            </div>
            
            <!-- Monsters Table -->
            <div class="weapons-table">
                <table>
                    <thead>
                        <tr>
                            <th>Icon</th>
                            <th>Name</th>
                            <th>Level</th>
                            <th>HP</th>
                            <th>MP</th>
                            <th>AC</th>
                            <th>Experience</th>
                            <th>Undead</th>
                            <th>Aggressive</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($monsters as $monster): ?>
                            <tr>
                                <td class="icon-cell">
                                    <img src="<?= SITE_URL ?>/assets/img/icons/ms<?= $monster['spriteId'] ?>.png" 
                                         alt="Icon" 
                                         onerror="this.src='<?= SITE_URL ?>/assets/img/icons/ms<?= $monster['spriteId'] ?>.gif'; this.onerror=function(){this.src='<?= SITE_URL ?>/assets/img/placeholders/monsters.png';}">
                                </td>
                                <td class="name-cell">
                                    <a href="monster_detail.php?id=<?= $monster['npcid'] ?>" class="weapon-link">
                                        <?= htmlspecialchars(cleanDescriptionPrefix($monster['desc_en'])) ?>
                                    </a>
                                </td>
                                <td><?= $monster['lvl'] ?></td>
                                <td><?= number_format($monster['hp']) ?></td>
                                <td><?= number_format($monster['mp']) ?></td>
                                <td><?= $monster['ac'] ?></td>
                                <td><?= number_format($monster['exp']) ?></td>
                                <td><?= $monster['undead'] == 'NONE' ? '-' : ucfirst(strtolower($monster['undead'])) ?></td>
                                <td><?= $monster['is_agro'] == 'true' ? 'Yes' : 'No' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($searchFilter) ?>&level_min=<?= urlencode($levelMinFilter) ?>&level_max=<?= urlencode($levelMaxFilter) ?>&undead=<?= urlencode($undeadFilter) ?>&weak_attr=<?= urlencode($weakAttrFilter) ?>&agro=<?= urlencode($agroFilter) ?>" class="page-btn prev">Previous</a>
                    <?php endif; ?>
                    
                    <?php
                    $start = max(1, $page - 2);
                    $end = min($totalPages, $page + 2);
                    
                    $urlParams = '&search=' . urlencode($searchFilter) . '&level_min=' . urlencode($levelMinFilter) . '&level_max=' . urlencode($levelMaxFilter) . '&undead=' . urlencode($undeadFilter) . '&weak_attr=' . urlencode($weakAttrFilter) . '&agro=' . urlencode($agroFilter);
                    
                    if ($start > 1) {
                        echo '<a href="?page=1' . $urlParams . '" class="page-btn">1</a>';
                        if ($start > 2) echo '<span class="page-dots">...</span>';
                    }
                    
                    for ($i = $start; $i <= $end; $i++) {
                        $activeClass = $i === $page ? 'active' : '';
                        echo '<a href="?page=' . $i . $urlParams . '" class="page-btn ' . $activeClass . '">' . $i . '</a>';
                    }
                    
                    if ($end < $totalPages) {
                        if ($end < $totalPages - 1) echo '<span class="page-dots">...</span>';
                        echo '<a href="?page=' . $totalPages . $urlParams . '" class="page-btn">' . $totalPages . '</a>';
                    }
                    ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($searchFilter) ?>&level_min=<?= urlencode($levelMinFilter) ?>&level_max=<?= urlencode($levelMaxFilter) ?>&undead=<?= urlencode($undeadFilter) ?>&weak_attr=<?= urlencode($weakAttrFilter) ?>&agro=<?= urlencode($agroFilter) ?>" class="page-btn next">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php getPageFooter(); ?>
