<?php
/**
 * Contrôleur des scores et classements - CORRIGÉ
 */

class ScoreController {
    private $scoreModel;
    private $quizModel;

    public function __construct() {
        $this->scoreModel = new Score();
        $this->quizModel = new Quiz();
        
        // Ne démarrer la session que si elle n'est pas déjà active
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
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
            // Calculer la position de l'utilisateur (à implémenter dans Score.php)
            $userPosition = null; // Simplified for now
        }

        $quizzes = $this->quizModel->getAllActive();
        
        // Utiliser le chemin absolu
        require_once __DIR__ . '/../views/score/leaderboard.php';
    }

    public function stats() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté pour voir vos statistiques';
            header('Location: ' . BASE_URL . '?page=login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $stats = $this->scoreModel->getUserStats($userId);
        
        // Utiliser le chemin absolu
        require_once __DIR__ . '/../views/score/stats.php';
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
        
        try {
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
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
?>