<?php
require_once dirname(dirname(dirname(__DIR__))) . '/includes/config.php';
require_once dirname(dirname(dirname(__DIR__))) . '/admin/includes/header.php';

// Get next available NPC ID
$stmt = $pdo->query("SELECT MAX(npcid) + 1 as next_id FROM npc");
$nextId = $stmt->fetchColumn() ?: 1;

// Helper functions
function getUndeadTypes() {
    return [
        'NONE' => 'Normal',
        'UNDEAD' => 'Undead',
        'DEMON' => 'Demon',
        'UNDEAD_BOSS' => 'Undead Boss',
        'DRANIUM' => 'Dranium'
    ];
}

function getImplementations() {
    return [
        'L1Monster' => 'Monster',
        'L1BlackKnight' => 'Black Knight',
        'L1Doppelganger' => 'Doppelganger',
        'L1GuardianTower' => 'Guardian Tower',
        'L1Merchant' => 'Merchant',
        'L1Npc' => 'NPC',
        'L1TeleporterNpc' => 'Teleporter'
    ];
}
?>

<div class="admin-content-wrapper">
    <div class="admin-header">
        <h1>Add New Monster</h1>
        <div class="admin-header-actions">
            <a href="monster_list.php" class="admin-btn admin-btn-secondary">
                <span>←</span> Back to Monster List
            </a>
        </div>
    </div>

    <div class="admin-breadcrumb">
        <ul class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="/l1jdb_database/admin/">Admin Dashboard</a></li>
            <li class="breadcrumb-separator">/</li>
            <li class="breadcrumb-item"><a href="monster_list.php">Monster Management</a></li>
            <li class="breadcrumb-separator">/</li>
            <li class="breadcrumb-item">Add Monster</li>
        </ul>
    </div>

    <form action="monster_process.php" method="POST" class="admin-form">
        <input type="hidden" name="action" value="add">
        
        <div class="form-progress">
            <div class="form-progress-bar" style="width: 0%" id="progressBar"></div>
        </div>

        <div class="form-tabs">
            <button type="button" class="form-tab active" data-tab="basic">Basic Info</button>
            <button type="button" class="form-tab" data-tab="stats">Statistics</button>
            <button type="button" class="form-tab" data-tab="advanced">Advanced</button>
        </div>

        <div class="form-tab-content active" id="basic-tab">
            <div class="field-group">
                <h3>Basic Information</h3>
                <div class="form-grid-3">
                    <div class="form-group">
                        <label for="npcid">NPC ID *</label>
                        <input type="number" id="npcid" name="npcid" value="<?= $nextId ?>" required min="1">
                    </div>
                    <div class="form-group">
                        <label for="spriteId">Sprite ID *</label>
                        <input type="number" id="spriteId" name="spriteId" value="0" required min="0">
                    </div>
                    <div class="form-group">
                        <label for="impl">Implementation *</label>
                        <select id="impl" name="impl" required>
                            <?php foreach (getImplementations() as $value => $label): ?>
                                <option value="<?= $value ?>" <?= $value === 'L1Monster' ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="desc_en">English Name *</label>
                        <input type="text" id="desc_en" name="desc_en" required maxlength="100" 
                               placeholder="Enter monster name in English">
                    </div>
                    <div class="form-group">
                        <label for="desc_kr">Korean Name</label>
                        <input type="text" id="desc_kr" name="desc_kr" maxlength="45" 
                               placeholder="Enter monster name in Korean">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-tab-content" id="stats-tab">
            <div class="field-group">
                <h3>Core Statistics</h3>
                <div class="form-grid-3">
                    <div class="form-group">
                        <label for="lvl">Level *</label>
                        <input type="number" id="lvl" name="lvl" value="1" required min="1" max="100">
                    </div>
                    <div class="form-group">
                        <label for="hp">Hit Points *</label>
                        <input type="number" id="hp" name="hp" value="100" required min="1">
                    </div>
                    <div class="form-group">
                        <label for="mp">Magic Points</label>
                        <input type="number" id="mp" name="mp" value="0" min="0">
                    </div>
                </div>
                
                <div class="form-grid-3">
                    <div class="form-group">
                        <label for="ac">Armor Class</label>
                        <input type="number" id="ac" name="ac" value="0">
                    </div>
                    <div class="form-group">
                        <label for="exp">Experience Points</label>
                        <input type="number" id="exp" name="exp" value="0" min="0">
                    </div>
                    <div class="form-group">
                        <label for="mr">Magic Resistance</label>
                        <input type="number" id="mr" name="mr" value="0" min="0" max="255">
                    </div>
                </div>
            </div>
            
            <div class="field-group stat-group-positive">
                <h3>Attribute Statistics</h3>
                <div class="form-grid-5">
                    <div class="form-group">
                        <label for="str">Strength</label>
                        <input type="number" id="str" name="str" value="0" min="0" max="255">
                    </div>
                    <div class="form-group">
                        <label for="con">Constitution</label>
                        <input type="number" id="con" name="con" value="0" min="0" max="255">
                    </div>
                    <div class="form-group">
                        <label for="dex">Dexterity</label>
                        <input type="number" id="dex" name="dex" value="0" min="0" max="255">
                    </div>
                    <div class="form-group">
                        <label for="wis">Wisdom</label>
                        <input type="number" id="wis" name="wis" value="0" min="0" max="255">
                    </div>
                    <div class="form-group">
                        <label for="intel">Intelligence</label>
                        <input type="number" id="intel" name="intel" value="0" min="0" max="255">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-tab-content" id="advanced-tab">
            <div class="field-group">
                <h3>Monster Properties</h3>
                <div class="form-grid-3">
                    <div class="form-group">
                        <label for="undead">Undead Type</label>
                        <select id="undead" name="undead">
                            <?php foreach (getUndeadTypes() as $value => $label): ?>
                                <option value="<?= $value ?>" <?= $value === 'NONE' ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="is_agro">Aggressive</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="is_agro" name="is_agro" value="true">
                            <span class="toggle-slider"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="is_bossmonster">Boss Monster</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="is_bossmonster" name="is_bossmonster" value="true">
                            <span class="toggle-slider"></span>
                        </div>
                    </div>
                </div>
                
                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="alignment">Alignment</label>
                        <input type="number" id="alignment" name="alignment" value="0">
                    </div>
                    <div class="form-group">
                        <label for="karma">Karma</label>
                        <input type="number" id="karma" name="karma" value="0">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="admin-btn admin-btn-primary admin-btn-large">
                Create Monster
            </button>
            <a href="monster_list.php" class="admin-btn admin-btn-secondary admin-btn-large">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.form-tab');
    const tabContents = document.querySelectorAll('.form-tab-content');
    const progressBar = document.getElementById('progressBar');
    
    function updateProgress() {
        const activeTabIndex = Array.from(tabs).findIndex(tab => tab.classList.contains('active'));
        const progress = ((activeTabIndex + 1) / tabs.length) * 100;
        progressBar.style.width = progress + '%';
    }
    
    tabs.forEach((tab) => {
        tab.addEventListener('click', function() {
            tabs.forEach(t => t.classList.remove('active'));
            tabContents.forEach(tc => tc.classList.remove('active'));
            
            tab.classList.add('active');
            document.getElementById(tab.dataset.tab + '-tab').classList.add('active');
            
            updateProgress();
        });
    });
    
    updateProgress();
    
    const spriteIdField = document.getElementById('spriteId');
    const descEnField = document.getElementById('desc_en');
    
    spriteIdField.addEventListener('change', function() {
        if (!descEnField.value && this.value) {
            descEnField.value = 'Monster_' + this.value;
        }
    });
    
    const levelField = document.getElementById('lvl');
    const hpField = document.getElementById('hp');
    const expField = document.getElementById('exp');
    
    levelField.addEventListener('change', function() {
        const level = parseInt(this.value);
        if (level > 0) {
            if (!hpField.value || hpField.value == 100) {
                hpField.value = Math.max(100, level * 50);
            }
            if (!expField.value || expField.value == 0) {
                expField.value = Math.max(10, level * level * 5);
            }
        }
    });
});
</script>

<?php require_once '../../includes/footer.php'; ?>
