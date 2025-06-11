<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../includes/header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Generate new item ID
        $stmt = $pdo->query("SELECT MAX(item_id) FROM weapon");
        $maxId = $stmt->fetchColumn();
        $newItemId = $maxId + 1;
        
        // Prepare insert query with all columns
        $sql = "INSERT INTO weapon (
                item_id, item_name_id, desc_kr, desc_en, desc_powerbook, note, desc_id,
                itemGrade, type, material, weight, iconId, spriteId, dmg_small, dmg_large,
                safenchant, use_royal, use_knight, use_mage, use_elf, use_darkelf,
                use_dragonknight, use_illusionist, use_warrior, use_fencer, use_lancer,
                hitmodifier, dmgmodifier, add_str, add_con, add_dex, add_int, add_wis,
                add_cha, add_hp, add_mp, add_hpr, add_mpr, add_sp, m_def, haste_item,
                double_dmg_chance, magicdmgmodifier, canbedmg, min_lvl, max_lvl, bless,
                trade, retrieve, specialretrieve, cant_delete, cant_sell, max_use_time,
                regist_skill, regist_spirit, regist_dragon, regist_fear, regist_all,
                hitup_skill, hitup_spirit, hitup_dragon, hitup_fear, hitup_all, hitup_magic,
                damage_reduction, MagicDamageReduction, reductionEgnor, reductionPercent,
                PVPDamage, PVPDamageReduction, PVPDamageReductionPercent, PVPMagicDamageReduction,
                PVPReductionEgnor, PVPMagicDamageReductionEgnor, abnormalStatusDamageReduction,
                abnormalStatusPVPDamageReduction, PVPDamagePercent, expBonus, rest_exp_reduce_efficiency,
                shortCritical, longCritical, magicCritical, addDg, addEr, addMe, poisonRegist,
                imunEgnor, stunDuration, tripleArrowStun, strangeTimeIncrease, strangeTimeDecrease,
                potionRegist, potionPercent, potionValue, hprAbsol32Second, mprAbsol64Second,
                mprAbsol16Second, hpPotionDelayDecrease, hpPotionCriticalProb, increaseArmorSkillProb,
                attackSpeedDelayRate, moveSpeedDelayRate, Magic_name
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $newItemId, $_POST['item_name_id'] ?? 0, $_POST['desc_kr'] ?? '', $_POST['desc_en'],
            $_POST['desc_powerbook'] ?? '', $_POST['note'] ?? '', $_POST['desc_id'] ?? '',
            $_POST['itemGrade'], $_POST['type'], $_POST['material'], $_POST['weight'] ?? 0,
            $_POST['iconId'] ?? 0, $_POST['spriteId'] ?? 0, $_POST['dmg_small'] ?? 0,
            $_POST['dmg_large'] ?? 0, $_POST['safenchant'] ?? 0,
            isset($_POST['use_royal']) ? 1 : 0, isset($_POST['use_knight']) ? 1 : 0,
            isset($_POST['use_mage']) ? 1 : 0, isset($_POST['use_elf']) ? 1 : 0,
            isset($_POST['use_darkelf']) ? 1 : 0, isset($_POST['use_dragonknight']) ? 1 : 0,
            isset($_POST['use_illusionist']) ? 1 : 0, isset($_POST['use_warrior']) ? 1 : 0,
            isset($_POST['use_fencer']) ? 1 : 0, isset($_POST['use_lancer']) ? 1 : 0,
            $_POST['hitmodifier'] ?? 0, $_POST['dmgmodifier'] ?? 0,
            $_POST['add_str'] ?? 0, $_POST['add_con'] ?? 0, $_POST['add_dex'] ?? 0,
            $_POST['add_int'] ?? 0, $_POST['add_wis'] ?? 0, $_POST['add_cha'] ?? 0,
            $_POST['add_hp'] ?? 0, $_POST['add_mp'] ?? 0, $_POST['add_hpr'] ?? 0,
            $_POST['add_mpr'] ?? 0, $_POST['add_sp'] ?? 0, $_POST['m_def'] ?? 0,
            $_POST['haste_item'] ?? 0, $_POST['double_dmg_chance'] ?? 0,
            $_POST['magicdmgmodifier'] ?? 0, $_POST['canbedmg'] ?? 0,
            $_POST['min_lvl'] ?? 0, $_POST['max_lvl'] ?? 0, $_POST['bless'] ?? 1,
            isset($_POST['trade']) ? 1 : 0, isset($_POST['retrieve']) ? 1 : 0,
            isset($_POST['specialretrieve']) ? 1 : 0, isset($_POST['cant_delete']) ? 1 : 0,
            isset($_POST['cant_sell']) ? 1 : 0, $_POST['max_use_time'] ?? 0,
            $_POST['regist_skill'] ?? 0, $_POST['regist_spirit'] ?? 0,
            $_POST['regist_dragon'] ?? 0, $_POST['regist_fear'] ?? 0,
            $_POST['regist_all'] ?? 0, $_POST['hitup_skill'] ?? 0,
            $_POST['hitup_spirit'] ?? 0, $_POST['hitup_dragon'] ?? 0,
            $_POST['hitup_fear'] ?? 0, $_POST['hitup_all'] ?? 0,
            $_POST['hitup_magic'] ?? 0, $_POST['damage_reduction'] ?? 0,
            $_POST['MagicDamageReduction'] ?? 0, $_POST['reductionEgnor'] ?? 0,
            $_POST['reductionPercent'] ?? 0, $_POST['PVPDamage'] ?? 0,
            $_POST['PVPDamageReduction'] ?? 0, $_POST['PVPDamageReductionPercent'] ?? 0,
            $_POST['PVPMagicDamageReduction'] ?? 0, $_POST['PVPReductionEgnor'] ?? 0,
            $_POST['PVPMagicDamageReductionEgnor'] ?? 0, $_POST['abnormalStatusDamageReduction'] ?? 0,
            $_POST['abnormalStatusPVPDamageReduction'] ?? 0, $_POST['PVPDamagePercent'] ?? 0,
            $_POST['expBonus'] ?? 0, $_POST['rest_exp_reduce_efficiency'] ?? 0,
            $_POST['shortCritical'] ?? 0, $_POST['longCritical'] ?? 0,
            $_POST['magicCritical'] ?? 0, $_POST['addDg'] ?? 0,
            $_POST['addEr'] ?? 0, $_POST['addMe'] ?? 0,
            $_POST['poisonRegist'] ?? 'false', $_POST['imunEgnor'] ?? 0,
            $_POST['stunDuration'] ?? 0, $_POST['tripleArrowStun'] ?? 0,
            $_POST['strangeTimeIncrease'] ?? 0, $_POST['strangeTimeDecrease'] ?? 0,
            $_POST['potionRegist'] ?? 0, $_POST['potionPercent'] ?? 0,
            $_POST['potionValue'] ?? 0, $_POST['hprAbsol32Second'] ?? 0,
            $_POST['mprAbsol64Second'] ?? 0, $_POST['mprAbsol16Second'] ?? 0,
            $_POST['hpPotionDelayDecrease'] ?? 0, $_POST['hpPotionCriticalProb'] ?? 0,
            $_POST['increaseArmorSkillProb'] ?? 0, $_POST['attackSpeedDelayRate'] ?? 0,
            $_POST['moveSpeedDelayRate'] ?? 0, $_POST['Magic_name'] ?? null
        ]);
        
        logAdminActivity('CREATE', 'weapon', $newItemId, 'Created weapon: ' . $_POST['desc_en']);
        header("Location: " . SITE_URL . "/admin/pages/weapon/weapon_edit.php?id=" . $newItemId);
        exit;
        
    } catch (PDOException $e) {
        $message = 'Error creating weapon: ' . $e->getMessage();
        $messageType = 'error';
    }
}
?>

        <div class="admin-header">
            <h1>Add New Weapon</h1>
            <div class="admin-header-actions">
                <a href="<?php echo SITE_URL; ?>/admin/pages/weapon/weapon_list.php" class="admin-btn admin-btn-secondary">← Back to List</a>
            </div>
        </div>

        <nav class="admin-breadcrumb">
            <ul class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/admin/">Admin</a></li>
                <li class="breadcrumb-separator">›</li>
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/admin/pages/weapon/weapon_list.php">Weapon Management</a></li>
                <li class="breadcrumb-separator">›</li>
                <li class="breadcrumb-item">Add New Weapon</li>
            </ul>
        </nav>

        <?php if (isset($message)): ?>
            <div class="admin-message admin-message-<?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="admin-form">
            <div class="form-section">
                <h3>📝 Basic Information</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="desc_en">English Name *</label>
                        <input type="text" id="desc_en" name="desc_en" required>
                    </div>
                    <div class="form-group">
                        <label for="desc_kr">Korean Name</label>
                        <input type="text" id="desc_kr" name="desc_kr">
                    </div>
                    <div class="form-group">
                        <label for="iconId">Icon ID</label>
                        <input type="number" id="iconId" name="iconId" value="0">
                    </div>
                    <div class="form-group">
                        <label for="weight">Weight</label>
                        <input type="number" id="weight" name="weight" value="0">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="type">Type *</label>
                        <select id="type" name="type" required>
                            <option value="SWORD">Sword</option>
                            <option value="DAGGER">Dagger</option>
                            <option value="TOHAND_SWORD">Two-Handed Sword</option>
                            <option value="BOW">Bow</option>
                            <option value="SPEAR">Spear</option>
                            <option value="BLUNT">Blunt</option>
                            <option value="STAFF">Staff</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="itemGrade">Grade</label>
                        <select id="itemGrade" name="itemGrade">
                            <option value="NORMAL">Normal</option>
                            <option value="ADVANC">Advanced</option>
                            <option value="RARE">Rare</option>
                            <option value="HERO">Hero</option>
                            <option value="LEGEND">Legendary</option>
                            <option value="MYTH">Mythic</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="material">Material</label>
                        <select id="material" name="material">
                            <option value="NONE(-)">None</option>
                            <option value="WOOD(나무)">Wood</option>
                            <option value="IRON(철)">Iron</option>
                            <option value="SILVER(은)">Silver</option>
                            <option value="MITHRIL(미스릴)">Mithril</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>⚔️ Combat Stats</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="dmg_small">Damage (Small)</label>
                        <input type="number" id="dmg_small" name="dmg_small" value="0">
                    </div>
                    <div class="form-group">
                        <label for="dmg_large">Damage (Large)</label>
                        <input type="number" id="dmg_large" name="dmg_large" value="0">
                    </div>
                    <div class="form-group">
                        <label for="hitmodifier">Hit Modifier</label>
                        <input type="number" id="hitmodifier" name="hitmodifier" value="0">
                    </div>
                    <div class="form-group">
                        <label for="safenchant">Safe Enchant</label>
                        <input type="number" id="safenchant" name="safenchant" value="0">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>👥 Class Restrictions</h3>
                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <input type="checkbox" id="use_royal" name="use_royal" value="1">
                        <label for="use_royal">Royal</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="use_knight" name="use_knight" value="1">
                        <label for="use_knight">Knight</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="use_mage" name="use_mage" value="1">
                        <label for="use_mage">Mage</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="use_elf" name="use_elf" value="1">
                        <label for="use_elf">Elf</label>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="btn-group">
                    <button type="submit" class="admin-btn admin-btn-primary admin-btn-large">💾 Create Weapon</button>
                    <a href="<?php echo SITE_URL; ?>/admin/pages/weapon/weapon_list.php" class="admin-btn admin-btn-secondary admin-btn-large">❌ Cancel</a>
                </div>
            </div>
        </form>
    </div>
</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
