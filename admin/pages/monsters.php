<?php
// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDbConnection();
    
    // Process form data
    $desc_en = isset($_POST['desc_en']) ? sanitizeInput($_POST['desc_en']) : '';
    $level = isset($_POST['level']) ? (int)$_POST['level'] : 1;
    $hp = isset($_POST['hp']) ? (int)$_POST['hp'] : 10;
    $mp = isset($_POST['mp']) ? (int)$_POST['mp'] : 1;
    $ac = isset($_POST['ac']) ? (int)$_POST['ac'] : 0;
    $str = isset($_POST['str']) ? (int)$_POST['str'] : 8;
    $con = isset($_POST['con']) ? (int)$_POST['con'] : 8;
    $dex = isset($_POST['dex']) ? (int)$_POST['dex'] : 8;
    $wis = isset($_POST['wis']) ? (int)$_POST['wis'] : 8;
    $intel = isset($_POST['intel']) ? (int)$_POST['intel'] : 8;
    $exp = isset($_POST['exp']) ? (int)$_POST['exp'] : 0;
    $karma = isset($_POST['karma']) ? (int)$_POST['karma'] : 0;
    $size = isset($_POST['size']) ? sanitizeInput($_POST['size']) : 'small';
    $impl = isset($_POST['impl']) ? sanitizeInput($_POST['impl']) : 'L1Monster';
    $classId = isset($_POST['classId']) ? (int)$_POST['classId'] : 0;
    
    // Validate the monster data
    $errors = [];
    
    if (empty($desc_en)) {
        $errors[] = "Monster name is required.";
    }
    
    if ($level < 1 || $level > 100) {
        $errors[] = "Level must be between 1 and 100.";
    }
    
    if ($hp < 1) {
        $errors[] = "HP must be greater than 0.";
    }
    
    // If no errors, insert into the database
    if (empty($errors)) {
        $sql = "INSERT INTO npc (desc_en, classId, lvl, hp, mp, ac, str, con, dex, wis, intel, exp, alignment, big, impl) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $big = ($size === 'large') ? 'true' : 'false';
        $stmt->bind_param("siiiiiiiiiisiss", $desc_en, $classId, $level, $hp, $mp, $ac, $str, $con, $dex, $wis, $intel, $exp, $karma, $big, $impl);
        
        if ($stmt->execute()) {
            $message = "Monster added successfully! ID: " . $conn->insert_id;
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

// Get the list of existing monsters
$conn = getDbConnection();
$monsters = [];
$page = isset($_GET['monster_page']) ? (int)$_GET['monster_page'] : 1;
$limit = 25;
$offset = ($page - 1) * $limit;

$sql = "SELECT npcid, desc_en, lvl, hp, mp, ac, exp, alignment, big, impl 
        FROM npc 
        ORDER BY npcid DESC 
        LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $monsters[] = $row;
    }
}

// Get total count for pagination
$countSql = "SELECT COUNT(*) as total FROM npc";
$countResult = $conn->query($countSql);
$totalMonsters = $countResult ? $countResult->fetch_assoc()['total'] : 0;
$totalPages = ceil($totalMonsters / $limit);

$conn->close();
?>

<div class="row">
    <div class="col-md-12">
        <div class="admin-breadcrumb mb-3">
            <a href="index.php" class="btn btn-secondary btn-sm">
                <span class="admin-icon admin-icon-dashboard"></span>Back to Dashboard
            </a>
        </div>

<!-- Add New Monster Modal -->
<div class="modal fade" id="addMonsterModal" tabindex="-1" aria-labelledby="addMonsterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMonsterModalLabel"><span class="admin-icon admin-icon-monsters"></span>Add New Monster</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addMonsterForm" method="post" action="index.php?page=monsters">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-accent mb-3">Basic Information</h5>
                            
                            <div class="mb-3">
                                <label for="add_desc_en" class="form-label">Monster Name</label>
                                <input type="text" class="form-control" id="add_desc_en" name="desc_en" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_level" class="form-label">Level</label>
                                        <input type="number" class="form-control" id="add_level" name="level" min="1" max="100" value="1" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_size" class="form-label">Size</label>
                                        <select class="form-select" id="add_size" name="size">
                                            <option value="small">Small</option>
                                            <option value="large">Large</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_impl" class="form-label">Implementation</label>
                                        <select class="form-select" id="add_impl" name="impl">
                                            <option value="L1Monster">L1Monster</option>
                                            <option value="L1Npc">L1Npc</option>
                                            <option value="L1Merchant">L1Merchant</option>
                                            <option value="L1Guard">L1Guard</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_classId" class="form-label">Class ID</label>
                                        <input type="number" class="form-control" id="add_classId" name="classId" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="text-accent mb-3">Combat Stats</h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_hp" class="form-label">Hit Points</label>
                                        <input type="number" class="form-control" id="add_hp" name="hp" min="1" value="10" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_mp" class="form-label">Magic Points</label>
                                        <input type="number" class="form-control" id="add_mp" name="mp" min="0" value="1">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="add_ac" class="form-label">Armor Class</label>
                                <input type="number" class="form-control" id="add_ac" name="ac" value="0">
                                <div class="form-text">Lower values = better armor (can be negative)</div>
                            </div>
                            
                            <h6 class="text-accent mb-2">Attributes</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="add_str" class="form-label">STR</label>
                                        <input type="number" class="form-control" id="add_str" name="str" min="1" max="50" value="8">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="add_con" class="form-label">CON</label>
                                        <input type="number" class="form-control" id="add_con" name="con" min="1" max="50" value="8">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="add_dex" class="form-label">DEX</label>
                                        <input type="number" class="form-control" id="add_dex" name="dex" min="1" max="50" value="8">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="add_wis" class="form-label">WIS</label>
                                        <input type="number" class="form-control" id="add_wis" name="wis" min="1" max="50" value="8">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="add_intel" class="form-label">INT</label>
                                        <input type="number" class="form-control" id="add_intel" name="intel" min="1" max="50" value="8">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="add_karma" class="form-label">Alignment</label>
                                        <input type="number" class="form-control" id="add_karma" name="karma" value="0">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_exp" class="form-label">Experience Points</label>
                                        <input type="number" class="form-control" id="add_exp" name="exp" min="0" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveNewMonster">Add Monster</button>
            </div>
        </div>
    </div>
</div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h2><span class="admin-icon admin-icon-monsters"></span>Manage Monsters</h2>
            </div>
            <div class="card-body">
                <p>Create or modify monster definitions. Configure monster stats, behavior, and properties.</p>
                
                <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?>" role="alert">
                    <?php echo $message; ?>
                </div>
                <?php endif; ?>
                
                <div id="alertPlaceholder"></div>
                
                <!-- Add New Monster Button -->
                <div class="admin-header mb-4">
                    <h3 class="text-accent">Monster Management</h3>
                    <div class="admin-header-actions">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMonsterModal">
                            <span class="admin-icon admin-icon-monsters"></span>Add New Monster
                        </button>
                    </div>
                </div>
                
                <!-- Monster List View -->
                <div class="form-section">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="search" class="form-label">Search Monsters</label>
                            <input type="text" class="form-control search-input" id="search" data-table="monstersTable" placeholder="Search by name or ID...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Total Monsters</label>
                            <div class="form-control" style="background-color: var(--secondary); color: var(--accent);"><?php echo number_format($totalMonsters); ?> monsters</div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover data-table" id="monstersTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Level</th>
                                    <th>HP</th>
                                    <th>MP</th>
                                    <th>AC</th>
                                    <th>EXP</th>
                                    <th>Size</th>
                                    <th>Implementation</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($monsters as $monster): ?>
                                <tr>
                                    <td><?php echo $monster['npcid']; ?></td>
                                    <td class="text-highlight"><?php echo $monster['desc_en']; ?></td>
                                    <td><?php echo $monster['lvl']; ?></td>
                                    <td><?php echo number_format($monster['hp']); ?></td>
                                    <td><?php echo number_format($monster['mp']); ?></td>
                                    <td><?php echo $monster['ac']; ?></td>
                                    <td><?php echo number_format($monster['exp']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $monster['big'] === 'true' ? 'large' : 'small'; ?>">
                                            <?php echo $monster['big'] === 'true' ? 'Large' : 'Small'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $monster['impl']; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editMonsterModal" 
                                                data-id="<?php echo $monster['npcid']; ?>">
                                            Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteMonsterModal" 
                                                data-id="<?php echo $monster['npcid']; ?>" 
                                                data-name="<?php echo htmlspecialchars($monster['desc_en']); ?>">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if (empty($monsters)): ?>
                    <div class="alert alert-info">No monsters found.</div>
                    <?php endif; ?>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <nav aria-label="Monster pagination">
                        <ul class="pagination justify-content-center mt-4">
                            <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="index.php?page=monsters&monster_page=<?php echo $page - 1; ?>">Previous</a>
                            </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="index.php?page=monsters&monster_page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="index.php?page=monsters&monster_page=<?php echo $page + 1; ?>">Next</a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Monster Modal -->
<div class="modal fade" id="editMonsterModal" tabindex="-1" aria-labelledby="editMonsterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMonsterModalLabel">Edit Monster</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Monster editing functionality can be implemented here.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Monster Modal -->
<div class="modal fade" id="deleteMonsterModal" tabindex="-1" aria-labelledby="deleteMonsterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteMonsterModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this monster? This action cannot be undone.</p>
                <div id="deleteMonsterInfo"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle save new monster
    const saveNewMonster = document.getElementById('saveNewMonster');
    if (saveNewMonster) {
        saveNewMonster.addEventListener('click', function() {
            document.getElementById('addMonsterForm').submit();
        });
    }
    
    // Handle delete monster modal
    const deleteMonsterModal = document.getElementById('deleteMonsterModal');
    if (deleteMonsterModal) {
        deleteMonsterModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            
            const infoDiv = document.getElementById('deleteMonsterInfo');
            infoDiv.innerHTML = `<strong>Monster:</strong> ${name} (ID: ${id})`;
        });
    }
});
</script>
