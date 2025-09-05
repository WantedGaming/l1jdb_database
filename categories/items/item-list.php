<?php
require_once '../../includes/functions.php';

$searchQuery = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$itemsPerPage = 20;
$offset = ($page - 1) * $itemsPerPage;

$items = getCategoryData('items', $itemsPerPage);
$totalItems = getCategoryCount('items');
$totalPages = ceil($totalItems / $itemsPerPage);
?>

<?php echo generateHeader('Items - L1j-R Database'); ?>

<?php echo generateBreadcrumb(['/categories/items/item-list.php' => 'Items']); ?>

<div class="page-header">
    <div class="container">
        <h1 class="page-title">Items Database</h1>
        <p class="page-subtitle">Explore consumables, materials, and special items</p>
    </div>
</div>

<div class="content-section">
    <div class="container">
        <div class="search-container">
            <form method="GET" class="search-form">
                <input type="text" 
                       name="search" 
                       id="search-input"
                       placeholder="Search items..." 
                       value="<?php echo htmlspecialchars($searchQuery); ?>"
                       class="search-input">
                <button type="submit" class="search-btn">Search</button>
            </form>
        </div>

        <div class="results-info">
            <p>Showing <?php echo count($items); ?> of <?php echo $totalItems; ?> items</p>
        </div>

        <div class="item-grid">
            <?php if (!empty($items)): ?>
                <?php foreach ($items as $item): ?>
                    <div class="item-card">
                        <div class="item-image">
                            <img src="<?php echo getPlaceholderImage('items'); ?>" 
                                 alt="<?php echo htmlspecialchars($item['name'] ?? 'Unknown'); ?>">
                        </div>
                        <div class="item-info">
                            <h3 class="item-name"><?php echo htmlspecialchars($item['name'] ?? 'Unknown'); ?></h3>
                            <p class="item-type">Type: <?php echo htmlspecialchars($item['type'] ?? 'Unknown'); ?></p>
                            <p class="item-effect">Effect: <?php echo htmlspecialchars($item['effect'] ?? 'N/A'); ?></p>
                            <a href="item-detail.php?id=<?php echo $item['id'] ?? 0; ?>" class="item-link">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results">
                    <h3>No items found</h3>
                    <p>Try adjusting your search criteria or browse all items.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php echo generatePagination($page, $totalPages, 'item-list.php'); ?>
    </div>
</div>

<?php echo generateFooter(); ?>
