<?php
require_once '../../includes/header.php';
require_once '../../../includes/config.php';

// Get command name from URL
$command_name = isset($_GET['name']) ? trim($_GET['name']) : '';

if (empty($command_name)) {
    header('Location: /l1jdb_database/admin/pages/commands/commands_list_view.php');
    exit;
}

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
            // Update command
            $update_sql = "UPDATE commands SET name = :name, access_level = :access_level, class_name = :class_name, description = :description WHERE name = :original_name";
            $update_stmt = $pdo->prepare($update_sql);
            $update_stmt->execute([
                'name' => $name,
                'access_level' => $access_level,
                'class_name' => $class_name,
                'description' => $description,
                'original_name' => $command_name
            ]);
            
            $success_message = "Command has been updated successfully!";
            
            // If name changed, redirect to new URL
            if ($name !== $command_name) {
                header("Location: /l1jdb_database/admin/pages/commands/command_detail.php?name=" . urlencode($name));
                exit;
            }
            
        } catch(PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
}

try {
    // Get command details
    $sql = "SELECT * FROM commands WHERE name = :name LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['name' => $command_name]);
    $command = $stmt->fetch();

    if (!$command) {
        header('Location: /l1jdb_database/admin/pages/commands/commands_list_view.php');
        exit;
    }

} catch(PDOException $e) {
    $error_message = "Database error: " . $e->getMessage();
    $command = null;
}

// Helper function to detect language
function detectLanguage($text) {
    return preg_match('/[가-힣]/', $text) ? 'Korean' : 'English';
}
?>

<!-- Admin Breadcrumb -->
<div class="admin-breadcrumb">
    <ol class="breadcrumb-list">
        <li class="breadcrumb-item"><a href="/l1jdb_database/admin/index.php">Dashboard</a></li>
        <li class="breadcrumb-separator">></li>
        <li class="breadcrumb-item"><a href="/l1jdb_database/admin/pages/commands/commands_list_view.php">Commands</a></li>
        <li class="breadcrumb-separator">></li>
        <li class="breadcrumb-item"><a href="/l1jdb_database/admin/pages/commands/command_detail.php?name=<?php echo urlencode($command_name); ?>"><?php echo htmlspecialchars($command_name); ?></a></li>
        <li class="breadcrumb-separator">></li>
        <li class="breadcrumb-item">Edit</li>
    </ol>
</div>

<!-- Page Header -->
<div class="admin-header">
    <h1>
        ✏️ Edit Command: 
        <?php 
        if ($command) {
            $lang_flag = detectLanguage($command['name']) === 'Korean' ? '🇰🇷' : '🇺🇸';
            echo $lang_flag . ' ' . htmlspecialchars($command['name']); 
        }
        ?>
    </h1>
    <div class="admin-header-actions">
        <a href="/l1jdb_database/admin/pages/commands/command_detail.php?name=<?php echo urlencode($command_name); ?>" class="admin-btn admin-btn-secondary">
            <span>👁️</span> View Details
        </a>
        <a href="/l1jdb_database/admin/pages/commands/commands_list_view.php" class="admin-btn admin-btn-secondary">
            <span>⬅️</span> Back to List
        </a>
    </div>
</div>

<?php if (!empty($success_message)): ?>
    <div class="admin-message admin-message-success">
        <?php echo $success_message; ?>
        <div style="margin-top: 1rem;">
            <a href="/l1jdb_database/admin/pages/commands/command_detail.php?name=<?php echo urlencode($command['name']); ?>" class="admin-btn admin-btn-secondary admin-btn-small">
                View Updated Command
            </a>
            <a href="/l1jdb_database/admin/pages/commands/commands_list_view.php" class="admin-btn admin-btn-primary admin-btn-small">
                Back to Commands List
            </a>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($error_message)): ?>
    <div class="admin-message admin-message-error">
        <?php echo $error_message; ?>
    </div>
<?php endif; ?>

<?php if ($command): ?>
<!-- Command Edit Form -->
<div class="admin-form">
    <form method="POST" id="commandEditForm">
        <div class="form-tabs">
            <button type="button" class="form-tab active" data-tab="basic">Basic Information</button>
            <button type="button" class="form-tab" data-tab="advanced">Advanced Settings</button>
            <button type="button" class="form-tab" data-tab="danger">Danger Zone</button>
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
                               value="<?php echo htmlspecialchars($command['name']); ?>"
                               placeholder="e.g., teleport, 텔레포트"
                               required>
                        <small style="color: var(--text); opacity: 0.7; margin-top: 0.5rem; display: block;">
                            Enter the command name as it will be used in game (without the dot prefix)
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label for="access_level">Access Level *</label>
                        <select id="access_level" name="access_level" required>
                            <option value="9999" <?php echo $command['access_level'] == 9999 ? 'selected' : ''; ?>>9999 - Super Admin</option>
                            <option value="500" <?php echo $command['access_level'] == 500 ? 'selected' : ''; ?>>500 - Senior Admin</option>
                            <option value="200" <?php echo $command['access_level'] == 200 ? 'selected' : ''; ?>>200 - Admin</option>
                            <option value="100" <?php echo $command['access_level'] == 100 ? 'selected' : ''; ?>>100 - Moderator</option>
                            <option value="50" <?php echo $command['access_level'] == 50 ? 'selected' : ''; ?>>50 - Junior Moderator</option>
                            <option value="1" <?php echo $command['access_level'] == 1 ? 'selected' : ''; ?>>1 - User</option>
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
                           value="<?php echo htmlspecialchars($command['class_name']); ?>"
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
                              placeholder="Describe what this command does and how to use it..."><?php echo htmlspecialchars($command['description']); ?></textarea>
                    <small style="color: var(--text); opacity: 0.7; margin-top: 0.5rem; display: block;">
                        Include parameters in brackets, e.g., "Parameters: [x] [y] [z] - Teleport to coordinates"
                    </small>
                </div>
                
                <div class="field-group stat-group-special">
                    <h3>💡 Usage Examples</h3>
                    <div style="background: var(--secondary); padding: 1rem; border-radius: 8px; font-family: 'Courier New', monospace;">
                        <div style="color: var(--accent); margin-bottom: 0.5rem;">Chat Usage:</div>
                        <div style="background: var(--primary); padding: 0.5rem; border-radius: 4px; margin-bottom: 1rem;">
                            <span style="color: #2ecc71;">.</span><span id="preview-name" style="color: var(--text);"><?php echo htmlspecialchars($command['name']); ?></span>
                        </div>
                        
                        <div style="color: var(--accent); margin-bottom: 0.5rem;">Class Implementation:</div>
                        <div style="background: var(--primary); padding: 0.5rem; border-radius: 4px;">
                            <span style="color: #9b59b6;">new</span> <span id="preview-class" style="color: var(--text);"><?php echo htmlspecialchars($command['class_name']); ?></span><span style="color: #2ecc71;">()</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Danger Zone Tab -->
        <div class="form-tab-content" id="danger">
            <div class="field-group stat-group-negative">
                <h3>⚠️ Danger Zone</h3>
                
                <div class="admin-message admin-message-warning">
                    <strong>Warning:</strong> These actions are irreversible and may affect server functionality.
                </div>
                
                <div style="background: var(--secondary); padding: 1.5rem; border-radius: 8px; border: 2px solid #e74c3c;">
                    <h4 style="color: #e74c3c; margin-bottom: 1rem;">Delete Command</h4>
                    <p style="margin-bottom: 1.5rem; opacity: 0.8;">
                        Permanently remove this command from the database. This action cannot be undone.
                    </p>
                    <button type="button" 
                            onclick="deleteCommand()" 
                            class="admin-btn admin-btn-danger">
                        <span>🗑️</span> Delete Command
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Form Actions -->
        <div style="background: var(--secondary); padding: 1.5rem; margin-top: 2rem; border-radius: 0 0 12px 12px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <button type="submit" class="admin-btn admin-btn-primary">
                    <span>💾</span> Update Command
                </button>
                <button type="reset" class="admin-btn admin-btn-secondary" onclick="resetForm()">
                    <span>🔄</span> Reset Changes
                </button>
            </div>
            
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <a href="/l1jdb_database/admin/pages/commands/command_detail.php?name=<?php echo urlencode($command['name']); ?>" class="admin-btn admin-btn-secondary">
                    <span>👁️</span> View Details
                </a>
            </div>
        </div>
    </form>
</div>

<?php else: ?>
<div class="admin-empty">
    <h3>Command Not Found</h3>
    <p>The requested command could not be found in the database.</p>
    <a href="/l1jdb_database/admin/pages/commands/commands_list_view.php" class="admin-btn admin-btn-primary">
        <span>⬅️</span> Back to Commands List
    </a>
</div>
<?php endif; ?>

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
    
    // Form validation
    const form = document.getElementById('commandEditForm');
    form.addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const className = document.getElementById('class_name').value.trim();
        
        if (!name || !className) {
            e.preventDefault();
            alert('Please fill in all required fields.');
            return;
        }
        
        // Check for valid command name
        if (!/^[a-zA-Z0-9_가-힣]+$/.test(name)) {
            e.preventDefault();
            alert('Command name can only contain letters, numbers, underscores, and Korean characters.');
            return;
        }
    });
});

// Delete command function
function deleteCommand() {
    const commandName = '<?php echo addslashes($command['name']); ?>';
    
    if (confirm(`Are you sure you want to delete the command "${commandName}"?\n\nThis action cannot be undone.`)) {
        if (confirm('This will permanently remove the command from the database. Are you absolutely sure?')) {
            // Create form to submit delete request
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/l1jdb_database/admin/pages/commands/command_delete.php';
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'command_name';
            input.value = commandName;
            
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    }
}

function resetForm() {
    if (confirm('Are you sure you want to reset all changes?')) {
        document.getElementById('commandEditForm').reset();
        updatePreview();
    }
}
</script>

<?php require_once '../../includes/footer.php'; ?>
