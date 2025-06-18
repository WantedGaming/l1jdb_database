<?php
require_once dirname(dirname(__DIR__)) . '/includes/config.php';
require_once dirname(dirname(__DIR__)) . '/includes/header.php';

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

// Call page header with weapon name
$weaponName = cleanDescriptionPrefix($weapon['desc_en']);
getPageHeader("Edit " . $weaponName);

// Render hero section
renderHero('weapons', "Edit " . $weaponName, 'Update weapon information');
?>

<main>
    <div class="main">
        <div class="container">
            <!-- Back Navigation -->
            <div class="back-nav">
                <a href="../weapons/weapon_detail.php?id=<?= $weaponId ?>" class="back-btn">&larr; Back to Weapon Details</a>
            </div>
            
            <form action="process.php" method="POST" class="weapon-form">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="item_id" value="<?= $weaponId ?>">
                
                <!-- Main Content Row -->
                <div class="weapon-detail-row">
                    <!-- Column 1: Image Preview -->
                    <div class="weapon-image-col">
                        <div class="weapon-image-container">
                            <img src="<?= SITE_URL ?>/assets/img/icons/<?= $weapon['iconId'] ?>.png" 
                                 alt="<?= htmlspecialchars($weaponName) ?>" 
                                 onerror="this.src='<?= SITE_URL ?>/assets/img/placeholders/0.png'"
                                 class="weapon-main-image"
                                 id="weapon-preview">
                            <div class="form-group">
                                <label for="iconId">Icon ID:</label>
                                <input type="number" id="iconId" name="iconId" value="<?= $weapon['iconId'] ?>" required
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
                                    <input type="text" id="desc_en" name="desc_en" value="<?= htmlspecialchars($weapon['desc_en']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="desc_kr">Korean Name:</label>
                                    <input type="text" id="desc_kr" name="desc_kr" value="<?= htmlspecialchars($weapon['desc_kr']) ?>">
                                </div>
                                <div class="form-group">
                                    <label for="type">Weapon Type:</label>
                                    <select id="type" name="type" required>
                                        <?php foreach (getWeaponTypes() as $type => $label): ?>
                                            <option value="<?= $type ?>" <?= $weapon['type'] == $type ? 'selected' : '' ?>><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="material">Material:</label>
                                    <select id="material" name="material">
                                        <?php foreach (getWeaponMaterials() as $material => $label): ?>
                                            <option value="<?= $material ?>" <?= $weapon['material'] == $material ? 'selected' : '' ?>><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="itemGrade">Item Grade:</label>
                                    <select id="itemGrade" name="itemGrade">
                                        <?php foreach (getWeaponGrades() as $grade => $label): ?>
                                            <option value="<?= $grade ?>" <?= $weapon['itemGrade'] == $grade ? 'selected' : '' ?>><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="weight">Weight:</label>
                                    <input type="number" id="weight" name="weight" value="<?= $weapon['weight'] ?>" required min="0">
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
                                    <input type="number" id="dmg_small" name="dmg_small" value="<?= $weapon['dmg_small'] ?>" required min="0">
                                </div>
                                <div class="form-group">
                                    <label for="dmg_large">Large Target Damage:</label>
                                    <input type="number" id="dmg_large" name="dmg_large" value="<?= $weapon['dmg_large'] ?>" required min="0">
                                </div>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <h3>Hit Modifiers</h3>
                            <div class="stat-values">
                                <div class="form-group">
                                    <label for="hitmodifier">Hit Bonus:</label>
                                    <input type="number" id="hitmodifier" name="hitmodifier" value="<?= $weapon['hitmodifier'] ?>">
                                </div>
                                <div class="form-group">
                                    <label for="dmgmodifier">Damage Modifier:</label>
                                    <input type="number" id="dmgmodifier" name="dmgmodifier" value="<?= $weapon['dmgmodifier'] ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <h3>Enchantment</h3>
                            <div class="stat-values">
                                <div class="form-group">
                                    <label for="safenchant">Safe Enchant Level:</label>
                                    <input type="number" id="safenchant" name="safenchant" value="<?= $weapon['safenchant'] ?>" min="0" max="15">
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
                            <input type="number" id="add_str" name="add_str" value="<?= $weapon['add_str'] ?>">
                        </div>
                        <div class="form-group">
                            <label for="add_con">CON Bonus:</label>
                            <input type="number" id="add_con" name="add_con" value="<?= $weapon['add_con'] ?>">
                        </div>
                        <div class="form-group">
                            <label for="add_dex">DEX Bonus:</label>
                            <input type="number" id="add_dex" name="add_dex" value="<?= $weapon['add_dex'] ?>">
                        </div>
                        <div class="form-group">
                            <label for="add_int">INT Bonus:</label>
                            <input type="number" id="add_int" name="add_int" value="<?= $weapon['add_int'] ?>">
                        </div>
                        <div class="form-group">
                            <label for="add_wis">WIS Bonus:</label>
                            <input type="number" id="add_wis" name="add_wis" value="<?= $weapon['add_wis'] ?>">
                        </div>
                        <div class="form-group">
                            <label for="add_cha">CHA Bonus:</label>
                            <input type="number" id="add_cha" name="add_cha" value="<?= $weapon['add_cha'] ?>">
                        </div>
                        <div class="form-group">
                            <label for="add_hp">HP Bonus:</label>
                            <input type="number" id="add_hp" name="add_hp" value="<?= $weapon['add_hp'] ?>">
                        </div>
                        <div class="form-group">
                            <label for="add_mp">MP Bonus:</label>
                            <input type="number" id="add_mp" name="add_mp" value="<?= $weapon['add_mp'] ?>">
                        </div>
                        <div class="form-group">
                            <label for="add_sp">SP Bonus:</label>
                            <input type="number" id="add_sp" name="add_sp" value="<?= $weapon['add_sp'] ?>">
                        </div>
                    </div>
                </div>
                
                <!-- Class Restrictions -->
                <div class="weapon-section">
                    <h2>Class Usage</h2>
                    <div class="class-grid">
                        <div class="form-group">
                            <label for="use_royal">
                                <input type="checkbox" id="use_royal" name="use_royal" value="1" <?= $weapon['use_royal'] ? 'checked' : '' ?>>
                                Royal
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="use_knight">
                                <input type="checkbox" id="use_knight" name="use_knight" value="1" <?= $weapon['use_knight'] ? 'checked' : '' ?>>
                                Knight
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="use_mage">
                                <input type="checkbox" id="use_mage" name="use_mage" value="1" <?= $weapon['use_mage'] ? 'checked' : '' ?>>
                                Mage
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="use_elf">
                                <input type="checkbox" id="use_elf" name="use_elf" value="1" <?= $weapon['use_elf'] ? 'checked' : '' ?>>
                                Elf
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="use_darkelf">
                                <input type="checkbox" id="use_darkelf" name="use_darkelf" value="1" <?= $weapon['use_darkelf'] ? 'checked' : '' ?>>
                                Dark Elf
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="use_dragonknight">
                                <input type="checkbox" id="use_dragonknight" name="use_dragonknight" value="1" <?= $weapon['use_dragonknight'] ? 'checked' : '' ?>>
                                Dragon Knight
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="use_illusionist">
                                <input type="checkbox" id="use_illusionist" name="use_illusionist" value="1" <?= $weapon['use_illusionist'] ? 'checked' : '' ?>>
                                Illusionist
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="use_warrior">
                                <input type="checkbox" id="use_warrior" name="use_warrior" value="1" <?= $weapon['use_warrior'] ? 'checked' : '' ?>>
                                Warrior
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="use_fencer">
                                <input type="checkbox" id="use_fencer" name="use_fencer" value="1" <?= $weapon['use_fencer'] ? 'checked' : '' ?>>
                                Fencer
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="use_lancer">
                                <input type="checkbox" id="use_lancer" name="use_lancer" value="1" <?= $weapon['use_lancer'] ? 'checked' : '' ?>>
                                Lancer
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="../weapons/weapon_detail.php?id=<?= $weaponId ?>" class="btn btn-secondary">Cancel</a>
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
