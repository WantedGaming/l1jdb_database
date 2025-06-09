<?php
require_once dirname(dirname(__DIR__)) . '/includes/config.php';
require_once dirname(dirname(__DIR__)) . '/includes/header.php';

// Function to normalize zone types
function getZoneTypeLabel($map) {
    $zones = [];
    
    if ($map['beginZone']) $zones[] = 'Beginner Zone';
    if ($map['redKnightZone']) $zones[] = 'Red Knight Zone';
    if ($map['ruunCastleZone']) $zones[] = 'Ruun Castle Zone';
    if ($map['interWarZone']) $zones[] = 'War Zone';
    if ($map['geradBuffZone']) $zones[] = 'Gerad Buff Zone';
    if ($map['growBuffZone']) $zones[] = 'Growth Buff Zone';
    if ($map['dungeon']) $zones[] = 'Dungeon';
    if ($map['underwater']) $zones[] = 'Underwater';
    
    return !empty($zones) ? implode(', ', $zones) : 'Normal Zone';
}

// Function to get restrictions
function getMapRestrictions($map) {
    $restrictions = [];
    
    if (!$map['markable']) $restrictions[] = 'No Mark';
    if (!$map['teleportable']) $restrictions[] = 'No Teleport';
    if (!$map['escapable']) $restrictions[] = 'No Escape';
    if (!$map['resurrection']) $restrictions[] = 'No Resurrection';
    if (!$map['painwand']) $restrictions[] = 'No Pain Wand';
    if (!$map['take_pets']) $restrictions[] = 'No Pets';
    if (!$map['recall_pets']) $restrictions[] = 'No Pet Recall';
    if (!$map['usable_item']) $restrictions[] = 'No Items';
    if (!$map['usable_skill']) $restrictions[] = 'No Skills';
    if ($map['penalty']) $restrictions[] = 'Death Penalty';
    if ($map['decreaseHp']) $restrictions[] = 'HP Decrease';
    
    return $restrictions;
}

// Pagination settings
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 25;
$offset = ($page - 1) * $perPage;

// Search and filter parameters
$searchName = isset($_GET['search']) ? trim($_GET['search']) : '';
$filterZone = isset($_GET['zone']) ? $_GET['zone'] : '';
$filterDungeon = isset($_GET['dungeon']) ? $_GET['dungeon'] : '';

// Build query
$where = "WHERE 1=1";
$params = [];

if (!empty($searchName)) {
    $where .= " AND (locationname LIKE :search OR desc_kr LIKE :search)";
    $params[':search'] = '%' . $searchName . '%';
}

if ($filterDungeon !== '') {
    $where .= " AND dungeon = :dungeon";
    $params[':dungeon'] = (int)$filterDungeon;
}

if (!empty($filterZone)) {
    switch ($filterZone) {
        case 'beginner':
            $where .= " AND beginZone = 1";
            break;
        case 'redknight':
            $where .= " AND redKnightZone = 1";
            break;
        case 'ruun':
            $where .= " AND ruunCastleZone = 1";
            break;
        case 'war':
            $where .= " AND interWarZone = 1";
            break;
        case 'underwater':
            $where .= " AND underwater = 1";
            break;
    }
}

// Get total count for pagination
$countSql = "SELECT COUNT(*) FROM mapids $where";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalMaps = $countStmt->fetchColumn();
$totalPages = ceil($totalMaps / $perPage);

// Get maps data
$sql = "SELECT * FROM mapids $where ORDER BY mapid ASC LIMIT :offset, :limit";
$stmt = $pdo->prepare($sql);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);

$stmt->execute();
$maps = $stmt->fetchAll();

// Call page header
getPageHeader('Maps');

// Render hero section
renderHero('maps', 'Maps Database', 'Explore the world of Lineage');
?>

<main>
    <div class="main">
        <div class="container">
            <!-- Filters -->
            <div class="weapon-filters">
                <form method="GET" class="filter-form">
                    <div class="filter-group">
                        <label for="search">Search:</label>
                        <input type="text" id="search" name="search" value="<?= htmlspecialchars($searchName) ?>" placeholder="Search maps...">
                    </div>
                    
                    <div class="filter-group">
                        <label for="zone">Zone Type:</label>
                        <select id="zone" name="zone">
                            <option value="">All Zones</option>
                            <option value="beginner" <?= $filterZone === 'beginner' ? 'selected' : '' ?>>Beginner Zone</option>
                            <option value="redknight" <?= $filterZone === 'redknight' ? 'selected' : '' ?>>Red Knight Zone</option>
                            <option value="ruun" <?= $filterZone === 'ruun' ? 'selected' : '' ?>>Ruun Castle Zone</option>
                            <option value="war" <?= $filterZone === 'war' ? 'selected' : '' ?>>War Zone</option>
                            <option value="underwater" <?= $filterZone === 'underwater' ? 'selected' : '' ?>>Underwater</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="dungeon">Map Type:</label>
                        <select id="dungeon" name="dungeon">
                            <option value="">All Types</option>
                            <option value="0" <?= $filterDungeon === '0' ? 'selected' : '' ?>>Open World</option>
                            <option value="1" <?= $filterDungeon === '1' ? 'selected' : '' ?>>Dungeon</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="filter-btn">Filter</button>
                    <a href="map_list.php" class="clear-btn">Clear</a>
                </form>
            </div>

            <!-- Results info -->
            <div class="results-info">
                Showing <?= number_format(count($maps)) ?> of <?= number_format($totalMaps) ?> maps
            </div>

            <!-- Maps Table -->
            <div class="weapons-table">
                <table>
                    <thead>
                        <tr>
                            <th>Icon</th>
                            <th>Name</th>
                            <th>Zone Type</th>
                            <th>Restrictions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($maps as $map): ?>
                        <tr>
                            <td class="icon-cell">
                                <img src="<?= SITE_URL ?>/assets/img/icons/<?= $map['pngId'] ?: 'default-map' ?>.png" 
                                     alt="<?= htmlspecialchars($map['locationname'] ?: $map['desc_kr'] ?: 'Map ' . $map['mapid']) ?>"
                                     onerror="this.src='<?= SITE_URL ?>/assets/img/icons/<?= $map['pngId'] ?: 'default-map' ?>.jpeg'; this.onerror=function(){this.src='<?= SITE_URL ?>/assets/img/placeholders/noimage.png';}">
                            </td>
                            <td class="name-cell">
                                <a href="map_detail.php?id=<?= $map['mapid'] ?>" class="weapon-link">
                                    <?= htmlspecialchars($map['locationname'] ?: 'Map ' . $map['mapid']) ?>
                                </a>
                            </td>
                            <td><?= getZoneTypeLabel($map) ?></td>
                            <td>
                                <?php 
                                $restrictions = getMapRestrictions($map);
                                if (empty($restrictions)): ?>
                                    <span style="color: #2ecc71;">None</span>
                                <?php else: ?>
                                    <span style="color: #e74c3c;" title="<?= implode(', ', $restrictions) ?>">
                                        <?= count($restrictions) ?> restriction<?= count($restrictions) > 1 ? 's' : '' ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>" class="page-btn">&laquo;</a>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" class="page-btn">&lsaquo;</a>
                <?php endif; ?>

                <?php
                $start = max(1, $page - 2);
                $end = min($totalPages, $page + 2);
                
                if ($start > 1): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>" class="page-btn">1</a>
                    <?php if ($start > 2): ?>
                        <span class="page-dots">...</span>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                       class="page-btn <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($end < $totalPages): ?>
                    <?php if ($end < $totalPages - 1): ?>
                        <span class="page-dots">...</span>
                    <?php endif; ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $totalPages])) ?>" class="page-btn"><?= $totalPages ?></a>
                <?php endif; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" class="page-btn">&rsaquo;</a>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $totalPages])) ?>" class="page-btn">&raquo;</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php getPageFooter(); ?>
