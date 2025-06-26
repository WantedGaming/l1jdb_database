<?php
require_once '../../includes/header.php';
require_once '../../../includes/config.php';

// Get command name from URL
$command_name = isset($_GET['name']) ? trim($_GET['name']) : '';

if (empty($command_name)) {
    header('Location: /l1jdb_database/admin/pages/commands/commands_list_view.php');
    exit;
}

try {
    // Get command details
    $sql = "SELECT * FROM commands WHERE name = :name LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['name' => $command_name]);
    $command = $stmt->fetch();

    if (!$command) {
        $error_message = "Command not found.";
    }

    // Get related commands (same class or similar name)
    $related_commands = [];
    if ($command) {
        $related_sql = "SELECT * FROM commands 
                       WHERE (class_name = :class_name OR name LIKE :similar_name) 
                       AND name != :current_name 
                       ORDER BY name ASC 
                       LIMIT 10";
        $related_stmt = $pdo->prepare($related_sql);
        $related_stmt->execute([
            'class_name' => $command['class_name'],
            'similar_name' => '%' . substr($command['name'], 0, 3) . '%',
            'current_name' => $command['name']
        ]);
        $related_commands = $related_stmt->fetchAll();
    }

} catch(PDOException $e) {
    $error_message = "Database error: " . $e->getMessage();
    $command = null;
    $related_commands = [];
}

// Helper function to detect language
function detectLanguage($text) {
    return preg_match('/[가-힣]/', $text) ? 'Korean' : 'English';
}

// Helper function to parse command description for parameters
function parseCommandParameters($description) {
    $parameters = [];
    
    // Look for parameter patterns like [param], (param), {param}
    if (preg_match_all('/[\[\(\{]([^)\]\}]+)[\]\)\}]/', $description, $matches)) {
        foreach ($matches[1] as $param) {
            $parameters[] = trim($param);
        }
    }
    
    return $parameters;
}
?>

<!-- Admin Breadcrumb -->
<div class="admin-breadcrumb">
    <ol class="breadcrumb-list">
        <li class="breadcrumb-item"><a href="/l1jdb_database/admin/index.php">Dashboard</a></li>
        <li class="breadcrumb-separator">></li>
        <li class="breadcrumb-item"><a href="/l1jdb_database/admin/pages/commands/commands_list_view.php">Commands</a></li>
        <li class="breadcrumb-separator">></li>
        <li class="breadcrumb-item"><?php echo htmlspecialchars($command_name); ?></li>
    </ol>
</div>

<?php if (isset($error_message)): ?>
    <div class="admin-message admin-message-error">
        <?php echo htmlspecialchars($error_message); ?>
    </div>
    <div class="admin-empty">
        <h3>Command Not Found</h3>
        <p>The requested command could not be found in the database.</p>
        <a href="/l1jdb_database/admin/pages/commands/commands_list_view.php" class="admin-btn admin-btn-primary">
            <span>⬅️</span> Back to Commands List
        </a>
    </div>
<?php else: ?>

<!-- Page Header -->
<div class="admin-header">
    <h1>
        <?php 
        $lang_flag = detectLanguage($command['name']) === 'Korean' ? '🇰🇷' : '🇺🇸';
        echo $lang_flag . ' ' . htmlspecialchars($command['name']); 
        ?>
    </h1>
    <div class="admin-header-actions">
        <a href="/l1jdb_database/admin/pages/commands/commands_list_view.php" class="admin-btn admin-btn-secondary">
            <span>⬅️</span> Back to List
        </a>
        <a href="/l1jdb_database/admin/pages/commands/command_edit.php?name=<?php echo urlencode($command['name']); ?>" class="admin-btn admin-btn-primary">
            <span>✏️</span> Edit Command
        </a>
    </div>
</div>

<!-- Command Details -->
<div class="weapon-advanced-grid">
    <!-- Basic Information -->
    <div class="field-group">
        <h3>🔧 Basic Information</h3>
        
        <div class="form-grid-2">
            <div class="form-group">
                <label>Command Name</label>
                <div style="background: var(--secondary); padding: 0.75rem; border-radius: 8px; border: 2px solid var(--primary); font-family: 'Courier New', monospace; font-weight: bold; color: var(--accent);">
                    <?php echo htmlspecialchars($command['name']); ?>
                </div>
            </div>
            
            <div class="form-group">
                <label>Language</label>
                <div style="background: var(--secondary); padding: 0.75rem; border-radius: 8px; border: 2px solid var(--primary);">
                    <?php 
                    $lang = detectLanguage($command['name']);
                    $flag = $lang === 'Korean' ? '🇰🇷' : '🇺🇸';
                    echo $flag . ' ' . $lang; 
                    ?>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label>Class Name</label>
            <div style="background: var(--secondary); padding: 0.75rem; border-radius: 8px; border: 2px solid var(--primary); font-family: 'Courier New', monospace;">
                <?php echo htmlspecialchars($command['class_name']); ?>
            </div>
        </div>
    </div>

    <!-- Access Control -->
    <div class="field-group">
        <h3>🔐 Access Control</h3>
        
        <div class="form-group">
            <label>Access Level</label>
            <div style="background: var(--secondary); padding: 0.75rem; border-radius: 8px; border: 2px solid var(--primary); display: flex; align-items: center; gap: 0.75rem;">
                <span class="badge <?php echo $command['access_level'] == 9999 ? 'badge-danger' : 'badge-info'; ?>" style="font-size: 1rem; padding: 0.5rem 1rem;">
                    <?php echo $command['access_level']; ?>
                </span>
                <span style="color: var(--text); opacity: 0.8;">
                    <?php 
                    if ($command['access_level'] == 9999) {
                        echo 'Maximum Admin Access';
                    } elseif ($command['access_level'] >= 100) {
                        echo 'High Level Admin';
                    } elseif ($command['access_level'] >= 50) {
                        echo 'Moderator Access';
                    } else {
                        echo 'User Access';
                    }
                    ?>
                </span>
            </div>
        </div>
        
        <div class="form-group">
            <label>Security Level</label>
            <div style="background: var(--secondary); padding: 0.75rem; border-radius: 8px; border: 2px solid var(--primary);">
                <?php 
                if ($command['access_level'] == 9999) {
                    echo '<span style="color: #e74c3c;">🔴 Critical - Super Admin Only</span>';
                } elseif ($command['access_level'] >= 100) {
                    echo '<span style="color: #f39c12;">🟡 High - Admin Required</span>';
                } else {
                    echo '<span style="color: #2ecc71;">🟢 Normal - Standard Access</span>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<!-- Description and Parameters -->
<div class="field-group">
    <h3>📝 Description & Usage</h3>
    
    <?php if (!empty($command['description'])): ?>
        <div class="form-group">
            <label>Description</label>
            <div style="background: var(--secondary); padding: 1rem; border-radius: 8px; border: 2px solid var(--primary); line-height: 1.6;">
                <?php echo nl2br(htmlspecialchars($command['description'])); ?>
            </div>
        </div>
        
        <?php 
        $parameters = parseCommandParameters($command['description']);
        if (!empty($parameters)): 
        ?>
            <div class="form-group">
                <label>Detected Parameters</label>
                <div style="background: var(--secondary); padding: 1rem; border-radius: 8px; border: 2px solid var(--primary);">
                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                        <?php foreach ($parameters as $param): ?>
                            <span class="badge badge-info" style="font-family: 'Courier New', monospace;">
                                <?php echo htmlspecialchars($param); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="admin-message admin-message-warning">
            <strong>No description available</strong><br>
            This command does not have a description in the database. Consider adding one for better documentation.
        </div>
    <?php endif; ?>
</div>

<!-- Usage Examples -->
<div class="field-group">
    <h3>💡 Usage Examples</h3>
    
    <div style="background: var(--secondary); padding: 1rem; border-radius: 8px; border: 2px solid var(--primary);">
        <div style="font-family: 'Courier New', monospace; background: var(--primary); padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem;">
            <span style="color: var(--accent);">Chat command:</span><br>
            <span style="color: #2ecc71;">.</span><span style="color: var(--text);"><?php echo htmlspecialchars($command['name']); ?></span>
            <?php if (!empty($parameters)): ?>
                <?php foreach ($parameters as $param): ?>
                    <span style="color: #f39c12;"> [<?php echo htmlspecialchars($param); ?>]</span>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div style="font-family: 'Courier New', monospace; background: var(--primary); padding: 0.75rem; border-radius: 6px;">
            <span style="color: var(--accent);">Class execution:</span><br>
            <span style="color: #9b59b6;">new</span> <span style="color: var(--text);"><?php echo htmlspecialchars($command['class_name']); ?></span><span style="color: #2ecc71;">()</span>
        </div>
    </div>
</div>

<!-- Related Commands -->
<?php if (!empty($related_commands)): ?>
<div class="field-group">
    <h3>🔗 Related Commands</h3>
    
    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Command Name</th>
                    <th>Access Level</th>
                    <th>Class Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($related_commands as $related): ?>
                    <tr class="clickable-row" onclick="window.location.href='/l1jdb_database/admin/pages/commands/command_detail.php?name=<?php echo urlencode($related['name']); ?>'">
                        <td>
                            <strong style="color: var(--accent); font-family: 'Courier New', monospace;">
                                <?php 
                                $related_name = htmlspecialchars($related['name']);
                                echo preg_match('/[가-힣]/', $related_name) ? '🇰🇷 ' : '🇺🇸 ';
                                echo $related_name; 
                                ?>
                            </strong>
                        </td>
                        <td>
                            <span class="badge <?php echo $related['access_level'] == 9999 ? 'badge-danger' : 'badge-info'; ?>">
                                <?php echo $related['access_level']; ?>
                            </span>
                        </td>
                        <td>
                            <code style="color: var(--text); background: var(--secondary); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.85rem;">
                                <?php echo htmlspecialchars($related['class_name']); ?>
                            </code>
                        </td>
                        <td>
                            <a href="/l1jdb_database/admin/pages/commands/command_detail.php?name=<?php echo urlencode($related['name']); ?>" 
                               class="admin-btn admin-btn-secondary admin-btn-small" 
                               onclick="event.stopPropagation()">
                                <span>👁️</span> View
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Technical Information -->
<div class="field-group stat-group-special">
    <h3>⚙️ Technical Details</h3>
    
    <div class="form-grid-3">
        <div class="form-group">
            <label>Command Type</label>
            <div style="background: var(--secondary); padding: 0.75rem; border-radius: 8px; border: 2px solid var(--primary);">
                <?php 
                if (detectLanguage($command['name']) === 'Korean') {
                    echo '🇰🇷 Localized Command';
                } else {
                    echo '🇺🇸 Standard Command';
                }
                ?>
            </div>
        </div>
        
        <div class="form-group">
            <label>Implementation</label>
            <div style="background: var(--secondary); padding: 0.75rem; border-radius: 8px; border: 2px solid var(--primary);">
                Java Class
            </div>
        </div>
        
        <div class="form-group">
            <label>Category</label>
            <div style="background: var(--secondary); padding: 0.75rem; border-radius: 8px; border: 2px solid var(--primary);">
                <?php 
                // Try to categorize based on class name
                $class_lower = strtolower($command['class_name']);
                if (strpos($class_lower, 'buff') !== false) {
                    echo '✨ Buff/Enhancement';
                } elseif (strpos($class_lower, 'spawn') !== false || strpos($class_lower, 'summon') !== false) {
                    echo '👹 Spawn/Summon';
                } elseif (strpos($class_lower, 'teleport') !== false || strpos($class_lower, 'move') !== false || strpos($class_lower, 'recall') !== false) {
                    echo '🌀 Teleportation';
                } elseif (strpos($class_lower, 'item') !== false || strpos($class_lower, 'create') !== false) {
                    echo '📦 Item Management';
                } elseif (strpos($class_lower, 'kick') !== false || strpos($class_lower, 'ban') !== false) {
                    echo '🔨 Moderation';
                } elseif (strpos($class_lower, 'chat') !== false) {
                    echo '💬 Communication';
                } elseif (strpos($class_lower, 'server') !== false || strpos($class_lower, 'system') !== false) {
                    echo '🖥️ System Control';
                } else {
                    echo '🔧 General Admin';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<script>
// Enhanced detail page functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add copy functionality for command name
    const commandName = document.querySelector('h1');
    if (commandName) {
        commandName.style.cursor = 'pointer';
        commandName.title = 'Click to copy command name';
        
        commandName.addEventListener('click', function() {
            const text = '<?php echo addslashes($command['name']); ?>';
            navigator.clipboard.writeText('.' + text).then(function() {
                // Show temporary feedback
                const originalText = commandName.textContent;
                commandName.textContent = '📋 Copied!';
                commandName.style.color = '#2ecc71';
                
                setTimeout(function() {
                    commandName.textContent = originalText;
                    commandName.style.color = '';
                }, 1500);
            }).catch(function() {
                alert('Command copied: .' + text);
            });
        });
    }
    
    // Enhanced table row interactions
    const rows = document.querySelectorAll('.clickable-row');
    rows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(3px)';
            this.style.boxShadow = '0 4px 12px rgba(253, 127, 68, 0.2)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
            this.style.boxShadow = '';
        });
    });
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // E to edit
        if (e.key === 'e' || e.key === 'E') {
            const editBtn = document.querySelector('a[href*="command_edit.php"]');
            if (editBtn) {
                window.location.href = editBtn.href;
            }
        }
        
        // B to go back
        if (e.key === 'b' || e.key === 'B') {
            const backBtn = document.querySelector('a[href*="commands_list_view.php"]');
            if (backBtn) {
                window.location.href = backBtn.href;
            }
        }
    });
});
</script>

<?php require_once '../../includes/footer.php'; ?>
