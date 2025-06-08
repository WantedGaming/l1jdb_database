<?php
require_once '../includes/header.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/admin/');
    exit;
}

$error = '';

if ($_POST) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (!empty($username) && !empty($password)) {
        // Check user credentials - using existing l1j_remastered accounts table
        $sql = "SELECT login, password, access_level FROM accounts WHERE login = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();
        
        if ($user && $password === $user['password']) { // Direct password comparison for L1J
            $_SESSION['user_id'] = $user['login'];
            $_SESSION['username'] = $user['login'];
            $_SESSION['access_level'] = $user['access_level'];
            
            // Log admin activity
            logAdminActivity('LOGIN', 'accounts', $user['login'], 'User logged in');
            
            header('Location: ' . SITE_URL . '/admin/');
            exit;
        } else {
            $error = 'Invalid username or password';
        }
    } else {
        $error = 'Please fill all fields';
    }
}

getPageHeader('Admin Login');
?>

<main>
    <?php renderHero('admin'); ?>
    <div class="login-container">
        <form method="POST" class="login-form">
            <h2>Admin Login</h2>
            
            <?php if ($error): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="login-btn">Login</button>
        </form>
    </div>
</main>

<style>
.login-container {
    max-width: 400px;
    margin: 4rem auto;
    padding: 2rem;
    background-color: var(--primary);
    border-radius: 8px;
}

.login-form h2 {
    text-align: center;
    margin-bottom: 2rem;
    color: var(--accent);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--text);
}

.form-group input {
    width: 100%;
    padding: 0.75rem;
    background-color: var(--secondary);
    border: 1px solid var(--primary);
    border-radius: 4px;
    color: var(--text);
    font-size: 1rem;
}

.form-group input:focus {
    outline: none;
    border-color: var(--accent);
}

.login-btn {
    width: 100%;
    padding: 0.75rem;
    background-color: var(--accent);
    color: var(--text);
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.login-btn:hover {
    background-color: #e56a37;
}

.error-message {
    background-color: #dc3545;
    color: white;
    padding: 0.75rem;
    border-radius: 4px;
    margin-bottom: 1rem;
    text-align: center;
}
</style>

<?php getPageFooter(); ?>
