<?php
/**
 * Contrôleur Quiz
 */

class QuizController {
    private $quizModel;
    private $userModel;

    public function __construct() {
        $this->quizModel = new Quiz();
        $this->userModel = new User();
    }

    public function index() {
        $quizzes = $this->quizModel->getAllActive();
        require_once '../app/views/quiz/index.php';
    }

    public function play() {
        $quizKey = $_GET['quiz'] ?? '';
        
        if (empty($quizKey)) {
            header('Location: /quiz');
            exit;
        }

        $quiz = $this->quizModel->findByKey($quizKey);
        
        if (!$quiz) {
            header('Location: /quiz');
            exit;
        }

        $questions = $this->quizModel->getQuestionsWithChoices($quiz['id']);
        
        require_once '../app/views/quiz/play.php';
    }

    public function result() {
        // Traitement des résultats
        require_once '../app/views/quiz/result.php';
    }
}