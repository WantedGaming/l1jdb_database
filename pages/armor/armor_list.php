<?php
require_once dirname(dirname(__DIR__)) . '/includes/config.php';
require_once dirname(dirname(__DIR__)) . '/includes/header.php';

// Call page header with title
getPageHeader('Armor');

// Render hero section
renderHero('armor');

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Filters
$typeFilter = isset($_GET['type']) ? $_GET['type'] : '';
$materialFilter = isset($_GET['material']) ? $_GET['material'] : '';
$classFilter = isset($_GET['class']) ? $_GET['class'] : '';

// Build WHERE clause
$whereConditions = [];
$params = [];

if (!empty($typeFilter)) {
    $whereConditions[] = "type = :type";
    $params[':type'] = $typeFilter;
}

if (!empty($materialFilter)) {
    $whereConditions[] = "material = :material";
    $params[':material'] = $materialFilter;
}

if (!empty($classFilter)) {
    switch($classFilter) {
        case 'royal':
            $whereConditions[] = "use_royal = 1";
            break;
        case 'knight':
            $whereConditions[] = "use_knight = 1";
            break;
        case 'mage':
            $whereConditions[] = "use_mage = 1";
            break;
        case 'elf':
            $whereConditions[] = "use_elf = 1";
            break;
        case 'darkelf':
            $whereConditions[] = "use_darkelf = 1";
            break;
        case 'dragonknight':
            $whereConditions[] = "use_dragonknight = 1";
            break;
        case 'illusionist':
            $whereConditions[] = "use_illusionist = 1";
            break;
        case 'warrior':
            $whereConditions[] = "use_warrior = 1";
            break;
        case 'fencer':
            $whereConditions[] = "use_fencer = 1";
            break;
        case 'lancer':
            $whereConditions[] = "use_lancer = 1";
            break;
    }
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Get total count
$countSql = "SELECT COUNT(*) FROM armor $whereClause";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalItems = $countStmt->fetchColumn();
$totalPages = ceil($totalItems / $limit);

// Get armor data
$sql = "SELECT item_id, desc_en, type, material, ac, m_def, safenchant, iconId 
        FROM armor 
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
$armors = $stmt->fetchAll();

// Get filter options
$typeSql = "SELECT DISTINCT type FROM armor ORDER BY type";
$typeStmt = $pdo->query($typeSql);
$types = $typeStmt->fetchAll(PDO::FETCH_COLUMN);

$materialSql = "SELECT DISTINCT material FROM armor ORDER BY material";
$materialStmt = $pdo->query($materialSql);
$materials = $materialStmt->fetchAll(PDO::FETCH_COLUMN);

$classes = [
    'royal' => 'Royal',
    'knight' => 'Knight',
    'mage' => 'Mage',
    'elf' => 'Elf',
    'darkelf' => 'Dark Elf',
    'dragonknight' => 'Dragon Knight',
    'illusionist' => 'Illusionist',
    'warrior' => 'Warrior',
    'fencer' => 'Fencer',
    'lancer' => 'Lancer'
];

// Note: Armor normalization functions are defined in includes/functions.php
?>

<main>
    <div class="main">
        <div class="container">
            <!-- Filters -->
            <div class="weapon-filters">
                <form method="GET" class="filter-form">
                    <div class="filter-group">
                        <label for="type">Type:</label>
                        <select name="type" id="type">
                            <option value="">All Types</option>
                            <?php foreach ($types as $type): ?>
                                <option value="<?= htmlspecialchars($type) ?>" <?= $typeFilter === $type ? 'selected' : '' ?>>
                                    <?= normalizeArmorType($type) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="material">Material:</label>
                        <select name="material" id="material">
                            <option value="">All Materials</option>
                            <?php foreach ($materials as $material): ?>
                                <option value="<?= htmlspecialchars($material) ?>" <?= $materialFilter === $material ? 'selected' : '' ?>>
                                    <?= normalizeArmorMaterial($material) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="class">Class:</label>
                        <select name="class" id="class">
                            <option value="">All Classes</option>
                            <?php foreach ($classes as $key => $name): ?>
                                <option value="<?= $key ?>" <?= $classFilter === $key ? 'selected' : '' ?>>
                                    <?= $name ?>
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
                Showing <?= count($armors) ?> of <?= $totalItems ?> armor pieces
            </div>
            
            <!-- Armor Table -->
            <div class="weapons-table">
                <table>
                    <thead>
                        <tr>
                            <th>Icon</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Material</th>
                            <th>AC</th>
                            <th>M.DEF</th>
                            <th>Safe</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($armors as $armor): ?>
                            <tr>
                                <td class="icon-cell">
                                    <img src="<?= SITE_URL ?>/assets/img/icons/<?= $armor['iconId'] ?>.png" 
                                         alt="Icon" 
                                         onerror="this.src='<?= SITE_URL ?>/assets/img/placeholders/0.png'">
                                </td>
                                <td class="name-cell">
                                    <a href="armor_detail.php?id=<?= $armor['item_id'] ?>" class="weapon-link">
                                        <?= htmlspecialchars(cleanDescriptionPrefix($armor['desc_en'])) ?>
                                    </a>
                                </td>
                                <td><?= normalizeArmorType($armor['type']) ?></td>
                                <td><?= normalizeArmorMaterial($armor['material']) ?></td>
                                <td><?= $armor['ac'] ?></td>
                                <td><?= $armor['m_def'] ?></td>
                                <td><?= $armor['safenchant'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>&type=<?= urlencode($typeFilter) ?>&material=<?= urlencode($materialFilter) ?>&class=<?= urlencode($classFilter) ?>" class="page-btn prev">Previous</a>
                    <?php endif; ?>
                    
                    <?php
                    $start = max(1, $page - 2);
                    $end = min($totalPages, $page + 2);
                    
                    if ($start > 1) {
                        echo '<a href="?page=1&type=' . urlencode($typeFilter) . '&material=' . urlencode($materialFilter) . '&class=' . urlencode($classFilter) . '" class="page-btn">1</a>';
                        if ($start > 2) echo '<span class="page-dots">...</span>';
                    }
                    
                    for ($i = $start; $i <= $end; $i++) {
                        $activeClass = $i === $page ? 'active' : '';
                        echo '<a href="?page=' . $i . '&type=' . urlencode($typeFilter) . '&material=' . urlencode($materialFilter) . '&class=' . urlencode($classFilter) . '" class="page-btn ' . $activeClass . '">' . $i . '</a>';
                    }
                    
                    if ($end < $totalPages) {
                        if ($end < $totalPages - 1) echo '<span class="page-dots">...</span>';
                        echo '<a href="?page=' . $totalPages . '&type=' . urlencode($typeFilter) . '&material=' . urlencode($materialFilter) . '&class=' . urlencode($classFilter) . '" class="page-btn">' . $totalPages . '</a>';
                    }
                    ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>&type=<?= urlencode($typeFilter) ?>&material=<?= urlencode($materialFilter) ?>&class=<?= urlencode($classFilter) ?>" class="page-btn next">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php getPageFooter(); ?>
