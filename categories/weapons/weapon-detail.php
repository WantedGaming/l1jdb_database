<?php
require_once '../../includes/functions.php';

// Get weapon ID from URL
$weaponId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($weaponId <= 0) {
    header('Location: weapon-list.php');
    exit;
}

// Get weapon data
$weapon = getItemById('weapons', $weaponId);

if (!$weapon) {
    header('Location: weapon-list.php');
    exit;
}
?>

<?php echo generateHeader('Weapon Details - L1j-R Database'); ?>

<?php echo generateBreadcrumb([
    '/categories/weapons/weapon-list.php' => 'Weapons',
    '#' => $weapon['name'] ?? 'Unknown Weapon'
]); ?>

<div class="page-header">
    <div class="container">
        <h1 class="page-title"><?php echo htmlspecialchars($weapon['name'] ?? 'Unknown Weapon'); ?></h1>
        <p class="page-subtitle">Weapon Details</p>
    </div>
</div>

<div class="content-section">
    <div class="container">
        <div class="detail-content">
            <div class="detail-image">
                <img src="<?php echo getPlaceholderImage('weapons'); ?>" 
                     alt="<?php echo htmlspecialchars($weapon['name'] ?? 'Unknown'); ?>">
            </div>
            
            <div class="detail-info">
                <div class="info-section">
                    <h3>Basic Information</h3>
                    <table class="detail-table">
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td><?php echo htmlspecialchars($weapon['name'] ?? 'Unknown'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Type:</strong></td>
                            <td><?php echo htmlspecialchars($weapon['type'] ?? 'Unknown'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Damage:</strong></td>
                            <td><?php echo htmlspecialchars($weapon['damage'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Level Required:</strong></td>
                            <td><?php echo htmlspecialchars($weapon['level_req'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Durability:</strong></td>
                            <td><?php echo htmlspecialchars($weapon['durability'] ?? 'N/A'); ?></td>
                        </tr>
                    </table>
                </div>
                
                <?php if (!empty($weapon['description'])): ?>
                <div class="info-section">
                    <h3>Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($weapon['description'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="navigation-buttons">
            <a href="weapon-list.php" class="nav-btn">← Back to Weapons</a>
        </div>
    </div>
</div>

<?php echo generateFooter(); ?>
