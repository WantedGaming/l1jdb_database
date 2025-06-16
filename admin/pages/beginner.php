<?php
// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDbConnection();
    
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'add') {
            // Process add form data
            $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
            $count = isset($_POST['count']) ? (int)$_POST['count'] : 1;
            $charge_count = isset($_POST['charge_count']) ? (int)$_POST['charge_count'] : 0;
            $enchantlvl = isset($_POST['enchantlvl']) ? (int)$_POST['enchantlvl'] : 0;
            $item_name = isset($_POST['item_name']) ? sanitizeInput($_POST['item_name']) : '';
            $desc_kr = isset($_POST['desc_kr']) ? sanitizeInput($_POST['desc_kr']) : '';
            $activate = isset($_POST['activate']) ? sanitizeInput($_POST['activate']) : 'A';
            
            // Validate the beginner item data
            $errors = [];
            
            if ($item_id <= 0) {
                $errors[] = "Item ID is required and must be greater than 0.";
            }
            
            if ($count <= 0) {
                $errors[] = "Count must be greater than 0.";
            }
            
            if (empty($item_name)) {
                $errors[] = "Item name is required.";
            }
            
            // If no errors, insert into the database
            if (empty($errors)) {
                $sql = "INSERT INTO beginner (item_id, count, charge_count, enchantlvl, item_name, desc_kr, activate) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iiiisss", $item_id, $count, $charge_count, $enchantlvl, $item_name, $desc_kr, $activate);
                
                if ($stmt->execute()) {
                    $message = "Beginner item added successfully!";
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
        } elseif ($action === 'edit') {
            // Process edit form data
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $item_id = isset($_POST['edit_item_id']) ? (int)$_POST['edit_item_id'] : 0;
            $count = isset($_POST['edit_count']) ? (int)$_POST['edit_count'] : 1;
            $charge_count = isset($_POST['edit_charge_count']) ? (int)$_POST['edit_charge_count'] : 0;
            $enchantlvl = isset($_POST['edit_enchantlvl']) ? (int)$_POST['edit_enchantlvl'] : 0;
            $item_name = isset($_POST['edit_item_name']) ? sanitizeInput($_POST['edit_item_name']) : '';
            $desc_kr = isset($_POST['edit_desc_kr']) ? sanitizeInput($_POST['edit_desc_kr']) : '';
            $activate = isset($_POST['edit_activate']) ? sanitizeInput($_POST['edit_activate']) : 'A';
            
            if ($id > 0 && $item_id > 0 && $count > 0 && !empty($item_name)) {
                $sql = "UPDATE beginner SET item_id=?, count=?, charge_count=?, enchantlvl=?, item_name=?, desc_kr=?, activate=? WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iiiisssi", $item_id, $count, $charge_count, $enchantlvl, $item_name, $desc_kr, $activate, $id);
                
                if ($stmt->execute()) {
                    $message = "Beginner item updated successfully!";
                    $messageType = "success";
                } else {
                    $message = "Error updating item: " . $stmt->error;
                    $messageType = "danger";
                }
                $stmt->close();
            }
        } elseif ($action === 'delete') {
            // Process delete
            $id = isset($_POST['delete_id']) ? (int)$_POST['delete_id'] : 0;
            
            if ($id > 0) {
                $sql = "DELETE FROM beginner WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id);
                
                if ($stmt->execute()) {
                    $message = "Beginner item deleted successfully!";
                    $messageType = "success";
                } else {
                    $message = "Error deleting item: " . $stmt->error;
                    $messageType = "danger";
                }
                $stmt->close();
            }
        }
    }
    
    $conn->close();
}

// Get the list of items for reference (weapons, armor, etcitems)
$conn = getDbConnection();
$item_list = [];

// Get weapons
$sql = "SELECT item_id, desc_en, 'weapon' as type FROM weapon WHERE desc_en IS NOT NULL AND desc_en != '' ORDER BY desc_en LIMIT 100";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $item_list[] = $row;
    }
}

// Get armor
$sql = "SELECT item_id, desc_en, 'armor' as type FROM armor WHERE desc_en IS NOT NULL AND desc_en != '' ORDER BY desc_en LIMIT 100";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $item_list[] = $row;
    }
}

// Get items
$sql = "SELECT item_id, desc_en, 'item' as type FROM etcitem WHERE desc_en IS NOT NULL AND desc_en != '' ORDER BY desc_en LIMIT 100";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $item_list[] = $row;
    }
}

// Get the list of existing beginner items
$beginner_items = [];
$page = isset($_GET['beginner_page']) ? (int)$_GET['beginner_page'] : 1;
$limit = 25;
$offset = ($page - 1) * $limit;

$sql = "SELECT b.*, 
               COALESCE(w.desc_en, a.desc_en, e.desc_en) as item_desc,
               CASE 
                   WHEN w.item_id IS NOT NULL THEN 'Weapon'
                   WHEN a.item_id IS NOT NULL THEN 'Armor'
                   WHEN e.item_id IS NOT NULL THEN 'Item'
                   ELSE 'Unknown'
               END as item_type
        FROM beginner b 
        LEFT JOIN weapon w ON b.item_id = w.item_id
        LEFT JOIN armor a ON b.item_id = a.item_id
        LEFT JOIN etcitem e ON b.item_id = e.item_id
        ORDER BY b.id DESC 
        LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $beginner_items[] = $row;
    }
}

// Get total count for pagination
$countSql = "SELECT COUNT(*) as total FROM beginner";
$countResult = $conn->query($countSql);
$totalItems = $countResult ? $countResult->fetch_assoc()['total'] : 0;
$totalPages = ceil($totalItems / $limit);

$conn->close();

// Character class mappings
$class_mappings = [
    'A' => 'All Classes',
    'P' => 'Prince',
    'K' => 'Knight', 
    'E' => 'Elf',
    'W' => 'Wizard',
    'D' => 'Dark Elf',
    'T' => 'Tamer',
    'B' => 'Berserker',
    'J' => 'Jester',
    'F' => 'Fighter',
    'L' => 'Lancer'
];
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
                <h2><span class="admin-icon admin-icon-beginner"></span>Beginner Items Management</h2>
            </div>
            <div class="card-body">
                <p>Configure starting items that new characters will receive when they create their character. Set item quantities, enchantment levels, and class restrictions.</p>
                
                <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?>" role="alert">
                    <?php echo $message; ?>
                </div>
                <?php endif; ?>
                
                <div id="alertPlaceholder"></div>
                
                <ul class="nav nav-tabs" id="beginnerTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="add-item-tab" data-bs-toggle="tab" data-bs-target="#add-item" type="button" role="tab" aria-controls="add-item" aria-selected="true">Add Beginner Item</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="view-items-tab" data-bs-toggle="tab" data-bs-target="#view-items" type="button" role="tab" aria-controls="view-items" aria-selected="false">View Beginner Items</button>
                    </li>
                </ul>
                
                <div class="tab-content" id="beginnerTabsContent">
                    <div class="tab-pane fade show active" id="add-item" role="tabpanel" aria-labelledby="add-item-tab">
                        <div class="form-section mt-4">
                            <form id="beginnerForm" method="post" action="index.php?page=beginner">
                                <input type="hidden" name="action" value="add">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5 class="text-accent mb-3">Item Selection</h5>
                                        
                                        <div class="mb-3">
                                            <label for="itemSearch" class="form-label">Search Item</label>
                                            <input type="text" class="form-control" id="itemSearch" placeholder="Search by item name">
                                            <div id="itemSuggestions" class="list-group mt-2"></div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="item_id" class="form-label">Item ID</label>
                                            <input type="number" class="form-control" id="item_id" name="item_id" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="item_name" class="form-label">Item Name</label>
                                            <input type="text" class="form-control" id="item_name" name="item_name" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="desc_kr" class="form-label">Korean Description</label>
                                            <input type="text" class="form-control" id="desc_kr" name="desc_kr">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h5 class="text-accent mb-3">Item Properties</h5>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="count" class="form-label">Count</label>
                                                    <input type="number" class="form-control" id="count" name="count" min="1" value="1" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="charge_count" class="form-label">Charge Count</label>
                                                    <input type="number" class="form-control" id="charge_count" name="charge_count" min="0" value="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="enchantlvl" class="form-label">Enchantment Level</label>
                                            <input type="number" class="form-control" id="enchantlvl" name="enchantlvl" min="0" max="15" value="0">
                                            <div class="form-text">Enchantment level (0-15)</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="activate" class="form-label">Character Class Restriction</label>
                                            <select class="form-select" id="activate" name="activate">
                                                <?php foreach ($class_mappings as $code => $name): ?>
                                                <option value="<?php echo $code; ?>" <?php echo ($code === 'A') ? 'selected' : ''; ?>><?php echo $name; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Add Beginner Item</button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="view-items" role="tabpanel" aria-labelledby="view-items-tab">
                        <div class="form-section mt-4">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="search" class="form-label">Search Beginner Items</label>
                                    <input type="text" class="form-control search-input" id="search" data-table="beginnerTable" placeholder="Search by item name...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Total Items</label>
                                    <div class="form-control" style="background-color: var(--secondary); color: var(--accent);"><?php echo number_format($totalItems); ?> items</div>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-striped table-hover data-table" id="beginnerTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Item ID</th>
                                            <th>Item Name</th>
                                            <th>Type</th>
                                            <th>Count</th>
                                            <th>Enchant</th>
                                            <th>Charges</th>
                                            <th>Class</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($beginner_items as $item): ?>
                                        <tr>
                                            <td><?php echo $item['id']; ?></td>
                                            <td><?php echo $item['item_id']; ?></td>
                                            <td class="text-highlight"><?php echo $item['item_name']; ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo strtolower($item['item_type']); ?>">
                                                    <?php echo $item['item_type']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo $item['count']; ?></td>
                                            <td><?php echo $item['enchantlvl'] > 0 ? '+' . $item['enchantlvl'] : '0'; ?></td>
                                            <td><?php echo $item['charge_count']; ?></td>
                                            <td><?php echo $class_mappings[$item['activate']] ?? $item['activate']; ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editItemModal" 
                                                        data-id="<?php echo $item['id']; ?>"
                                                        data-item-id="<?php echo $item['item_id']; ?>"
                                                        data-item-name="<?php echo htmlspecialchars($item['item_name']); ?>"
                                                        data-count="<?php echo $item['count']; ?>"
                                                        data-charge-count="<?php echo $item['charge_count']; ?>"
                                                        data-enchantlvl="<?php echo $item['enchantlvl']; ?>"
                                                        data-desc-kr="<?php echo htmlspecialchars($item['desc_kr']); ?>"
                                                        data-activate="<?php echo $item['activate']; ?>">
                                                    Edit
                                                </button>
                                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteItemModal" 
                                                        data-id="<?php echo $item['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($item['item_name']); ?>">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <?php if (empty($beginner_items)): ?>
                            <div class="alert alert-info">No beginner items found.</div>
                            <?php endif; ?>
                            
                            <!-- Pagination -->
                            <?php if ($totalPages > 1): ?>
                            <nav aria-label="Beginner items pagination">
                                <ul class="pagination justify-content-center mt-4">
                                    <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="index.php?page=beginner&beginner_page=<?php echo $page - 1; ?>">Previous</a>
                                    </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                        <a class="page-link" href="index.php?page=beginner&beginner_page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="index.php?page=beginner&beginner_page=<?php echo $page + 1; ?>">Next</a>
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
    </div>
</div>

<!-- Edit Item Modal -->
<div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editItemModalLabel">Edit Beginner Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editItemForm" method="post" action="index.php?page=beginner">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" id="editId" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editItemId" class="form-label">Item ID</label>
                                <input type="number" class="form-control" id="editItemId" name="edit_item_id" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="editItemName" class="form-label">Item Name</label>
                                <input type="text" class="form-control" id="editItemName" name="edit_item_name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="editDescKr" class="form-label">Korean Description</label>
                                <input type="text" class="form-control" id="editDescKr" name="edit_desc_kr">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editCount" class="form-label">Count</label>
                                        <input type="number" class="form-control" id="editCount" name="edit_count" min="1" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editChargeCount" class="form-label">Charge Count</label>
                                        <input type="number" class="form-control" id="editChargeCount" name="edit_charge_count" min="0">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="editEnchantLvl" class="form-label">Enchantment Level</label>
                                <input type="number" class="form-control" id="editEnchantLvl" name="edit_enchantlvl" min="0" max="15">
                            </div>
                            
                            <div class="mb-3">
                                <label for="editActivate" class="form-label">Character Class Restriction</label>
                                <select class="form-select" id="editActivate" name="edit_activate">
                                    <?php foreach ($class_mappings as $code => $name): ?>
                                    <option value="<?php echo $code; ?>"><?php echo $name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveItemChanges">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Item Modal -->
<div class="modal fade" id="deleteItemModal" tabindex="-1" aria-labelledby="deleteItemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteItemModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this beginner item? This action cannot be undone.</p>
                <div id="deleteItemInfo"></div>
                <form id="deleteItemForm" method="post" action="index.php?page=beginner">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" id="deleteId" name="delete_id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteItem">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Item search functionality
    const itemSearch = document.getElementById('itemSearch');
    const itemSuggestions = document.getElementById('itemSuggestions');
    const itemIdInput = document.getElementById('item_id');
    const itemNameInput = document.getElementById('item_name');
    
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
                itemNameInput.value = item.desc_en;
                itemSuggestions.innerHTML = '';
            });
            
            itemSuggestions.appendChild(suggestion);
        });
    });
    
    // Handle edit item modal
    const editItemModal = document.getElementById('editItemModal');
    if (editItemModal) {
        editItemModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            
            document.getElementById('editId').value = button.getAttribute('data-id');
            document.getElementById('editItemId').value = button.getAttribute('data-item-id');
            document.getElementById('editItemName').value = button.getAttribute('data-item-name');
            document.getElementById('editCount').value = button.getAttribute('data-count');
            document.getElementById('editChargeCount').value = button.getAttribute('data-charge-count');
            document.getElementById('editEnchantLvl').value = button.getAttribute('data-enchantlvl');
            document.getElementById('editDescKr').value = button.getAttribute('data-desc-kr');
            document.getElementById('editActivate').value = button.getAttribute('data-activate');
        });
    }
    
    // Handle save changes button
    const saveItemChanges = document.getElementById('saveItemChanges');
    if (saveItemChanges) {
        saveItemChanges.addEventListener('click', function() {
            document.getElementById('editItemForm').submit();
        });
    }
    
    // Handle delete item modal
    const deleteItemModal = document.getElementById('deleteItemModal');
    if (deleteItemModal) {
        deleteItemModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteItemInfo').innerHTML = `<strong>Item:</strong> ${name} (ID: ${id})`;
        });
    }
    
    // Handle confirm delete button
    const confirmDeleteItem = document.getElementById('confirmDeleteItem');
    if (confirmDeleteItem) {
        confirmDeleteItem.addEventListener('click', function() {
            document.getElementById('deleteItemForm').submit();
        });
    }
});
</script>
