<?php
require_once 'config.php';
require_once 'functions.php';
require_once 'hero.php';

function getPageHeader($title = '') {
    $siteTitle = !empty($title) ? $title . ' - ' . SITE_NAME : SITE_NAME;
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($siteTitle); ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>
<body>
    <header class="header">
        <div class="nav-container">
            <a href="<?php echo SITE_URL; ?>" class="logo"><?php echo SITE_NAME; ?></a>
            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="<?php echo SITE_URL; ?>" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">Database</a>
                        <div class="dropdown">
                            <a href="<?php echo SITE_URL; ?>/pages/weapons/weapon_list.php" class="dropdown-link">Weapons</a>
                            <a href="<?php echo SITE_URL; ?>/pages/armor/armor_list.php" class="dropdown-link">Armor</a>
                            <a href="<?php echo SITE_URL; ?>/pages/items/items_list.php" class="dropdown-link">Items</a>
                            <a href="<?php echo SITE_URL; ?>/dolls/" class="dropdown-link">Dolls</a>
                            <a href="<?php echo SITE_URL; ?>/pages/maps/map_list.php" class="dropdown-link">Maps</a>
                            <a href="<?php echo SITE_URL; ?>/pages/monsters/monster_list.php" class="dropdown-link">Monsters</a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">Admin</a>
                        <div class="dropdown">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="<?php echo SITE_URL; ?>/admin/" class="dropdown-link">Dashboard</a>
                                <?php if (isAdmin()): ?>
                                    <a href="<?php echo SITE_URL; ?>/admin/weapons/" class="dropdown-link">Manage Weapons</a>
                                    <a href="<?php echo SITE_URL; ?>/admin/armor/" class="dropdown-link">Manage Armor</a>
                                    <a href="<?php echo SITE_URL; ?>/admin/items/" class="dropdown-link">Manage Items</a>
                                    <a href="<?php echo SITE_URL; ?>/admin/dolls/" class="dropdown-link">Manage Dolls</a>
                                    <a href="<?php echo SITE_URL; ?>/admin/maps/" class="dropdown-link">Manage Maps</a>
                                    <a href="<?php echo SITE_URL; ?>/admin/monsters/" class="dropdown-link">Manage Monsters</a>
                                <?php endif; ?>
                                <a href="<?php echo SITE_URL; ?>/admin/logout.php" class="dropdown-link">Logout</a>
                            <?php else: ?>
                                <a href="<?php echo SITE_URL; ?>/admin/login.php" class="dropdown-link">Login</a>
                            <?php endif; ?>
                        </div>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
    <?php
}

function getPageFooter() {
    ?>
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
    <?php
}
?>