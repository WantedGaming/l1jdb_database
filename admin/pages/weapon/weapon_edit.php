                        <input type="number" id="dmgmodifier" name="dmgmodifier" value="<?php echo htmlspecialchars($weapon['dmgmodifier']); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="safenchant">Safe Enchant Level</label>
                        <input type="number" id="safenchant" name="safenchant" value="<?php echo htmlspecialchars($weapon['safenchant']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="double_dmg_chance">Double Damage Chance</label>
                        <input type="number" id="double_dmg_chance" name="double_dmg_chance" value="<?php echo htmlspecialchars($weapon['double_dmg_chance']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="magicdmgmodifier">Magic Damage Modifier</label>
                        <input type="number" id="magicdmgmodifier" name="magicdmgmodifier" value="<?php echo htmlspecialchars($weapon['magicdmgmodifier']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="canbedmg">Can Be Damaged</label>
                        <input type="number" id="canbedmg" name="canbedmg" value="<?php echo htmlspecialchars($weapon['canbedmg']); ?>">
                    </div>
                </div>
            </div>

            <!-- Class Restrictions -->
            <div class="form-section">
                <h3>👥 Class Restrictions</h3>
                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <input type="checkbox" id="use_royal" name="use_royal" value="1" <?php echo $weapon['use_royal'] ? 'checked' : ''; ?>>
                        <label for="use_royal">Royal</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="use_knight" name="use_knight" value="1" <?php echo $weapon['use_knight'] ? 'checked' : ''; ?>>
                        <label for="use_knight">Knight</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="use_mage" name="use_mage" value="1" <?php echo $weapon['use_mage'] ? 'checked' : ''; ?>>
                        <label for="use_mage">Mage</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="use_elf" name="use_elf" value="1" <?php echo $weapon['use_elf'] ? 'checked' : ''; ?>>
                        <label for="use_elf">Elf</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="use_darkelf" name="use_darkelf" value="1" <?php echo $weapon['use_darkelf'] ? 'checked' : ''; ?>>
                        <label for="use_darkelf">Dark Elf</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="use_dragonknight" name="use_dragonknight" value="1" <?php echo $weapon['use_dragonknight'] ? 'checked' : ''; ?>>
                        <label for="use_dragonknight">Dragon Knight</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="use_illusionist" name="use_illusionist" value="1" <?php echo $weapon['use_illusionist'] ? 'checked' : ''; ?>>
                        <label for="use_illusionist">Illusionist</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="use_warrior" name="use_warrior" value="1" <?php echo $weapon['use_warrior'] ? 'checked' : ''; ?>>
                        <label for="use_warrior">Warrior</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="use_fencer" name="use_fencer" value="1" <?php echo $weapon['use_fencer'] ? 'checked' : ''; ?>>
                        <label for="use_fencer">Fencer</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="use_lancer" name="use_lancer" value="1" <?php echo $weapon['use_lancer'] ? 'checked' : ''; ?>>
                        <label for="use_lancer">Lancer</label>
                    </div>
                </div>
            </div>

            <!-- Stat Bonuses -->
            <div class="form-section">
                <h3>📊 Stat Bonuses</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="add_str">Strength</label>
                        <input type="number" id="add_str" name="add_str" value="<?php echo htmlspecialchars($weapon['add_str']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="add_con">Constitution</label>
                        <input type="number" id="add_con" name="add_con" value="<?php echo htmlspecialchars($weapon['add_con']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="add_dex">Dexterity</label>
                        <input type="number" id="add_dex" name="add_dex" value="<?php echo htmlspecialchars($weapon['add_dex']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="add_int">Intelligence</label>
                        <input type="number" id="add_int" name="add_int" value="<?php echo htmlspecialchars($weapon['add_int']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="add_wis">Wisdom</label>
                        <input type="number" id="add_wis" name="add_wis" value="<?php echo htmlspecialchars($weapon['add_wis']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="add_cha">Charisma</label>
                        <input type="number" id="add_cha" name="add_cha" value="<?php echo htmlspecialchars($weapon['add_cha']); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="add_hp">HP Bonus</label>
                        <input type="number" id="add_hp" name="add_hp" value="<?php echo htmlspecialchars($weapon['add_hp']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="add_mp">MP Bonus</label>
                        <input type="number" id="add_mp" name="add_mp" value="<?php echo htmlspecialchars($weapon['add_mp']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="add_hpr">HP Regen</label>
                        <input type="number" id="add_hpr" name="add_hpr" value="<?php echo htmlspecialchars($weapon['add_hpr']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="add_mpr">MP Regen</label>
                        <input type="number" id="add_mpr" name="add_mpr" value="<?php echo htmlspecialchars($weapon['add_mpr']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="add_sp">SP Bonus</label>
                        <input type="number" id="add_sp" name="add_sp" value="<?php echo htmlspecialchars($weapon['add_sp']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="m_def">Magic Defense</label>
                        <input type="number" id="m_def" name="m_def" value="<?php echo htmlspecialchars($weapon['m_def']); ?>">
                    </div>
                </div>
            </div>

            <!-- Level and Trade Restrictions -->
            <div class="form-section">
                <h3>🔒 Restrictions</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="min_lvl">Minimum Level</label>
                        <input type="number" id="min_lvl" name="min_lvl" value="<?php echo htmlspecialchars($weapon['min_lvl']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="max_lvl">Maximum Level</label>
                        <input type="number" id="max_lvl" name="max_lvl" value="<?php echo htmlspecialchars($weapon['max_lvl']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="bless">Bless</label>
                        <select id="bless" name="bless">
                            <option value="1" <?php echo $weapon['bless'] == 1 ? 'selected' : ''; ?>>Yes</option>
                            <option value="0" <?php echo $weapon['bless'] == 0 ? 'selected' : ''; ?>>No</option>
                        </select>
                    </div>
                </div>
                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <input type="checkbox" id="trade" name="trade" value="1" <?php echo $weapon['trade'] ? 'checked' : ''; ?>>
                        <label for="trade">Tradeable</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="retrieve" name="retrieve" value="1" <?php echo $weapon['retrieve'] ? 'checked' : ''; ?>>
                        <label for="retrieve">Retrievable</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="cant_delete" name="cant_delete" value="1" <?php echo $weapon['cant_delete'] ? 'checked' : ''; ?>>
                        <label for="cant_delete">Cannot Delete</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="cant_sell" name="cant_sell" value="1" <?php echo $weapon['cant_sell'] ? 'checked' : ''; ?>>
                        <label for="cant_sell">Cannot Sell</label>
                    </div>
                </div>
            </div>

            <!-- Advanced Properties -->
            <div class="form-section">
                <h3>⚙️ Advanced Properties</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="Magic_name">Magic Name</label>
                        <input type="text" id="Magic_name" name="Magic_name" value="<?php echo htmlspecialchars($weapon['Magic_name']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="poisonRegist">Poison Resist</label>
                        <select id="poisonRegist" name="poisonRegist">
                            <option value="false" <?php echo $weapon['poisonRegist'] === 'false' ? 'selected' : ''; ?>>False</option>
                            <option value="true" <?php echo $weapon['poisonRegist'] === 'true' ? 'selected' : ''; ?>>True</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-section">
                <div class="btn-group">
                    <button type="submit" class="admin-btn admin-btn-primary admin-btn-large">
                        💾 Update Weapon
                    </button>
                    <a href="<?php echo SITE_URL; ?>/admin/pages/weapon_list.php" class="admin-btn admin-btn-secondary admin-btn-large">
                        ❌ Cancel
                    </a>
                    <a href="<?php echo SITE_URL; ?>/admin/pages/weapon_list.php?action=delete&id=<?php echo $weaponId; ?>" 
                       class="admin-btn admin-btn-danger admin-btn-large"
                       onclick="return confirm('Are you sure you want to delete this weapon? This action cannot be undone.')">
                        🗑️ Delete Weapon
                    </a>
                </div>
            </div>
        </form>
        <?php else: ?>
            <div class="admin-empty">
                <h3>Weapon Not Found</h3>
                <p>The requested weapon could not be found.</p>
                <a href="<?php echo SITE_URL; ?>/admin/pages/weapon_list.php" class="admin-btn admin-btn-primary">
                    Back to Weapon List
                </a>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
// Form validation and enhancement
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.admin-form');
    
    if (form) {
        // Form validation
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

<?php getPageFooter(); ?>="max_lvl" value="<?php echo htmlspecialchars($weapon['max_lvl']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="bless">Bless</label>
                        <select id="bless" name="bless">
                            <option value="1" <?php echo $weapon['bless'] == 1 ? 'selected' : ''; ?>>Yes</option>
                            <option value="0" <?php echo $weapon['bless'] == 0 ? 'selected' : ''; ?>>No</option>
                        </select>
                    </div>
                </div>
                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <input type="checkbox" id="trade" name="trade" value="1" <?php echo $weapon['trade'] ? 'checked' : ''; ?>>
                        <label for="trade">Tradeable</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="retrieve" name="retrieve" value="1" <?php echo $weapon['retrieve'] ? 'checked' : ''; ?>>
                        <label for="retrieve">Retrievable</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="cant_delete" name="cant_delete" value="1" <?php echo $weapon['cant_delete'] ? 'checked' : ''; ?>>
                        <label for="cant_delete">Cannot Delete</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="cant_sell" name="cant_sell" value="1" <?php echo $weapon['cant_sell'] ? 'checked' : ''; ?>>
                        <label for="cant_sell">Cannot Sell</label>
                    </div>
                </div>
            </div>

            <!-- Advanced Properties -->
            <div class="form-section">
                <h3>⚙️ Advanced Properties</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="Magic_name">Magic Name</label>
                        <input type="text" id="Magic_name" name="Magic_name" value="<?php echo htmlspecialchars($weapon['Magic_name']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="poisonRegist">Poison Resist</label>
                        <select id="poisonRegist" name="poisonRegist">
                            <option value="false" <?php echo $weapon['poisonRegist'] === 'false' ? 'selected' : ''; ?>>False</option>
                            <option value="true" <?php echo $weapon['poisonRegist'] === 'true' ? 'selected' : ''; ?>>True</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-section">
                <div class="btn-group">
                    <button type="submit" class="admin-btn admin-btn-primary admin-btn-large">
                        💾 Update Weapon
                    </button>
                    <a href="<?php echo SITE_URL; ?>/admin/pages/weapon_list.php" class="admin-btn admin-btn-secondary admin-btn-large">
                        ❌ Cancel
                    </a>
                    <a href="<?php echo SITE_URL; ?>/admin/pages/weapon_list.php?action=delete&id=<?php echo $weaponId; ?>" 
                       class="admin-btn admin-btn-danger admin-btn-large"
                       onclick="return confirm('Are you sure you want to delete this weapon? This action cannot be undone.')">
                        🗑️ Delete Weapon
                    </a>
                </div>
            </div>
        </form>
        <?php else: ?>
            <div class="admin-empty">
                <h3>Weapon Not Found</h3>
                <p>The requested weapon could not be found.</p>
                <a href="<?php echo SITE_URL; ?>/admin/pages/weapon_list.php" class="admin-btn admin-btn-primary">
                    Back to Weapon List
                </a>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
// Form validation and enhancement
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.admin-form');
    
    if (form) {
        // Form validation
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

<?php getPageFooter(); ?>
