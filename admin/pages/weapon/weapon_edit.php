<?php
require_once dirname(dirname(dirname(__DIR__))) . '/includes/config.php';
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

// Function to get enum values for form options
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

function getWeaponMaterials() {
    return [
        'NONE(-)' => 'None',
        'LIQUID(액체)' => 'Liquid',
        'WAX(밀랍)' => 'Wax',
        'VEGGY(식물성)' => 'Vegetable',
        'FLESH(동물성)' => 'Flesh',
        'PAPER(종이)' => 'Paper',
        'CLOTH(천)' => 'Cloth',
        'LEATHER(가죽)' => 'Leather',
        'WOOD(나무)' => 'Wood',
        'BONE(뼈)' => 'Bone',
        'DRAGON_HIDE(용비늘)' => 'Dragon Hide',
        'IRON(철)' => 'Iron',
        'METAL(금속)' => 'Metal',
        'COPPER(구리)' => 'Copper',
        'SILVER(은)' => 'Silver',
        'GOLD(금)' => 'Gold',
        'PLATINUM(백금)' => 'Platinum',
        'MITHRIL(미스릴)' => 'Mithril',
        'PLASTIC(블랙미스릴)' => 'Black Mithril',
        'GLASS(유리)' => 'Glass',
        'GEMSTONE(보석)' => 'Gemstone',
        'MINERAL(광석)' => 'Mineral',
        'ORIHARUKON(오리하루콘)' => 'Oriharukon',
        'DRANIUM(드라니움)' => 'Dranium'
    ];
}
?>

<div class="admin-content-wrapper">
    <!-- Back Navigation -->
    <div class="admin-breadcrumb">
        <ul class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="weapon_list.php">Weapons</a></li>
            <li class="breadcrumb-separator">/</li>
            <li class="breadcrumb-item"><?= htmlspecialchars($weapon['desc_en']) ?></li>
            <li class="breadcrumb-separator">/</li>
            <li class="breadcrumb-item">Edit</li>
        </ul>
    </div>

    <div class="admin-header">
        <h1>Edit Weapon: <?= htmlspecialchars($weapon['desc_en']) ?></h1>
        <div class="admin-header-actions">
            <a href="weapon_list.php" class="admin-btn admin-btn-secondary">
                <span>←</span> Back to List
            </a>
        </div>
    </div>

    <form action="weapon_process.php" method="POST" class="admin-form" id="weaponForm">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="item_id" value="<?= $weaponId ?>">

        <!-- Progress Indicator -->
        <div class="form-progress">
            <div class="form-progress-bar" style="width: 0%"></div>
        </div>

        <!-- Tabbed Interface -->
        <div class="form-tabs">
            <button type="button" class="form-tab active" data-tab="basic">Basic Info</button>
            <button type="button" class="form-tab" data-tab="combat">Combat Stats</button>
            <button type="button" class="form-tab" data-tab="bonuses">Stat Bonuses</button>
            <button type="button" class="form-tab" data-tab="classes">Class Usage</button>
            <button type="button" class="form-tab" data-tab="resistances">Resistances</button>
            <button type="button" class="form-tab" data-tab="hitups">Hit Bonuses</button>
            <button type="button" class="form-tab" data-tab="reduction">Damage Reduction</button>
            <button type="button" class="form-tab" data-tab="pvp">PVP Settings</button>
            <button type="button" class="form-tab" data-tab="special">Special Effects</button>
            <button type="button" class="form-tab" data-tab="potions">Potion Effects</button>
            <button type="button" class="form-tab" data-tab="advanced">Advanced Properties</button>
        </div>

        <!-- Tab 1: Basic Information -->
        <div class="form-tab-content active" id="tab-basic">
            <div class="form-grid-2">
                <!-- Image Preview Column -->
                <div class="field-group">
                    <h3>Visual</h3>
                    <div class="preview-container">
                        <img src="/l1jdb_database/assets/img/icons/<?= $weapon['iconId'] ?>.png" 
                             alt="Weapon Icon" 
                             onerror="this.src='/l1jdb_database/assets/img/placeholders/0.png'"
                             class="preview-image"
                             id="weapon-preview"
                             style="width: 64px; height: 64px; object-fit: contain;">
                        <div class="preview-info">
                            <div class="form-group">
                                <label for="iconId">Icon ID</label>
                                <input type="number" id="iconId" name="iconId" value="<?= $weapon['iconId'] ?>">
                            </div>
                            <div class="form-group">
                                <label for="spriteId">Sprite ID</label>
                                <input type="number" id="spriteId" name="spriteId" value="<?= $weapon['spriteId'] ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Basic Properties Column -->
                <div class="field-group">
                    <h3>Basic Properties</h3>
                    <div class="form-group">
                        <label for="item_name_id">Item Name ID</label>
                        <input type="number" id="item_name_id" name="item_name_id" value="<?= $weapon['item_name_id'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="desc_en">English Name</label>
                        <input type="text" id="desc_en" name="desc_en" value="<?= htmlspecialchars($weapon['desc_en']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="desc_kr">Korean Name</label>
                        <input type="text" id="desc_kr" name="desc_kr" value="<?= htmlspecialchars($weapon['desc_kr']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="desc_powerbook">Powerbook Description</label>
                        <input type="text" id="desc_powerbook" name="desc_powerbook" value="<?= htmlspecialchars($weapon['desc_powerbook']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="desc_id">Description ID</label>
                        <input type="text" id="desc_id" name="desc_id" value="<?= htmlspecialchars($weapon['desc_id']) ?>">
                    </div>
                </div>
            </div>

            <div class="form-grid-3">
                <div class="form-group">
                    <label for="itemGrade">Item Grade</label>
                    <select id="itemGrade" name="itemGrade">
                        <?php foreach (getWeaponGrades() as $grade => $label): ?>
                            <option value="<?= $grade ?>" <?= $weapon['itemGrade'] == $grade ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="type">Weapon Type</label>
                    <select id="type" name="type" required>
                        <?php foreach (getWeaponTypes() as $type => $label): ?>
                            <option value="<?= $type ?>" <?= $weapon['type'] == $type ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="material">Material</label>
                    <select id="material" name="material">
                        <?php foreach (getWeaponMaterials() as $material => $label): ?>
                            <option value="<?= $material ?>" <?= $weapon['material'] == $material ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="note">Notes</label>
                <textarea id="note" name="note" rows="4"><?= htmlspecialchars($weapon['note']) ?></textarea>
            </div>
        </div>

        <!-- Tab 2: Combat Statistics -->
        <div class="form-tab-content" id="tab-combat">
            <div class="weapon-advanced-grid">
                <div class="field-group stat-group-neutral">
                    <h3>Damage Statistics</h3>
                    <div class="form-grid-2" style="gap: 1rem; margin-bottom: 1rem;">
                        <div class="form-group" style="width: 100%;">
                            <label for="dmg_small">Small Damage</label>
                            <input type="number" id="dmg_small" name="dmg_small" value="<?= $weapon['dmg_small'] ?>" required style="width: 100%; box-sizing: border-box;">
                        </div>
                        <div class="form-group" style="width: 100%;">
                            <label for="dmg_large">Large Damage</label>
                            <input type="number" id="dmg_large" name="dmg_large" value="<?= $weapon['dmg_large'] ?>" required style="width: 100%; box-sizing: border-box;">
                        </div>
                    </div>
                    <div class="form-grid-2" style="gap: 1rem; margin-bottom: 1rem;">
                        <div class="form-group" style="width: 100%;">
                            <label for="hitmodifier">Hit Modifier</label>
                            <input type="number" id="hitmodifier" name="hitmodifier" value="<?= $weapon['hitmodifier'] ?>" style="width: 100%; box-sizing: border-box;">
                        </div>
                        <div class="form-group" style="width: 100%;">
                            <label for="dmgmodifier">Damage Modifier</label>
                            <input type="number" id="dmgmodifier" name="dmgmodifier" value="<?= $weapon['dmgmodifier'] ?>" style="width: 100%; box-sizing: border-box;">
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label for="magicdmgmodifier">Magic Damage Modifier</label>
                        <input type="number" id="magicdmgmodifier" name="magicdmgmodifier" value="<?= $weapon['magicdmgmodifier'] ?>" style="width: 100%; box-sizing: border-box;">
                    </div>
                </div>

                <div class="field-group stat-group-special">
                    <h3>Enchantment & Durability</h3>
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label for="safenchant">Safe Enchant Level</label>
                        <input type="number" id="safenchant" name="safenchant" value="<?= $weapon['safenchant'] ?>" min="0" max="15" style="width: 100%; box-sizing: border-box;">
                    </div>
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label for="weight">Weight</label>
                        <input type="number" id="weight" name="weight" value="<?= $weapon['weight'] ?>" required style="width: 100%; box-sizing: border-box;">
                    </div>
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label for="canbedmg">Can Be Damaged</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="canbedmg" name="canbedmg" value="1" <?= $weapon['canbedmg'] ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label for="max_use_time">Max Use Time</label>
                        <input type="number" id="max_use_time" name="max_use_time" value="<?= $weapon['max_use_time'] ?>" style="width: 100%; box-sizing: border-box;">
                    </div>
                </div>

                <div class="field-group stat-group-positive">
                    <h3>Special Combat Effects</h3>
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label for="haste_item">Haste Effect</label>
                        <input type="number" id="haste_item" name="haste_item" value="<?= $weapon['haste_item'] ?>" min="0" max="2" style="width: 100%; box-sizing: border-box;">
                    </div>
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label for="double_dmg_chance">Double Damage Chance (%)</label>
                        <input type="number" id="double_dmg_chance" name="double_dmg_chance" value="<?= $weapon['double_dmg_chance'] ?>" min="0" max="100" style="width: 100%; box-sizing: border-box;">
                    </div>
                    <div class="form-grid-2" style="gap: 1rem; margin-bottom: 1rem;">
                        <div class="form-group" style="width: 100%;">
                            <label for="min_lvl">Minimum Level</label>
                            <input type="number" id="min_lvl" name="min_lvl" value="<?= $weapon['min_lvl'] ?>" min="0" style="width: 100%; box-sizing: border-box;">
                        </div>
                        <div class="form-group" style="width: 100%;">
                            <label for="max_lvl">Maximum Level</label>
                            <input type="number" id="max_lvl" name="max_lvl" value="<?= $weapon['max_lvl'] ?>" min="0" style="width: 100%; box-sizing: border-box;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 3: Stat Bonuses -->
        <div class="form-tab-content" id="tab-bonuses">
            <div class="weapon-advanced-grid">
                <div class="field-group stat-group-positive">
                    <h3>Primary Stats</h3>
                    <div class="form-grid-3" style="gap: 1rem; margin-bottom: 1rem;">
                        <div class="form-group" style="width: 100%;">
                            <label for="add_str">Strength (STR)</label>
                            <input type="number" id="add_str" name="add_str" value="<?= $weapon['add_str'] ?>" style="width: 100%; box-sizing: border-box;">
                        </div>
                        <div class="form-group" style="width: 100%;">
                            <label for="add_con">Constitution (CON)</label>
                            <input type="number" id="add_con" name="add_con" value="<?= $weapon['add_con'] ?>" style="width: 100%; box-sizing: border-box;">
                        </div>
                        <div class="form-group" style="width: 100%;">
                            <label for="add_dex">Dexterity (DEX)</label>
                            <input type="number" id="add_dex" name="add_dex" value="<?= $weapon['add_dex'] ?>" style="width: 100%; box-sizing: border-box;">
                        </div>
                        <div class="form-group" style="width: 100%;">
                            <label for="add_int">Intelligence (INT)</label>
                            <input type="number" id="add_int" name="add_int" value="<?= $weapon['add_int'] ?>" style="width: 100%; box-sizing: border-box;">
                        </div>
                        <div class="form-group" style="width: 100%;">
                            <label for="add_wis">Wisdom (WIS)</label>
                            <input type="number" id="add_wis" name="add_wis" value="<?= $weapon['add_wis'] ?>" style="width: 100%; box-sizing: border-box;">
                        </div>
                        <div class="form-group" style="width: 100%;">
                            <label for="add_cha">Charisma (CHA)</label>
                            <input type="number" id="add_cha" name="add_cha" value="<?= $weapon['add_cha'] ?>" style="width: 100%; box-sizing: border-box;">
                        </div>
                    </div>
                </div>

                <div class="field-group stat-group-positive">
                    <h3>Health & Magic</h3>
                    <div class="form-grid-3" style="gap: 1rem; margin-bottom: 1rem;">
                        <div class="form-group" style="width: 100%;">
                            <label for="add_hp">Hit Points (HP)</label>
                            <input type="number" id="add_hp" name="add_hp" value="<?= $weapon['add_hp'] ?>" style="width: 100%; box-sizing: border-box;">
                        </div>
                        <div class="form-group" style="width: 100%;">
                            <label for="add_mp">Magic Points (MP)</label>
                            <input type="number" id="add_mp" name="add_mp" value="<?= $weapon['add_mp'] ?>" style="width: 100%; box-sizing: border-box;">
                        </div>
                        <div class="form-group" style="width: 100%;">
                            <label for="add_sp">Spell Points (SP)</label>
                            <input type="number" id="add_sp" name="add_sp" value="<?= $weapon['add_sp'] ?>" style="width: 100%; box-sizing: border-box;">
                        </div>
                        <div class="form-group" style="width: 100%;">
                            <label for="add_hpr">HP Regeneration</label>
                            <input type="number" id="add_hpr" name="add_hpr" value="<?= $weapon['add_hpr'] ?>" style="width: 100%; box-sizing: border-box;">
                        </div>
                        <div class="form-group" style="width: 100%;">
                            <label for="add_mpr">MP Regeneration</label>
                            <input type="number" id="add_mpr" name="add_mpr" value="<?= $weapon['add_mpr'] ?>" style="width: 100%; box-sizing: border-box;">
                        </div>
                        <div class="form-group" style="width: 100%;">
                            <label for="m_def">Magic Defense</label>
                            <input type="number" id="m_def" name="m_def" value="<?= $weapon['m_def'] ?>" style="width: 100%; box-sizing: border-box;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 4: Class Usage -->
        <div class="form-tab-content" id="tab-classes">
            <div class="field-group">
                <h3>Class Restrictions</h3>
                <div class="form-grid-5">
                    <div class="form-group">
                        <label for="use_royal">Royal</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="use_royal" name="use_royal" value="1" <?= $weapon['use_royal'] ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="use_knight">Knight</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="use_knight" name="use_knight" value="1" <?= $weapon['use_knight'] ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="use_mage">Mage</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="use_mage" name="use_mage" value="1" <?= $weapon['use_mage'] ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="use_elf">Elf</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="use_elf" name="use_elf" value="1" <?= $weapon['use_elf'] ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="use_darkelf">Dark Elf</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="use_darkelf" name="use_darkelf" value="1" <?= $weapon['use_darkelf'] ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="use_dragonknight">Dragon Knight</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="use_dragonknight" name="use_dragonknight" value="1" <?= $weapon['use_dragonknight'] ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="use_illusionist">Illusionist</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="use_illusionist" name="use_illusionist" value="1" <?= $weapon['use_illusionist'] ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="use_warrior">Warrior</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="use_warrior" name="use_warrior" value="1" <?= $weapon['use_warrior'] ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="use_fencer">Fencer</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="use_fencer" name="use_fencer" value="1" <?= $weapon['use_fencer'] ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="use_lancer">Lancer</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="use_lancer" name="use_lancer" value="1" <?= $weapon['use_lancer'] ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 5: Resistances -->
        <div class="form-tab-content" id="tab-resistances">
            <div class="field-group stat-group-neutral">
                <h3>Magic Resistances</h3>
                <div class="form-grid-3">
                    <div class="form-group">
                        <label for="regist_skill">Skill Resistance</label>
                        <input type="number" id="regist_skill" name="regist_skill" value="<?= $weapon['regist_skill'] ?>" min="-128" max="127">
                    </div>
                    <div class="form-group">
                        <label for="regist_spirit">Spirit Resistance</label>
                        <input type="number" id="regist_spirit" name="regist_spirit" value="<?= $weapon['regist_spirit'] ?>" min="-128" max="127">
                    </div>
                    <div class="form-group">
                        <label for="regist_dragon">Dragon Resistance</label>
                        <input type="number" id="regist_dragon" name="regist_dragon" value="<?= $weapon['regist_dragon'] ?>" min="-128" max="127">
                    </div>
                    <div class="form-group">
                        <label for="regist_fear">Fear Resistance</label>
                        <input type="number" id="regist_fear" name="regist_fear" value="<?= $weapon['regist_fear'] ?>" min="-128" max="127">
                    </div>
                    <div class="form-group">
                        <label for="regist_all">All Resistance</label>
                        <input type="number" id="regist_all" name="regist_all" value="<?= $weapon['regist_all'] ?>" min="-128" max="127">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 6: Hit Bonuses -->
        <div class="form-tab-content" id="tab-hitups">
            <div class="field-group stat-group-positive">
                <h3>Hit Rate Bonuses</h3>
                <div class="form-grid-3">
                    <div class="form-group">
                        <label for="hitup_skill">Skill Hit Bonus</label>
                        <input type="number" id="hitup_skill" name="hitup_skill" value="<?= $weapon['hitup_skill'] ?>" min="-128" max="127">
                    </div>
                    <div class="form-group">
                        <label for="hitup_spirit">Spirit Hit Bonus</label>
                        <input type="number" id="hitup_spirit" name="hitup_spirit" value="<?= $weapon['hitup_spirit'] ?>" min="-128" max="127">
                    </div>
                    <div class="form-group">
                        <label for="hitup_dragon">Dragon Hit Bonus</label>
                        <input type="number" id="hitup_dragon" name="hitup_dragon" value="<?= $weapon['hitup_dragon'] ?>" min="-128" max="127">
                    </div>
                    <div class="form-group">
                        <label for="hitup_fear">Fear Hit Bonus</label>
                        <input type="number" id="hitup_fear" name="hitup_fear" value="<?= $weapon['hitup_fear'] ?>" min="-128" max="127">
                    </div>
                    <div class="form-group">
                        <label for="hitup_all">All Hit Bonus</label>
                        <input type="number" id="hitup_all" name="hitup_all" value="<?= $weapon['hitup_all'] ?>" min="-128" max="127">
                    </div>
                    <div class="form-group">
                        <label for="hitup_magic">Magic Hit Bonus</label>
                        <input type="number" id="hitup_magic" name="hitup_magic" value="<?= $weapon['hitup_magic'] ?>" min="-128" max="127">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 7: Damage Reduction -->
        <div class="form-tab-content" id="tab-reduction">
            <div class="weapon-advanced-grid">
                <div class="field-group stat-group-neutral">
                    <h3>Basic Damage Reduction</h3>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="damage_reduction">Physical Damage Reduction</label>
                            <input type="number" id="damage_reduction" name="damage_reduction" value="<?= $weapon['damage_reduction'] ?>" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="MagicDamageReduction">Magic Damage Reduction</label>
                            <input type="number" id="MagicDamageReduction" name="MagicDamageReduction" value="<?= $weapon['MagicDamageReduction'] ?>" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="reductionEgnor">Reduction Ignore</label>
                            <input type="number" id="reductionEgnor" name="reductionEgnor" value="<?= $weapon['reductionEgnor'] ?>" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="reductionPercent">Reduction Percentage</label>
                            <input type="number" id="reductionPercent" name="reductionPercent" value="<?= $weapon['reductionPercent'] ?>" min="0" max="100">
                        </div>
                    </div>
                </div>

                <div class="field-group stat-group-special">
                    <h3>Abnormal Status</h3>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="abnormalStatusDamageReduction">Abnormal Status Damage Reduction</label>
                            <input type="number" id="abnormalStatusDamageReduction" name="abnormalStatusDamageReduction" value="<?= $weapon['abnormalStatusDamageReduction'] ?>" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="abnormalStatusPVPDamageReduction">Abnormal Status PVP Damage Reduction</label>
                            <input type="number" id="abnormalStatusPVPDamageReduction" name="abnormalStatusPVPDamageReduction" value="<?= $weapon['abnormalStatusPVPDamageReduction'] ?>" min="-128" max="127">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 8: PVP Settings -->
        <div class="form-tab-content" id="tab-pvp">
            <div class="weapon-advanced-grid">
                <div class="field-group stat-group-negative">
                    <h3>PVP Damage</h3>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="PVPDamage">PVP Damage Bonus</label>
                            <input type="number" id="PVPDamage" name="PVPDamage" value="<?= $weapon['PVPDamage'] ?>" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="PVPDamagePercent">PVP Damage Percentage</label>
                            <input type="number" id="PVPDamagePercent" name="PVPDamagePercent" value="<?= $weapon['PVPDamagePercent'] ?>" min="0" max="100">
                        </div>
                    </div>
                </div>

                <div class="field-group stat-group-neutral">
                    <h3>PVP Damage Reduction</h3>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="PVPDamageReduction">PVP Damage Reduction</label>
                            <input type="number" id="PVPDamageReduction" name="PVPDamageReduction" value="<?= $weapon['PVPDamageReduction'] ?>" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="PVPDamageReductionPercent">PVP Damage Reduction Percentage</label>
                            <input type="number" id="PVPDamageReductionPercent" name="PVPDamageReductionPercent" value="<?= $weapon['PVPDamageReductionPercent'] ?>" min="0" max="100">
                        </div>
                        <div class="form-group">
                            <label for="PVPMagicDamageReduction">PVP Magic Damage Reduction</label>
                            <input type="number" id="PVPMagicDamageReduction" name="PVPMagicDamageReduction" value="<?= $weapon['PVPMagicDamageReduction'] ?>" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="PVPReductionEgnor">PVP Reduction Ignore</label>
                            <input type="number" id="PVPReductionEgnor" name="PVPReductionEgnor" value="<?= $weapon['PVPReductionEgnor'] ?>" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="PVPMagicDamageReductionEgnor">PVP Magic Damage Reduction Ignore</label>
                            <input type="number" id="PVPMagicDamageReductionEgnor" name="PVPMagicDamageReductionEgnor" value="<?= $weapon['PVPMagicDamageReductionEgnor'] ?>" min="-128" max="127">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 9: Special Effects -->
        <div class="form-tab-content" id="tab-special">
            <div class="weapon-advanced-grid">
                <div class="field-group stat-group-positive">
                    <h3>Critical & Experience</h3>
                    <div class="form-grid-3">
                        <div class="form-group">
                            <label for="shortCritical">Short Range Critical</label>
                            <input type="number" id="shortCritical" name="shortCritical" value="<?= $weapon['shortCritical'] ?>" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="longCritical">Long Range Critical</label>
                            <input type="number" id="longCritical" name="longCritical" value="<?= $weapon['longCritical'] ?>" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="magicCritical">Magic Critical</label>
                            <input type="number" id="magicCritical" name="magicCritical" value="<?= $weapon['magicCritical'] ?>" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="expBonus">Experience Bonus</label>
                            <input type="number" id="expBonus" name="expBonus" value="<?= $weapon['expBonus'] ?>" min="-1000" max="1000">
                        </div>
                        <div class="form-group">
                            <label for="rest_exp_reduce_efficiency">Rest EXP Reduce Efficiency</label>
                            <input type="number" id="rest_exp_reduce_efficiency" name="rest_exp_reduce_efficiency" value="<?= $weapon['rest_exp_reduce_efficiency'] ?>" min="-1000" max="1000">
                        </div>
                    </div>
                </div>

                <div class="field-group stat-group-special">
                    <h3>Special Attributes</h3>
                    <div class="form-grid-3">
                        <div class="form-group">
                            <label for="addDg">Add Dragon Slayer</label>
                            <input type="number" id="addDg" name="addDg" value="<?= $weapon['addDg'] ?>" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="addEr">Add Evil Resist</label>
                            <input type="number" id="addEr" name="addEr" value="<?= $weapon['addEr'] ?>" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="addMe">Add Magic Effect</label>
                            <input type="number" id="addMe" name="addMe" value="<?= $weapon['addMe'] ?>" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="poisonRegist">Poison Resistance</label>
                            <div class="toggle-switch">
                                <input type="checkbox" id="poisonRegist" name="poisonRegist" value="true" <?= $weapon['poisonRegist'] == 'true' ? 'checked' : '' ?>>
                                <span class="toggle-slider"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="imunEgnor">Immunity Ignore</label>
                            <input type="number" id="imunEgnor" name="imunEgnor" value="<?= $weapon['imunEgnor'] ?>" min="-1000" max="1000">
                        </div>
                    </div>
                </div>

                <div class="field-group stat-group-neutral">
                    <h3>Stun & Time Effects</h3>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="stunDuration">Stun Duration</label>
                            <input type="number" id="stunDuration" name="stunDuration" value="<?= $weapon['stunDuration'] ?>" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="tripleArrowStun">Triple Arrow Stun</label>
                            <input type="number" id="tripleArrowStun" name="tripleArrowStun" value="<?= $weapon['tripleArrowStun'] ?>" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="strangeTimeIncrease">Strange Time Increase</label>
                            <input type="number" id="strangeTimeIncrease" name="strangeTimeIncrease" value="<?= $weapon['strangeTimeIncrease'] ?>" min="-10000" max="10000">
                        </div>
                        <div class="form-group">
                            <label for="strangeTimeDecrease">Strange Time Decrease</label>
                            <input type="number" id="strangeTimeDecrease" name="strangeTimeDecrease" value="<?= $weapon['strangeTimeDecrease'] ?>" min="-10000" max="10000">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 10: Potion Effects -->
        <div class="form-tab-content" id="tab-potions">
            <div class="weapon-advanced-grid">
                <div class="field-group stat-group-positive">
                    <h3>Potion Enhancement</h3>
                    <div class="form-grid-3">
                        <div class="form-group">
                            <label for="potionRegist">Potion Resistance</label>
                            <input type="number" id="potionRegist" name="potionRegist" value="<?= $weapon['potionRegist'] ?>" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="potionPercent">Potion Percentage</label>
                            <input type="number" id="potionPercent" name="potionPercent" value="<?= $weapon['potionPercent'] ?>" min="0" max="100">
                        </div>
                        <div class="form-group">
                            <label for="potionValue">Potion Value</label>
                            <input type="number" id="potionValue" name="potionValue" value="<?= $weapon['potionValue'] ?>" min="-128" max="127">
                        </div>
                    </div>
                </div>

                <div class="field-group stat-group-positive">
                    <h3>Regeneration Effects</h3>
                    <div class="form-grid-3">
                        <div class="form-group">
                            <label for="hprAbsol32Second">HPR Absolute (32 sec)</label>
                            <input type="number" id="hprAbsol32Second" name="hprAbsol32Second" value="<?= $weapon['hprAbsol32Second'] ?>" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="mprAbsol64Second">MPR Absolute (64 sec)</label>
                            <input type="number" id="mprAbsol64Second" name="mprAbsol64Second" value="<?= $weapon['mprAbsol64Second'] ?>" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="mprAbsol16Second">MPR Absolute (16 sec)</label>
                            <input type="number" id="mprAbsol16Second" name="mprAbsol16Second" value="<?= $weapon['mprAbsol16Second'] ?>" min="-128" max="127">
                        </div>
                    </div>
                </div>

                <div class="field-group stat-group-special">
                    <h3>Potion Enhancement Effects</h3>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="hpPotionDelayDecrease">HP Potion Delay Decrease</label>
                            <input type="number" id="hpPotionDelayDecrease" name="hpPotionDelayDecrease" value="<?= $weapon['hpPotionDelayDecrease'] ?>" min="-10000" max="10000">
                        </div>
                        <div class="form-group">
                            <label for="hpPotionCriticalProb">HP Potion Critical Probability</label>
                            <input type="number" id="hpPotionCriticalProb" name="hpPotionCriticalProb" value="<?= $weapon['hpPotionCriticalProb'] ?>" min="-10000" max="10000">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 11: Advanced Properties -->
        <div class="form-tab-content" id="tab-advanced">
            <div class="weapon-advanced-grid">
                <div class="field-group stat-group-neutral">
                    <h3>Item Properties</h3>
                    <div class="form-grid-3">
                        <div class="form-group">
                            <label for="bless">Blessed</label>
                            <div class="toggle-switch">
                                <input type="checkbox" id="bless" name="bless" value="1" <?= $weapon['bless'] ? 'checked' : '' ?>>
                                <span class="toggle-slider"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="trade">Tradeable</label>
                            <div class="toggle-switch">
                                <input type="checkbox" id="trade" name="trade" value="1" <?= $weapon['trade'] ? 'checked' : '' ?>>
                                <span class="toggle-slider"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="retrieve">Retrievable</label>
                            <div class="toggle-switch">
                                <input type="checkbox" id="retrieve" name="retrieve" value="1" <?= $weapon['retrieve'] ? 'checked' : '' ?>>
                                <span class="toggle-slider"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="specialretrieve">Special Retrieve</label>
                            <div class="toggle-switch">
                                <input type="checkbox" id="specialretrieve" name="specialretrieve" value="1" <?= $weapon['specialretrieve'] ? 'checked' : '' ?>>
                                <span class="toggle-slider"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="cant_delete">Can't Delete</label>
                            <div class="toggle-switch">
                                <input type="checkbox" id="cant_delete" name="cant_delete" value="1" <?= $weapon['cant_delete'] ? 'checked' : '' ?>>
                                <span class="toggle-slider"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="cant_sell">Can't Sell</label>
                            <div class="toggle-switch">
                                <input type="checkbox" id="cant_sell" name="cant_sell" value="1" <?= $weapon['cant_sell'] ? 'checked' : '' ?>>
                                <span class="toggle-slider"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="field-group stat-group-special">
                    <h3>Speed & Skill Effects</h3>
                    <div class="form-grid-3">
                        <div class="form-group">
                            <label for="increaseArmorSkillProb">Increase Armor Skill Probability</label>
                            <input type="number" id="increaseArmorSkillProb" name="increaseArmorSkillProb" value="<?= $weapon['increaseArmorSkillProb'] ?>" min="-10000" max="10000">
                        </div>
                        <div class="form-group">
                            <label for="attackSpeedDelayRate">Attack Speed Delay Rate</label>
                            <input type="number" id="attackSpeedDelayRate" name="attackSpeedDelayRate" value="<?= $weapon['attackSpeedDelayRate'] ?>" min="-1000" max="1000">
                        </div>
                        <div class="form-group">
                            <label for="moveSpeedDelayRate">Move Speed Delay Rate</label>
                            <input type="number" id="moveSpeedDelayRate" name="moveSpeedDelayRate" value="<?= $weapon['moveSpeedDelayRate'] ?>" min="-1000" max="1000">
                        </div>
                    </div>
                </div>

                <div class="field-group stat-group-special">
                    <h3>Magic Properties</h3>
                    <div class="form-group">
                        <label for="Magic_name">Magic Name</label>
                        <input type="text" id="Magic_name" name="Magic_name" value="<?= htmlspecialchars($weapon['Magic_name']) ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="admin-header mt-3">
            <div class="admin-header-actions">
                <button type="submit" class="admin-btn admin-btn-primary admin-btn-large">
                    Save Changes
                </button>
                <a href="weapon_list.php" class="admin-btn admin-btn-secondary admin-btn-large">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>

<style>
/* Enhanced weapon edit form styles */
.preview-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 8px;
}

.preview-info {
    width: 100%;
}

.preview-info .form-group {
    margin-bottom: 0.75rem;
}

.preview-info .form-group:last-child {
    margin-bottom: 0;
}

.weapon-advanced-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2rem;
}

@media (min-width: 1200px) {
    .weapon-advanced-grid {
        grid-template-columns: 1fr 1fr;
    }
}

@media (min-width: 1600px) {
    .weapon-advanced-grid {
        grid-template-columns: 1fr 1fr 1fr;
    }
}

.form-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    align-items: start;
}

.form-grid-3 {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    align-items: start;
}

.form-group input[type="number"] {
    padding: 0.5rem;
    border-radius: 4px;
    font-size: 0.9rem;
}

.form-group input[type="number"]:focus {
    outline: 0;
}
</style>

<script>
// Tab functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.form-tab');
    const tabContents = document.querySelectorAll('.form-tab-content');
    const progressBar = document.querySelector('.form-progress-bar');
    
    tabs.forEach((tab, index) => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs and contents
            tabs.forEach(t => t.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding content
            this.classList.add('active');
            const tabId = this.getAttribute('data-tab');
            document.getElementById('tab-' + tabId).classList.add('active');
            
            // Update progress bar
            const progress = ((index + 1) / tabs.length) * 100;
            progressBar.style.width = progress + '%';
        });
    });
    
    // Image preview update with enhanced functionality
    window.updatePreview = function(iconId) {
        const preview = document.getElementById('weapon-preview');
        
        // Don't update if iconId is empty or invalid
        if (!iconId || iconId.trim() === '') {
            preview.src = '/l1jdb_database/assets/img/placeholders/0.png';
            return;
        }
        
        // Add loading state
        preview.style.opacity = '0.5';
        preview.style.transition = 'opacity 0.3s ease';
        
        // Create new image to test loading
        const testImg = new Image();
        testImg.onload = function() {
            preview.src = `/l1jdb_database/assets/img/icons/${iconId}.png`;
            preview.style.opacity = '1';
        };
        testImg.onerror = function() {
            preview.src = '/l1jdb_database/assets/img/placeholders/0.png';
            preview.style.opacity = '1';
        };
        testImg.src = `/l1jdb_database/assets/img/icons/${iconId}.png`;
    };
    
    // Add debounced input handler for better performance
    let updateTimeout;
    const iconIdInput = document.getElementById('iconId');
    if (iconIdInput) {
        iconIdInput.addEventListener('input', function() {
            clearTimeout(updateTimeout);
            updateTimeout = setTimeout(() => {
                updatePreview(this.value);
            }, 300); // 300ms delay
        });
    }
    
    // Form validation
    const form = document.getElementById('weaponForm');
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('input[required], select[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            const group = field.closest('.form-group');
            if (!field.value.trim()) {
                group.classList.add('has-error');
                isValid = false;
            } else {
                group.classList.remove('has-error');
                group.classList.add('has-success');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });
});
</script>

<?php require_once '../../includes/footer.php'; ?>
