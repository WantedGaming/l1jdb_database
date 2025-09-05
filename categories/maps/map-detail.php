<?php
require_once '../../includes/functions.php';

$mapId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($mapId <= 0) {
    header('Location: map-list.php');
    exit;
}

$map = getItemById('maps', $mapId);

if (!$map) {
    header('Location: map-list.php');
    exit;
}
?>

<?php echo generateHeader('Map Details - L1j-R Database'); ?>

<?php echo generateBreadcrumb([
    '/categories/maps/map-list.php' => 'Maps',
    '#' => $map['name'] ?? 'Unknown Map'
]); ?>

<div class="page-header">
    <div class="container">
        <h1 class="page-title"><?php echo htmlspecialchars($map['name'] ?? 'Unknown Map'); ?></h1>
        <p class="page-subtitle">Map Details</p>
    </div>
</div>

<div class="content-section">
    <div class="container">
        <div class="detail-content">
            <div class="detail-image">
                <img src="<?php echo getPlaceholderImage('maps'); ?>" 
                     alt="<?php echo htmlspecialchars($map['name'] ?? 'Unknown'); ?>">
            </div>
            
            <div class="detail-info">
                <div class="info-section">
                    <h3>Basic Information</h3>
                    <table class="detail-table">
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td><?php echo htmlspecialchars($map['name'] ?? 'Unknown'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Zone:</strong></td>
                            <td><?php echo htmlspecialchars($map['zone'] ?? 'Unknown'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Level Range:</strong></td>
                            <td><?php echo htmlspecialchars($map['level_range'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Size:</strong></td>
                            <td><?php echo htmlspecialchars($map['size'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Type:</strong></td>
                            <td><?php echo htmlspecialchars($map['type'] ?? 'N/A'); ?></td>
                        </tr>
                    </table>
                </div>
                
                <?php if (!empty($map['description'])): ?>
                <div class="info-section">
                    <h3>Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($map['description'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="navigation-buttons">
            <a href="map-list.php" class="nav-btn">← Back to Maps</a>
        </div>
    </div>
</div>

<?php echo generateFooter(); ?>
