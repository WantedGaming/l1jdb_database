<?php
require_once '../../includes/functions.php';

// Get weapon ID from URL
$weaponId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($weaponId <= 0) {
    header('Location: weapon-list.php');
    exit;
}

// Get weapon data
$weapon = getWeaponById($weaponId);

if (!$weapon) {
    header('Location: weapon-list.php');
    exit;
}

$basePath = getBasePath();
?>

<?php echo generateHeader('Weapon Details - L1j-R Database'); ?>

<?php echo generateBreadcrumb([
    '../weapons/weapon-list.php' => 'Weapons',
    '#' => $weapon['desc_en']
]); ?>

<!-- Hero Section -->
<section class="weapon-hero">
    <div class="hero-content">
        <h1><?php echo htmlspecialchars($weapon['desc_en']); ?></h1>
        <p>Detailed weapon information and statistics</p>
    </div>
</section>

<!-- Main Content -->
<main class="content-section">
    <div class="container">
        
        <!-- First Row: Image Card (Left) + Basic Details (Right) - Equal Weight -->
        <div class="cards-grid" style="grid-template-columns: 1fr 1fr; margin-bottom: 20px;">
            <!-- Left Column: Image Card -->
            <div class="category-card">
                <div class="card-header">
                    <h2 class="card-title"><?php echo htmlspecialchars($weapon['desc_en']); ?></h2>
                </div>
                <div class="card-image-wrapper">
                    <img src="<?php echo $basePath; ?>assets/img/icons/<?php echo htmlspecialchars($weapon['iconId']); ?>.png" 
                         alt="<?php echo htmlspecialchars($weapon['desc_en']); ?>" 
                         class="card-image"
                         onerror="this.src='<?php echo $basePath; ?>assets/img/placeholders/noimage.png'">
                </div>
                <div class="card-content">
                    <div class="card-description">
                        <?php echo htmlspecialchars($weapon['desc_en']); ?>
                    </div>
                    <div class="card-description">
                        <strong>Small Damage:</strong> <?php echo number_format($weapon['dmg_small']); ?><br>
                        <strong>Large Damage:</strong> <?php echo number_format($weapon['dmg_large']); ?>
                    </div>
                </div>
            </div>
            
            <!-- Right Column: Basic Details -->
            <div class="category-card">
                <div class="card-header">
                    <h3 class="card-title">Basic Information</h3>
                </div>
                <div class="card-content">
                    <table class="detail-table">
                        <tr>
                            <td><strong>Item ID:</strong></td>
                            <td><?php echo htmlspecialchars($weapon['item_id']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Name ID:</strong></td>
                            <td><?php echo htmlspecialchars($weapon['item_name_id']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Korean Name:</strong></td>
                            <td><?php echo htmlspecialchars($weapon['desc_kr']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>English Name:</strong></td>
                            <td><?php echo htmlspecialchars($weapon['desc_en']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Indonesian Name:</strong></td>
                            <td><?php echo htmlspecialchars($weapon['desc_id']); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Second Row: Three Cards -->
        <div class="cards-grid" style="grid-template-columns: 1fr 1fr 1fr; margin-bottom: 20px;">
            <div class="category-card">
                <div class="card-header">
                    <h3 class="card-title">Weapon Specifications</h3>
                </div>
                <div class="card-content">
                    <table class="detail-table">
                        <tr>
                            <td><strong>Type:</strong></td>
                            <td><?php echo htmlspecialchars(normalizeType($weapon['type'])); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Material:</strong></td>
                            <td><?php echo htmlspecialchars(normalizeMaterial($weapon['material'])); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Weight:</strong></td>
                            <td><?php echo number_format($weapon['weight']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Item Grade:</strong></td>
                            <td><?php echo htmlspecialchars($weapon['itemGrade']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Safe Enchant:</strong></td>
                            <td><?php echo htmlspecialchars($weapon['safenchant']); ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="category-card">
                <div class="card-header">
                    <h3 class="card-title">Damage & Combat</h3>
                </div>
                <div class="card-content">
                    <table class="detail-table">
                        <tr>
                            <td><strong>Small Damage:</strong></td>
                            <td><?php echo number_format($weapon['dmg_small']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Large Damage:</strong></td>
                            <td><?php echo number_format($weapon['dmg_large']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Hit Modifier:</strong></td>
                            <td><?php echo htmlspecialchars($weapon['hitmodifier']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Damage Modifier:</strong></td>
                            <td><?php echo htmlspecialchars($weapon['dmgmodifier']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Magic Damage Modifier:</strong></td>
                            <td><?php echo htmlspecialchars($weapon['magicdmgmodifier']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Double Damage Chance:</strong></td>
                            <td><?php echo htmlspecialchars($weapon['double_dmg_chance']); ?>%</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="category-card">
                <div class="card-header">
                    <h3 class="card-title">Class Usage</h3>
                </div>
                <div class="card-content">
                    <table class="detail-table">
                        <?php 
                        $classes = getWeaponClasses();
                        foreach ($classes as $classKey => $className): 
                            $usable = $weapon['use_' . $classKey] == 1;
                        ?>
                        <tr>
                            <td><strong><?php echo $className; ?>:</strong></td>
                            <td>
                                <?php if ($usable): ?>
                                    <span style="color: #00ff00;">✓</span>
                                <?php else: ?>
                                    <span style="color: #ff0000;">✗</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>

        <!-- Third Row: Four Cards -->
        <div class="cards-grid" style="grid-template-columns: 1fr 1fr 1fr 1fr; margin-bottom: 20px;">
            <div class="category-card">
                <div class="card-header">
                    <h3 class="card-title">Stat Bonuses</h3>
                </div>
                <div class="card-content">
                    <table class="detail-table">
                        <tr>
                            <td><strong>Strength:</strong></td>
                            <td><?php echo $weapon['add_str'] != 0 ? ($weapon['add_str'] > 0 ? '+' : '') . $weapon['add_str'] : '-'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Constitution:</strong></td>
                            <td><?php echo $weapon['add_con'] != 0 ? ($weapon['add_con'] > 0 ? '+' : '') . $weapon['add_con'] : '-'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Dexterity:</strong></td>
                            <td><?php echo $weapon['add_dex'] != 0 ? ($weapon['add_dex'] > 0 ? '+' : '') . $weapon['add_dex'] : '-'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Intelligence:</strong></td>
                            <td><?php echo $weapon['add_int'] != 0 ? ($weapon['add_int'] > 0 ? '+' : '') . $weapon['add_int'] : '-'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Wisdom:</strong></td>
                            <td><?php echo $weapon['add_wis'] != 0 ? ($weapon['add_wis'] > 0 ? '+' : '') . $weapon['add_wis'] : '-'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Charisma:</strong></td>
                            <td><?php echo $weapon['add_cha'] != 0 ? ($weapon['add_cha'] > 0 ? '+' : '') . $weapon['add_cha'] : '-'; ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="category-card">
                <div class="card-header">
                    <h3 class="card-title">Health & Mana</h3>
                </div>
                <div class="card-content">
                    <table class="detail-table">
                        <tr>
                            <td><strong>HP Bonus:</strong></td>
                            <td><?php echo $weapon['add_hp'] != 0 ? ($weapon['add_hp'] > 0 ? '+' : '') . $weapon['add_hp'] : '-'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>MP Bonus:</strong></td>
                            <td><?php echo $weapon['add_mp'] != 0 ? ($weapon['add_mp'] > 0 ? '+' : '') . $weapon['add_mp'] : '-'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>HP Regen:</strong></td>
                            <td><?php echo $weapon['add_hpr'] != 0 ? ($weapon['add_hpr'] > 0 ? '+' : '') . $weapon['add_hpr'] : '-'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>MP Regen:</strong></td>
                            <td><?php echo $weapon['add_mpr'] != 0 ? ($weapon['add_mpr'] > 0 ? '+' : '') . $weapon['add_mpr'] : '-'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>SP Bonus:</strong></td>
                            <td><?php echo $weapon['add_sp'] != 0 ? ($weapon['add_sp'] > 0 ? '+' : '') . $weapon['add_sp'] : '-'; ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="category-card">
                <div class="card-header">
                    <h3 class="card-title">Special Properties</h3>
                </div>
                <div class="card-content">
                    <table class="detail-table">
                        <tr>
                            <td><strong>Magic Defense:</strong></td>
                            <td><?php echo $weapon['m_def'] != 0 ? ($weapon['m_def'] > 0 ? '+' : '') . $weapon['m_def'] : '-'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Haste Item:</strong></td>
                            <td><?php echo $weapon['haste_item'] == 1 ? 'Yes' : 'No'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Min Level:</strong></td>
                            <td><?php echo $weapon['min_lvl'] > 0 ? $weapon['min_lvl'] : '-'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Max Level:</strong></td>
                            <td><?php echo $weapon['max_lvl'] > 0 ? $weapon['max_lvl'] : '-'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Bless Status:</strong></td>
                            <td><?php echo $weapon['bless'] == 1 ? 'Blessed' : 'Normal'; ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="category-card">
                <div class="card-header">
                    <h3 class="card-title">Resistances</h3>
                </div>
                <div class="card-content">
                    <table class="detail-table">
                        <tr>
                            <td><strong>Skill Resist:</strong></td>
                            <td><?php echo $weapon['regist_skill'] != 0 ? ($weapon['regist_skill'] > 0 ? '+' : '') . $weapon['regist_skill'] : '-'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Spirit Resist:</strong></td>
                            <td><?php echo $weapon['regist_spirit'] != 0 ? ($weapon['regist_spirit'] > 0 ? '+' : '') . $weapon['regist_spirit'] : '-'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Dragon Resist:</strong></td>
                            <td><?php echo $weapon['regist_dragon'] != 0 ? ($weapon['regist_dragon'] > 0 ? '+' : '') . $weapon['regist_dragon'] : '-'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Fear Resist:</strong></td>
                            <td><?php echo $weapon['regist_fear'] != 0 ? ($weapon['regist_fear'] > 0 ? '+' : '') . $weapon['regist_fear'] : '-'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>All Resist:</strong></td>
                            <td><?php echo $weapon['regist_all'] != 0 ? ($weapon['regist_all'] > 0 ? '+' : '') . $weapon['regist_all'] : '-'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Poison Resist:</strong></td>
                            <td><?php echo $weapon['poisonRegist'] == 'true' ? 'Yes' : 'No'; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Fourth Row: Two Cards -->
        <div class="cards-grid" style="grid-template-columns: 1fr 1fr; margin-bottom: 20px;">
            <div class="category-card">
                <div class="card-header">
                    <h3 class="card-title">Critical & Hit Bonuses</h3>
                </div>
                <div class="card-content">
                    <table class="detail-table">
                        <tr>
                            <td><strong>Short Critical:</strong></td>
                            <td><?php echo $weapon['shortCritical'] != 0 ? ($weapon['shortCritical'] > 0 ? '+' : '') . $weapon['shortCritical'] : '-'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Long Critical:</strong></td>
                            <td><?php echo $weapon['longCritical'] != 0 ? ($weapon['longCritical'] > 0 ? '+' : '') . $weapon['longCritical'] : '-'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Magic Critical:</strong></td>
                            <td><?php echo $weapon['magicCritical'] != 0 ? ($weapon['magicCritical'] > 0 ? '+' : '') . $weapon['magicCritical'] : '-'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Hit vs Skill:</strong></td>
                            <td><?php echo $weapon['hitup_skill'] != 0 ? ($weapon['hitup_skill'] > 0 ? '+' : '') . $weapon['hitup_skill'] : '-'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Hit vs Spirit:</strong></td>
                            <td><?php echo $weapon['hitup_spirit'] != 0 ? ($weapon['hitup_spirit'] > 0 ? '+' : '') . $weapon['hitup_spirit'] : '-'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Hit vs Dragon:</strong></td>
                            <td><?php echo $weapon['hitup_dragon'] != 0 ? ($weapon['hitup_dragon'] > 0 ? '+' : '') . $weapon['hitup_dragon'] : '-'; ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="category-card">
                <div class="card-header">
                    <h3 class="card-title">Trading & Usage</h3>
                </div>
                <div class="card-content">
                    <table class="detail-table">
                        <tr>
                            <td><strong>Tradable:</strong></td>
                            <td><?php echo $weapon['trade'] == 1 ? 'Yes' : 'No'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Retrievable:</strong></td>
                            <td><?php echo $weapon['retrieve'] == 1 ? 'Yes' : 'No'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Can't Delete:</strong></td>
                            <td><?php echo $weapon['cant_delete'] == 1 ? 'Yes' : 'No'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Can't Sell:</strong></td>
                            <td><?php echo $weapon['cant_sell'] == 1 ? 'Yes' : 'No'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Max Use Time:</strong></td>
                            <td><?php echo $weapon['max_use_time'] > 0 ? number_format($weapon['max_use_time']) : 'Unlimited'; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Additional Sections -->
        <?php if (!empty($weapon['Magic_name'])): ?>
        <div class="cards-grid" style="grid-template-columns: 1fr; margin-bottom: 20px;">
            <div class="category-card">
                <div class="card-header">
                    <h3 class="card-title">Magic Properties</h3>
                </div>
                <div class="card-content">
                    <table class="detail-table">
                        <tr>
                            <td><strong>Magic Name:</strong></td>
                            <td><?php echo htmlspecialchars($weapon['Magic_name']); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($weapon['note'])): ?>
        <div class="cards-grid" style="grid-template-columns: 1fr; margin-bottom: 20px;">
            <div class="category-card">
                <div class="card-header">
                    <h3 class="card-title">Notes</h3>
                </div>
                <div class="card-content">
                    <div class="card-description">
                        <?php echo nl2br(htmlspecialchars($weapon['note'])); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="navigation-buttons">
            <a href="weapon-list.php" class="nav-btn">← Back to Weapons</a>
        </div>
    </div>
</main>

<?php echo generateFooter(); ?>
