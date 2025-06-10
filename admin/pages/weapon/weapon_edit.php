<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../includes/header.php';

// Get weapon ID from URL
$weaponId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $weaponId > 0) {
    try {
        // Prepare update query
        $sql = "UPDATE weapon SET 
                desc_en = ?, desc_kr = ?, iconId = ?, type = ?, 
                itemGrade = ?, material = ?, weight = ?, bless = ?,
                dmg_small = ?, dmg_large = ?, hit_modifier = ?, dmgmodifier = ?,
                safenchant = ?, double_dmg_chance = ?, magicdmgmodifier = ?, canbedmg = ?,
                use_royal = ?, use_knight = ?, use_mage = ?, use_elf = ?,
                use_darkelf = ?, use_dragonknight = ?, use_illusionist = ?, use_warrior = ?,
                use_fencer = ?, use_lancer = ?, add_str = ?, add_con = ?,
                add_dex = ?, add_int = ?, add_wis = ?, add_cha = ?,
                add_hp = ?, add_mp = ?, add_hpr = ?, add_mpr = ?,
                add_sp = ?, m_def = ?, min_lvl = ?, max_lvl = ?,
                trade = ?, retrieve = ?, cant_delete = ?, cant_sell = ?,
                Magic_name = ?, poisonRegist = ?
                WHERE item_id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['desc_en'], $_POST['desc_kr'], $_POST['iconId'], $_POST['type'],
            $_POST['itemGrade'], $_POST['material'], $_POST['weight'], $_POST['bless'],
            $_POST['dmg_small'], $_POST['dmg_large'], $_POST['hit_modifier'], $_POST['dmgmodifier'],
            $_POST['safenchant'], $_POST['double_dmg_chance'], $_POST['magicdmgmodifier'], $_POST['canbedmg'],
            isset($_POST['use_royal']) ? 1 : 0, isset($_POST['use_knight']) ? 1 : 0, 
            isset($_POST['use_mage']) ? 1 : 0, isset($_POST['use_elf']) ? 1 : 0,
            isset($_POST['use_darkelf']) ? 1 : 0, isset($_POST['use_dragonknight']) ? 1 : 0, 
            isset($_POST['use_illusionist']) ? 1 : 0, isset($_POST['use_warrior']) ? 1 : 0,
            isset($_POST['use_fencer']) ? 1 : 0, isset($_POST['use_lancer']) ? 1 : 0,
            $_POST['add_str'], $_POST['add_con'], $_POST['add_dex'], $_POST['add_int'],
            $_POST['add_wis'], $_POST['add_cha'], $_POST['add_hp'], $_POST['add_mp'],
            $_POST['add_hpr'], $_POST['add_mpr'], $_POST['add_sp'], $_POST['m_def'],
            $_POST['min_lvl'], $_POST['max_lvl'], isset($_POST['trade']) ? 1 : 0, 
            isset($_POST['retrieve']) ? 1 : 0, isset($_POST['cant_delete']) ? 1 : 0, 
            isset($_POST['cant_sell']) ? 1 : 0, $_POST['Magic_name'], $_POST['poisonRegist'],
            $weaponId
        ]);
        
        logAdminActivity('UPDATE', 'weapon', $weaponId, 'Updated weapon: ' . $_POST['desc_en']);
        
        $message = 'Weapon updated successfully!';
        $messageType = 'success';
    } catch (PDOException $e) {
        $message = 'Error updating weapon: ' . $e->getMessage();
        $messageType = 'error';
    }
}

// Get weapon data
$weapon = null;
if ($weaponId > 0) {
    $sql = "SELECT * FROM weapon WHERE item_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$weaponId]);
    $weapon = $stmt->fetch();
}
?>

        <!-- Admin Header -->
        <div class="admin-header">
            <h1>Edit Weapon</h1>
            <div class="admin-header-actions">
                <a href="<?php echo SITE_URL; ?>/admin/pages/weapon/weapon_list.php" class="admin-btn admin-btn-secondary">
                    ← Back to List
                </a>
            </div>
        </div>

        <!-- Breadcrumb -->
        <nav class="admin-breadcrumb">
            <ul class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="/l1jdb_database/admin/">Admin</a></li>
                <li class="breadcrumb-separator">›</li>
                <li class="breadcrumb-item"><a href="/l1jdb_database/admin/pages/weapon/weapon_list.php">Weapon Management</a></li>
                <li class="breadcrumb-separator">›</li>
                <li class="breadcrumb-item">Edit Weapon</li>
            </ul>
        </nav>

        <?php if (isset($message)): ?>
            <div class="admin-message admin-message-<?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($weapon): ?>
        <form method="POST" class="admin-form">
            <!-- Basic Information -->
            <div class="form-section">
                <h3>📝 Basic Information</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="item_id">Item ID</label>
                        <input type="text" id="item_id" value="<?php echo htmlspecialchars($weapon['item_id']); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="desc_en">English Name *</label>
                        <input type="text" id="desc_en" name="desc_en" value="<?php echo htmlspecialchars($weapon['desc_en']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="desc_kr">Korean Name</label>
                        <input type="text" id="desc_kr" name="desc_kr" value="<?php echo htmlspecialchars($weapon['desc_kr']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="iconId">Icon ID</label>
                        <input type="number" id="iconId" name="iconId" value="<?php echo htmlspecialchars($weapon['iconId']); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="type">Type</label>
                        <select id="type" name="type">
                            <option value="SWORD" <?php echo $weapon['type'] === 'SWORD' ? 'selected' : ''; ?>>Sword</option>
                            <option value="DAGGER" <?php echo $weapon['type'] === 'DAGGER' ? 'selected' : ''; ?>>Dagger</option>
                            <option value="TOHAND_SWORD" <?php echo $weapon['type'] === 'TOHAND_SWORD' ? 'selected' : ''; ?>>Two-Handed Sword</option>
                            <option value="BOW" <?php echo $weapon['type'] === 'BOW' ? 'selected' : ''; ?>>Bow</option>
                            <option value="SPEAR" <?php echo $weapon['type'] === 'SPEAR' ? 'selected' : ''; ?>>Spear</option>
                            <option value="BLUNT" <?php echo $weapon['type'] === 'BLUNT' ? 'selected' : ''; ?>>Blunt</option>
                            <option value="STAFF" <?php echo $weapon['type'] === 'STAFF' ? 'selected' : ''; ?>>Staff</option>
                            <option value="STING" <?php echo $weapon['type'] === 'STING' ? 'selected' : ''; ?>>Sting</option>
                            <option value="ARROW" <?php echo $weapon['type'] === 'ARROW' ? 'selected' : ''; ?>>Arrow</option>
                            <option value="GAUNTLET" <?php echo $weapon['type'] === 'GAUNTLET' ? 'selected' : ''; ?>>Gauntlet</option>
                            <option value="CLAW" <?php echo $weapon['type'] === 'CLAW' ? 'selected' : ''; ?>>Claw</option>
                            <option value="EDORYU" <?php echo $weapon['type'] === 'EDORYU' ? 'selected' : ''; ?>>Edoryu</option>
                            <option value="SINGLE_BOW" <?php echo $weapon['type'] === 'SINGLE_BOW' ? 'selected' : ''; ?>>Single Bow</option>
                            <option value="SINGLE_SPEAR" <?php echo $weapon['type'] === 'SINGLE_SPEAR' ? 'selected' : ''; ?>>Single Spear</option>
                            <option value="TOHAND_BLUNT" <?php echo $weapon['type'] === 'TOHAND_BLUNT' ? 'selected' : ''; ?>>Two-Handed Blunt</option>
                            <option value="TOHAND_STAFF" <?php echo $weapon['type'] === 'TOHAND_STAFF' ? 'selected' : ''; ?>>Two-Handed Staff</option>
                            <option value="KEYRINGK" <?php echo $weapon['type'] === 'KEYRINGK' ? 'selected' : ''; ?>>Kiringku</option>
                            <option value="CHAINSWORD" <?php echo $weapon['type'] === 'CHAINSWORD' ? 'selected' : ''; ?>>Chain Sword</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="itemGrade">Grade</label>
                        <select id="itemGrade" name="itemGrade">
                            <option value="NORMAL" <?php echo $weapon['itemGrade'] === 'NORMAL' ? 'selected' : ''; ?>>Normal</option>
                            <option value="ADVANC" <?php echo $weapon['itemGrade'] === 'ADVANC' ? 'selected' : ''; ?>>Advanced</option>
                            <option value="RARE" <?php echo $weapon['itemGrade'] === 'RARE' ? 'selected' : ''; ?>>Rare</option>
                            <option value="HERO" <?php echo $weapon['itemGrade'] === 'HERO' ? 'selected' : ''; ?>>Hero</option>
                            <option value="LEGEND" <?php echo $weapon['itemGrade'] === 'LEGEND' ? 'selected' : ''; ?>>Legendary</option>
                            <option value="MYTH" <?php echo $weapon['itemGrade'] === 'MYTH' ? 'selected' : ''; ?>>Mythic</option>
                            <option value="ONLY" <?php echo $weapon['itemGrade'] === 'ONLY' ? 'selected' : ''; ?>>Only</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="material">Material</label>
                        <select id="material" name="material">
                            <option value="NONE(-)" <?php echo $weapon['material'] === 'NONE(-)' ? 'selected' : ''; ?>>None</option>
                            <option value="WOOD(나무)" <?php echo $weapon['material'] === 'WOOD(나무)' ? 'selected' : ''; ?>>Wood</option>
                            <option value="IRON(철)" <?php echo $weapon['material'] === 'IRON(철)' ? 'selected' : ''; ?>>Iron</option>
                            <option value="SILVER(은)" <?php echo $weapon['material'] === 'SILVER(은)' ? 'selected' : ''; ?>>Silver</option>
                            <option value="GOLD(금)" <?php echo $weapon['material'] === 'GOLD(금)' ? 'selected' : ''; ?>>Gold</option>
                            <option value="PLATINUM(백금)" <?php echo $weapon['material'] === 'PLATINUM(백금)' ? 'selected' : ''; ?>>Platinum</option>
                            <option value="MITHRIL(미스릴)" <?php echo $weapon['material'] === 'MITHRIL(미스릴)' ? 'selected' : ''; ?>>Mithril</option>
                            <option value="ORIHARUKON(오리하루콘)" <?php echo $weapon['material'] === 'ORIHARUKON(오리하루콘)' ? 'selected' : ''; ?>>Oriharukon</option>
                            <option value="DRANIUM(드라니움)" <?php echo $weapon['material'] === 'DRANIUM(드라니움)' ? 'selected' : ''; ?>>Dranium</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="weight">Weight</label>
                        <input type="number" id="weight" name="weight" value="<?php echo htmlspecialchars($weapon['weight']); ?>">
                    </div>
                </div>
            </div>

            <!-- Combat Stats -->
            <div class="form-section">
                <h3>⚔️ Combat Stats</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="dmg_small">Damage (Small)</label>
                        <input type="number" id="dmg_small" name="dmg_small" value="<?php echo htmlspecialchars($weapon['dmg_small']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="dmg_large">Damage (Large)</label>
                        <input type="number" id="dmg_large" name="dmg_large" value="<?php echo htmlspecialchars($weapon['dmg_large']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="hit_modifier">Hit Modifier</label>
                        <input type="number" id="hit_modifier" name="hit_modifier" value="<?php echo htmlspecialchars($weapon['hit_modifier']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="dmgmodifier">Damage Modifier</label>
                        <input type="number" id="dmgmodifier" name="dmgmodifier" value="<?php echo htmlspecialchars($weapon['dmgmodifier']); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="safenchant">Safe Enchant Level</label>
                        <input type="number" id="safenchant" name="safenchant" value="<?php echo htmlspecialchars($weapon['safenchant']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="double_dmg_chance">Double Damage Chance</label>
                        <input type="number" id="double_dmg_chance" name="double_dmg_chance" value="<?php echo htmlspecialchars($weapon['double_dmg_chance']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="magicdmgmodifier">Magic Damage Modifier</label>
                        <input type="number" id="magicdmgmodifier" name="magicdmgmodifier" value="<?php echo htmlspecialchars($weapon['magicdmgmodifier']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="canbedmg">Can Be Damaged</label>
                        <input type="number" id="canbedmg" name="canbedmg" value="<?php echo htmlspecialchars($weapon['canbedmg']); ?>">
                    </div>
                </div>
            </div>

            <!-- Class Restrictions -->
            <div class="form-section">
                <h3>👥 Class Restrictions</h3>
                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <input type="checkbox" id="use_royal" name="use_royal" value="1" <?php echo $weapon['use_royal'] ? 'checked' : ''; ?>>
                        <label for="use_royal">Royal</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="use_knight" name="use_knight" value="1" <?php echo $weapon['use_knight'] ? 'checked' : ''; ?>>
                        <label for="use_knight">Knight</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="use_mage" name="use_mage" value="1" <?php echo $weapon['use_mage'] ? 'checked' : ''; ?>>
                        <label for="use_mage">Mage</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="use_elf" name="use_elf" value="1" <?php echo $weapon['use_elf'] ? 'checked' : ''; ?>>
                        <label for="use_elf">Elf</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="use_darkelf" name="use_darkelf" value="1" <?php echo $weapon['use_darkelf'] ? 'checked' : ''; ?>>
                        <label for="use_darkelf">Dark Elf</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="use_dragonknight" name="use_dragonknight" value="1" <?php echo $weapon['use_dragonknight'] ? 'checked' : ''; ?>>
                        <label for="use_dragonknight">Dragon Knight</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="use_illusionist" name="use_illusionist" value="1" <?php echo $weapon['use_illusionist'] ? 'checked' : ''; ?>>
                        <label for="use_illusionist">Illusionist</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="use_warrior" name="use_warrior" value="1" <?php echo $weapon['use_warrior'] ? 'checked' : ''; ?>>
                        <label for="use_warrior">Warrior</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="use_fencer" name="use_fencer" value="1" <?php echo $weapon['use_fencer'] ? 'checked' : ''; ?>>
                        <label for="use_fencer">Fencer</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="use_lancer" name="use_lancer" value="1" <?php echo $weapon['use_lancer'] ? 'checked' : ''; ?>>
                        <label for="use_lancer">Lancer</label>
                    </div>
                </div>
            </div>

            <!-- Stat Bonuses -->
            <div class="form-section">
                <h3>📊 Stat Bonuses</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="add_str">Strength</label>
                        <input type="number" id="add_str" name="add_str" value="<?php echo htmlspecialchars($weapon['add_str']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="add_con">Constitution</label>
                        <input type="number" id="add_con" name="add_con" value="<?php echo htmlspecialchars($weapon['add_con']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="add_dex">Dexterity</label>
                        <input type="number" id="add_dex" name="add_dex" value="<?php echo htmlspecialchars($weapon['add_dex']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="add_int">Intelligence</label>
                        <input type="number" id="add_int" name="add_int" value="<?php echo htmlspecialchars($weapon['add_int']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="add_wis">Wisdom</label>
                        <input type="number" id="add_wis" name="add_wis" value="<?php echo htmlspecialchars($weapon['add_wis']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="add_cha">Charisma</label>
                        <input type="number" id="add_cha" name="add_cha" value="<?php echo htmlspecialchars($weapon['add_cha']); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="add_hp">HP Bonus</label>
                        <input type="number" id="add_hp" name="add_hp" value="<?php echo htmlspecialchars($weapon['add_hp']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="add_mp">MP Bonus</label>
                        <input type="number" id="add_mp" name="add_mp" value="<?php echo htmlspecialchars($weapon['add_mp']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="add_hpr">HP Regen</label>
                        <input type="number" id="add_hpr" name="add_hpr" value="<?php echo htmlspecialchars($weapon['add_hpr']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="add_mpr">MP Regen</label>
                        <input type="number" id="add_mpr" name="add_mpr" value="<?php echo htmlspecialchars($weapon['add_mpr']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="add_sp">SP Bonus</label>
                        <input type="number" id="add_sp" name="add_sp" value="<?php echo htmlspecialchars($weapon['add_sp']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="m_def">Magic Defense</label>
                        <input type="number" id="m_def" name="m_def" value="<?php echo htmlspecialchars($weapon['m_def']); ?>">
                    </div>
                </div>
            </div>

            <!-- Level and Trade Restrictions -->
            <div class="form-section">
                <h3>🔒 Restrictions</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="min_lvl">Minimum Level</label>
                        <input type="number" id="min_lvl" name="min_lvl" value="<?php echo htmlspecialchars($weapon['min_lvl']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="max_lvl">Maximum Level</label>
                        <input type="number" id="max_lvl" name="max_lvl" value="<?php echo htmlspecialchars($weapon['max_lvl']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="bless">Bless</label>
                        <select id="bless" name="bless">
                            <option value="1" <?php echo $weapon['bless'] == 1 ? 'selected' : ''; ?>>Yes</option>
                            <option value="0" <?php echo $weapon['bless'] == 0 ? 'selected' : ''; ?>>No</option>
                        </select>
                    </div>
                </div>
                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <input type="checkbox" id="trade" name="trade" value="1" <?php echo $weapon['trade'] ? 'checked' : ''; ?>>
                        <label for="trade">Tradeable</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="retrieve" name="retrieve" value="1" <?php echo $weapon['retrieve'] ? 'checked' : ''; ?>>
                        <label for="retrieve">Retrievable</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="cant_delete" name="cant_delete" value="1" <?php echo $weapon['cant_delete'] ? 'checked' : ''; ?>>
                        <label for="cant_delete">Cannot Delete</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="cant_sell" name="cant_sell" value="1" <?php echo $weapon['cant_sell'] ? 'checked' : ''; ?>>
                        <label for="cant_sell">Cannot Sell</label>
                    </div>
                </div>
            </div>

            <!-- Advanced Properties -->
            <div class="form-section">
                <h3>⚙️ Advanced Properties</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="Magic_name">Magic Name</label>
                        <input type="text" id="Magic_name" name="Magic_name" value="<?php echo htmlspecialchars($weapon['Magic_name']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="poisonRegist">Poison Resist</label>
                        <select id="poisonRegist" name="poisonRegist">
                            <option value="false" <?php echo $weapon['poisonRegist'] === 'false' ? 'selected' : ''; ?>>False</option>
                            <option value="true" <?php echo $weapon['poisonRegist'] === 'true' ? 'selected' : ''; ?>>True</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-section">
                <div class="btn-group">
                    <button type="submit" class="admin-btn admin-btn-primary admin-btn-large">
                        💾 Update Weapon
                    </button>
                    <a href="<?php echo SITE_URL; ?>/admin/pages/weapon/weapon_list.php" class="admin-btn admin-btn-secondary admin-btn-large">
                        ❌ Cancel
                    </a>
                    <a href="<?php echo SITE_URL; ?>/admin/pages/weapon/weapon_list.php?action=delete&id=<?php echo $weaponId; ?>" 
                       class="admin-btn admin-btn-danger admin-btn-large"
                       onclick="return confirm('Are you sure you want to delete this weapon? This action cannot be undone.')">
                        🗑️ Delete Weapon
                    </a>
                </div>
            </div>
        </form>
        <?php else: ?>
        <div class="admin-empty">
                <h3>Weapon Not Found</h3>
                <p>The requested weapon could not be found.</p>
                <a href="<?php echo SITE_URL; ?>/admin/pages/weapon/weapon_list.php" class="admin-btn admin-btn-primary">
                    Back to Weapon List
                </a>
            </div>
        <?php endif; ?>

<script>
// Form validation and enhancement
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.admin-form');
    
    if (form) {
        // Form validation
        form.addEventListener('submit', function(e) {
            const descEn = document.querySelector('#desc_en').value;
            
            if (!descEn) {
                e.preventDefault();
                alert('English name is required.');
                return false;
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
