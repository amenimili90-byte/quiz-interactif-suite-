<?php
/**
 * Modèle Question - CORRIGÉ POUR ORACLE
 */

class Question {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    /**
     * Récupérer toutes les questions d'un quiz
     */
    public function getByQuizId($quizId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, quiz_id,
                       DBMS_LOB.SUBSTR(question_text, 4000, 1) as question_text,
                       question_type, time_limit,
                       DBMS_LOB.SUBSTR(explanation, 4000, 1) as explanation,
                       difficulty, points, display_order, active
                FROM questions 
                WHERE quiz_id = ? AND active = 1
                ORDER BY display_order, id
            ");
            $stmt->execute([$quizId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getByQuizId: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Trouver une question par ID
     */
    public function findById($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, quiz_id,
                       DBMS_LOB.SUBSTR(question_text, 4000, 1) as question_text,
                       question_type, time_limit,
                       DBMS_LOB.SUBSTR(explanation, 4000, 1) as explanation,
                       difficulty, points, display_order, active
                FROM questions 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur findById: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Créer une nouvelle question
     */
    public function create($data) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO questions 
                (quiz_id, question_text, question_type, time_limit, explanation, difficulty, points, display_order) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $data['quiz_id'],
                $data['question_text'],
                $data['question_type'],
                $data['time_limit'] ?? 30,
                $data['explanation'] ?? '',
                $data['difficulty'] ?? 'moyen',
                $data['points'] ?? 10,
                $data['display_order'] ?? 0
            ]);

            if ($result) {
                // Récupérer l'ID de la question créée
                $stmt = $this->pdo->prepare("
                    SELECT id FROM questions 
                    WHERE quiz_id = ? 
                    AND DBMS_LOB.COMPARE(question_text, ?) = 0
                    ORDER BY id DESC
                    FETCH FIRST 1 ROWS ONLY
                ");
                $stmt->execute([$data['quiz_id'], $data['question_text']]);
                $question = $stmt->fetch(PDO::FETCH_ASSOC);
                return $question ? $question['id'] : null;
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Erreur create question: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Ajouter un choix de réponse
     */
    public function addChoice($questionId, $choiceText, $isCorrect = false) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO choices (question_id, choice_text, is_correct) 
                VALUES (?, ?, ?)
            ");
            return $stmt->execute([$questionId, $choiceText, $isCorrect ? 1 : 0]);
        } catch (PDOException $e) {
            error_log("Erreur addChoice: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer tous les choix d'une question
     */
    public function getChoices($questionId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id,
                       DBMS_LOB.SUBSTR(choice_text, 4000, 1) as choice_text,
                       is_correct, display_order
                FROM choices 
                WHERE question_id = ? 
                ORDER BY display_order, id
            ");
            $stmt->execute([$questionId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getChoices: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer le choix correct
     */
    public function getCorrectChoice($questionId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id,
                       DBMS_LOB.SUBSTR(choice_text, 4000, 1) as choice_text,
                       is_correct
                FROM choices 
                WHERE question_id = ? AND is_correct = 1
            ");
            $stmt->execute([$questionId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getCorrectChoice: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Vérifier si une réponse est correcte
     */
    public function verifyAnswer($questionId, $choiceId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT is_correct FROM choices 
                WHERE id = ? AND question_id = ?
            ");
            $stmt->execute([$choiceId, $questionId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ? (bool)$result['is_correct'] : false;
        } catch (PDOException $e) {
            error_log("Erreur verifyAnswer: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Compter le nombre total de questions
     */
    public function getTotalCount() {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM questions");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            error_log("Erreur getTotalCount: " . $e->getMessage());
            return 0;
        }
    }
}
?>