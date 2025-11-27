<?php
/**
 * API pour vérifier une réponse (anti-triche)
 */

require_once '../config.php';

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSON(['success' => false, 'error' => 'Méthode non autorisée'], 405);
}

// Récupérer les données JSON
$input = json_decode(file_get_contents('php://input'), true);

$questionId = intval($input['question_id'] ?? 0);
$choiceId = intval($input['choice_id'] ?? 0);
$timeSpent = intval($input['time_spent'] ?? 0);

if ($questionId <= 0 || $choiceId <= 0) {
    sendJSON(['success' => false, 'error' => 'Données invalides'], 400);
}

try {
    $pdo = getDB();
    
    // Récupérer la question et vérifier si le choix est correct
    $stmt = $pdo->prepare("
        SELECT 
            q.id,
            q.question_text,
            q.explanation,
            q.points,
            q.time_limit,
            c.id as choice_id,
            c.choice_text,
            c.is_correct
        FROM questions q
        JOIN choices c ON c.question_id = q.id
        WHERE q.id = ? AND c.id = ?
    ");
    $stmt->execute([$questionId, $choiceId]);
    $result = $stmt->fetch();
    
    if (!$result) {
        sendJSON(['success' => false, 'error' => 'Question ou choix invalide'], 404);
    }
    
    $isCorrect = (bool)$result['is_correct'];
    
    // Calculer les points en fonction du temps (pénalité temporelle)
    $basePoints = $result['points'];
    $timePenalty = floor($timeSpent / 3);
    $pointsEarned = $isCorrect ? max(1, $basePoints - $timePenalty) : 0;
    
    // Récupérer l'ID du choix correct pour feedback
    $stmt = $pdo->prepare("
        SELECT id, choice_text 
        FROM choices 
        WHERE question_id = ? AND is_correct = 1
    ");
    $stmt->execute([$questionId]);
    $correctChoice = $stmt->fetch();
    
    sendJSON([
        'success' => true,
        'is_correct' => $isCorrect,
        'points_earned' => $pointsEarned,
        'explanation' => $result['explanation'],
        'correct_choice' => [
            'id' => $correctChoice['id'],
            'text' => $correctChoice['choice_text']
        ]
    ]);
    
} catch (PDOException $e) {
    logError("Erreur lors de la vérification de la réponse", [
        'error' => $e->getMessage(),
        'question_id' => $questionId,
        'choice_id' => $choiceId
    ]);
    sendJSON(['success' => false, 'error' => 'Erreur lors de la vérification'], 500);
}
?>