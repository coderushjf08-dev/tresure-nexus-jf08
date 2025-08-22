<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$page_title = "Add Clues";

if (!isset($_GET['hunt_id'])) {
    header("Location: create_hunt.php");
    exit;
}

$hunt_id = (int) $_GET['hunt_id'];
$db = getDB();

function table_has_column(PDO $db, $table, $column) {
    try {
        $stmt = $db->query("DESCRIBE `$table`");
        $cols = $stmt->fetchAll();
        foreach ($cols as $c) {
            if (strcasecmp($c['Field'], $column) === 0) return true;
        }
    } catch (Exception $e) {}
    return false;
}
$clues_has_points = table_has_column($db, 'clues', 'points');
$clues_title_required = false;
try {
    $stmt = $db->query("DESCRIBE `clues`");
    foreach ($stmt->fetchAll() as $c) {
        if (strcasecmp($c['Field'], 'title') === 0 && stripos($c['Null'], 'NO') !== false) {
            $clues_title_required = true;
        }
    }
} catch (Exception $e) {}

// Handle clue submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clue_text = trim($_POST['clue_text'] ?? '');
    $answer    = trim($_POST['answer'] ?? '');
    $order_num = isset($_POST['order_num']) ? (int) $_POST['order_num'] : 0;
    $points    = isset($_POST['points']) ? (int) $_POST['points'] : 10;

    if ($clue_text === '' || $answer === '' || $order_num <= 0) {
        $error = "All required fields must be filled and clue order must be a positive number!";
    } else {
        // Optional upload
        $file_path = null;
        if (!empty($_FILES['file']['name'])) {
            $upload_dir = __DIR__ . "/uploads/";
            if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }
            $safe_name = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($_FILES['file']['name']));
            $file_name = time() . "_" . $safe_name;
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
                $file_path = "uploads/" . $file_name;
            } else {
                $error = "File upload failed.";
            }
        }

        if (!isset($error)) {
            $type = 'text';
            $title = $clues_title_required ? ("Clue #" . $order_num) : null;

            if ($clues_has_points) {
                $stmt = $db->prepare("
                    INSERT INTO clues (hunt_id, clue_number, type, title, content, content_file, answer, points)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$hunt_id, $order_num, $type, $title ?? '', $clue_text, $file_path, $answer, $points]);
            } else {
                $stmt = $db->prepare("
                    INSERT INTO clues (hunt_id, clue_number, type, title, content, content_file, answer)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$hunt_id, $order_num, $type, $title ?? '', $clue_text, $file_path, $answer]);
            }
            $success = "✅ Clue added successfully!";
        }
    }
}

// Fetch existing clues
$stmt = $db->prepare("SELECT * FROM clues WHERE hunt_id = ? ORDER BY clue_number ASC");
$stmt->execute([$hunt_id]);
$clues = $stmt->fetchAll();

include 'includes/header.php';
?>
<section style="min-height: 100vh; padding: 100px 20px 20px;">
    <div class="glass-card" style="max-width: 800px; margin: auto; padding: 40px;">
        <h1 class="text-gradient-cyber" style="font-size: 2rem; margin-bottom: 16px;">➕ Add Clues</h1>
        <p style="color: var(--text-muted); margin-bottom: 24px;">Attach clues step by step to your hunt.</p>

        <?php if (!empty($error)): ?>
            <div style="padding: 12px; background: var(--glass-bg); border: 1px solid var(--neon-red); color: var(--neon-red); margin-bottom: 20px;">❌ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div style="padding: 12px; background: var(--glass-bg); border: 1px solid var(--neon-green); color: var(--neon-green); margin-bottom: 20px;"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 20px;">
            <div>
                <label style="color: var(--text-muted);">Clue Text:</label>
                <textarea name="clue_text" required rows="3" class="cyber-input" placeholder="Write your clue"></textarea>
            </div>
            <div>
                <label style="color: var(--text-muted);">Answer (keyword/solution):</label>
                <input type="text" name="answer" required class="cyber-input" placeholder="Enter correct answer">
            </div>
            <div>
                <label style="color: var(--text-muted);">Clue Order:</label>
                <input type="number" name="order_num" required min="1" class="cyber-input" placeholder="1, 2, 3...">
            </div>
            <?php if ($clues_has_points): ?>
            <div>
                <label style="color: var(--text-muted);">Points for this clue:</label>
                <input type="number" name="points" min="1" value="10" class="cyber-input" placeholder="e.g., 10">
            </div>
            <?php endif; ?>
            <div>
                <label style="color: var(--text-muted);">Attach File (optional - image/audio):</label>
                <input type="file" name="file" accept="image/*,audio/*" class="cyber-input">
            </div>
            <button type="submit" class="cyber-btn hologram" style="padding: 14px;">💾 Save Clue</button>
        </form>

        <?php if (!empty($clues)): ?>
            <h2 class="text-gradient-cyber" style="margin-top: 40px;">📜 Existing Clues</h2>
            <ul style="margin-top: 20px; list-style: none; padding: 0;">
                <?php foreach ($clues as $clue): ?>
                    <li style="margin-bottom: 16px; padding: 12px; background: var(--glass-bg); border-radius: 8px;">
                        <strong>#<?php echo (int)$clue['clue_number']; ?>:</strong>
                        <?php echo htmlspecialchars($clue['content']); ?>
                        <?php if (!empty($clue['content_file'])): ?>
                            <br><a href="<?php echo htmlspecialchars($clue['content_file']); ?>" target="_blank" class="neon-blue">📂 View File</a>
                        <?php endif; ?>
                        <?php if (isset($clue['points'])): ?>
                            <div style="color: var(--text-muted); margin-top:4px;">Points: <?php echo (int)$clue['points']; ?></div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div style="margin-top: 30px; text-align: center;">
            <a href="index.php" class="cyber-btn" style="padding: 14px 20px;">✅ Finish Hunt</a>
        </div>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
