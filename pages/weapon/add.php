<?php
require_once dirname(dirname(__DIR__)) . '/includes/config.php';
require_once dirname(dirname(__DIR__)) . '/includes/header.php';

// Call page header with title
getPageHeader('Add New Weapon');

// Render hero section
renderHero('weapons', 'Add New Weapon', 'Contribute a new weapon to the database');

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

<main>
    <div class="main">
        <div class="container">
            <!-- Back Navigation -->
            <div class="back-nav">
                <a href="../weapons/weapon_list.php" class="back-btn">&larr; Back to Weapons List</a>
            </div>
            
            <form action="process.php" method="POST" class="weapon-form">
                <input type="hidden" name="action" value="add">
                
                <!-- Main Content Row -->
                <div class="weapon-detail-row">
                    <!-- Column 1: Image Preview -->
                    <div class="weapon-image-col">
                        <div class="weapon-image-container">
                            <img src="<?= SITE_URL ?>/assets/img/placeholders/0.png" 
                                 alt="Weapon Preview" 
                                 id="weapon-preview"
                                 class="weapon-main-image">
                            <div class="form-group">
                                <label for="iconId">Icon ID:</label>
                                <input type="number" id="iconId" name="iconId" value="0" required 
                                       onchange="updatePreview(this.value)">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Column 2: Basic Information -->
                    <div class="weapon-info-col">
                        <div class="weapon-basic-info">
                            <h2>Basic Information</h2>
                            <div class="info-grid">
                                <div class="form-group">
                                    <label for="desc_en">Weapon Name:</label>
                                    <input type="text" id="desc_en" name="desc_en" placeholder="Enter weapon name" required>
                                </div>
                                <div class="form-group">
                                    <label for="desc_kr">Korean Name (Optional):</label>
                                    <input type="text" id="desc_kr" name="desc_kr" placeholder="Korean name">
                                </div>
                                <div class="form-group">
                                    <label for="type">Weapon Type:</label>
                                    <select id="type" name="type" required>
                                        <?php foreach (getWeaponTypes() as $type => $label): ?>
                                            <option value="<?= $type ?>" <?= $type == 'SWORD' ? 'selected' : '' ?>><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="material">Material:</label>
                                    <select id="material" name="material">
                                        <?php foreach (getWeaponMaterials() as $material => $label): ?>
                                            <option value="<?= $material ?>" <?= $material == 'IRON(철)' ? 'selected' : '' ?>><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="itemGrade">Item Grade:</label>
                                    <select id="itemGrade" name="itemGrade">
                                        <?php foreach (getWeaponGrades() as $grade => $label): ?>
                                            <option value="<?= $grade ?>" <?= $grade == 'NORMAL' ? 'selected' : '' ?>><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="weight">Weight:</label>
                                    <input type="number" id="weight" name="weight" value="1" required min="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Combat Stats Section -->
                <div class="weapon-section">
                    <h2>Combat Statistics</h2>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <h3>Damage</h3>
                            <div class="stat-values">
                                <div class="form-group">
                                    <label for="dmg_small">Small Target Damage:</label>
                                    <input type="number" id="dmg_small" name="dmg_small" value="1" required min="0">
                                </div>
                                <div class="form-group">
                                    <label for="dmg_large">Large Target Damage:</label>
                                    <input type="number" id="dmg_large" name="dmg_large" value="1" required min="0">
                                </div>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <h3>Hit Modifiers</h3>
                            <div class="stat-values">
                                <div class="form-group">
                                    <label for="hitmodifier">Hit Bonus:</label>
                                    <input type="number" id="hitmodifier" name="hitmodifier" value="0">
                                </div>
                                <div class="form-group">
                                    <label for="dmgmodifier">Damage Modifier:</label>
                                    <input type="number" id="dmgmodifier" name="dmgmodifier" value="0">
                                </div>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <h3>Enchantment</h3>
                            <div class="stat-values">
                                <div class="form-group">
                                    <label for="safenchant">Safe Enchant Level:</label>
                                    <input type="number" id="safenchant" name="safenchant" value="0" min="0" max="15">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Stat Bonuses Section -->
                <div class="weapon-section">
                    <h2>Stat Bonuses</h2>
                    <div class="bonus-grid">
                        <div class="form-group">
                            <label for="add_str">STR Bonus:</label>
                            <input type="number" id="add_str" name="add_str" value="0">
                        </div>
                        <div class="form-group">
                            <label for="add_con">CON Bonus:</label>
                            <input type="number" id="add_con" name="add_con" value="0">
                        </div>
                        <div class="form-group">
                            <label for="add_dex">DEX Bonus:</label>
                            <input type="number" id="add_dex" name="add_dex" value="0">
                        </div>
                        <div class="form-group">
                            <label for="add_int">INT Bonus:</label>
                            <input type="number" id="add_int" name="add_int" value="0">
                        </div>
                        <div class="form-group">
                            <label for="add_wis">WIS Bonus:</label>
                            <input type="number" id="add_wis" name="add_wis" value="0">
                        </div>
                        <div class="form-group">
                            <label for="add_cha">CHA Bonus:</label>
                            <input type="number" id="add_cha" name="add_cha" value="0">
                        </div>
                        <div class="form-group">
                            <label for="add_hp">HP Bonus:</label>
                            <input type="number" id="add_hp" name="add_hp" value="0">
                        </div>
                        <div class="form-group">
                            <label for="add_mp">MP Bonus:</label>
                            <input type="number" id="add_mp" name="add_mp" value="0">
                        </div>
                        <div class="form-group">
                            <label for="add_sp">SP Bonus:</label>
                            <input type="number" id="add_sp" name="add_sp" value="0">
                        </div>
                    </div>
                </div>
                
                <!-- Class Restrictions -->
                <div class="weapon-section">
                    <h2>Class Usage</h2>
                    <div class="class-grid">
                        <div class="form-group">
                            <label for="use_royal">
                                <input type="checkbox" id="use_royal" name="use_royal" value="1" checked>
                                Royal
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="use_knight">
                                <input type="checkbox" id="use_knight" name="use_knight" value="1" checked>
                                Knight
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="use_mage">
                                <input type="checkbox" id="use_mage" name="use_mage" value="1" checked>
                                Mage
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="use_elf">
                                <input type="checkbox" id="use_elf" name="use_elf" value="1" checked>
                                Elf
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="use_darkelf">
                                <input type="checkbox" id="use_darkelf" name="use_darkelf" value="1" checked>
                                Dark Elf
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="use_dragonknight">
                                <input type="checkbox" id="use_dragonknight" name="use_dragonknight" value="1" checked>
                                Dragon Knight
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="use_illusionist">
                                <input type="checkbox" id="use_illusionist" name="use_illusionist" value="1" checked>
                                Illusionist
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="use_warrior">
                                <input type="checkbox" id="use_warrior" name="use_warrior" value="1" checked>
                                Warrior
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="use_fencer">
                                <input type="checkbox" id="use_fencer" name="use_fencer" value="1" checked>
                                Fencer
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="use_lancer">
                                <input type="checkbox" id="use_lancer" name="use_lancer" value="1" checked>
                                Lancer
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Add Weapon</button>
                    <a href="../weapons/weapon_list.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
function updatePreview(iconId) {
    const preview = document.getElementById('weapon-preview');
    preview.src = `<?= SITE_URL ?>/assets/img/icons/${iconId}.png`;
    preview.onerror = function() {
        this.src = '<?= SITE_URL ?>/assets/img/placeholders/0.png';
    };
}
</script>

<?php getPageFooter(); ?>
