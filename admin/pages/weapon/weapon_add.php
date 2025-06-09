="add_sp" value="<?php echo htmlspecialchars($weapon['add_sp']); ?>">
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
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="haste_item">Haste Item</label>
                        <input type="number" id="haste_item" name="haste_item" value="<?php echo htmlspecialchars($weapon['haste_item']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="PVPDamage">PVP Damage</label>
                        <input type="number" id="PVPDamage" name="PVPDamage" value="<?php echo htmlspecialchars($weapon['PVPDamage']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="shortCritical">Short Critical</label>
                        <input type="number" id="shortCritical" name="shortCritical" value="<?php echo htmlspecialchars($weapon['shortCritical']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="longCritical">Long Critical</label>
                        <input type="number" id="longCritical" name="longCritical" value="<?php echo htmlspecialchars($weapon['longCritical']); ?>">
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-section">
                <div class="btn-group">
                    <button type="submit" class="admin-btn admin-btn-primary admin-btn-large">
                        💾 Add Weapon
                    </button>
                    <a href="<?php echo SITE_URL; ?>/admin/pages/weapon_list.php" class="admin-btn admin-btn-secondary admin-btn-large">
                        ❌ Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</main>

<script>
// Form validation and enhancement
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.admin-form');
    const itemIdInput = document.querySelector('#item_id');
    const iconIdInput = document.querySelector('#iconId');
    
    // Auto-fill iconId with item_id if iconId is empty
    itemIdInput.addEventListener('input', function() {
        if (!iconIdInput.value && this.value) {
            iconIdInput.value = this.value;
        }
    });
    
    // Update icon preview when iconId changes
    iconIdInput.addEventListener('input', function() {
        updateIconPreview();
    });
    
    function updateIconPreview() {
        const iconId = iconIdInput.value || 0;
        const iconPreviewImage = document.querySelector('#iconPreviewImage');
        iconPreviewImage.src = `${siteUrl}/assets/img/icons/${iconId}.png`;
    }
    
    // Call once on page load
    updateIconPreview();
    
    // Form validation
    form.addEventListener('submit', function(e) {
        const itemId = itemIdInput.value;
        const descEn = document.querySelector('#desc_en').value;
        
        if (!itemId || !descEn) {
            e.preventDefault();
            alert('Please fill in all required fields (Item ID and English Name).');
            return false;
        }
        
        if (itemId < 1) {
            e.preventDefault();
            alert('Item ID must be a positive number.');
            return false;
        }
    });
});

// Define site URL for JavaScript
const siteUrl = '<?php echo SITE_URL; ?>';
</script>

<?php getPageFooter(); ?>
