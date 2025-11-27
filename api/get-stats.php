<?php
/**
 * API pour récupérer les statistiques d'un utilisateur
 */

require_once '../config.php';

// Vérifier que c'est une requête GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJSON(['success' => false, 'error' => 'Méthode non autorisée'], 405);
}

// Vérifier que l'utilisateur est connecté
if (!isLoggedIn()) {
    sendJSON(['success' => false, 'error' => 'Vous devez être connecté'], 401);
}

try {
    $pdo = getDB();
    $userId = getCurrentUserId();
    
    // Statistiques globales de l'utilisateur
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(DISTINCT s.quiz_id) as quizzes_completed,
            COUNT(s.id) as total_attempts,
            ROUND(AVG(s.percentage), 2) as avg_percentage,
            MAX(s.percentage) as best_percentage,
            SUM(s.time_spent) as total_time_spent,
            SUM(s.score) as total_points
        FROM scores s
        WHERE s.user_id = ?
    ");
    $stmt->execute([$userId]);
    $globalStats = $stmt->fetch();
    
    // Statistiques par quiz
    $stmt = $pdo->prepare("
        SELECT 
            q.quiz_key,
            q.title,
            q.icon,
            COUNT(s.id) as attempts,
            ROUND(AVG(s.percentage), 2) as avg_percentage,
            MAX(s.percentage) as best_percentage,
            MIN(s.time_spent) as best_time,
            MAX(s.completed_at) as last_attempt
        FROM scores s
        JOIN quizzes q ON s.quiz_id = q.id
        WHERE s.user_id = ?
        GROUP BY q.id, q.quiz_key, q.title, q.icon
        ORDER BY best_percentage DESC
    ");
    $stmt->execute([$userId]);
    $quizStats = $stmt->fetchAll();
    
    // Historique récent (10 dernières tentatives)
    $stmt = $pdo->prepare("
        SELECT 
            q.title as quiz_title,
            s.score,
            s.total_questions,
            s.percentage,
            s.time_spent,
            s.completed_at
        FROM scores s
        JOIN quizzes q ON s.quiz_id = q.id
        WHERE s.user_id = ?
        ORDER BY s.completed_at DESC
        LIMIT 10
    ");
    $stmt->execute([$userId]);
    $recentHistory = $stmt->fetchAll();
    
    // Progression dans le temps (score moyen par mois)
    $stmt = $pdo->prepare("
        SELECT 
            DATE_FORMAT(s.completed_at, '%Y-%m') as month,
            ROUND(AVG(s.percentage), 2) as avg_percentage,
            COUNT(s.id) as attempts
        FROM scores s
        WHERE s.user_id = ?
        AND s.completed_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(s.completed_at, '%Y-%m')
        ORDER BY month ASC
    ");
    $stmt->execute([$userId]);
    $progression = $stmt->fetchAll();
    
    // Points forts et points faibles (par difficulté)
    $stmt = $pdo->prepare("
        SELECT 
            q.difficulty,
            COUNT(ua.id) as total_answers,
            SUM(CASE WHEN ua.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers,
            ROUND(SUM(CASE WHEN ua.is_correct = 1 THEN 1 ELSE 0 END) * 100.0 / COUNT(ua.id), 2) as success_rate
        FROM user_answers ua
        JOIN scores s ON ua.score_id = s.id
        JOIN questions q ON ua.question_id = q.id
        WHERE s.user_id = ?
        GROUP BY q.difficulty
    ");
    $stmt->execute([$userId]);
    $difficultyStats = $stmt->fetchAll();
    
    sendJSON([
        'success' => true,
        'stats' => [
            'global' => $globalStats,
            'by_quiz' => $quizStats,
            'recent_history' => $recentHistory,
            'progression' => $progression,
            'difficulty_stats' => $difficultyStats
        ]
    ]);
    
} catch (PDOException $e) {
    logError("Erreur lors de la récupération des statistiques", [
        'error' => $e->getMessage(),
        'user_id' => getCurrentUserId()
    ]);
    sendJSON(['success' => false, 'error' => 'Erreur lors de la récupération des statistiques'], 500);
}
?>