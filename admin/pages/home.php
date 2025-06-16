<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header">
                <h2><span class="admin-icon admin-icon-dashboard"></span>L1J Database Admin Dashboard</h2>
            </div>
            <div class="card-body">
                <p class="lead">Welcome to the L1J Database Administration Interface. This panel allows you to manage game data across multiple SQL files.</p>
                <div id="alertPlaceholder"></div>
                
                <!-- Database Statistics -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h4 class="text-accent mb-3"><span class="admin-icon admin-icon-stats"></span>Database Statistics</h4>
                        
                        <?php
                        $conn = getDbConnection();
                        
                        // Get counts from different tables
                        $tables = ['spawnlist', 'droplist', 'npc', 'etcitem', 'armor', 'weapon', 'beginner'];
                        $stats = [];
                        
                        foreach ($tables as $table) {
                            $sql = "SELECT COUNT(*) as count FROM $table";
                            $result = $conn->query($sql);
                            
                            if ($result && $result->num_rows > 0) {
                                $row = $result->fetch_assoc();
                                $stats[$table] = $row['count'];
                            } else {
                                $stats[$table] = 0;
                            }
                        }
                        
                        $conn->close();
                        ?>
                        
                        <div class="stats-overview">
                            <div class="row">
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                                    <div class="stat-item">
                                        <h3><?php echo number_format($stats['spawnlist']); ?></h3>
                                        <p>Spawns</p>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                                    <div class="stat-item">
                                        <h3><?php echo number_format($stats['droplist']); ?></h3>
                                        <p>Drops</p>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                                    <div class="stat-item">
                                        <h3><?php echo number_format($stats['npc']); ?></h3>
                                        <p>Monsters</p>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                                    <div class="stat-item">
                                        <h3><?php echo number_format($stats['etcitem']); ?></h3>
                                        <p>Items</p>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                                    <div class="stat-item">
                                        <h3><?php echo number_format($stats['armor']); ?></h3>
                                        <p>Armor</p>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                                    <div class="stat-item">
                                        <h3><?php echo number_format($stats['weapon']); ?></h3>
                                        <p>Weapons</p>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                                    <div class="stat-item">
                                        <h3><?php echo number_format($stats['beginner']); ?></h3>
                                        <p>Beginner Items</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Management Cards -->
<div class="row">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card admin-card management-card">
            <div class="card-header">
                <h4><span class="admin-icon admin-icon-spawn"></span>Manage Spawns</h4>
            </div>
            <div class="card-body">
                <p>Add or modify monster spawns in the game world.</p>
                <p><strong>Files:</strong> spawnlist.sql and related spawn files</p>
                <a href="index.php?page=spawn" class="btn btn-primary w-100">Manage Spawns</a>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card admin-card management-card">
            <div class="card-header">
                <h4><span class="admin-icon admin-icon-drops"></span>Manage Drops</h4>
            </div>
            <div class="card-body">
                <p>Configure item drops for monsters in the game.</p>
                <p><strong>Files:</strong> droplist.sql with references to etcitem.sql, armor.sql, and weapon.sql</p>
                <a href="index.php?page=drops" class="btn btn-primary w-100">Manage Drops</a>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card admin-card management-card">
            <div class="card-header">
                <h4><span class="admin-icon admin-icon-monsters"></span>Manage Monsters</h4>
            </div>
            <div class="card-body">
                <p>Create or modify monster definitions.</p>
                <p><strong>Files:</strong> npc.sql</p>
                <a href="index.php?page=monsters" class="btn btn-primary w-100">Manage Monsters</a>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card admin-card management-card">
            <div class="card-header">
                <h4><span class="admin-icon admin-icon-beginner"></span>Beginner Items</h4>
            </div>
            <div class="card-body">
                <p>Configure starting items that new characters receive.</p>
                <p><strong>Files:</strong> beginner.sql</p>
                <a href="index.php?page=beginner" class="btn btn-primary w-100">Manage Beginner Items</a>
            </div>
        </div>
    </div>
</div>
