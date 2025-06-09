<?php
require_once dirname(dirname(__DIR__)) . '/includes/config.php';
require_once dirname(dirname(__DIR__)) . '/includes/header.php';

// Get armor ID from URL
$armorId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($armorId <= 0) {
    header('Location: armor_list.php');
    exit;
}

// Get armor data
$sql = "SELECT * FROM armor WHERE item_id = :item_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':item_id' => $armorId]);
$armor = $stmt->fetch();

if (!$armor) {
    header('Location: armor_list.php');
    exit;
}

// Note: Armor normalization functions are defined in includes/functions.php

// Call page header with armor name
$armorName = cleanDescriptionPrefix($armor['desc_en']);
getPageHeader($armorName);

// Create armor type and material for hero
$heroText = normalizeArmorType($armor['type']) . ' - ' . normalizeArmorMaterial($armor['material']);

// Render hero section
renderHero('armor', $armorName, $heroText);

// Get armor set information if this armor is part of a set
$armorSetInfo = null;
$setItems = [];
if (!empty($armor['Set_Id'])) {
    $setSql = "SELECT * FROM armor_set WHERE id = :set_id";
    $setStmt = $pdo->prepare($setSql);
    $setStmt->execute([':set_id' => $armor['Set_Id']]);
    $armorSetInfo = $setStmt->fetch();
    
    // Get all items in this armor set
    if ($armorSetInfo) {
        $setItemsSql = "SELECT item_id, desc_en, type, iconId FROM armor WHERE Set_Id = :set_id ORDER BY type, item_id";
        $setItemsStmt = $pdo->prepare($setItemsSql);
        $setItemsStmt->execute([':set_id' => $armor['Set_Id']]);
        $setItems = $setItemsStmt->fetchAll();
    }
}
?>

<main>
    <div class="main">
        <div class="container">
            <!-- Back Navigation -->
            <div class="back-nav">
                <a href="armor_list.php" class="back-btn">&larr; Back to Armor</a>
            </div>
            
            <!-- Main Content Row -->
            <div class="weapon-detail-row">
                <!-- Column 1: Image Preview -->
                <div class="weapon-image-col">
                    <div class="weapon-image-container">
                        <img src="<?= SITE_URL ?>/assets/img/icons/<?= $armor['iconId'] ?>.png" 
                             alt="<?= htmlspecialchars($armorName) ?>" 
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
                                <span><?= $armor['item_id'] ?></span>
                            </div>
                            <div class="info-item">
                                <label>Type:</label>
                                <span><?= normalizeArmorType($armor['type']) ?></span>
                            </div>
                            <div class="info-item">
                                <label>Material:</label>
                                <span><?= normalizeArmorMaterial($armor['material']) ?></span>
                            </div>
                            <div class="info-item">
                                <label>Weight:</label>
                                <span><?= number_format($armor['weight']) ?></span>
                            </div>
                            <?php $gradeDisplay = displayGrade($armor['itemGrade']); ?>
                            <?php if ($gradeDisplay): ?>
                            <div class="info-item">
                                <label>Grade:</label>
                                <?= $gradeDisplay ?>
                            </div>
                            <?php endif; ?>
                            <div class="info-item">
                                <label>Bless:</label>
                                <span><?= $armor['bless'] == 1 ? 'Normal' : ($armor['bless'] == 0 ? 'Cursed' : 'Blessed') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Defense Stats Section -->
            <div class="weapon-section">
                <h2>Defense Statistics</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Armor Class</h3>
                        <div class="stat-values">
                            <div class="stat-item">
                                <label>AC:</label>
                                <span class="stat-value"><?= $armor['ac'] ?></span>
                            </div>
                            <div class="stat-item">
                                <label>AC Sub:</label>
                                <span class="stat-value"><?= $armor['ac_sub'] ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Magic Defense</h3>
                        <div class="stat-values">
                            <div class="stat-item">
                                <label>M.DEF:</label>
                                <span class="stat-value"><?= $armor['m_def'] ?></span>
                            </div>
                            <div class="stat-item">
                                <label>Damage Reduction:</label>
                                <span class="stat-value"><?= $armor['damage_reduction'] ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Enchantment</h3>
                        <div class="stat-values">
                            <div class="stat-item">
                                <label>Safe Enchant:</label>
                                <span class="stat-value"><?= $armor['safenchant'] ?></span>
                            </div>
                            <div class="stat-item">
                                <label>Haste:</label>
                                <span class="stat-value"><?= $armor['haste_item'] ? 'Yes' : 'No' ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Elemental Resistances Section -->
            <?php if ($armor['defense_water'] || $armor['defense_wind'] || $armor['defense_fire'] || $armor['defense_earth']): ?>
            <div class="weapon-section">
                <h2>Elemental Resistances</h2>
                <div class="bonus-grid">
                    <?php if ($armor['defense_fire']): ?>
                    <div class="bonus-item">
                        <label>Fire:</label>
                        <span class="bonus-positive">+<?= $armor['defense_fire'] ?>%</span>
                    </div>
                    <?php endif; ?>
                    <?php if ($armor['defense_water']): ?>
                    <div class="bonus-item">
                        <label>Water:</label>
                        <span class="bonus-positive">+<?= $armor['defense_water'] ?>%</span>
                    </div>
                    <?php endif; ?>
                    <?php if ($armor['defense_wind']): ?>
                    <div class="bonus-item">
                        <label>Wind:</label>
                        <span class="bonus-positive">+<?= $armor['defense_wind'] ?>%</span>
                    </div>
                    <?php endif; ?>
                    <?php if ($armor['defense_earth']): ?>
                    <div class="bonus-item">
                        <label>Earth:</label>
                        <span class="bonus-positive">+<?= $armor['defense_earth'] ?>%</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Stat Bonuses Section -->
            <?php if ($armor['add_str'] || $armor['add_con'] || $armor['add_dex'] || $armor['add_int'] || $armor['add_wis'] || $armor['add_cha'] || $armor['add_hp'] || $armor['add_mp']): ?>
            <div class="weapon-section">
                <h2>Stat Bonuses</h2>
                <div class="bonus-grid">
                    <?php if ($armor['add_str']): ?>
                    <div class="bonus-item">
                        <label>STR:</label>
                        <span class="bonus-positive">+<?= $armor['add_str'] ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($armor['add_con']): ?>
                    <div class="bonus-item">
                        <label>CON:</label>
                        <span class="bonus-positive">+<?= $armor['add_con'] ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($armor['add_dex']): ?>
                    <div class="bonus-item">
                        <label>DEX:</label>
                        <span class="bonus-positive">+<?= $armor['add_dex'] ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($armor['add_int']): ?>
                    <div class="bonus-item">
                        <label>INT:</label>
                        <span class="bonus-positive">+<?= $armor['add_int'] ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($armor['add_wis']): ?>
                    <div class="bonus-item">
                        <label>WIS:</label>
                        <span class="bonus-positive">+<?= $armor['add_wis'] ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($armor['add_cha']): ?>
                    <div class="bonus-item">
                        <label>CHA:</label>
                        <span class="bonus-positive">+<?= $armor['add_cha'] ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($armor['add_hp']): ?>
                    <div class="bonus-item">
                        <label>HP:</label>
                        <span class="bonus-positive">+<?= $armor['add_hp'] ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($armor['add_mp']): ?>
                    <div class="bonus-item">
                        <label>MP:</label>
                        <span class="bonus-positive">+<?= $armor['add_mp'] ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($armor['add_sp']): ?>
                    <div class="bonus-item">
                        <label>SP:</label>
                        <span class="bonus-positive">+<?= $armor['add_sp'] ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Resistance Bonuses Section -->
            <?php if ($armor['regist_stone'] || $armor['regist_sleep'] || $armor['regist_freeze'] || $armor['regist_blind'] || $armor['regist_skill'] || $armor['regist_spirit'] || $armor['regist_dragon'] || $armor['regist_fear']): ?>
            <div class="weapon-section">
                <h2>Resistance Bonuses</h2>
                <div class="bonus-grid">
                    <?php if ($armor['regist_stone']): ?>
                    <div class="bonus-item">
                        <label>Stone:</label>
                        <span class="bonus-positive">+<?= $armor['regist_stone'] ?>%</span>
                    </div>
                    <?php endif; ?>
                    <?php if ($armor['regist_sleep']): ?>
                    <div class="bonus-item">
                        <label>Sleep:</label>
                        <span class="bonus-positive">+<?= $armor['regist_sleep'] ?>%</span>
                    </div>
                    <?php endif; ?>
                    <?php if ($armor['regist_freeze']): ?>
                    <div class="bonus-item">
                        <label>Freeze:</label>
                        <span class="bonus-positive">+<?= $armor['regist_freeze'] ?>%</span>
                    </div>
                    <?php endif; ?>
                    <?php if ($armor['regist_blind']): ?>
                    <div class="bonus-item">
                        <label>Blind:</label>
                        <span class="bonus-positive">+<?= $armor['regist_blind'] ?>%</span>
                    </div>
                    <?php endif; ?>
                    <?php if ($armor['regist_skill']): ?>
                    <div class="bonus-item">
                        <label>Skill:</label>
                        <span class="bonus-positive">+<?= $armor['regist_skill'] ?>%</span>
                    </div>
                    <?php endif; ?>
                    <?php if ($armor['regist_spirit']): ?>
                    <div class="bonus-item">
                        <label>Spirit:</label>
                        <span class="bonus-positive">+<?= $armor['regist_spirit'] ?>%</span>
                    </div>
                    <?php endif; ?>
                    <?php if ($armor['regist_dragon']): ?>
                    <div class="bonus-item">
                        <label>Dragon:</label>
                        <span class="bonus-positive">+<?= $armor['regist_dragon'] ?>%</span>
                    </div>
                    <?php endif; ?>
                    <?php if ($armor['regist_fear']): ?>
                    <div class="bonus-item">
                        <label>Fear:</label>
                        <span class="bonus-positive">+<?= $armor['regist_fear'] ?>%</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Class Restrictions -->
            <div class="weapon-section">
                <h2>Class Restrictions</h2>
                <div class="class-grid">
                    <div class="class-item <?= $armor['use_royal'] ? 'allowed' : 'restricted' ?>">
                        <span>Royal</span>
                        <span class="status"><?= $armor['use_royal'] ? '✓' : '✗' ?></span>
                    </div>
                    <div class="class-item <?= $armor['use_knight'] ? 'allowed' : 'restricted' ?>">
                        <span>Knight</span>
                        <span class="status"><?= $armor['use_knight'] ? '✓' : '✗' ?></span>
                    </div>
                    <div class="class-item <?= $armor['use_mage'] ? 'allowed' : 'restricted' ?>">
                        <span>Mage</span>
                        <span class="status"><?= $armor['use_mage'] ? '✓' : '✗' ?></span>
                    </div>
                    <div class="class-item <?= $armor['use_elf'] ? 'allowed' : 'restricted' ?>">
                        <span>Elf</span>
                        <span class="status"><?= $armor['use_elf'] ? '✓' : '✗' ?></span>
                    </div>
                    <div class="class-item <?= $armor['use_darkelf'] ? 'allowed' : 'restricted' ?>">
                        <span>Dark Elf</span>
                        <span class="status"><?= $armor['use_darkelf'] ? '✓' : '✗' ?></span>
                    </div>
                    <div class="class-item <?= $armor['use_dragonknight'] ? 'allowed' : 'restricted' ?>">
                        <span>Dragon Knight</span>
                        <span class="status"><?= $armor['use_dragonknight'] ? '✓' : '✗' ?></span>
                    </div>
                    <div class="class-item <?= $armor['use_illusionist'] ? 'allowed' : 'restricted' ?>">
                        <span>Illusionist</span>
                        <span class="status"><?= $armor['use_illusionist'] ? '✓' : '✗' ?></span>
                    </div>
                    <div class="class-item <?= $armor['use_warrior'] ? 'allowed' : 'restricted' ?>">
                        <span>Warrior</span>
                        <span class="status"><?= $armor['use_warrior'] ? '✓' : '✗' ?></span>
                    </div>
                    <div class="class-item <?= $armor['use_fencer'] ? 'allowed' : 'restricted' ?>">
                        <span>Fencer</span>
                        <span class="status"><?= $armor['use_fencer'] ? '✓' : '✗' ?></span>
                    </div>
                    <div class="class-item <?= $armor['use_lancer'] ? 'allowed' : 'restricted' ?>">
                        <span>Lancer</span>
                        <span class="status"><?= $armor['use_lancer'] ? '✓' : '✗' ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Armor Set Information -->
            <?php if ($armorSetInfo): ?>
            <div class="weapon-section">
                <h2>Armor Set Bonuses</h2>
                <div class="set-info">
                    <?php if (!empty($armorSetInfo['note'])): ?>
                    <p> <?= htmlspecialchars($armorSetInfo['note']) ?></p>
                    <?php endif; ?>
                    
                    <?php if ($armorSetInfo['min_enchant'] > 0): ?>
                    <p><strong>Minimum Enchant Required:</strong> +<?= $armorSetInfo['min_enchant'] ?></p>
                    <?php endif; ?>
                    
                    <!-- Set Items -->
                    <?php if (!empty($setItems)): ?>
                    <div class="armor-set-items">
                        <h4>Set Items (<?= count($setItems) ?> pieces):</h4>
                        <div class="set-items-grid">
                            <?php foreach ($setItems as $setItem): ?>
                            <a href="armor_detail.php?id=<?= $setItem['item_id'] ?>" class="set-item-card <?= $setItem['item_id'] == $armor['item_id'] ? 'current-item' : '' ?>">
                                <div class="set-item-image">
                                    <img src="<?= SITE_URL ?>/assets/img/icons/<?= $setItem['iconId'] ?>.png" 
                                         alt="<?= htmlspecialchars($setItem['desc_en']) ?>" 
                                         onerror="this.src='<?= SITE_URL ?>/assets/img/placeholders/0.png'">
                                </div>
                                <div class="set-item-name">
                                    <?= htmlspecialchars(cleanDescriptionPrefix($setItem['desc_en'])) ?>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
					<!-- Use armor_set.sql file to get more fileds.  Some are left out, because there is no point, when all the stats are at 0.  We left off at "abnormalStatusDamageReduction" column, which hasn't been added to this list. -->
                    <div class="set-bonuses">
                        <h4>Set Bonuses:</h4>
                        <div class="bonus-grid">
                            <?php if ($armorSetInfo['ac']): ?>
                            <div class="bonus-item">
                                <label>AC:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['ac'] ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($armorSetInfo['hp']): ?>
                            <div class="bonus-item">
                                <label>HP:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['hp'] ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($armorSetInfo['mp']): ?>
                            <div class="bonus-item">
                                <label>MP:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['mp'] ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($armorSetInfo['mr']): ?>
                            <div class="bonus-item">
                                <label>MR:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['mr'] ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($armorSetInfo['str']): ?>
                            <div class="bonus-item">
                                <label>STR:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['str'] ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($armorSetInfo['dex']): ?>
                            <div class="bonus-item">
                                <label>DEX:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['dex'] ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($armorSetInfo['con']): ?>
                            <div class="bonus-item">
                                <label>CON:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['con'] ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($armorSetInfo['wis']): ?>
                            <div class="bonus-item">
                                <label>WIS:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['wis'] ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($armorSetInfo['intl']): ?>
                            <div class="bonus-item">
                                <label>INT:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['intl'] ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($armorSetInfo['cha']): ?>
                            <div class="bonus-item">
                                <label>CHA:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['cha'] ?></span>
                            </div>
                            <?php endif; ?>
							<?php if ($armorSetInfo['hpr']): ?>
                            <div class="bonus-item">
                                <label>HPR:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['hpr'] ?></span>
                            </div>
                            <?php endif; ?>
							<?php if ($armorSetInfo['mpr']): ?>
                            <div class="bonus-item">
                                <label>MPR:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['mpr'] ?></span>
                            </div>
                            <?php endif; ?>
							<?php if ($armorSetInfo['shorthitup']): ?>
                            <div class="bonus-item">
                                <label>Melee Hit:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['shorthitup'] ?></span>
                            </div>
                            <?php endif; ?>
							<?php if ($armorSetInfo['shortCritical']): ?>
                            <div class="bonus-item">
                                <label>Melee Crit:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['shortCritical'] ?></span>
                            </div>
                            <?php endif; ?>
							<?php if ($armorSetInfo['longhitup']): ?>
                            <div class="bonus-item">
                                <label>Range Hit:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['longhitup'] ?></span>
                            </div>
                            <?php endif; ?>
							<?php if ($armorSetInfo['longdmgup']): ?>
                            <div class="bonus-item">
                                <label>Range Dmg:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['longdmgup'] ?></span>
                            </div>
                            <?php endif; ?>
							<?php if ($armorSetInfo['longCritical']): ?>
                            <div class="bonus-item">
                                <label>Range Crit:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['longCritical'] ?></span>
                            </div>
                            <?php endif; ?>
							<?php if ($armorSetInfo['sp']): ?>
                            <div class="bonus-item">
                                <label>SP:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['sp'] ?></span>
                            </div>
                            <?php endif; ?>
							<?php if ($armorSetInfo['magichitup']): ?>
                            <div class="bonus-item">
                                <label>Magic Hit:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['magichitup'] ?></span>
                            </div>
                            <?php endif; ?>
							<?php if ($armorSetInfo['magicCritical']): ?>
                            <div class="bonus-item">
                                <label>Magic Crit:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['magicCritical'] ?></span>
                            </div>
                            <?php endif; ?>
							<?php if ($armorSetInfo['earth']): ?>
                            <div class="bonus-item">
                                <label>Earth Resist:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['earth'] ?></span>
                            </div>
                            <?php endif; ?>
							<?php if ($armorSetInfo['fire']): ?>
                            <div class="bonus-item">
                                <label>Fire Resist:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['fire'] ?></span>
                            </div>
                            <?php endif; ?>
							<?php if ($armorSetInfo['wind']): ?>
                            <div class="bonus-item">
                                <label>Wind Resist:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['wind'] ?></span>
                            </div>
                            <?php endif; ?>
							<?php if ($armorSetInfo['water']): ?>
                            <div class="bonus-item">
                                <label>Water Resist:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['water'] ?></span>
                            </div>
                            <?php endif; ?>
							<?php if ($armorSetInfo['reduction']): ?>
                            <div class="bonus-item">
                                <label>Reduction:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['reduction'] ?></span>
                            </div>
                            <?php endif; ?>
							<?php if ($armorSetInfo['reductionEgnor']): ?>
                            <div class="bonus-item">
                                <label>Ignore Reduction:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['reductionEgnor'] ?></span>
                            </div>
                            <?php endif; ?>
							<?php if ($armorSetInfo['magicReduction']): ?>
                            <div class="bonus-item">
                                <label>Magic Reduction:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['magicReduction'] ?></span>
                            </div>
                            <?php endif; ?>
							<?php if ($armorSetInfo['PVPDamage']): ?>
                            <div class="bonus-item">
                                <label>PVP Damage:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['PVPDamage'] ?></span>
                            </div>
                            <?php endif; ?>
							<?php if ($armorSetInfo['PVPDamageReduction']): ?>
                            <div class="bonus-item">
                                <label>PVP Damage Reduction:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['PVPDamageReduction'] ?></span>
                            </div>
                            <?php endif; ?>
							<?php if ($armorSetInfo['PVPMagicDamageReduction']): ?>
                            <div class="bonus-item">
                                <label>PVP Damage Reduction (Magic):</label>
                                <span class="bonus-positive"><?= $armorSetInfo['PVPMagicDamageReduction'] ?></span>
                            </div>
                            <?php endif; ?>
							<?php if ($armorSetInfo['PVPReductionEgnor']): ?>
                            <div class="bonus-item">
                                <label>Ignore PVP Reduction:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['PVPReductionEgnor'] ?></span>
                            </div>
                            <?php endif; ?>
							<?php if ($armorSetInfo['PVPMagicDamageReductionEgnor']): ?>
                            <div class="bonus-item">
                                <label>Ignore PVP Reduction (Magic):</label>
                                <span class="bonus-positive"><?= $armorSetInfo['shorthitup'] ?></span>
                            </div>
                            <?php endif; ?>
							<?php if ($armorSetInfo['shorthitup']): ?>
                            <div class="bonus-item">
                                <label>Melee Hit:</label>
                                <span class="bonus-positive"><?= $armorSetInfo['shorthitup'] ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Advanced Properties -->
            <div class="weapon-section">
                <h2>Advanced Properties</h2>
                <div class="advanced-grid">
                    <div class="advanced-item">
                        <label>Level Requirement:</label>
                        <span><?= $armor['min_lvl'] ?> - <?= $armor['max_lvl'] ?: 'No Limit' ?></span>
                    </div>
                    <div class="advanced-item">
                        <label>Hit Rate Bonus:</label>
                        <span><?= $armor['hit_rate'] > 0 ? '+' : '' ?><?= $armor['hit_rate'] ?></span>
                    </div>
                    <div class="advanced-item">
                        <label>Damage Rate Bonus:</label>
                        <span><?= $armor['dmg_rate'] > 0 ? '+' : '' ?><?= $armor['dmg_rate'] ?></span>
                    </div>
                    <div class="advanced-item">
                        <label>Bow Hit Rate:</label>
                        <span><?= $armor['bow_hit_rate'] > 0 ? '+' : '' ?><?= $armor['bow_hit_rate'] ?></span>
                    </div>
                    <div class="advanced-item">
                        <label>Bow Damage Rate:</label>
                        <span><?= $armor['bow_dmg_rate'] > 0 ? '+' : '' ?><?= $armor['bow_dmg_rate'] ?></span>
                    </div>
                    <div class="advanced-item">
                        <label>Carry Bonus:</label>
                        <span><?= $armor['carryBonus'] ?></span>
                    </div>
                    <div class="advanced-item">
                        <label>Tradeable:</label>
                        <span><?= $armor['trade'] ? 'Yes' : 'No' ?></span>
                    </div>
                    <div class="advanced-item">
                        <label>Retrievable:</label>
                        <span><?= $armor['retrieve'] ? 'Yes' : 'No' ?></span>
                    </div>
                    <div class="advanced-item">
                        <label>Can Delete:</label>
                        <span><?= $armor['cant_delete'] ? 'No' : 'Yes' ?></span>
                    </div>
                    <div class="advanced-item">
                        <label>Can Sell:</label>
                        <span><?= $armor['cant_sell'] ? 'No' : 'Yes' ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Dropped By Section -->
            <?php $droppedBy = getMonstersByItemDrop($armor['item_id']); ?>
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
