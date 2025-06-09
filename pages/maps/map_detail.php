<?php
require_once dirname(dirname(__DIR__)) . '/includes/config.php';
require_once dirname(dirname(__DIR__)) . '/includes/header.php';

// Get map ID from URL
$mapId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($mapId <= 0) {
    header('Location: map_list.php');
    exit;
}

// Get map data
$sql = "SELECT * FROM mapids WHERE mapid = :mapid";
$stmt = $pdo->prepare($sql);
$stmt->execute([':mapid' => $mapId]);
$map = $stmt->fetch();

if (!$map) {
    header('Location: map_list.php');
    exit;
}

// Function to get zone types
function getZoneTypes($map) {
    $zones = [];
    
    if ($map['beginZone']) $zones[] = 'Beginner Zone';
    if ($map['redKnightZone']) $zones[] = 'Red Knight Zone';
    if ($map['ruunCastleZone']) $zones[] = 'Ruun Castle Zone';
    if ($map['interWarZone']) $zones[] = 'War Zone';
    if ($map['geradBuffZone']) $zones[] = 'Gerad Buff Zone';
    if ($map['growBuffZone']) $zones[] = 'Growth Buff Zone';
    if ($map['dungeon']) $zones[] = 'Dungeon';
    if ($map['underwater']) $zones[] = 'Underwater';
    
    return !empty($zones) ? $zones : ['Normal Zone'];
}

// Function to get map features
function getMapFeatures($map) {
    $features = [];
    
    if ($map['markable']) $features[] = 'Markable';
    if ($map['teleportable']) $features[] = 'Teleportable';
    if ($map['escapable']) $features[] = 'Escapable';
    if ($map['resurrection']) $features[] = 'Resurrection Allowed';
    if ($map['painwand']) $features[] = 'Pain Wand Usable';
    if ($map['take_pets']) $features[] = 'Pets Allowed';
    if ($map['recall_pets']) $features[] = 'Pet Recall';
    if ($map['usable_item']) $features[] = 'Items Usable';
    if ($map['usable_skill']) $features[] = 'Skills Usable';
    if ($map['dominationTeleport']) $features[] = 'Domination Teleport';
    
    return $features;
}

// Function to get map restrictions
function getMapRestrictions($map) {
    $restrictions = [];
    
    if (!$map['markable']) $restrictions[] = 'No Mark';
    if (!$map['teleportable']) $restrictions[] = 'No Teleport';
    if (!$map['escapable']) $restrictions[] = 'No Escape';
    if (!$map['resurrection']) $restrictions[] = 'No Resurrection';
    if (!$map['painwand']) $restrictions[] = 'No Pain Wand';
    if (!$map['take_pets']) $restrictions[] = 'No Pets';
    if (!$map['recall_pets']) $restrictions[] = 'No Pet Recall';
    if (!$map['usable_item']) $restrictions[] = 'No Items';
    if (!$map['usable_skill']) $restrictions[] = 'No Skills';
    if ($map['penalty']) $restrictions[] = 'Death Penalty';
    if ($map['decreaseHp']) $restrictions[] = 'HP Decrease';
    
    return $restrictions;
}

// Function to get spawned monsters
function getMapMonsters($pdo, $mapId) {
    $monsters = [];
    
    // Query normal spawnlist
    $sql = "SELECT DISTINCT s.npc_templateid, n.desc_en, n.spriteId, n.lvl, COUNT(*) as spawn_count
            FROM spawnlist s 
            LEFT JOIN npc n ON s.npc_templateid = n.npcid 
            WHERE s.mapid = :mapid AND n.npcid IS NOT NULL
            GROUP BY s.npc_templateid, n.desc_en, n.spriteId, n.lvl
            ORDER BY n.lvl ASC, n.desc_en ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':mapid' => $mapId]);
    $normalSpawns = $stmt->fetchAll();
    foreach ($normalSpawns as $spawn) {
        $spawn['spawn_type'] = 'normal';
        $monsters[] = $spawn;
    }
    
    // Query boss spawnlist
    $sql = "SELECT DISTINCT sb.npcid as npc_templateid, n.desc_en, n.spriteId, n.lvl, COUNT(*) as spawn_count
            FROM spawnlist_boss sb 
            LEFT JOIN npc n ON sb.npcid = n.npcid 
            WHERE sb.spawnMapId = :mapid AND n.npcid IS NOT NULL
            GROUP BY sb.npcid, n.desc_en, n.spriteId, n.lvl
            ORDER BY n.lvl ASC, n.desc_en ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':mapid' => $mapId]);
    $bossSpawns = $stmt->fetchAll();
    foreach ($bossSpawns as $spawn) {
        $spawn['spawn_type'] = 'boss';
        $monsters[] = $spawn;
    }
    
    // Query world war spawnlist
    $sql = "SELECT DISTINCT sw.npc_id as npc_templateid, n.desc_en, n.spriteId, n.lvl, COUNT(*) as spawn_count
            FROM spawnlist_worldwar sw 
            LEFT JOIN npc n ON sw.npc_id = n.npcid 
            WHERE sw.mapid = :mapid AND n.npcid IS NOT NULL
            GROUP BY sw.npc_id, n.desc_en, n.spriteId, n.lvl
            ORDER BY n.lvl ASC, n.desc_en ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':mapid' => $mapId]);
    $worldwarSpawns = $stmt->fetchAll();
    foreach ($worldwarSpawns as $spawn) {
        $spawn['spawn_type'] = 'worldwar';
        $monsters[] = $spawn;
    }
    
    // Query unicorn temple spawnlist
    $sql = "SELECT DISTINCT su.npc_id as npc_templateid, n.desc_en, n.spriteId, n.lvl, COUNT(*) as spawn_count
            FROM spawnlist_unicorntemple su 
            LEFT JOIN npc n ON su.npc_id = n.npcid 
            WHERE su.mapId = :mapid AND n.npcid IS NOT NULL
            GROUP BY su.npc_id, n.desc_en, n.spriteId, n.lvl
            ORDER BY n.lvl ASC, n.desc_en ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':mapid' => $mapId]);
    $unicornSpawns = $stmt->fetchAll();
    foreach ($unicornSpawns as $spawn) {
        $spawn['spawn_type'] = 'unicorntemple';
        $monsters[] = $spawn;
    }
    
    // Query Ruun spawnlist
    $sql = "SELECT DISTINCT sr.npcId as npc_templateid, n.desc_en, n.spriteId, n.lvl, COUNT(*) as spawn_count
            FROM spawnlist_ruun sr 
            LEFT JOIN npc n ON sr.npcId = n.npcid 
            WHERE sr.mapId = :mapid AND n.npcid IS NOT NULL
            GROUP BY sr.npcId, n.desc_en, n.spriteId, n.lvl
            ORDER BY n.lvl ASC, n.desc_en ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':mapid' => $mapId]);
    $ruunSpawns = $stmt->fetchAll();
    foreach ($ruunSpawns as $spawn) {
        $spawn['spawn_type'] = 'ruun';
        $monsters[] = $spawn;
    }
    
    // Query other spawnlist
    $sql = "SELECT DISTINCT so.npc_id as npc_templateid, n.desc_en, n.spriteId, n.lvl, COUNT(*) as spawn_count
            FROM spawnlist_other so 
            LEFT JOIN npc n ON so.npc_id = n.npcid 
            WHERE so.mapId = :mapid AND n.npcid IS NOT NULL
            GROUP BY so.npc_id, n.desc_en, n.spriteId, n.lvl
            ORDER BY n.lvl ASC, n.desc_en ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':mapid' => $mapId]);
    $otherSpawns = $stmt->fetchAll();
    foreach ($otherSpawns as $spawn) {
        $spawn['spawn_type'] = 'other';
        $monsters[] = $spawn;
    }
    
    // Query indun spawnlist
    $sql = "SELECT DISTINCT si.npc_id as npc_templateid, n.desc_en, n.spriteId, n.lvl, COUNT(*) as spawn_count
            FROM spawnlist_indun si 
            LEFT JOIN npc n ON si.npc_id = n.npcid 
            WHERE si.mapId = :mapid AND n.npcid IS NOT NULL
            GROUP BY si.npc_id, n.desc_en, n.spriteId, n.lvl
            ORDER BY n.lvl ASC, n.desc_en ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':mapid' => $mapId]);
    $indunSpawns = $stmt->fetchAll();
    foreach ($indunSpawns as $spawn) {
        $spawn['spawn_type'] = 'indun';
        $monsters[] = $spawn;
    }
    
    return $monsters;
}

// Function to get spawn type label
function getSpawnTypeLabel($type) {
    $labels = [
        'normal' => 'Normal Spawn',
        'boss' => 'Boss Spawn',
        'worldwar' => 'World War',
        'indun' => 'Instance Dungeon',
        'other' => 'Special Event',
        'unicorntemple' => 'Unicorn Temple',
        'ruun' => 'Ruun Castle'
    ];
    return $labels[$type] ?? 'Unknown';
}

// Get monster data
$monsters = getMapMonsters($pdo, $mapId);

// Call page header with map name
$mapName = $map['locationname'] ?: $map['desc_kr'] ?: 'Map ' . $map['mapid'];
getPageHeader($mapName);

// Create map description for hero
$heroText = 'Map ID: ' . $map['mapid'];
if ($map['dungeon']) {
    $heroText .= ' - Dungeon';
} else {
    $heroText .= ' - Open World';
}

// Render hero section
renderHero('maps', $mapName, $heroText);
?>

<main>
    <div class="main">
        <div class="container">
            <!-- Back Navigation -->
            <div class="back-nav">
                <a href="map_list.php" class="back-btn">&larr; Back to Maps</a>
            </div>
            
            <!-- Map Image Row -->
            <div class="weapon-detail-row">
                <!-- Full Width Image -->
                <div class="weapon-image-col" style="grid-column: 1 / -1; max-width: none;">
                    <div class="weapon-image-container">
                        <img src="<?= SITE_URL ?>/assets/img/icons/<?= $map['pngId'] ?: 'default-map' ?>.png" 
                             alt="<?= htmlspecialchars($mapName) ?>" 
                             onerror="this.src='<?= SITE_URL ?>/assets/img/icons/<?= $map['pngId'] ?: 'default-map' ?>.jpeg'; this.onerror=function(){this.src='<?= SITE_URL ?>/assets/img/placeholders/noimage.png';}"
                             class="weapon-main-image"  style="max-width: 1600px; max-height: 800px;">
                    </div>
                </div>
            </div>
            
            <!-- Map Information and Zone Properties Row -->
            <div class="weapon-detail-row" style="margin-top: 2rem;">
                <!-- Column 1: Map Information -->
                <div class="weapon-info-col">
                    <div class="weapon-basic-info">
                        <h2>Map Information</h2>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>English Name:</label>
                                <span><?= htmlspecialchars($map['locationname'] ?: 'N/A') ?></span>
                            </div>
                            <div class="info-item">
                                <label>Map Type:</label>
                                <span><?= $map['dungeon'] ? 'Dungeon' : 'Open World' ?></span>
                            </div>
                            <?php if ($map['startX'] || $map['endX'] || $map['startY'] || $map['endY']): ?>
                            <div class="info-item">
                                <label>Coordinates:</label>
                                <span><?= $map['startX'] ?>,<?= $map['startY'] ?> to <?= $map['endX'] ?>,<?= $map['endY'] ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($map['monster_amount'] > 0): ?>
                            <div class="info-item">
                                <label>Monster Amount:</label>
                                <span><?= $map['monster_amount'] ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($map['drop_rate'] > 0): ?>
                            <div class="info-item">
                                <label>Drop Rate Modifier:</label>
                                <span><?= $map['drop_rate'] ?>x</span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Column 2: Zone Properties -->
                <div class="weapon-info-col">
                    <div class="weapon-basic-info">
                        <h2>Zone Properties</h2>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Zone Types:</label>
                                <span><?= implode(', ', getZoneTypes($map)) ?></span>
                            </div>
                            <?php if ($map['dmgModiPc2Npc'] != 0): ?>
                            <div class="info-item">
                                <label>PC → NPC Damage:</label>
                                <span><?= $map['dmgModiPc2Npc'] > 0 ? '+' : '' ?><?= $map['dmgModiPc2Npc'] ?>%</span>
                            </div>
                            <?php endif; ?>
                            <?php if ($map['dmgModiNpc2Pc'] != 0): ?>
                            <div class="info-item">
                                <label>NPC → PC Damage:</label>
                                <span><?= $map['dmgModiNpc2Pc'] > 0 ? '+' : '' ?><?= $map['dmgModiNpc2Pc'] ?>%</span>
                            </div>
                            <?php endif; ?>
                            <?php if ($map['interKind'] != 0): ?>
                            <div class="info-item">
                                <label>Interaction Kind:</label>
                                <span><?= $map['interKind'] ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($map['cloneStart'] || $map['cloneEnd']): ?>
                            <div class="info-item">
                                <label>Clone Range:</label>
                                <span><?= $map['cloneStart'] ?> - <?= $map['cloneEnd'] ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($map['script']): ?>
                            <div class="info-item">
                                <label>Script:</label>
                                <span><?= htmlspecialchars($map['script']) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Features and Restrictions Section -->
            <div class="weapon-section">
                <h2>Map Features & Restrictions</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Allowed Features</h3>
                        <div class="stat-values">
                            <?php 
                            $features = getMapFeatures($map);
                            if (!empty($features)): ?>
                                <?php foreach ($features as $feature): ?>
                                <div class="stat-item">
                                    <span class="stat-value" style="color: #2ecc71;">✓ <?= $feature ?></span>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="stat-item">
                                    <span class="stat-value" style="color: #e74c3c;">No features allowed</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Restrictions</h3>
                        <div class="stat-values">
                            <?php 
                            $restrictions = getMapRestrictions($map);
                            if (!empty($restrictions)): ?>
                                <?php foreach ($restrictions as $restriction): ?>
                                <div class="stat-item">
                                    <span class="stat-value" style="color: #e74c3c;">✗ <?= $restriction ?></span>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="stat-item">
                                    <span class="stat-value" style="color: #2ecc71;">No restrictions</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Monsters Section -->
            <div class="weapon-section">
                <h2>Monsters</h2>
                <?php if (!empty($monsters)): ?>
                    <div class="dropped-by-grid">
                        <?php foreach ($monsters as $monster): ?>
                            <div class="monster-card">
                                <div class="monster-info">
                                    <div class="monster-image">
                                        <img src="<?= SITE_URL ?>/assets/img/icons/ms<?= $monster['spriteId'] ?>.png" 
                                             alt="<?= htmlspecialchars(cleanDescriptionPrefix($monster['desc_en'])) ?>"
                                             onerror="this.src='<?= SITE_URL ?>/assets/img/icons/ms<?= $monster['spriteId'] ?>.gif'; this.onerror=function(){this.src='<?= SITE_URL ?>/assets/img/placeholders/monsters.png';}">
                                    </div>
                                    <div class="monster-details">
                                        <h4>
                                            <a href="../monsters/monster_detail.php?id=<?= $monster['npc_templateid'] ?>" 
                                               class="weapon-link" 
                                               title="<?= htmlspecialchars(cleanDescriptionPrefix($monster['desc_en'])) ?> - Level <?= $monster['lvl'] ?> Monster">
                                                <?= htmlspecialchars(cleanDescriptionPrefix($monster['desc_en'])) ?>
                                            </a>
                                        </h4>
                                        <div class="monster-level">Level <?= $monster['lvl'] ?></div>
                                    </div>
                                </div>
                                <div class="drop-stats">
                                    <div class="drop-stat">
                                        <span class="drop-stat-label">Spawn Type:</span>
                                        <span class="drop-stat-value"><?= getSpawnTypeLabel($monster['spawn_type']) ?></span>
                                    </div>
                                    <div class="drop-stat">
                                        <span class="drop-stat-label">Spawn Count:</span>
                                        <span class="drop-stat-value"><?= $monster['spawn_count'] ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-drops-message">
                        No monsters spawn on this map.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php getPageFooter(); ?>
