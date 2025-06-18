<?php
require_once dirname(dirname(dirname(__DIR__))) . '/includes/config.php';
require_once dirname(dirname(__DIR__)) . '/includes/header.php';

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
            <li class="breadcrumb-item">Add New Weapon</li>
        </ul>
    </div>

    <div class="admin-header">
        <h1>Add New Weapon</h1>
        <div class="admin-header-actions">
            <a href="weapon_list.php" class="admin-btn admin-btn-secondary">
                <span>←</span> Back to List
            </a>
        </div>
    </div>

    <form action="weapon_process.php" method="POST" class="admin-form" id="weaponForm">
        <input type="hidden" name="action" value="add">

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
                        <img src="/l1jdb_database/assets/img/placeholders/0.png" 
                             alt="Weapon Icon" 
                             class="preview-image"
                             id="weapon-preview">
                        <div class="preview-info">
                            <div class="form-group">
                                <label for="iconId">Icon ID</label>
                                <input type="number" id="iconId" name="iconId" value="0" 
                                       onchange="updatePreview(this.value)" required>
                            </div>
                            <div class="form-group">
                                <label for="spriteId">Sprite ID</label>
                                <input type="number" id="spriteId" name="spriteId" value="0">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Basic Properties Column -->
                <div class="field-group">
                    <h3>Basic Properties</h3>
                    <div class="form-group">
                        <label for="item_name_id">Item Name ID</label>
                        <input type="number" id="item_name_id" name="item_name_id" value="0">
                    </div>
                    <div class="form-group">
                        <label for="desc_en">English Name</label>
                        <input type="text" id="desc_en" name="desc_en" placeholder="Enter weapon name" required>
                    </div>
                    <div class="form-group">
                        <label for="desc_kr">Korean Name</label>
                        <input type="text" id="desc_kr" name="desc_kr" placeholder="Korean name (optional)">
                    </div>
                    <div class="form-group">
                        <label for="desc_powerbook">Powerbook Description</label>
                        <input type="text" id="desc_powerbook" name="desc_powerbook" placeholder="Powerbook description">
                    </div>
                    <div class="form-group">
                        <label for="desc_id">Description ID</label>
                        <input type="text" id="desc_id" name="desc_id" placeholder="Description identifier">
                    </div>
                </div>
            </div>

            <div class="form-grid-3">
                <div class="form-group">
                    <label for="itemGrade">Item Grade</label>
                    <select id="itemGrade" name="itemGrade">
                        <?php foreach (getWeaponGrades() as $grade => $label): ?>
                            <option value="<?= $grade ?>" <?= $grade == 'NORMAL' ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="type">Weapon Type</label>
                    <select id="type" name="type" required>
                        <?php foreach (getWeaponTypes() as $type => $label): ?>
                            <option value="<?= $type ?>" <?= $type == 'SWORD' ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="material">Material</label>
                    <select id="material" name="material">
                        <?php foreach (getWeaponMaterials() as $material => $label): ?>
                            <option value="<?= $material ?>" <?= $material == 'IRON(철)' ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="note">Notes</label>
                <textarea id="note" name="note" rows="4" placeholder="Additional notes about this weapon"></textarea>
            </div>
        </div>

        <!-- Tab 2: Combat Statistics -->
        <div class="form-tab-content" id="tab-combat">
            <div class="weapon-advanced-grid">
                <div class="field-group stat-group-neutral">
                    <h3>Damage Statistics</h3>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="dmg_small">Small Damage</label>
                            <input type="number" id="dmg_small" name="dmg_small" value="1" required min="0">
                        </div>
                        <div class="form-group">
                            <label for="dmg_large">Large Damage</label>
                            <input type="number" id="dmg_large" name="dmg_large" value="1" required min="0">
                        </div>
                    </div>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="hitmodifier">Hit Modifier</label>
                            <input type="number" id="hitmodifier" name="hitmodifier" value="0">
                        </div>
                        <div class="form-group">
                            <label for="dmgmodifier">Damage Modifier</label>
                            <input type="number" id="dmgmodifier" name="dmgmodifier" value="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="magicdmgmodifier">Magic Damage Modifier</label>
                        <input type="number" id="magicdmgmodifier" name="magicdmgmodifier" value="0">
                    </div>
                </div>

                <div class="field-group stat-group-special">
                    <h3>Enchantment & Durability</h3>
                    <div class="form-group">
                        <label for="safenchant">Safe Enchant Level</label>
                        <input type="number" id="safenchant" name="safenchant" value="0" min="0" max="15">
                    </div>
                    <div class="form-group">
                        <label for="weight">Weight</label>
                        <input type="number" id="weight" name="weight" value="1" required min="0">
                    </div>
                    <div class="form-group">
                        <label for="canbedmg">Can Be Damaged</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="canbedmg" name="canbedmg" value="1">
                            <span class="toggle-slider"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="max_use_time">Max Use Time</label>
                        <input type="number" id="max_use_time" name="max_use_time" value="0">
                    </div>
                </div>

                <div class="field-group stat-group-positive">
                    <h3>Special Combat Effects</h3>
                    <div class="form-group">
                        <label for="haste_item">Haste Effect</label>
                        <input type="number" id="haste_item" name="haste_item" value="0" min="0" max="2">
                    </div>
                    <div class="form-group">
                        <label for="double_dmg_chance">Double Damage Chance (%)</label>
                        <input type="number" id="double_dmg_chance" name="double_dmg_chance" value="0" min="0" max="100">
                    </div>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="min_lvl">Minimum Level</label>
                            <input type="number" id="min_lvl" name="min_lvl" value="0" min="0">
                        </div>
                        <div class="form-group">
                            <label for="max_lvl">Maximum Level</label>
                            <input type="number" id="max_lvl" name="max_lvl" value="0" min="0">
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
                    <div class="form-grid-3">
                        <div class="form-group">
                            <label for="add_str">Strength (STR)</label>
                            <input type="number" id="add_str" name="add_str" value="0">
                        </div>
                        <div class="form-group">
                            <label for="add_con">Constitution (CON)</label>
                            <input type="number" id="add_con" name="add_con" value="0">
                        </div>
                        <div class="form-group">
                            <label for="add_dex">Dexterity (DEX)</label>
                            <input type="number" id="add_dex" name="add_dex" value="0">
                        </div>
                        <div class="form-group">
                            <label for="add_int">Intelligence (INT)</label>
                            <input type="number" id="add_int" name="add_int" value="0">
                        </div>
                        <div class="form-group">
                            <label for="add_wis">Wisdom (WIS)</label>
                            <input type="number" id="add_wis" name="add_wis" value="0">
                        </div>
                        <div class="form-group">
                            <label for="add_cha">Charisma (CHA)</label>
                            <input type="number" id="add_cha" name="add_cha" value="0">
                        </div>
                    </div>
                </div>

                <div class="field-group stat-group-positive">
                    <h3>Health & Magic</h3>
                    <div class="form-grid-3">
                        <div class="form-group">
                            <label for="add_hp">Hit Points (HP)</label>
                            <input type="number" id="add_hp" name="add_hp" value="0">
                        </div>
                        <div class="form-group">
                            <label for="add_mp">Magic Points (MP)</label>
                            <input type="number" id="add_mp" name="add_mp" value="0">
                        </div>
                        <div class="form-group">
                            <label for="add_sp">Spell Points (SP)</label>
                            <input type="number" id="add_sp" name="add_sp" value="0">
                        </div>
                        <div class="form-group">
                            <label for="add_hpr">HP Regeneration</label>
                            <input type="number" id="add_hpr" name="add_hpr" value="0">
                        </div>
                        <div class="form-group">
                            <label for="add_mpr">MP Regeneration</label>
                            <input type="number" id="add_mpr" name="add_mpr" value="0">
                        </div>
                        <div class="form-group">
                            <label for="m_def">Magic Defense</label>
                            <input type="number" id="m_def" name="m_def" value="0">
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
                            <input type="checkbox" id="use_royal" name="use_royal" value="1" checked>
                            <span class="toggle-slider"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="use_knight">Knight</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="use_knight" name="use_knight" value="1" checked>
                            <span class="toggle-slider"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="use_mage">Mage</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="use_mage" name="use_mage" value="1" checked>
                            <span class="toggle-slider"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="use_elf">Elf</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="use_elf" name="use_elf" value="1" checked>
                            <span class="toggle-slider"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="use_darkelf">Dark Elf</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="use_darkelf" name="use_darkelf" value="1" checked>
                            <span class="toggle-slider"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="use_dragonknight">Dragon Knight</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="use_dragonknight" name="use_dragonknight" value="1" checked>
                            <span class="toggle-slider"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="use_illusionist">Illusionist</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="use_illusionist" name="use_illusionist" value="1" checked>
                            <span class="toggle-slider"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="use_warrior">Warrior</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="use_warrior" name="use_warrior" value="1" checked>
                            <span class="toggle-slider"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="use_fencer">Fencer</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="use_fencer" name="use_fencer" value="1" checked>
                            <span class="toggle-slider"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="use_lancer">Lancer</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="use_lancer" name="use_lancer" value="1" checked>
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
                        <input type="number" id="regist_skill" name="regist_skill" value="0" min="-128" max="127">
                    </div>
                    <div class="form-group">
                        <label for="regist_spirit">Spirit Resistance</label>
                        <input type="number" id="regist_spirit" name="regist_spirit" value="0" min="-128" max="127">
                    </div>
                    <div class="form-group">
                        <label for="regist_dragon">Dragon Resistance</label>
                        <input type="number" id="regist_dragon" name="regist_dragon" value="0" min="-128" max="127">
                    </div>
                    <div class="form-group">
                        <label for="regist_fear">Fear Resistance</label>
                        <input type="number" id="regist_fear" name="regist_fear" value="0" min="-128" max="127">
                    </div>
                    <div class="form-group">
                        <label for="regist_all">All Resistance</label>
                        <input type="number" id="regist_all" name="regist_all" value="0" min="-128" max="127">
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
                        <input type="number" id="hitup_skill" name="hitup_skill" value="0" min="-128" max="127">
                    </div>
                    <div class="form-group">
                        <label for="hitup_spirit">Spirit Hit Bonus</label>
                        <input type="number" id="hitup_spirit" name="hitup_spirit" value="0" min="-128" max="127">
                    </div>
                    <div class="form-group">
                        <label for="hitup_dragon">Dragon Hit Bonus</label>
                        <input type="number" id="hitup_dragon" name="hitup_dragon" value="0" min="-128" max="127">
                    </div>
                    <div class="form-group">
                        <label for="hitup_fear">Fear Hit Bonus</label>
                        <input type="number" id="hitup_fear" name="hitup_fear" value="0" min="-128" max="127">
                    </div>
                    <div class="form-group">
                        <label for="hitup_all">All Hit Bonus</label>
                        <input type="number" id="hitup_all" name="hitup_all" value="0" min="-128" max="127">
                    </div>
                    <div class="form-group">
                        <label for="hitup_magic">Magic Hit Bonus</label>
                        <input type="number" id="hitup_magic" name="hitup_magic" value="0" min="-128" max="127">
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
                            <input type="number" id="damage_reduction" name="damage_reduction" value="0" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="MagicDamageReduction">Magic Damage Reduction</label>
                            <input type="number" id="MagicDamageReduction" name="MagicDamageReduction" value="0" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="reductionEgnor">Reduction Ignore</label>
                            <input type="number" id="reductionEgnor" name="reductionEgnor" value="0" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="reductionPercent">Reduction Percentage</label>
                            <input type="number" id="reductionPercent" name="reductionPercent" value="0" min="0" max="100">
                        </div>
                    </div>
                </div>

                <div class="field-group stat-group-special">
                    <h3>Abnormal Status</h3>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="abnormalStatusDamageReduction">Abnormal Status Damage Reduction</label>
                            <input type="number" id="abnormalStatusDamageReduction" name="abnormalStatusDamageReduction" value="0" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="abnormalStatusPVPDamageReduction">Abnormal Status PVP Damage Reduction</label>
                            <input type="number" id="abnormalStatusPVPDamageReduction" name="abnormalStatusPVPDamageReduction" value="0" min="-128" max="127">
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
                            <input type="number" id="PVPDamage" name="PVPDamage" value="0" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="PVPDamagePercent">PVP Damage Percentage</label>
                            <input type="number" id="PVPDamagePercent" name="PVPDamagePercent" value="0" min="0" max="100">
                        </div>
                    </div>
                </div>

                <div class="field-group stat-group-neutral">
                    <h3>PVP Damage Reduction</h3>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="PVPDamageReduction">PVP Damage Reduction</label>
                            <input type="number" id="PVPDamageReduction" name="PVPDamageReduction" value="0" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="PVPDamageReductionPercent">PVP Damage Reduction Percentage</label>
                            <input type="number" id="PVPDamageReductionPercent" name="PVPDamageReductionPercent" value="0" min="0" max="100">
                        </div>
                        <div class="form-group">
                            <label for="PVPMagicDamageReduction">PVP Magic Damage Reduction</label>
                            <input type="number" id="PVPMagicDamageReduction" name="PVPMagicDamageReduction" value="0" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="PVPReductionEgnor">PVP Reduction Ignore</label>
                            <input type="number" id="PVPReductionEgnor" name="PVPReductionEgnor" value="0" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="PVPMagicDamageReductionEgnor">PVP Magic Damage Reduction Ignore</label>
                            <input type="number" id="PVPMagicDamageReductionEgnor" name="PVPMagicDamageReductionEgnor" value="0" min="-128" max="127">
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
                            <input type="number" id="shortCritical" name="shortCritical" value="0" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="longCritical">Long Range Critical</label>
                            <input type="number" id="longCritical" name="longCritical" value="0" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="magicCritical">Magic Critical</label>
                            <input type="number" id="magicCritical" name="magicCritical" value="0" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="expBonus">Experience Bonus</label>
                            <input type="number" id="expBonus" name="expBonus" value="0" min="-1000" max="1000">
                        </div>
                        <div class="form-group">
                            <label for="rest_exp_reduce_efficiency">Rest EXP Reduce Efficiency</label>
                            <input type="number" id="rest_exp_reduce_efficiency" name="rest_exp_reduce_efficiency" value="0" min="-1000" max="1000">
                        </div>
                    </div>
                </div>

                <div class="field-group stat-group-special">
                    <h3>Special Attributes</h3>
                    <div class="form-grid-3">
                        <div class="form-group">
                            <label for="addDg">Add Dragon Slayer</label>
                            <input type="number" id="addDg" name="addDg" value="0" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="addEr">Add Evil Resist</label>
                            <input type="number" id="addEr" name="addEr" value="0" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="addMe">Add Magic Effect</label>
                            <input type="number" id="addMe" name="addMe" value="0" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="poisonRegist">Poison Resistance</label>
                            <div class="toggle-switch">
                                <input type="checkbox" id="poisonRegist" name="poisonRegist" value="true">
                                <span class="toggle-slider"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="imunEgnor">Immunity Ignore</label>
                            <input type="number" id="imunEgnor" name="imunEgnor" value="0" min="-1000" max="1000">
                        </div>
                    </div>
                </div>

                <div class="field-group stat-group-neutral">
                    <h3>Stun & Time Effects</h3>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="stunDuration">Stun Duration</label>
                            <input type="number" id="stunDuration" name="stunDuration" value="0" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="tripleArrowStun">Triple Arrow Stun</label>
                            <input type="number" id="tripleArrowStun" name="tripleArrowStun" value="0" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="strangeTimeIncrease">Strange Time Increase</label>
                            <input type="number" id="strangeTimeIncrease" name="strangeTimeIncrease" value="0" min="-10000" max="10000">
                        </div>
                        <div class="form-group">
                            <label for="strangeTimeDecrease">Strange Time Decrease</label>
                            <input type="number" id="strangeTimeDecrease" name="strangeTimeDecrease" value="0" min="-10000" max="10000">
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
                            <input type="number" id="potionRegist" name="potionRegist" value="0" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="potionPercent">Potion Percentage</label>
                            <input type="number" id="potionPercent" name="potionPercent" value="0" min="0" max="100">
                        </div>
                        <div class="form-group">
                            <label for="potionValue">Potion Value</label>
                            <input type="number" id="potionValue" name="potionValue" value="0" min="-128" max="127">
                        </div>
                    </div>
                </div>

                <div class="field-group stat-group-positive">
                    <h3>Regeneration Effects</h3>
                    <div class="form-grid-3">
                        <div class="form-group">
                            <label for="hprAbsol32Second">HPR Absolute (32 sec)</label>
                            <input type="number" id="hprAbsol32Second" name="hprAbsol32Second" value="0" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="mprAbsol64Second">MPR Absolute (64 sec)</label>
                            <input type="number" id="mprAbsol64Second" name="mprAbsol64Second" value="0" min="-128" max="127">
                        </div>
                        <div class="form-group">
                            <label for="mprAbsol16Second">MPR Absolute (16 sec)</label>
                            <input type="number" id="mprAbsol16Second" name="mprAbsol16Second" value="0" min="-128" max="127">
                        </div>
                    </div>
                </div>

                <div class="field-group stat-group-special">
                    <h3>Potion Enhancement Effects</h3>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="hpPotionDelayDecrease">HP Potion Delay Decrease</label>
                            <input type="number" id="hpPotionDelayDecrease" name="hpPotionDelayDecrease" value="0" min="-10000" max="10000">
                        </div>
                        <div class="form-group">
                            <label for="hpPotionCriticalProb">HP Potion Critical Probability</label>
                            <input type="number" id="hpPotionCriticalProb" name="hpPotionCriticalProb" value="0" min="-10000" max="10000">
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
                                <input type="checkbox" id="bless" name="bless" value="1" checked>
                                <span class="toggle-slider"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="trade">Tradeable</label>
                            <div class="toggle-switch">
                                <input type="checkbox" id="trade" name="trade" value="1">
                                <span class="toggle-slider"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="retrieve">Retrievable</label>
                            <div class="toggle-switch">
                                <input type="checkbox" id="retrieve" name="retrieve" value="1">
                                <span class="toggle-slider"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="specialretrieve">Special Retrieve</label>
                            <div class="toggle-switch">
                                <input type="checkbox" id="specialretrieve" name="specialretrieve" value="1">
                                <span class="toggle-slider"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="cant_delete">Can't Delete</label>
                            <div class="toggle-switch">
                                <input type="checkbox" id="cant_delete" name="cant_delete" value="1">
                                <span class="toggle-slider"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="cant_sell">Can't Sell</label>
                            <div class="toggle-switch">
                                <input type="checkbox" id="cant_sell" name="cant_sell" value="1">
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
                            <input type="number" id="increaseArmorSkillProb" name="increaseArmorSkillProb" value="0" min="-10000" max="10000">
                        </div>
                        <div class="form-group">
                            <label for="attackSpeedDelayRate">Attack Speed Delay Rate</label>
                            <input type="number" id="attackSpeedDelayRate" name="attackSpeedDelayRate" value="0" min="-1000" max="1000">
                        </div>
                        <div class="form-group">
                            <label for="moveSpeedDelayRate">Move Speed Delay Rate</label>
                            <input type="number" id="moveSpeedDelayRate" name="moveSpeedDelayRate" value="0" min="-1000" max="1000">
                        </div>
                    </div>
                </div>

                <div class="field-group stat-group-special">
                    <h3>Magic Properties</h3>
                    <div class="form-group">
                        <label for="Magic_name">Magic Name</label>
                        <input type="text" id="Magic_name" name="Magic_name" placeholder="Magic spell name">
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="admin-header mt-3">
            <div class="admin-header-actions">
                <button type="submit" class="admin-btn admin-btn-primary admin-btn-large">
                    Create Weapon
                </button>
                <a href="weapon_list.php" class="admin-btn admin-btn-secondary admin-btn-large">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>

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
    
    // Image preview update
    window.updatePreview = function(iconId) {
        const preview = document.getElementById('weapon-preview');
        preview.src = `/l1jdb_database/assets/img/icons/${iconId}.png`;
        preview.onerror = function() {
            this.src = '/l1jdb_database/assets/img/placeholders/0.png';
        };
    };
    
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
