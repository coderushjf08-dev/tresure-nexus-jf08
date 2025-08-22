<?php
session_start();
require_once 'config/database.php';

$page_title = "Hunter Registration";

if ($_POST) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if ($username && $email && $password && $password === $confirm) {
        $db = getDB();

        // Check if username or email exists
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = "Username or Email already taken!";
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hash]);

            $_SESSION['user_id'] = $db->lastInsertId();
            $_SESSION['username'] = $username;

            header("Location: index.php");
            exit;
        }
    } else {
        $error = "Please fill all fields correctly.";
    }
}

include 'includes/header.php';
?>

<section style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 100px 20px 20px;">
    <div class="glass-card" style="width: 100%; max-width: 500px; padding: 40px;">
        <div class="text-center" style="margin-bottom: 32px;">
            <h1 class="text-gradient-cyber" style="font-size: 2.5rem; margin-bottom: 8px;">
                🧑‍🚀 Register
            </h1>
            <p style="color: var(--text-muted);">Join the treasure hunting galaxy</p>
        </div>

        <?php if (isset($error)): ?>
            <div style="padding: 16px; margin-bottom: 24px; background: var(--glass-bg); border: 1px solid var(--neon-red); border-radius: 8px; color: var(--neon-red);">
                ❌ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" style="display: flex; flex-direction: column; gap: 20px;">
            <div>
                <label style="color: var(--text-muted);">Username:</label>
                <input type="text" name="username" required class="cyber-input" placeholder="Choose username">
            </div>

            <div>
                <label style="color: var(--text-muted);">Email:</label>
                <input type="email" name="email" required class="cyber-input" placeholder="Enter email">
            </div>

            <div>
                <label style="color: var(--text-muted);">Password:</label>
                <input type="password" name="password" required class="cyber-input" placeholder="Enter password">
            </div>

            <div>
                <label style="color: var(--text-muted);">Confirm Password:</label>
                <input type="password" name="confirm" required class="cyber-input" placeholder="Re-enter password">
            </div>

            <button type="submit" class="cyber-btn hologram" style="width: 100%; padding: 16px; font-size: 1.1rem;">
                🚀 Launch Account
            </button>
        </form>

        <div class="text-center" style="margin-top: 24px;">
            <p style="color: var(--text-muted);">
                Already a hunter? <a href="login.php" class="neon-blue" style="text-decoration: none;">Login</a>
            </p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
