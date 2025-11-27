<?php
/**
 * API pour récupérer le classement (leaderboard)
 */

require_once '../config.php';

// Vérifier que c'est une requête GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJSON(['success' => false, 'error' => 'Méthode non autorisée'], 405);
}

$quizKey = sanitizeInput($_GET['quiz'] ?? '');
$limit = intval($_GET['limit'] ?? 10);
$limit = min(max($limit, 1), 100); // Entre 1 et 100

try {
    $pdo = getDB();
    
    if (!empty($quizKey)) {
        // Classement pour un quiz spécifique
        $stmt = $pdo->prepare("
            SELECT 
                u.username,
                s.score,
                s.total_questions,
                s.percentage,
                s.time_spent,
                s.completed_at,
                RANK() OVER (ORDER BY s.percentage DESC, s.time_spent ASC) as rank
            FROM scores s
            JOIN users u ON s.user_id = u.id
            JOIN quizzes q ON s.quiz_id = q.id
            WHERE q.quiz_key = ?
            ORDER BY s.percentage DESC, s.time_spent ASC
            LIMIT ?
        ");
        $stmt->execute([$quizKey, $limit]);
        
    } else {
        // Classement global (meilleur score de chaque utilisateur tous quiz confondus)
        $stmt = $pdo->prepare("
            SELECT 
                u.username,
                AVG(best_scores.percentage) as avg_percentage,
                SUM(best_scores.score) as total_score,
                COUNT(DISTINCT best_scores.quiz_id) as quizzes_completed,
                SUM(best_scores.time_spent) as total_time_spent
            FROM users u
            JOIN (
                SELECT 
                    user_id,
                    quiz_id,
                    MAX(percentage) as percentage,
                    MAX(score) as score,
                    MIN(time_spent) as time_spent
                FROM scores
                GROUP BY user_id, quiz_id
            ) best_scores ON u.id = best_scores.user_id
            GROUP BY u.id, u.username
            ORDER BY avg_percentage DESC, total_score DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
    }
    
    $leaderboard = $stmt->fetchAll();
    
    // Ajouter le rang manuellement pour le classement global
    if (empty($quizKey)) {
        foreach ($leaderboard as $index => &$entry) {
            $entry['rank'] = $index + 1;
            $entry['avg_percentage'] = round($entry['avg_percentage'], 2);
        }
    }
    
    // Si l'utilisateur est connecté, ajouter sa position
    $userPosition = null;
    if (isLoggedIn()) {
        $userId = getCurrentUserId();
        
        if (!empty($quizKey)) {
            $stmt = $pdo->prepare("
                SELECT COUNT(*) + 1 as position
                FROM (
                    SELECT DISTINCT user_id
                    FROM scores s
                    JOIN quizzes q ON s.quiz_id = q.id
                    WHERE q.quiz_key = ?
                    AND (s.percentage > (
                        SELECT MAX(percentage) FROM scores WHERE user_id = ? AND quiz_id = q.id
                    ))
                ) ranked_users
            ");
            $stmt->execute([$quizKey, $userId]);
            
        } else {
            $stmt = $pdo->prepare("
                SELECT 
                    AVG(best_scores.percentage) as avg_percentage,
                    SUM(best_scores.score) as total_score
                FROM (
                    SELECT 
                        quiz_id,
                        MAX(percentage) as percentage,
                        MAX(score) as score
                    FROM scores
                    WHERE user_id = ?
                    GROUP BY quiz_id
                ) best_scores
            ");
            $stmt->execute([$userId]);
            $userStats = $stmt->fetch();
            
            if ($userStats) {
                $stmt = $pdo->prepare("
                    SELECT COUNT(*) + 1 as position
                    FROM (
                        SELECT 
                            u.id,
                            AVG(best_scores.percentage) as avg_percentage,
                            SUM(best_scores.score) as total_score
                        FROM users u
                        JOIN (
                            SELECT 
                                user_id,
                                quiz_id,
                                MAX(percentage) as percentage,
                                MAX(score) as score
                            FROM scores
                            GROUP BY user_id, quiz_id
                        ) best_scores ON u.id = best_scores.user_id
                        GROUP BY u.id
                        HAVING avg_percentage > ? OR (avg_percentage = ? AND total_score > ?)
                    ) ranked_users
                ");
                $stmt->execute([
                    $userStats['avg_percentage'],
                    $userStats['avg_percentage'],
                    $userStats['total_score']
                ]);
            }
        }
        
        $positionData = $stmt->fetch();
        $userPosition = $positionData['position'] ?? null;
    }
    
    sendJSON([
        'success' => true,
        'leaderboard' => $leaderboard,
        'user_position' => $userPosition,
        'total_entries' => count($leaderboard)
    ]);
    
} catch (PDOException $e) {
    logError("Erreur lors de la récupération du classement", [
        'error' => $e->getMessage(),
        'quiz' => $quizKey
    ]);
    sendJSON(['success' => false, 'error' => 'Erreur lors de la récupération du classement'], 500);
}
?>