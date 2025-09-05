<?php
require_once dirname(dirname(__DIR__)) . '/includes/config.php';
require_once dirname(dirname(__DIR__)) . '/includes/header.php';

// Get item ID from URL
$itemId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($itemId <= 0) {
    header('Location: item_list.php');
    exit;
}

// Get item data
$sql = "SELECT * FROM etcitem WHERE item_id = :item_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':item_id' => $itemId]);
$item = $stmt->fetch();

if (!$item) {
    header('Location: item_list.php');
    exit;
}

// Call page header with item name
$itemName = cleanDescriptionPrefix($item['desc_en']);
getPageHeader($itemName);

// Create item type and material for hero
$heroText = normalizeItemType($item['item_type']) . ' - ' . normalizeItemMaterial($item['material']);

// Render hero section
renderHero('items', $itemName, $heroText);
?>

<main>
    <div class="main">
        <div class="container">
            <!-- Back Navigation -->
            <div class="back-nav">
                <a href="item_list.php" class="back-btn">&larr; Back to Items</a>
            </div>
            
            <!-- Main Content Row -->
            <div class="weapon-detail-row">
                <!-- Column 1: Image Preview -->
                <div class="weapon-image-col">
                    <div class="weapon-image-container">
                        <img src="<?= SITE_URL ?>/assets/img/icons/<?= $item['iconId'] ?>.png" 
                             alt="<?= htmlspecialchars($itemName) ?>" 
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
                                <label>Item ID:</label>
                                <span><?= $item['item_id'] ?></span>
                            </div>
                            <div class="info-item">
                                <label>Type:</label>
                                <span><?= normalizeItemType($item['item_type']) ?></span>
                            </div>
                            <div class="info-item">
                                <label>Use Type:</label>
                                <span><?= normalizeItemUseType($item['use_type']) ?></span>
                            </div>
                            <div class="info-item">
                                <label>Material:</label>
                                <span><?= normalizeItemMaterial($item['material']) ?></span>
                            </div>
                            <div class="info-item">
                                <label>Weight:</label>
                                <span><?= number_format($item['weight']) ?></span>
                            </div>
                            <?php $gradeDisplay = displayGrade($item['itemGrade']); ?>
                            <?php if ($gradeDisplay): ?>
                            <div class="info-item">
                                <label>Grade:</label>
                                <?= $gradeDisplay ?>
                            </div>
                            <?php endif; ?>
                            <div class="info-item">
                                <label>Mergeable:</label>
                                <span><?= $item['merge'] === 'true' ? 'Yes' : 'No' ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Combat Stats Section -->
            <?php if ($item['dmg_small'] || $item['dmg_large'] || $item['ac_bonus'] || $item['shortHit'] || $item['shortDmg'] || $item['longHit'] || $item['longDmg']): ?>
            <div class="weapon-section">
                <h2>Combat Statistics</h2>
                <div class="stats-grid">
                    <?php if ($item['dmg_small'] || $item['dmg_large']): ?>
                    <div class="stat-card">
                        <h3>Damage</h3>
                        <div class="stat-values">
                            <div class="stat-item">
                                <label>Small:</label>
                                <span class="stat-value"><?= $item['dmg_small'] ?></span>
                            </div>
                            <div class="stat-item">
                                <label>Large:</label>
                                <span class="stat-value"><?= $item['dmg_large'] ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($item['shortHit'] || $item['shortDmg']): ?>
                    <div class="stat-card">
                        <h3>Melee Modifiers</h3>
                        <div class="stat-values">
                            <div class="stat-item">
                                <label>Hit Bonus:</label>
                                <span class="stat-value"><?= $item['shortHit'] > 0 ? '+' : '' ?><?= $item['shortHit'] ?></span>
                            </div>
                            <div class="stat-item">
                                <label>Damage Bonus:</label>
                                <span class="stat-value"><?= $item['shortDmg'] > 0 ? '+' : '' ?><?= $item['shortDmg'] ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($item['longHit'] || $item['longDmg']): ?>
                    <div class="stat-card">
                        <h3>Ranged Modifiers</h3>
                        <div class="stat-values">
                            <div class="stat-item">
                                <label>Hit Bonus:</label>
                                <span class="stat-value"><?= $item['longHit'] > 0 ? '+' : '' ?><?= $item['longHit'] ?></span>
                            </div>
                            <div class="stat-item">
                                <label>Damage Bonus:</label>
                                <span class="stat-value"><?= $item['longDmg'] > 0 ? '+' : '' ?><?= $item['longDmg'] ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($item['ac_bonus'] || $item['m_def']): ?>
                    <div class="stat-card">
                        <h3>Defense</h3>
                        <div class="stat-values">
                            <?php if ($item['ac_bonus']): ?>
                            <div class="stat-item">
                                <label>AC Bonus:</label>
                                <span class="stat-value"><?= $item['ac_bonus'] > 0 ? '+' : '' ?><?= $item['ac_bonus'] ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($item['m_def']): ?>
                            <div class="stat-item">
                                <label>Magic Defense:</label>
                                <span class="stat-value"><?= $item['m_def'] ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Stat Bonuses Section -->
            <?php if ($item['add_str'] || $item['add_con'] || $item['add_dex'] || $item['add_int'] || $item['add_wis'] || $item['add_cha'] || $item['add_hp'] || $item['add_mp'] || $item['add_hpr'] || $item['add_mpr'] || $item['add_sp']): ?>
            <div class="weapon-section">
                <h2>Stat Bonuses</h2>
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
                    <?php if ($item['add_hpr']): ?>
                    <div class="bonus-item">
                        <label>HPR:</label>
                        <span class="bonus-positive">+<?= $item['add_hpr'] ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($item['add_mpr']): ?>
                    <div class="bonus-item">
                        <label>MPR:</label>
                        <span class="bonus-positive">+<?= $item['add_mpr'] ?></span>
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
            <?php endif; ?>
            
            <!-- Elemental Resistances Section -->
            <?php if ($item['defense_water'] || $item['defense_wind'] || $item['defense_fire'] || $item['defense_earth']): ?>
            <div class="weapon-section">
                <h2>Elemental Resistances</h2>
                <div class="bonus-grid">
                    <?php if ($item['defense_fire']): ?>
                    <div class="bonus-item">
                        <label>Fire:</label>
                        <span class="bonus-positive">+<?= $item['defense_fire'] ?>%</span>
                    </div>
                    <?php endif; ?>
                    <?php if ($item['defense_water']): ?>
                    <div class="bonus-item">
                        <label>Water:</label>
                        <span class="bonus-positive">+<?= $item['defense_water'] ?>%</span>
                    </div>
                    <?php endif; ?>
                    <?php if ($item['defense_wind']): ?>
                    <div class="bonus-item">
                        <label>Wind:</label>
                        <span class="bonus-positive">+<?= $item['defense_wind'] ?>%</span>
                    </div>
                    <?php endif; ?>
                    <?php if ($item['defense_earth']): ?>
                    <div class="bonus-item">
                        <label>Earth:</label>
                        <span class="bonus-positive">+<?= $item['defense_earth'] ?>%</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Magic & Spell Properties Section -->
            <?php if (!empty($item['Magic_name']) || $item['level'] || $item['attr'] !== 'NONE'): ?>
            <div class="weapon-section">
                <h2>Magic Properties</h2>
                <div class="advanced-grid">
                    <?php if (!empty($item['Magic_name'])): ?>
                    <div class="advanced-item">
                        <label>Magic Name:</label>
                        <span><?= htmlspecialchars($item['Magic_name']) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($item['level']): ?>
                    <div class="advanced-item">
                        <label>Magic Level:</label>
                        <span><?= $item['level'] ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($item['attr'] !== 'NONE'): ?>
                    <div class="advanced-item">
                        <label>Element:</label>
                        <span><?= normalizeElement($item['attr']) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($item['alignment'] !== 'NONE'): ?>
                    <div class="advanced-item">
                        <label>Alignment:</label>
                        <span><?= normalizeAlignment($item['alignment']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Class Restrictions -->
            <div class="weapon-section">
                <h2>Class Restrictions</h2>
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
            
            <!-- Advanced Properties -->
            <div class="weapon-section">
                <h2>Advanced Properties</h2>
                <div class="advanced-grid">
                    <div class="advanced-item">
                        <label>Level Requirement:</label>
                        <span><?= $item['min_lvl'] ?> - <?= $item['max_lvl'] ?: 'No Limit' ?></span>
                    </div>
                    <div class="advanced-item">
                        <label>Max Charge Count:</label>
                        <span><?= $item['max_charge_count'] ?></span>
                    </div>
                    <div class="advanced-item">
                        <label>Carry Bonus:</label>
                        <span><?= $item['carryBonus'] ?></span>
                    </div>
                    <div class="advanced-item">
                        <label>Food Volume:</label>
                        <span><?= $item['food_volume'] ?></span>
                    </div>
                    <div class="advanced-item">
                        <label>Delay Time:</label>
                        <span><?= $item['delay_time'] ?> ms</span>
                    </div>
                    <div class="advanced-item">
                        <label>Skill Type:</label>
                        <span><?= ucfirst($item['skill_type']) ?></span>
                    </div>
                    <div class="advanced-item">
                        <label>Tradeable:</label>
                        <span><?= $item['trade'] ? 'Yes' : 'No' ?></span>
                    </div>
                    <div class="advanced-item">
                        <label>Retrievable:</label>
                        <span><?= $item['retrieve'] ? 'Yes' : 'No' ?></span>
                    </div>
                    <div class="advanced-item">
                        <label>Can Delete:</label>
                        <span><?= $item['cant_delete'] ? 'No' : 'Yes' ?></span>
                    </div>
                    <div class="advanced-item">
                        <label>Can Sell:</label>
                        <span><?= $item['cant_sell'] ? 'No' : 'Yes' ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Special Bonuses Section -->
            <?php if ($item['regist_stone'] || $item['regist_sleep'] || $item['regist_freeze'] || $item['regist_blind'] || $item['regist_skill'] || $item['regist_spirit'] || $item['regist_dragon'] || $item['regist_fear'] || $item['regist_all']): ?>
            <div class="weapon-section">
                <h2>Resistance Bonuses</h2>
                <div class="bonus-grid">
                    <?php if ($item['regist_stone']): ?>
                    <div class="bonus-item">
                        <label>Stone:</label>
                        <span class="bonus-positive">+<?= $item['regist_stone'] ?>%</span>
                    </div>
                    <?php endif; ?>
                    <?php if ($item['regist_sleep']): ?>
                    <div class="bonus-item">
                        <label>Sleep:</label>
                        <span class="bonus-positive">+<?= $item['regist_sleep'] ?>%</span>
                    </div>
                    <?php endif; ?>
                    <?php if ($item['regist_freeze']): ?>
                    <div class="bonus-item">
                        <label>Freeze:</label>
                        <span class="bonus-positive">+<?= $item['regist_freeze'] ?>%</span>
                    </div>
                    <?php endif; ?>
                    <?php if ($item['regist_blind']): ?>
                    <div class="bonus-item">
                        <label>Blind:</label>
                        <span class="bonus-positive">+<?= $item['regist_blind'] ?>%</span>
                    </div>
                    <?php endif; ?>
                    <?php if ($item['regist_skill']): ?>
                    <div class="bonus-item">
                        <label>Skill:</label>
                        <span class="bonus-positive">+<?= $item['regist_skill'] ?>%</span>
                    </div>
                    <?php endif; ?>
                    <?php if ($item['regist_spirit']): ?>
                    <div class="bonus-item">
                        <label>Spirit:</label>
                        <span class="bonus-positive">+<?= $item['regist_spirit'] ?>%</span>
                    </div>
                    <?php endif; ?>
                    <?php if ($item['regist_dragon']): ?>
                    <div class="bonus-item">
                        <label>Dragon:</label>
                        <span class="bonus-positive">+<?= $item['regist_dragon'] ?>%</span>
                    </div>
                    <?php endif; ?>
                    <?php if ($item['regist_fear']): ?>
                    <div class="bonus-item">
                        <label>Fear:</label>
                        <span class="bonus-positive">+<?= $item['regist_fear'] ?>%</span>
                    </div>
                    <?php endif; ?>
                    <?php if ($item['regist_all']): ?>
                    <div class="bonus-item">
                        <label>All Resistances:</label>
                        <span class="bonus-positive">+<?= $item['regist_all'] ?>%</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Dropped By Section -->
            <?php $droppedBy = getMonstersByItemDrop($item['item_id']); ?>
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
