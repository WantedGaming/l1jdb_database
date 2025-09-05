<?php
require_once '../../includes/functions.php';

$searchQuery = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$itemsPerPage = 20;
$offset = ($page - 1) * $itemsPerPage;

$monsters = getCategoryData('monsters', $itemsPerPage);
$totalMonsters = getCategoryCount('monsters');
$totalPages = ceil($totalMonsters / $itemsPerPage);
?>

<?php echo generateHeader('Monsters - L1j-R Database'); ?>

<?php echo generateBreadcrumb(['/categories/monsters/monster-list.php' => 'Monsters']); ?>

<div class="page-header">
    <div class="container">
        <h1 class="page-title">Monsters Database</h1>
        <p class="page-subtitle">Explore creatures, their abilities, and statistics</p>
    </div>
</div>

<div class="content-section">
    <div class="container">
        <div class="search-container">
            <form method="GET" class="search-form">
                <input type="text" 
                       name="search" 
                       id="search-input"
                       placeholder="Search monsters..." 
                       value="<?php echo htmlspecialchars($searchQuery); ?>"
                       class="search-input">
                <button type="submit" class="search-btn">Search</button>
            </form>
        </div>

        <div class="results-info">
            <p>Showing <?php echo count($monsters); ?> of <?php echo $totalMonsters; ?> monsters</p>
        </div>

        <div class="item-grid">
            <?php if (!empty($monsters)): ?>
                <?php foreach ($monsters as $monster): ?>
                    <div class="item-card">
                        <div class="item-image">
                            <img src="<?php echo getPlaceholderImage('monsters'); ?>" 
                                 alt="<?php echo htmlspecialchars($monster['name'] ?? 'Unknown'); ?>">
                        </div>
                        <div class="item-info">
                            <h3 class="item-name"><?php echo htmlspecialchars($monster['name'] ?? 'Unknown'); ?></h3>
                            <p class="item-type">Type: <?php echo htmlspecialchars($monster['type'] ?? 'Unknown'); ?></p>
                            <p class="item-level">Level: <?php echo htmlspecialchars($monster['level'] ?? 'N/A'); ?></p>
                            <a href="monster-detail.php?id=<?php echo $monster['id'] ?? 0; ?>" class="item-link">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results">
                    <h3>No monsters found</h3>
                    <p>Try adjusting your search criteria or browse all monsters.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php echo generatePagination($page, $totalPages, 'monster-list.php'); ?>
    </div>
</div>

<?php echo generateFooter(); ?>
