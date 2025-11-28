<?php
/**
 * Modèle Question
 */

class Question {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function getByQuizId($quizId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM questions 
            WHERE quiz_id = ? 
            ORDER BY display_order, id
        ");
        $stmt->execute([$quizId]);
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM questions WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO questions 
            (quiz_id, question_text, question_type, time_limit, explanation, difficulty, points, display_order) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['quiz_id'],
            $data['question_text'],
            $data['question_type'],
            $data['time_limit'] ?? 30,
            $data['explanation'] ?? '',
            $data['difficulty'] ?? 'moyen',
            $data['points'] ?? 10,
            $data['display_order'] ?? 0
        ]);

        return $this->pdo->lastInsertId();
    }

    public function addChoice($questionId, $choiceText, $isCorrect = false) {
        $stmt = $this->pdo->prepare("
            INSERT INTO choices (question_id, choice_text, is_correct) 
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([$questionId, $choiceText, $isCorrect ? 1 : 0]);
    }

    public function getChoices($questionId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM choices 
            WHERE question_id = ? 
            ORDER BY display_order, id
        ");
        $stmt->execute([$questionId]);
        return $stmt->fetchAll();
    }

    public function getCorrectChoice($questionId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM choices 
            WHERE question_id = ? AND is_correct = 1
        ");
        $stmt->execute([$questionId]);
        return $stmt->fetch();
    }

    public function verifyAnswer($questionId, $choiceId) {
        $stmt = $this->pdo->prepare("
            SELECT is_correct FROM choices 
            WHERE id = ? AND question_id = ?
        ");
        $stmt->execute([$choiceId, $questionId]);
        $result = $stmt->fetch();
        
        return $result ? (bool)$result['is_correct'] : false;
    }

    public function getTotalCount() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM questions");
        return $stmt->fetchColumn();
    }
}