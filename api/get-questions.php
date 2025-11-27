<?php
/**
 * API pour récupérer les questions d'un quiz
 */

require_once '../config.php';

// Vérifier que c'est une requête GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJSON(['success' => false, 'error' => 'Méthode non autorisée'], 405);
}

$quizKey = sanitizeInput($_GET['quiz'] ?? '');

if (empty($quizKey)) {
    sendJSON(['success' => false, 'error' => 'ID du quiz requis'], 400);
}

try {
    $pdo = getDB();
    
    // Récupérer les informations du quiz
    $stmt = $pdo->prepare("
        SELECT id, title, description, icon 
        FROM quizzes 
        WHERE quiz_key = ? AND active = 1
    ");
    $stmt->execute([$quizKey]);
    $quiz = $stmt->fetch();
    
    if (!$quiz) {
        sendJSON(['success' => false, 'error' => 'Quiz non trouvé'], 404);
    }
    
    // Récupérer les questions du quiz avec leurs choix
    $stmt = $pdo->prepare("
        SELECT 
            q.id,
            q.question_text,
            q.question_type,
            q.time_limit,
            q.explanation,
            q.difficulty,
            q.points,
            q.display_order
        FROM questions q
        WHERE q.quiz_id = ? AND q.active = 1
        ORDER BY q.display_order ASC, q.id ASC
    ");
    $stmt->execute([$quiz['id']]);
    $questions = $stmt->fetchAll();
    
    // Pour chaque question, récupérer ses choix
    foreach ($questions as &$question) {
        $stmt = $pdo->prepare("
            SELECT 
                id,
                choice_text,
                display_order
            FROM choices
            WHERE question_id = ?
            ORDER BY display_order ASC, id ASC
        ");
        $stmt->execute([$question['id']]);
        $choices = $stmt->fetchAll();
        
        // Formater les choix (sans révéler la bonne réponse)
        $question['choices'] = array_map(function($choice) {
            return [
                'id' => $choice['id'],
                'text' => $choice['choice_text']
            ];
        }, $choices);
        
        // Supprimer les données sensibles
        unset($question['explanation']); // L'explication sera fournie après validation
    }
    
    sendJSON([
        'success' => true,
        'quiz' => [
            'id' => $quiz['id'],
            'key' => $quizKey,
            'title' => $quiz['title'],
            'description' => $quiz['description'],
            'icon' => $quiz['icon'],
            'questions' => $questions
        ]
    ]);
    
} catch (PDOException $e) {
    logError("Erreur lors de la récupération des questions", [
        'error' => $e->getMessage(),
        'quiz' => $quizKey
    ]);
    sendJSON(['success' => false, 'error' => 'Erreur lors de la récupération des questions'], 500);
}
?>