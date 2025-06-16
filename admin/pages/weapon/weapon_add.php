<?php
require_once dirname(dirname(dirname(__DIR__))) . '/includes/config.php';
require_once dirname(dirname(dirname(__DIR__))) . '/includes/header.php';

// Call page header
getPageHeader("Add New Weapon");

// Render hero section
renderHero('weapons', "Add New Weapon", "Create a new weapon entry");
?>

<main>
    <div class="main">
        <div class="container">
            <!-- Back Navigation -->
            <div class="back-nav">
                <a href="../weapons/weapon_list.php" class="back-btn">&larr; Back to Weapons List</a>
            </div>
            
            <form action="weapon_process.php" method="POST" class="weapon-form">
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
                                    <label for="desc_en">Name:</label>
                                    <input type="text" id="desc_en" name="desc_en" required>
                                </div>
                                <div class="form-group">
                                    <label for="type">Type:</label>
                                    <select id="type" name="type" required>
                                        <?php foreach (getWeaponTypes() as $type => $label): ?>
                                            <option value="<?= $type ?>"><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="material">Material:</label>
                                    <select id="material" name="material" required>
                                        <?php foreach (getWeaponMaterials() as $material => $label): ?>
                                            <option value="<?= $material ?>"><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="weight">Weight:</label>
                                    <input type="number" id="weight" name="weight" value="0" required>
                                </div>
                                <div class="form-group">
                                    <label for="itemGrade">Grade:</label>
                                    <select id="itemGrade" name="itemGrade">
                                        <option value="0">None</option>
                                        <option value="1">D</option>
                                        <option value="2">C</option>
                                        <option value="3">B</option>
                                        <option value="4">A</option>
                                        <option value="5">S</option>
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
                                    <input type="number" id="dmg_small" name="dmg_small" value="0" required>
                                </div>
                                <div class="form-group">
                                    <label for="dmg_large">Large:</label>
                                    <input type="number" id="dmg_large" name="dmg_large" value="0" required>
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
                                    <label for="safenchant">Safe Enchant:</label>
                                    <input type="number" id="safenchant" name="safenchant" value="0">
                                </div>
                                <div class="form-group">
                                    <label for="canbedmg">Can be damaged:</label>
                                    <select id="canbedmg" name="canbedmg">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
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
                            <input type="number" id="add_str" name="add_str" value="0">
                        </div>
                        <div class="form-group">
                            <label for="add_con">CON:</label>
                            <input type="number" id="add_con" name="add_con" value="0">
                        </div>
                        <div class="form-group">
                            <label for="add_dex">DEX:</label>
                            <input type="number" id="add_dex" name="add_dex" value="0">
                        </div>
                        <div class="form-group">
                            <label for="add_int">INT:</label>
                            <input type="number" id="add_int" name="add_int" value="0">
                        </div>
                        <div class="form-group">
                            <label for="add_wis">WIS:</label>
                            <input type="number" id="add_wis" name="add_wis" value="0">
                        </div>
                        <div class="form-group">
                            <label for="add_cha">CHA:</label>
                            <input type="number" id="add_cha" name="add_cha" value="0">
                        </div>
                        <div class="form-group">
                            <label for="add_hp">HP:</label>
                            <input type="number" id="add_hp" name="add_hp" value="0">
                        </div>
                        <div class="form-group">
                            <label for="add_mp">MP:</label>
                            <input type="number" id="add_mp" name="add_mp" value="0">
                        </div>
                        <div class="form-group">
                            <label for="add_sp">SP:</label>
                            <input type="number" id="add_sp" name="add_sp" value="0">
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
                                <option value="1">Allowed</option>
                                <option value="0">Restricted</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="use_knight">Knight:</label>
                            <select id="use_knight" name="use_knight">
                                <option value="1">Allowed</option>
                                <option value="0">Restricted</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="use_mage">Mage:</label>
                            <select id="use_mage" name="use_mage">
                                <option value="1">Allowed</option>
                                <option value="0">Restricted</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="use_elf">Elf:</label>
                            <select id="use_elf" name="use_elf">
                                <option value="1">Allowed</option>
                                <option value="0">Restricted</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="use_darkelf">Dark Elf:</label>
                            <select id="use_darkelf" name="use_darkelf">
                                <option value="1">Allowed</option>
                                <option value="0">Restricted</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="use_dragonknight">Dragon Knight:</label>
                            <select id="use_dragonknight" name="use_dragonknight">
                                <option value="1">Allowed</option>
                                <option value="0">Restricted</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="use_illusionist">Illusionist:</label>
                            <select id="use_illusionist" name="use_illusionist">
                                <option value="1">Allowed</option>
                                <option value="0">Restricted</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="use_warrior">Warrior:</label>
                            <select id="use_warrior" name="use_warrior">
                                <option value="1">Allowed</option>
                                <option value="0">Restricted</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="use_fencer">Fencer:</label>
                            <select id="use_fencer" name="use_fencer">
                                <option value="1">Allowed</option>
                                <option value="0">Restricted</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="use_lancer">Lancer:</label>
                            <select id="use_lancer" name="use_lancer">
                                <option value="1">Allowed</option>
                                <option value="0">Restricted</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Create Weapon</button>
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

<?php require_once dirname(dirname(dirname(__DIR__))) . '/includes/footer.php'; ?> 