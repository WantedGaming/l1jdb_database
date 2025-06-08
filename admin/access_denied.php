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

<style>
.access-denied-page {
    text-align: center;
    background-color: var(--primary);
    padding: 4rem 2rem;
    border-radius: 8px;
    margin: 4rem auto;
    max-width: 600px;
}

.access-denied-page h1 {
    color: var(--accent);
    margin-bottom: 2rem;
    font-size: 2.5rem;
}

.access-denied-page p {
    margin-bottom: 1rem;
    font-size: 1.1rem;
    opacity: 0.9;
}

.btn-home {
    display: inline-block;
    margin-top: 2rem;
    padding: 1rem 2rem;
    background-color: var(--accent);
    color: var(--text);
    text-decoration: none;
    border-radius: 4px;
    transition: background-color 0.3s ease;
}

.btn-home:hover {
    background-color: #e56a37;
}
</style>

<?php getPageFooter(); ?>
