<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../includes/header.php';

// Get weapon ID from URL
$weaponId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $weaponId > 0) {
    try {
        // Prepare update query
        $sql = "UPDATE weapon SET 
                desc_en = ?, desc_kr = ?, iconId = ?, type = ?, 
                itemGrade = ?, material = ?, weight = ?, bless = ?,
                dmg_small = ?, dmg_large = ?, hitmodifier = ?, dmgmodifier = ?,
                safenchant = ?, double_dmg_chance = ?, magicdmgmodifier = ?, canbedmg = ?,
                use_royal = ?, use_knight = ?, use_mage = ?, use_elf = ?,
                use_darkelf = ?, use_dragonknight = ?, use_illusionist = ?, use_warrior = ?,
                use_fencer = ?, use_lancer = ?, add_str = ?, add_con = ?,
                add_dex = ?, add_int = ?, add_wis = ?, add_cha = ?,
                add_hp = ?, add_mp = ?, add_hpr = ?, add_mpr = ?,
                add_sp = ?, m_def = ?, min_lvl = ?, max_lvl = ?,
                trade = ?, retrieve = ?, cant_delete = ?, cant_sell = ?,
                Magic_name = ?, poisonRegist = ?
                WHERE item_id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['desc_en'], $_POST['desc_kr'], $_POST['iconId'], $_POST['type'],
            $_POST['itemGrade'], $_POST['material'], $_POST['weight'], $_POST['bless'],
            $_POST['dmg_small'], $_POST['dmg_large'], $_POST['hitmodifier'], $_POST['dmgmodifier'],
            $_POST['safenchant'], $_POST['double_dmg_chance'], $_POST['magicdmgmodifier'], $_POST['canbedmg'],
            isset($_POST['use_royal']) ? 1 : 0, isset($_POST['use_knight']) ? 1 : 0, 
            isset($_POST['use_mage']) ? 1 : 0, isset($_POST['use_elf']) ? 1 : 0,
            isset($_POST['use_darkelf']) ? 1 : 0, isset($_POST['use_dragonknight']) ? 1 : 0, 
            isset($_POST['use_illusionist']) ? 1 : 0, isset($_POST['use_warrior']) ? 1 : 0,
            isset($_POST['use_fencer']) ? 1 : 0, isset($_POST['use_lancer']) ? 1 : 0,
            $_POST['add_str'], $_POST['add_con'], $_POST['add_dex'], $_POST['add_int'],
            $_POST['add_wis'], $_POST['add_cha'], $_POST['add_hp'], $_POST['add_mp'],
            $_POST['add_hpr'], $_POST['add_mpr'], $_POST['add_sp'], $_POST['m_def'],
            $_POST['min_lvl'], $_POST['max_lvl'], isset($_POST['trade']) ? 1 : 0, 
            isset($_POST['retrieve']) ? 1 : 0, isset($_POST['cant_delete']) ? 1 : 0, 
            isset($_POST['cant_sell']) ? 1 : 0, $_POST['Magic_name'], $_POST['poisonRegist'],
            $weaponId
        ]);
        
        logAdminActivity('UPDATE', 'weapon', $weaponId, 'Updated weapon: ' . $_POST['desc_en']);
        
        $message = 'Weapon updated successfully!';
        $messageType = 'success';
    } catch (PDOException $e) {
        $message = 'Error updating weapon: ' . $e->getMessage();
        $messageType = 'error';
    }
}

// Get weapon data
$weapon = null;
if ($weaponId > 0) {
    $sql = "SELECT * FROM weapon WHERE item_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$weaponId]);
    $weapon = $stmt->fetch();
}
?>

        <!-- Admin Header -->
        <div class="admin-header">
            <h1>✏️ Edit Weapon</h1>
            <div class="admin-header-actions">
                <a href="<?php echo SITE_URL; ?>/admin/pages/weapon/weapon_list.php" class="admin-btn admin-btn-secondary">
                    ← Back to List
                </a>
                <a href="<?php echo SITE_URL; ?>/pages/weapons/weapon_detail.php?id=<?php echo $weaponId; ?>" class="admin-btn admin-btn-info" target="_blank">
                    👁️ View Public
                </a>
            </div>
        </div>

        <!-- Breadcrumb -->
        <nav class="admin-breadcrumb">
            <ul class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/admin/">Admin</a></li>
                <li class="breadcrumb-separator">›</li>
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/admin/pages/weapon/weapon_list.php">Weapon Management</a></li>
                <li class="breadcrumb-separator">›</li>
                <li class="breadcrumb-item">Edit Weapon</li>
            </ul>
        </nav>

        <?php if (isset($message)): ?>
            <div class="admin-message admin-message-<?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($weapon): ?>
        <form method="POST" class="admin-form">
            <!-- Image Preview and Basic Information Row -->
            <div class="edit-cards-row">
                <!-- Image Preview Card -->
                <div class="image-preview-card">
                    <div class="image-preview-container">
                        <img id="weaponPreviewImage" 
                             src="<?php echo SITE_URL; ?>/assets/img/icons/<?php echo $weapon['iconId']; ?>.png" 
                             alt="Weapon Icon" 
                             class="preview-image"
                             onerror="this.style.display='none'; document.getElementById('imagePlaceholder').style.display='flex';">
                        <div id="imagePlaceholder" class="image-placeholder" style="display: none;">
                            No Image
                        </div>
                        
                        <div class="image-info">
                            <div class="icon-id">Icon ID: <?php echo $weapon['iconId']; ?></div>
                            <div class="icon-path">icons/<?php echo $weapon['iconId']; ?>.png</div>
                        </div>
                        
                        <div class="icon-input-group">
                            <label for="iconId">Change Icon ID</label>
                            <input type="number" id="iconId" name="iconId" value="<?php echo htmlspecialchars($weapon['iconId']); ?>" min="0">
                        </div>
                    </div>
                </div>

                <!-- Basic Information Card -->
                <div class="basic-info-card">
                    <h3>📝 Basic Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="item_id">Item ID</label>
                            <input type="text" id="item_id" value="<?php echo htmlspecialchars($weapon['item_id']); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="desc_en">English Name *</label>
                            <input type="text" id="desc_en" name="desc_en" value="<?php echo htmlspecialchars($weapon['desc_en']); ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="desc_kr">Korean Name</label>
                            <input type="text" id="desc_kr" name="desc_kr" value="<?php echo htmlspecialchars($weapon['desc_kr']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="weight">Weight</label>
                            <input type="number" id="weight" name="weight" value="<?php echo htmlspecialchars($weapon['weight']); ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="type">Type</label>
                            <select id="type" name="type">
                                <option value="SWORD" <?php echo $weapon['type'] === 'SWORD' ? 'selected' : ''; ?>>Sword</option>
                                <option value="DAGGER" <?php echo $weapon['type'] === 'DAGGER' ? 'selected' : ''; ?>>Dagger</option>
                                <option value="TOHAND_SWORD" <?php echo $weapon['type'] === 'TOHAND_SWORD' ? 'selected' : ''; ?>>Two-Handed Sword</option>
                                <option value="BOW" <?php echo $weapon['type'] === 'BOW' ? 'selected' : ''; ?>>Bow</option>
                                <option value="SPEAR" <?php echo $weapon['type'] === 'SPEAR' ? 'selected' : ''; ?>>Spear</option>
                                <option value="BLUNT" <?php echo $weapon['type'] === 'BLUNT' ? 'selected' : ''; ?>>Blunt</option>
                                <option value="STAFF" <?php echo $weapon['type'] === 'STAFF' ? 'selected' : ''; ?>>Staff</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="itemGrade">Grade</label>
                            <select id="itemGrade" name="itemGrade">
                                <option value="NORMAL" <?php echo $weapon['itemGrade'] === 'NORMAL' ? 'selected' : ''; ?>>Normal</option>
                                <option value="ADVANC" <?php echo $weapon['itemGrade'] === 'ADVANC' ? 'selected' : ''; ?>>Advanced</option>
                                <option value="RARE" <?php echo $weapon['itemGrade'] === 'RARE' ? 'selected' : ''; ?>>Rare</option>
                                <option value="HERO" <?php echo $weapon['itemGrade'] === 'HERO' ? 'selected' : ''; ?>>Hero</option>
                                <option value="LEGEND" <?php echo $weapon['itemGrade'] === 'LEGEND' ? 'selected' : ''; ?>>Legendary</option>
                                <option value="MYTH" <?php echo $weapon['itemGrade'] === 'MYTH' ? 'selected' : ''; ?>>Mythic</option>
                                <option value="ONLY" <?php echo $weapon['itemGrade'] === 'ONLY' ? 'selected' : ''; ?>>Only</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="material">Material</label>
                            <select id="material" name="material">
                                <option value="NONE(-)" <?php echo $weapon['material'] === 'NONE(-)' ? 'selected' : ''; ?>>None</option>
                                <option value="WOOD(나무)" <?php echo $weapon['material'] === 'WOOD(나무)' ? 'selected' : ''; ?>>Wood</option>
                                <option value="IRON(철)" <?php echo $weapon['material'] === 'IRON(철)' ? 'selected' : ''; ?>>Iron</option>
                                <option value="SILVER(은)" <?php echo $weapon['material'] === 'SILVER(은)' ? 'selected' : ''; ?>>Silver</option>
                                <option value="MITHRIL(미스릴)" <?php echo $weapon['material'] === 'MITHRIL(미스릴)' ? 'selected' : ''; ?>>Mithril</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="bless">Bless</label>
                            <select id="bless" name="bless">
                                <option value="1" <?php echo $weapon['bless'] == 1 ? 'selected' : ''; ?>>Yes</option>
                                <option value="0" <?php echo $weapon['bless'] == 0 ? 'selected' : ''; ?>>No</option>
                            </select>
                        </div>
                    </div>

                    <!-- Combat Stats in Basic Info Card -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="dmg_small">Damage (Small)</label>
                            <input type="number" id="dmg_small" name="dmg_small" value="<?php echo htmlspecialchars($weapon['dmg_small']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="dmg_large">Damage (Large)</label>
                            <input type="number" id="dmg_large" name="dmg_large" value="<?php echo htmlspecialchars($weapon['dmg_large']); ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="hitmodifier">Hit Modifier</label>
                            <input type="number" id="hitmodifier" name="hitmodifier" value="<?php echo htmlspecialchars($weapon['hitmodifier']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="safenchant">Safe Enchant</label>
                            <input type="number" id="safenchant" name="safenchant" value="<?php echo htmlspecialchars($weapon['safenchant']); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden fields for other stats -->
            <input type="hidden" name="dmgmodifier" value="<?php echo htmlspecialchars($weapon['dmgmodifier']); ?>">
            <input type="hidden" name="double_dmg_chance" value="<?php echo htmlspecialchars($weapon['double_dmg_chance']); ?>">
            <input type="hidden" name="magicdmgmodifier" value="<?php echo htmlspecialchars($weapon['magicdmgmodifier']); ?>">
            <input type="hidden" name="canbedmg" value="<?php echo htmlspecialchars($weapon['canbedmg']); ?>">
            <input type="hidden" name="use_royal" value="<?php echo $weapon['use_royal']; ?>">
            <input type="hidden" name="use_knight" value="<?php echo $weapon['use_knight']; ?>">
            <input type="hidden" name="use_mage" value="<?php echo $weapon['use_mage']; ?>">
            <input type="hidden" name="use_elf" value="<?php echo $weapon['use_elf']; ?>">
            <input type="hidden" name="use_darkelf" value="<?php echo $weapon['use_darkelf']; ?>">
            <input type="hidden" name="use_dragonknight" value="<?php echo $weapon['use_dragonknight']; ?>">
            <input type="hidden" name="use_illusionist" value="<?php echo $weapon['use_illusionist']; ?>">
            <input type="hidden" name="use_warrior" value="<?php echo $weapon['use_warrior']; ?>">
            <input type="hidden" name="use_fencer" value="<?php echo $weapon['use_fencer']; ?>">
            <input type="hidden" name="use_lancer" value="<?php echo $weapon['use_lancer']; ?>">
            <input type="hidden" name="add_str" value="<?php echo $weapon['add_str']; ?>">
            <input type="hidden" name="add_con" value="<?php echo $weapon['add_con']; ?>">
            <input type="hidden" name="add_dex" value="<?php echo $weapon['add_dex']; ?>">
            <input type="hidden" name="add_int" value="<?php echo $weapon['add_int']; ?>">
            <input type="hidden" name="add_wis" value="<?php echo $weapon['add_wis']; ?>">
            <input type="hidden" name="add_cha" value="<?php echo $weapon['add_cha']; ?>">
            <input type="hidden" name="add_hp" value="<?php echo $weapon['add_hp']; ?>">
            <input type="hidden" name="add_mp" value="<?php echo $weapon['add_mp']; ?>">
            <input type="hidden" name="add_hpr" value="<?php echo $weapon['add_hpr']; ?>">
            <input type="hidden" name="add_mpr" value="<?php echo $weapon['add_mpr']; ?>">
            <input type="hidden" name="add_sp" value="<?php echo $weapon['add_sp']; ?>">
            <input type="hidden" name="m_def" value="<?php echo $weapon['m_def']; ?>">
            <input type="hidden" name="min_lvl" value="<?php echo $weapon['min_lvl']; ?>">
            <input type="hidden" name="max_lvl" value="<?php echo $weapon['max_lvl']; ?>">
            <input type="hidden" name="trade" value="<?php echo $weapon['trade']; ?>">
            <input type="hidden" name="retrieve" value="<?php echo $weapon['retrieve']; ?>">
            <input type="hidden" name="cant_delete" value="<?php echo $weapon['cant_delete']; ?>">
            <input type="hidden" name="cant_sell" value="<?php echo $weapon['cant_sell']; ?>">
            <input type="hidden" name="Magic_name" value="<?php echo htmlspecialchars($weapon['Magic_name']); ?>">
            <input type="hidden" name="poisonRegist" value="<?php echo $weapon['poisonRegist']; ?>">

            <!-- Form Actions -->
            <div class="form-section">
                <div class="btn-group">
                    <button type="submit" class="admin-btn admin-btn-primary admin-btn-large">💾 Update Weapon</button>
                    <a href="<?php echo SITE_URL; ?>/admin/pages/weapon/weapon_list.php" class="admin-btn admin-btn-secondary admin-btn-large">❌ Cancel</a>
                </div>
            </div>
        </form>
        <?php else: ?>
        <div class="admin-empty">
            <h3>Weapon Not Found</h3>
            <p>The requested weapon could not be found.</p>
            <a href="<?php echo SITE_URL; ?>/admin/pages/weapon/weapon_list.php" class="admin-btn admin-btn-primary">Back to Weapon List</a>
        </div>
        <?php endif; ?>
    </div>
</main>

<script>
// Image preview functionality
document.addEventListener('DOMContentLoaded', function() {
    const iconIdInput = document.getElementById('iconId');
    const previewImage = document.getElementById('weaponPreviewImage');
    const placeholder = document.getElementById('imagePlaceholder');
    const iconIdDisplay = document.querySelector('.icon-id');
    const iconPathDisplay = document.querySelector('.icon-path');
    
    // Update image preview when icon ID changes
    function updateImagePreview() {
        const iconId = iconIdInput.value || 0;
        const imagePath = `<?php echo SITE_URL; ?>/assets/img/icons/${iconId}.png`;
        
        previewImage.src = imagePath;
        previewImage.style.display = 'block';
        placeholder.style.display = 'none';
        
        iconIdDisplay.textContent = `Icon ID: ${iconId}`;
        iconPathDisplay.textContent = `icons/${iconId}.png`;
        
        // Handle image load error
        previewImage.onerror = function() {
            this.style.display = 'none';
            placeholder.style.display = 'flex';
        };
    }
    
    // Listen for input changes
    iconIdInput.addEventListener('input', updateImagePreview);
    iconIdInput.addEventListener('change', updateImagePreview);
    
    // Form validation
    const form = document.querySelector('.admin-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const descEn = document.querySelector('#desc_en').value;
            
            if (!descEn) {
                e.preventDefault();
                alert('English name is required.');
                return false;
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
