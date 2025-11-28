<?php
/**
 * Contrôleur des scores et classements
 */

class ScoreController {
    private $scoreModel;
    private $quizModel;

    public function __construct() {
        $this->scoreModel = new Score();
        $this->quizModel = new Quiz();
        session_start();
    }

    public function leaderboard() {
        $quizKey = $_GET['quiz'] ?? '';
        $leaderboard = [];
        $userPosition = null;

        if ($quizKey) {
            $leaderboard = $this->scoreModel->getLeaderboardByQuiz($quizKey, 10);
        } else {
            $leaderboard = $this->scoreModel->getGlobalLeaderboard(10);
        }

        if (isset($_SESSION['user_id'])) {
            $userPosition = $this->scoreModel->getUserPosition($_SESSION['user_id'], $quizKey);
        }

        $quizzes = $this->quizModel->getAllActive();
        
        require_once '../app/views/score/leaderboard.php';
    }

    public function stats() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $stats = $this->scoreModel->getUserStats($userId);
        
        require_once '../app/views/score/stats.php';
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Non authentifié']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        
        $result = $this->scoreModel->saveScore(
            $_SESSION['user_id'],
            $input['quiz_key'],
            $input['score'],
            $input['total_questions'],
            $input['time_spent'],
            $input['answers'] ?? []
        );

        if ($result) {
            echo json_encode(['success' => true, 'data' => $result]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erreur sauvegarde']);
        }
    }
}