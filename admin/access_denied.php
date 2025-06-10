<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

getPageHeader('Access Denied');
?>

<div class="main">
    <div class="access-denied-page">
        <h1>Access Denied</h1>
        <p>You don't have sufficient privileges to access this area.</p>
        <p>Please contact an administrator if you believe this is an error.</p>
        <a href="<?php echo SITE_URL; ?>" class="btn-home">Return to Home</a>
    </div>
</div>



<?php getPageFooter(); ?>
