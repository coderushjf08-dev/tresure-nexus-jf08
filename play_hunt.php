<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$page_title = "Play Hunt";

if (!isset($_GET['hunt_id'])) {
    header("Location: index.php");
    exit;
}

$hunt_id = (int) $_GET['hunt_id'];
$user_id = (int) $_SESSION['user_id'];
$db = getDB();

// Fetch hunt
$stmt = $db->prepare("SELECT * FROM hunts WHERE id = ?");
$stmt->execute([$hunt_id]);
$hunt = $stmt->fetch();
if (!$hunt) {
    header("Location: index.php");
    exit;
}

// Fetch clues ordered
$stmt = $db->prepare("SELECT * FROM clues WHERE hunt_id = ? ORDER BY clue_number ASC");
$stmt->execute([$hunt_id]);
$clues = $stmt->fetchAll();
$total_clues = count($clues);
if ($total_clues === 0) {
    $error = "This hunt has no clues yet.";
}

// Helper: detect if clues table has 'points'
function clues_has_points(PDO $db) {
    try {
        $stmt = $db->query("DESCRIBE `clues`");
        foreach ($stmt->fetchAll() as $c) {
            if (strcasecmp($c['Field'], 'points') === 0) return true;
        }
    } catch (Exception $e) {}
    return false;
}
$has_points = clues_has_points($db);

// Init session state
if (!isset($_SESSION['progress'])) { $_SESSION['progress'] = []; }
if (!isset($_SESSION['start_time'])) { $_SESSION['start_time'] = []; }

if (!isset($_SESSION['progress'][$hunt_id])) {
    $_SESSION['progress'][$hunt_id] = 0;
    $_SESSION['start_time'][$hunt_id] = time();
}

$current_index = $_SESSION['progress'][$hunt_id]; // 0-based

// Timer: use hunts.estimated_duration (minutes)
$time_limit_sec = max(1, (int)($hunt['estimated_duration'] ?? 5)) * 60;
$elapsed = time() - (int)$_SESSION['start_time'][$hunt_id];
$remaining = max(0, $time_limit_sec - $elapsed);

// Handle submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($clues[$current_index])) {
    $given_answer = strtolower(trim($_POST['answer'] ?? ''));
    $correct_answer = strtolower(trim($clues[$current_index]['answer']));

    if ($remaining <= 0) {
        $error = "⏳ Time's up for this hunt!";
    } elseif ($given_answer === $correct_answer) {
        // Points for this clue (if present)
        $award = 10;
        if ($has_points && isset($clues[$current_index]['points']) && (int)$clues[$current_index]['points'] > 0) {
            $award = (int)$clues[$current_index]['points'];
        }

        // Advance
        $_SESSION['progress'][$hunt_id]++;

        // Update per-hunt score
        $stmt = $db->prepare("
            INSERT INTO scores (user_id, hunt_id, score, time_taken)
            VALUES (?, ?, ?, 0)
            ON DUPLICATE KEY UPDATE score = score + VALUES(score)
        ");
        $stmt->execute([$user_id, $hunt_id, $award]);

        // Update global score
        $stmt = $db->prepare("UPDATE users SET total_score = total_score + ? WHERE id = ?");
        $stmt->execute([$award, $user_id]);

        if ($_SESSION['progress'][$hunt_id] >= $total_clues) {
            // Finished
            $finished = true;
            $time_taken = $elapsed;

            // Update users aggregate stats
            $stmt = $db->prepare("SELECT hunts_completed, avg_time FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $u = $stmt->fetch();
            $prev_completed = (int)($u['hunts_completed'] ?? 0);
            $prev_avg = 0;
            if (!empty($u['avg_time']) && preg_match('/^(\d{2}):(\d{2})$/', $u['avg_time'], $m)) {
                $prev_avg = ((int)$m[1]) * 60 + (int)$m[2];
            }
            $new_completed = $prev_completed + 1;
            $new_avg_seconds = $new_completed > 0 ? (int) round((($prev_avg * $prev_completed) + $time_taken) / $new_completed) : $time_taken;
            $new_avg = sprintf('%02d:%02d', intdiv($new_avg_seconds, 60), $new_avg_seconds % 60);

            $stmt = $db->prepare("UPDATE users SET hunts_completed = ?, avg_time = ? WHERE id = ?");
            $stmt->execute([$new_completed, $new_avg, $user_id]);

            // Save time on scores
            $stmt = $db->prepare("UPDATE scores SET time_taken = ? WHERE user_id = ? AND hunt_id = ?");
            $stmt->execute([$time_taken, $user_id, $hunt_id]);

            // Increment hunt players
            $db->prepare("UPDATE hunts SET total_players = total_players + 1 WHERE id = ?")->execute([$hunt_id]);

            // Clear session for replay
            unset($_SESSION['progress'][$hunt_id], $_SESSION['start_time'][$hunt_id]);
        } else {
            $success = "✅ Correct! Next clue unlocked.";
            $current_index = $_SESSION['progress'][$hunt_id];
        }
    } else {
        $error = "❌ Wrong answer. Try again!";
    }
}

// Per-hunt leaderboard
$lb_stmt = $db->prepare("
    SELECT u.username, s.score, s.time_taken
    FROM scores s
    JOIN users u ON u.id = s.user_id
    WHERE s.hunt_id = ?
    ORDER BY s.score DESC, s.time_taken ASC, u.username ASC
    LIMIT 10
");
$lb_stmt->execute([$hunt_id]);
$leaderboard = $lb_stmt->fetchAll();

include 'includes/header.php';
?>
<section style="min-height: 100vh; padding: 100px 20px 20px;">
    <div class="glass-card" style="max-width: 900px; margin: auto; padding: 40px;">
        <h1 class="text-gradient-cyber" style="font-size: 2rem; margin-bottom: 8px;">🎯 <?php echo htmlspecialchars($hunt['title']); ?></h1>
        <p style="color: var(--text-muted); margin-bottom: 24px;"><?php echo htmlspecialchars($hunt['description']); ?></p>

        <div style="margin-bottom: 20px; font-size: 1.1rem;">⏳ Time Left: <strong id="timer"><?php echo gmdate("i:s", $remaining); ?></strong></div>

        <?php if (!empty($error)): ?>
            <div style="padding: 12px; background: var(--glass-bg); border: 1px solid var(--neon-red); color: var(--neon-red); margin-bottom: 20px;"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div style="padding: 12px; background: var(--glass-bg); border: 1px solid var(--neon-green); color: var(--neon-green); margin-bottom: 20px;"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if (!empty($finished)): ?>
            <div style="text-align: center; padding: 30px;">
                <h2 class="text-gradient-cyber" style="font-size: 2rem;">🏆 Hunt Completed!</h2>
                <p style="color: var(--text-muted); margin-top: 12px;">You’ve solved all clues. Score saved.</p>
                <a href="browse_hunts.php" class="cyber-btn hologram" style="margin-top: 16px; display: inline-block; padding: 12px 18px;">🔙 Back to Hunts</a>
            </div>
        <?php elseif ($total_clues > 0 && isset($clues[$current_index])): ?>
            <div style="margin-bottom: 24px;">
                <h3 style="margin-bottom: 12px;">📜 Clue #<?php echo (int)$clues[$current_index]['clue_number']; ?></h3>
                <p style="margin-bottom: 12px;"><?php echo nl2br(htmlspecialchars($clues[$current_index]['content'])); ?></p>
                <?php if (!empty($clues[$current_index]['content_file'])): ?>
                    <?php $file = $clues[$current_index]['content_file']; $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION)); ?>
                    <?php if (in_array($ext, ['jpg','jpeg','png','gif','webp'])): ?>
                        <img src="<?php echo htmlspecialchars($file); ?>" alt="Clue Image" style="max-width: 100%; border-radius: 8px; margin-bottom: 16px;">
                    <?php elseif (in_array($ext, ['mp3','wav','ogg'])): ?>
                        <audio controls style="margin-bottom: 16px; width: 100%;">
                            <source src="<?php echo htmlspecialchars($file); ?>" type="audio/<?php echo $ext; ?>">
                        </audio>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <form method="POST" style="display: flex; gap: 12px; align-items: center;">
                <input type="text" name="answer" required class="cyber-input" placeholder="Enter your answer" style="flex: 1;">
                <button type="submit" class="cyber-btn hologram" style="padding: 12px 20px;">Submit</button>
            </form>

            <p style="margin-top: 16px; color: var(--text-muted);">Progress: <?php echo (int)$current_index; ?> / <?php echo (int)$total_clues; ?> clues solved</p>
        <?php endif; ?>

        <div style="margin-top: 32px;">
            <h3 class="text-gradient-cyber" style="margin-bottom: 12px;">🏅 Leaderboard (This Hunt)</h3>
            <?php if (!empty($leaderboard)): ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align:left; border-bottom: 1px solid var(--glass-border);">
                            <th style="padding: 8px;">Rank</th>
                            <th style="padding: 8px;">Player</th>
                            <th style="padding: 8px;">Score</th>
                            <th style="padding: 8px;">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $rank = 1; foreach ($leaderboard as $row): ?>
                        <tr style="border-bottom: 1px solid var(--glass-border);">
                            <td style="padding: 8px;"><?php echo $rank++; ?></td>
                            <td style="padding: 8px;"><?php echo htmlspecialchars($row['username']); ?></td>
                            <td style="padding: 8px;"><?php echo (int)$row['score']; ?></td>
                            <td style="padding: 8px;"><?php $t = (int)$row['time_taken']; echo $t > 0 ? sprintf('%02d:%02d', intdiv($t,60), $t%60) : '—'; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: var(--text-muted);">No scores yet. Be the first!</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
let remaining = <?php echo (int)$remaining; ?>;
const timerElem = document.getElementById("timer");
function updateTimer() {
    if (!timerElem) return;
    if (remaining <= 0) {
        timerElem.textContent = "00:00";
        alert("⏳ Time is up!");
        window.location.reload();
        return;
    }
    const mins = Math.floor(remaining / 60);
    const secs = remaining % 60;
    timerElem.textContent = String(mins).padStart(2, '0') + ":" + String(secs).padStart(2, '0');
    remaining--;
}
setInterval(updateTimer, 1000);
</script>

<?php include 'includes/footer.php'; ?>
