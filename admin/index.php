<?php
require_once '../includes/functions.php';

// Simple admin check (you can implement proper authentication later)
session_start();
?>

<?php echo generateHeader('Admin Panel - L1j-R Database'); ?>

<div class="page-header">
    <div class="container">
        <h1 class="page-title">Admin Panel</h1>
        <p class="page-subtitle">Database Management</p>
    </div>
</div>

<div class="content-section">
    <div class="container">
        <div class="admin-grid">
            <div class="admin-card">
                <h3>Database Status</h3>
                <p>Monitor database connections and performance</p>
                <a href="#" class="admin-btn">View Status</a>
            </div>
            
            <div class="admin-card">
                <h3>Data Management</h3>
                <p>Add, edit, or remove database entries</p>
                <a href="#" class="admin-btn">Manage Data</a>
            </div>
            
            <div class="admin-card">
                <h3>User Analytics</h3>
                <p>View site statistics and user behavior</p>
                <a href="#" class="admin-btn">View Analytics</a>
            </div>
            
            <div class="admin-card">
                <h3>System Settings</h3>
                <p>Configure site settings and preferences</p>
                <a href="#" class="admin-btn">Settings</a>
            </div>
        </div>
    </div>
</div>

<style>
.admin-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.admin-card {
    background-color: var(--primary);
    padding: 30px;
    border-radius: 10px;
    text-align: center;
    border: 2px solid var(--secondary);
    transition: all 0.3s ease;
}

.admin-card:hover {
    border-color: var(--accent);
    transform: translateY(-5px);
}

.admin-card h3 {
    color: var(--accent);
    margin-bottom: 15px;
}

.admin-card p {
    color: #cccccc;
    margin-bottom: 20px;
}

.admin-btn {
    display: inline-block;
    background-color: var(--accent);
    color: var(--text);
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

.admin-btn:hover {
    background-color: #e66d38;
}
</style>

<?php echo generateFooter(); ?>
