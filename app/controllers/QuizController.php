<?php
/**
 * Contrôleur Quiz - VERSION FINALE CORRIGÉE
 */

class QuizController {
    private $quizModel;
    private $questionModel;

    public function __construct() {
    $this->quizModel = new Quiz();
    $this->questionModel = new Question();
    
    // Définir SITE_URL si pas déjà défini - CORRECTION ICI
    if (!defined('SITE_URL')) {
       define('SITE_URL', BASE_URL);

    }
    
    // Ne démarrer la session que si elle n'est pas déjà active
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

public function play() {
    $quizKey = $_GET['quiz'] ?? '';
    
    error_log("=== DEBUG QUIZ PLAY DÉTAILLÉ ===");
    error_log("Quiz key reçue: '{$quizKey}'");
    error_log("GET params: " . print_r($_GET, true));

    if (empty($quizKey)) {
        error_log("❌ Quiz key vide!");
        $_SESSION['error'] = "Aucun quiz sélectionné";
        header('Location: ' . BASE_URL . '?page=quiz');
        exit;
    }

    try {
        // Rechercher le quiz
        $quiz = $this->quizModel->findByKey($quizKey);
        
        if (!$quiz) {
            error_log("❌ Quiz non trouvé pour la clé: '{$quizKey}'");
            
            // Debug: afficher tous les quiz disponibles
            $allQuizzes = $this->quizModel->getAllActive();
            error_log("Quiz disponibles: " . print_r($allQuizzes, true));
            
            $_SESSION['error'] = "Quiz '{$quizKey}' introuvable";
            header('Location: ' . BASE_URL . '?page=quiz');
            exit;
        }

        error_log("✅ Quiz trouvé: {$quiz['title']} (ID: {$quiz['id']})");

        // Récupérer les questions avec leurs choix
        $questions = $this->quizModel->getQuestionsWithChoices($quiz['id']);
        
        error_log("📋 Nombre de questions récupérées: " . count($questions));
        
        if (empty($questions)) {
            error_log("❌ AUCUNE QUESTION pour le quiz ID: {$quiz['id']}");
            $_SESSION['error'] = 'Ce quiz ne contient pas encore de questions';
            header('Location: ' . BASE_URL . '?page=quiz');
            exit;
        }

        // Vérifier que chaque question a des choix
        foreach ($questions as $index => $question) {
            error_log("Question {$index}: " . ($question['question_text'] ?? 'SANS TEXTE'));
            error_log("  - Choix: " . (isset($question['choices']) ? count($question['choices']) : '0'));
            
            if (empty($question['choices'])) {
                error_log("⚠️ Question sans choix détectée!");
            }
        }

        // Inclure la vue
        require_once __DIR__ . '/../views/quiz/play.php';
        
    } catch (Exception $e) {
        error_log("🚨 ERREUR CRITIQUE: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        $_SESSION['error'] = 'Erreur lors du chargement du quiz';
        header('Location: ' . BASE_URL . '?page=quiz');
        exit;
    }
}
   

  public function index() {
        try {
            // Récupérer tous les quiz actifs
            $quizzes = $this->quizModel->getAllActive();
            
            // Vérifier si des quiz existent
            if (!$quizzes) {
                $quizzes = [];
            }
            
            // Log pour debug
            error_log("Nombre de quiz trouvés: " . count($quizzes));
            
            // Inclure la vue avec le chemin absolu
            require_once __DIR__ . '/../views/quiz/index.php';
            
        } catch (Exception $e) {
            error_log("Erreur dans QuizController::index - " . $e->getMessage());
            echo "<div style='padding: 20px; background: #fee; color: #c00; border: 1px solid #c00; border-radius: 5px; margin: 20px;'>";
            echo "<h2>Erreur</h2>";
            echo "<p>Erreur lors du chargement des quiz: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p><a href='/QuizInteractif/public/'>Retour à l'accueil</a></p>";
            echo "</div>";
        }
    }
    public function result() {
        try {
            require_once __DIR__ . '/../views/quiz/result.php';
        } catch (Exception $e) {
            error_log("Erreur dans QuizController::result - " . $e->getMessage());
            echo "<div style='padding: 20px; background: #fee; color: #c00; border: 1px solid #c00; border-radius: 5px; margin: 20px;'>";
            echo "<h2>Erreur</h2>";
            echo "<p>Erreur lors de l'affichage des résultats: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p><a href='/quiz_interactif_suite/public/quiz'>Retour à la liste des quiz</a></p>";
            echo "</div>";
        }
    }

}
?>