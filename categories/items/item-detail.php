<?php
require_once '../../includes/functions.php';

$itemId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($itemId <= 0) {
    header('Location: item-list.php');
    exit;
}

$item = getItemById('items', $itemId);

if (!$item) {
    header('Location: item-list.php');
    exit;
}
?>

<?php echo generateHeader('Item Details - L1j-R Database'); ?>

<?php echo generateBreadcrumb([
    '/categories/items/item-list.php' => 'Items',
    '#' => $item['name'] ?? 'Unknown Item'
]); ?>

<div class="page-header">
    <div class="container">
        <h1 class="page-title"><?php echo htmlspecialchars($item['name'] ?? 'Unknown Item'); ?></h1>
        <p class="page-subtitle">Item Details</p>
    </div>
</div>

<div class="content-section">
    <div class="container">
        <div class="detail-content">
            <div class="detail-image">
                <img src="<?php echo getPlaceholderImage('items'); ?>" 
                     alt="<?php echo htmlspecialchars($item['name'] ?? 'Unknown'); ?>">
            </div>
            
            <div class="detail-info">
                <div class="info-section">
                    <h3>Basic Information</h3>
                    <table class="detail-table">
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td><?php echo htmlspecialchars($item['name'] ?? 'Unknown'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Type:</strong></td>
                            <td><?php echo htmlspecialchars($item['type'] ?? 'Unknown'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Effect:</strong></td>
                            <td><?php echo htmlspecialchars($item['effect'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Stackable:</strong></td>
                            <td><?php echo ($item['stackable'] ?? 0) ? 'Yes' : 'No'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Weight:</strong></td>
                            <td><?php echo htmlspecialchars($item['weight'] ?? 'N/A'); ?></td>
                        </tr>
                    </table>
                </div>
                
                <?php if (!empty($item['description'])): ?>
                <div class="info-section">
                    <h3>Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="navigation-buttons">
            <a href="item-list.php" class="nav-btn">← Back to Items</a>
        </div>
    </div>
</div>

<?php echo generateFooter(); ?>
