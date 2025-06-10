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
    <!-- Include main style.css for CSS variables -->
    <link rel="stylesheet" href="/l1jdb_database/assets/css/style.css">
    <!-- Include admin-specific styles -->
    <link rel="stylesheet" href="/l1jdb_database/assets/css/admin.css">

</head>
<body>
    <header class="admin-nav-header">
        <div class="admin-header-container">
            <a href="/l1jdb_database/admin/index.php" class="admin-logo">L1J Database Admin</a>
            
            <nav class="admin-nav-menu">
                <a href="/l1jdb_database/admin/index.php">Dashboard</a>
                <a href="/l1jdb_database/admin/bin/">Binary Tables</a>
                <a href="/l1jdb_database/admin/pages/weapon/weapon_list.php">Weapons</a>
                <a href="/l1jdb_database/admin/tools/">Tools</a>
            </nav>
            
            <div class="admin-user-info">
                <span class="username">Admin</span>
                <a href="/l1jdb_database/admin/logout.php" class="admin-logout-btn">Logout</a>
            </div>
        </div>
    </header>
    
    <div class="admin-content-wrapper">
