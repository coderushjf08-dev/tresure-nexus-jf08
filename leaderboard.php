<?php
session_start();
require_once 'config/database.php';

$page_title = "Leaderboard";
$db = getDB();

$stmt = $db->query("
    SELECT username, level, total_score, hunts_completed, avg_time
    FROM users
    ORDER BY total_score DESC, hunts_completed DESC, avg_time ASC, username ASC
    LIMIT 20
");
$players = $stmt->fetchAll();

include 'includes/header.php';
?>
<section style="min-height: 100vh; padding: 100px 20px 20px;">
    <div class="glass-card" style="max-width: 900px; margin: auto; padding: 40px;">
        <h1 class="text-gradient-cyber" style="font-size: 2rem; margin-bottom: 16px;">🏆 Global Leaderboard</h1>
        <?php if (!empty($players)): ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align:left; border-bottom: 1px solid var(--glass-border);">
                    <th style="padding: 8px;">Rank</th>
                    <th style="padding: 8px;">Player</th>
                    <th style="padding: 8px;">Level</th>
                    <th style="padding: 8px;">Total Score</th>
                    <th style="padding: 8px;">Hunts Completed</th>
                    <th style="padding: 8px;">Avg Time</th>
                </tr>
            </thead>
            <tbody>
                <?php $rank=1; foreach ($players as $p): ?>
                <tr style="border-bottom: 1px solid var(--glass-border);">
                    <td style="padding: 8px;"><?php echo $rank++; ?></td>
                    <td style="padding: 8px;"><?php echo htmlspecialchars($p['username']); ?></td>
                    <td style="padding: 8px;"><?php echo (int)$p['level']; ?></td>
                    <td style="padding: 8px;"><?php echo (int)$p['total_score']; ?></td>
                    <td style="padding: 8px;"><?php echo (int)$p['hunts_completed']; ?></td>
                    <td style="padding: 8px;"><?php echo htmlspecialchars($p['avg_time']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p style="color: var(--text-muted);">No players yet.</p>
        <?php endif; ?>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
