<?php
/**
 * Shared utility functions - consolidated from duplicates
 * Centralized functions for material normalization, validation, etc.
 */

// Material mappings consolidated from all duplicate functions
const MATERIAL_MAPPINGS = [
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

/**
 * Unified material normalization function
 * Replaces: normalizeWeaponMaterial, normalizeItemMaterial, normalizeArmorMaterial
 */
function normalizeMaterial($material) {
    return MATERIAL_MAPPINGS[$material] ?? $material;
}

/**
 * Unified input sanitization function
 * Combines functionality from sanitizeInput and escape functions
 */
function sanitizeInput($input, $connection = null) {
    if ($connection && method_exists($connection, 'real_escape_string')) {
        // Use MySQLi escaping if connection provided
        return htmlspecialchars(trim($connection->real_escape_string($input)), ENT_QUOTES, 'UTF-8');
    }
    
    // Standard sanitization
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Enhanced numeric validation function
 */
function validateNumeric($input, $fieldName, $min = null, $max = null) {
    if (!is_numeric($input)) {
        return "$fieldName must be a number.";
    }
    
    $input = (float)$input;
    
    if ($min !== null && $input < $min) {
        return "$fieldName must be at least $min.";
    }
    
    if ($max !== null && $input > $max) {
        return "$fieldName must not exceed $max.";
    }
    
    return null; // No error
}

/**
 * Centralized database connection function
 * Standardizes on PDO with fallback options
 */
function getDatabaseConnection($host = 'localhost', $dbname = 'l1j_remastered', $username = 'root', $password = '') {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    return $pdo;
}

/**
 * Safe database query execution with error handling
 */
function executeQuery($query, $params = [], $connection = null) {
    if ($connection === null) {
        $connection = getDatabaseConnection();
    }
    
    try {
        $stmt = $connection->prepare($query);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Database query error: " . $e->getMessage());
        throw new Exception("Database query failed");
    }
}

/**
 * Get single value from database query
 */
function getSingleValue($query, $params = [], $connection = null) {
    $stmt = executeQuery($query, $params, $connection);
    $result = $stmt->fetch();
    return $result ? array_values($result)[0] : null;
}

/**
 * Validate table names against allowlist
 */
function isValidTable($table) {
    $allowed_tables = [
        'weapon', 'armor', 'etcitem', 'npc', 'droplist',
        'spawnlist', 'spawnlist_boss', 'spawnlist_clandungeon',
        'spawnlist_indun', 'spawnlist_other', 'spawnlist_ruun',
        'spawnlist_ub', 'spawnlist_unicorntemple', 'spawnlist_worldwar',
        'mapids', 'database_activity', 'admin_activity'
    ];
    
    return in_array($table, $allowed_tables);
}

/**
 * Clean description prefixes from item names
 */
function cleanDescriptionPrefix($desc) {
    return preg_replace('/^\\\\a[HFGEJ]/', '', $desc);
}

/**
 * Format drop chance percentage
 */
function formatDropChance($chance) {
    // 1000000 = 100%, 500000 = 50%, 100000 = 10%, 10000 = 1%
    $percentage = ($chance / 10000);
    
    if ($percentage >= 1) {
        return number_format($percentage, ($percentage == floor($percentage) ? 0 : 2)) . '%';
    } else {
        return number_format($percentage, 3) . '%';
    }
}

/**
 * Grade normalization with unified mappings
 */
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

/**
 * Display grade only if it's above normal
 */
function displayGrade($grade) {
    $normalizedGrade = normalizeGrade($grade);
    
    if (empty($grade) || strtoupper($grade) === 'NORMAL') {
        return '';
    }
    
    $cssClass = 'grade-' . strtolower($grade);
    return '<span class="' . $cssClass . '">' . $normalizedGrade . '</span>';
}

/**
 * Get grade CSS class
 */
function getGradeClass($grade) {
    if (empty($grade)) {
        return 'grade-normal';
    }
    return 'grade-' . strtolower($grade);
}

/**
 * Element normalization
 */
function normalizeElement($element) {
    $elements = [
        'EARTH' => 'Earth',
        'AIR' => 'Air',
        'WATER' => 'Water',
        'FIRE' => 'Fire',
        'NONE' => 'None'
    ];
    return $elements[$element] ?? $element;
}

/**
 * Alignment normalization
 */
function normalizeAlignment($alignment) {
    $alignments = [
        'CAOTIC' => 'Chaotic',
        'NEUTRAL' => 'Neutral',
        'LAWFUL' => 'Lawful',
        'NONE' => 'None'
    ];
    return $alignments[$alignment] ?? $alignment;
}

/**
 * Authentication helper functions
 */
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

/**
 * Logging functions
 */
function logAdminActivity($action, $table_name, $record_id = null, $details = null) {
    if (!isset($_SESSION['user_id'])) return;
    
    $query = "INSERT INTO admin_activity (admin_username, activity_type, description, entity_type, entity_id, ip_address, user_agent, timestamp) 
              VALUES (:admin_username, :activity_type, :description, :entity_type, :entity_id, :ip_address, :user_agent, NOW())";
    
    try {
        executeQuery($query, [
            ':admin_username' => $_SESSION['username'] ?? 'unknown',
            ':activity_type' => $action,
            ':description' => $details ?? $action,
            ':entity_type' => $table_name,
            ':entity_id' => $record_id,
            ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
    } catch (Exception $e) {
        error_log("Failed to log admin activity: " . $e->getMessage());
    }
}

/**
 * Image/Icon helper functions
 */
function getItemIcon($category, $itemId) {
    $connection = getDatabaseConnection();
    
    try {
        $iconPath = null;
        
        switch ($category) {
            case 'weapons':
                $query = "SELECT iconId FROM weapon WHERE item_id = :item_id";
                break;
            case 'armor':
                $query = "SELECT iconId FROM armor WHERE item_id = :item_id";
                break;
            case 'items':
            case 'dolls':
                $query = "SELECT iconId FROM etcitem WHERE item_id = :item_id";
                break;
            case 'monsters':
                $query = "SELECT spriteId as iconId FROM npc WHERE npcid = :item_id";
                break;
            default:
                return getDefaultIcon($category);
        }
        
        $result = getSingleValue($query, [':item_id' => $itemId], $connection);
        
        if ($result) {
            $iconPath = ($category === 'monsters' ? 'ms' : '') . $result . '.png';
            
            // Check if the icon file exists
            if (file_exists(__DIR__ . '/../assets/img/icons/' . $iconPath)) {
                return $iconPath;
            }
        }
        
        return getDefaultIcon($category);
    } catch (Exception $e) {
        return getDefaultIcon($category);
    }
}

function getDefaultIcon($category) {
    $categoryIcons = [
        'weapons' => '2.png',
        'armor' => '20.png',
        'items' => '40017.png',
        'dolls' => '49150.png',
        'monsters' => 'ms45037.png',
        'maps' => '40030.png'
    ];
    
    return $categoryIcons[$category] ?? '0.png';
}

/**
 * Get monsters that drop a specific item
 */
function getMonstersByItemDrop($itemId) {
    $query = "SELECT DISTINCT n.npcid, n.desc_en, n.spriteId, n.lvl, d.chance, d.min, d.max, d.Enchant
              FROM droplist d 
              INNER JOIN npc n ON d.mobId = n.npcid 
              WHERE d.itemId = :itemId 
              AND n.impl IN ('L1Monster', 'L1BlackKnight', 'L1Doppelganger')
              ORDER BY n.lvl ASC, n.desc_en ASC";
    
    try {
        $stmt = executeQuery($query, [':itemId' => $itemId]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get NPC sprite image path
 */
function getNpcSprite($npcTemplateId, $fallback = true) {
    try {
        $spriteId = getSingleValue("SELECT spriteId FROM npc WHERE npcid = :npc_id", [':npc_id' => $npcTemplateId]);
        
        if ($spriteId) {
            $pngPath = '/assets/img/icons/ms' . $spriteId . '.png';
            $gifPath = '/assets/img/icons/ms' . $spriteId . '.gif';
            
            if (file_exists(__DIR__ . '/..' . $pngPath)) {
                return $pngPath;
            } else if (file_exists(__DIR__ . '/..' . $gifPath)) {
                return $gifPath;
            }
        }
        
        return $fallback ? '/assets/img/icons/0.png' : null;
    } catch (Exception $e) {
        return $fallback ? '/assets/img/icons/0.png' : null;
    }
}

?>
