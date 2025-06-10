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
    <style>
        /* Admin-specific header overrides */
        .admin-nav-header {
            background-color: var(--primary);
            border-bottom: 2px solid var(--accent);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .admin-header-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-logo {
            font-size: 24px;
            font-weight: bold;
            color: var(--accent);
            text-decoration: none;
        }

        .admin-nav-menu {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .admin-nav-menu a {
            color: var(--text);
            text-decoration: none;
            transition: color 0.3s ease;
            font-weight: 500;
        }

        .admin-nav-menu a:hover {
            color: var(--accent);
        }

        .admin-user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .admin-user-info .username {
            color: var(--text);
            opacity: 0.8;
        }

        .admin-logout-btn {
            background: var(--accent);
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .admin-logout-btn:hover {
            background: #e06b3c;
        }

        .admin-content-wrapper {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 20px;
            min-height: calc(100vh - 150px);
        }

        /* Alert styles to match admin.css */
        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            font-weight: 500;
        }

        .alert-success {
            background-color: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
            border: 1px solid #2ecc71;
        }

        .alert-error {
            background-color: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
            border: 1px solid #e74c3c;
        }

        .alert-warning {
            background-color: rgba(243, 156, 18, 0.2);
            color: #f39c12;
            border: 1px solid #f39c12;
        }
    </style>
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
