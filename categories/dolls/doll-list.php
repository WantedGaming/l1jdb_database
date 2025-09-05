<?php
require_once '../../includes/functions.php';

$searchQuery = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$itemsPerPage = 20;
$offset = ($page - 1) * $itemsPerPage;

$dolls = getCategoryData('dolls', $itemsPerPage);
$totalDolls = getCategoryCount('dolls');
$totalPages = ceil($totalDolls / $itemsPerPage);
?>

<?php echo generateHeader('Dolls - L1j-R Database'); ?>

<?php echo generateBreadcrumb(['/categories/dolls/doll-list.php' => 'Dolls']); ?>

<div class="page-header">
    <div class="container">
        <h1 class="page-title">Dolls Database</h1>
        <p class="page-subtitle">Explore magical dolls and companions</p>
    </div>
</div>

<div class="content-section">
    <div class="container">
        <div class="search-container">
            <form method="GET" class="search-form">
                <input type="text" 
                       name="search" 
                       id="search-input"
                       placeholder="Search dolls..." 
                       value="<?php echo htmlspecialchars($searchQuery); ?>"
                       class="search-input">
                <button type="submit" class="search-btn">Search</button>
            </form>
        </div>

        <div class="results-info">
            <p>Showing <?php echo count($dolls); ?> of <?php echo $totalDolls; ?> dolls</p>
        </div>

        <div class="item-grid">
            <?php if (!empty($dolls)): ?>
                <?php foreach ($dolls as $doll): ?>
                    <div class="item-card">
                        <div class="item-image">
                            <img src="<?php echo getPlaceholderImage('dolls'); ?>" 
                                 alt="<?php echo htmlspecialchars($doll['name'] ?? 'Unknown'); ?>">
                        </div>
                        <div class="item-info">
                            <h3 class="item-name"><?php echo htmlspecialchars($doll['name'] ?? 'Unknown'); ?></h3>
                            <p class="item-type">Type: <?php echo htmlspecialchars($doll['type'] ?? 'Unknown'); ?></p>
                            <p class="item-rarity">Rarity: <?php echo htmlspecialchars($doll['rarity'] ?? 'N/A'); ?></p>
                            <a href="doll-detail.php?id=<?php echo $doll['id'] ?? 0; ?>" class="item-link">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results">
                    <h3>No dolls found</h3>
                    <p>Try adjusting your search criteria or browse all dolls.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php echo generatePagination($page, $totalPages, 'doll-list.php'); ?>
    </div>
</div>

<?php echo generateFooter(); ?>
