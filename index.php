<?php
require_once 'includes/functions.php';
?>

<?php echo generateHeader('L1j-R Database - Ultimate MMORPG Resource'); ?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h1>L1j-R Database</h1>
        <p>Your ultimate resource for weapons, armor, items, monsters, maps, and dolls</p>
        <a href="#categories" class="hero-btn">Explore Database</a>
    </div>
</section>

<!-- Categories Section -->
<section class="categories" id="categories">
    <div class="container">
        <h2 class="section-title">Browse Categories</h2>
        
        <div class="cards-grid">
            <?php $basePath = getBasePath(); ?>
            <!-- Weapons Card -->
            <a href="<?php echo $basePath; ?>categories/weapons/weapon-list.php" class="category-card">
                <div class="card-header">
                    <h3 class="card-title">Weapons</h3>
                </div>
                <div class="card-image-wrapper">
                    <img src="<?php echo $basePath; ?>assets/img/placeholders/weapons.png" alt="Weapons" class="card-image">
                </div>
                <div class="card-content">
                    <p class="card-description">Discover powerful weapons and their stats</p>
                </div>
            </a>

            <!-- Armor Card -->
            <a href="<?php echo $basePath; ?>categories/armor/armor-list.php" class="category-card">
                <div class="card-header">
                    <h3 class="card-title">Armor</h3>
                </div>
                <div class="card-image-wrapper">
                    <img src="<?php echo $basePath; ?>assets/img/placeholders/armor.png" alt="Armor" class="card-image">
                </div>
                <div class="card-content">
                    <p class="card-description">Browse defensive gear and protection</p>
                </div>
            </a>

            <!-- Items Card -->
            <a href="<?php echo $basePath; ?>categories/items/item-list.php" class="category-card">
                <div class="card-header">
                    <h3 class="card-title">Items</h3>
                </div>
                <div class="card-image-wrapper">
                    <img src="<?php echo $basePath; ?>assets/img/placeholders/items.png" alt="Items" class="card-image">
                </div>
                <div class="card-content">
                    <p class="card-description">Explore consumables and special items</p>
                </div>
            </a>

            <!-- Monsters Card -->
            <a href="<?php echo $basePath; ?>categories/monsters/monster-list.php" class="category-card">
                <div class="card-header">
                    <h3 class="card-title">Monsters</h3>
                </div>
                <div class="card-image-wrapper">
                    <img src="<?php echo $basePath; ?>assets/img/placeholders/monsters.png" alt="Monsters" class="card-image">
                </div>
                <div class="card-content">
                    <p class="card-description">Learn about creatures and their abilities</p>
                </div>
            </a>

            <!-- Maps Card -->
            <a href="<?php echo $basePath; ?>categories/maps/map-list.php" class="category-card">
                <div class="card-header">
                    <h3 class="card-title">Maps</h3>
                </div>
                <div class="card-image-wrapper">
                    <img src="<?php echo $basePath; ?>assets/img/placeholders/maps.png" alt="Maps" class="card-image">
                </div>
                <div class="card-content">
                    <p class="card-description">Explore worlds and locations</p>
                </div>
            </a>

            <!-- Dolls Card -->
            <a href="<?php echo $basePath; ?>categories/dolls/doll-list.php" class="category-card">
                <div class="card-header">
                    <h3 class="card-title">Dolls</h3>
                </div>
                <div class="card-image-wrapper">
                    <img src="<?php echo $basePath; ?>assets/img/placeholders/dolls.png" alt="Dolls" class="card-image">
                </div>
                <div class="card-content">
                    <p class="card-description">Collection of magical dolls and companions</p>
                </div>
            </a>
        </div>
    </div>
</section>

<?php echo generateFooter(); ?>
