<?php
require_once '../../../includes/config.php';

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /l1jdb_database/admin/pages/commands/commands_list_view.php');
    exit;
}

$command_name = trim($_POST['command_name'] ?? '');

if (empty($command_name)) {
    header('Location: /l1jdb_database/admin/pages/commands/commands_list_view.php');
    exit;
}

try {
    // Check if command exists
    $check_sql = "SELECT COUNT(*) FROM commands WHERE name = :name";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute(['name' => $command_name]);
    
    if ($check_stmt->fetchColumn() == 0) {
        // Command doesn't exist, redirect with error
        header('Location: /l1jdb_database/admin/pages/commands/commands_list_view.php?error=not_found');
        exit;
    }
    
    // Delete the command
    $delete_sql = "DELETE FROM commands WHERE name = :name";
    $delete_stmt = $pdo->prepare($delete_sql);
    $delete_stmt->execute(['name' => $command_name]);
    
    // Redirect with success message
    header('Location: /l1jdb_database/admin/pages/commands/commands_list_view.php?success=deleted&command=' . urlencode($command_name));
    exit;
    
} catch(PDOException $e) {
    // Redirect with error message
    header('Location: /l1jdb_database/admin/pages/commands/commands_list_view.php?error=delete_failed');
    exit;
}
?>
