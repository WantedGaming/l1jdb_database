<?php
/**
 * L1j-R Database Functions
 * Common functions for the MMORPG database website
 */

// Database connection settings (adjust as needed)
$db_host = 'localhost';
$db_name = 'l1j_remastered';
$db_user = 'root';
$db_pass = '';

/**
 * Get base path for the website
 */
function getBasePath() {
    // Simple approach: count how many levels deep we are from the project root
    $currentFile = $_SERVER['SCRIPT_FILENAME'];
    $currentDir = dirname($currentFile);
    
    // Count levels by looking for includes/functions.php
    $levels = 0;
    $testDir = $currentDir;
    
    while ($levels < 10) { // Safety limit
        if (file_exists($testDir . '/includes/functions.php')) {
            break;
        }
        $testDir = dirname($testDir);
        $levels++;
        
        // If we reach system root, something went wrong
        if ($testDir === dirname($testDir)) {
            $levels = 0;
            break;
        }
    }
    
    // Build relative path
    if ($levels === 0) {
        // We're in the project root
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        return rtrim($scriptDir, '/') . '/';
    } else {
        // We're in subdirectories, go back up
        return str_repeat('../', $levels);
    }
}

/**
 * Get database connection
 */
function getDBConnection() {
    global $db_host, $db_name, $db_user, $db_pass;
    
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

/**
 * Generate page header HTML
 */
function generateHeader($title = "L1j-R Database") {
    // Get the base path for assets
    $basePath = getBasePath();
    
    $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($title) . '</title>
    <link rel="stylesheet" href="' . $basePath . 'assets/css/style.css">
    <link rel="icon" type="image/x-icon" href="' . $basePath . 'assets/img/favicon/favicon.ico">
</head>
<body>
    <header class="header">
        <div class="nav-container">
            <a href="' . $basePath . '" class="logo">L1j-R Database</a>
            <nav>
                <ul class="nav-menu">
                    <li><a href="' . $basePath . '">Home</a></li>
                    <li><a href="' . $basePath . 'categories/weapons/weapon-list.php">Weapons</a></li>
                    <li><a href="' . $basePath . 'categories/armor/armor-list.php">Armor</a></li>
                    <li><a href="' . $basePath . 'categories/items/item-list.php">Items</a></li>
                    <li><a href="' . $basePath . 'categories/monsters/monster-list.php">Monsters</a></li>
                    <li><a href="' . $basePath . 'categories/maps/map-list.php">Maps</a></li>
                    <li><a href="' . $basePath . 'categories/dolls/doll-list.php">Dolls</a></li>
                </ul>
            </nav>
        </div>
    </header>';
    
    return $html;
}

/**
 * Generate page footer HTML
 */
function generateFooter() {
    $basePath = getBasePath();
    
    $html = '
    <footer class="footer">
        <div class="container">
            <p>&copy; ' . date('Y') . ' L1j-R Database. All rights reserved.</p>
            <p>Your ultimate MMORPG database resource</p>
        </div>
    </footer>
    <script src="' . $basePath . 'assets/js/main.js"></script>
</body>
</html>';
    
    return $html;
}

/**
 * Generate breadcrumb navigation
 */
function generateBreadcrumb($items) {
    $basePath = getBasePath();
    
    $html = '<div class="breadcrumb">
        <div class="breadcrumb-nav">
            <a href="' . $basePath . '">Home</a> > ';
    
    $total = count($items);
    $current = 0;
    
    foreach($items as $link => $text) {
        $current++;
        if($current == $total) {
            $html .= '<span>' . htmlspecialchars($text) . '</span>';
        } else {
            $html .= '<a href="' . htmlspecialchars($link) . '">' . htmlspecialchars($text) . '</a> > ';
        }
    }
    
    $html .= '</div>
    </div>';
    
    return $html;
}

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

/**
 * Format number with commas
 */
function formatNumber($number) {
    return number_format($number);
}

/**
 * Get category data
 */
function getCategoryData($category, $limit = null) {
    $pdo = getDBConnection();
    
    $sql = "SELECT * FROM $category";
    if ($limit) {
        $sql .= " LIMIT " . intval($limit);
    }
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        return [];
    }
}

/**
 * Get single item by ID
 */
function getItemById($category, $id) {
    $pdo = getDBConnection();
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM $category WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        return null;
    }
}

/**
 * Search items in category
 */
function searchItems($category, $query, $limit = 50) {
    $pdo = getDBConnection();
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM $category WHERE name LIKE ? LIMIT ?");
        $stmt->execute(['%' . $query . '%', $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        return [];
    }
}

/**
 * Get total count for category
 */
function getCategoryCount($category) {
    $pdo = getDBConnection();
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM $category");
        $stmt->execute();
        return $stmt->fetchColumn();
    } catch(PDOException $e) {
        return 0;
    }
}

/**
 * Generate pagination links
 */
function generatePagination($currentPage, $totalPages, $baseUrl) {
    if ($totalPages <= 1) return '';
    
    $html = '<div class="pagination">';
    
    // Previous button
    if ($currentPage > 1) {
        $html .= '<a href="' . $baseUrl . '?page=' . ($currentPage - 1) . '" class="page-btn">« Previous</a>';
    }
    
    // Page numbers
    for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++) {
        if ($i == $currentPage) {
            $html .= '<span class="page-btn active">' . $i . '</span>';
        } else {
            $html .= '<a href="' . $baseUrl . '?page=' . $i . '" class="page-btn">' . $i . '</a>';
        }
    }
    
    // Next button
    if ($currentPage < $totalPages) {
        $html .= '<a href="' . $baseUrl . '?page=' . ($currentPage + 1) . '" class="page-btn">Next »</a>';
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Get placeholder image path for category
 */
function getPlaceholderImage($category) {
    $basePath = getBasePath();
    
    $images = [
        'weapons' => $basePath . 'assets/img/placeholders/weapons.png',
        'armor' => $basePath . 'assets/img/placeholders/armor.png',
        'items' => $basePath . 'assets/img/placeholders/items.png',
        'monsters' => $basePath . 'assets/img/placeholders/monsters.png',
        'maps' => $basePath . 'assets/img/placeholders/maps.png',
        'dolls' => $basePath . 'assets/img/placeholders/dolls.png'
    ];
    
    return isset($images[$category]) ? $images[$category] : $basePath . 'assets/img/placeholders/noimage.png';
}
?>