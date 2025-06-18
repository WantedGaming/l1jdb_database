<?php
require_once dirname(dirname(dirname(__DIR__))) . '/includes/config.php';
require_once dirname(dirname(dirname(__DIR__))) . '/admin/includes/header.php';

// Get weapon ID from URL
$weaponId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($weaponId <= 0) {
    header('Location: weapon_list.php?error=invalid_id');
    exit;
}

// Get weapon data
$sql = "SELECT * FROM weapon WHERE item_id = :item_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':item_id' => $weaponId]);
$weapon = $stmt->fetch();

if (!$weapon) {
    header('Location: weapon_list.php?error=not_found');
    exit;
}

// Helper functions
function getWeaponGrades() {
    return [
        'NORMAL' => 'Normal',
        'ADVANC' => 'Advanced',
        'RARE' => 'Rare',
        'HERO' => 'Hero',
        'LEGEND' => 'Legend',
        'MYTH' => 'Myth',
        'ONLY' => 'Only'
    ];
}

function getWeaponTypes() {
    return [
        'SWORD' => 'Sword',
        'DAGGER' => 'Dagger',
        'TOHAND_SWORD' => 'Two-Hand Sword',
        'BOW' => 'Bow',
        'SPEAR' => 'Spear',
        'BLUNT' => 'Blunt',
        'STAFF' => 'Staff',
        'STING' => 'Sting',
        'ARROW' => 'Arrow',
        'GAUNTLET' => 'Gauntlet',
        'CLAW' => 'Claw',
        'EDORYU' => 'Edoryu',
        'SINGLE_BOW' => 'Single Bow',
        'SINGLE_SPEAR' => 'Single Spear',
        'TOHAND_BLUNT' => 'Two-Hand Blunt',
        'TOHAND_STAFF' => 'Two-Hand Staff',
        'KEYRINGK' => 'Keyring',
        'CHAINSWORD' => 'Chain Sword'
    ];
}

function formatMaterial($material) {
    // Extract the English part from materials like "IRON(철)"
    if (preg_match('/^([A-Z_]+)\(/', $material, $matches)) {
        return ucfirst(strtolower($matches[1]));
    }
    return ucfirst(strtolower($material));
}

$weaponTypes = getWeaponTypes();
$weaponGrades = getWeaponGrades();
?>

<div class="admin-content-wrapper">
    <!-- Admin Header -->
    <div class="admin-header">
        <h1>Delete Weapon Confirmation</h1>
        <div class="admin-header-actions">
            <a href="weapon_list.php" class="admin-btn admin-btn-secondary">
                <span>←</span> Back to List
            </a>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="admin-breadcrumb">
        <ul class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="/l1jdb_database/admin/">Admin Dashboard</a></li>
            <li class="breadcrumb-separator">/</li>
            <li class="breadcrumb-item"><a href="weapon_list.php">Weapon Management</a></li>
            <li class="breadcrumb-separator">/</li>
            <li class="breadcrumb-item">Delete Confirmation</li>
        </ul>
    </div>

    <!-- Warning Message -->
    <div class="admin-message admin-message-warning">
        <strong>⚠️ Warning:</strong> You are about to permanently delete this weapon from the database. This action cannot be undone!
    </div>

    <!-- Weapon Details Card -->
    <div class="weapon-detail-row">
        <!-- Weapon Preview -->
        <div class="weapon-image-col">
            <div class="weapon-image-container">
                <img src="/l1jdb_database/assets/img/icons/<?= $weapon['iconId'] ?>.png" 
                     alt="<?= htmlspecialchars($weapon['desc_en']) ?>" 
                     onerror="this.src='/l1jdb_database/assets/img/placeholders/0.png'"
                     class="weapon-main-image">
                <div class="preview-info text-center mt-2">
                    <div class="text-large text-highlight"><?= htmlspecialchars($weapon['desc_en']) ?></div>
                    <div class="text-small text-muted">Icon ID: <?= $weapon['iconId'] ?></div>
                </div>
            </div>
        </div>

        <!-- Weapon Information -->
        <div class="weapon-info-col">
            <div class="weapon-basic-info">
                <h2>Weapon Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="text-muted">Weapon ID:</span>
                        <span class="text-highlight"><?= $weapon['item_id'] ?></span>
                    </div>
                    <div class="info-item">
                        <span class="text-muted">English Name:</span>
                        <span><?= htmlspecialchars($weapon['desc_en']) ?></span>
                    </div>
                    <?php if (!empty($weapon['desc_kr'])): ?>
                    <div class="info-item">
                        <span class="text-muted">Korean Name:</span>
                        <span><?= htmlspecialchars($weapon['desc_kr']) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="info-item">
                        <span class="text-muted">Type:</span>
                        <span><?= $weaponTypes[$weapon['type']] ?? $weapon['type'] ?></span>
                    </div>
                    <div class="info-item">
                        <span class="text-muted">Grade:</span>
                        <span class="text-highlight"><?= $weaponGrades[$weapon['itemGrade']] ?? $weapon['itemGrade'] ?></span>
                    </div>
                    <div class="info-item">
                        <span class="text-muted">Material:</span>
                        <span><?= formatMaterial($weapon['material']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="text-muted">Damage (Small/Large):</span>
                        <span class="text-positive"><?= $weapon['dmg_small'] ?> / <?= $weapon['dmg_large'] ?></span>
                    </div>
                    <div class="info-item">
                        <span class="text-muted">Weight:</span>
                        <span><?= $weapon['weight'] ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Section -->
    <div class="weapon-section">
        <h2>Combat Statistics</h2>
        <div class="weapon-advanced-grid">
            <div class="field-group">
                <h3>Damage Modifiers</h3>
                <div class="form-grid-2">
                    <div class="info-item">
                        <span class="text-muted">Hit Modifier:</span>
                        <span class="<?= $weapon['hitmodifier'] > 0 ? 'text-positive' : ($weapon['hitmodifier'] < 0 ? 'text-negative' : 'text-neutral') ?>">
                            <?= $weapon['hitmodifier'] > 0 ? '+' : '' ?><?= $weapon['hitmodifier'] ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="text-muted">Damage Modifier:</span>
                        <span class="<?= $weapon['dmgmodifier'] > 0 ? 'text-positive' : ($weapon['dmgmodifier'] < 0 ? 'text-negative' : 'text-neutral') ?>">
                            <?= $weapon['dmgmodifier'] > 0 ? '+' : '' ?><?= $weapon['dmgmodifier'] ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="text-muted">Safe Enchant:</span>
                        <span><?= $weapon['safenchant'] ?></span>
                    </div>
                    <div class="info-item">
                        <span class="text-muted">Can Be Damaged:</span>
                        <span class="<?= $weapon['canbedmg'] ? 'text-negative' : 'text-positive' ?>">
                            <?= $weapon['canbedmg'] ? 'Yes' : 'No' ?>
                        </span>
                    </div>
                </div>
            </div>

            <?php 
            $hasStatBonuses = $weapon['add_str'] || $weapon['add_con'] || $weapon['add_dex'] || 
                              $weapon['add_int'] || $weapon['add_wis'] || $weapon['add_cha'] ||
                              $weapon['add_hp'] || $weapon['add_mp'] || $weapon['add_sp'];
            ?>
            <?php if ($hasStatBonuses): ?>
            <div class="field-group">
                <h3>Stat Bonuses</h3>
                <div class="form-grid-3">
                    <?php if ($weapon['add_str'] != 0): ?>
                    <div class="info-item">
                        <span class="text-muted">STR:</span>
                        <span class="text-positive">+<?= $weapon['add_str'] ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($weapon['add_con'] != 0): ?>
                    <div class="info-item">
                        <span class="text-muted">CON:</span>
                        <span class="text-positive">+<?= $weapon['add_con'] ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($weapon['add_dex'] != 0): ?>
                    <div class="info-item">
                        <span class="text-muted">DEX:</span>
                        <span class="text-positive">+<?= $weapon['add_dex'] ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($weapon['add_int'] != 0): ?>
                    <div class="info-item">
                        <span class="text-muted">INT:</span>
                        <span class="text-positive">+<?= $weapon['add_int'] ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($weapon['add_wis'] != 0): ?>
                    <div class="info-item">
                        <span class="text-muted">WIS:</span>
                        <span class="text-positive">+<?= $weapon['add_wis'] ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($weapon['add_cha'] != 0): ?>
                    <div class="info-item">
                        <span class="text-muted">CHA:</span>
                        <span class="text-positive">+<?= $weapon['add_cha'] ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($weapon['add_hp'] != 0): ?>
                    <div class="info-item">
                        <span class="text-muted">HP:</span>
                        <span class="text-positive">+<?= $weapon['add_hp'] ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($weapon['add_mp'] != 0): ?>
                    <div class="info-item">
                        <span class="text-muted">MP:</span>
                        <span class="text-positive">+<?= $weapon['add_mp'] ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($weapon['add_sp'] != 0): ?>
                    <div class="info-item">
                        <span class="text-muted">SP:</span>
                        <span class="text-positive">+<?= $weapon['add_sp'] ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="field-group">
                <h3>Class Restrictions</h3>
                <div class="form-grid-5">
                    <?php 
                    $classes = [
                        'use_royal' => 'Royal',
                        'use_knight' => 'Knight',
                        'use_mage' => 'Mage',
                        'use_elf' => 'Elf',
                        'use_darkelf' => 'Dark Elf',
                        'use_dragonknight' => 'Dragon Knight',
                        'use_illusionist' => 'Illusionist',
                        'use_warrior' => 'Warrior',
                        'use_fencer' => 'Fencer',
                        'use_lancer' => 'Lancer'
                    ];
                    ?>
                    <?php foreach ($classes as $field => $className): ?>
                    <div class="info-item">
                        <span class="text-muted"><?= $className ?>:</span>
                        <span class="<?= $weapon[$field] ? 'text-positive' : 'text-negative' ?>">
                            <?= $weapon[$field] ? 'Allowed' : 'Restricted' ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Actions -->
    <div class="admin-message admin-message-error">
        <strong>🚨 Final Warning:</strong> Once deleted, this weapon and all its data will be permanently removed from the database.
    </div>

    <div class="admin-header mt-3">
        <h2>Confirm Deletion</h2>
        <div class="admin-header-actions">
            <form method="POST" action="weapon_process.php" style="display: inline-block;">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="item_id" value="<?= $weaponId ?>">
                <button type="submit" class="admin-btn admin-btn-danger admin-btn-large" id="confirmDeleteBtn">
                    🗑️ Yes, Delete This Weapon
                </button>
            </form>
            <a href="weapon_list.php" class="admin-btn admin-btn-secondary admin-btn-large">
                Cancel
            </a>
            <a href="weapon_edit.php?id=<?= $weaponId ?>" class="admin-btn admin-btn-primary admin-btn-large">
                Edit Instead
            </a>
        </div>
    </div>

    <!-- Safety Information -->
    <div class="weapon-section">
        <h3>Before You Delete</h3>
        <div class="field-group">
            <ul style="color: var(--text); opacity: 0.9; line-height: 1.6;">
                <li><strong>Database Impact:</strong> This weapon will be completely removed from the database</li>
                <li><strong>References:</strong> Any game references to this weapon may cause errors</li>
                <li><strong>Backup:</strong> Consider exporting this weapon data before deletion</li>
                <li><strong>Alternative:</strong> You can edit the weapon instead of deleting it</li>
            </ul>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteBtn = document.getElementById('confirmDeleteBtn');
    const weaponName = "<?= addslashes($weapon['desc_en']) ?>";
    
    deleteBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        const confirmText = `Type "DELETE" to confirm deletion of ${weaponName}:`;
        const userInput = prompt(confirmText);
        
        if (userInput === 'DELETE') {
            // Double confirmation
            const finalConfirm = confirm(`Are you absolutely sure you want to delete "${weaponName}"?\n\nThis action cannot be undone!`);
            if (finalConfirm) {
                this.closest('form').submit();
            }
        } else if (userInput !== null) {
            alert('Deletion cancelled. You must type "DELETE" exactly to confirm.');
        }
    });
});
</script>

<?php require_once '../../includes/footer.php'; ?>
