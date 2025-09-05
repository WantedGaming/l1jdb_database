<?php
require_once '../../includes/functions.php';

$searchQuery = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$itemsPerPage = 20;
$offset = ($page - 1) * $itemsPerPage;

$armor = getCategoryData('armor', $itemsPerPage);
$totalArmor = getCategoryCount('armor');
$totalPages = ceil($totalArmor / $itemsPerPage);
?>

<?php echo generateHeader('Armor - L1j-R Database'); ?>

<?php echo generateBreadcrumb(['/categories/armor/armor-list.php' => 'Armor']); ?>

<div class="page-header">
    <div class="container">
        <h1 class="page-title">Armor Database</h1>
        <p class="page-subtitle">Explore our complete collection of armor and protective gear</p>
    </div>
</div>

<div class="content-section">
    <div class="container">
        <div class="search-container">
            <form method="GET" class="search-form">
                <input type="text" 
                       name="search" 
                       id="search-input"
                       placeholder="Search armor..." 
                       value="<?php echo htmlspecialchars($searchQuery); ?>"
                       class="search-input">
                <button type="submit" class="search-btn">Search</button>
            </form>
        </div>

        <div class="results-info">
            <p>Showing <?php echo count($armor); ?> of <?php echo $totalArmor; ?> armor pieces</p>
        </div>

        <div class="item-grid">
            <?php if (!empty($armor)): ?>
                <?php foreach ($armor as $armorPiece): ?>
                    <div class="item-card">
                        <div class="item-image">
                            <img src="<?php echo getPlaceholderImage('armor'); ?>" 
                                 alt="<?php echo htmlspecialchars($armorPiece['name'] ?? 'Unknown'); ?>">
                        </div>
                        <div class="item-info">
                            <h3 class="item-name"><?php echo htmlspecialchars($armorPiece['name'] ?? 'Unknown'); ?></h3>
                            <p class="item-type">Type: <?php echo htmlspecialchars($armorPiece['type'] ?? 'Unknown'); ?></p>
                            <p class="item-defense">Defense: <?php echo htmlspecialchars($armorPiece['defense'] ?? 'N/A'); ?></p>
                            <a href="armor-detail.php?id=<?php echo $armorPiece['id'] ?? 0; ?>" class="item-link">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results">
                    <h3>No armor found</h3>
                    <p>Try adjusting your search criteria or browse all armor.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php echo generatePagination($page, $totalPages, 'armor-list.php'); ?>
    </div>
</div>

<?php echo generateFooter(); ?>
