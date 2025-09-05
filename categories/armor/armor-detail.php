<?php
require_once '../../includes/functions.php';

$armorId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($armorId <= 0) {
    header('Location: armor-list.php');
    exit;
}

$armor = getItemById('armor', $armorId);

if (!$armor) {
    header('Location: armor-list.php');
    exit;
}
?>

<?php echo generateHeader('Armor Details - L1j-R Database'); ?>

<?php echo generateBreadcrumb([
    '/categories/armor/armor-list.php' => 'Armor',
    '#' => $armor['name'] ?? 'Unknown Armor'
]); ?>

<div class="page-header">
    <div class="container">
        <h1 class="page-title"><?php echo htmlspecialchars($armor['name'] ?? 'Unknown Armor'); ?></h1>
        <p class="page-subtitle">Armor Details</p>
    </div>
</div>

<div class="content-section">
    <div class="container">
        <div class="detail-content">
            <div class="detail-image">
                <img src="<?php echo getPlaceholderImage('armor'); ?>" 
                     alt="<?php echo htmlspecialchars($armor['name'] ?? 'Unknown'); ?>">
            </div>
            
            <div class="detail-info">
                <div class="info-section">
                    <h3>Basic Information</h3>
                    <table class="detail-table">
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td><?php echo htmlspecialchars($armor['name'] ?? 'Unknown'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Type:</strong></td>
                            <td><?php echo htmlspecialchars($armor['type'] ?? 'Unknown'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Defense:</strong></td>
                            <td><?php echo htmlspecialchars($armor['defense'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Level Required:</strong></td>
                            <td><?php echo htmlspecialchars($armor['level_req'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Durability:</strong></td>
                            <td><?php echo htmlspecialchars($armor['durability'] ?? 'N/A'); ?></td>
                        </tr>
                    </table>
                </div>
                
                <?php if (!empty($armor['description'])): ?>
                <div class="info-section">
                    <h3>Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($armor['description'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="navigation-buttons">
            <a href="armor-list.php" class="nav-btn">← Back to Armor</a>
        </div>
    </div>
</div>

<?php echo generateFooter(); ?>
