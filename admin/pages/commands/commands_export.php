<?php
require_once '../../../includes/config.php';

// Set the content type and filename for download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="l1j_commands_export_' . date('Y-m-d_H-i-s') . '.csv"');

// Open output stream
$output = fopen('php://output', 'w');

try {
    // Get all commands
    $sql = "SELECT name, access_level, class_name, description FROM commands ORDER BY name ASC";
    $stmt = $pdo->query($sql);
    $commands = $stmt->fetchAll();

    // Write CSV header
    fputcsv($output, ['Command Name', 'Access Level', 'Class Name', 'Description', 'Language', 'Category']);

    // Helper function to detect language
    function detectLanguage($text) {
        return preg_match('/[가-힣]/', $text) ? 'Korean' : 'English';
    }

    // Helper function to categorize commands
    function categorizeCommand($className) {
        $class_lower = strtolower($className);
        if (strpos($class_lower, 'buff') !== false) {
            return 'Buff/Enhancement';
        } elseif (strpos($class_lower, 'spawn') !== false || strpos($class_lower, 'summon') !== false) {
            return 'Spawn/Summon';
        } elseif (strpos($class_lower, 'teleport') !== false || strpos($class_lower, 'move') !== false || strpos($class_lower, 'recall') !== false) {
            return 'Teleportation';
        } elseif (strpos($class_lower, 'item') !== false || strpos($class_lower, 'create') !== false) {
            return 'Item Management';
        } elseif (strpos($class_lower, 'kick') !== false || strpos($class_lower, 'ban') !== false) {
            return 'Moderation';
        } elseif (strpos($class_lower, 'chat') !== false) {
            return 'Communication';
        } elseif (strpos($class_lower, 'server') !== false || strpos($class_lower, 'system') !== false) {
            return 'System Control';
        } else {
            return 'General Admin';
        }
    }

    // Write command data
    foreach ($commands as $command) {
        fputcsv($output, [
            $command['name'],
            $command['access_level'],
            $command['class_name'],
            $command['description'],
            detectLanguage($command['name']),
            categorizeCommand($command['class_name'])
        ]);
    }

} catch(PDOException $e) {
    // If there's an error, write error message to CSV
    fputcsv($output, ['Error', 'Database error: ' . $e->getMessage()]);
}

// Close the output stream
fclose($output);
exit;
?>
