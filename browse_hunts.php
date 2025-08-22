<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$page_title = "Discover Hunts";
include 'includes/header.php';

$db = getDB();
$stmt = $db->prepare("
    SELECT h.id, h.title, h.description, h.created_at, u.username, h.estimated_duration
    FROM hunts h
    JOIN users u ON h.creator_id = u.id
    WHERE h.privacy = 'public'
    ORDER BY h.created_at DESC
");
$stmt->execute();
$hunts = $stmt->fetchAll();
?>
<section style="min-height: 100vh; padding: 100px 20px 20px;">
    <div class="container">
        <h1 class="text-gradient-cyber" style="font-size: 2rem; margin-bottom: 16px;">🌍 Public Hunts</h1>
        <?php if (!empty($hunts)): ?>
            <div class="grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px;">
                <?php foreach ($hunts as $h): ?>
                <div class="glass-card" style="padding: 16px;">
                    <h3 style="margin:0 0 8px;"><?php echo htmlspecialchars($h['title']); ?></h3>
                    <p style="color:var(--text-muted);margin:0 0 8px;"><?php echo htmlspecialchars($h['description']); ?></p>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <small>👤 <?php echo htmlspecialchars($h['username']); ?> • ⏳ <?php echo (int)$h['estimated_duration']; ?>m</small>
                        <a href="play_hunt.php?hunt_id=<?php echo (int)$h['id']; ?>" class="cyber-btn hologram" style="padding: 8px 14px;">🚀 Start Hunt</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="padding: 16px; background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 8px; color: var(--text-muted); text-align: center;">
                No hunts available yet. Be the first to <a href="create_hunt.php" class="neon-blue">create one</a>!
            </div>
        <?php endif; ?>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
