<?php
require_once '../../includes/header.php';
require_once '../../../includes/config.php';

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $access_level = (int)($_POST['access_level'] ?? 9999);
    $class_name = trim($_POST['class_name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Command name is required.";
    }
    
    if (empty($class_name)) {
        $errors[] = "Class name is required.";
    }
    
    if ($access_level < 0 || $access_level > 9999) {
        $errors[] = "Access level must be between 0 and 9999.";
    }

    if (empty($errors)) {
        try {
            // Check if command already exists
            $check_sql = "SELECT COUNT(*) FROM commands WHERE name = :name";
            $check_stmt = $pdo->prepare($check_sql);
            $check_stmt->execute(['name' => $name]);
            
            if ($check_stmt->fetchColumn() > 0) {
                $error_message = "A command with this name already exists.";
            } else {
                // Insert new command
                $insert_sql = "INSERT INTO commands (name, access_level, class_name, description) VALUES (:name, :access_level, :class_name, :description)";
                $insert_stmt = $pdo->prepare($insert_sql);
                $insert_stmt->execute([
                    'name' => $name,
                    'access_level' => $access_level,
                    'class_name' => $class_name,
                    'description' => $description
                ]);
                
                $success_message = "Command '{$name}' has been added successfully!";
                
                // Clear form data
                $name = $access_level = $class_name = $description = '';
            }
        } catch(PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
}
?>

<!-- Admin Breadcrumb -->
<div class="admin-breadcrumb">
    <ol class="breadcrumb-list">
        <li class="breadcrumb-item"><a href="/l1jdb_database/admin/index.php">Dashboard</a></li>
        <li class="breadcrumb-separator">></li>
        <li class="breadcrumb-item"><a href="/l1jdb_database/admin/pages/commands/commands_list_view.php">Commands</a></li>
        <li class="breadcrumb-separator">></li>
        <li class="breadcrumb-item">Add New Command</li>
    </ol>
</div>

<!-- Page Header -->
<div class="admin-header">
    <h1>➕ Add New Command</h1>
    <div class="admin-header-actions">
        <a href="/l1jdb_database/admin/pages/commands/commands_list_view.php" class="admin-btn admin-btn-secondary">
            <span>⬅️</span> Back to Commands
        </a>
    </div>
</div>

<?php if (!empty($success_message)): ?>
    <div class="admin-message admin-message-success">
        <?php echo $success_message; ?>
        <div style="margin-top: 1rem;">
            <a href="/l1jdb_database/admin/pages/commands/commands_list_view.php" class="admin-btn admin-btn-secondary admin-btn-small">
                View All Commands
            </a>
            <a href="/l1jdb_database/admin/pages/commands/command_add.php" class="admin-btn admin-btn-primary admin-btn-small">
                Add Another Command
            </a>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($error_message)): ?>
    <div class="admin-message admin-message-error">
        <?php echo $error_message; ?>
    </div>
<?php endif; ?>

<!-- Command Add Form -->
<div class="admin-form">
    <form method="POST" id="commandForm">
        <div class="form-tabs">
            <button type="button" class="form-tab active" data-tab="basic">Basic Information</button>
            <button type="button" class="form-tab" data-tab="advanced">Advanced Settings</button>
        </div>
        
        <!-- Basic Information Tab -->
        <div class="form-tab-content active" id="basic">
            <div class="field-group">
                <h3>🔧 Basic Command Information</h3>
                
                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="name">Command Name *</label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="<?php echo htmlspecialchars($name ?? ''); ?>"
                               placeholder="e.g., teleport, 텔레포트"
                               required>
                        <small style="color: var(--text); opacity: 0.7; margin-top: 0.5rem; display: block;">
                            Enter the command name as it will be used in game (without the dot prefix)
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label for="access_level">Access Level *</label>
                        <select id="access_level" name="access_level" required>
                            <option value="9999" <?php echo ($access_level ?? 9999) == 9999 ? 'selected' : ''; ?>>9999 - Super Admin</option>
                            <option value="500" <?php echo ($access_level ?? '') == 500 ? 'selected' : ''; ?>>500 - Senior Admin</option>
                            <option value="200" <?php echo ($access_level ?? '') == 200 ? 'selected' : ''; ?>>200 - Admin</option>
                            <option value="100" <?php echo ($access_level ?? '') == 100 ? 'selected' : ''; ?>>100 - Moderator</option>
                            <option value="50" <?php echo ($access_level ?? '') == 50 ? 'selected' : ''; ?>>50 - Junior Moderator</option>
                            <option value="1" <?php echo ($access_level ?? '') == 1 ? 'selected' : ''; ?>>1 - User</option>
                        </select>
                        <small style="color: var(--text); opacity: 0.7; margin-top: 0.5rem; display: block;">
                            Higher numbers = more restricted access
                        </small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="class_name">Java Class Name *</label>
                    <input type="text" 
                           id="class_name" 
                           name="class_name" 
                           value="<?php echo htmlspecialchars($class_name ?? ''); ?>"
                           placeholder="e.g., L1Teleport, L1CreateItem"
                           required>
                    <small style="color: var(--text); opacity: 0.7; margin-top: 0.5rem; display: block;">
                        The Java class that implements this command
                    </small>
                </div>
            </div>
        </div>
        
        <!-- Advanced Settings Tab -->
        <div class="form-tab-content" id="advanced">
            <div class="field-group">
                <h3>📝 Command Documentation</h3>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" 
                              name="description" 
                              rows="6"
                              placeholder="Describe what this command does and how to use it..."><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                    <small style="color: var(--text); opacity: 0.7; margin-top: 0.5rem; display: block;">
                        Include parameters in brackets, e.g., "Parameters: [x] [y] [z] - Teleport to coordinates"
                    </small>
                </div>
                
                <div class="field-group stat-group-special">
                    <h3>💡 Usage Examples</h3>
                    <div style="background: var(--secondary); padding: 1rem; border-radius: 8px; font-family: 'Courier New', monospace;">
                        <div style="color: var(--accent); margin-bottom: 0.5rem;">Chat Usage:</div>
                        <div style="background: var(--primary); padding: 0.5rem; border-radius: 4px; margin-bottom: 1rem;">
                            <span style="color: #2ecc71;">.</span><span id="preview-name" style="color: var(--text);">commandname</span>
                        </div>
                        
                        <div style="color: var(--accent); margin-bottom: 0.5rem;">Class Implementation:</div>
                        <div style="background: var(--primary); padding: 0.5rem; border-radius: 4px;">
                            <span style="color: #9b59b6;">new</span> <span id="preview-class" style="color: var(--text);">ClassName</span><span style="color: #2ecc71;">()</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Form Actions -->
        <div style="background: var(--secondary); padding: 1.5rem; margin-top: 2rem; border-radius: 0 0 12px 12px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <button type="submit" class="admin-btn admin-btn-primary">
                    <span>💾</span> Create Command
                </button>
                <button type="reset" class="admin-btn admin-btn-secondary" onclick="resetForm()">
                    <span>🔄</span> Reset Form
                </button>
            </div>
            
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <a href="/l1jdb_database/admin/pages/commands/commands_list_view.php" class="admin-btn admin-btn-secondary">
                    <span>📋</span> View All Commands
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Quick Templates -->
<div class="field-group" style="margin-top: 2rem;">
    <h3>🚀 Quick Templates</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
        <div class="template-card" onclick="applyTemplate('teleport')" style="background: var(--secondary); padding: 1rem; border-radius: 8px; cursor: pointer; border: 2px solid var(--primary); transition: all 0.3s ease;">
            <h4 style="color: var(--accent); margin-bottom: 0.5rem;">🌀 Teleport Command</h4>
            <p style="font-size: 0.9rem; opacity: 0.8;">Movement and teleportation</p>
        </div>
        
        <div class="template-card" onclick="applyTemplate('item')" style="background: var(--secondary); padding: 1rem; border-radius: 8px; cursor: pointer; border: 2px solid var(--primary); transition: all 0.3s ease;">
            <h4 style="color: var(--accent); margin-bottom: 0.5rem;">📦 Item Command</h4>
            <p style="font-size: 0.9rem; opacity: 0.8;">Item creation and management</p>
        </div>
        
        <div class="template-card" onclick="applyTemplate('buff')" style="background: var(--secondary); padding: 1rem; border-radius: 8px; cursor: pointer; border: 2px solid var(--primary); transition: all 0.3s ease;">
            <h4 style="color: var(--accent); margin-bottom: 0.5rem;">✨ Buff Command</h4>
            <p style="font-size: 0.9rem; opacity: 0.8;">Character enhancements</p>
        </div>
        
        <div class="template-card" onclick="applyTemplate('admin')" style="background: var(--secondary); padding: 1rem; border-radius: 8px; cursor: pointer; border: 2px solid var(--primary); transition: all 0.3s ease;">
            <h4 style="color: var(--accent); margin-bottom: 0.5rem;">🔧 Admin Command</h4>
            <p style="font-size: 0.9rem; opacity: 0.8;">Administrative tools</p>
        </div>
    </div>
</div>

<script>
// Form tab functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.form-tab');
    const tabContents = document.querySelectorAll('.form-tab-content');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Remove active class from all tabs and contents
            tabs.forEach(t => t.classList.remove('active'));
            tabContents.forEach(tc => tc.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding content
            this.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });
    
    // Real-time preview updates
    const nameInput = document.getElementById('name');
    const classInput = document.getElementById('class_name');
    const previewName = document.getElementById('preview-name');
    const previewClass = document.getElementById('preview-class');
    
    function updatePreview() {
        previewName.textContent = nameInput.value || 'commandname';
        previewClass.textContent = classInput.value || 'ClassName';
    }
    
    nameInput.addEventListener('input', updatePreview);
    classInput.addEventListener('input', updatePreview);
    
    // Template card hover effects
    const templateCards = document.querySelectorAll('.template-card');
    templateCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.borderColor = 'var(--accent)';
            this.style.transform = 'translateY(-2px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.borderColor = 'var(--primary)';
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Form validation
    const form = document.getElementById('commandForm');
    form.addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const className = document.getElementById('class_name').value.trim();
        
        if (!name || !className) {
            e.preventDefault();
            alert('Please fill in all required fields.');
            return;
        }
        
        // Check for valid command name (no spaces, special chars except underscore)
        if (!/^[a-zA-Z0-9_가-힣]+$/.test(name)) {
            e.preventDefault();
            alert('Command name can only contain letters, numbers, underscores, and Korean characters.');
            return;
        }
    });
});

// Template application functions
function applyTemplate(type) {
    const templates = {
        teleport: {
            name: 'teleport',
            class_name: 'L1Teleport',
            description: 'Parameters: [x] [y] [mapId] - Teleport to specified coordinates on the given map'
        },
        item: {
            name: 'createitem',
            class_name: 'L1CreateItem',
            description: 'Parameters: [itemId] [count] [enchant] - Create the specified item with given quantity and enchantment level'
        },
        buff: {
            name: 'buff',
            class_name: 'L1Buff',
            description: 'Parameters: [skillId] [duration] - Apply the specified buff for the given duration in seconds'
        },
        admin: {
            name: 'admin',
            class_name: 'L1Admin',
            description: 'Administrative command for server management and control'
        }
    };
    
    const template = templates[type];
    if (template) {
        document.getElementById('name').value = template.name;
        document.getElementById('class_name').value = template.class_name;
        document.getElementById('description').value = template.description;
        
        // Update preview
        document.getElementById('preview-name').textContent = template.name;
        document.getElementById('preview-class').textContent = template.class_name;
        
        // Show success feedback
        const card = event.currentTarget;
        const originalBg = card.style.backgroundColor;
        card.style.backgroundColor = 'rgba(46, 204, 113, 0.2)';
        card.style.borderColor = '#2ecc71';
        
        setTimeout(() => {
            card.style.backgroundColor = originalBg;
            card.style.borderColor = 'var(--primary)';
        }, 1000);
    }
}

function resetForm() {
    document.getElementById('commandForm').reset();
    document.getElementById('preview-name').textContent = 'commandname';
    document.getElementById('preview-class').textContent = 'ClassName';
}
</script>

<?php require_once '../../includes/footer.php'; ?>
