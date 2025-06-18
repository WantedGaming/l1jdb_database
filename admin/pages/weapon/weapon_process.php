<?php
require_once dirname(dirname(dirname(__DIR__))) . '/includes/config.php';
session_start();

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: ../../login.php');
    exit;
}

// Get action from POST data
$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {
    case 'add':
        handleAddWeapon();
        break;
    case 'edit':
        handleEditWeapon();
        break;
    case 'delete':
        handleDeleteWeapon();
        break;
    default:
        header('Location: weapon_list.php');
        exit;
}

function handleAddWeapon() {
    global $pdo;
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Prepare comprehensive SQL statement with all fields
        $sql = "INSERT INTO weapon (
            item_name_id, desc_kr, desc_en, desc_powerbook, note, desc_id,
            itemGrade, type, material, weight, iconId, spriteId,
            dmg_small, dmg_large, safenchant,
            use_royal, use_knight, use_mage, use_elf, use_darkelf,
            use_dragonknight, use_illusionist, use_warrior, use_fencer, use_lancer,
            hitmodifier, dmgmodifier,
            add_str, add_con, add_dex, add_int, add_wis, add_cha,
            add_hp, add_mp, add_hpr, add_mpr, add_sp, m_def,
            haste_item, double_dmg_chance, magicdmgmodifier,
            canbedmg, min_lvl, max_lvl, bless, trade, retrieve,
            specialretrieve, cant_delete, cant_sell, max_use_time,
            regist_skill, regist_spirit, regist_dragon, regist_fear, regist_all,
            hitup_skill, hitup_spirit, hitup_dragon, hitup_fear, hitup_all, hitup_magic,
            damage_reduction, MagicDamageReduction, reductionEgnor, reductionPercent,
            PVPDamage, PVPDamageReduction, PVPDamageReductionPercent,
            PVPMagicDamageReduction, PVPReductionEgnor, PVPMagicDamageReductionEgnor,
            abnormalStatusDamageReduction, abnormalStatusPVPDamageReduction, PVPDamagePercent,
            expBonus, rest_exp_reduce_efficiency,
            shortCritical, longCritical, magicCritical,
            addDg, addEr, addMe, poisonRegist, imunEgnor,
            stunDuration, tripleArrowStun, strangeTimeIncrease, strangeTimeDecrease,
            potionRegist, potionPercent, potionValue,
            hprAbsol32Second, mprAbsol64Second, mprAbsol16Second,
            hpPotionDelayDecrease, hpPotionCriticalProb, increaseArmorSkillProb,
            attackSpeedDelayRate, moveSpeedDelayRate, Magic_name
        ) VALUES (
            :item_name_id, :desc_kr, :desc_en, :desc_powerbook, :note, :desc_id,
            :itemGrade, :type, :material, :weight, :iconId, :spriteId,
            :dmg_small, :dmg_large, :safenchant,
            :use_royal, :use_knight, :use_mage, :use_elf, :use_darkelf,
            :use_dragonknight, :use_illusionist, :use_warrior, :use_fencer, :use_lancer,
            :hitmodifier, :dmgmodifier,
            :add_str, :add_con, :add_dex, :add_int, :add_wis, :add_cha,
            :add_hp, :add_mp, :add_hpr, :add_mpr, :add_sp, :m_def,
            :haste_item, :double_dmg_chance, :magicdmgmodifier,
            :canbedmg, :min_lvl, :max_lvl, :bless, :trade, :retrieve,
            :specialretrieve, :cant_delete, :cant_sell, :max_use_time,
            :regist_skill, :regist_spirit, :regist_dragon, :regist_fear, :regist_all,
            :hitup_skill, :hitup_spirit, :hitup_dragon, :hitup_fear, :hitup_all, :hitup_magic,
            :damage_reduction, :MagicDamageReduction, :reductionEgnor, :reductionPercent,
            :PVPDamage, :PVPDamageReduction, :PVPDamageReductionPercent,
            :PVPMagicDamageReduction, :PVPReductionEgnor, :PVPMagicDamageReductionEgnor,
            :abnormalStatusDamageReduction, :abnormalStatusPVPDamageReduction, :PVPDamagePercent,
            :expBonus, :rest_exp_reduce_efficiency,
            :shortCritical, :longCritical, :magicCritical,
            :addDg, :addEr, :addMe, :poisonRegist, :imunEgnor,
            :stunDuration, :tripleArrowStun, :strangeTimeIncrease, :strangeTimeDecrease,
            :potionRegist, :potionPercent, :potionValue,
            :hprAbsol32Second, :mprAbsol64Second, :mprAbsol16Second,
            :hpPotionDelayDecrease, :hpPotionCriticalProb, :increaseArmorSkillProb,
            :attackSpeedDelayRate, :moveSpeedDelayRate, :Magic_name
        )";
        
        $stmt = $pdo->prepare($sql);
        
        // Prepare parameters with proper data types and defaults
        $params = [
            // Basic Information
            ':item_name_id' => (int)($_POST['item_name_id'] ?? 0),
            ':desc_kr' => $_POST['desc_kr'] ?? '',
            ':desc_en' => $_POST['desc_en'] ?? '',
            ':desc_powerbook' => $_POST['desc_powerbook'] ?? '',
            ':note' => $_POST['note'] ?? '',
            ':desc_id' => $_POST['desc_id'] ?? '',
            ':itemGrade' => $_POST['itemGrade'] ?? 'NORMAL',
            ':type' => $_POST['type'] ?? 'SWORD',
            ':material' => $_POST['material'] ?? 'IRON(철)',
            ':weight' => (int)($_POST['weight'] ?? 1),
            ':iconId' => (int)($_POST['iconId'] ?? 0),
            ':spriteId' => (int)($_POST['spriteId'] ?? 0),
            
            // Combat Stats
            ':dmg_small' => (int)($_POST['dmg_small'] ?? 1),
            ':dmg_large' => (int)($_POST['dmg_large'] ?? 1),
            ':safenchant' => (int)($_POST['safenchant'] ?? 0),
            ':hitmodifier' => (int)($_POST['hitmodifier'] ?? 0),
            ':dmgmodifier' => (int)($_POST['dmgmodifier'] ?? 0),
            ':magicdmgmodifier' => (int)($_POST['magicdmgmodifier'] ?? 0),
            ':haste_item' => (int)($_POST['haste_item'] ?? 0),
            ':double_dmg_chance' => (int)($_POST['double_dmg_chance'] ?? 0),
            ':canbedmg' => isset($_POST['canbedmg']) ? 1 : 0,
            ':min_lvl' => (int)($_POST['min_lvl'] ?? 0),
            ':max_lvl' => (int)($_POST['max_lvl'] ?? 0),
            ':max_use_time' => (int)($_POST['max_use_time'] ?? 0),
            
            // Class Usage
            ':use_royal' => isset($_POST['use_royal']) ? 1 : 0,
            ':use_knight' => isset($_POST['use_knight']) ? 1 : 0,
            ':use_mage' => isset($_POST['use_mage']) ? 1 : 0,
            ':use_elf' => isset($_POST['use_elf']) ? 1 : 0,
            ':use_darkelf' => isset($_POST['use_darkelf']) ? 1 : 0,
            ':use_dragonknight' => isset($_POST['use_dragonknight']) ? 1 : 0,
            ':use_illusionist' => isset($_POST['use_illusionist']) ? 1 : 0,
            ':use_warrior' => isset($_POST['use_warrior']) ? 1 : 0,
            ':use_fencer' => isset($_POST['use_fencer']) ? 1 : 0,
            ':use_lancer' => isset($_POST['use_lancer']) ? 1 : 0,
            
            // Stat Bonuses
            ':add_str' => (int)($_POST['add_str'] ?? 0),
            ':add_con' => (int)($_POST['add_con'] ?? 0),
            ':add_dex' => (int)($_POST['add_dex'] ?? 0),
            ':add_int' => (int)($_POST['add_int'] ?? 0),
            ':add_wis' => (int)($_POST['add_wis'] ?? 0),
            ':add_cha' => (int)($_POST['add_cha'] ?? 0),
            ':add_hp' => (int)($_POST['add_hp'] ?? 0),
            ':add_mp' => (int)($_POST['add_mp'] ?? 0),
            ':add_hpr' => (int)($_POST['add_hpr'] ?? 0),
            ':add_mpr' => (int)($_POST['add_mpr'] ?? 0),
            ':add_sp' => (int)($_POST['add_sp'] ?? 0),
            ':m_def' => (int)($_POST['m_def'] ?? 0),
            
            // Resistances
            ':regist_skill' => (int)($_POST['regist_skill'] ?? 0),
            ':regist_spirit' => (int)($_POST['regist_spirit'] ?? 0),
            ':regist_dragon' => (int)($_POST['regist_dragon'] ?? 0),
            ':regist_fear' => (int)($_POST['regist_fear'] ?? 0),
            ':regist_all' => (int)($_POST['regist_all'] ?? 0),
            
            // Hit Bonuses
            ':hitup_skill' => (int)($_POST['hitup_skill'] ?? 0),
            ':hitup_spirit' => (int)($_POST['hitup_spirit'] ?? 0),
            ':hitup_dragon' => (int)($_POST['hitup_dragon'] ?? 0),
            ':hitup_fear' => (int)($_POST['hitup_fear'] ?? 0),
            ':hitup_all' => (int)($_POST['hitup_all'] ?? 0),
            ':hitup_magic' => (int)($_POST['hitup_magic'] ?? 0),
            
            // Damage Reduction
            ':damage_reduction' => (int)($_POST['damage_reduction'] ?? 0),
            ':MagicDamageReduction' => (int)($_POST['MagicDamageReduction'] ?? 0),
            ':reductionEgnor' => (int)($_POST['reductionEgnor'] ?? 0),
            ':reductionPercent' => (int)($_POST['reductionPercent'] ?? 0),
            ':abnormalStatusDamageReduction' => (int)($_POST['abnormalStatusDamageReduction'] ?? 0),
            ':abnormalStatusPVPDamageReduction' => (int)($_POST['abnormalStatusPVPDamageReduction'] ?? 0),
            
            // PVP Settings
            ':PVPDamage' => (int)($_POST['PVPDamage'] ?? 0),
            ':PVPDamageReduction' => (int)($_POST['PVPDamageReduction'] ?? 0),
            ':PVPDamageReductionPercent' => (int)($_POST['PVPDamageReductionPercent'] ?? 0),
            ':PVPMagicDamageReduction' => (int)($_POST['PVPMagicDamageReduction'] ?? 0),
            ':PVPReductionEgnor' => (int)($_POST['PVPReductionEgnor'] ?? 0),
            ':PVPMagicDamageReductionEgnor' => (int)($_POST['PVPMagicDamageReductionEgnor'] ?? 0),
            ':PVPDamagePercent' => (int)($_POST['PVPDamagePercent'] ?? 0),
            
            // Special Effects
            ':expBonus' => (int)($_POST['expBonus'] ?? 0),
            ':rest_exp_reduce_efficiency' => (int)($_POST['rest_exp_reduce_efficiency'] ?? 0),
            ':shortCritical' => (int)($_POST['shortCritical'] ?? 0),
            ':longCritical' => (int)($_POST['longCritical'] ?? 0),
            ':magicCritical' => (int)($_POST['magicCritical'] ?? 0),
            ':addDg' => (int)($_POST['addDg'] ?? 0),
            ':addEr' => (int)($_POST['addEr'] ?? 0),
            ':addMe' => (int)($_POST['addMe'] ?? 0),
            ':poisonRegist' => isset($_POST['poisonRegist']) ? 'true' : 'false',
            ':imunEgnor' => (int)($_POST['imunEgnor'] ?? 0),
            ':stunDuration' => (int)($_POST['stunDuration'] ?? 0),
            ':tripleArrowStun' => (int)($_POST['tripleArrowStun'] ?? 0),
            ':strangeTimeIncrease' => (int)($_POST['strangeTimeIncrease'] ?? 0),
            ':strangeTimeDecrease' => (int)($_POST['strangeTimeDecrease'] ?? 0),
            
            // Potion Effects
            ':potionRegist' => (int)($_POST['potionRegist'] ?? 0),
            ':potionPercent' => (int)($_POST['potionPercent'] ?? 0),
            ':potionValue' => (int)($_POST['potionValue'] ?? 0),
            ':hprAbsol32Second' => (int)($_POST['hprAbsol32Second'] ?? 0),
            ':mprAbsol64Second' => (int)($_POST['mprAbsol64Second'] ?? 0),
            ':mprAbsol16Second' => (int)($_POST['mprAbsol16Second'] ?? 0),
            ':hpPotionDelayDecrease' => (int)($_POST['hpPotionDelayDecrease'] ?? 0),
            ':hpPotionCriticalProb' => (int)($_POST['hpPotionCriticalProb'] ?? 0),
            
            // Advanced Properties
            ':bless' => isset($_POST['bless']) ? 1 : 0,
            ':trade' => isset($_POST['trade']) ? 1 : 0,
            ':retrieve' => isset($_POST['retrieve']) ? 1 : 0,
            ':specialretrieve' => isset($_POST['specialretrieve']) ? 1 : 0,
            ':cant_delete' => isset($_POST['cant_delete']) ? 1 : 0,
            ':cant_sell' => isset($_POST['cant_sell']) ? 1 : 0,
            ':increaseArmorSkillProb' => (int)($_POST['increaseArmorSkillProb'] ?? 0),
            ':attackSpeedDelayRate' => (int)($_POST['attackSpeedDelayRate'] ?? 0),
            ':moveSpeedDelayRate' => (int)($_POST['moveSpeedDelayRate'] ?? 0),
            ':Magic_name' => $_POST['Magic_name'] ?? null
        ];
        
        $stmt->execute($params);
        
        // Get the new weapon ID
        $weaponId = $pdo->lastInsertId();
        
        // Commit transaction
        $pdo->commit();
        
        // Redirect to the new weapon's detail page with success message
        header('Location: weapon_list.php?success=added&id=' . $weaponId);
        exit;
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        
        // Log error
        error_log('Error adding weapon: ' . $e->getMessage());
        
        // Redirect to add page with error
        header('Location: weapon_add.php?error=database&msg=' . urlencode($e->getMessage()));
        exit;
    }
}

function handleEditWeapon() {
    global $pdo;
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Get weapon ID
        $weaponId = (int)$_POST['item_id'];
        
        if ($weaponId <= 0) {
            throw new Exception('Invalid weapon ID');
        }
        
        // Prepare comprehensive UPDATE statement
        $sql = "UPDATE weapon SET
            item_name_id = :item_name_id,
            desc_kr = :desc_kr,
            desc_en = :desc_en,
            desc_powerbook = :desc_powerbook,
            note = :note,
            desc_id = :desc_id,
            itemGrade = :itemGrade,
            type = :type,
            material = :material,
            weight = :weight,
            iconId = :iconId,
            spriteId = :spriteId,
            dmg_small = :dmg_small,
            dmg_large = :dmg_large,
            safenchant = :safenchant,
            use_royal = :use_royal,
            use_knight = :use_knight,
            use_mage = :use_mage,
            use_elf = :use_elf,
            use_darkelf = :use_darkelf,
            use_dragonknight = :use_dragonknight,
            use_illusionist = :use_illusionist,
            use_warrior = :use_warrior,
            use_fencer = :use_fencer,
            use_lancer = :use_lancer,
            hitmodifier = :hitmodifier,
            dmgmodifier = :dmgmodifier,
            add_str = :add_str,
            add_con = :add_con,
            add_dex = :add_dex,
            add_int = :add_int,
            add_wis = :add_wis,
            add_cha = :add_cha,
            add_hp = :add_hp,
            add_mp = :add_mp,
            add_hpr = :add_hpr,
            add_mpr = :add_mpr,
            add_sp = :add_sp,
            m_def = :m_def,
            haste_item = :haste_item,
            double_dmg_chance = :double_dmg_chance,
            magicdmgmodifier = :magicdmgmodifier,
            canbedmg = :canbedmg,
            min_lvl = :min_lvl,
            max_lvl = :max_lvl,
            bless = :bless,
            trade = :trade,
            retrieve = :retrieve,
            specialretrieve = :specialretrieve,
            cant_delete = :cant_delete,
            cant_sell = :cant_sell,
            max_use_time = :max_use_time,
            regist_skill = :regist_skill,
            regist_spirit = :regist_spirit,
            regist_dragon = :regist_dragon,
            regist_fear = :regist_fear,
            regist_all = :regist_all,
            hitup_skill = :hitup_skill,
            hitup_spirit = :hitup_spirit,
            hitup_dragon = :hitup_dragon,
            hitup_fear = :hitup_fear,
            hitup_all = :hitup_all,
            hitup_magic = :hitup_magic,
            damage_reduction = :damage_reduction,
            MagicDamageReduction = :MagicDamageReduction,
            reductionEgnor = :reductionEgnor,
            reductionPercent = :reductionPercent,
            PVPDamage = :PVPDamage,
            PVPDamageReduction = :PVPDamageReduction,
            PVPDamageReductionPercent = :PVPDamageReductionPercent,
            PVPMagicDamageReduction = :PVPMagicDamageReduction,
            PVPReductionEgnor = :PVPReductionEgnor,
            PVPMagicDamageReductionEgnor = :PVPMagicDamageReductionEgnor,
            abnormalStatusDamageReduction = :abnormalStatusDamageReduction,
            abnormalStatusPVPDamageReduction = :abnormalStatusPVPDamageReduction,
            PVPDamagePercent = :PVPDamagePercent,
            expBonus = :expBonus,
            rest_exp_reduce_efficiency = :rest_exp_reduce_efficiency,
            shortCritical = :shortCritical,
            longCritical = :longCritical,
            magicCritical = :magicCritical,
            addDg = :addDg,
            addEr = :addEr,
            addMe = :addMe,
            poisonRegist = :poisonRegist,
            imunEgnor = :imunEgnor,
            stunDuration = :stunDuration,
            tripleArrowStun = :tripleArrowStun,
            strangeTimeIncrease = :strangeTimeIncrease,
            strangeTimeDecrease = :strangeTimeDecrease,
            potionRegist = :potionRegist,
            potionPercent = :potionPercent,
            potionValue = :potionValue,
            hprAbsol32Second = :hprAbsol32Second,
            mprAbsol64Second = :mprAbsol64Second,
            mprAbsol16Second = :mprAbsol16Second,
            hpPotionDelayDecrease = :hpPotionDelayDecrease,
            hpPotionCriticalProb = :hpPotionCriticalProb,
            increaseArmorSkillProb = :increaseArmorSkillProb,
            attackSpeedDelayRate = :attackSpeedDelayRate,
            moveSpeedDelayRate = :moveSpeedDelayRate,
            Magic_name = :Magic_name
            WHERE item_id = :item_id";
        
        $stmt = $pdo->prepare($sql);
        
        // Use the same parameter preparation as in add function, plus item_id
        $params = [
            ':item_id' => $weaponId,
            // Basic Information
            ':item_name_id' => (int)($_POST['item_name_id'] ?? 0),
            ':desc_kr' => $_POST['desc_kr'] ?? '',
            ':desc_en' => $_POST['desc_en'] ?? '',
            ':desc_powerbook' => $_POST['desc_powerbook'] ?? '',
            ':note' => $_POST['note'] ?? '',
            ':desc_id' => $_POST['desc_id'] ?? '',
            ':itemGrade' => $_POST['itemGrade'] ?? 'NORMAL',
            ':type' => $_POST['type'] ?? 'SWORD',
            ':material' => $_POST['material'] ?? 'IRON(철)',
            ':weight' => (int)($_POST['weight'] ?? 1),
            ':iconId' => (int)($_POST['iconId'] ?? 0),
            ':spriteId' => (int)($_POST['spriteId'] ?? 0),
            
            // Combat Stats
            ':dmg_small' => (int)($_POST['dmg_small'] ?? 1),
            ':dmg_large' => (int)($_POST['dmg_large'] ?? 1),
            ':safenchant' => (int)($_POST['safenchant'] ?? 0),
            ':hitmodifier' => (int)($_POST['hitmodifier'] ?? 0),
            ':dmgmodifier' => (int)($_POST['dmgmodifier'] ?? 0),
            ':magicdmgmodifier' => (int)($_POST['magicdmgmodifier'] ?? 0),
            ':haste_item' => (int)($_POST['haste_item'] ?? 0),
            ':double_dmg_chance' => (int)($_POST['double_dmg_chance'] ?? 0),
            ':canbedmg' => isset($_POST['canbedmg']) ? 1 : 0,
            ':min_lvl' => (int)($_POST['min_lvl'] ?? 0),
            ':max_lvl' => (int)($_POST['max_lvl'] ?? 0),
            ':max_use_time' => (int)($_POST['max_use_time'] ?? 0),
            
            // Class Usage
            ':use_royal' => isset($_POST['use_royal']) ? 1 : 0,
            ':use_knight' => isset($_POST['use_knight']) ? 1 : 0,
            ':use_mage' => isset($_POST['use_mage']) ? 1 : 0,
            ':use_elf' => isset($_POST['use_elf']) ? 1 : 0,
            ':use_darkelf' => isset($_POST['use_darkelf']) ? 1 : 0,
            ':use_dragonknight' => isset($_POST['use_dragonknight']) ? 1 : 0,
            ':use_illusionist' => isset($_POST['use_illusionist']) ? 1 : 0,
            ':use_warrior' => isset($_POST['use_warrior']) ? 1 : 0,
            ':use_fencer' => isset($_POST['use_fencer']) ? 1 : 0,
            ':use_lancer' => isset($_POST['use_lancer']) ? 1 : 0,
            
            // Stat Bonuses
            ':add_str' => (int)($_POST['add_str'] ?? 0),
            ':add_con' => (int)($_POST['add_con'] ?? 0),
            ':add_dex' => (int)($_POST['add_dex'] ?? 0),
            ':add_int' => (int)($_POST['add_int'] ?? 0),
            ':add_wis' => (int)($_POST['add_wis'] ?? 0),
            ':add_cha' => (int)($_POST['add_cha'] ?? 0),
            ':add_hp' => (int)($_POST['add_hp'] ?? 0),
            ':add_mp' => (int)($_POST['add_mp'] ?? 0),
            ':add_hpr' => (int)($_POST['add_hpr'] ?? 0),
            ':add_mpr' => (int)($_POST['add_mpr'] ?? 0),
            ':add_sp' => (int)($_POST['add_sp'] ?? 0),
            ':m_def' => (int)($_POST['m_def'] ?? 0),
            
            // Resistances
            ':regist_skill' => (int)($_POST['regist_skill'] ?? 0),
            ':regist_spirit' => (int)($_POST['regist_spirit'] ?? 0),
            ':regist_dragon' => (int)($_POST['regist_dragon'] ?? 0),
            ':regist_fear' => (int)($_POST['regist_fear'] ?? 0),
            ':regist_all' => (int)($_POST['regist_all'] ?? 0),
            
            // Hit Bonuses
            ':hitup_skill' => (int)($_POST['hitup_skill'] ?? 0),
            ':hitup_spirit' => (int)($_POST['hitup_spirit'] ?? 0),
            ':hitup_dragon' => (int)($_POST['hitup_dragon'] ?? 0),
            ':hitup_fear' => (int)($_POST['hitup_fear'] ?? 0),
            ':hitup_all' => (int)($_POST['hitup_all'] ?? 0),
            ':hitup_magic' => (int)($_POST['hitup_magic'] ?? 0),
            
            // Damage Reduction
            ':damage_reduction' => (int)($_POST['damage_reduction'] ?? 0),
            ':MagicDamageReduction' => (int)($_POST['MagicDamageReduction'] ?? 0),
            ':reductionEgnor' => (int)($_POST['reductionEgnor'] ?? 0),
            ':reductionPercent' => (int)($_POST['reductionPercent'] ?? 0),
            ':abnormalStatusDamageReduction' => (int)($_POST['abnormalStatusDamageReduction'] ?? 0),
            ':abnormalStatusPVPDamageReduction' => (int)($_POST['abnormalStatusPVPDamageReduction'] ?? 0),
            
            // PVP Settings
            ':PVPDamage' => (int)($_POST['PVPDamage'] ?? 0),
            ':PVPDamageReduction' => (int)($_POST['PVPDamageReduction'] ?? 0),
            ':PVPDamageReductionPercent' => (int)($_POST['PVPDamageReductionPercent'] ?? 0),
            ':PVPMagicDamageReduction' => (int)($_POST['PVPMagicDamageReduction'] ?? 0),
            ':PVPReductionEgnor' => (int)($_POST['PVPReductionEgnor'] ?? 0),
            ':PVPMagicDamageReductionEgnor' => (int)($_POST['PVPMagicDamageReductionEgnor'] ?? 0),
            ':PVPDamagePercent' => (int)($_POST['PVPDamagePercent'] ?? 0),
            
            // Special Effects
            ':expBonus' => (int)($_POST['expBonus'] ?? 0),
            ':rest_exp_reduce_efficiency' => (int)($_POST['rest_exp_reduce_efficiency'] ?? 0),
            ':shortCritical' => (int)($_POST['shortCritical'] ?? 0),
            ':longCritical' => (int)($_POST['longCritical'] ?? 0),
            ':magicCritical' => (int)($_POST['magicCritical'] ?? 0),
            ':addDg' => (int)($_POST['addDg'] ?? 0),
            ':addEr' => (int)($_POST['addEr'] ?? 0),
            ':addMe' => (int)($_POST['addMe'] ?? 0),
            ':poisonRegist' => isset($_POST['poisonRegist']) ? 'true' : 'false',
            ':imunEgnor' => (int)($_POST['imunEgnor'] ?? 0),
            ':stunDuration' => (int)($_POST['stunDuration'] ?? 0),
            ':tripleArrowStun' => (int)($_POST['tripleArrowStun'] ?? 0),
            ':strangeTimeIncrease' => (int)($_POST['strangeTimeIncrease'] ?? 0),
            ':strangeTimeDecrease' => (int)($_POST['strangeTimeDecrease'] ?? 0),
            
            // Potion Effects
            ':potionRegist' => (int)($_POST['potionRegist'] ?? 0),
            ':potionPercent' => (int)($_POST['potionPercent'] ?? 0),
            ':potionValue' => (int)($_POST['potionValue'] ?? 0),
            ':hprAbsol32Second' => (int)($_POST['hprAbsol32Second'] ?? 0),
            ':mprAbsol64Second' => (int)($_POST['mprAbsol64Second'] ?? 0),
            ':mprAbsol16Second' => (int)($_POST['mprAbsol16Second'] ?? 0),
            ':hpPotionDelayDecrease' => (int)($_POST['hpPotionDelayDecrease'] ?? 0),
            ':hpPotionCriticalProb' => (int)($_POST['hpPotionCriticalProb'] ?? 0),
            
            // Advanced Properties
            ':bless' => isset($_POST['bless']) ? 1 : 0,
            ':trade' => isset($_POST['trade']) ? 1 : 0,
            ':retrieve' => isset($_POST['retrieve']) ? 1 : 0,
            ':specialretrieve' => isset($_POST['specialretrieve']) ? 1 : 0,
            ':cant_delete' => isset($_POST['cant_delete']) ? 1 : 0,
            ':cant_sell' => isset($_POST['cant_sell']) ? 1 : 0,
            ':increaseArmorSkillProb' => (int)($_POST['increaseArmorSkillProb'] ?? 0),
            ':attackSpeedDelayRate' => (int)($_POST['attackSpeedDelayRate'] ?? 0),
            ':moveSpeedDelayRate' => (int)($_POST['moveSpeedDelayRate'] ?? 0),
            ':Magic_name' => $_POST['Magic_name'] ?? null
        ];
        
        $stmt->execute($params);
        
        // Commit transaction
        $pdo->commit();
        
        // Redirect to the weapon's detail page with success message
        header('Location: weapon_list.php?success=updated&id=' . $weaponId);
        exit;
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        
        // Log error
        error_log('Error editing weapon: ' . $e->getMessage());
        
        // Redirect to edit page with error
        header('Location: weapon_edit.php?id=' . ($_POST['item_id'] ?? 0) . '&error=database&msg=' . urlencode($e->getMessage()));
        exit;
    }
}

function handleDeleteWeapon() {
    global $pdo;
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Get weapon ID
        $weaponId = (int)($_POST['item_id'] ?? $_GET['id'] ?? 0);
        
        if ($weaponId <= 0) {
            throw new Exception('Invalid weapon ID');
        }
        
        // Check if weapon exists
        $checkSql = "SELECT desc_en FROM weapon WHERE item_id = :item_id";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([':item_id' => $weaponId]);
        $weapon = $checkStmt->fetch();
        
        if (!$weapon) {
            throw new Exception('Weapon not found');
        }
        
        // Delete weapon
        $sql = "DELETE FROM weapon WHERE item_id = :item_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':item_id' => $weaponId]);
        
        // Commit transaction
        $pdo->commit();
        
        // Redirect to weapon list with success message
        header('Location: weapon_list.php?success=deleted&name=' . urlencode($weapon['desc_en']));
        exit;
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        
        // Log error
        error_log('Error deleting weapon: ' . $e->getMessage());
        
        // Redirect to list with error
        header('Location: weapon_list.php?error=delete&msg=' . urlencode($e->getMessage()));
        exit;
    }
}
?>
