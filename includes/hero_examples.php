<?php
/**
 * HERO SYSTEM USAGE EXAMPLES
 * 
 * How to implement dynamic heroes in your pages
 */

// BASIC USAGE - Category listing pages
// weapons/index.php
require_once '../includes/header.php';
getPageHeader('Weapons');
?>
<main>
    <?php renderHero('weapons'); ?>
    <div class="main">
        <!-- Your content here -->
    </div>
</main>
<?php getPageFooter(); ?>

// ADVANCED USAGE - Detail pages with database data
// weapons/weapon_detail.php
require_once '../includes/header.php';

// Get weapon data
$weapon_id = $_GET['id'] ?? 0;
$sql = "SELECT * FROM weapon WHERE item_id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $weapon_id]);
$weapon = $stmt->fetch();

getPageHeader($weapon['desc_kr'] ?? 'Weapon Details');
?>
<main>
    <?php renderHero('weapon_detail', $weapon); ?>
    <div class="main">
        <!-- Weapon details here -->
    </div>
</main>
<?php getPageFooter(); ?>

// AVAILABLE HERO TYPES:
// 'home'          - Homepage with database stats
// 'weapons'       - Weapons listing
// 'armor'         - Armor listing  
// 'items'         - Items listing
// 'dolls'         - Magic dolls listing
// 'maps'          - Maps listing
// 'monsters'      - Monsters listing
// 'weapon_detail' - Individual weapon (requires weapon data)
// 'armor_detail'  - Individual armor (requires armor data)
// 'admin'         - Admin dashboard

// CUSTOM HERO - Override hero content manually
$customHero = [
    'title' => 'Custom Title',
    'subtitle' => 'Custom description with dynamic data',
    'background' => '/path/to/custom/background.jpg'
];
// Then pass as data parameter
renderHero('custom', $customHero);

?>
