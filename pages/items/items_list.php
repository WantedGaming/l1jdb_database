<?php
require_once dirname(dirname(__DIR__)) . '/includes/config.php';
require_once dirname(dirname(__DIR__)) . '/includes/header.php';

// Call page header with title
getPageHeader('Items');

// Render hero section
renderHero('items');

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Filters
$typeFilter = isset($_GET['type']) ? $_GET['type'] : '';

// Build WHERE clause
$whereConditions = [];
$params = [];

if (!empty($typeFilter)) {
    $whereConditions[] = "item_type = :type";
    $params[':type'] = $typeFilter;
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Get total count
$countSql = "SELECT COUNT(*) FROM etcitem $whereClause";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalItems = $countStmt->fetchColumn();
$totalPages = ceil($totalItems / $limit);

// Get items data
$sql = "SELECT item_id, desc_en, item_type, material, weight, iconId, merge, bless 
        FROM etcitem 
        $whereClause 
        ORDER BY item_id 
        LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$items = $stmt->fetchAll();

// Get filter options
$typeSql = "SELECT DISTINCT item_type FROM etcitem ORDER BY item_type";
$typeStmt = $pdo->query($typeSql);
$types = $typeStmt->fetchAll(PDO::FETCH_COLUMN);
?>

<main>
    <div class="main">
        <div class="container">
            <!-- Filters -->
            <div class="weapon-filters">
                <form method="GET" class="filter-form">
                    <div class="filter-group">
                        <label for="type">Item Type:</label>
                        <select name="type" id="type">
                            <option value="">All Types</option>
                            <?php foreach ($types as $type): ?>
                                <option value="<?= htmlspecialchars($type) ?>" <?= $typeFilter === $type ? 'selected' : '' ?>>
                                    <?= normalizeItemType($type) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="filter-btn">Filter</button>
                    <a href="?" class="clear-btn">Clear</a>
                </form>
            </div>
            
            <!-- Results info -->
            <div class="results-info">
                Showing <?= count($items) ?> of <?= $totalItems ?> items
            </div>
            
            <!-- Items Table -->
            <div class="weapons-table">
                <table>
                    <thead>
                        <tr>
                            <th>Icon</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Material</th>
                            <th>Weight</th>
                            <th>Mergeable</th>
                            <th>Bless</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td class="icon-cell">
                                    <img src="<?= SITE_URL ?>/assets/img/icons/<?= $item['iconId'] ?>.png" 
                                         alt="Icon" 
                                         onerror="this.src='<?= SITE_URL ?>/assets/img/placeholders/0.png'">
                                </td>
                                <td class="name-cell">
                                    <a href="items_detail.php?id=<?= $item['item_id'] ?>" class="weapon-link">
                                        <?= htmlspecialchars(cleanDescriptionPrefix($item['desc_en'])) ?>
                                    </a>
                                </td>
                                <td><?= normalizeItemType($item['item_type']) ?></td>
                                <td><?= normalizeWeaponMaterial($item['material']) ?></td>
                                <td><?= number_format($item['weight']) ?></td>
                                <td><?= $item['merge'] === 'true' ? 'Yes' : 'No' ?></td>
                                <td><?= $item['bless'] == 1 ? 'Normal' : ($item['bless'] == 0 ? 'Cursed' : 'Blessed') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>&type=<?= urlencode($typeFilter) ?>" class="page-btn prev">Previous</a>
                    <?php endif; ?>
                    
                    <?php
                    $start = max(1, $page - 2);
                    $end = min($totalPages, $page + 2);
                    
                    if ($start > 1) {
                        echo '<a href="?page=1&type=' . urlencode($typeFilter) . '" class="page-btn">1</a>';
                        if ($start > 2) echo '<span class="page-dots">...</span>';
                    }
                    
                    for ($i = $start; $i <= $end; $i++) {
                        $activeClass = $i === $page ? 'active' : '';
                        echo '<a href="?page=' . $i . '&type=' . urlencode($typeFilter) . '" class="page-btn ' . $activeClass . '">' . $i . '</a>';
                    }
                    
                    if ($end < $totalPages) {
                        if ($end < $totalPages - 1) echo '<span class="page-dots">...</span>';
                        echo '<a href="?page=' . $totalPages . '&type=' . urlencode($typeFilter) . '" class="page-btn">' . $totalPages . '</a>';
                    }
                    ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>&type=<?= urlencode($typeFilter) ?>" class="page-btn next">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php getPageFooter(); ?>
