<?php
require_once '../../includes/functions.php';

$monsterId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($monsterId <= 0) {
    header('Location: monster-list.php');
    exit;
}

$monster = getItemById('monsters', $monsterId);

if (!$monster) {
    header('Location: monster-list.php');
    exit;
}
?>

<?php echo generateHeader('Monster Details - L1j-R Database'); ?>

<?php echo generateBreadcrumb([
    '/categories/monsters/monster-list.php' => 'Monsters',
    '#' => $monster['name'] ?? 'Unknown Monster'
]); ?>

<div class="page-header">
    <div class="container">
        <h1 class="page-title"><?php echo htmlspecialchars($monster['name'] ?? 'Unknown Monster'); ?></h1>
        <p class="page-subtitle">Monster Details</p>
    </div>
</div>

<div class="content-section">
    <div class="container">
        <div class="detail-content">
            <div class="detail-image">
                <img src="<?php echo getPlaceholderImage('monsters'); ?>" 
                     alt="<?php echo htmlspecialchars($monster['name'] ?? 'Unknown'); ?>">
            </div>
            
            <div class="detail-info">
                <div class="info-section">
                    <h3>Basic Information</h3>
                    <table class="detail-table">
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td><?php echo htmlspecialchars($monster['name'] ?? 'Unknown'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Type:</strong></td>
                            <td><?php echo htmlspecialchars($monster['type'] ?? 'Unknown'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Level:</strong></td>
                            <td><?php echo htmlspecialchars($monster['level'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>HP:</strong></td>
                            <td><?php echo htmlspecialchars($monster['hp'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>MP:</strong></td>
                            <td><?php echo htmlspecialchars($monster['mp'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>AC:</strong></td>
                            <td><?php echo htmlspecialchars($monster['ac'] ?? 'N/A'); ?></td>
                        </tr>
                    </table>
                </div>
                
                <?php if (!empty($monster['description'])): ?>
                <div class="info-section">
                    <h3>Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($monster['description'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="navigation-buttons">
            <a href="monster-list.php" class="nav-btn">← Back to Monsters</a>
        </div>
    </div>
</div>

<?php echo generateFooter(); ?>
