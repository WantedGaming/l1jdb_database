<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/header.php';

// Get database statistics
$stats = [];

// Total accounts
$stats['accounts'] = getSingleValue($conn, "SELECT COUNT(*) FROM accounts");

// Total characters
$stats['characters'] = getSingleValue($conn, "SELECT COUNT(*) FROM characters");

// Total clans
$stats['clans'] = getSingleValue($conn, "SELECT COUNT(*) FROM clan_data");

// Total items
$stats['items'] = getSingleValue($conn, "SELECT COUNT(*) FROM etcitem") + 
                  getSingleValue($conn, "SELECT COUNT(*) FROM weapon") + 
                  getSingleValue($conn, "SELECT COUNT(*) FROM armor");

// Total NPCs
$stats['npcs'] = getSingleValue($conn, "SELECT COUNT(*) FROM npc");

// Total skills
$stats['skills'] = getSingleValue($conn, "SELECT COUNT(*) FROM skills");

// Binary tables count
$stats['bin_tables'] = 35; // We know there are 35 bin tables

// Get recent activity (dummy data for now)
$recent_activities = [
    ['action' => 'Login', 'user' => 'Admin', 'time' => date('Y-m-d H:i:s')],
];
?>



<div class="content-wrapper">
    <h1>Admin Dashboard</h1>
    
    <div class="dashboard-grid">
        <div class="stat-card">
            <h3>Total Accounts</h3>
            <div class="value"><?php echo number_format($stats['accounts']); ?></div>
        </div>
        
        <div class="stat-card">
            <h3>Total Characters</h3>
            <div class="value"><?php echo number_format($stats['characters']); ?></div>
        </div>
        
        <div class="stat-card">
            <h3>Total Clans</h3>
            <div class="value"><?php echo number_format($stats['clans']); ?></div>
        </div>
        
        <div class="stat-card">
            <h3>Total Items</h3>
            <div class="value"><?php echo number_format($stats['items']); ?></div>
        </div>
        
        <div class="stat-card">
            <h3>Total NPCs</h3>
            <div class="value"><?php echo number_format($stats['npcs']); ?></div>
        </div>
        
        <div class="stat-card">
            <h3>Total Skills</h3>
            <div class="value"><?php echo number_format($stats['skills']); ?></div>
        </div>
        
        <div class="stat-card">
            <h3>Binary Tables</h3>
            <div class="value"><?php echo $stats['bin_tables']; ?></div>
        </div>
    </div>
    
    <div class="quick-links">
        <h2>Quick Links</h2>
        <div class="links-grid">
            <a href="pages/bin/index.php" class="link-item">Binary Tables</a>
            <a href="pages/" class="link-item">Manage Pages</a>
            <a href="tools/" class="link-item">Admin Tools</a>
            <a href="../" class="link-item" target="_blank">View Site</a>
        </div>
    </div>
    
    <div class="activity-section">
        <h2>Recent Activity</h2>
        <ul class="activity-list">
            <?php foreach ($recent_activities as $activity): ?>
                <li>
                    <strong><?php echo htmlspecialchars($activity['action']); ?></strong> by 
                    <?php echo htmlspecialchars($activity['user']); ?> at 
                    <?php echo htmlspecialchars($activity['time']); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>