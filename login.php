<?php
session_start();
require_once 'config/database.php';

$page_title = "Hunter Login";

if ($_POST) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if ($username && $password) {
        $db = getDB();
        $stmt = $db->prepare("SELECT id, username, password FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: index.php');
            exit;
        } else {
            $error = "Invalid credentials";
        }
    }
}

include 'includes/header.php';
?>

<section style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 100px 20px 20px;">
    <div class="glass-card" style="width: 100%; max-width: 400px; padding: 40px;">
        <div class="text-center" style="margin-bottom: 32px;">
            <h1 class="text-gradient-cyber" style="font-size: 2.5rem; margin-bottom: 8px;">
                Hunter Login
            </h1>
            <p style="color: var(--text-muted);">Access your digital treasure hunting account</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div style="padding: 16px; margin-bottom: 24px; background: var(--glass-bg); border: 1px solid var(--neon-red); border-radius: 8px; color: var(--neon-red);">
                ❌ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" style="display: flex; flex-direction: column; gap: 20px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--text-muted);">Username or Email:</label>
                <input type="text" name="username" required 
                       style="width: 100%; padding: 12px; background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 8px; color: var(--text-primary); font-size: 1rem;"
                       placeholder="Enter username or email">
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--text-muted);">Password:</label>
                <input type="password" name="password" required
                       style="width: 100%; padding: 12px; background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 8px; color: var(--text-primary); font-size: 1rem;"
                       placeholder="Enter password">
            </div>
            
            <button type="submit" class="cyber-btn hologram" style="width: 100%; padding: 16px; font-size: 1.1rem; margin-top: 8px;">
                🚀 Enter The Nexus
            </button>
        </form>
        
        <div class="text-center" style="margin-top: 24px;">
            <p style="color: var(--text-muted);">
                New hunter? <a href="register.php" class="neon-blue" style="text-decoration: none;">Join the hunt</a>
            </p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>