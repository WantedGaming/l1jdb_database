<?php
require_once __DIR__ . '/../common/detail_header.php';

// Get id from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo "<div class='alert alert-error'>Invalid potential ID provided.</div>";
    require_once __DIR__ . '/../common/detail_footer.php';
    exit;
}

// Main query to get potential details with translation
$query = "
    SELECT 
        p.*,
        t.text_english as desc_en
    FROM bin_potential_common p
    LEFT JOIN 0_translations t ON p.desc_kr = t.text_korean
    WHERE p.id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$potential = $result->fetch_assoc();

if (!$potential) {
    echo "<div class='alert alert-error'>Potential with ID $id not found.</div>";
    require_once __DIR__ . '/../common/detail_footer.php';
    exit;
}

// Helper function to format grade
function formatGrade($grade) {
    $grade_names = [
        0 => 'Normal',
        1 => 'Magic',
        2 => 'Rare',
        3 => 'Epic',
        4 => 'Legendary',
        5 => 'Mythic'
    ];
    
    return isset($grade_names[$grade]) ? $grade_names[$grade] . " ($grade)" : "Grade $grade";
}

// Helper function to parse and format material list
function formatMaterialList($material_list) {
    if (empty($material_list)) return 'None';
    
    // Try to parse as JSON or structured data
    $lines = explode("\n", $material_list);
    $formatted = [];
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line)) {
            $formatted[] = htmlspecialchars($line);
        }
    }
    
    return implode('<br>', $formatted);
}

// Helper function to parse and format event config
function formatEventConfig($event_config) {
    if (empty($event_config)) return 'None';
    
    // Try to parse as JSON or structured data
    $lines = explode("\n", $event_config);
    $formatted = [];
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line)) {
            $formatted[] = htmlspecialchars($line);
        }
    }
    
    return implode('<br>', $formatted);
}
?>

<div class="page-header">
    <nav class="breadcrumb">
        <a href="../../index.php">Dashboard</a> → 
        <a href="../index.php">Binary Tables</a> → 
        <a href="potential_list_view.php">Potential Common List</a> → 
        <span>Potential #<?php echo $id; ?></span>
    </nav>
    <h1>Potential Details - ID: <?php echo $id; ?></h1>
</div>

<div class="admin-actions">
    <a href="potential_list_view.php" class="admin-btn admin-btn-secondary">← Back to List</a>
</div>

<div class="detail-container">
    <!-- Main Content Row -->
    <div class="weapon-detail-row">
        <!-- Column 1: Image Preview (or placeholder) -->
        <div class="weapon-image-col">
            <div class="weapon-image-container">
                <img src="../../../../assets/img/icons/0.png" 
                     alt="Potential Icon" 
                     class="weapon-main-image">
            </div>
            <div class="icon-id-display">
                <span>Potential ID: <?php echo htmlspecialchars($potential['id']); ?></span>
            </div>
        </div>
        
        <!-- Column 2: Basic Information -->
        <div class="weapon-info-col">
            <div class="weapon-basic-info">
                <h2>Basic Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Grade:</label>
                        <span><?php echo formatGrade($potential['grade']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Description ID:</label>
                        <span><?php echo htmlspecialchars($potential['desc_id']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="detail-section">
        <h2>Description</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Description (Korean):</label>
                <span><?php echo htmlspecialchars($potential['desc_kr'] ?: 'None'); ?></span>
            </div>
            <div class="detail-item">
                <label>Description (English):</label>
                <span><?php echo htmlspecialchars($potential['desc_en'] ?: 'Not translated'); ?></span>
            </div>
        </div>
    </div>

    <?php if (!empty($potential['material_list'])): ?>
    <div class="detail-section">
        <h2>Material List</h2>
        <div class="detail-item">
            <label>Materials:</label>
            <div class="detail-text"><?php echo formatMaterialList($potential['material_list']); ?></div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($potential['event_config'])): ?>
    <div class="detail-section">
        <h2>Event Configuration</h2>
        <div class="detail-item">
            <label>Event Config:</label>
            <div class="detail-text"><?php echo formatEventConfig($potential['event_config']); ?></div>
        </div>
    </div>
    <?php endif; ?>

    <div class="detail-section">
        <h2>Raw Data</h2>
        <div class="detail-grid">
            <?php if (!empty($potential['material_list'])): ?>
            <div class="detail-item">
                <label>Raw Material List:</label>
                <div class="detail-text" style="white-space: pre-wrap; word-wrap: break-word; overflow-wrap: break-word; max-width: 100%; font-family: monospace; padding: 10px; border-radius: 4px;"><?php echo htmlspecialchars($potential['material_list']); ?></div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($potential['event_config'])): ?>
            <div class="detail-item">
                <label>Raw Event Config:</label>
                <div class="detail-text" style="white-space: pre-wrap; word-wrap: break-word; overflow-wrap: break-word; max-width: 100%; font-family: monospace; padding: 10px; border-radius: 4px;"><?php echo htmlspecialchars($potential['event_config']); ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../common/detail_footer.php'; ?>
