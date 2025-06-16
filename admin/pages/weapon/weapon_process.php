<?php
require_once dirname(dirname(dirname(__DIR__))) . '/includes/config.php';
require_once dirname(dirname(dirname(__DIR__))) . '/includes/header.php';

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || !isAdmin($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login.php');
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
        header('Location: ../weapons/weapon_list.php');
        exit;
}

function handleAddWeapon() {
    global $pdo;
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Prepare SQL statement
        $sql = "INSERT INTO weapon (
            iconId, desc_en, type, material, weight, itemGrade,
            dmg_small, dmg_large, hitmodifier, dmgmodifier,
            safenchant, canbedmg,
            add_str, add_con, add_dex, add_int, add_wis, add_cha,
            add_hp, add_mp, add_sp,
            use_royal, use_knight, use_mage, use_elf, use_darkelf,
            use_dragonknight, use_illusionist, use_warrior,
            use_fencer, use_lancer
        ) VALUES (
            :iconId, :desc_en, :type, :material, :weight, :itemGrade,
            :dmg_small, :dmg_large, :hitmodifier, :dmgmodifier,
            :safenchant, :canbedmg,
            :add_str, :add_con, :add_dex, :add_int, :add_wis, :add_cha,
            :add_hp, :add_mp, :add_sp,
            :use_royal, :use_knight, :use_mage, :use_elf, :use_darkelf,
            :use_dragonknight, :use_illusionist, :use_warrior,
            :use_fencer, :use_lancer
        )";
        
        $stmt = $pdo->prepare($sql);
        
        // Bind parameters
        $params = [
            ':iconId' => $_POST['iconId'],
            ':desc_en' => $_POST['desc_en'],
            ':type' => $_POST['type'],
            ':material' => $_POST['material'],
            ':weight' => $_POST['weight'],
            ':itemGrade' => $_POST['itemGrade'],
            ':dmg_small' => $_POST['dmg_small'],
            ':dmg_large' => $_POST['dmg_large'],
            ':hitmodifier' => $_POST['hitmodifier'],
            ':dmgmodifier' => $_POST['dmgmodifier'],
            ':safenchant' => $_POST['safenchant'],
            ':canbedmg' => $_POST['canbedmg'],
            ':add_str' => $_POST['add_str'],
            ':add_con' => $_POST['add_con'],
            ':add_dex' => $_POST['add_dex'],
            ':add_int' => $_POST['add_int'],
            ':add_wis' => $_POST['add_wis'],
            ':add_cha' => $_POST['add_cha'],
            ':add_hp' => $_POST['add_hp'],
            ':add_mp' => $_POST['add_mp'],
            ':add_sp' => $_POST['add_sp'],
            ':use_royal' => $_POST['use_royal'],
            ':use_knight' => $_POST['use_knight'],
            ':use_mage' => $_POST['use_mage'],
            ':use_elf' => $_POST['use_elf'],
            ':use_darkelf' => $_POST['use_darkelf'],
            ':use_dragonknight' => $_POST['use_dragonknight'],
            ':use_illusionist' => $_POST['use_illusionist'],
            ':use_warrior' => $_POST['use_warrior'],
            ':use_fencer' => $_POST['use_fencer'],
            ':use_lancer' => $_POST['use_lancer']
        ];
        
        $stmt->execute($params);
        
        // Get the new weapon ID
        $weaponId = $pdo->lastInsertId();
        
        // Commit transaction
        $pdo->commit();
        
        // Redirect to the new weapon's detail page
        header('Location: ../weapons/weapon_detail.php?id=' . $weaponId);
        exit;
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        
        // Log error
        error_log('Error adding weapon: ' . $e->getMessage());
        
        // Redirect to add page with error
        header('Location: weapon_add.php?error=1');
        exit;
    }
}

function handleEditWeapon() {
    global $pdo;
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Prepare SQL statement
        $sql = "UPDATE weapon SET
            iconId = :iconId,
            desc_en = :desc_en,
            type = :type,
            material = :material,
            weight = :weight,
            itemGrade = :itemGrade,
            dmg_small = :dmg_small,
            dmg_large = :dmg_large,
            hitmodifier = :hitmodifier,
            dmgmodifier = :dmgmodifier,
            safenchant = :safenchant,
            canbedmg = :canbedmg,
            add_str = :add_str,
            add_con = :add_con,
            add_dex = :add_dex,
            add_int = :add_int,
            add_wis = :add_wis,
            add_cha = :add_cha,
            add_hp = :add_hp,
            add_mp = :add_mp,
            add_sp = :add_sp,
            use_royal = :use_royal,
            use_knight = :use_knight,
            use_mage = :use_mage,
            use_elf = :use_elf,
            use_darkelf = :use_darkelf,
            use_dragonknight = :use_dragonknight,
            use_illusionist = :use_illusionist,
            use_warrior = :use_warrior,
            use_fencer = :use_fencer,
            use_lancer = :use_lancer
            WHERE item_id = :item_id";
        
        $stmt = $pdo->prepare($sql);
        
        // Bind parameters
        $params = [
            ':item_id' => $_POST['item_id'],
            ':iconId' => $_POST['iconId'],
            ':desc_en' => $_POST['desc_en'],
            ':type' => $_POST['type'],
            ':material' => $_POST['material'],
            ':weight' => $_POST['weight'],
            ':itemGrade' => $_POST['itemGrade'],
            ':dmg_small' => $_POST['dmg_small'],
            ':dmg_large' => $_POST['dmg_large'],
            ':hitmodifier' => $_POST['hitmodifier'],
            ':dmgmodifier' => $_POST['dmgmodifier'],
            ':safenchant' => $_POST['safenchant'],
            ':canbedmg' => $_POST['canbedmg'],
            ':add_str' => $_POST['add_str'],
            ':add_con' => $_POST['add_con'],
            ':add_dex' => $_POST['add_dex'],
            ':add_int' => $_POST['add_int'],
            ':add_wis' => $_POST['add_wis'],
            ':add_cha' => $_POST['add_cha'],
            ':add_hp' => $_POST['add_hp'],
            ':add_mp' => $_POST['add_mp'],
            ':add_sp' => $_POST['add_sp'],
            ':use_royal' => $_POST['use_royal'],
            ':use_knight' => $_POST['use_knight'],
            ':use_mage' => $_POST['use_mage'],
            ':use_elf' => $_POST['use_elf'],
            ':use_darkelf' => $_POST['use_darkelf'],
            ':use_dragonknight' => $_POST['use_dragonknight'],
            ':use_illusionist' => $_POST['use_illusionist'],
            ':use_warrior' => $_POST['use_warrior'],
            ':use_fencer' => $_POST['use_fencer'],
            ':use_lancer' => $_POST['use_lancer']
        ];
        
        $stmt->execute($params);
        
        // Commit transaction
        $pdo->commit();
        
        // Redirect to the weapon's detail page
        header('Location: ../weapons/weapon_detail.php?id=' . $_POST['item_id']);
        exit;
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        
        // Log error
        error_log('Error editing weapon: ' . $e->getMessage());
        
        // Redirect to edit page with error
        header('Location: weapon_edit.php?id=' . $_POST['item_id'] . '&error=1');
        exit;
    }
}

function handleDeleteWeapon() {
    global $pdo;
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Prepare SQL statement
        $sql = "DELETE FROM weapon WHERE item_id = :item_id";
        $stmt = $pdo->prepare($sql);
        
        // Execute with weapon ID
        $stmt->execute([':item_id' => $_POST['item_id']]);
        
        // Commit transaction
        $pdo->commit();
        
        // Redirect to weapon list
        header('Location: ../weapons/weapon_list.php');
        exit;
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        
        // Log error
        error_log('Error deleting weapon: ' . $e->getMessage());
        
        // Redirect to delete page with error
        header('Location: weapon_delete.php?id=' . $_POST['item_id'] . '&error=1');
        exit;
    }
} 