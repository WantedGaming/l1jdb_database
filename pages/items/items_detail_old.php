<?php
require_once dirname(dirname(__DIR__)) . '/includes/config.php';
require_once dirname(dirname(__DIR__)) . '/includes/header.php';

// Get item ID from URL
$itemId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($itemId <= 0) {
    header('Location: items_list.php');
    exit;
}

// Get item data
$sql = "SELECT * FROM etcitem WHERE item_id = :item_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':item_id' => $itemId]);
$item = $stmt->fetch();

if (!$item) {
    header('Location: items_list.php');
    exit;
}

// Call page header with item name
$itemName = cleanDescriptionPrefix($item['desc_en']);
getPageHeader($itemName);

// Create item type and material for hero
$heroText = normalizeItemType($item['item_type']) . ' - ' . normalizeWeaponMaterial($item['material']);

// Render hero section
renderHero('items', $itemName, $heroText);
?>

<main>
    <div class="main">
        <div class="container">
            <!-- Back Navigation -->
            <div class="back-nav">
                <a href="items_list.php" class="back-btn">&larr; Back to Items</a>
            </div>
            
            <!-- Main Content Row -->
            <div class="detail-container">
                <div class="detail-content">
                    <!-- Row with image card and basic info -->
                    <div class="flex flex-wrap">
                        <!-- Column 1: Image Preview Card -->
                        <div class="weapon-image-card">
                            <div class="weapon-image-container">
                                <img src="<?= SITE_URL ?>/assets/img/icons/<?= $item['iconId'] ?>.png" 
                                     alt="<?= htmlspecialchars($itemName) ?>" 
                                     onerror="this.src='<?= SITE_URL ?>/assets/img/placeholders/0.png'"
                                     class="weapon-main-image">
                            </div>
                        </div>
                        
                        <!-- Column 2: Basic Information -->
                        <div class="weapon-info-wrap">
                            <div class="weapon-basic-info">
                                <h2>Basic Information</h2>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <label>Item ID:</label>
                                        <span><?= $item['item_id'] ?></span>
                                    </div>
                                    <div class="info-item">
                                        <label>Type:</label>
                                        <span><?= normalizeItemType($item['item_type']) ?></span>
                                    </div>
                                    <div class="info-item">
                                        <label>Material:</label>
                                        <span><?= normalizeWeaponMaterial($item['material']) ?></span>
                                    </div>
                                    <div class="info-item">
                                        <label>Weight:</label>
                                        <span><?= number_format($item['weight']) ?></span>
                                    </div>
                                    <div class="info-item">
                                        <label>Mergeable:</label>
                                        <span><?= $item['merge'] === 'true' ? 'Yes' : 'No' ?></span>
                                    </div>
                                    <div class="info-item">
                                        <label>Bless:</label>
                                        <span><?= $item['bless'] == 1 ? 'Normal' : ($item['bless'] == 0 ? 'Cursed' : 'Blessed') ?></span>
                                    </div>
                                    <?php $gradeDisplay = displayGrade($item['itemGrade'] ?? ''); ?>
                                    <?php if ($gradeDisplay): ?>
                                    <div class="info-item">
                                        <label>Grade:</label>
                                        <?= $gradeDisplay ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Usage Information Section -->
            <div class="detail-container">
                <div class="detail-content">
                    <h2 class="section-title">Usage Information</h2>
                    <div class="stats-wrapper">
                        <div class="stats-grid">
                            <div class="stat-card">
                                <h3>Usage</h3>
                                <div class="stat-values">
                                    <?php if ($item['max_charge_count'] > 0): ?>
                                    <div class="stat-item">
                                        <label>Max Charges:</label>
                                        <span class="stat-value"><?= $item['max_charge_count'] ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($item['food_volume'] > 0): ?>
                                    <div class="stat-item">
                                        <label>Food Volume:</label>
                                        <span class="stat-value"><?= $item['food_volume'] ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($item['use_type'] !== 'NONE'): ?>
                                    <div class="stat-item">
                                        <label>Use Type:</label>
                                        <span class="stat-value"><?= $item['use_type'] ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($item['delay_time'] > 0): ?>
                                    <div class="stat-item">
                                        <label>Delay Time:</label>
                                        <span class="stat-value"><?= $item['delay_time'] ?>ms</span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="stat-card">
                                <h3>Restrictions</h3>
                                <div class="stat-values">
                                    <?php if ($item['min_lvl'] > 0 || $item['max_lvl'] > 0): ?>
                                    <div class="stat-item">
                                        <label>Level Range:</label>
                                        <span class="stat-value"><?= $item['min_lvl'] ?> - <?= $item['max_lvl'] > 0 ? $item['max_lvl'] : 'No Limit' ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <div class="stat-item">
                                        <label>Tradeable:</label>
                                        <span class="stat-value"><?= $item['trade'] ? 'Yes' : 'No' ?></span>
                                    </div>
                                    <div class="stat-item">
                                        <label>Retrievable:</label>
                                        <span class="stat-value"><?= $item['retrieve'] ? 'Yes' : 'No' ?></span>
                                    </div>
                                    <div class="stat-item">
                                        <label>Can Delete:</label>
                                        <span class="stat-value"><?= $item['cant_delete'] ? 'No' : 'Yes' ?></span>
                                    </div>
                                    <div class="stat-item">
                                        <label>Can Sell:</label>
                                        <span class="stat-value"><?= $item['cant_sell'] ? 'No' : 'Yes' ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($item['item_type'] == 'FOOD' || $item['item_type'] == 'POTION'): ?>
                            <div class="stat-card">
                                <h3>Effects</h3>
                                <div class="stat-values">
                                    <?php if ($item['food_volume'] > 0): ?>
                                    <div class="stat-item">
                                        <label>Food Volume:</label>
                                        <span class="stat-value"><?= $item['food_volume'] ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($item['delay_effect'] > 0): ?>
                                    <div class="stat-item">
                                        <label>Effect:</label>
                                        <span class="stat-value"><?= $item['delay_effect'] ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($item['buffDurationSecond'] > 0): ?>
                                    <div class="stat-item">
                                        <label>Buff Duration:</label>
                                        <span class="stat-value"><?= $item['buffDurationSecond'] ?>s</span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Stat Bonuses Section -->
            <?php if ($item['add_str'] || $item['add_con'] || $item['add_dex'] || $item['add_int'] || $item['add_wis'] || $item['add_cha'] || $item['add_hp'] || $item['add_mp']): ?>
            <div class="detail-container">
                <div class="detail-content">
                    <h2 class="section-title">Stat Bonuses</h2>
                    <div class="bonus-grid">
                        <?php if ($item['add_str']): ?>
                        <div class="bonus-item">
                            <label>STR:</label>
                            <span class="bonus-positive">+<?= $item['add_str'] ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($item['add_con']): ?>
                        <div class="bonus-item">
                            <label>CON:</label>
                            <span class="bonus-positive">+<?= $item['add_con'] ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($item['add_dex']): ?>
                        <div class="bonus-item">
                            <label>DEX:</label>
                            <span class="bonus-positive">+<?= $item['add_dex'] ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($item['add_int']): ?>
                        <div class="bonus-item">
                            <label>INT:</label>
                            <span class="bonus-positive">+<?= $item['add_int'] ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($item['add_wis']): ?>
                        <div class="bonus-item">
                            <label>WIS:</label>
                            <span class="bonus-positive">+<?= $item['add_wis'] ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($item['add_cha']): ?>
                        <div class="bonus-item">
                            <label>CHA:</label>
                            <span class="bonus-positive">+<?= $item['add_cha'] ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($item['add_hp']): ?>
                        <div class="bonus-item">
                            <label>HP:</label>
                            <span class="bonus-positive">+<?= $item['add_hp'] ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($item['add_mp']): ?>
                        <div class="bonus-item">
                            <label>MP:</label>
                            <span class="bonus-positive">+<?= $item['add_mp'] ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($item['add_sp']): ?>
                        <div class="bonus-item">
                            <label>SP:</label>
                            <span class="bonus-positive">+<?= $item['add_sp'] ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Class Restrictions -->
            <div class="detail-container">
                <div class="detail-content">
                    <h2 class="section-title">Class Restrictions</h2>
                    <div class="class-grid">
                        <div class="class-item <?= $item['use_royal'] ? 'allowed' : 'restricted' ?>">
                            <span>Royal</span>
                            <span class="status"><?= $item['use_royal'] ? '✓' : '✗' ?></span>
                        </div>
                        <div class="class-item <?= $item['use_knight'] ? 'allowed' : 'restricted' ?>">
                            <span>Knight</span>
                            <span class="status"><?= $item['use_knight'] ? '✓' : '✗' ?></span>
                        </div>
                        <div class="class-item <?= $item['use_mage'] ? 'allowed' : 'restricted' ?>">
                            <span>Mage</span>
                            <span class="status"><?= $item['use_mage'] ? '✓' : '✗' ?></span>
                        </div>
                        <div class="class-item <?= $item['use_elf'] ? 'allowed' : 'restricted' ?>">
                            <span>Elf</span>
                            <span class="status"><?= $item['use_elf'] ? '✓' : '✗' ?></span>
                        </div>
                        <div class="class-item <?= $item['use_darkelf'] ? 'allowed' : 'restricted' ?>">
                            <span>Dark Elf</span>
                            <span class="status"><?= $item['use_darkelf'] ? '✓' : '✗' ?></span>
                        </div>
                        <div class="class-item <?= $item['use_dragonknight'] ? 'allowed' : 'restricted' ?>">
                            <span>Dragon Knight</span>
                            <span class="status"><?= $item['use_dragonknight'] ? '✓' : '✗' ?></span>
                        </div>
                        <div class="class-item <?= $item['use_illusionist'] ? 'allowed' : 'restricted' ?>">
                            <span>Illusionist</span>
                            <span class="status"><?= $item['use_illusionist'] ? '✓' : '✗' ?></span>
                        </div>
                        <div class="class-item <?= $item['use_warrior'] ? 'allowed' : 'restricted' ?>">
                            <span>Warrior</span>
                            <span class="status"><?= $item['use_warrior'] ? '✓' : '✗' ?></span>
                        </div>
                        <div class="class-item <?= $item['use_fencer'] ? 'allowed' : 'restricted' ?>">
                            <span>Fencer</span>
                            <span class="status"><?= $item['use_fencer'] ? '✓' : '✗' ?></span>
                        </div>
                        <div class="class-item <?= $item['use_lancer'] ? 'allowed' : 'restricted' ?>">
                            <span>Lancer</span>
                            <span class="status"><?= $item['use_lancer'] ? '✓' : '✗' ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Description Section -->
            <?php if (!empty($item['note'])): ?>
            <div class="detail-container">
                <div class="detail-content">
                    <h2 class="section-title">Description</h2>
                    <div class="description-text">
                        <?= nl2br(htmlspecialchars($item['note'])) ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Additional Properties -->
            <div class="detail-container">
                <div class="detail-content">
                    <h2 class="section-title">Additional Properties</h2>
                    <div class="advanced-grid">
                        <?php if (!empty($item['Magic_name'])): ?>
                        <div class="advanced-item">
                            <label>Magic Effect:</label>
                            <span><?= htmlspecialchars($item['Magic_name']) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($item['attr']) && $item['attr'] != 'NONE'): ?>
                        <div class="advanced-item">
                            <label>Attribute:</label>
                            <span><?= $item['attr'] ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($item['alignment']) && $item['alignment'] != 'NONE'): ?>
                        <div class="advanced-item">
                            <label>Alignment:</label>
                            <span><?= $item['alignment'] ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($item['spriteId'] > 0): ?>
                        <div class="advanced-item">
                            <label>Sprite ID:</label>
                            <span><?= $item['spriteId'] ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($item['delay_id'] > 0): ?>
                        <div class="advanced-item">
                            <label>Delay ID:</label>
                            <span><?= $item['delay_id'] ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($item['save_at_once'] > 0): ?>
                        <div class="advanced-item">
                            <label>Save At Once:</label>
                            <span><?= $item['save_at_once'] ? 'Yes' : 'No' ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($item['max_charge_count'] > 0): ?>
                        <div class="advanced-item">
                            <label>Max Charge Count:</label>
                            <span><?= $item['max_charge_count'] ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($item['dmg_small'] > 0): ?>
                        <div class="advanced-item">
                            <label>Damage (Small):</label>
                            <span><?= $item['dmg_small'] ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($item['dmg_large'] > 0): ?>
                        <div class="advanced-item">
                            <label>Damage (Large):</label>
                            <span><?= $item['dmg_large'] ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($item['prob'] > 0): ?>
                        <div class="advanced-item">
                            <label>Probability:</label>
                            <span><?= $item['prob'] ?>%</span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Dropped By Section -->
            <?php $droppedBy = getMonstersByItemDrop($item['item_id']); ?>
            <div class="detail-container">
                <div class="detail-content">
                    <h2 class="section-title">Dropped By</h2>
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
    </div>
</main>

<?php getPageFooter(); ?>
