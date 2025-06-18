<?php
require_once 'includes/header.php';
?>

<!-- Admin Hero Section -->
<section class="admin-hero">
    <div class="admin-hero-content">
        <h1 class="admin-hero-title">L1J Database Administration</h1>
        <p class="admin-hero-subtitle">Manage and monitor your Lineage 1 database with comprehensive tools and analytics</p>
        <div class="admin-hero-stats">
            <div class="hero-stat-item">
                <span class="hero-stat-number">25</span>
                <span class="hero-stat-label">Database Tables</span>
            </div>
            <div class="hero-stat-item">
                <span class="hero-stat-number">1,200+</span>
                <span class="hero-stat-label">Items Managed</span>
            </div>
            <div class="hero-stat-item">
                <span class="hero-stat-number">500+</span>
                <span class="hero-stat-label">NPCs & Monsters</span>
            </div>
        </div>
    </div>
</section>

<!-- Quick Actions Section -->
<section class="admin-quick-actions">
    <div class="admin-header">
        <h2>Quick Actions</h2>
        <div class="admin-header-actions">
            <a href="/l1jdb_database/admin/tools/" class="admin-btn admin-btn-primary">
                <span class="admin-icon admin-icon-stats"></span>
                Advanced Tools
            </a>
        </div>
    </div>
    
    <div class="admin-dashboard-grid">
        <!-- Binary Data Management -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <span class="admin-icon admin-icon-dashboard"></span>
                <h3>Binary Data Management</h3>
            </div>
            <div class="dashboard-card-content">
                <p>Manage and configure binary data tables including items, NPCs, spells, and game mechanics.</p>
                <div class="dashboard-card-links">
                    <a href="/l1jdb_database/admin/pages/bin/index.php" class="admin-btn admin-btn-secondary admin-btn-small">View All Tables</a>
                </div>
                <div class="dashboard-quick-links">
                    <a href="/l1jdb_database/admin/pages/bin/item_common/item_list_view.php">Items</a>
                    <a href="/l1jdb_database/admin/pages/bin/npc_common/npc_list_view.php">NPCs</a>
                    <a href="/l1jdb_database/admin/pages/bin/spawn_common/spawn_list_view.php">Spawns</a>
                    <a href="/l1jdb_database/admin/pages/bin/spell_common/spell_list_view.php">Spells</a>
                    <a href="/l1jdb_database/admin/pages/bin/catalyst/catalyst_list_view.php">Catalyst</a>
                </div>
            </div>
        </div>

        <!-- Weapons Management -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <span class="admin-icon admin-icon-monsters"></span>
                <h3>Weapons Management</h3>
            </div>
            <div class="dashboard-card-content">
                <p>Comprehensive weapon database management with detailed statistics and properties.</p>
                <div class="dashboard-card-links">
                    <a href="/l1jdb_database/admin/pages/weapon/weapon_list.php" class="admin-btn admin-btn-secondary admin-btn-small">View Weapons</a>
                    <a href="/l1jdb_database/admin/pages/weapon/weapon_add.php" class="admin-btn admin-btn-primary admin-btn-small">Add New</a>
                </div>
                <div class="dashboard-stats-mini">
                    <div class="mini-stat">
                        <span class="mini-stat-number">150+</span>
                        <span class="mini-stat-label">Total Weapons</span>
                    </div>
                    <div class="mini-stat">
                        <span class="mini-stat-number">12</span>
                        <span class="mini-stat-label">Categories</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enchant Systems -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <span class="admin-icon admin-icon-spawn"></span>
                <h3>Enchant Systems</h3>
            </div>
            <div class="dashboard-card-content">
                <p>Manage enchant scrolls, enhancement systems, and upgrade mechanics.</p>
                <div class="dashboard-card-links">
                    <a href="/l1jdb_database/admin/pages/bin/enchant_scroll_table_common/enchant_scroll_list_view.php" class="admin-btn admin-btn-secondary admin-btn-small">Enchant Scrolls</a>
                    <a href="/l1jdb_database/admin/pages/bin/potential_common/potential_list_view.php" class="admin-btn admin-btn-secondary admin-btn-small">Potential System</a>
                </div>
                <div class="dashboard-quick-links">
                    <a href="/l1jdb_database/admin/pages/bin/enchant_scroll_table_common/enchant_scroll_list_view.php">Scroll Tables</a>
                    <a href="/l1jdb_database/admin/pages/bin/potential_common/potential_list_view.php">Potentials</a>
                </div>
            </div>
        </div>

        <!-- Crafting Systems -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <span class="admin-icon admin-icon-drops"></span>
                <h3>Crafting Systems</h3>
            </div>
            <div class="dashboard-card-content">
                <p>Configure crafting recipes, materials, and production systems.</p>
                <div class="dashboard-card-links">
                    <a href="/l1jdb_database/admin/pages/bin/craft_common/craft_list_view.php" class="admin-btn admin-btn-secondary admin-btn-small">Craft Recipes</a>
                    <a href="/l1jdb_database/admin/pages/bin/catalyst/catalyst_list_view.php" class="admin-btn admin-btn-secondary admin-btn-small">Catalysts</a>
                </div>
                <div class="dashboard-stats-mini">
                    <div class="mini-stat">
                        <span class="mini-stat-number">80+</span>
                        <span class="mini-stat-label">Recipes</span>
                    </div>
                    <div class="mini-stat">
                        <span class="mini-stat-number">25</span>
                        <span class="mini-stat-label">Catalysts</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Spawns Management -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <span class="admin-icon admin-icon-spawn"></span>
                <h3>Spawns</h3>
            </div>
            <div class="dashboard-card-content">
                <p>Manage all spawns in the game world from various spawn tables.</p>
                <div class="dashboard-card-links">
                    <a href="/l1jdb_database/admin/pages/bin/spawn_common/spawn_list_view.php" class="admin-btn admin-btn-secondary admin-btn-small">View Spawns</a>
                </div>
                <div class="dashboard-quick-links">
                    <a href="/l1jdb_database/admin/pages/bin/spawn_common/spawn_list_view.php?table=spawnlist">Regular Spawns</a>
                    <a href="/l1jdb_database/admin/pages/bin/spawn_common/spawn_list_view.php?table=spawnlist_boss">Boss Spawns</a>
                    <a href="/l1jdb_database/admin/pages/bin/spawn_common/spawn_list_view.php?table=spawnlist_clandungeon">Clan Dungeon</a>
                </div>
            </div>
        </div>

        <!-- Map Systems -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <span class="admin-icon admin-icon-beginner"></span>
                <h3>Map & Navigation</h3>
            </div>
            <div class="dashboard-card-content">
                <p>Manage map access, teleportation points, and entry requirements.</p>
                <div class="dashboard-card-links">
                    <a href="/l1jdb_database/admin/pages/bin/entermaps_common/entermaps_list_view.php" class="admin-btn admin-btn-secondary admin-btn-small">Enter Maps</a>
                    <a href="/l1jdb_database/admin/pages/bin/ndl_common/ndl_list_view.php" class="admin-btn admin-btn-secondary admin-btn-small">NDL System</a>
                </div>
                <div class="dashboard-quick-links">
                    <a href="/l1jdb_database/admin/pages/bin/entermaps_common/entermaps_list_view.php">Map Entries</a>
                    <a href="/l1jdb_database/admin/pages/bin/ndl_common/ndl_list_view.php">NDL Data</a>
                </div>
            </div>
        </div>

        <!-- Database Tools -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <span class="admin-icon admin-icon-stats"></span>
                <h3>Database Tools</h3>
            </div>
            <div class="dashboard-card-content">
                <p>Advanced database management tools and system utilities.</p>
                <div class="dashboard-card-links">
                    <a href="/l1jdb_database/admin/tools/" class="admin-btn admin-btn-primary admin-btn-small">Tool Suite</a>
                    <a href="/l1jdb_database/admin/pages/bin/index.php" class="admin-btn admin-btn-secondary admin-btn-small">All Tables</a>
                </div>
                <div class="dashboard-stats-mini">
                    <div class="mini-stat">
                        <span class="mini-stat-number">10+</span>
                        <span class="mini-stat-label">Admin Tools</span>
                    </div>
                    <div class="mini-stat">
                        <span class="mini-stat-number">25</span>
                        <span class="mini-stat-label">Data Tables</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- System Status Section -->
<section class="admin-system-status">
    <div class="admin-header">
        <h2>System Status</h2>
    </div>
    
    <div class="admin-stats">
        <div class="stat-card">
            <h3>Database Health</h3>
            <div class="stat-number status-good">Excellent</div>
            <div class="stat-label">All systems operational</div>
        </div>
        
        <div class="stat-card">
            <h3>Last Backup</h3>
            <div class="stat-number">2 hours ago</div>
            <div class="stat-label">Automated backup successful</div>
        </div>
        
        <div class="stat-card">
            <h3>Active Sessions</h3>
            <div class="stat-number">1</div>
            <div class="stat-label">Administrator sessions</div>
        </div>
        
        <div class="stat-card">
            <h3>Data Integrity</h3>
            <div class="stat-number status-good">100%</div>
            <div class="stat-label">No corruption detected</div>
        </div>
    </div>
</section>

<!-- Recent Activity Section -->
<section class="admin-recent-activity">
    <div class="admin-header">
        <h2>Recent Activity</h2>
        <div class="admin-header-actions">
            <a href="/l1jdb_database/admin/tools/" class="admin-btn admin-btn-secondary admin-btn-small">View All Logs</a>
        </div>
    </div>
    
    <div class="activity-feed">
        <div class="activity-item">
            <div class="activity-icon admin-icon-dashboard"></div>
            <div class="activity-content">
                <div class="activity-title">System startup completed</div>
                <div class="activity-time">5 minutes ago</div>
            </div>
        </div>
        
        <div class="activity-item">
            <div class="activity-icon admin-icon-stats"></div>
            <div class="activity-content">
                <div class="activity-title">Database backup completed successfully</div>
                <div class="activity-time">2 hours ago</div>
            </div>
        </div>
        
        <div class="activity-item">
            <div class="activity-icon admin-icon-spawn"></div>
            <div class="activity-content">
                <div class="activity-title">Admin session started</div>
                <div class="activity-time">5 hours ago</div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
