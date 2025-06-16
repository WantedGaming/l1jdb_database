// Admin Panel JavaScript functionality

document.addEventListener('DOMContentLoaded', function() {
    // Tooltips Initialization
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Initialize data tables
    initializeTables();
    
    // Setup form validation
    setupFormValidation();
    
    // Initialize search functionality
    setupSearch();
});

function initializeTables() {
    // Add sorting and filtering to tables
    const tables = document.querySelectorAll('.data-table');
    tables.forEach(table => {
        // Setup sorting
        const headers = table.querySelectorAll('th[data-sort]');
        headers.forEach(header => {
            header.addEventListener('click', function() {
                const column = this.dataset.sort;
                const direction = this.dataset.direction === 'asc' ? 'desc' : 'asc';
                
                // Reset all headers
                headers.forEach(h => {
                    h.dataset.direction = '';
                    h.querySelector('span')?.remove();
                });
                
                // Set current header
                this.dataset.direction = direction;
                
                // Add indicator
                const span = document.createElement('span');
                span.innerHTML = direction === 'asc' ? ' ▲' : ' ▼';
                this.appendChild(span);
                
                // Sort the table
                sortTable(table, column, direction);
            });
        });
    });
}

function sortTable(table, column, direction) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    // Sort the rows
    const sortedRows = rows.sort((a, b) => {
        const aValue = a.querySelector(`td[data-column="${column}"]`).textContent.trim();
        const bValue = b.querySelector(`td[data-column="${column}"]`).textContent.trim();
        
        if (!isNaN(aValue) && !isNaN(bValue)) {
            return direction === 'asc' 
                ? parseFloat(aValue) - parseFloat(bValue)
                : parseFloat(bValue) - parseFloat(aValue);
        } else {
            return direction === 'asc'
                ? aValue.localeCompare(bValue)
                : bValue.localeCompare(aValue);
        }
    });
    
    // Remove existing rows
    rows.forEach(row => tbody.removeChild(row));
    
    // Add sorted rows
    sortedRows.forEach(row => tbody.appendChild(row));
}

function setupFormValidation() {
    // Form validation for spawn form
    const spawnForm = document.getElementById('spawnForm');
    if (spawnForm) {
        spawnForm.addEventListener('submit', function(event) {
            if (!validateSpawnForm()) {
                event.preventDefault();
            }
        });
    }
    
    // Form validation for drops form
    const dropsForm = document.getElementById('dropsForm');
    if (dropsForm) {
        dropsForm.addEventListener('submit', function(event) {
            if (!validateDropsForm()) {
                event.preventDefault();
            }
        });
    }
    
    // Form validation for monsters form
    const monstersForm = document.getElementById('monstersForm');
    if (monstersForm) {
        monstersForm.addEventListener('submit', function(event) {
            if (!validateMonstersForm()) {
                event.preventDefault();
            }
        });
    }
}

function validateSpawnForm() {
    // Get form fields
    const npcId = document.getElementById('npc_templateid');
    const count = document.getElementById('count');
    const locx = document.getElementById('locx');
    const locy = document.getElementById('locy');
    const mapid = document.getElementById('mapid');
    
    // Check required fields
    if (!npcId.value || !count.value || !locx.value || !locy.value || !mapid.value) {
        showAlert('Please fill out all required fields', 'danger');
        return false;
    }
    
    // Validate numeric values
    if (isNaN(npcId.value) || isNaN(count.value) || isNaN(locx.value) || isNaN(locy.value) || isNaN(mapid.value)) {
        showAlert('Please enter valid numeric values', 'danger');
        return false;
    }
    
    return true;
}

function validateDropsForm() {
    // Get form fields
    const mobId = document.getElementById('mobId');
    const itemId = document.getElementById('itemId');
    const min = document.getElementById('min');
    const max = document.getElementById('max');
    const chance = document.getElementById('chance');
    
    // Check required fields
    if (!mobId.value || !itemId.value || !min.value || !max.value || !chance.value) {
        showAlert('Please fill out all required fields', 'danger');
        return false;
    }
    
    // Validate numeric values
    if (isNaN(mobId.value) || isNaN(itemId.value) || isNaN(min.value) || isNaN(max.value) || isNaN(chance.value)) {
        showAlert('Please enter valid numeric values', 'danger');
        return false;
    }
    
    // Check min <= max
    if (parseInt(min.value) > parseInt(max.value)) {
        showAlert('Minimum value cannot be greater than maximum value', 'danger');
        return false;
    }
    
    return true;
}

function validateMonstersForm() {
    // Get form fields
    const name = document.getElementById('desc_en');
    const level = document.getElementById('lvl');
    const hp = document.getElementById('hp');
    
    // Check required fields
    if (!name.value || !level.value || !hp.value) {
        showAlert('Please fill out all required fields', 'danger');
        return false;
    }
    
    // Validate numeric values for level and hp
    if (isNaN(level.value) || isNaN(hp.value)) {
        showAlert('Please enter valid numeric values for level and HP', 'danger');
        return false;
    }
    
    return true;
}

function showAlert(message, type) {
    const alertPlaceholder = document.getElementById('alertPlaceholder');
    const wrapper = document.createElement('div');
    wrapper.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    alertPlaceholder.appendChild(wrapper);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alert = bootstrap.Alert.getOrCreateInstance(wrapper.querySelector('.alert'));
        alert.close();
    }, 5000);
}

function setupSearch() {
    // Setup search functionality for tables
    const searchInputs = document.querySelectorAll('.search-input');
    searchInputs.forEach(input => {
        input.addEventListener('keyup', function() {
            const tableId = this.dataset.table;
            const table = document.getElementById(tableId);
            const searchText = this.value.toLowerCase();
            
            if (table) {
                const rows = table.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchText) ? '' : 'none';
                });
            }
        });
    });
    
    // Item selector in drops form
    const itemSelector = document.getElementById('itemType');
    if (itemSelector) {
        itemSelector.addEventListener('change', function() {
            const type = this.value;
            // Update the item selector based on the selected type
            fetchItems(type);
        });
    }
    
    // NPC selector in spawn form
    const npcSearch = document.getElementById('npcSearch');
    if (npcSearch) {
        npcSearch.addEventListener('keyup', function() {
            if (this.value.length >= 2) {
                fetchNpcSuggestions(this.value);
            } else {
                document.getElementById('npcSuggestions').innerHTML = '';
            }
        });
    }
}

function fetchItems(type) {
    // Fetch items based on the selected type
    fetch(`api/get_items.php?type=${type}`)
        .then(response => response.json())
        .then(data => {
            const itemSelect = document.getElementById('itemId');
            itemSelect.innerHTML = '';
            
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = `${item.id} - ${item.name}`;
                itemSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error fetching items:', error);
        });
}

function fetchNpcSuggestions(query) {
    // Fetch NPC suggestions based on the search query
    fetch(`api/search_npc.php?q=${query}`)
        .then(response => response.json())
        .then(data => {
            const suggestions = document.getElementById('npcSuggestions');
            suggestions.innerHTML = '';
            
            data.forEach(npc => {
                const div = document.createElement('div');
                div.className = 'suggestion-item';
                div.textContent = `${npc.id} - ${npc.name}`;
                div.addEventListener('click', function() {
                    document.getElementById('npc_templateid').value = npc.id;
                    document.getElementById('npcSearch').value = npc.name;
                    suggestions.innerHTML = '';
                });
                suggestions.appendChild(div);
            });
        })
        .catch(error => {
            console.error('Error fetching NPC suggestions:', error);
        });
}
