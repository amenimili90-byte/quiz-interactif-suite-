<?php
/**
 * Modèle Score
 */

class Score {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function saveScore($userId, $quizKey, $score, $totalQuestions, $timeSpent, $answers = []) {
        try {
            $this->pdo->beginTransaction();

            // Récupérer l'ID du quiz
            $quizStmt = $this->pdo->prepare("SELECT id FROM quizzes WHERE quiz_key = ?");
            $quizStmt->execute([$quizKey]);
            $quiz = $quizStmt->fetch();
            
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

            $scoreId = $this->pdo->lastInsertId();

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
                        $answer['choice_id'],
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
            throw $e;
        }
    }

    private function getScoreResult($scoreId, $userId, $quizId, $percentage, $timeSpent) {
        // Classement
        $rankStmt = $this->pdo->prepare("
            SELECT COUNT(*) + 1 as rank
            FROM scores 
            WHERE quiz_id = ? 
            AND (percentage > ? OR (percentage = ? AND time_spent < ?))
        ");
        $rankStmt->execute([$quizId, $percentage, $percentage, $timeSpent]);
        $rankData = $rankStmt->fetch();

        // Meilleur score
        $bestStmt = $this->pdo->prepare("
            SELECT MAX(percentage) as best_percentage 
            FROM scores 
            WHERE user_id = ? AND quiz_id = ?
        ");
        $bestStmt->execute([$userId, $quizId]);
        $bestData = $bestStmt->fetch();

        return [
            'score_id' => $scoreId,
            'percentage' => $percentage,
            'rank' => $rankData['rank'],
            'best_percentage' => $bestData['best_percentage'] ?? $percentage,
            'is_personal_best' => $percentage >= ($bestData['best_percentage'] ?? 0)
        ];
    }

    public function getLeaderboardByQuiz($quizKey, $limit = 10) {
        $stmt = $this->pdo->prepare("
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
        return $stmt->fetchAll();
    }

    public function getGlobalLeaderboard($limit = 10) {
        $stmt = $this->pdo->prepare("
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
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getUserPosition($userId, $quizKey = '') {
        if ($quizKey) {
            $stmt = $this->pdo->prepare("
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
            // Logique pour le classement global
            return null; // Simplifié pour cet exemple
        }
        
        $result = $stmt->fetch();
        return $result['position'] ?? null;
    }

    public function getUserStats($userId) {
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
        $globalStats = $globalStmt->fetch();

        // Statistiques par quiz
        $quizStmt = $this->pdo->prepare("
            SELECT 
                q.quiz_key,
                q.title,
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
        $quizStats = $quizStmt->fetchAll();

        return [
            'global' => $globalStats,
            'by_quiz' => $quizStats
        ];
    }

    public function getTotalCount() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM scores");
        return $stmt->fetchColumn();
    }
}