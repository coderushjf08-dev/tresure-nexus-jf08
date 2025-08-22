<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$page_title = "Create Hunt";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $privacy = $_POST['privacy'] ?? 'public';
    $estimated_duration = (int)($_POST['estimated_duration'] ?? 30); // minutes, user-defined

    if ($title !== '' && $description !== '' && $estimated_duration > 0) {
        $db = getDB();
        $difficulty = 'Medium'; // default
        $stmt = $db->prepare("
            INSERT INTO hunts (creator_id, title, description, privacy, difficulty, estimated_duration)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$_SESSION['user_id'], $title, $description, $privacy, $difficulty, $estimated_duration]);

        $hunt_id = $db->lastInsertId();
        header("Location: add_clue.php?hunt_id=" . $hunt_id);
        exit;
    } else {
        $error = "All fields are required and time must be > 0!";
    }
}

include 'includes/header.php';
?>
<section style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 100px 20px 20px;">
    <div class="glass-card" style="width: 100%; max-width: 600px; padding: 40px;">
        <div class="text-center" style="margin-bottom: 32px;">
            <h1 class="text-gradient-cyber" style="font-size: 2.5rem; margin-bottom: 8px;">🗺️ Create a Hunt</h1>
            <p style="color: var(--text-muted);">Design your own treasure hunt adventure</p>
        </div>

        <?php if (!empty($error)): ?>
            <div style="padding: 16px; margin-bottom: 24px; background: var(--glass-bg); border: 1px solid var(--neon-red); border-radius: 8px; color: var(--neon-red);">
                ❌ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" style="display: flex; flex-direction: column; gap: 20px;">
            <div>
                <label style="color: var(--text-muted);">Hunt Title:</label>
                <input type="text" name="title" required class="cyber-input" placeholder="Enter hunt name">
            </div>

            <div>
                <label style="color: var(--text-muted);">Description:</label>
                <textarea name="description" required rows="4" class="cyber-input" placeholder="Write a short description of the hunt"></textarea>
            </div>

            <div>
                <label style="color: var(--text-muted);">Privacy:</label>
                <select name="privacy" required class="cyber-input">
                    <option value="public">🌍 Public - Anyone can play</option>
                    <option value="private">🔒 Private - Only invited players</option>
                </select>
            </div>

            <div>
                <label style="color: var(--text-muted);">Time Limit (minutes):</label>
                <input type="number" name="estimated_duration" min="1" value="30" required class="cyber-input" placeholder="e.g., 30">
                <small style="color: var(--text-muted);">Players will see a countdown for this duration.</small>
            </div>

            <button type="submit" class="cyber-btn hologram" style="width: 100%; padding: 16px; font-size: 1.1rem;">🎯 Create & Add Clues</button>
        </form>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
