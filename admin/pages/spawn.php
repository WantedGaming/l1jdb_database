<?php
// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDbConnection();
    
    // Process form data
    $name = isset($_POST['name']) ? sanitizeInput($_POST['name']) : '';
    $count = isset($_POST['count']) ? (int)$_POST['count'] : 0;
    $npc_templateid = isset($_POST['npc_templateid']) ? (int)$_POST['npc_templateid'] : 0;
    $locx = isset($_POST['locx']) ? (int)$_POST['locx'] : 0;
    $locy = isset($_POST['locy']) ? (int)$_POST['locy'] : 0;
    $randomx = isset($_POST['randomx']) ? (int)$_POST['randomx'] : 0;
    $randomy = isset($_POST['randomy']) ? (int)$_POST['randomy'] : 0;
    $heading = isset($_POST['heading']) ? (int)$_POST['heading'] : 0;
    $min_respawn_delay = isset($_POST['min_respawn_delay']) ? (int)$_POST['min_respawn_delay'] : 0;
    $max_respawn_delay = isset($_POST['max_respawn_delay']) ? (int)$_POST['max_respawn_delay'] : 0;
    $mapid = isset($_POST['mapid']) ? (int)$_POST['mapid'] : 0;
    $movement_distance = isset($_POST['movement_distance']) ? (int)$_POST['movement_distance'] : 0;
    $spawnlist_type = isset($_POST['spawnlist_type']) ? sanitizeInput($_POST['spawnlist_type']) : 'spawnlist';
    
    // Validate the spawn data
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    
    if ($count <= 0) {
        $errors[] = "Count must be greater than 0.";
    }
    
    if ($npc_templateid <= 0) {
        $errors[] = "NPC Template ID must be greater than 0.";
    }
    
    // If no errors, insert into the database
    if (empty($errors)) {
        $tableName = $spawnlist_type; // Use the selected spawnlist table
        
        $sql = "INSERT INTO $tableName (name, count, npc_templateid, locx, locy, randomx, randomy, heading, min_respawn_delay, max_respawn_delay, mapid, movement_distance) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siiiiiiiiii", $name, $count, $npc_templateid, $locx, $locy, $randomx, $randomy, $heading, $min_respawn_delay, $max_respawn_delay, $mapid, $movement_distance);
        
        if ($stmt->execute()) {
            $message = "Spawn added successfully!";
            $messageType = "success";
        } else {
            $message = "Error: " . $stmt->error;
            $messageType = "danger";
        }
        
        $stmt->close();
    } else {
        $message = "Please correct the following errors: " . implode(", ", $errors);
        $messageType = "danger";
    }
    
    $conn->close();
}

// Get the list of NPC templates for reference
$conn = getDbConnection();
$npc_list = [];

$sql = "SELECT npcid, desc_en FROM npc ORDER BY npcid LIMIT 100";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $npc_list[] = $row;
    }
}

// Get spawn lists for reference
$spawn_tables = [
    'spawnlist' => 'Regular Spawn',
    'spawnlist_boss' => 'Boss Spawn',
    'spawnlist_clandungeon' => 'Clan Dungeon Spawn',
    'spawnlist_indun' => 'Instance Dungeon Spawn',
    'spawnlist_other' => 'Other Spawn',
    'spawnlist_ruun' => 'Ruun Spawn',
    'spawnlist_ub' => 'Underground Battle Spawn',
    'spawnlist_unicorntemple' => 'Unicorn Temple Spawn',
    'spawnlist_worldwar' => 'World War Spawn'
];

// Get the list of existing spawns
$spawns = [];
$selectedTable = isset($_GET['spawnlist_type']) ? sanitizeInput($_GET['spawnlist_type']) : 'spawnlist';

if (!array_key_exists($selectedTable, $spawn_tables)) {
    $selectedTable = 'spawnlist';
}

$sql = "SELECT s.*, n.desc_en as npc_name 
        FROM $selectedTable s 
        LEFT JOIN npc n ON s.npc_templateid = n.npcid 
        ORDER BY s.id DESC 
        LIMIT 50";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $spawns[] = $row;
    }
}

$conn->close();
?>

<div class="row">
    <div class="col-md-12">
        <div class="admin-breadcrumb mb-3">
            <a href="index.php" class="btn btn-secondary btn-sm">
                <span class="admin-icon admin-icon-dashboard"></span>Back to Dashboard
            </a>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h2><span class="admin-icon admin-icon-spawn"></span>Manage Spawns</h2>
            </div>
            <div class="card-body">
                <p>Add or modify monster spawns in the game world. Choose the appropriate spawn list type for your needs.</p>
                
                <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?>" role="alert">
                    <?php echo $message; ?>
                </div>
                <?php endif; ?>
                
                <div id="alertPlaceholder"></div>
                
                <ul class="nav nav-tabs" id="spawnTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="add-tab" data-bs-toggle="tab" data-bs-target="#add" type="button" role="tab" aria-controls="add" aria-selected="true">Add Spawn</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="view-tab" data-bs-toggle="tab" data-bs-target="#view" type="button" role="tab" aria-controls="view" aria-selected="false">View Spawns</button>
                    </li>
                </ul>
                
                <div class="tab-content" id="spawnTabsContent">
                    <div class="tab-pane fade show active" id="add" role="tabpanel" aria-labelledby="add-tab">
                        <div class="form-section mt-4">
                            <form id="spawnForm" method="post" action="index.php?page=spawn">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="spawnlist_type" class="form-label">Spawn List Type</label>
                                            <select class="form-select" id="spawnlist_type" name="spawnlist_type">
                                                <?php foreach ($spawn_tables as $table => $label): ?>
                                                <option value="<?php echo $table; ?>"><?php echo $label; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" class="form-control" id="name" name="name" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="count" class="form-label">Count</label>
                                            <input type="number" class="form-control" id="count" name="count" min="1" value="1" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="npcSearch" class="form-label">Search NPC</label>
                                            <input type="text" class="form-control" id="npcSearch" placeholder="Search by NPC name">
                                            <div id="npcSuggestions" class="list-group mt-2"></div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="npc_templateid" class="form-label">NPC Template ID</label>
                                            <input type="number" class="form-control" id="npc_templateid" name="npc_templateid" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="locx" class="form-label">Location X</label>
                                                    <input type="number" class="form-control" id="locx" name="locx" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="locy" class="form-label">Location Y</label>
                                                    <input type="number" class="form-control" id="locy" name="locy" required>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="randomx" class="form-label">Random X</label>
                                                    <input type="number" class="form-control" id="randomx" name="randomx" value="0">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="randomy" class="form-label">Random Y</label>
                                                    <input type="number" class="form-control" id="randomy" name="randomy" value="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="heading" class="form-label">Heading</label>
                                            <input type="number" class="form-control" id="heading" name="heading" value="0" min="0" max="7">
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="min_respawn_delay" class="form-label">Min Respawn Delay</label>
                                                    <input type="number" class="form-control" id="min_respawn_delay" name="min_respawn_delay" value="60">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="max_respawn_delay" class="form-label">Max Respawn Delay</label>
                                                    <input type="number" class="form-control" id="max_respawn_delay" name="max_respawn_delay" value="120">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="mapid" class="form-label">Map ID</label>
                                                    <input type="number" class="form-control" id="mapid" name="mapid" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="movement_distance" class="form-label">Movement Distance</label>
                                                    <input type="number" class="form-control" id="movement_distance" name="movement_distance" value="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Add Spawn</button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="view" role="tabpanel" aria-labelledby="view-tab">
                        <div class="form-section mt-4">
                            <form method="get" action="index.php" class="mb-4">
                                <input type="hidden" name="page" value="spawn">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="spawnlist_type_view" class="form-label">Select Spawn List Type</label>
                                        <select class="form-select" id="spawnlist_type_view" name="spawnlist_type" onchange="this.form.submit()">
                                            <?php foreach ($spawn_tables as $table => $label): ?>
                                            <option value="<?php echo $table; ?>" <?php echo ($selectedTable === $table) ? 'selected' : ''; ?>><?php echo $label; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="search" class="form-label">Search</label>
                                        <input type="text" class="form-control search-input" id="search" data-table="spawnsTable" placeholder="Search spawns...">
                                    </div>
                                </div>
                            </form>
                            
                            <div class="table-responsive">
                                <table class="table table-striped table-hover data-table" id="spawnsTable">
                                    <thead>
                                        <tr>
                                            <th data-sort="id">ID</th>
                                            <th data-sort="name">Name</th>
                                            <th data-sort="npc_templateid">NPC ID</th>
                                            <th>NPC Name</th>
                                            <th data-sort="count">Count</th>
                                            <th>Location</th>
                                            <th>Map ID</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($spawns as $spawn): ?>
                                        <tr>
                                            <td data-column="id"><?php echo $spawn['id']; ?></td>
                                            <td data-column="name"><?php echo $spawn['name']; ?></td>
                                            <td data-column="npc_templateid"><?php echo $spawn['npc_templateid']; ?></td>
                                            <td><?php echo $spawn['npc_name']; ?></td>
                                            <td data-column="count"><?php echo $spawn['count']; ?></td>
                                            <td>(<?php echo $spawn['locx']; ?>, <?php echo $spawn['locy']; ?>)</td>
                                            <td><?php echo $spawn['mapid']; ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editSpawnModal" 
                                                        data-id="<?php echo $spawn['id']; ?>" 
                                                        data-table="<?php echo $selectedTable; ?>">
                                                    Edit
                                                </button>
                                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteSpawnModal" 
                                                        data-id="<?php echo $spawn['id']; ?>" 
                                                        data-table="<?php echo $selectedTable; ?>">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <?php if (empty($spawns)): ?>
                            <div class="alert alert-info">No spawns found in the selected table.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Spawn Modal -->
<div class="modal fade" id="editSpawnModal" tabindex="-1" aria-labelledby="editSpawnModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSpawnModalLabel">Edit Spawn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editSpawnForm">
                    <input type="hidden" id="editId" name="id">
                    <input type="hidden" id="editTable" name="table">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editName" class="form-label">Name</label>
                                <input type="text" class="form-control" id="editName" name="name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="editCount" class="form-label">Count</label>
                                <input type="number" class="form-control" id="editCount" name="count" min="1" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="editNpcId" class="form-label">NPC Template ID</label>
                                <input type="number" class="form-control" id="editNpcId" name="npc_templateid" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editLocx" class="form-label">Location X</label>
                                        <input type="number" class="form-control" id="editLocx" name="locx" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editLocy" class="form-label">Location Y</label>
                                        <input type="number" class="form-control" id="editLocy" name="locy" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editMapid" class="form-label">Map ID</label>
                                        <input type="number" class="form-control" id="editMapid" name="mapid" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editMovementDistance" class="form-label">Movement Distance</label>
                                        <input type="number" class="form-control" id="editMovementDistance" name="movement_distance">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editMinRespawn" class="form-label">Min Respawn Delay</label>
                                        <input type="number" class="form-control" id="editMinRespawn" name="min_respawn_delay">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editMaxRespawn" class="form-label">Max Respawn Delay</label>
                                        <input type="number" class="form-control" id="editMaxRespawn" name="max_respawn_delay">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveSpawnChanges">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Spawn Modal -->
<div class="modal fade" id="deleteSpawnModal" tabindex="-1" aria-labelledby="deleteSpawnModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteSpawnModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this spawn? This action cannot be undone.</p>
                <form id="deleteSpawnForm">
                    <input type="hidden" id="deleteId" name="id">
                    <input type="hidden" id="deleteTable" name="table">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteSpawn">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
// Handle edit spawn modal
document.addEventListener('DOMContentLoaded', function() {
    const editSpawnModal = document.getElementById('editSpawnModal');
    if (editSpawnModal) {
        editSpawnModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const table = button.getAttribute('data-table');
            
            document.getElementById('editId').value = id;
            document.getElementById('editTable').value = table;
            
            // Fetch spawn data and populate the form
            fetch(`/l1jdb_database/admin/api/get_spawn.php?id=${id}&table=${table}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editName').value = data.name;
                    document.getElementById('editCount').value = data.count;
                    document.getElementById('editNpcId').value = data.npc_templateid;
                    document.getElementById('editLocx').value = data.locx;
                    document.getElementById('editLocy').value = data.locy;
                    document.getElementById('editMapid').value = data.mapid;
                    document.getElementById('editMovementDistance').value = data.movement_distance;
                    document.getElementById('editMinRespawn').value = data.min_respawn_delay;
                    document.getElementById('editMaxRespawn').value = data.max_respawn_delay;
                })
                .catch(error => {
                    console.error('Error fetching spawn data:', error);
                    showAlert('Error fetching spawn data.', 'danger');
                });
        });
    }
    
    // Handle save changes button
    const saveSpawnChanges = document.getElementById('saveSpawnChanges');
    if (saveSpawnChanges) {
        saveSpawnChanges.addEventListener('click', function() {
            const form = document.getElementById('editSpawnForm');
            const formData = new FormData(form);
            
            fetch('/l1jdb_database/admin/api/update_spawn.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(editSpawnModal).hide();
                    showAlert('Spawn updated successfully!', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showAlert('Error updating spawn: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error updating spawn:', error);
                showAlert('Error updating spawn.', 'danger');
            });
        });
    }
    
    // Handle delete spawn modal
    const deleteSpawnModal = document.getElementById('deleteSpawnModal');
    if (deleteSpawnModal) {
        deleteSpawnModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const table = button.getAttribute('data-table');
            
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteTable').value = table;
        });
    }
    
    // Handle confirm delete button
    const confirmDeleteSpawn = document.getElementById('confirmDeleteSpawn');
    if (confirmDeleteSpawn) {
        confirmDeleteSpawn.addEventListener('click', function() {
            const form = document.getElementById('deleteSpawnForm');
            const formData = new FormData(form);
            
            fetch('/l1jdb_database/admin/api/delete_spawn.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(deleteSpawnModal).hide();
                    showAlert('Spawn deleted successfully!', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showAlert('Error deleting spawn: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error deleting spawn:', error);
                showAlert('Error deleting spawn.', 'danger');
            });
        });
    }
});
</script>
