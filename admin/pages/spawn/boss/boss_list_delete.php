<?php
require_once('../../../includes/header.php');

// Database connection (using the same pattern as API files)
function getDbConnection() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "l1j_remastered";
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8");
    
    return $conn;
}

// Validate input
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<div class="admin-content-wrapper"><div class="admin-message admin-message-error">Invalid boss ID!</div>';
    echo '<a href="boss_list_view.php" class="admin-btn admin-btn-primary">Back to Boss List</a></div>';
    require_once('../../../includes/footer.php');
    exit;
}

$id = (int)$_GET['id'];
$db = getDbConnection();

// Get boss data for confirmation
$query = "SELECT * FROM spawnlist_boss WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo '<div class="admin-content-wrapper"><div class="admin-message admin-message-warning">Boss not found!</div>';
    echo '<a href="boss_list_view.php" class="admin-btn admin-btn-primary">Back to Boss List</a></div>';
    require_once('../../../includes/footer.php');
    exit;
}

$boss = $result->fetch_assoc();
$bossName = $boss['name'];

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete']) && $_POST['confirm_delete'] === 'yes') {
    $deleteQuery = "DELETE FROM spawnlist_boss WHERE id = ?";
    $deleteStmt = $db->prepare($deleteQuery);
    $deleteStmt->bind_param("i", $id);
    
    if ($deleteStmt->execute()) {
        // Redirect to the list with success message
        header("Location: boss_list_view.php?deleted=true&name=" . urlencode($bossName));
        exit;
    } else {
        $error = "Error deleting boss spawn: " . $deleteStmt->error;
    }
}
?>

<div class="admin-content-wrapper">
    <div class="page-header">
        <div class="breadcrumb">
            <a href="/l1jdb_database/admin/">Dashboard</a> &raquo; 
            <a href="boss_list_view.php">Boss Management</a> &raquo; 
            <span>Delete Boss</span>
        </div>
        <h1>Confirm Deletion</h1>
    </div>
    
    <div class="field-group" style="max-width: 800px; margin: 0 auto;">
        <div class="admin-message admin-message-warning">
            <h4><i class="fa fa-exclamation-triangle"></i> Warning!</h4>
            <p>You are about to delete the following boss spawn. This action cannot be undone!</p>
        </div>
        
        <h3>Boss Information</h3>
        <table class="admin-table">
            <tr>
                <th width="30%">ID</th>
                <td><?php echo htmlspecialchars($boss['id']); ?></td>
            </tr>
            <tr>
                <th>Name</th>
                <td><?php echo htmlspecialchars($boss['name']); ?></td>
            </tr>
            <tr>
                <th>NPC ID</th>
                <td><?php echo htmlspecialchars($boss['npcid']); ?></td>
            </tr>
            <tr>
                <th>Map ID</th>
                <td><?php echo htmlspecialchars($boss['spawnMapId']); ?></td>
            </tr>
            <tr>
                <th>Spawn Type</th>
                <td><?php echo htmlspecialchars($boss['spawnType']); ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <?php if ($boss['isYN'] == 'true'): ?>
                        <span class="badge badge-success">Active</span>
                    <?php else: ?>
                        <span class="badge badge-danger">Inactive</span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        
        <?php if (isset($error)): ?>
            <div class="admin-message admin-message-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" style="text-align: center; margin-top: 2rem;">
            <input type="hidden" name="confirm_delete" value="yes">
            <button type="submit" class="admin-btn admin-btn-danger admin-btn-large">Yes, Delete This Boss Spawn</button>
            <a href="boss_list_detail.php?id=<?php echo $id; ?>" class="admin-btn admin-btn-secondary admin-btn-large" style="margin-left: 1rem;">Cancel</a>
        </form>
    </div>
    
    <div style="text-align: center; margin-top: 2rem;">
        <a href="boss_list_view.php" class="admin-btn admin-btn-primary">Back to Boss List</a>
    </div>
</div>

<?php
$db->close();
require_once('../../../includes/footer.php');
?>