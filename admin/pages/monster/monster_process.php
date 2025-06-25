<?php
require_once dirname(dirname(dirname(__DIR__))) . '/includes/config.php';
require_once dirname(dirname(dirname(__DIR__))) . '/admin/includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: monster_list.php');
    exit;
}

$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'add':
            handleAdd();
            break;
        case 'edit':
            handleEdit();
            break;
        case 'delete':
            handleDelete();
            break;
        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    $errorMsg = urlencode($e->getMessage());
    header("Location: monster_list.php?error=database&msg=$errorMsg");
    exit;
}

function handleAdd() {
    global $pdo;
    
    // Validate required fields
    $required = ['npcid', 'desc_en', 'spriteId', 'impl'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Required field '$field' is missing");
        }
    }
    
    // Check if NPC ID already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM npc WHERE npcid = ?");
    $stmt->execute([$_POST['npcid']]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("NPC ID {$_POST['npcid']} already exists");
    }
    
    // Prepare data
    $data = prepareMonsterData();
    
    // Build SQL
    $fields = array_keys($data);
    $placeholders = ':' . implode(', :', $fields);
    $sql = "INSERT INTO npc (" . implode(', ', $fields) . ") VALUES ($placeholders)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
    
    $monsterName = urlencode($_POST['desc_en']);
    header("Location: monster_list.php?success=added&name=$monsterName");
    exit;
}

function handleEdit() {
    global $pdo;
    
    // Validate required fields
    $required = ['npcid', 'desc_en', 'spriteId', 'impl'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Required field '$field' is missing");
        }
    }
    
    // Check if monster exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM npc WHERE npcid = ?");
    $stmt->execute([$_POST['npcid']]);
    if ($stmt->fetchColumn() == 0) {
        throw new Exception("Monster with ID {$_POST['npcid']} not found");
    }
    
    // Prepare data
    $data = prepareMonsterData();
    $npcid = $data['npcid'];
    unset($data['npcid']); // Remove npcid from update data
    
    // Build SQL
    $fields = array_keys($data);
    $setClause = implode(' = ?, ', $fields) . ' = ?';
    $sql = "UPDATE npc SET $setClause WHERE npcid = ?";
    
    $values = array_values($data);
    $values[] = $npcid;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    
    $monsterName = urlencode($_POST['desc_en']);
    header("Location: monster_list.php?success=updated&name=$monsterName");
    exit;
}

function handleDelete() {
    global $pdo;
    
    $npcid = $_POST['npcid'] ?? '';
    if (empty($npcid)) {
        throw new Exception("Monster ID is required");
    }
    
    // Get monster name for confirmation message
    $stmt = $pdo->prepare("SELECT desc_en FROM npc WHERE npcid = ?");
    $stmt->execute([$npcid]);
    $monsterName = $stmt->fetchColumn();
    
    if (!$monsterName) {
        throw new Exception("Monster not found");
    }
    
    // Delete monster
    $stmt = $pdo->prepare("DELETE FROM npc WHERE npcid = ?");
    $stmt->execute([$npcid]);
    
    if ($stmt->rowCount() == 0) {
        throw new Exception("Failed to delete monster");
    }
    
    $encodedName = urlencode($monsterName);
    header("Location: monster_list.php?success=deleted&name=$encodedName");
    exit;
}

function prepareMonsterData() {
    // Basic Information
    $data = [
        'npcid' => (int)$_POST['npcid'],
        'classId' => (int)($_POST['classId'] ?? 0),
        'desc_en' => trim($_POST['desc_en']),
        'desc_powerbook' => trim($_POST['desc_powerbook'] ?? ''),
        'desc_kr' => trim($_POST['desc_kr'] ?? ''),
        'desc_id' => trim($_POST['desc_id'] ?? ''),
        'note' => trim($_POST['note'] ?? ''),
        'impl' => trim($_POST['impl']),
        'spriteId' => (int)$_POST['spriteId'],
        'family' => trim($_POST['family'] ?? ''),
    ];
    
    // Core Statistics
    $data += [
        'lvl' => (int)($_POST['lvl'] ?? 1),
        'hp' => (int)($_POST['hp'] ?? 100),
        'mp' => (int)($_POST['mp'] ?? 0),
        'ac' => (int)($_POST['ac'] ?? 0),
        'exp' => (int)($_POST['exp'] ?? 0),
        'alignment' => (int)($_POST['alignment'] ?? 0),
    ];
    
    // Attribute Statistics
    $data += [
        'str' => (int)($_POST['str'] ?? 0),
        'con' => (int)($_POST['con'] ?? 0),
        'dex' => (int)($_POST['dex'] ?? 0),
        'wis' => (int)($_POST['wis'] ?? 0),
        'intel' => (int)($_POST['intel'] ?? 0),
    ];
    
    // Combat Properties
    $data += [
        'mr' => (int)($_POST['mr'] ?? 0),
        'ranged' => (int)($_POST['ranged'] ?? 0),
        'damage_reduction' => (int)($_POST['damage_reduction'] ?? 0),
        'atkspeed' => (int)($_POST['atkspeed'] ?? 0),
        'atk_magic_speed' => (int)($_POST['atk_magic_speed'] ?? 0),
        'sub_magic_speed' => (int)($_POST['sub_magic_speed'] ?? 0),
        'undead' => trim($_POST['undead'] ?? 'NONE'),
        'weakAttr' => trim($_POST['weakAttr'] ?? 'NONE'),
        'poison_atk' => trim($_POST['poison_atk'] ?? 'NONE'),
    ];
    
    // Behavioral Settings
    $data += [
        'is_agro' => isset($_POST['is_agro']) ? 'true' : 'false',
        'is_agro_poly' => isset($_POST['is_agro_poly']) ? 'true' : 'false',
        'is_agro_invis' => isset($_POST['is_agro_invis']) ? 'true' : 'false',
        'is_taming' => isset($_POST['is_taming']) ? 'true' : 'false',
        'is_picupitem' => isset($_POST['is_picupitem']) ? 'true' : 'false',
        'is_teleport' => isset($_POST['is_teleport']) ? 'true' : 'false',
        'passispeed' => (int)($_POST['passispeed'] ?? 0),
        'agrofamily' => (int)($_POST['agrofamily'] ?? 0),
        'digestitem' => (int)($_POST['digestitem'] ?? 0),
    ];
    
    // Regeneration Settings
    $data += [
        'hpr' => (int)($_POST['hpr'] ?? 0),
        'hprinterval' => (int)($_POST['hprinterval'] ?? 0),
        'mpr' => (int)($_POST['mpr'] ?? 0),
        'mprinterval' => (int)($_POST['mprinterval'] ?? 0),
    ];
    
    // Advanced Properties
    $data += [
        'big' => isset($_POST['big']) ? 'true' : 'false',
        'is_hard' => isset($_POST['is_hard']) ? 'true' : 'false',
        'is_bossmonster' => isset($_POST['is_bossmonster']) ? 'true' : 'false',
        'can_turnundead' => isset($_POST['can_turnundead']) ? 'true' : 'false',
        'is_bravespeed' => isset($_POST['is_bravespeed']) ? 'true' : 'false',
        'cant_resurrect' => isset($_POST['cant_resurrect']) ? 'true' : 'false',
    ];
    
    // Random Properties
    $data += [
        'randomlevel' => (int)($_POST['randomlevel'] ?? 0),
        'randomhp' => (int)($_POST['randomhp'] ?? 0),
        'randommp' => (int)($_POST['randommp'] ?? 0),
        'randomac' => (int)($_POST['randomac'] ?? 0),
        'randomexp' => (int)($_POST['randomexp'] ?? 0),
        'randomAlign' => (int)($_POST['randomAlign'] ?? 0),
    ];
    
    // Special Properties
    $data += [
        'bowSpritetId' => (int)($_POST['bowSpritetId'] ?? 0),
        'karma' => (int)($_POST['karma'] ?? 0),
        'transform_id' => (int)($_POST['transform_id'] ?? -1),
        'transform_gfxid' => (int)($_POST['transform_gfxid'] ?? 0),
        'light_size' => (int)($_POST['light_size'] ?? 0),
        'is_amount_fixed' => isset($_POST['is_amount_fixed']) ? 'true' : 'false',
        'is_change_head' => isset($_POST['is_change_head']) ? 'true' : 'false',
        'spawnlist_door' => (int)($_POST['spawnlist_door'] ?? 0),
        'count_map' => (int)($_POST['count_map'] ?? 0),
        'isHide' => isset($_POST['isHide']) ? 'true' : 'false',
    ];
    
    // Additional fields for aggro graphics (if provided)
    $data += [
        'agrogfxid1' => (int)($_POST['agrogfxid1'] ?? -1),
        'agrogfxid2' => (int)($_POST['agrogfxid2'] ?? -1),
    ];
    
    return $data;
}
?>
