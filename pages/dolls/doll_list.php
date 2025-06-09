<?php
require_once dirname(dirname(__DIR__)) . '/includes/config.php';
require_once dirname(dirname(__DIR__)) . '/includes/header.php';

// Call page header with title
getPageHeader('Magic Dolls');

// Render hero section
renderHero('dolls');

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Filters
$searchFilter = isset($_GET['search']) ? trim($_GET['search']) : '';
$gradeFilter = isset($_GET['grade']) ? $_GET['grade'] : '';

// Build WHERE clause
$whereConditions = [];
$params = [];

if (!empty($searchFilter)) {
    $whereConditions[] = "(mi.name LIKE :search OR e.desc_en LIKE :search_desc)";
    $params[':search'] = '%' . $searchFilter . '%';
    $params[':search_desc'] = '%' . $searchFilter . '%';
}

if (!empty($gradeFilter)) {
    $whereConditions[] = "mi.grade = :grade";
    $params[':grade'] = $gradeFilter;
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Get total count
$countSql = "SELECT COUNT(*) 
             FROM magicdoll_info mi 
             LEFT JOIN etcitem e ON mi.itemId = e.item_id 
             $whereClause";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalItems = $countStmt->fetchColumn();
$totalPages = ceil($totalItems / $limit);

// Get dolls data
$sql = "SELECT mi.itemId, mi.name, mi.dollNpcId, mi.grade, mi.damageChance, mi.haste,
               e.desc_en, e.iconId, e.desc_kr
        FROM magicdoll_info mi 
        LEFT JOIN etcitem e ON mi.itemId = e.item_id 
        $whereClause 
        ORDER BY mi.grade DESC, mi.itemId 
        LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$dolls = $stmt->fetchAll();

// Get grade options
$gradeSql = "SELECT DISTINCT grade FROM magicdoll_info ORDER BY grade DESC";
$gradeStmt = $pdo->query($gradeSql);
$grades = $gradeStmt->fetchAll(PDO::FETCH_COLUMN);
?>

<main>
    <div class="main">
        <div class="container">
            <!-- Filters -->
            <div class="weapon-filters">
                <form method="GET" class="filter-form">
                    <div class="filter-group">
                        <label for="search">Search:</label>
                        <input type="text" name="search" id="search" placeholder="Search dolls..." value="<?= htmlspecialchars($searchFilter) ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label for="grade">Grade:</label>
                        <select name="grade" id="grade">
                            <option value="">All Grades</option>
                            <?php foreach ($grades as $grade): ?>
                                <option value="<?= $grade ?>" <?= $gradeFilter == $grade ? 'selected' : '' ?>>
                                <?= getDollGradeDisplay($grade) ?>
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
                Showing <?= count($dolls) ?> of <?= $totalItems ?> magic dolls
            </div>
            
            <!-- Dolls Table -->
            <div class="weapons-table">
                <table>
                    <thead>
                        <tr>
                            <th>Icon</th>
                            <th>Name</th>
                            <th>Grade</th>
                            <th>Attack Chance</th>
                            <th>Haste</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dolls as $doll): ?>
                            <tr>
                                <td class="icon-cell">
                                    <img src="<?= SITE_URL ?>/assets/img/icons/<?= $doll['iconId'] ?>.png" 
                                         alt="Icon" 
                                         onerror="this.src='<?= SITE_URL ?>/assets/img/placeholders/0.png'">
                                </td>
                                <td class="name-cell">
                                    <a href="doll_detail.php?id=<?= $doll['itemId'] ?>" class="weapon-link">
                                        <?= htmlspecialchars(cleanDollName($doll['name'])) ?>
                                    </a>
                                </td>
                                <td><?= getDollGradeDisplay($doll['grade']) ?></td>
                                <td><?= $doll['damageChance'] > 0 ? $doll['damageChance'] . '%' : 'None' ?></td>
                                <td><?= $doll['haste'] === 'true' ? 'Yes' : 'No' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($searchFilter) ?>&grade=<?= urlencode($gradeFilter) ?>" class="page-btn prev">Previous</a>
                    <?php endif; ?>
                    
                    <?php
                    $start = max(1, $page - 2);
                    $end = min($totalPages, $page + 2);
                    
                    if ($start > 1) {
                        echo '<a href="?page=1&search=' . urlencode($searchFilter) . '&grade=' . urlencode($gradeFilter) . '" class="page-btn">1</a>';
                        if ($start > 2) echo '<span class="page-dots">...</span>';
                    }
                    
                    for ($i = $start; $i <= $end; $i++) {
                        $activeClass = $i === $page ? 'active' : '';
                        echo '<a href="?page=' . $i . '&search=' . urlencode($searchFilter) . '&grade=' . urlencode($gradeFilter) . '" class="page-btn ' . $activeClass . '">' . $i . '</a>';
                    }
                    
                    if ($end < $totalPages) {
                        if ($end < $totalPages - 1) echo '<span class="page-dots">...</span>';
                        echo '<a href="?page=' . $totalPages . '&search=' . urlencode($searchFilter) . '&grade=' . urlencode($gradeFilter) . '" class="page-btn">' . $totalPages . '</a>';
                    }
                    ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($searchFilter) ?>&grade=<?= urlencode($gradeFilter) ?>" class="page-btn next">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php getPageFooter(); ?>
