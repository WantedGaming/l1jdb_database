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

$sql = "SELECT npcid, desc_en FROM npc WHERE desc_en IS NOT NULL AND desc_en != '' ORDER BY desc_en LIMIT 100";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $monster_list[] = $row;
    }
}

// Get the list of items for reference
$item_list = [];

// Get weapons
$sql = "SELECT item_id, desc_en, 'weapon' as type FROM weapon WHERE desc_en IS NOT NULL AND desc_en != '' ORDER BY desc_en LIMIT 50";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $item_list[] = $row;
    }
}

// Get armor
$sql = "SELECT item_id, desc_en, 'armor' as type FROM armor WHERE desc_en IS NOT NULL AND desc_en != '' ORDER BY desc_en LIMIT 50";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $item_list[] = $row;
    }
}

// Get items
$sql = "SELECT item_id, desc_en, 'item' as type FROM etcitem WHERE desc_en IS NOT NULL AND desc_en != '' ORDER BY desc_en LIMIT 50";
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

$sql = "SELECT d.*, n.desc_en as monster_name,
               COALESCE(w.desc_en, a.desc_en, e.desc_en) as item_name,
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
                                    <th>Monster ID</th>
                                    <th>Monster Name</th>
                                    <th>Item ID</th>
                                    <th>Item Name</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>Drop Rate</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($drops as $drop): ?>
                                <tr>
                                    <td><?php echo $drop['mobId']; ?></td>
                                    <td><?php echo $drop['monster_name'] ?: 'Unknown Monster'; ?></td>
                                    <td><?php echo $drop['itemId']; ?></td>
                                    <td><?php echo $drop['item_name'] ?: 'Unknown Item'; ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo strtolower($drop['item_type']); ?>">
                                            <?php echo $drop['item_type']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $drop['min']; ?><?php echo ($drop['max'] > $drop['min']) ? ' - ' . $drop['max'] : ''; ?></td>
                                    <td><?php echo number_format(($drop['chance'] / 10000), 2); ?>%</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editDropModal" 
                                                data-mobid="<?php echo $drop['mobId']; ?>" 
                                                data-itemid="<?php echo $drop['itemId']; ?>">
                                            Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteDropModal" 
                                                data-mobid="<?php echo $drop['mobId']; ?>" 
                                                data-itemid="<?php echo $drop['itemId']; ?>">
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

<script>
// Handle drop chance calculation
document.addEventListener('DOMContentLoaded', function() {
    const chanceInput = document.getElementById('chance');
    const chancePreview = document.getElementById('chancePreview');
    
    function updateChancePreview() {
        const chance = parseInt(chanceInput.value) || 0;
        const percentage = (chance / 10000).toFixed(4);
        chancePreview.textContent = percentage + '%';
    }
    
    chanceInput.addEventListener('input', updateChancePreview);
    updateChancePreview(); // Initial calculation
    
    // Monster search functionality
    const monsterSearch = document.getElementById('monsterSearch');
    const monsterSuggestions = document.getElementById('monsterSuggestions');
    const mobIdInput = document.getElementById('mobId');
    
    const monsters = <?php echo json_encode($monster_list); ?>;
    
    monsterSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        monsterSuggestions.innerHTML = '';
        
        if (searchTerm.length < 2) return;
        
        const filteredMonsters = monsters.filter(monster => 
            monster.desc_en.toLowerCase().includes(searchTerm)
        ).slice(0, 10);
        
        filteredMonsters.forEach(monster => {
            const suggestion = document.createElement('div');
            suggestion.className = 'list-group-item list-group-item-action';
            suggestion.style.cursor = 'pointer';
            suggestion.textContent = `${monster.desc_en} (ID: ${monster.npcid})`;
            
            suggestion.addEventListener('click', function() {
                monsterSearch.value = monster.desc_en;
                mobIdInput.value = monster.npcid;
                monsterSuggestions.innerHTML = '';
            });
            
            monsterSuggestions.appendChild(suggestion);
        });
    });
    
    // Item search functionality
    const itemSearch = document.getElementById('itemSearch');
    const itemSuggestions = document.getElementById('itemSuggestions');
    const itemIdInput = document.getElementById('itemId');
    
    const items = <?php echo json_encode($item_list); ?>;
    
    itemSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        itemSuggestions.innerHTML = '';
        
        if (searchTerm.length < 2) return;
        
        const filteredItems = items.filter(item => 
            item.desc_en.toLowerCase().includes(searchTerm)
        ).slice(0, 10);
        
        filteredItems.forEach(item => {
            const suggestion = document.createElement('div');
            suggestion.className = 'list-group-item list-group-item-action';
            suggestion.style.cursor = 'pointer';
            suggestion.textContent = `${item.desc_en} (ID: ${item.item_id}) [${item.type}]`;
            
            suggestion.addEventListener('click', function() {
                itemSearch.value = item.desc_en;
                itemIdInput.value = item.item_id;
                itemSuggestions.innerHTML = '';
            });
            
            itemSuggestions.appendChild(suggestion);
        });
    });
});
</script>
