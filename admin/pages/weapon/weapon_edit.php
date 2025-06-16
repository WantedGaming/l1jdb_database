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
getPageHeader("Edit " . $weaponName);

// Create weapon type and material for hero
$heroText = normalizeWeaponType($weapon['type']) . ' - ' . normalizeWeaponMaterial($weapon['material']);

// Render hero section
renderHero('weapons', "Edit " . $weaponName, $heroText);
?>

<main>
    <div class="main">
        <div class="container">
            <!-- Back Navigation -->
            <div class="back-nav">
                <a href="../weapons/weapon_detail.php?id=<?= $weaponId ?>" class="back-btn">&larr; Back to Weapon Details</a>
            </div>
            
            <form action="weapon_process.php" method="POST" class="weapon-form">
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
                                 class="weapon-main-image">
                            <div class="form-group">
                                <label for="iconId">Icon ID:</label>
                                <input type="number" id="iconId" name="iconId" value="<?= $weapon['iconId'] ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Column 2: Basic Information -->
                    <div class="weapon-info-col">
                        <div class="weapon-basic-info">
                            <h2>Basic Information</h2>
                            <div class="info-grid">
                                <div class="form-group">
                                    <label for="desc_en">Name:</label>
                                    <input type="text" id="desc_en" name="desc_en" value="<?= htmlspecialchars($weapon['desc_en']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="type">Type:</label>
                                    <select id="type" name="type" required>
                                        <?php foreach (getWeaponTypes() as $type => $label): ?>
                                            <option value="<?= $type ?>" <?= $weapon['type'] == $type ? 'selected' : '' ?>><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="material">Material:</label>
                                    <select id="material" name="material" required>
                                        <?php foreach (getWeaponMaterials() as $material => $label): ?>
                                            <option value="<?= $material ?>" <?= $weapon['material'] == $material ? 'selected' : '' ?>><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="weight">Weight:</label>
                                    <input type="number" id="weight" name="weight" value="<?= $weapon['weight'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="itemGrade">Grade:</label>
                                    <select id="itemGrade" name="itemGrade">
                                        <option value="0" <?= $weapon['itemGrade'] == 0 ? 'selected' : '' ?>>None</option>
                                        <option value="1" <?= $weapon['itemGrade'] == 1 ? 'selected' : '' ?>>D</option>
                                        <option value="2" <?= $weapon['itemGrade'] == 2 ? 'selected' : '' ?>>C</option>
                                        <option value="3" <?= $weapon['itemGrade'] == 3 ? 'selected' : '' ?>>B</option>
                                        <option value="4" <?= $weapon['itemGrade'] == 4 ? 'selected' : '' ?>>A</option>
                                        <option value="5" <?= $weapon['itemGrade'] == 5 ? 'selected' : '' ?>>S</option>
                                    </select>
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
                                <div class="form-group">
                                    <label for="dmg_small">Small:</label>
                                    <input type="number" id="dmg_small" name="dmg_small" value="<?= $weapon['dmg_small'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="dmg_large">Large:</label>
                                    <input type="number" id="dmg_large" name="dmg_large" value="<?= $weapon['dmg_large'] ?>" required>
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
                                    <label for="safenchant">Safe Enchant:</label>
                                    <input type="number" id="safenchant" name="safenchant" value="<?= $weapon['safenchant'] ?>">
                                </div>
                                <div class="form-group">
                                    <label for="canbedmg">Can be damaged:</label>
                                    <select id="canbedmg" name="canbedmg">
                                        <option value="1" <?= $weapon['canbedmg'] ? 'selected' : '' ?>>Yes</option>
                                        <option value="0" <?= !$weapon['canbedmg'] ? 'selected' : '' ?>>No</option>
                                    </select>
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
                            <label for="add_str">STR:</label>
                            <input type="number" id="add_str" name="add_str" value="<?= $weapon['add_str'] ?>">
                        </div>
                        <div class="form-group">
                            <label for="add_con">CON:</label>
                            <input type="number" id="add_con" name="add_con" value="<?= $weapon['add_con'] ?>">
                        </div>
                        <div class="form-group">
                            <label for="add_dex">DEX:</label>
                            <input type="number" id="add_dex" name="add_dex" value="<?= $weapon['add_dex'] ?>">
                        </div>
                        <div class="form-group">
                            <label for="add_int">INT:</label>
                            <input type="number" id="add_int" name="add_int" value="<?= $weapon['add_int'] ?>">
                        </div>
                        <div class="form-group">
                            <label for="add_wis">WIS:</label>
                            <input type="number" id="add_wis" name="add_wis" value="<?= $weapon['add_wis'] ?>">
                        </div>
                        <div class="form-group">
                            <label for="add_cha">CHA:</label>
                            <input type="number" id="add_cha" name="add_cha" value="<?= $weapon['add_cha'] ?>">
                        </div>
                        <div class="form-group">
                            <label for="add_hp">HP:</label>
                            <input type="number" id="add_hp" name="add_hp" value="<?= $weapon['add_hp'] ?>">
                        </div>
                        <div class="form-group">
                            <label for="add_mp">MP:</label>
                            <input type="number" id="add_mp" name="add_mp" value="<?= $weapon['add_mp'] ?>">
                        </div>
                        <div class="form-group">
                            <label for="add_sp">SP:</label>
                            <input type="number" id="add_sp" name="add_sp" value="<?= $weapon['add_sp'] ?>">
                        </div>
                    </div>
                </div>
                
                <!-- Class Restrictions -->
                <div class="weapon-section">
                    <h2>Class Restrictions</h2>
                    <div class="class-grid">
                        <div class="form-group">
                            <label for="use_royal">Royal:</label>
                            <select id="use_royal" name="use_royal">
                                <option value="1" <?= $weapon['use_royal'] ? 'selected' : '' ?>>Allowed</option>
                                <option value="0" <?= !$weapon['use_royal'] ? 'selected' : '' ?>>Restricted</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="use_knight">Knight:</label>
                            <select id="use_knight" name="use_knight">
                                <option value="1" <?= $weapon['use_knight'] ? 'selected' : '' ?>>Allowed</option>
                                <option value="0" <?= !$weapon['use_knight'] ? 'selected' : '' ?>>Restricted</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="use_mage">Mage:</label>
                            <select id="use_mage" name="use_mage">
                                <option value="1" <?= $weapon['use_mage'] ? 'selected' : '' ?>>Allowed</option>
                                <option value="0" <?= !$weapon['use_mage'] ? 'selected' : '' ?>>Restricted</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="use_elf">Elf:</label>
                            <select id="use_elf" name="use_elf">
                                <option value="1" <?= $weapon['use_elf'] ? 'selected' : '' ?>>Allowed</option>
                                <option value="0" <?= !$weapon['use_elf'] ? 'selected' : '' ?>>Restricted</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="use_darkelf">Dark Elf:</label>
                            <select id="use_darkelf" name="use_darkelf">
                                <option value="1" <?= $weapon['use_darkelf'] ? 'selected' : '' ?>>Allowed</option>
                                <option value="0" <?= !$weapon['use_darkelf'] ? 'selected' : '' ?>>Restricted</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="use_dragonknight">Dragon Knight:</label>
                            <select id="use_dragonknight" name="use_dragonknight">
                                <option value="1" <?= $weapon['use_dragonknight'] ? 'selected' : '' ?>>Allowed</option>
                                <option value="0" <?= !$weapon['use_dragonknight'] ? 'selected' : '' ?>>Restricted</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="use_illusionist">Illusionist:</label>
                            <select id="use_illusionist" name="use_illusionist">
                                <option value="1" <?= $weapon['use_illusionist'] ? 'selected' : '' ?>>Allowed</option>
                                <option value="0" <?= !$weapon['use_illusionist'] ? 'selected' : '' ?>>Restricted</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="use_warrior">Warrior:</label>
                            <select id="use_warrior" name="use_warrior">
                                <option value="1" <?= $weapon['use_warrior'] ? 'selected' : '' ?>>Allowed</option>
                                <option value="0" <?= !$weapon['use_warrior'] ? 'selected' : '' ?>>Restricted</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="use_fencer">Fencer:</label>
                            <select id="use_fencer" name="use_fencer">
                                <option value="1" <?= $weapon['use_fencer'] ? 'selected' : '' ?>>Allowed</option>
                                <option value="0" <?= !$weapon['use_fencer'] ? 'selected' : '' ?>>Restricted</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="use_lancer">Lancer:</label>
                            <select id="use_lancer" name="use_lancer">
                                <option value="1" <?= $weapon['use_lancer'] ? 'selected' : '' ?>>Allowed</option>
                                <option value="0" <?= !$weapon['use_lancer'] ? 'selected' : '' ?>>Restricted</option>
                            </select>
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

<?php require_once dirname(dirname(dirname(__DIR__))) . '/includes/footer.php'; ?> 