<?php
require_once '../../includes/header.php';
?>

<div class="admin-content-wrapper">
    <div class="page-header">
        <div class="breadcrumb">
            <a href="/l1jdb_database/admin/">Dashboard</a> &raquo; 
            <a href="/l1jdb_database/admin/pages/spawn/">Spawns</a> &raquo; 
            <span>Spawn Management</span>
        </div>
        <h1>Spawn Management</h1>
    </div>
    
    <div class="admin-header-actions">
        <button id="createSpawnBtn" class="admin-btn admin-btn-primary">
            <i class="fa fa-plus"></i> Create New Spawn
        </button>
    </div>
    
    <!-- Results Section -->
    <div id="resultsInfo" class="results-info">
        Loading spawns...
    </div>
    
    <div class="table-responsive">
        <table class="admin-table" id="spawnsTable">
            <thead>
                <tr>
                    <th class="table-cell-id">ID</th>
                    <th class="table-cell-id">NPC ID</th>
                    <th class="table-cell-name">Name</th>
                    <th>Location</th>
                    <th>Count</th>
                    <th>Respawn</th>
                    <th class="table-cell-actions">Actions</th>
                </tr>
            </thead>
            <tbody id="spawnsTableBody">
                <!-- Table data will be populated by JavaScript -->
                <tr>
                    <td colspan="7" class="admin-loading">
                        <div class="admin-spinner"></div> Loading spawns...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="admin-pagination" id="paginationContainer">
        <!-- Pagination will be populated by JavaScript -->
    </div>
</div>

<!-- Spawn Form Modal -->
<div id="spawnModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Create New Spawn</h2>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body">
            <form id="spawnForm">
                <input type="hidden" id="formAction" value="create">
                <input type="hidden" id="formTable" name="table" value="">
                
                <div class="form-tabs">
                    <button type="button" class="form-tab active" data-tab="basic-info">Basic Info</button>
                    <button type="button" class="form-tab" data-tab="location-info">Location</button>
                    <button type="button" class="form-tab" data-tab="respawn-info">Respawn</button>
                </div>
                
                <!-- Basic Info Tab -->
                <div class="form-tab-content active" id="basic-info">
                    <div class="field-group">
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label for="spawnId">Spawn ID*:</label>
                                <div class="id-input-group">
                                    <input type="number" id="spawnId" name="id" class="form-control" required min="1">
                                    <span id="idAvailability" class="id-check"></span>
                                </div>
                                <small class="form-text">Must be a unique ID</small>
                            </div>
                            <div class="form-group">
                                <label for="spawnName">Name*:</label>
                                <input type="text" id="spawnName" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="npcTemplateId">NPC Template ID*:</label>
                                <input type="number" id="npcTemplateId" name="npc_templateid" class="form-control" required min="1">
                                <small class="form-text">ID of the NPC from the NPC table</small>
                            </div>
                            <div class="form-group">
                                <label for="spawnCount">Count*:</label>
                                <input type="number" id="spawnCount" name="count" class="form-control" required min="1" value="1">
                                <small class="form-text">Number of NPCs to spawn</small>
                            </div>
                            <div class="form-group">
                                <label for="groupId">Group ID:</label>
                                <input type="number" id="groupId" name="group_id" class="form-control" min="0" value="0">
                                <small class="form-text">Optional group identifier</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Location Tab -->
                <div class="form-tab-content" id="location-info">
                    <div class="field-group">
                        <h3>Map & Coordinates</h3>
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label for="mapId">Map ID*:</label>
                                <input type="number" id="mapId" name="mapid" class="form-control" required min="0" value="0">
                            </div>
                            <div class="form-group">
                                <label for="movementDistance">Movement Distance:</label>
                                <input type="number" id="movementDistance" name="movement_distance" class="form-control" min="0" value="0">
                            </div>
                            <div class="form-group">
                                <label for="locX">X Position*:</label>
                                <input type="number" id="locX" name="locx" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="locY">Y Position*:</label>
                                <input type="number" id="locY" name="locy" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="heading">Heading:</label>
                                <input type="number" id="heading" name="heading" class="form-control" min="0" max="7" value="0">
                                <small class="form-text">Direction (0-7)</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="field-group">
                        <h3>Random Spawn Area</h3>
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label for="randomX">Random X Range:</label>
                                <input type="number" id="randomX" name="randomx" class="form-control" min="0" value="0">
                            </div>
                            <div class="form-group">
                                <label for="randomY">Random Y Range:</label>
                                <input type="number" id="randomY" name="randomy" class="form-control" min="0" value="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="field-group">
                        <h3>Area Spawning (Min/Max Coordinates)</h3>
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label for="locX1">Min X (locx1):</label>
                                <input type="number" id="locX1" name="locx1" class="form-control" value="0">
                            </div>
                            <div class="form-group">
                                <label for="locY1">Min Y (locy1):</label>
                                <input type="number" id="locY1" name="locy1" class="form-control" value="0">
                            </div>
                            <div class="form-group">
                                <label for="locX2">Max X (locx2):</label>
                                <input type="number" id="locX2" name="locx2" class="form-control" value="0">
                            </div>
                            <div class="form-group">
                                <label for="locY2">Max Y (locy2):</label>
                                <input type="number" id="locY2" name="locy2" class="form-control" value="0">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Respawn Tab -->
                <div class="form-tab-content" id="respawn-info">
                    <div class="field-group">
                        <h3>Respawn Settings</h3>
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label for="minRespawnDelay">Min Respawn Delay (seconds):</label>
                                <input type="number" id="minRespawnDelay" name="min_respawn_delay" class="form-control" min="0" value="60">
                            </div>
                            <div class="form-group">
                                <label for="maxRespawnDelay">Max Respawn Delay (seconds):</label>
                                <input type="number" id="maxRespawnDelay" name="max_respawn_delay" class="form-control" min="0" value="120">
                            </div>
                        </div>
                    </div>
                    
                    <div class="field-group">
                        <h3>Special Respawn Flags</h3>
                        <div class="form-grid-3">
                            <div class="form-group">
                                <label for="respawnScreen">Respawn Screen:</label>
                                <select id="respawnScreen" name="respawn_screen" class="form-control">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="rest">Rest:</label>
                                <select id="rest" name="rest" class="form-control">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="nearSpawn">Near Spawn:</label>
                                <select id="nearSpawn" name="near_spawn" class="form-control">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <div id="formError" class="form-error"></div>
                    <button type="button" class="admin-btn admin-btn-secondary" id="cancelBtn">Cancel</button>
                    <button type="submit" class="admin-btn admin-btn-primary" id="saveBtn">Save Spawn</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Confirm Delete</h2>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this spawn? This action cannot be undone.</p>
            <input type="hidden" id="deleteId">
            <input type="hidden" id="deleteTable">
            <div class="modal-footer">
                <button type="button" class="admin-btn admin-btn-secondary" id="cancelDeleteBtn">Cancel</button>
                <button type="button" class="admin-btn admin-btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    z-index: 1000;
    overflow-y: auto;
}

.modal-content {
    background: var(--primary);
    margin: 5vh auto;
    border-radius: 12px;
    max-width: 800px;
    width: 90%;
    box-shadow: 0 5px 30px rgba(0, 0, 0, 0.5);
    animation: modalFadeIn 0.3s ease-out;
    overflow: hidden;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
}

@keyframes modalFadeIn {
    from { opacity: 0; transform: translateY(-30px); }
    to { opacity: 1; transform: translateY(0); }
}

.modal-header {
    background: var(--secondary);
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid var(--accent);
}

.modal-header h2 {
    color: var(--accent);
    margin: 0;
    font-size: 1.5rem;
}

.modal-body {
    padding: 0;
    overflow-y: auto;
    max-height: calc(90vh - 130px);
}

.modal-footer {
    padding: 1.5rem;
    background: var(--secondary);
    border-top: 1px solid var(--primary);
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    align-items: center;
}

.close {
    color: var(--text);
    font-size: 1.8rem;
    font-weight: bold;
    cursor: pointer;
    transition: color 0.3s ease;
}

.close:hover {
    color: var(--accent);
}

/* Form Error */
.form-error {
    color: #e74c3c;
    margin-right: auto;
    font-size: 0.9rem;
}

/* ID Check Styles */
.id-input-group {
    display: flex;
    align-items: center;
}

.id-check {
    margin-left: 10px;
    font-size: 0.9rem;
}

.id-available {
    color: #2ecc71;
}

.id-taken {
    color: #e74c3c;
}

.form-text {
    font-size: 0.8rem;
    color: var(--text);
    opacity: 0.7;
    margin-top: 0.25rem;
    display: block;
}
</style>

<script>
// Global state
let currentPage = 1;
let totalPages = 1;
let currentTable = 'spawnlist';
let currentSearch = '';

// Function to load spawns
function loadSpawns(page = 1, table = 'spawnlist', search = '') {
    currentPage = page;
    currentTable = table;
    currentSearch = search;
    
    // Show loading state
    document.getElementById('spawnsTableBody').innerHTML = `
        <tr>
            <td colspan="7" class="admin-loading">
                <div class="admin-spinner"></div> Loading spawns...
            </td>
        </tr>
    `;
    
    // Build API URL
    const apiUrl = `/l1jdb_database/admin/api/get_spawn_list.php?page=${page}&table=${table}&limit=20${search ? '&search=' + encodeURIComponent(search) : ''}`;
    
    // Fetch data
    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderSpawns(data.data);
                renderPagination(data.pagination);
                updateResultsInfo(data.pagination);
            } else {
                document.getElementById('spawnsTableBody').innerHTML = `
                    <tr>
                        <td colspan="7">Error loading data: ${data.message || 'Unknown error'}</td>
                    </tr>
                `;
            }
        })
        .catch(error => {
            document.getElementById('spawnsTableBody').innerHTML = `
                <tr>
                    <td colspan="7">Error loading data: ${error.message}</td>
                </tr>
            `;
        });
}

// Function to render spawns in the table
function renderSpawns(spawns) {
    const tableBody = document.getElementById('spawnsTableBody');
    
    if (spawns.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="7" class="admin-empty">
                    <h3>No Spawns Found</h3>
                    <p>Try adjusting your search criteria</p>
                </td>
            </tr>
        `;
        return;
    }
    
    let html = '';
    
    spawns.forEach(spawn => {
        const locationText = `Map: ${spawn.mapid}, X: ${spawn.locx}, Y: ${spawn.locy}`;
        const respawnText = `${spawn.min_respawn_delay} - ${spawn.max_respawn_delay} sec`;
        const npcName = spawn.npc_name || 'Unknown NPC';
        
        html += `
            <tr class="clickable-row" data-id="${spawn.id}" data-table="${currentTable}">
                <td class="table-cell-id">${spawn.id}</td>
                <td class="table-cell-id">${spawn.npc_templateid}</td>
                <td class="table-cell-name">${npcName} (${spawn.name})</td>
                <td>${locationText}</td>
                <td class="table-cell-number">${spawn.count}</td>
                <td>${respawnText}</td>
                <td class="table-cell-actions">
                    <button class="admin-btn admin-btn-secondary admin-btn-small view-spawn" data-id="${spawn.id}" data-table="${currentTable}">
                        View
                    </button>
                    <button class="admin-btn admin-btn-primary admin-btn-small edit-spawn" data-id="${spawn.id}" data-table="${currentTable}">
                        Edit
                    </button>
                    <button class="admin-btn admin-btn-danger admin-btn-small delete-spawn" data-id="${spawn.id}" data-table="${currentTable}">
                        Delete
                    </button>
                </td>
            </tr>
        `;
    });
    
    tableBody.innerHTML = html;
    
    // Add event listeners to rows and buttons
    document.querySelectorAll('.view-spawn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const id = this.getAttribute('data-id');
            const table = this.getAttribute('data-table');
            viewSpawnDetails(id, table);
        });
    });
    
    document.querySelectorAll('.edit-spawn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const id = this.getAttribute('data-id');
            const table = this.getAttribute('data-table');
            editSpawn(id, table);
        });
    });
    
    document.querySelectorAll('.delete-spawn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const id = this.getAttribute('data-id');
            const table = this.getAttribute('data-table');
            confirmDelete(id, table);
        });
    });
    
    document.querySelectorAll('.clickable-row').forEach(row => {
        row.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const table = this.getAttribute('data-table');
            viewSpawnDetails(id, table);
        });
    });
}

// Function to view spawn details
function viewSpawnDetails(id, table) {
    // Redirect to detail view
    window.location.href = `/l1jdb_database/admin/pages/spawn/spawn_detail_view.php?id=${id}&table=${table}`;
}

// Function to render pagination
function renderPagination(pagination) {
    const container = document.getElementById('paginationContainer');
    const { page, total_pages } = pagination;
    totalPages = total_pages;
    
    if (total_pages <= 1) {
        container.innerHTML = '';
        return;
    }
    
    let html = `
        <a href="#" class="admin-btn-page ${page === 1 ? 'disabled' : ''}" data-page="1">First</a>
        <a href="#" class="admin-btn-page ${page === 1 ? 'disabled' : ''}" data-page="${Math.max(1, page - 1)}">Prev</a>
        <div class="pagination-pages">
    `;
    
    const startPage = Math.max(1, page - 2);
    const endPage = Math.min(total_pages, page + 2);
    
    if (startPage > 1) {
        html += '<span class="page-dots">...</span>';
    }
    
    for (let i = startPage; i <= endPage; i++) {
        html += `<a href="#" class="admin-btn-page ${i === page ? 'active' : ''}" data-page="${i}">${i}</a>`;
    }
    
    if (endPage < total_pages) {
        html += '<span class="page-dots">...</span>';
    }
    
    html += `
        </div>
        <a href="#" class="admin-btn-page ${page === total_pages ? 'disabled' : ''}" data-page="${Math.min(total_pages, page + 1)}">Next</a>
        <a href="#" class="admin-btn-page ${page === total_pages ? 'disabled' : ''}" data-page="${total_pages}">Last</a>
    `;
    
    container.innerHTML = html;
    
    // Add event listeners
    document.querySelectorAll('.admin-btn-page:not(.disabled)').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const page = parseInt(this.getAttribute('data-page'));
            loadSpawns(page, currentTable, currentSearch);
            
            // Scroll to top of results
            document.querySelector('.page-header').scrollIntoView({ behavior: 'smooth' });
        });
    });
}

// Function to update results info
function updateResultsInfo(pagination) {
    const container = document.getElementById('resultsInfo');
    const { page, limit, total_records, total_pages } = pagination;
    
    const start = (page - 1) * limit + 1;
    const end = Math.min(page * limit, total_records);
    
    container.innerHTML = `Showing ${start} to ${end} of ${total_records} spawns`;
}

// Function to open modal
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden'; // Prevent scrolling
}

// Function to close modal
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.style.display = 'none';
    document.body.style.overflow = ''; // Restore scrolling
}

// Function to reset form
function resetForm() {
    document.getElementById('spawnForm').reset();
    document.getElementById('formAction').value = 'create';
    document.getElementById('modalTitle').textContent = 'Create New Spawn';
    document.getElementById('formError').textContent = '';
    document.getElementById('idAvailability').textContent = '';
    document.getElementById('idAvailability').className = 'id-check';
    document.getElementById('spawnId').disabled = false;
    
    // Reset form table to current table
    document.getElementById('formTable').value = currentTable;
    
    // Show first tab
    document.querySelectorAll('.form-tab').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.form-tab-content').forEach(content => content.classList.remove('active'));
    document.querySelector('.form-tab[data-tab="basic-info"]').classList.add('active');
    document.getElementById('basic-info').classList.add('active');
}

// Function to check if ID is available
function checkIdAvailability(id, table) {
    if (id <= 0) {
        document.getElementById('idAvailability').textContent = '';
        document.getElementById('idAvailability').className = 'id-check';
        return;
    }
    
    fetch(`/l1jdb_database/admin/api/check_spawn_id.php?id=${id}&table=${table}`)
        .then(response => response.json())
        .then(data => {
            const idCheck = document.getElementById('idAvailability');
            if (data.available) {
                idCheck.textContent = 'Available';
                idCheck.className = 'id-check id-available';
            } else {
                idCheck.textContent = 'Already taken';
                idCheck.className = 'id-check id-taken';
            }
        })
        .catch(error => {
            console.error('Error checking ID availability:', error);
        });
}

// Function to create a new spawn
function createSpawn(formData) {
    fetch('/l1jdb_database/admin/api/create_spawn.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('spawnModal');
            loadSpawns(currentPage, currentTable, currentSearch);
            showAlert('Spawn created successfully', 'success');
        } else {
            document.getElementById('formError').textContent = data.message || 'Error creating spawn';
            
            if (data.id_taken) {
                const idCheck = document.getElementById('idAvailability');
                idCheck.textContent = 'Already taken';
                idCheck.className = 'id-check id-taken';
            }
        }
    })
    .catch(error => {
        document.getElementById('formError').textContent = 'Error: ' + error.message;
    });
}

// Function to update a spawn
function updateSpawn(formData) {
    fetch('/l1jdb_database/admin/api/update_spawn.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('spawnModal');
            loadSpawns(currentPage, currentTable, currentSearch);
            showAlert('Spawn updated successfully', 'success');
        } else {
            document.getElementById('formError').textContent = data.message || 'Error updating spawn';
        }
    })
    .catch(error => {
        document.getElementById('formError').textContent = 'Error: ' + error.message;
    });
}

// Function to delete a spawn
function deleteSpawn(id, table) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('table', table);
    
    fetch('/l1jdb_database/admin/api/delete_spawn.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('deleteModal');
            loadSpawns(currentPage, currentTable, currentSearch);
            showAlert('Spawn deleted successfully', 'success');
        } else {
            showAlert(data.message || 'Error deleting spawn', 'error');
        }
    })
    .catch(error => {
        showAlert('Error: ' + error.message, 'error');
    });
}

// Function to open edit form
function editSpawn(id, table) {
    resetForm();
    document.getElementById('modalTitle').textContent = 'Edit Spawn';
    document.getElementById('formAction').value = 'update';
    document.getElementById('formTable').value = table;
    
    // Disable ID field in edit mode
    document.getElementById('spawnId').disabled = true;
    
    // Fetch spawn data
    fetch(`/l1jdb_database/admin/api/get_spawn.php?id=${id}&table=${table}`)
        .then(response => response.json())
        .then(spawn => {
            // Fill form with spawn data
            document.getElementById('spawnId').value = spawn.id;
            document.getElementById('spawnName').value = spawn.name;
            document.getElementById('npcTemplateId').value = spawn.npc_templateid;
            document.getElementById('spawnCount').value = spawn.count;
            document.getElementById('groupId').value = spawn.group_id;
            document.getElementById('mapId').value = spawn.mapid;
            document.getElementById('locX').value = spawn.locx;
            document.getElementById('locY').value = spawn.locy;
            document.getElementById('randomX').value = spawn.randomx;
            document.getElementById('randomY').value = spawn.randomy;
            document.getElementById('locX1').value = spawn.locx1;
            document.getElementById('locY1').value = spawn.locy1;
            document.getElementById('locX2').value = spawn.locx2;
            document.getElementById('locY2').value = spawn.locy2;
            document.getElementById('heading').value = spawn.heading;
            document.getElementById('minRespawnDelay').value = spawn.min_respawn_delay;
            document.getElementById('maxRespawnDelay').value = spawn.max_respawn_delay;
            document.getElementById('movementDistance').value = spawn.movement_distance;
            document.getElementById('respawnScreen').value = spawn.respawn_screen;
            document.getElementById('rest').value = spawn.rest;
            document.getElementById('nearSpawn').value = spawn.near_spawn;
            
            // Open modal
            openModal('spawnModal');
        })
        .catch(error => {
            showAlert('Error loading spawn data: ' + error.message, 'error');
        });
}

// Function to confirm deletion
function confirmDelete(id, table) {
    document.getElementById('deleteId').value = id;
    document.getElementById('deleteTable').value = table;
    openModal('deleteModal');
}

// Function to show alert
function showAlert(message, type = 'info') {
    // Create alert element
    const alert = document.createElement('div');
    alert.className = `admin-message admin-message-${type}`;
    alert.textContent = message;
    
    // Add to page
    document.querySelector('.admin-content-wrapper').prepend(alert);
    
    // Remove after 3 seconds
    setTimeout(() => {
        alert.remove();
    }, 3000);
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Get table from URL parameter if available
    const urlParams = new URLSearchParams(window.location.search);
    const tableParam = urlParams.get('table');
    const searchParam = urlParams.get('search') || '';
    
    // Use the URL parameter or default to 'spawnlist'
    const table = tableParam || 'spawnlist';
    
    // Load initial data
    loadSpawns(1, table, searchParam);
    
    // Set up modal close buttons
    document.querySelectorAll('.close, #cancelBtn').forEach(button => {
        button.addEventListener('click', function() {
            closeModal('spawnModal');
        });
    });
    
    document.querySelectorAll('.close, #cancelDeleteBtn').forEach(button => {
        button.addEventListener('click', function() {
            closeModal('deleteModal');
        });
    });
    
    // Set up form tabs
    document.querySelectorAll('.form-tab').forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            const tabId = this.getAttribute('data-tab');
            
            // Remove active class from all tabs and contents
            document.querySelectorAll('.form-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.form-tab-content').forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked tab and its content
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });
    
    // Set up create button
    document.getElementById('createSpawnBtn').addEventListener('click', function() {
        resetForm();
        openModal('spawnModal');
    });
    
    // Set up ID check
    document.getElementById('spawnId').addEventListener('input', function() {
        const id = parseInt(this.value);
        const table = document.getElementById('formTable').value;
        checkIdAvailability(id, table);
    });
    
    // Set up form submission
    document.getElementById('spawnForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const action = document.getElementById('formAction').value;
        
        if (action === 'create') {
            createSpawn(formData);
        } else {
            updateSpawn(formData);
        }
    });
    
    // Set up delete confirmation
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        const id = document.getElementById('deleteId').value;
        const table = document.getElementById('deleteTable').value;
        deleteSpawn(id, table);
    });
    
    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            closeModal(e.target.id);
        }
    });
});
</script>

<?php require_once '../../includes/footer.php'; ?>
