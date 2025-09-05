<?php
require_once '../../includes/functions.php';

// Get current page
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$itemsPerPage = 20;

// Get filter values
$filters = [];
if (!empty($_GET['name'])) {
    $filters['name'] = sanitizeInput($_GET['name']);
}
if (!empty($_GET['class'])) {
    $filters['class'] = sanitizeInput($_GET['class']);
}
if (!empty($_GET['material'])) {
    $filters['material'] = sanitizeInput($_GET['material']);
}

// Get weapons data
$weapons = getWeapons($filters, $currentPage, $itemsPerPage);
$totalWeapons = getWeaponsCount($filters);
$totalPages = ceil($totalWeapons / $itemsPerPage);

// Get filter options
$weaponClasses = getWeaponClasses();
$weaponMaterials = getWeaponMaterials();

// Build query string for pagination
$queryParams = [];
if (!empty($filters['name'])) $queryParams['name'] = $filters['name'];
if (!empty($filters['class'])) $queryParams['class'] = $filters['class'];
if (!empty($filters['material'])) $queryParams['material'] = $filters['material'];
$queryString = !empty($queryParams) ? '&' . http_build_query($queryParams) : '';
$baseUrl = 'weapon-list.php';

$basePath = getBasePath();
?>

<?php echo generateHeader("Weapons - L1j-R Database"); ?>

<?php echo generateBreadcrumb([
    '../weapons/weapon-list.php' => 'Weapons'
]); ?>

<!-- Hero Section -->
<section class="weapon-hero">
    <div class="hero-content">
        <h1>Weapon Database</h1>
        <p>Discover and explore all weapons in the L1j-R universe</p>
    </div>
</section>

<!-- Main Content -->
<main class="content-section">
    <div class="container">
        
        <!-- Filter Section -->
        <div class="filter-container">
            <form class="filter-form" method="GET" action="">
                <div class="filter-group">
                    <label for="name">Name</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           class="filter-input" 
                           placeholder="Search by name..." 
                           value="<?php echo isset($filters['name']) ? htmlspecialchars($filters['name']) : ''; ?>">
                </div>
                
                <div class="filter-group">
                    <label for="class">Class</label>
                    <select id="class" name="class" class="filter-select">
                        <option value="">All Classes</option>
                        <?php foreach ($weaponClasses as $class): ?>
                            <option value="<?php echo htmlspecialchars($class); ?>" 
                                    <?php echo (isset($filters['class']) && $filters['class'] === $class) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($class); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="material">Material</label>
                    <select id="material" name="material" class="filter-select">
                        <option value="">All Materials</option>
                        <?php foreach ($weaponMaterials as $material): ?>
                            <option value="<?php echo htmlspecialchars($material); ?>" 
                                    <?php echo (isset($filters['material']) && $filters['material'] === $material) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars(normalizeMaterial($material)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="filter-btn">Filter</button>
                <a href="weapon-list.php" class="filter-btn clear-btn">Clear</a>
            </form>
        </div>

        <!-- Results Info -->
        <div class="results-info">
            <?php if ($totalWeapons > 0): ?>
                Showing <?php echo (($currentPage - 1) * $itemsPerPage) + 1; ?> to 
                <?php echo min($currentPage * $itemsPerPage, $totalWeapons); ?> of 
                <?php echo number_format($totalWeapons); ?> weapons
                <?php if (!empty($filters)): ?>
                    (filtered)
                <?php endif; ?>
            <?php else: ?>
                No weapons found
                <?php if (!empty($filters)): ?>
                    with the current filters
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Weapons Table -->
        <?php if (!empty($weapons)): ?>
            <table class="weapons-table">
                <thead>
                    <tr>
                        <th>Icon</th>
                        <th>Name</th>
                        <th>ID</th>
                        <th>Material</th>
                        <th>Weight</th>
                        <th>Small Dmg</th>
                        <th>Large Dmg</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($weapons as $weapon): ?>
                        <tr>
                            <td>
                                <img src="<?php echo $basePath; ?>assets/img/icons/<?php echo htmlspecialchars($weapon['iconId']); ?>.png" 
                                     alt="<?php echo htmlspecialchars($weapon['desc_en']); ?>" 
                                     class="weapon-icon"
                                     onerror="this.src='<?php echo $basePath; ?>assets/img/placeholders/noimage.png'">
                            </td>
                            <td class="weapon-name"><?php echo htmlspecialchars($weapon['desc_en']); ?></td>
                            <td class="weapon-id"><?php echo htmlspecialchars($weapon['item_id']); ?></td>
                            <td class="weapon-material"><?php echo htmlspecialchars(normalizeMaterial($weapon['material'])); ?></td>
                            <td class="weapon-weight"><?php echo number_format($weapon['weight']); ?></td>
                            <td class="weapon-damage"><?php echo number_format($weapon['dmg_small']); ?></td>
                            <td class="weapon-damage"><?php echo number_format($weapon['dmg_large']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-results">
                <h3>No weapons found</h3>
                <p>Try adjusting your filters or search criteria.</p>
            </div>
        <?php endif; ?>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($currentPage > 1): ?>
                    <a href="<?php echo $baseUrl; ?>?page=<?php echo $currentPage - 1; ?><?php echo $queryString; ?>" class="page-btn">« Previous</a>
                <?php endif; ?>
                
                <?php
                $startPage = max(1, $currentPage - 2);
                $endPage = min($totalPages, $currentPage + 2);
                
                for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <?php if ($i == $currentPage): ?>
                        <span class="page-btn active"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="<?php echo $baseUrl; ?>?page=<?php echo $i; ?><?php echo $queryString; ?>" class="page-btn"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($currentPage < $totalPages): ?>
                    <a href="<?php echo $baseUrl; ?>?page=<?php echo $currentPage + 1; ?><?php echo $queryString; ?>" class="page-btn">Next »</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php echo generateFooter(); ?>
