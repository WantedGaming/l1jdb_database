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
                        <p>Browse all weapons with detailed stats and requirements.</p>
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
                        <p>Explore armor protective gear with defense ratings and class requirements.</p>
                    </div>
                </a>

                <a href="<?php echo SITE_URL; ?>/pages/items/item_list.php" class="card">
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

                <a href="<?php echo SITE_URL; ?>/pages/dolls/doll_list.php" class="card">
                    <div class="card-header">
                        <h3 class="card-title">Magic Dolls</h3>
                    </div>
                    <div class="card-image">
                        <img src="<?php echo SITE_URL; ?>/assets/img/placeholders/dolls.png" alt="Dolls" onerror="this.style.display='none'">
                    </div>
                    <div class="card-description">
                        <p>Summon powerful companions with unique abilities to aid your journey.</p>
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
                        <p>Explore world maps with zone types and location details.</p>
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