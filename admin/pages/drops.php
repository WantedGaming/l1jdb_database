<?php
// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDbConnection();
    
    // Process form data
    $mobId = isset($_POST['mobId']) ? (int)$_POST['mobId'] : 0;
    $itemId = isset($_POST['itemId']) ? (int)$_POST['itemId'] : 0;
    $min = isset($_POST['min']) ? (int)$_POST['min'] : 1;
    $max = isset($_POST['max']) ? (int)$_POST['max'] : 1;
    $chance = isset($_POST['chance']) ? (int)$_POST['chance'] : 100000;
    
    // Validate the drop data
    $errors = [];
    
    if ($mobId <= 0) {
        $errors[] = "Monster ID is required and must be greater than 0.";
    }
    
    if ($itemId <= 0) {
        $errors[] = "Item ID is required and must be greater than 0.";
    }
    
    if ($min <= 0) {
        $errors[] = "Minimum quantity must be greater than 0.";
    }
    
    if ($max < $min) {
        $errors[] = "Maximum quantity must be greater than or equal to minimum quantity.";
    }
    
    if ($chance < 1 || $chance > 1000000) {
        $errors[] = "Drop chance must be between 1 and 1,000,000.";
    }
    
    // If no errors, insert into the database
    if (empty($errors)) {
        $sql = "INSERT INTO droplist (mobId, itemId, min, max, chance) VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiii", $mobId, $itemId, $min, $max, $chance);
        
        if ($stmt->execute()) {
            $message = "Drop added successfully!";
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

// Get the list of monsters for reference
$conn = getDbConnection();
$monster_list = [];

$sql = "SELECT npcid, desc_en, spriteId FROM npc WHERE desc_en IS NOT NULL AND desc_en != '' ORDER BY desc_en LIMIT 100";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $monster_list[] = $row;
    }
}

// Get the list of items for reference
$item_list = [];

// Get weapons
$sql = "SELECT item_id, desc_en, iconId, 'weapon' as type FROM weapon WHERE desc_en IS NOT NULL AND desc_en != '' ORDER BY desc_en LIMIT 50";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $item_list[] = $row;
    }
}

// Get armor
$sql = "SELECT item_id, desc_en, iconId, 'armor' as type FROM armor WHERE desc_en IS NOT NULL AND desc_en != '' ORDER BY desc_en LIMIT 50";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $item_list[] = $row;
    }
}

// Get items
$sql = "SELECT item_id, desc_en, iconId, 'item' as type FROM etcitem WHERE desc_en IS NOT NULL AND desc_en != '' ORDER BY desc_en LIMIT 50";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $item_list[] = $row;
    }
}

// Get the list of existing drops
$drops = [];
$page = isset($_GET['drop_page']) ? (int)$_GET['drop_page'] : 1;
$limit = 25;
$offset = ($page - 1) * $limit;

$sql = "SELECT d.*, n.desc_en as monster_name, n.spriteId as monster_sprite,
               COALESCE(w.desc_en, a.desc_en, e.desc_en) as item_name,
               COALESCE(w.iconId, a.iconId, e.iconId) as item_icon,
               CASE 
                   WHEN w.item_id IS NOT NULL THEN 'Weapon'
                   WHEN a.item_id IS NOT NULL THEN 'Armor'
                   WHEN e.item_id IS NOT NULL THEN 'Item'
                   ELSE 'Unknown'
               END as item_type
        FROM droplist d 
        LEFT JOIN npc n ON d.mobId = n.npcid 
        LEFT JOIN weapon w ON d.itemId = w.item_id
        LEFT JOIN armor a ON d.itemId = a.item_id
        LEFT JOIN etcitem e ON d.itemId = e.item_id
        ORDER BY d.mobId, d.itemId 
        LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $drops[] = $row;
    }
}

// Get total count for pagination
$countSql = "SELECT COUNT(*) as total FROM droplist";
$countResult = $conn->query($countSql);
$totalDrops = $countResult ? $countResult->fetch_assoc()['total'] : 0;
$totalPages = ceil($totalDrops / $limit);

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
                <h2><span class="admin-icon admin-icon-drops"></span>Manage Drops</h2>
            </div>
            <div class="card-body">
                <p>Add or modify item drops for monsters. Configure drop rates, quantities, and which items monsters will drop.</p>
                
                <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?>" role="alert">
                    <?php echo $message; ?>
                </div>
                <?php endif; ?>
                
                <div id="alertPlaceholder"></div>
                
                <!-- Add New Drop Button -->
                <div class="admin-header mb-4">
                    <h3 class="text-accent">Drop Management</h3>
                    <div class="admin-header-actions">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDropModal">
                            <span class="admin-icon admin-icon-drops"></span>Add New Drop
                        </button>
                    </div>
                </div>
                
                <!-- Drop List View -->
                <div class="form-section">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="search" class="form-label">Search Drops</label>
                            <input type="text" class="form-control search-input" id="search" data-table="dropsTable" placeholder="Search by monster or item name...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Total Drops</label>
                            <div class="form-control" style="background-color: var(--secondary); color: var(--accent);"><?php echo number_format($totalDrops); ?> drops</div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover data-table" id="dropsTable">
                            <thead>
                                <tr>
                                    <th>Monster</th>
                                    <th>Monster Name</th>
                                    <th>Item</th>
                                    <th>Item Name</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>Drop Rate</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($drops as $drop): ?>
                                <tr class="clickable-row" data-mobid="<?php echo $drop['mobId']; ?>" data-itemid="<?php echo $drop['itemId']; ?>">
                                    <td class="icon-cell">
                                        <img src="<?php echo '/l1jdb_database/assets/img/icons/ms' . $drop['monster_sprite'] . '.png'; ?>" 
                                             alt="Monster Icon" 
                                             onerror="this.src='/l1jdb_database/assets/img/icons/ms<?php echo $drop['monster_sprite']; ?>.gif'; this.onerror=function(){this.src='/l1jdb_database/assets/img/placeholders/monsters.png';}">
                                    </td>
                                    <td><?php echo $drop['monster_name'] ?: 'Unknown Monster'; ?></td>
                                    <td class="icon-cell">
                                        <img src="<?php echo '/l1jdb_database/assets/img/icons/' . $drop['item_icon'] . '.png'; ?>" 
                                             alt="Item Icon" 
                                             onerror="this.src='/l1jdb_database/assets/img/icons/0.png'">
                                    </td>
                                    <td><?php echo $drop['item_name'] ?: 'Unknown Item'; ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo strtolower($drop['item_type']); ?>">
                                            <?php echo $drop['item_type']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $drop['min']; ?><?php echo ($drop['max'] > $drop['min']) ? ' - ' . $drop['max'] : ''; ?></td>
                                    <td><?php echo number_format(($drop['chance'] / 10000), 2); ?>%</td>
                                    <td class="table-cell-actions">
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editDropModal" 
                                                data-mobid="<?php echo $drop['mobId']; ?>" 
                                                data-itemid="<?php echo $drop['itemId']; ?>"
                                                onclick="event.stopPropagation();">
                                            Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteDropModal" 
                                                data-mobid="<?php echo $drop['mobId']; ?>" 
                                                data-itemid="<?php echo $drop['itemId']; ?>"
                                                onclick="event.stopPropagation();">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if (empty($drops)): ?>
                    <div class="alert alert-info">No drops found.</div>
                    <?php endif; ?>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <nav aria-label="Drop pagination">
                        <ul class="pagination justify-content-center mt-4">
                            <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="index.php?page=drops&drop_page=<?php echo $page - 1; ?>">Previous</a>
                            </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="index.php?page=drops&drop_page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="index.php?page=drops&drop_page=<?php echo $page + 1; ?>">Next</a>
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

<!-- Add New Drop Modal -->
<div class="modal fade" id="addDropModal" tabindex="-1" aria-labelledby="addDropModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDropModalLabel"><span class="admin-icon admin-icon-drops"></span>Add New Drop</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addDropForm" method="post" action="index.php?page=drops">
                    <!-- Monster Selection Section -->
                    <div class="modal-form-section">
                        <h6 class="modal-section-title">Monster Selection</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="monsterSearch" class="form-label">Search Monster</label>
                                    <input type="text" class="form-control" id="monsterSearch" placeholder="Search by monster name">
                                    <div id="monsterSuggestions" class="suggestion-list mt-2"></div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="mobId" class="form-label">Monster ID</label>
                                    <input type="number" class="form-control" id="mobId" name="mobId" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Monster Preview</label>
                                    <div id="monster_preview" class="preview-container">
                                        <img id="monster_image" src="/l1jdb_database/assets/img/placeholders/monsters.png" alt="Monster Preview">
                                        <div class="preview-info">
                                            <div id="monster_name" class="preview-name preview-placeholder">Select a monster to see preview</div>
                                            <div id="monster_id_display" class="preview-id"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Item Selection Section -->
                    <div class="modal-form-section">
                        <h6 class="modal-section-title">Item Selection</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="itemSearch" class="form-label">Search Item</label>
                                    <input type="text" class="form-control" id="itemSearch" placeholder="Search by item name">
                                    <div id="itemSuggestions" class="suggestion-list mt-2"></div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="itemId" class="form-label">Item ID</label>
                                    <input type="number" class="form-control" id="itemId" name="itemId" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Item Preview</label>
                                    <div id="item_preview" class="preview-container">
                                        <img id="item_image" src="/l1jdb_database/assets/img/icons/0.png" alt="Item Preview">
                                        <div class="preview-info">
                                            <div id="item_name" class="preview-name preview-placeholder">Select an item to see preview</div>
                                            <div id="item_id_display" class="preview-id"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Drop Configuration Section -->
                    <div class="modal-form-section">
                        <h6 class="modal-section-title">Drop Configuration</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="min" class="form-label">Min Quantity</label>
                                    <input type="number" class="form-control" id="min" name="min" min="1" value="1" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="max" class="form-label">Max Quantity</label>
                                    <input type="number" class="form-control" id="max" name="max" min="1" value="1" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="chance" class="form-label">Drop Chance (1-1000000)</label>
                                    <input type="number" class="form-control" id="chance" name="chance" min="1" max="1000000" value="100000" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Drop Rate Preview</label>
                                    <div class="drop-rate-preview" id="chancePreview">10.00%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveNewDrop">Add Drop</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Drop Modal -->
<div class="modal fade" id="editDropModal" tabindex="-1" aria-labelledby="editDropModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDropModalLabel">Edit Drop</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editDropForm">
                    <input type="hidden" id="editMobId" name="mobId">
                    <input type="hidden" id="editItemId" name="itemId">
                    
                    <!-- Current Drop Information -->
                    <div class="modal-form-section">
                        <h6 class="modal-section-title">Current Drop Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Monster</label>
                                    <div id="edit_monster_preview" class="preview-container">
                                        <img id="edit_monster_image" src="/l1jdb_database/assets/img/placeholders/monsters.png" alt="Monster Preview">
                                        <div class="preview-info">
                                            <div id="edit_monster_name" class="preview-name">Loading...</div>
                                            <div id="edit_monster_id_display" class="preview-id"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Item</label>
                                    <div id="edit_item_preview" class="preview-container">
                                        <img id="edit_item_image" src="/l1jdb_database/assets/img/icons/0.png" alt="Item Preview">
                                        <div class="preview-info">
                                            <div id="edit_item_name" class="preview-name">Loading...</div>
                                            <div id="edit_item_id_display" class="preview-id"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Drop Configuration -->
                    <div class="modal-form-section">
                        <h6 class="modal-section-title">Drop Configuration</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="editMin" class="form-label">Min Quantity</label>
                                    <input type="number" class="form-control" id="editMin" name="min" min="1" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="editMax" class="form-label">Max Quantity</label>
                                    <input type="number" class="form-control" id="editMax" name="max" min="1" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="editChance" class="form-label">Drop Chance (1-1000000)</label>
                                    <input type="number" class="form-control" id="editChance" name="chance" min="1" max="1000000" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Drop Rate Preview</label>
                                    <div class="drop-rate-preview" id="editChancePreview">0.00%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveDropChanges">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Drop Modal -->
<div class="modal fade" id="deleteDropModal" tabindex="-1" aria-labelledby="deleteDropModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteDropModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this drop? This action cannot be undone.</p>
                <div id="deleteDropInfo"></div>
                <form id="deleteDropForm">
                    <input type="hidden" id="deleteMobId" name="mobId">
                    <input type="hidden" id="deleteItemId" name="itemId">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteDrop">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
// Handle clickable rows and drop functionality
document.addEventListener('DOMContentLoaded', function() {
    // Make table rows clickable
    document.querySelectorAll('.clickable-row').forEach(function(row) {
        row.addEventListener('click', function(e) {
            // Don't trigger if clicking on buttons
            if (e.target.tagName === 'BUTTON' || e.target.closest('button')) {
                return;
            }
            
            const mobId = this.getAttribute('data-mobid');
            const itemId = this.getAttribute('data-itemid');
            
            // Trigger edit modal
            const editButton = this.querySelector('[data-bs-target="#editDropModal"]');
            if (editButton) {
                editButton.click();
            }
        });
    });
    
    const chanceInput = document.getElementById('chance');
    const chancePreview = document.getElementById('chancePreview');
    
    function updateChancePreview() {
        const chance = parseInt(chanceInput.value) || 0;
        const percentage = (chance / 10000).toFixed(4);
        chancePreview.textContent = percentage + '%';
    }
    
    if (chanceInput) {
        chanceInput.addEventListener('input', updateChancePreview);
        updateChancePreview(); // Initial calculation
    }
    
    // Function to update monster preview
    function updateMonsterPreview(modalType, monsterId, monsterName, spriteId) {
        const prefix = modalType ? modalType + '_' : '';
        const imageElement = document.getElementById(prefix + 'monster_image');
        const nameElement = document.getElementById(prefix + 'monster_name');
        const idElement = document.getElementById(prefix + 'monster_id_display');
        
        if (monsterId && spriteId) {
            const imagePath = `/l1jdb_database/assets/img/icons/ms${spriteId}.png`;
            imageElement.src = imagePath;
            imageElement.onerror = function() {
                this.src = `/l1jdb_database/assets/img/icons/ms${spriteId}.gif`;
                this.onerror = function() {
                    this.src = '/l1jdb_database/assets/img/placeholders/monsters.png';
                };
            };
            nameElement.textContent = monsterName || `Monster ID: ${monsterId}`;
            nameElement.className = 'preview-name';
            if (idElement) idElement.textContent = `ID: ${monsterId}`;
        } else {
            imageElement.src = '/l1jdb_database/assets/img/placeholders/monsters.png';
            nameElement.textContent = modalType === '' ? 'Select a monster to see preview' : 'Unknown Monster';
            nameElement.className = 'preview-name preview-placeholder';
            if (idElement) idElement.textContent = '';
        }
    }
    
    // Function to update item preview
    function updateItemPreview(modalType, itemId, itemName, iconId, itemType) {
        const prefix = modalType ? modalType + '_' : '';
        const imageElement = document.getElementById(prefix + 'item_image');
        const nameElement = document.getElementById(prefix + 'item_name');
        const idElement = document.getElementById(prefix + 'item_id_display');
        
        if (itemId && iconId) {
            const imagePath = `/l1jdb_database/assets/img/icons/${iconId}.png`;
            imageElement.src = imagePath;
            imageElement.onerror = function() {
                this.src = '/l1jdb_database/assets/img/icons/0.png';
            };
            nameElement.textContent = itemName || `Item ID: ${itemId}`;
            nameElement.className = 'preview-name';
            if (idElement) {
                idElement.textContent = `ID: ${itemId}` + (itemType ? ` (${itemType})` : '');
            }
        } else {
            imageElement.src = '/l1jdb_database/assets/img/icons/0.png';
            nameElement.textContent = modalType === '' ? 'Select an item to see preview' : 'Unknown Item';
            nameElement.className = 'preview-name preview-placeholder';
            if (idElement) idElement.textContent = '';
        }
    }
    
    const monsters = <?php echo json_encode($monster_list); ?>;
    const items = <?php echo json_encode($item_list); ?>;
    
    // Function to find monster data by ID
    function findMonsterById(monsterId) {
        return monsters.find(monster => monster.npcid == monsterId);
    }
    
    // Function to find item data by ID
    function findItemById(itemId) {
        return items.find(item => item.item_id == itemId);
    }
    
    // Monster search functionality
    const monsterSearch = document.getElementById('monsterSearch');
    const monsterSuggestions = document.getElementById('monsterSuggestions');
    const mobIdInput = document.getElementById('mobId');
    
    if (monsterSearch) {
        monsterSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            monsterSuggestions.innerHTML = '';
            
            if (searchTerm.length < 2) {
                monsterSuggestions.style.display = 'none';
                return;
            }
            
            const filteredMonsters = monsters.filter(monster => 
                monster.desc_en.toLowerCase().includes(searchTerm)
            ).slice(0, 10);
            
            if (filteredMonsters.length > 0) {
                monsterSuggestions.style.display = 'block';
                filteredMonsters.forEach(monster => {
                    const suggestion = document.createElement('div');
                    suggestion.className = 'suggestion-item';
                    
                    const monsterImg = document.createElement('img');
                    monsterImg.src = `/l1jdb_database/assets/img/icons/ms${monster.spriteId}.png`;
                    monsterImg.alt = monster.desc_en;
                    monsterImg.onerror = function() {
                        this.src = `/l1jdb_database/assets/img/icons/ms${monster.spriteId}.gif`;
                        this.onerror = function() {
                            this.src = '/l1jdb_database/assets/img/placeholders/monsters.png';
                        };
                    };
                    
                    const suggestionText = document.createElement('div');
                    suggestionText.className = 'suggestion-text';
                    suggestionText.textContent = `${monster.desc_en} (ID: ${monster.npcid})`;
                    
                    suggestion.appendChild(monsterImg);
                    suggestion.appendChild(suggestionText);
                    
                    suggestion.addEventListener('click', function() {
                        monsterSearch.value = monster.desc_en;
                        mobIdInput.value = monster.npcid;
                        monsterSuggestions.innerHTML = '';
                        monsterSuggestions.style.display = 'none';
                        updateMonsterPreview('', monster.npcid, monster.desc_en, monster.spriteId);
                    });
                    
                    monsterSuggestions.appendChild(suggestion);
                });
            } else {
                monsterSuggestions.style.display = 'none';
            }
        });
        
        // Listen for manual Monster ID input
        mobIdInput.addEventListener('input', function() {
            const monsterId = this.value;
            const monster = findMonsterById(monsterId);
            if (monster) {
                updateMonsterPreview('', monster.npcid, monster.desc_en, monster.spriteId);
            } else {
                updateMonsterPreview('', null, null, null);
            }
        });
    }
    
    // Item search functionality
    const itemSearch = document.getElementById('itemSearch');
    const itemSuggestions = document.getElementById('itemSuggestions');
    const itemIdInput = document.getElementById('itemId');
    
    if (itemSearch) {
        itemSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            itemSuggestions.innerHTML = '';
            
            if (searchTerm.length < 2) {
                itemSuggestions.style.display = 'none';
                return;
            }
            
            const filteredItems = items.filter(item => 
                item.desc_en.toLowerCase().includes(searchTerm)
            ).slice(0, 10);
            
            if (filteredItems.length > 0) {
                itemSuggestions.style.display = 'block';
                filteredItems.forEach(item => {
                    const suggestion = document.createElement('div');
                    suggestion.className = 'suggestion-item';
                    
                    const itemImg = document.createElement('img');
                    itemImg.src = `/l1jdb_database/assets/img/icons/${item.iconId}.png`;
                    itemImg.alt = item.desc_en;
                    itemImg.onerror = function() {
                        this.src = '/l1jdb_database/assets/img/icons/0.png';
                    };
                    
                    const suggestionText = document.createElement('div');
                    suggestionText.className = 'suggestion-text';
                    suggestionText.textContent = `${item.desc_en} (ID: ${item.item_id})`;
                    
                    const suggestionType = document.createElement('div');
                    suggestionType.className = 'suggestion-type';
                    suggestionType.textContent = item.type;
                    
                    suggestion.appendChild(itemImg);
                    suggestion.appendChild(suggestionText);
                    suggestion.appendChild(suggestionType);
                    
                    suggestion.addEventListener('click', function() {
                        itemSearch.value = item.desc_en;
                        itemIdInput.value = item.item_id;
                        itemSuggestions.innerHTML = '';
                        itemSuggestions.style.display = 'none';
                        updateItemPreview('', item.item_id, item.desc_en, item.iconId, item.type);
                    });
                    
                    itemSuggestions.appendChild(suggestion);
                });
            } else {
                itemSuggestions.style.display = 'none';
            }
        });
        
        // Listen for manual Item ID input
        itemIdInput.addEventListener('input', function() {
            const itemId = this.value;
            const item = findItemById(itemId);
            if (item) {
                updateItemPreview('', item.item_id, item.desc_en, item.iconId, item.type);
            } else {
                updateItemPreview('', null, null, null);
            }
        });
    }
    
    // Handle save new drop
    const saveNewDrop = document.getElementById('saveNewDrop');
    if (saveNewDrop) {
        saveNewDrop.addEventListener('click', function() {
            document.getElementById('addDropForm').submit();
        });
    }
    
    // Handle edit drop modal
    const editDropModal = document.getElementById('editDropModal');
    if (editDropModal) {
        editDropModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const mobId = button.getAttribute('data-mobid');
            const itemId = button.getAttribute('data-itemid');
            
            document.getElementById('editMobId').value = mobId;
            document.getElementById('editItemId').value = itemId;
            
            // Find and display monster info
            const monster = findMonsterById(mobId);
            if (monster) {
                updateMonsterPreview('edit', monster.npcid, monster.desc_en, monster.spriteId);
            }
            
            // Find and display item info
            const item = findItemById(itemId);
            if (item) {
                updateItemPreview('edit', item.item_id, item.desc_en, item.iconId, item.type);
            }
            
            // Fetch existing drop data
            fetch(`/l1jdb_database/admin/api/get_drop.php?mobId=${mobId}&itemId=${itemId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('editMin').value = data.drop.min;
                        document.getElementById('editMax').value = data.drop.max;
                        document.getElementById('editChance').value = data.drop.chance;
                        
                        // Update chance preview
                        const editChancePreview = document.getElementById('editChancePreview');
                        const percentage = (data.drop.chance / 10000).toFixed(4);
                        editChancePreview.textContent = percentage + '%';
                    }
                })
                .catch(error => {
                    console.error('Error fetching drop data:', error);
                });
        });
    }
    
    // Handle edit chance preview
    const editChanceInput = document.getElementById('editChance');
    const editChancePreview = document.getElementById('editChancePreview');
    
    if (editChanceInput) {
        editChanceInput.addEventListener('input', function() {
            const chance = parseInt(this.value) || 0;
            const percentage = (chance / 10000).toFixed(4);
            editChancePreview.textContent = percentage + '%';
        });
    }
});
</script>
