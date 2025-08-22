<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

session_start();
require_once '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? $_GET['action'] ?? '';

$db = getDB();

try {
    switch ($action) {
        case 'get':
            $huntId = $_GET['id'];
            $stmt = $db->prepare("SELECT h.*, u.username as creator_name FROM hunts h JOIN users u ON h.creator_id = u.id WHERE h.id = ?");
            $stmt->execute([$huntId]);
            $hunt = $stmt->fetch();
            
            if ($hunt) {
                echo json_encode(['success' => true, 'hunt' => $hunt]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Hunt not found']);
            }
            break;
            
        case 'get_clue':
            $huntId = $_GET['hunt_id'];
            $clueNumber = $_GET['clue_number'];
            
            $stmt = $db->prepare("SELECT * FROM clues WHERE hunt_id = ? AND clue_number = ?");
            $stmt->execute([$huntId, $clueNumber]);
            $clue = $stmt->fetch();
            
            if ($clue) {
                // Don't send the answer to the client
                unset($clue['answer']);
                echo json_encode(['success' => true, 'clue' => $clue]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Clue not found']);
            }
            break;
            
        case 'submit_answer':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'message' => 'Please login first']);
                break;
            }
            
            $huntId = $input['hunt_id'];
            $clueNumber = $input['clue_number'];
            $userAnswer = trim(strtolower($input['answer']));
            
            // Get correct answer
            $stmt = $db->prepare("SELECT answer FROM clues WHERE hunt_id = ? AND clue_number = ?");
            $stmt->execute([$huntId, $clueNumber]);
            $clue = $stmt->fetch();
            
            if ($clue) {
                $correctAnswer = trim(strtolower($clue['answer']));
                $isCorrect = $userAnswer === $correctAnswer;
                
                // Log the attempt
                $stmt = $db->prepare("INSERT INTO clue_attempts (attempt_id, clue_id, user_answer, is_correct, time_taken) VALUES (?, ?, ?, ?, ?)");
                // You'd need to track attempt_id properly in a real implementation
                
                echo json_encode(['success' => true, 'correct' => $isCorrect]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Clue not found']);
            }
            break;
            
        case 'get_hint':
            $huntId = $_GET['hunt_id'];
            $clueNumber = $_GET['clue_number'];
            
            $stmt = $db->prepare("SELECT hint FROM clues WHERE hunt_id = ? AND clue_number = ?");
            $stmt->execute([$huntId, $clueNumber]);
            $clue = $stmt->fetch();
            
            if ($clue && $clue['hint']) {
                echo json_encode(['success' => true, 'hint' => $clue['hint']]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Hint not available']);
            }
            break;
            
        case 'complete_hunt':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'message' => 'Please login first']);
                break;
            }
            
            $huntId = $input['hunt_id'];
            $totalTime = $input['total_time'];
            $hintsUsed = $input['hints_used'];
            $userId = $_SESSION['user_id'];
            
            // Calculate score (example scoring system)
            $baseScore = 1000;
            $timeBonus = max(0, 300 - $totalTime); // Bonus for speed
            $hintPenalty = $hintsUsed * 50; // Penalty for hints
            $score = $baseScore + $timeBonus - $hintPenalty;
            $stmt = $db->prepare("SELECT COUNT(*) + 1 as rank FROM leaderboard WHERE hunt_id = ? AND score > ?");
            $stmt->execute([$huntId, $score]);
            $rank = $stmt->fetch()['rank'];
            
            // Insert leaderboard entry
            $stmt = $db->prepare("INSERT INTO leaderboard (user_id, hunt_id, total_time, score, rank_position) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE total_time = ?, score = ?, rank_position = ?");
            $stmt->execute([$userId, $huntId, $totalTime, $score, $rank, $totalTime, $score, $rank]);
            
            // Update user stats
            $stmt = $db->prepare("UPDATE users SET hunts_completed = hunts_completed + 1, total_score = total_score + ? WHERE id = ?");
            $stmt->execute([$score, $userId]);
            
            // Get rank
            $stmt = $db->prepare("SELECT COUNT(*) + 1 as rank FROM leaderboard WHERE hunt_id = ? AND score > ?");
            $stmt->execute([$huntId, $score]);
            $rank = $stmt->fetch()['rank'];
            
            echo json_encode(['success' => true, 'score' => $score, 'rank' => $rank]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>