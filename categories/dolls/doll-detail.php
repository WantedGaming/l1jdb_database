<?php
require_once '../../includes/functions.php';

$dollId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($dollId <= 0) {
    header('Location: doll-list.php');
    exit;
}

$doll = getItemById('dolls', $dollId);

if (!$doll) {
    header('Location: doll-list.php');
    exit;
}
?>

<?php echo generateHeader('Doll Details - L1j-R Database'); ?>

<?php echo generateBreadcrumb([
    '/categories/dolls/doll-list.php' => 'Dolls',
    '#' => $doll['name'] ?? 'Unknown Doll'
]); ?>

<div class="page-header">
    <div class="container">
        <h1 class="page-title"><?php echo htmlspecialchars($doll['name'] ?? 'Unknown Doll'); ?></h1>
        <p class="page-subtitle">Doll Details</p>
    </div>
</div>

<div class="content-section">
    <div class="container">
        <div class="detail-content">
            <div class="detail-image">
                <img src="<?php echo getPlaceholderImage('dolls'); ?>" 
                     alt="<?php echo htmlspecialchars($doll['name'] ?? 'Unknown'); ?>">
            </div>
            
            <div class="detail-info">
                <div class="info-section">
                    <h3>Basic Information</h3>
                    <table class="detail-table">
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td><?php echo htmlspecialchars($doll['name'] ?? 'Unknown'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Type:</strong></td>
                            <td><?php echo htmlspecialchars($doll['type'] ?? 'Unknown'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Rarity:</strong></td>
                            <td><?php echo htmlspecialchars($doll['rarity'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Ability:</strong></td>
                            <td><?php echo htmlspecialchars($doll['ability'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Duration:</strong></td>
                            <td><?php echo htmlspecialchars($doll['duration'] ?? 'N/A'); ?></td>
                        </tr>
                    </table>
                </div>
                
                <?php if (!empty($doll['description'])): ?>
                <div class="info-section">
                    <h3>Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($doll['description'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="navigation-buttons">
            <a href="doll-list.php" class="nav-btn">← Back to Dolls</a>
        </div>
    </div>
</div>

<?php echo generateFooter(); ?>
