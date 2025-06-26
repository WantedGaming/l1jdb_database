<?php
require_once '../../includes/header.php';
require_once '../../../includes/config.php';

// Handle success/error messages
$success_message = '';
$error_message = '';

if (isset($_GET['success'])) {
    if ($_GET['success'] === 'deleted' && isset($_GET['command'])) {
        $success_message = "Command '" . htmlspecialchars($_GET['command']) . "' has been deleted successfully.";
    }
}

if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'not_found':
            $error_message = "Command not found.";
            break;
        case 'delete_failed':
            $error_message = "Failed to delete command. Please try again.";
            break;
    }
}

// Pagination settings
$records_per_page = 25;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Filter settings
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$access_level_filter = isset($_GET['access_level']) ? $_GET['access_level'] : '';
$language_filter = isset($_GET['language']) ? $_GET['language'] : '';

// Build WHERE clause
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(name LIKE :search OR class_name LIKE :search OR description LIKE :search)";
    $params['search'] = '%' . $search . '%';
}

if (!empty($access_level_filter)) {
    $where_conditions[] = "access_level = :access_level";
    $params['access_level'] = $access_level_filter;
}

// Language filter based on command name pattern
if ($language_filter === 'english') {
    $where_conditions[] = "name REGEXP '^[a-zA-Z0-9_]+$'";
} elseif ($language_filter === 'korean') {
    $where_conditions[] = "name REGEXP '[가-힣]'";
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

try {
    // Get total count
    $count_sql = "SELECT COUNT(*) as total FROM commands $where_clause";
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total_records = $count_stmt->fetch()['total'];
    $total_pages = ceil($total_records / $records_per_page);

    // Get commands with pagination
    $sql = "SELECT * FROM commands $where_clause ORDER BY name ASC LIMIT :offset, :limit";
    $stmt = $pdo->prepare($sql);
    
    // Bind parameters
    foreach ($params as $key => $value) {
        $stmt->bindValue(':' . $key, $value);
    }
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
    
    $stmt->execute();
    $commands = $stmt->fetchAll();

    // Get unique access levels for filter
    $access_levels_sql = "SELECT DISTINCT access_level FROM commands ORDER BY access_level";
    $access_levels_stmt = $pdo->query($access_levels_sql);
    $access_levels = $access_levels_stmt->fetchAll();

} catch(PDOException $e) {
    $database_error_message = "Database error: " . $e->getMessage();
    $commands = [];
    $total_records = 0;
    $total_pages = 0;
    $access_levels = [];
}
?>

<!-- Admin Breadcrumb -->
<div class="admin-breadcrumb">
    <ol class="breadcrumb-list">
        <li class="breadcrumb-item"><a href="/l1jdb_database/admin/index.php">Dashboard</a></li>
        <li class="breadcrumb-separator">></li>
        <li class="breadcrumb-item">In-Game Commands</li>
    </ol>
</div>

<!-- Page Header -->
<div class="admin-header">
    <h1>In-Game Commands Management</h1>
    <div class="admin-header-actions">
        <a href="/l1jdb_database/admin/pages/commands/command_add.php" class="admin-btn admin-btn-primary">
            <span>➕</span> Add New Command
        </a>
        <a href="/l1jdb_database/admin/pages/commands/commands_export.php" class="admin-btn admin-btn-secondary">
            <span>📊</span> Export Commands
        </a>
    </div>
</div>

<?php if (!empty($success_message)): ?>
    <div class="admin-message admin-message-success">
        <?php echo $success_message; ?>
    </div>
<?php endif; ?>

<?php if (!empty($error_message)): ?>
    <div class="admin-message admin-message-error">
        <?php echo $error_message; ?>
    </div>
<?php endif; ?>

<?php if (isset($database_error_message)): ?>
    <div class="admin-message admin-message-error">
        <?php echo htmlspecialchars($database_error_message); ?>
    </div>
<?php endif; ?>

<!-- Filters Section -->
<div class="admin-filters">
    <h3>🔍 Filter Commands</h3>
    
    <form class="filter-form" method="GET">
        <div class="filter-group">
            <label for="search">Search Commands</label>
            <input type="text" 
                   id="search" 
                   name="search" 
                   class="search-input" 
                   placeholder="Search by name, class, or description..." 
                   value="<?php echo htmlspecialchars($search); ?>">
        </div>
        
        <div class="filter-group">
            <label for="access_level">Access Level</label>
            <select id="access_level" name="access_level">
                <option value="">All Access Levels</option>
                <?php foreach ($access_levels as $level): ?>
                    <option value="<?php echo $level['access_level']; ?>" 
                            <?php echo $access_level_filter == $level['access_level'] ? 'selected' : ''; ?>>
                        <?php echo $level['access_level']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="filter-group">
            <label for="language">Language</label>
            <select id="language" name="language">
                <option value="">All Languages</option>
                <option value="english" <?php echo $language_filter === 'english' ? 'selected' : ''; ?>>English Commands</option>
                <option value="korean" <?php echo $language_filter === 'korean' ? 'selected' : ''; ?>>Korean Commands</option>
            </select>
        </div>
        
        <div class="btn-group">
            <button type="submit" class="admin-btn admin-btn-primary">
                <span>🔍</span> Apply Filters
            </button>
            <a href="/l1jdb_database/admin/pages/commands/commands_list_view.php" class="admin-btn admin-btn-secondary">
                <span>🔄</span> Reset
            </a>
        </div>
    </form>
</div>

<!-- Results Info -->
<?php if (!empty($commands) || !empty($search) || !empty($access_level_filter) || !empty($language_filter)): ?>
    <div class="results-info">
        <strong>Results:</strong> 
        Showing <?php echo count($commands); ?> of <?php echo $total_records; ?> commands
        <?php if (!empty($search)): ?>
            for search "<?php echo htmlspecialchars($search); ?>"
        <?php endif; ?>
        <?php if (!empty($access_level_filter)): ?>
            with access level <?php echo htmlspecialchars($access_level_filter); ?>
        <?php endif; ?>
        <?php if (!empty($language_filter)): ?>
            in <?php echo htmlspecialchars($language_filter); ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Commands Table -->
<?php if (!empty($commands)): ?>
    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th class="table-cell-name">Command Name</th>
                    <th class="table-cell-number">Access Level</th>
                    <th class="table-cell-name">Class Name</th>
                    <th class="table-cell-name">Description</th>
                    <th class="table-cell-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commands as $command): ?>
                    <tr class="clickable-row" onclick="window.location.href='/l1jdb_database/admin/pages/commands/command_detail.php?name=<?php echo urlencode($command['name']); ?>'">
                        <td class="table-cell-name">
                            <strong style="color: var(--accent); font-family: 'Courier New', monospace;">
                                <?php 
                                $command_name = htmlspecialchars($command['name']);
                                echo preg_match('/[가-힣]/', $command_name) ? '🇰🇷 ' : '🇺🇸 ';
                                echo $command_name; 
                                ?>
                            </strong>
                        </td>
                        <td class="table-cell-number">
                            <span class="badge <?php echo $command['access_level'] == 9999 ? 'badge-danger' : 'badge-info'; ?>">
                                <?php echo $command['access_level']; ?>
                            </span>
                        </td>
                        <td class="table-cell-name">
                            <code style="color: var(--text); background: var(--secondary); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.85rem;">
                                <?php echo htmlspecialchars($command['class_name']); ?>
                            </code>
                        </td>
                        <td class="table-cell-name">
                            <?php 
                            $description = htmlspecialchars($command['description']);
                            if (empty($description)) {
                                echo '<span style="color: var(--text); opacity: 0.5; font-style: italic;">No description available</span>';
                            } else {
                                // Truncate long descriptions
                                $max_length = 100;
                                if (strlen($description) > $max_length) {
                                    echo substr($description, 0, $max_length) . '...';
                                } else {
                                    echo $description;
                                }
                            }
                            ?>
                        </td>
                        <td class="table-cell-actions">
                            <a href="/l1jdb_database/admin/pages/commands/command_detail.php?name=<?php echo urlencode($command['name']); ?>" 
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

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div class="admin-pagination">
            <?php if ($page > 1): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>" class="admin-btn-page">
                    ⏮️ First
                </a>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="admin-btn-page">
                    ⬅️ Previous
                </a>
            <?php endif; ?>

            <div class="pagination-pages">
                <?php
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);
                
                if ($start_page > 1): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>" class="admin-btn-page">1</a>
                    <?php if ($start_page > 2): ?>
                        <span class="page-dots">...</span>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                       class="admin-btn-page <?php echo $i == $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($end_page < $total_pages): ?>
                    <?php if ($end_page < $total_pages - 1): ?>
                        <span class="page-dots">...</span>
                    <?php endif; ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $total_pages])); ?>" class="admin-btn-page"><?php echo $total_pages; ?></a>
                <?php endif; ?>
            </div>

            <?php if ($page < $total_pages): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="admin-btn-page">
                    Next ➡️
                </a>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $total_pages])); ?>" class="admin-btn-page">
                    Last ⏭️
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

<?php else: ?>
    <div class="admin-empty">
        <h3>No Commands Found</h3>
        <p>
            <?php if (!empty($search) || !empty($access_level_filter) || !empty($language_filter)): ?>
                No commands match your current filters. Try adjusting your search criteria.
            <?php else: ?>
                No commands are currently available in the database.
            <?php endif; ?>
        </p>
        <a href="/l1jdb_database/admin/pages/commands/command_add.php" class="admin-btn admin-btn-primary">
            <span>➕</span> Add First Command
        </a>
    </div>
<?php endif; ?>

<!-- Statistics Section -->
<section class="admin-system-status">
    <div class="admin-header">
        <h2>📊 Command Statistics</h2>
    </div>
    
    <div class="admin-stats">
        <div class="stat-card">
            <h3>Total Commands</h3>
            <div class="stat-number"><?php echo $total_records; ?></div>
            <div class="stat-label">Available in system</div>
        </div>
        
        <div class="stat-card">
            <h3>Admin Commands</h3>
            <div class="stat-number status-good">
                <?php 
                try {
                    $admin_count = $pdo->query("SELECT COUNT(*) FROM commands WHERE access_level = 9999")->fetchColumn();
                    echo $admin_count;
                } catch(PDOException $e) {
                    echo '0';
                }
                ?>
            </div>
            <div class="stat-label">Access level 9999</div>
        </div>
        
        <div class="stat-card">
            <h3>English Commands</h3>
            <div class="stat-number">
                <?php 
                try {
                    $english_count = $pdo->query("SELECT COUNT(*) FROM commands WHERE name REGEXP '^[a-zA-Z0-9_]+$'")->fetchColumn();
                    echo $english_count;
                } catch(PDOException $e) {
                    echo '0';
                }
                ?>
            </div>
            <div class="stat-label">English language</div>
        </div>
        
        <div class="stat-card">
            <h3>Korean Commands</h3>
            <div class="stat-number">
                <?php 
                try {
                    $korean_count = $pdo->query("SELECT COUNT(*) FROM commands WHERE name REGEXP '[가-힣]'")->fetchColumn();
                    echo $korean_count;
                } catch(PDOException $e) {
                    echo '0';
                }
                ?>
            </div>
            <div class="stat-label">Korean language</div>
        </div>
    </div>
</section>

<script>
// Enhanced search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const filterForm = document.querySelector('.filter-form');
    let searchTimeout;

    // Auto-submit search after typing stops
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                // Auto-submit if search has content and user stopped typing
                if (searchInput.value.length >= 3) {
                    filterForm.submit();
                }
            }, 1000);
        });
    }

    // Enhance table row click handling
    const rows = document.querySelectorAll('.clickable-row');
    rows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(2px)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });

    // Add keyboard navigation
    document.addEventListener('keydown', function(e) {
        // Ctrl+F to focus search
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            searchInput.focus();
            searchInput.select();
        }
    });
});
</script>

<?php require_once '../../includes/footer.php'; ?>
