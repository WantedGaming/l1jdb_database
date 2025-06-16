<?php
require_once dirname(dirname(dirname(__DIR__))) . '/includes/config.php';
require_once dirname(dirname(dirname(__DIR__))) . '/includes/header.php';

// Get weapon ID from URL
$weaponId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($weaponId <= 0) {
    header('Location: ../weapons/weapon_list.php');
    exit;
}

// Get weapon data
$sql = "SELECT * FROM weapon WHERE item_id = :item_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':item_id' => $weaponId]);
$weapon = $stmt->fetch();

if (!$weapon) {
    header('Location: ../weapons/weapon_list.php');
    exit;
}

// Call page header with weapon name
$weaponName = cleanDescriptionPrefix($weapon['desc_en']);
getPageHeader("Delete " . $weaponName);

// Create weapon type and material for hero
$heroText = normalizeWeaponType($weapon['type']) . ' - ' . normalizeWeaponMaterial($weapon['material']);

// Render hero section
renderHero('weapons', "Delete " . $weaponName, $heroText);
?>

<main>
    <div class="main">
        <div class="container">
            <!-- Back Navigation -->
            <div class="back-nav">
                <a href="../weapons/weapon_detail.php?id=<?= $weaponId ?>" class="back-btn">&larr; Back to Weapon Details</a>
            </div>
            
            <div class="delete-confirmation">
                <div class="weapon-preview">
                    <img src="<?= SITE_URL ?>/assets/img/icons/<?= $weapon['iconId'] ?>.png" 
                         alt="<?= htmlspecialchars($weaponName) ?>" 
                         onerror="this.src='<?= SITE_URL ?>/assets/img/placeholders/0.png'"
                         class="weapon-main-image">
                </div>
                
                <div class="confirmation-message">
                    <h2>Are you sure you want to delete this weapon?</h2>
                    <p>This action cannot be undone. The following weapon will be permanently deleted:</p>
                    
                    <div class="weapon-details">
                        <h3><?= htmlspecialchars($weaponName) ?></h3>
                        <p>Type: <?= normalizeWeaponType($weapon['type']) ?></p>
                        <p>Material: <?= normalizeWeaponMaterial($weapon['material']) ?></p>
                        <p>Damage: <?= $weapon['dmg_small'] ?> (Small) / <?= $weapon['dmg_large'] ?> (Large)</p>
                    </div>
                    
                    <form action="weapon_process.php" method="POST" class="delete-form">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="item_id" value="<?= $weaponId ?>">
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-danger">Delete Weapon</button>
                            <a href="../weapons/weapon_detail.php?id=<?= $weaponId ?>" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once dirname(dirname(dirname(__DIR__))) . '/includes/footer.php'; ?> 