<?php
/**
 * Contrôleur d'administration
 */

class AdminController {
    private $quizModel;
    private $questionModel;
    private $userModel;

    public function __construct() {
        $this->quizModel = new Quiz();
        $this->questionModel = new Question();
        $this->userModel = new User();
        session_start();
        $this->checkAdmin();
    }

    private function checkAdmin() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /login');
            exit;
        }
    }

    public function dashboard() {
        $stats = [
            'total_users' => $this->userModel->getTotalCount(),
            'total_quizzes' => $this->quizModel->getTotalCount(),
            'total_questions' => $this->questionModel->getTotalCount(),
            'total_scores' => $this->scoreModel->getTotalCount()
        ];
        
        require_once '../app/views/admin/dashboard.php';
    }

    public function quizzes() {
        $quizzes = $this->quizModel->getAll();
        require_once '../app/views/admin/quizzes.php';
    }

    public function questions() {
        $quizId = $_GET['quiz_id'] ?? null;
        $questions = [];
        
        if ($quizId) {
            $questions = $this->questionModel->getByQuizId($quizId);
        }
        
        $quizzes = $this->quizModel->getAllActive();
        require_once '../app/views/admin/questions.php';
    }

    public function createQuiz() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/quizzes');
            exit;
        }

        $data = [
            'quiz_key' => $_POST['quiz_key'],
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'icon' => $_POST['icon']
        ];

        if ($this->quizModel->create($data)) {
            $_SESSION['success'] = 'Quiz créé avec succès';
        } else {
            $_SESSION['error'] = 'Erreur lors de la création du quiz';
        }

        header('Location: /admin/quizzes');
        exit;
    }

    public function createQuestion() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/questions');
            exit;
        }

        $data = [
            'quiz_id' => $_POST['quiz_id'],
            'question_text' => $_POST['question_text'],
            'question_type' => $_POST['question_type'],
            'time_limit' => $_POST['time_limit'],
            'explanation' => $_POST['explanation'],
            'difficulty' => $_POST['difficulty'],
            'points' => $_POST['points']
        ];

        $questionId = $this->questionModel->create($data);

        if ($questionId) {
            // Ajouter les choix
            $choices = $_POST['choices'] ?? [];
            foreach ($choices as $choice) {
                $this->questionModel->addChoice($questionId, $choice['text'], $choice['is_correct']);
            }
            
            $_SESSION['success'] = 'Question créée avec succès';
        } else {
            $_SESSION['error'] = 'Erreur lors de la création de la question';
        }

        header('Location: /admin/questions?quiz_id=' . $_POST['quiz_id']);
        exit;
    }
}