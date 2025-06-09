<?php
require_once dirname(dirname(__DIR__)) . '/includes/config.php';
require_once dirname(dirname(__DIR__)) . '/includes/header.php';

// Get weapon ID from URL
$weaponId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($weaponId <= 0) {
    header('Location: weapon_list.php');
    exit;
}

// Get weapon data
$sql = "SELECT * FROM weapon WHERE item_id = :item_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':item_id' => $weaponId]);
$weapon = $stmt->fetch();

if (!$weapon) {
    header('Location: weapon_list.php');
    exit;
}

// Call page header with weapon name
$weaponName = cleanDescriptionPrefix($weapon['desc_en']);
getPageHeader($weaponName);

// Create weapon type and material for hero
$heroText = normalizeWeaponType($weapon['type']) . ' - ' . normalizeWeaponMaterial($weapon['material']);

// Render hero section
renderHero('weapons', $weaponName, $heroText);
?>

<main>
    <div class="main">
        <div class="container">
            <!-- Back Navigation -->
            <div class="back-nav">
                <a href="weapon_list.php" class="back-btn">&larr; Back to Weapons</a>
            </div>
            
            <!-- Main Content Row -->
            <div class="weapon-detail-row">
                <!-- Column 1: Image Preview -->
                <div class="weapon-image-col">
                    <div class="weapon-image-container">
                        <img src="<?= SITE_URL ?>/assets/img/icons/<?= $weapon['iconId'] ?>.png" 
                             alt="<?= htmlspecialchars($weaponName) ?>" 
                             onerror="this.src='<?= SITE_URL ?>/assets/img/placeholders/0.png'"
                             class="weapon-main-image">
                    </div>
                </div>
                
                <!-- Column 2: Basic Information -->
                <div class="weapon-info-col">
                    <div class="weapon-basic-info">
                        <h2>Basic</h2>
                        <div class="info-grid">
							<div class="info-item">
                                <label>S (Small):</label>
                                <span><?= ($weapon['dmg_small']) ?></span>
                            </div>
							<div class="info-item">
                                <label>L (Large):</label>
                                <span><?= ($weapon['dmg_large']) ?></span>
                            </div>
                            <div class="info-item">
                                <label>Type:</label>
                                <span><?= normalizeWeaponType($weapon['type']) ?></span>
                            </div>
                            <div class="info-item">
                                <label>Material:</label>
                                <span><?= normalizeWeaponMaterial($weapon['material']) ?></span>
                            </div>
                            <div class="info-item">
                                <label>Weight:</label>
                                <span><?= ($weapon['weight']) ?></span>
                            </div>
                            <?php $gradeDisplay = displayGrade($weapon['itemGrade']); ?>
                            <?php if ($gradeDisplay): ?>
                            <div class="info-item">
                                <label>Grade:</label>
                                <?= $gradeDisplay ?>
                            </div>
                            <?php endif; ?>
                            <div class="info-item">
                                <label>Safe:</label>
                                <span><?= ($weapon['safenchant']) ?></span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Combat Stats Section -->
            <div class="weapon-section">
                <h2>Statistics</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Damage</h3>
                        <div class="stat-values">
                            <div class="stat-item">
                                <label>Small:</label>
                                <span class="stat-value"><?= $weapon['dmg_small'] ?></span>
                            </div>
                            <div class="stat-item">
                                <label>Large:</label>
                                <span class="stat-value"><?= $weapon['dmg_large'] ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Hit Modifiers</h3>
                        <div class="stat-values">
                            <div class="stat-item">
                                <label>Hit Bonus:</label>
                                <span class="stat-value"><?= $weapon['hitmodifier'] > 0 ? '+' : '' ?><?= $weapon['hitmodifier'] ?></span>
                            </div>
                            <div class="stat-item">
                                <label>Damage Modifier:</label>
                                <span class="stat-value"><?= $weapon['dmgmodifier'] > 0 ? '+' : '' ?><?= $weapon['dmgmodifier'] ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Enchantment</h3>
                        <div class="stat-values">
                            <div class="stat-item">
                                <label>Safe Enchant:</label>
                                <span class="stat-value"><?= $weapon['safenchant'] ?></span>
                            </div>
                            <div class="stat-item">
                                <label>Can be damaged:</label>
                                <span class="stat-value"><?= $weapon['canbedmg'] ? 'Yes' : 'No' ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Stat Bonuses Section -->
            <?php if ($weapon['add_str'] || $weapon['add_con'] || $weapon['add_dex'] || $weapon['add_int'] || $weapon['add_wis'] || $weapon['add_cha'] || $weapon['add_hp'] || $weapon['add_mp']): ?>
            <div class="weapon-section">
                <h2>Stat Bonuses</h2>
                <div class="bonus-grid">
                    <?php if ($weapon['add_str']): ?>
                    <div class="bonus-item">
                        <label>STR:</label>
                        <span class="bonus-positive">+<?= $weapon['add_str'] ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($weapon['add_con']): ?>
                    <div class="bonus-item">
                        <label>CON:</label>
                        <span class="bonus-positive">+<?= $weapon['add_con'] ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($weapon['add_dex']): ?>
                    <div class="bonus-item">
                        <label>DEX:</label>
                        <span class="bonus-positive">+<?= $weapon['add_dex'] ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($weapon['add_int']): ?>
                    <div class="bonus-item">
                        <label>INT:</label>
                        <span class="bonus-positive">+<?= $weapon['add_int'] ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($weapon['add_wis']): ?>
                    <div class="bonus-item">
                        <label>WIS:</label>
                        <span class="bonus-positive">+<?= $weapon['add_wis'] ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($weapon['add_cha']): ?>
                    <div class="bonus-item">
                        <label>CHA:</label>
                        <span class="bonus-positive">+<?= $weapon['add_cha'] ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($weapon['add_hp']): ?>
                    <div class="bonus-item">
                        <label>HP:</label>
                        <span class="bonus-positive">+<?= $weapon['add_hp'] ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($weapon['add_mp']): ?>
                    <div class="bonus-item">
                        <label>MP:</label>
                        <span class="bonus-positive">+<?= $weapon['add_mp'] ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($weapon['add_sp']): ?>
                    <div class="bonus-item">
                        <label>SP:</label>
                        <span class="bonus-positive">+<?= $weapon['add_sp'] ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Class Restrictions -->
            <div class="weapon-section">
                <h2>Class Restrictions</h2>
                <div class="class-grid">
                    <div class="class-item <?= $weapon['use_royal'] ? 'allowed' : 'restricted' ?>">
                        <span>Royal</span>
                        <span class="status"><?= $weapon['use_royal'] ? '✓' : '✗' ?></span>
                    </div>
                    <div class="class-item <?= $weapon['use_knight'] ? 'allowed' : 'restricted' ?>">
                        <span>Knight</span>
                        <span class="status"><?= $weapon['use_knight'] ? '✓' : '✗' ?></span>
                    </div>
                    <div class="class-item <?= $weapon['use_mage'] ? 'allowed' : 'restricted' ?>">
                        <span>Mage</span>
                        <span class="status"><?= $weapon['use_mage'] ? '✓' : '✗' ?></span>
                    </div>
                    <div class="class-item <?= $weapon['use_elf'] ? 'allowed' : 'restricted' ?>">
                        <span>Elf</span>
                        <span class="status"><?= $weapon['use_elf'] ? '✓' : '✗' ?></span>
                    </div>
                    <div class="class-item <?= $weapon['use_darkelf'] ? 'allowed' : 'restricted' ?>">
                        <span>Dark Elf</span>
                        <span class="status"><?= $weapon['use_darkelf'] ? '✓' : '✗' ?></span>
                    </div>
                    <div class="class-item <?= $weapon['use_dragonknight'] ? 'allowed' : 'restricted' ?>">
                        <span>Dragon Knight</span>
                        <span class="status"><?= $weapon['use_dragonknight'] ? '✓' : '✗' ?></span>
                    </div>
                    <div class="class-item <?= $weapon['use_illusionist'] ? 'allowed' : 'restricted' ?>">
                        <span>Illusionist</span>
                        <span class="status"><?= $weapon['use_illusionist'] ? '✓' : '✗' ?></span>
                    </div>
                    <div class="class-item <?= $weapon['use_warrior'] ? 'allowed' : 'restricted' ?>">
                        <span>Warrior</span>
                        <span class="status"><?= $weapon['use_warrior'] ? '✓' : '✗' ?></span>
                    </div>
                    <div class="class-item <?= $weapon['use_fencer'] ? 'allowed' : 'restricted' ?>">
                        <span>Fencer</span>
                        <span class="status"><?= $weapon['use_fencer'] ? '✓' : '✗' ?></span>
                    </div>
                    <div class="class-item <?= $weapon['use_lancer'] ? 'allowed' : 'restricted' ?>">
                        <span>Lancer</span>
                        <span class="status"><?= $weapon['use_lancer'] ? '✓' : '✗' ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Advanced Properties -->
            <div class="weapon-section">
                <h2>Advanced Properties</h2>
                <div class="advanced-grid">
                    <div class="advanced-item">
                        <label>Level Requirement:</label>
                        <span><?= $weapon['min_lvl'] ?> - <?= $weapon['max_lvl'] ?: 'No Limit' ?></span>
                    </div>
                    <div class="advanced-item">
                        <label>Magic Defense:</label>
                        <span><?= $weapon['m_def'] ?></span>
                    </div>
                    <div class="advanced-item">
                        <label>Haste:</label>
                        <span><?= $weapon['haste_item'] ? 'Yes' : 'No' ?></span>
                    </div>
                    <div class="advanced-item">
                        <label>Double Damage Chance:</label>
                        <span><?= $weapon['double_dmg_chance'] ?>%</span>
                    </div>
                    <div class="advanced-item">
                        <label>Magic Damage Modifier:</label>
                        <span><?= $weapon['magicdmgmodifier'] > 0 ? '+' : '' ?><?= $weapon['magicdmgmodifier'] ?></span>
                    </div>
                    <div class="advanced-item">
                        <label>Tradeable:</label>
                        <span><?= $weapon['trade'] ? 'Yes' : 'No' ?></span>
                    </div>
                    <div class="advanced-item">
                        <label>Retrievable:</label>
                        <span><?= $weapon['retrieve'] ? 'Yes' : 'No' ?></span>
                    </div>
                    <div class="advanced-item">
                        <label>Can Delete:</label>
                        <span><?= $weapon['cant_delete'] ? 'No' : 'Yes' ?></span>
                    </div>
                    <div class="advanced-item">
                        <label>Can Sell:</label>
                        <span><?= $weapon['cant_sell'] ? 'No' : 'Yes' ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Dropped By Section -->
            <?php $droppedBy = getMonstersByItemDrop($weapon['item_id']); ?>
            <div class="weapon-section">
                <h2>Dropped By</h2>
                <?php if (!empty($droppedBy)): ?>
                    <div class="dropped-by-grid">
                        <?php foreach ($droppedBy as $monster): ?>
                            <div class="monster-card">
                                <div class="monster-info">
                                    <div class="monster-image">
                                        <img src="<?= SITE_URL ?>/assets/img/icons/<?= getMonsterImagePath($monster['spriteId']) ?>" 
                                             alt="<?= htmlspecialchars($monster['desc_en']) ?>"
                                             onerror="this.src='<?= SITE_URL ?>/assets/img/placeholders/0.png'">
                                    </div>
                                    <div class="monster-details">
                                        <h4><?= htmlspecialchars(cleanDescriptionPrefix($monster['desc_en'])) ?></h4>
                                        <div class="monster-level">Level <?= $monster['lvl'] ?></div>
                                    </div>
                                </div>
                                <div class="drop-stats">
                                    <div class="drop-stat">
                                        <span class="drop-stat-label">Chance:</span>
                                        <span class="drop-stat-value"><?= formatDropChance($monster['chance']) ?></span>
                                    </div>
                                    <?php if ($monster['min'] > 0 || $monster['max'] > 0): ?>
                                    <div class="drop-stat">
                                        <span class="drop-stat-label">Quantity:</span>
                                        <span class="drop-stat-value"><?= $monster['min'] == $monster['max'] ? $monster['min'] : $monster['min'] . '-' . $monster['max'] ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($monster['Enchant'] > 0): ?>
                                    <div class="drop-stat">
                                        <span class="drop-stat-label">Enchant:</span>
                                        <span class="drop-stat-value">+<?= $monster['Enchant'] ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-drops-message">
                        There are no monsters that drop this item.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php getPageFooter(); ?>
