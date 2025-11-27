<?php
/**
 * API pour sauvegarder le score d'un quiz
 */

require_once '../config.php';

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSON(['success' => false, 'error' => 'Méthode non autorisée'], 405);
}

// Vérifier que l'utilisateur est connecté
if (!isLoggedIn()) {
    sendJSON(['success' => false, 'error' => 'Vous devez être connecté'], 401);
}

// Récupérer les données JSON
$input = json_decode(file_get_contents('php://input'), true);

$quizKey = sanitizeInput($input['quiz_key'] ?? '');
$score = intval($input['score'] ?? 0);
$totalQuestions = intval($input['total_questions'] ?? 0);
$timeSpent = intval($input['time_spent'] ?? 0);
$answers = $input['answers'] ?? [];

if (empty($quizKey) || $totalQuestions <= 0) {
    sendJSON(['success' => false, 'error' => 'Données invalides'], 400);
}

try {
    $pdo = getDB();
    
    // Récupérer l'ID du quiz
    $stmt = $pdo->prepare("SELECT id FROM quizzes WHERE quiz_key = ?");
    $stmt->execute([$quizKey]);
    $quiz = $stmt->fetch();
    
    if (!$quiz) {
        sendJSON(['success' => false, 'error' => 'Quiz non trouvé'], 404);
    }
    
    $quizId = $quiz['id'];
    $userId = getCurrentUserId();
    $percentage = round(($score / ($totalQuestions * 10)) * 100, 2);
    $ipAddress = getClientIP();
    
    // Démarrer une transaction
    $pdo->beginTransaction();
    
    try {
        // Insérer le score principal
        $stmt = $pdo->prepare("
            INSERT INTO scores (user_id, quiz_id, score, total_questions, time_spent, percentage, ip_address)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$userId, $quizId, $score, $totalQuestions, $timeSpent, $percentage, $ipAddress]);
        $scoreId = $pdo->lastInsertId();
        
        // Insérer les réponses détaillées
        if (!empty($answers) && is_array($answers)) {
            $stmt = $pdo->prepare("
                INSERT INTO user_answers (score_id, question_id, choice_id, is_correct, time_spent)
                VALUES (?, ?, ?, ?, ?)
            ");
            
            foreach ($answers as $answer) {
                $stmt->execute([
                    $scoreId,
                    intval($answer['question_id']),
                    intval($answer['choice_id']),
                    intval($answer['is_correct']),
                    intval($answer['time_spent'])
                ]);
            }
        }
        
        // Valider la transaction
        $pdo->commit();
        
        // Récupérer le rang de l'utilisateur pour ce quiz
        $stmt = $pdo->prepare("
            SELECT COUNT(*) + 1 as rank
            FROM scores
            WHERE quiz_id = ? 
            AND (percentage > ? OR (percentage = ? AND time_spent < ?))
        ");
        $stmt->execute([$quizId, $percentage, $percentage, $timeSpent]);
        $rankData = $stmt->fetch();
        $rank = $rankData['rank'];
        
        // Récupérer le meilleur score de l'utilisateur pour ce quiz
        $stmt = $pdo->prepare("
            SELECT MAX(percentage) as best_percentage
            FROM scores
            WHERE user_id = ? AND quiz_id = ?
        ");
        $stmt->execute([$userId, $quizId]);
        $bestData = $stmt->fetch();
        $bestPercentage = $bestData['best_percentage'];
        
        sendJSON([
            'success' => true,
            'message' => 'Score enregistré avec succès',
            'data' => [
                'score_id' => $scoreId,
                'score' => $score,
                'percentage' => $percentage,
                'rank' => $rank,
                'best_percentage' => $bestPercentage,
                'is_personal_best' => $percentage >= $bestPercentage
            ]
        ], 201);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
    
} catch (PDOException $e) {
    logError("Erreur lors de la sauvegarde du score", [
        'error' => $e->getMessage(),
        'user_id' => getCurrentUserId(),
        'quiz' => $quizKey
    ]);
    sendJSON(['success' => false, 'error' => 'Erreur lors de la sauvegarde du score'], 500);
}
?>