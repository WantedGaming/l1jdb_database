<?php
require_once 'config.php';

function getRecentUpdates($limit = 5) {
    global $pdo;
    
    try {
        // Try to get real database activity first
        $sql = "SELECT entity_type as category, entity_name as item_name, 
                       CONCAT(UPPER(activity_type), ' - ', COALESCE(details, 'Database update')) as description,
                       timestamp as updated_at
                FROM database_activity 
                ORDER BY timestamp DESC 
                LIMIT :limit";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        
        if (empty($result)) {
            // Fallback to placeholder data
            $result = [
                ['category' => 'weapons', 'item_name' => 'Sword of Light', 'description' => 'Added new legendary weapon', 'updated_at' => date('Y-m-d H:i:s')],
                ['category' => 'armor', 'item_name' => 'Dragon Scale Armor', 'description' => 'Updated defense values', 'updated_at' => date('Y-m-d H:i:s', strtotime('-1 day'))],
                ['category' => 'items', 'item_name' => 'Health Potion', 'description' => 'Modified healing amount', 'updated_at' => date('Y-m-d H:i:s', strtotime('-2 days'))]
            ];
        }
        
        return $result;
    } catch(PDOException $e) {
        // Return placeholder data on error
        return [
            ['category' => 'weapons', 'item_name' => 'Sword of Light', 'description' => 'Added new legendary weapon', 'updated_at' => date('Y-m-d H:i:s')],
            ['category' => 'armor', 'item_name' => 'Dragon Scale Armor', 'description' => 'Updated defense values', 'updated_at' => date('Y-m-d H:i:s', strtotime('-1 day'))],
            ['category' => 'items', 'item_name' => 'Health Potion', 'description' => 'Modified healing amount', 'updated_at' => date('Y-m-d H:i:s', strtotime('-2 days'))]
        ];
    }
}

function isAdmin() {
    return isset($_SESSION['user_id']) && isset($_SESSION['access_level']) && $_SESSION['access_level'] == 1;
}

function requireAuth() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . SITE_URL . '/admin/login.php');
        exit;
    }
}

function requireAdmin() {
    requireAuth();
    if (!isAdmin()) {
        header('Location: ' . SITE_URL . '/admin/access_denied.php');
        exit;
    }
}

function logAdminActivity($action, $table_name, $record_id = null, $details = null) {
    global $pdo;
    
    if (!isset($_SESSION['user_id'])) return;
    
    $sql = "INSERT INTO admin_activity (admin_username, activity_type, description, entity_type, entity_id, ip_address, user_agent, timestamp) 
            VALUES (:admin_username, :activity_type, :description, :entity_type, :entity_id, :ip_address, :user_agent, NOW())";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':admin_username' => $_SESSION['username'] ?? 'unknown',
            ':activity_type' => $action,
            ':description' => $details ?? $action,
            ':entity_type' => $table_name,
            ':entity_id' => $record_id,
            ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
    } catch(PDOException $e) {
        // Log error silently
    }
}

// Weapon normalization functions
function normalizeWeaponType($type) {
    $types = [
        'SWORD' => 'Sword',
        'DAGGER' => 'Dagger',
        'TOHAND_SWORD' => 'Two-Handed Sword',
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
        'TOHAND_BLUNT' => 'Two-Handed Blunt',
        'TOHAND_STAFF' => 'Two-Handed Staff',
        'KEYRINGK' => 'Kiringku',
        'CHAINSWORD' => 'Chain Sword'
    ];
    return $types[$type] ?? $type;
}

function normalizeWeaponMaterial($material) {
    $materials = [
        'NONE(-)' => 'None',
        'LIQUID(액체)' => 'Liquid',
        'WAX(밀랍)' => 'Wax',
        'VEGGY(식물성)' => 'Vegetal',
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
    return $materials[$material] ?? $material;
}

function cleanDescriptionPrefix($desc) {
    return preg_replace('/^\\\\a[HFG]/', '', $desc);
}

// Item normalization function
function normalizeItemType($type) {
    $types = [
        'ARROW' => 'Arrow',
        'WAND' => 'Wand',
        'LIGHT' => 'Light',
        'GEM' => 'Gem',
        'TOTEM' => 'Totem',
        'FIRE_CRACKER' => 'Fire Cracker',
        'POTION' => 'Potion',
        'FOOD' => 'Food',
        'SCROLL' => 'Scroll',
        'QUEST_ITEM' => 'Quest Item',
        'SPELL_BOOK' => 'Spell Book',
        'PET_ITEM' => 'Pet Item',
        'OTHER' => 'Other',
        'MATERIAL' => 'Material',
        'EVENT' => 'Event',
        'STING' => 'Sting',
        'TREASURE_BOX' => 'Treasure Box',
        'MAGIC_DOLL' => 'Magic Doll'
    ];
    return $types[$type] ?? $type;
}

// Armor normalization functions
function normalizeArmorType($type) {
    $types = [
        'NONE' => 'None',
        'HELMET' => 'Helmet',
        'ARMOR' => 'Armor',
        'T_SHIRT' => 'T-Shirt',
        'CLOAK' => 'Cloak',
        'GLOVE' => 'Gloves',
        'BOOTS' => 'Boots',
        'SHIELD' => 'Shield',
        'AMULET' => 'Amulet',
        'RING' => 'Ring',
        'BELT' => 'Belt',
        'RING_2' => 'Ring (2nd)',
        'EARRING' => 'Earring',
        'GARDER' => 'Garter',
        'RON' => 'Ron',
        'PAIR' => 'Pair',
        'SENTENCE' => 'Sentence',
        'SHOULDER' => 'Shoulder',
        'BADGE' => 'Badge',
        'PENDANT' => 'Pendant'
    ];
    return $types[$type] ?? $type;
}

function normalizeArmorMaterial($material) {
    $materials = [
        'NONE(-)' => 'None',
        'LIQUID(액체)' => 'Liquid',
        'WAX(밀랍)' => 'Wax',
        'VEGGY(식물성)' => 'Vegetal',
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
    return $materials[$material] ?? $material;
}

// Grade normalization function
function normalizeGrade($grade) {
    $grades = [
        'ONLY' => 'Only',
        'MYTH' => 'Mythic',
        'LEGEND' => 'Legendary', 
        'HERO' => 'Hero',
        'RARE' => 'Rare',
        'ADVANC' => 'Advanced',
        'NORMAL' => 'Normal'
    ];
    return $grades[strtoupper($grade)] ?? $grade;
}

// Function to display grade only if it's above normal
function displayGrade($grade) {
    $normalizedGrade = normalizeGrade($grade);
    
    // Don't display grade if it's normal or empty
    if (empty($grade) || strtoupper($grade) === 'NORMAL') {
        return '';
    }
    
    // Return the grade with appropriate CSS class
    $cssClass = 'grade-' . strtolower($grade);
    return '<span class="' . $cssClass . '">' . $normalizedGrade . '</span>';
}

// Function to get grade CSS class
function getGradeClass($grade) {
    if (empty($grade)) {
        return 'grade-normal';
    }
    return 'grade-' . strtolower($grade);
}

// Function to get monster image path
function getMonsterImagePath($spriteId) {
    return 'ms' . $spriteId . '.png';
}

// Function to get monsters that drop a specific item
function getMonstersByItemDrop($itemId) {
    global $pdo;
    
    try {
        $sql = "SELECT DISTINCT n.npcid, n.desc_en, n.spriteId, n.lvl, d.chance, d.min, d.max, d.Enchant
                FROM droplist d 
                INNER JOIN npc n ON d.mobId = n.npcid 
                WHERE d.itemId = :itemId 
                AND n.impl IN ('L1Monster', 'L1BlackKnight', 'L1Doppelganger')
                ORDER BY n.lvl ASC, n.desc_en ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':itemId' => $itemId]);
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        return [];
    }
}

// Function to format drop chance percentage
function formatDropChance($chance) {
    // 1000000 = 100%, 500000 = 50%, 100000 = 10%, 10000 = 1%
    $percentage = ($chance / 10000);
    
    if ($percentage >= 1) {
        return number_format($percentage, ($percentage == floor($percentage) ? 0 : 2)) . '%';
    } else {
        return number_format($percentage, 3) . '%';
    }
}
?>