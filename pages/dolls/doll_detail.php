<?php
require_once dirname(dirname(__DIR__)) . '/includes/config.php';
require_once dirname(dirname(__DIR__)) . '/includes/header.php';

// Get doll ID from URL
$dollId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($dollId <= 0) {
    header('Location: doll_list.php');
    exit;
}

// Get doll data with item info
$sql = "SELECT mi.*, e.desc_en, e.iconId, e.desc_kr, e.weight, e.bless, e.trade, e.retrieve, 
               e.cant_delete, e.cant_sell, e.use_royal, e.use_knight, e.use_mage, e.use_elf,
               e.use_darkelf, e.use_dragonknight, e.use_illusionist, e.use_warrior, 
               e.use_fencer, e.use_lancer, e.note, e.itemGrade
        FROM magicdoll_info mi 
        LEFT JOIN etcitem e ON mi.itemId = e.item_id 
        WHERE mi.itemId = :doll_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':doll_id' => $dollId]);
$doll = $stmt->fetch();

if (!$doll) {
    header('Location: doll_list.php');
    exit;
}

// Get potential info if bonusItemId exists
$potential = null;
if ($doll['bonusItemId'] > 0) {
    $potentialSql = "SELECT * FROM magicdoll_potential WHERE bonusId = :bonus_id";
    $potentialStmt = $pdo->prepare($potentialSql);
    $potentialStmt->execute([':bonus_id' => $doll['bonusItemId']]);
    $potential = $potentialStmt->fetch();
}

// Call page header with doll name
$dollName = cleanDollName($doll['name']);
getPageHeader($dollName);

// Create hero text
$heroText = getDollGradeDisplay($doll['grade']) . ' Magic Doll - Summon your loyal companion';

// Render hero section
renderHero('dolls', $dollName, $heroText);
?>

<main>
    <div class="main">
        <div class="container">
            <!-- Back Navigation -->
            <div class="back-nav">
                <a href="doll_list.php" class="back-btn">&larr; Back to Magic Dolls</a>
            </div>
            
            <!-- Main Content Row -->
            <div class="weapon-detail-row">
                <!-- Column 1: Image Preview -->
                <div class="weapon-image-col">
                    <div class="weapon-image-container">
                        <img src="<?= SITE_URL ?>/assets/img/icons/<?= $doll['iconId'] ?>.png" 
                             alt="<?= htmlspecialchars($dollName) ?>" 
                             onerror="this.src='<?= SITE_URL ?>/assets/img/placeholders/0.png'"
                             class="weapon-main-image">
                    </div>
                </div>
                
                <!-- Column 2: Basic Information -->
                <div class="weapon-info-col">
                    <div class="weapon-basic-info">
                        <h2>Basic Information</h2>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Item ID:</label>
                                <span><?= $doll['itemId'] ?></span>
                            </div>
                            <div class="info-item">
                                <label>NPC ID:</label>
                                <span><?= $doll['dollNpcId'] ?></span>
                            </div>
                            <div class="info-item">
                                <label>Grade:</label>
                                <span><?= getDollGradeDisplay($doll['grade']) ?></span>
                            </div>
                            <?php if ($doll['weight']): ?>
                            <div class="info-item">
                                <label>Weight:</label>
                                <span><?= number_format($doll['weight']) ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="info-item">
                                <label>Bless:</label>
                                <span><?= $doll['bless'] == 1 ? 'Normal' : ($doll['bless'] == 0 ? 'Cursed' : 'Blessed') ?></span>
                            </div>
                            <div class="info-item">
                                <label>Haste:</label>
                                <span><?= $doll['haste'] === 'true' ? 'Yes' : 'No' ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Doll Abilities Section -->
            <div class="weapon-section">
                <h2>Doll Abilities</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Combat</h3>
                        <div class="stat-values">
                            <?php if ($doll['damageChance'] > 0): ?>
                            <div class="stat-item">
                                <label>Attack Chance:</label>
                                <span class="stat-value"><?= $doll['damageChance'] ?>%</span>
                            </div>
                            <?php endif; ?>
                            <?php if ($doll['attackSkillEffectId'] > 0): ?>
                            <div class="stat-item">
                                <label>Attack Skill Effect:</label>
                                <span class="stat-value"><?= $doll['attackSkillEffectId'] ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($doll['haste'] === 'true'): ?>
                            <div class="stat-item">
                                <label>Haste Bonus:</label>
                                <span class="stat-value">Active</span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if ($doll['bonusItemId'] > 0 || $doll['bonusCount'] > 0 || $doll['bonusInterval'] > 0): ?>
                    <div class="stat-card">
                        <h3>Bonus Items</h3>
                        <div class="stat-values">
                            <?php if ($doll['bonusItemId'] > 0): ?>
                            <div class="stat-item">
                                <label>Bonus Item ID:</label>
                                <span class="stat-value"><?= $doll['bonusItemId'] ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($doll['bonusCount'] > 0): ?>
                            <div class="stat-item">
                                <label>Bonus Count:</label>
                                <span class="stat-value"><?= $doll['bonusCount'] ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($doll['bonusInterval'] > 0): ?>
                            <div class="stat-item">
                                <label>Bonus Interval:</label>
                                <span class="stat-value"><?= $doll['bonusInterval'] ?>s</span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($doll['blessItemId']): ?>
                    <div class="stat-card">
                        <h3>Enhancement</h3>
                        <div class="stat-values">
                            <div class="stat-item">
                                <label>Bless Item ID:</label>
                                <span class="stat-value"><?= $doll['blessItemId'] ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Potential Bonuses Section -->
            <?php if ($potential): ?>
            <div class="weapon-section">
                <h2>Potential Bonuses</h2>
                <?php if ($potential['name']): ?>
                <div class="potential-header">
                    <h3><?= htmlspecialchars($potential['name']) ?></h3>
                    <?php if ($potential['desc_kr']): ?>
                    <p class="potential-desc"><?= htmlspecialchars($potential['desc_kr']) ?></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <!-- Stats Bonuses -->
                <?php if ($potential['str'] || $potential['con'] || $potential['dex'] || $potential['int'] || $potential['wis'] || $potential['cha'] || $potential['allStatus']): ?>
                <div class="bonus-section">
                    <h4>Stat Bonuses</h4>
                    <div class="bonus-grid">
                        <?php if ($potential['str']): ?>
                        <div class="bonus-item">
                            <label>STR:</label>
                            <span class="bonus-positive">+<?= $potential['str'] ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($potential['con']): ?>
                        <div class="bonus-item">
                            <label>CON:</label>
                            <span class="bonus-positive">+<?= $potential['con'] ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($potential['dex']): ?>
                        <div class="bonus-item">
                            <label>DEX:</label>
                            <span class="bonus-positive">+<?= $potential['dex'] ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($potential['int']): ?>
                        <div class="bonus-item">
                            <label>INT:</label>
                            <span class="bonus-positive">+<?= $potential['int'] ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($potential['wis']): ?>
                        <div class="bonus-item">
                            <label>WIS:</label>
                            <span class="bonus-positive">+<?= $potential['wis'] ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($potential['cha']): ?>
                        <div class="bonus-item">
                            <label>CHA:</label>
                            <span class="bonus-positive">+<?= $potential['cha'] ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($potential['allStatus']): ?>
                        <div class="bonus-item">
                            <label>All Stats:</label>
                            <span class="bonus-positive">+<?= $potential['allStatus'] ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Class Restrictions -->
            <div class="weapon-section">
                <h2>Class Restrictions</h2>
                <div class="class-grid">
                    <div class="class-item <?= $doll['use_royal'] ? 'allowed' : 'restricted' ?>">
                        <span>Royal</span>
                        <span class="status"><?= $doll['use_royal'] ? '✓' : '✗' ?></span>
                    </div>
                    <div class="class-item <?= $doll['use_knight'] ? 'allowed' : 'restricted' ?>">
                        <span>Knight</span>
                        <span class="status"><?= $doll['use_knight'] ? '✓' : '✗' ?></span>
                    </div>
                    <div class="class-item <?= $doll['use_mage'] ? 'allowed' : 'restricted' ?>">
                        <span>Mage</span>
                        <span class="status"><?= $doll['use_mage'] ? '✓' : '✗' ?></span>
                    </div>
                    <div class="class-item <?= $doll['use_elf'] ? 'allowed' : 'restricted' ?>">
                        <span>Elf</span>
                        <span class="status"><?= $doll['use_elf'] ? '✓' : '✗' ?></span>
                    </div>
                    <div class="class-item <?= $doll['use_darkelf'] ? 'allowed' : 'restricted' ?>">
                        <span>Dark Elf</span>
                        <span class="status"><?= $doll['use_darkelf'] ? '✓' : '✗' ?></span>
                    </div>
                    <div class="class-item <?= $doll['use_dragonknight'] ? 'allowed' : 'restricted' ?>">
                        <span>Dragon Knight</span>
                        <span class="status"><?= $doll['use_dragonknight'] ? '✓' : '✗' ?></span>
                    </div>
                    <div class="class-item <?= $doll['use_illusionist'] ? 'allowed' : 'restricted' ?>">
                        <span>Illusionist</span>
                        <span class="status"><?= $doll['use_illusionist'] ? '✓' : '✗' ?></span>
                    </div>
                    <div class="class-item <?= $doll['use_warrior'] ? 'allowed' : 'restricted' ?>">
                        <span>Warrior</span>
                        <span class="status"><?= $doll['use_warrior'] ? '✓' : '✗' ?></span>
                    </div>
                    <div class="class-item <?= $doll['use_fencer'] ? 'allowed' : 'restricted' ?>">
                        <span>Fencer</span>
                        <span class="status"><?= $doll['use_fencer'] ? '✓' : '✗' ?></span>
                    </div>
                    <div class="class-item <?= $doll['use_lancer'] ? 'allowed' : 'restricted' ?>">
                        <span>Lancer</span>
                        <span class="status"><?= $doll['use_lancer'] ? '✓' : '✗' ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php getPageFooter(); ?>
