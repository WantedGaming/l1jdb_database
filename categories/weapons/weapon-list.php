<?php
require_once '../../includes/functions.php';

// Get search query if provided
$searchQuery = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$itemsPerPage = 20;
$offset = ($page - 1) * $itemsPerPage;

// Get weapons data (you'll need to implement this based on your database structure)
$weapons = getCategoryData('weapons', $itemsPerPage);
$totalWeapons = getCategoryCount('weapons');
$totalPages = ceil($totalWeapons / $itemsPerPage);
?>

<?php echo generateHeader('Weapons - L1j-R Database'); ?>

<?php echo generateBreadcrumb(['/categories/weapons/weapon-list.php' => 'Weapons']); ?>

<div class="page-header">
    <div class="container">
        <h1 class="page-title">Weapons Database</h1>
        <p class="page-subtitle">Explore our complete collection of weapons</p>
    </div>
</div>

<div class="content-section">
    <div class="container">
        <!-- Search Bar -->
        <div class="search-container">
            <form method="GET" class="search-form">
                <input type="text" 
                       name="search" 
                       id="search-input"
                       placeholder="Search weapons..." 
                       value="<?php echo htmlspecialchars($searchQuery); ?>"
                       class="search-input">
                <button type="submit" class="search-btn">Search</button>
            </form>
        </div>

        <!-- Results Info -->
        <div class="results-info">
            <p>Showing <?php echo count($weapons); ?> of <?php echo $totalWeapons; ?> weapons</p>
        </div>

        <!-- Weapons Grid -->
        <div class="item-grid">
            <?php if (!empty($weapons)): ?>
                <?php foreach ($weapons as $weapon): ?>
                    <div class="item-card">
                        <div class="item-image">
                            <img src="<?php echo getPlaceholderImage('weapons'); ?>" 
                                 alt="<?php echo htmlspecialchars($weapon['name'] ?? 'Unknown'); ?>">
                        </div>
                        <div class="item-info">
                            <h3 class="item-name"><?php echo htmlspecialchars($weapon['name'] ?? 'Unknown'); ?></h3>
                            <p class="item-type">Type: <?php echo htmlspecialchars($weapon['type'] ?? 'Unknown'); ?></p>
                            <p class="item-damage">Damage: <?php echo htmlspecialchars($weapon['damage'] ?? 'N/A'); ?></p>
                            <a href="weapon-detail.php?id=<?php echo $weapon['id'] ?? 0; ?>" class="item-link">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results">
                    <h3>No weapons found</h3>
                    <p>Try adjusting your search criteria or browse all weapons.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php echo generatePagination($page, $totalPages, 'weapon-list.php'); ?>
    </div>
</div>

<?php echo generateFooter(); ?>
