<?php
require_once 'includes/header.php';

// Get recent updates
$recentUpdates = getRecentUpdates(5);

getPageHeader('Home');
?>

<main>
    <!-- Dynamic Hero Section -->
    <?php renderHero('home'); ?>

    <div class="main">
        <!-- Recent Updates Section -->
        <section class="recent-updates">
            <h2>Recent Updates</h2>
            <?php if (!empty($recentUpdates)): ?>
                <?php foreach ($recentUpdates as $update): ?>
                    <div class="update-item">
                        <div class="update-meta">
                            <?php echo ucfirst($update['category']); ?> • 
                            <?php echo date('M j, Y g:i A', strtotime($update['updated_at'])); ?>
                        </div>
                        <div class="update-content">
                            <strong><?php echo htmlspecialchars($update['item_name']); ?></strong>: 
                            <?php echo htmlspecialchars($update['description']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="update-item">
                    <div class="update-content">No recent updates available.</div>
                </div>
            <?php endif; ?>
        </section>

        <!-- Database Categories -->
        <section class="database-categories">
            <h2 class="section-title">Browse Database</h2>
            <div class="cards-grid">
                <a href="<?php echo SITE_URL; ?>/pages/weapons/weapon_list.php" class="card">
                    <div class="card-header">
                        <h3 class="card-title">Weapons</h3>
                    </div>
                    <div class="card-image">
                        <img src="<?php echo SITE_URL; ?>/assets/img/placeholders/weapons.png" alt="Weapons" onerror="this.style.display='none'">
                    </div>
                    <div class="card-description">
                        <p>Browse all weapons including swords, bows, staffs, and magical weapons with detailed stats and requirements.</p>
                    </div>
                </a>

                <a href="<?php echo SITE_URL; ?>/pages/armor/armor_list.php" class="card">
                    <div class="card-header">
                        <h3 class="card-title">Armor</h3>
                    </div>
                    <div class="card-image">
                        <img src="<?php echo SITE_URL; ?>/assets/img/placeholders/armor.png" alt="Armor" onerror="this.style.display='none'">
                    </div>
                    <div class="card-description">
                        <p>Explore armor sets, helmets, shields, and protective gear with defense ratings, set bonuses, and class requirements.</p>
                    </div>
                </a>

                <a href="<?php echo SITE_URL; ?>/pages/items/items_list.php" class="card">
                    <div class="card-header">
                        <h3 class="card-title">Items</h3>
                    </div>
                    <div class="card-image">
                        <img src="<?php echo SITE_URL; ?>/assets/img/placeholders/items.png" alt="Items" onerror="this.style.display='none'">
                    </div>
                    <div class="card-description">
                        <p>Discover consumables, quest items, crafting materials, and other useful items.</p>
                    </div>
                </a>

                <a href="<?php echo SITE_URL; ?>/dolls/" class="card">
                    <div class="card-header">
                        <h3 class="card-title">Dolls</h3>
                    </div>
                    <div class="card-image">
                        <img src="<?php echo SITE_URL; ?>/assets/img/placeholders/dolls.png" alt="Dolls" onerror="this.style.display='none'">
                    </div>
                    <div class="card-description">
                        <p>View magical dolls and their enchantment effects, summoning requirements, and abilities.</p>
                    </div>
                </a>

                <a href="<?php echo SITE_URL; ?>/pages/maps/map_list.php" class="card">
                    <div class="card-header">
                        <h3 class="card-title">Maps</h3>
                    </div>
                    <div class="card-image">
                        <img src="<?php echo SITE_URL; ?>/assets/img/placeholders/maps.png" alt="Maps" onerror="this.style.display='none'">
                    </div>
                    <div class="card-description">
                        <p>Explore world maps with zone types, restrictions, monster spawns, and location details.</p>
                    </div>
                </a>

                <a href="<?php echo SITE_URL; ?>/pages/monsters/monster_list.php" class="card">
                    <div class="card-header">
                        <h3 class="card-title">Monsters</h3>
                    </div>
                    <div class="card-image">
                        <img src="<?php echo SITE_URL; ?>/assets/img/placeholders/monsters.png" alt="Monsters" onerror="this.style.display='none'">
                    </div>
                    <div class="card-description">
                        <p>Study monster statistics, drops, locations, and combat strategies.</p>
                    </div>
                </a>
            </div>
        </section>
    </div>
</main>

<?php getPageFooter(); ?>