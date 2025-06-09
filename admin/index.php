<?php
require_once '../includes/header.php';

requireAuth();

getPageHeader('Admin Dashboard');
?>

<main>
    <!-- Dynamic Hero Section -->
    <?php renderHero('admin'); ?>

    <div class="main">

    <?php if (isAdmin()): ?>
        <div class="admin-stats">
            <div class="stat-card">
                <h3>Database Management</h3>
                <p>Manage all database categories and content</p>
            </div>
        </div>

        <div class="admin-actions">
            <div class="cards-grid">
                <a href="<?php echo SITE_URL; ?>/admin/pages/weapon_list.php" class="card">
                    <div class="card-header">
                        <h3 class="card-title">Manage Weapons</h3>
                    </div>
                    <div class="card-description">
                        <p>Add, edit, and delete weapons</p>
                    </div>
                </a>

                <a href="<?php echo SITE_URL; ?>/admin/armor/" class="card">
                    <div class="card-header">
                        <h3 class="card-title">Manage Armor</h3>
                    </div>
                    <div class="card-description">
                        <p>Add, edit, and delete armor</p>
                    </div>
                </a>

                <a href="<?php echo SITE_URL; ?>/admin/items/" class="card">
                    <div class="card-header">
                        <h3 class="card-title">Manage Items</h3>
                    </div>
                    <div class="card-description">
                        <p>Add, edit, and delete items</p>
                    </div>
                </a>

                <a href="<?php echo SITE_URL; ?>/admin/dolls/" class="card">
                    <div class="card-header">
                        <h3 class="card-title">Manage Dolls</h3>
                    </div>
                    <div class="card-description">
                        <p>Add, edit, and delete dolls</p>
                    </div>
                </a>

                <a href="<?php echo SITE_URL; ?>/admin/maps/" class="card">
                    <div class="card-header">
                        <h3 class="card-title">Manage Maps</h3>
                    </div>
                    <div class="card-description">
                        <p>Add, edit, and delete maps</p>
                    </div>
                </a>

                <a href="<?php echo SITE_URL; ?>/admin/monsters/" class="card">
                    <div class="card-header">
                        <h3 class="card-title">Manage Monsters</h3>
                    </div>
                    <div class="card-description">
                        <p>Add, edit, and delete monsters</p>
                    </div>
                </a>
				<a href="<?php echo SITE_URL; ?>/admin/pages/bin/items/" class="card">
                    <div class="card-header">
                        <h3 class="card-title">Bin Database</h3>
                    </div>
                    <div class="card-description">
                        <p>Manage bin database tables</p>
                    </div>
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="access-denied">
            <h2>Access Denied</h2>
            <p>You don't have admin privileges to access this area.</p>
        </div>
    <?php endif; ?>
    </div>
</main>



<?php getPageFooter(); ?>
