<?php
require_once dirname(dirname(__DIR__)) . '/includes/config.php';
require_once dirname(dirname(__DIR__)) . '/includes/header.php';

// Get monster ID from URL
$monsterId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($monsterId <= 0) {
    header('Location: monster_list.php');
    exit;
}

// Get monster data
$sql = "SELECT * FROM npc WHERE npcid = :npcid";
$stmt = $pdo->prepare($sql);
$stmt->execute([':npcid' => $monsterId]);
$monster = $stmt->fetch();

if (!$monster) {
    header('Location: monster_list.php');
    exit;
}

// Function to get monster drops
function getMonsterDrops($pdo, $npcId) {
    $sql = "SELECT d.*, 
                   w.desc_en as item_name, w.iconId as item_icon, 'weapon' as item_type
            FROM droplist d 
            LEFT JOIN weapon w ON d.itemId = w.item_id 
            WHERE d.mobId = :npcid AND w.item_id IS NOT NULL
            
            UNION ALL
            
            SELECT d.*, 
                   a.desc_en as item_name, a.iconId as item_icon, 'armor' as item_type
            FROM droplist d 
            LEFT JOIN armor a ON d.itemId = a.item_id 
            WHERE d.mobId = :npcid AND a.item_id IS NOT NULL
            
            UNION ALL
            
            SELECT d.*, 
                   e.desc_en as item_name, e.iconId as item_icon, 'etcitem' as item_type
            FROM droplist d 
            LEFT JOIN etcitem e ON d.itemId = e.item_id 
            WHERE d.mobId = :npcid AND e.item_id IS NOT NULL
            
            ORDER BY chance DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':npcid' => $npcId]);
    return $stmt->fetchAll();
}
function getMonsterSpawns($pdo, $npcId) {
    $spawns = [];
    
    // Query normal spawnlist
    $sql = "SELECT s.*, m.locationname, m.desc_kr, m.pngId 
            FROM spawnlist s 
            LEFT JOIN mapids m ON s.mapid = m.mapid 
            WHERE s.npc_templateid = :npcid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':npcid' => $npcId]);
    $normalSpawns = $stmt->fetchAll();
    foreach ($normalSpawns as $spawn) {
        $spawn['spawn_type'] = 'normal';
        $spawn['table_source'] = 'spawnlist';
        $spawns[] = $spawn;
    }
    
    // Query boss spawnlist
    $sql = "SELECT sb.*, m.locationname, m.desc_kr, m.pngId,
            sb.spawnX as locx, sb.spawnY as locy, sb.spawnMapId as mapid
            FROM spawnlist_boss sb 
            LEFT JOIN mapids m ON sb.spawnMapId = m.mapid 
            WHERE sb.npcid = :npcid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':npcid' => $npcId]);
    $bossSpawns = $stmt->fetchAll();
    foreach ($bossSpawns as $spawn) {
        $spawn['spawn_type'] = 'boss';
        $spawn['table_source'] = 'spawnlist_boss';
        $spawns[] = $spawn;
    }
    
    // Query world war spawnlist
    $sql = "SELECT sw.*, m.locationname, m.desc_kr, m.pngId 
            FROM spawnlist_worldwar sw 
            LEFT JOIN mapids m ON sw.mapid = m.mapid 
            WHERE sw.npc_id = :npcid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':npcid' => $npcId]);
    $worldwarSpawns = $stmt->fetchAll();
    foreach ($worldwarSpawns as $spawn) {
        $spawn['spawn_type'] = 'worldwar';
        $spawn['table_source'] = 'spawnlist_worldwar';
        $spawns[] = $spawn;
    }
    
    // Query unicorn temple spawnlist
    $sql = "SELECT su.*, m.locationname, m.desc_kr, m.pngId,
            su.mapId as mapid
            FROM spawnlist_unicorntemple su 
            LEFT JOIN mapids m ON su.mapId = m.mapid 
            WHERE su.npc_id = :npcid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':npcid' => $npcId]);
    $unicornSpawns = $stmt->fetchAll();
    foreach ($unicornSpawns as $spawn) {
        $spawn['spawn_type'] = 'unicorntemple';
        $spawn['table_source'] = 'spawnlist_unicorntemple';
        $spawns[] = $spawn;
    }
    
    // Query UB spawnlist
    $sql = "SELECT sub.*, 'Underground' as locationname, '' as desc_kr, 0 as pngId
            FROM spawnlist_ub sub 
            WHERE sub.npc_templateid = :npcid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':npcid' => $npcId]);
    $ubSpawns = $stmt->fetchAll();
    foreach ($ubSpawns as $spawn) {
        $spawn['spawn_type'] = 'ub';
        $spawn['table_source'] = 'spawnlist_ub';
        $spawn['mapid'] = 0; // UB doesn't have specific map
        $spawns[] = $spawn;
    }
    
    // Query Ruun spawnlist
    $sql = "SELECT sr.*, m.locationname, m.desc_kr, m.pngId,
            sr.locX as locx, sr.locY as locy, sr.mapId as mapid
            FROM spawnlist_ruun sr 
            LEFT JOIN mapids m ON sr.mapId = m.mapid 
            WHERE sr.npcId = :npcid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':npcid' => $npcId]);
    $ruunSpawns = $stmt->fetchAll();
    foreach ($ruunSpawns as $spawn) {
        $spawn['spawn_type'] = 'ruun';
        $spawn['table_source'] = 'spawnlist_ruun';
        $spawns[] = $spawn;
    }
    
    // Query other spawnlist
    $sql = "SELECT so.*, m.locationname, m.desc_kr, m.pngId,
            so.mapId as mapid
            FROM spawnlist_other so 
            LEFT JOIN mapids m ON so.mapId = m.mapid 
            WHERE so.npc_id = :npcid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':npcid' => $npcId]);
    $otherSpawns = $stmt->fetchAll();
    foreach ($otherSpawns as $spawn) {
        $spawn['spawn_type'] = 'other';
        $spawn['table_source'] = 'spawnlist_other';
        $spawns[] = $spawn;
    }
    
    // Query indun spawnlist
    $sql = "SELECT si.*, m.locationname, m.desc_kr, m.pngId,
            si.mapId as mapid
            FROM spawnlist_indun si 
            LEFT JOIN mapids m ON si.mapId = m.mapid 
            WHERE si.npc_id = :npcid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':npcid' => $npcId]);
    $indunSpawns = $stmt->fetchAll();
    foreach ($indunSpawns as $spawn) {
        $spawn['spawn_type'] = 'indun';
        $spawn['table_source'] = 'spawnlist_indun';
        $spawns[] = $spawn;
    }
    
    return $spawns;
}

// Function to format respawn time
function formatRespawnTime($spawn) {
    if ($spawn['spawn_type'] == 'boss') {
        if (!empty($spawn['spawnTime']) && !empty($spawn['spawnDay'])) {
            return $spawn['spawnDay'] . ' at ' . $spawn['spawnTime'];
        }
        if (!empty($spawn['aliveSecond'])) {
            $hours = floor($spawn['aliveSecond'] / 3600);
            $minutes = floor(($spawn['aliveSecond'] % 3600) / 60);
            return $hours . 'h ' . $minutes . 'm';
        }
        return 'Boss Schedule';
    }
    
    if (!empty($spawn['min_respawn_delay']) && !empty($spawn['max_respawn_delay'])) {
        $minMinutes = floor($spawn['min_respawn_delay'] / 60000);
        $maxMinutes = floor($spawn['max_respawn_delay'] / 60000);
        if ($minMinutes == $maxMinutes) {
            return $minMinutes . ' minutes';
        }
        return $minMinutes . '-' . $maxMinutes . ' minutes';
    }
    
    if (!empty($spawn['spawn_delay'])) {
        $minutes = floor($spawn['spawn_delay'] / 60000);
        return $minutes . ' minutes';
    }
    
    if ($spawn['spawn_type'] == 'worldwar') {
        return 'World War Event';
    }
    
    if ($spawn['spawn_type'] == 'other' && !empty($spawn['timeMillisToDelete'])) {
        $minutes = floor($spawn['timeMillisToDelete'] / 60000);
        return $minutes . ' minutes (temporary)';
    }
    
    return 'Variable';
}

// Function to get spawn type label
function getSpawnTypeLabel($type) {
    $labels = [
        'normal' => 'Normal Spawn',
        'boss' => 'Boss Spawn',
        'worldwar' => 'World War',
        'ub' => 'Underground',
        'indun' => 'Instance Dungeon',
        'other' => 'Special Event',
        'unicorntemple' => 'Unicorn Temple',
        'ruun' => 'Ruun Castle'
    ];
    return $labels[$type] ?? 'Unknown';
}

// Get spawn data
$spawnData = getMonsterSpawns($pdo, $monsterId);

// Get drops data
$dropsData = getMonsterDrops($pdo, $monsterId);

// Call page header with monster name
$monsterName = cleanDescriptionPrefix($monster['desc_en']);
getPageHeader($monsterName);

// Create monster description for hero
$heroText = 'Level ' . $monster['lvl'] . ' Monster';

// Render hero section
renderHero('monsters', $monsterName, $heroText);
?>

<main>
    <div class="main">
        <div class="container">
            <!-- Back Navigation -->
            <div class="back-nav">
                <a href="monster_list.php" class="back-btn">&larr; Back to Monsters</a>
            </div>
            
            <!-- Main Content Row -->
            <div class="weapon-detail-row">
                <!-- Column 1: Image Preview -->
                <div class="weapon-image-col">
                    <div class="weapon-image-container">
                        <img src="<?= SITE_URL ?>/assets/img/icons/ms<?= $monster['spriteId'] ?>.png" 
                             alt="<?= htmlspecialchars($monsterName) ?>" 
                             onerror="this.src='<?= SITE_URL ?>/assets/img/icons/ms<?= $monster['spriteId'] ?>.gif'; this.onerror=function(){this.src='<?= SITE_URL ?>/assets/img/placeholders/monsters.png';}"
                             class="weapon-main-image">
                    </div>
                </div>
                
                <!-- Column 2: Basic Information -->
                <div class="weapon-info-col">
                    <div class="weapon-basic-info">
                        <h2>Basic</h2>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Level:</label>
                                <span><?= $monster['lvl'] ?></span>
                            </div>
                            <div class="info-item">
                                <label>HP:</label>
                                <span><?= number_format($monster['hp']) ?></span>
                            </div>
                            <div class="info-item">
                                <label>MP:</label>
                                <span><?= number_format($monster['mp']) ?></span>
                            </div>
                            <div class="info-item">
                                <label>AC:</label>
                                <span><?= $monster['ac'] ?></span>
                            </div>
                            <div class="info-item">
                                <label>Experience:</label>
                                <span><?= number_format($monster['exp']) ?></span>
                            </div>
                            <div class="info-item">
                                <label>Alignment:</label>
                                <span><?= $monster['alignment'] ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Stats Section -->
            <div class="weapon-section">
                <h2>Statistics</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Attributes</h3>
                        <div class="stat-values">
                            <div class="stat-item">
                                <label>STR:</label>
                                <span class="stat-value"><?= $monster['str'] ?></span>
                            </div>
                            <div class="stat-item">
                                <label>CON:</label>
                                <span class="stat-value"><?= $monster['con'] ?></span>
                            </div>
                            <div class="stat-item">
                                <label>DEX:</label>
                                <span class="stat-value"><?= $monster['dex'] ?></span>
                            </div>
                            <div class="stat-item">
                                <label>WIS:</label>
                                <span class="stat-value"><?= $monster['wis'] ?></span>
                            </div>
                            <div class="stat-item">
                                <label>INT:</label>
                                <span class="stat-value"><?= $monster['intel'] ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Combat</h3>
                        <div class="stat-values">
                            <div class="stat-item">
                                <label>Magic Resistance:</label>
                                <span class="stat-value"><?= $monster['mr'] ?></span>
                            </div>
                            <div class="stat-item">
                                <label>Ranged:</label>
                                <span class="stat-value"><?= $monster['ranged'] ?></span>
                            </div>
                            <div class="stat-item">
                                <label>Attack Speed:</label>
                                <span class="stat-value"><?= $monster['atkspeed'] ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Properties</h3>
                        <div class="stat-values">
                            <div class="stat-item">
                                <label>Undead:</label>
                                <span class="stat-value"><?= $monster['undead'] ?></span>
                            </div>
                            <div class="stat-item">
                                <label>Weak Attribute:</label>
                                <span class="stat-value"><?= $monster['weakAttr'] ?></span>
                            </div>
                            <div class="stat-item">
                                <label>Aggressive:</label>
                                <span class="stat-value"><?= $monster['is_agro'] == 'true' ? 'Yes' : 'No' ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Drops Section -->
            <div class="weapon-section">
                <h2>Drops</h2>
                <?php if (!empty($dropsData)): ?>
                    <div class="<?= count($dropsData) == 1 ? 'dropped-by-grid single-item' : 'dropped-by-grid' ?>">
                        <?php foreach ($dropsData as $drop): ?>
                            <?php 
                                // Get item link based on type
                                $itemLink = '';
                                
                                if ($drop['item_type'] == 'weapon') {
                                    $itemLink = '../weapons/weapon_detail.php?id=' . $drop['itemId'];
                                } elseif ($drop['item_type'] == 'armor') {
                                    $itemLink = '../armor/armor_detail.php?id=' . $drop['itemId'];
                                } elseif ($drop['item_type'] == 'etcitem') {
                                    $itemLink = '../items/items_detail.php?id=' . $drop['itemId'];
                                }
                            ?>
                            <div class="monster-card">
                                <div class="monster-info">
                                    <div class="monster-image">
                                        <img src="<?= SITE_URL ?>/assets/img/icons/<?= $drop['item_icon'] ?>.png" 
                                             alt="<?= htmlspecialchars(cleanDescriptionPrefix($drop['item_name'])) ?>"
                                             onerror="this.src='<?= SITE_URL ?>/assets/img/placeholders/0.png'">
                                    </div>
                                    <div class="monster-details">
                                        <h4>
                                            <a href="<?= $itemLink ?>" class="weapon-link">
                                                <?= htmlspecialchars(cleanDescriptionPrefix($drop['item_name'])) ?>
                                            </a>
                                        </h4>
                                        <div class="monster-level"><?= ucfirst($drop['item_type']) ?></div>
                                    </div>
                                </div>
                                <div class="drop-stats">
                                    <div class="drop-stat">
                                        <span class="drop-stat-label">Chance:</span>
                                        <span class="drop-stat-value"><?= formatDropChance($drop['chance']) ?></span>
                                    </div>
                                    <?php if ($drop['min'] > 0 || $drop['max'] > 0): ?>
                                    <div class="drop-stat">
                                        <span class="drop-stat-label">Quantity:</span>
                                        <span class="drop-stat-value"><?= $drop['min'] == $drop['max'] ? $drop['min'] : $drop['min'] . '-' . $drop['max'] ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($drop['Enchant'] > 0): ?>
                                    <div class="drop-stat">
                                        <span class="drop-stat-label">Enchant:</span>
                                        <span class="drop-stat-value">+<?= $drop['Enchant'] ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-drops-message">
                        This monster does not drop any items.
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Spawns Section -->
            <div class="weapon-section spawns-section">
                <h2>Spawns</h2>
                <?php if (!empty($spawnData)): ?>
                    <div class="<?= count($spawnData) == 1 ? 'dropped-by-grid single-item' : 'dropped-by-grid' ?>">
                        <?php foreach ($spawnData as $spawn): ?>
                            <div class="monster-card">
                                <div class="monster-info">
                                    <div class="monster-image">
                                        <img src="<?= SITE_URL ?>/assets/img/icons/<?= $spawn['pngId'] ?: 'default-map' ?>.png" 
                                             alt="<?= htmlspecialchars($spawn['locationname'] ?: $spawn['desc_kr'] ?: 'Unknown Location') ?>"
                                             onerror="this.src='<?= SITE_URL ?>/assets/img/icons/<?= $spawn['pngId'] ?: 'default-map' ?>.jpeg'; this.onerror=function(){this.src='<?= SITE_URL ?>/assets/img/placeholders/0.png';}">
                                        <div class="map-overlay">
                                            <h5><?= htmlspecialchars($spawn['locationname'] ?: $spawn['desc_kr'] ?: 'Unknown Location') ?></h5>
                                            <?php if (!empty($spawn['locx']) && !empty($spawn['locy'])): ?>
                                            <p>Coordinates: <?= $spawn['locx'] ?>, <?= $spawn['locy'] ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="spawn-type">
                                    <h4><?= getSpawnTypeLabel($spawn['spawn_type']) ?></h4>
                                </div>
                                <div class="drop-stats">
                                    <div class="drop-stat">
                                        <span class="drop-stat-label">Count:</span>
                                        <span class="drop-stat-value"><?= isset($spawn['count']) && $spawn['count'] ? $spawn['count'] : '1' ?></span>
                                    </div>
                                    <div class="drop-stat">
                                        <span class="drop-stat-label">Respawn:</span>
                                        <span class="drop-stat-value"><?= formatRespawnTime($spawn) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-drops-message">
                        No spawn locations found for this monster.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php getPageFooter(); ?>
