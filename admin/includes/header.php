<?php
// Ensure authentication is checked
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>L1J Database Admin</title>
    <link rel="icon" type="image/x-icon" href="/l1jdb_database/assets/img/favicon/favicon.ico">
    <!-- Include base styles with CSS variables -->
    <link rel="stylesheet" href="/l1jdb_database/assets/css/base.css">
    <!-- Include admin-specific styles -->
    <link rel="stylesheet" href="/l1jdb_database/assets/css/admin.css">

</head>
<body>
    <header class="admin-nav-header">
        <div class="admin-header-container">
            <a href="/l1jdb_database/admin/index.php" class="admin-logo">L1J Database Admin</a>
            
            <nav class="admin-nav-menu">
                <a href="/l1jdb_database/admin/index.php">Dashboard</a>
                
                <!-- Binary Dropdown -->
                <div class="nav-dropdown">
                    <a href="#" class="nav-dropdown-toggle">Binary <span class="dropdown-arrow">▼</span></a>
                    <div class="nav-dropdown-menu">
                        <a href="/l1jdb_database/admin/pages/bin/index.php">All Binary Tables</a>
                        <div class="dropdown-divider"></div>
                        <a href="/l1jdb_database/admin/pages/bin/catalyst/catalyst_list_view.php">Catalyst</a>
                        <a href="/l1jdb_database/admin/pages/bin/craft_common/craft_list_view.php">Craft</a>
                        <a href="/l1jdb_database/admin/pages/bin/enchant_scroll_table_common/enchant_scroll_list_view.php">Enchant Scrolls</a>
                        <a href="/l1jdb_database/admin/pages/bin/entermaps_common/entermaps_list_view.php">Enter Maps</a>
                        <a href="/l1jdb_database/admin/pages/bin/item_common/item_list_view.php">Items</a>
                        <a href="/l1jdb_database/admin/pages/bin/ndl_common/ndl_list_view.php">NDL</a>
                        <a href="/l1jdb_database/admin/pages/bin/npc_common/npc_list_view.php">NPCs</a>
                        <a href="/l1jdb_database/admin/pages/bin/potential_common/potential_list_view.php">Potential</a>
                        <a href="/l1jdb_database/admin/pages/bin/spell_common/spell_list_view.php">Spells</a>
                    </div>
                </div>
                
                <a href="/l1jdb_database/admin/pages/weapon/weapon_list.php">Weapons</a>
                <a href="/l1jdb_database/admin/pages/monster/monster_list.php">Monsters</a>
                <a href="/l1jdb_database/admin/tools/">Tools</a>
            </nav>
            
            <div class="admin-user-info">
                <span class="username">Admin</span>
                <a href="/l1jdb_database/admin/logout.php" class="admin-logout-btn">Logout</a>
            </div>
        </div>
    </header>
    
    <script>
    // Dropdown functionality
    document.addEventListener('DOMContentLoaded', function() {
        const dropdown = document.querySelector('.nav-dropdown');
        const dropdownToggle = document.querySelector('.nav-dropdown-toggle');
        
        if (dropdown && dropdownToggle) {
            // Toggle dropdown on click
            dropdownToggle.addEventListener('click', function(e) {
                e.preventDefault();
                dropdown.classList.toggle('active');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!dropdown.contains(e.target)) {
                    dropdown.classList.remove('active');
                }
            });
            
            // Close dropdown when pressing Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    dropdown.classList.remove('active');
                }
            });
        }
    });
    </script>
    
    <div class="admin-content-wrapper">
