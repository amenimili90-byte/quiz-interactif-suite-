<?php
/**
 * Modèle Score - CORRIGÉ POUR ORACLE
 */

class Score {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    /**
     * Sauvegarder un score
     */
    public function saveScore($userId, $quizKey, $score, $totalQuestions, $timeSpent, $answers = []) {
        try {
            $this->pdo->beginTransaction();

            // Récupérer l'ID du quiz
            $quizStmt = $this->pdo->prepare("SELECT id FROM quizzes WHERE quiz_key = ?");
            $quizStmt->execute([$quizKey]);
            $quiz = $quizStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$quiz) {
                throw new Exception('Quiz non trouvé');
            }

            $quizId = $quiz['id'];
            $percentage = round(($score / ($totalQuestions * 10)) * 100, 2);

            // Insérer le score principal
            $stmt = $this->pdo->prepare("
                INSERT INTO scores 
                (user_id, quiz_id, score, total_questions, time_spent, percentage, ip_address) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $userId, 
                $quizId, 
                $score, 
                $totalQuestions, 
                $timeSpent, 
                $percentage,
                $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN'
            ]);

            // Récupérer l'ID du score créé
            $stmt = $this->pdo->prepare("
                SELECT id FROM scores 
                WHERE user_id = ? AND quiz_id = ? 
                ORDER BY id DESC 
                FETCH FIRST 1 ROWS ONLY
            ");
            $stmt->execute([$userId, $quizId]);
            $scoreData = $stmt->fetch(PDO::FETCH_ASSOC);
            $scoreId = $scoreData['id'];

            // Insérer les réponses détaillées
            if (!empty($answers)) {
                $answerStmt = $this->pdo->prepare("
                    INSERT INTO user_answers 
                    (score_id, question_id, choice_id, is_correct, time_spent) 
                    VALUES (?, ?, ?, ?, ?)
                ");

                foreach ($answers as $answer) {
                    $answerStmt->execute([
                        $scoreId,
                        $answer['question_id'],
                        $answer['choice_id'] ?? null,
                        $answer['is_correct'],
                        $answer['time_spent']
                    ]);
                }
            }

            $this->pdo->commit();

            // Récupérer les informations de classement
            return $this->getScoreResult($scoreId, $userId, $quizId, $percentage, $timeSpent);

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Erreur saveScore: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtenir le résultat d'un score avec classement
     */
    private function getScoreResult($scoreId, $userId, $quizId, $percentage, $timeSpent) {
        try {
            // Classement
            $rankStmt = $this->pdo->prepare("
                SELECT COUNT(*) + 1 as rank
                FROM scores 
                WHERE quiz_id = ? 
                AND (percentage > ? OR (percentage = ? AND time_spent < ?))
            ");
            $rankStmt->execute([$quizId, $percentage, $percentage, $timeSpent]);
            $rankData = $rankStmt->fetch(PDO::FETCH_ASSOC);

            // Meilleur score
            $bestStmt = $this->pdo->prepare("
                SELECT MAX(percentage) as best_percentage 
                FROM scores 
                WHERE user_id = ? AND quiz_id = ?
            ");
            $bestStmt->execute([$userId, $quizId]);
            $bestData = $bestStmt->fetch(PDO::FETCH_ASSOC);

            return [
                'score_id' => $scoreId,
                'percentage' => $percentage,
                'rank' => $rankData['rank'],
                'best_percentage' => $bestData['best_percentage'] ?? $percentage,
                'is_personal_best' => $percentage >= ($bestData['best_percentage'] ?? 0)
            ];
        } catch (PDOException $e) {
            error_log("Erreur getScoreResult: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtenir le classement pour un quiz spécifique
     */
    public function getLeaderboardByQuiz($quizKey, $limit = 10) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM (
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
                )
                WHERE ROWNUM <= ?
            ");
            $stmt->execute([$quizKey, $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getLeaderboardByQuiz: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtenir le classement global
     */
    public function getGlobalLeaderboard($limit = 10) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM (
                    SELECT 
                        u.username,
                        AVG(s.percentage) as avg_percentage,
                        COUNT(s.id) as total_attempts,
                        MAX(s.completed_at) as last_activity
                    FROM users u
                    LEFT JOIN scores s ON u.id = s.user_id
                    GROUP BY u.id, u.username
                    HAVING COUNT(s.id) > 0
                    ORDER BY avg_percentage DESC
                )
                WHERE ROWNUM <= ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getGlobalLeaderboard: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtenir les statistiques d'un utilisateur
     */
    public function getUserStats($userId) {
        try {
            // Statistiques globales
            $globalStmt = $this->pdo->prepare("
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
            $globalStmt->execute([$userId]);
            $globalStats = $globalStmt->fetch(PDO::FETCH_ASSOC);

            // Statistiques par quiz
            $quizStmt = $this->pdo->prepare("
                SELECT 
                    q.quiz_key,
                    DBMS_LOB.SUBSTR(q.title, 4000, 1) as title,
                    q.icon,
                    COUNT(s.id) as attempts,
                    ROUND(AVG(s.percentage), 2) as avg_percentage,
                    MAX(s.percentage) as best_percentage,
                    MIN(s.time_spent) as best_time
                FROM scores s
                JOIN quizzes q ON s.quiz_id = q.id
                WHERE s.user_id = ?
                GROUP BY q.id, q.quiz_key, q.title, q.icon
                ORDER BY best_percentage DESC
            ");
            $quizStmt->execute([$userId]);
            $quizStats = $quizStmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'global' => $globalStats,
                'by_quiz' => $quizStats
            ];
        } catch (PDOException $e) {
            error_log("Erreur getUserStats: " . $e->getMessage());
            return ['global' => [], 'by_quiz' => []];
        }
    }

    /**
     * Compter le nombre total de scores
     */
    public function getTotalCount() {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM scores");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            error_log("Erreur getTotalCount: " . $e->getMessage());
            return 0;
        }
    }
}
?>