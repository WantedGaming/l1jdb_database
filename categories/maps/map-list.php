<?php
require_once '../../includes/functions.php';

$searchQuery = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$itemsPerPage = 20;
$offset = ($page - 1) * $itemsPerPage;

$maps = getCategoryData('maps', $itemsPerPage);
$totalMaps = getCategoryCount('maps');
$totalPages = ceil($totalMaps / $itemsPerPage);
?>

<?php echo generateHeader('Maps - L1j-R Database'); ?>

<?php echo generateBreadcrumb(['/categories/maps/map-list.php' => 'Maps']); ?>

<div class="page-header">
    <div class="container">
        <h1 class="page-title">Maps Database</h1>
        <p class="page-subtitle">Explore worlds, zones, and locations</p>
    </div>
</div>

<div class="content-section">
    <div class="container">
        <div class="search-container">
            <form method="GET" class="search-form">
                <input type="text" 
                       name="search" 
                       id="search-input"
                       placeholder="Search maps..." 
                       value="<?php echo htmlspecialchars($searchQuery); ?>"
                       class="search-input">
                <button type="submit" class="search-btn">Search</button>
            </form>
        </div>

        <div class="results-info">
            <p>Showing <?php echo count($maps); ?> of <?php echo $totalMaps; ?> maps</p>
        </div>

        <div class="item-grid">
            <?php if (!empty($maps)): ?>
                <?php foreach ($maps as $map): ?>
                    <div class="item-card">
                        <div class="item-image">
                            <img src="<?php echo getPlaceholderImage('maps'); ?>" 
                                 alt="<?php echo htmlspecialchars($map['name'] ?? 'Unknown'); ?>">
                        </div>
                        <div class="item-info">
                            <h3 class="item-name"><?php echo htmlspecialchars($map['name'] ?? 'Unknown'); ?></h3>
                            <p class="item-type">Zone: <?php echo htmlspecialchars($map['zone'] ?? 'Unknown'); ?></p>
                            <p class="item-level">Level Range: <?php echo htmlspecialchars($map['level_range'] ?? 'N/A'); ?></p>
                            <a href="map-detail.php?id=<?php echo $map['id'] ?? 0; ?>" class="item-link">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results">
                    <h3>No maps found</h3>
                    <p>Try adjusting your search criteria or browse all maps.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php echo generatePagination($page, $totalPages, 'map-list.php'); ?>
    </div>
</div>

<?php echo generateFooter(); ?>
