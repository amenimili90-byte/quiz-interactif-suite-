<?php
/**
 * Fichier de test du routage - À placer dans /public/
 */

// Configuration
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';

// Autoloader
spl_autoload_register(function ($class) {
    $paths = [
        BASE_PATH . '/app/models/' . $class . '.php',
        BASE_PATH . '/app/controllers/' . $class . '.php',
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

echo "<pre style='background:#1a1a2e; color:#0f0; padding:20px; font-family:monospace;'>";
echo "=== TEST DE ROUTAGE QUIZ ===\n\n";

// Test 1: Vérifier les paramètres GET
echo "1. PARAMÈTRES GET:\n";
echo "   - page: " . ($_GET['page'] ?? 'NON DÉFINI') . "\n";
echo "   - action: " . ($_GET['action'] ?? 'NON DÉFINI') . "\n";
echo "   - quiz: " . ($_GET['quiz'] ?? 'NON DÉFINI') . "\n";
echo "   - URL complète: " . $_SERVER['REQUEST_URI'] . "\n\n";

// Test 2: Charger le modèle Quiz
echo "2. CHARGEMENT MODÈLE QUIZ:\n";
try {
    $quizModel = new Quiz();
    echo "   ✅ Modèle chargé avec succès\n\n";
} catch (Exception $e) {
    echo "   ❌ ERREUR: " . $e->getMessage() . "\n\n";
    exit;
}

// Test 3: Récupérer tous les quiz
echo "3. LISTE DES QUIZ DISPONIBLES:\n";
$quizzes = $quizModel->getAllActive();
foreach ($quizzes as $q) {
    echo "   - ID: {$q['id']} | Clé: '{$q['quiz_key']}' | Titre: {$q['title']}\n";
}
echo "\n";

// Test 4: Si un quiz est spécifié, le tester
if (!empty($_GET['quiz'])) {
    $quizKey = $_GET['quiz'];
    echo "4. TEST DU QUIZ '{$quizKey}':\n";
    
    // Test findByKey
    $quiz = $quizModel->findByKey($quizKey);
    if ($quiz) {
        echo "   ✅ Quiz trouvé:\n";
        echo "      - ID: {$quiz['id']}\n";
        echo "      - Titre: {$quiz['title']}\n";
        echo "      - Description: {$quiz['description']}\n\n";
        
        // Test des questions
        echo "5. TEST DES QUESTIONS:\n";
        $questions = $quizModel->getQuestionsWithChoices($quiz['id']);
        echo "   - Nombre de questions: " . count($questions) . "\n\n";
        
        foreach ($questions as $index => $question) {
            echo "   Question " . ($index + 1) . ":\n";
            echo "      - ID: {$question['id']}\n";
            echo "      - Texte: " . substr($question['question_text'], 0, 50) . "...\n";
            echo "      - Type: {$question['question_type']}\n";
            echo "      - Points: {$question['points']}\n";
            echo "      - Nombre de choix: " . (isset($question['choices']) ? count($question['choices']) : 0) . "\n";
            
            if (isset($question['choices'])) {
                foreach ($question['choices'] as $choiceIndex => $choice) {
                    $correct = $choice['is_correct'] ? '✓' : '✗';
                    echo "         {$correct} Choix " . ($choiceIndex + 1) . ": " . substr($choice['choice_text'], 0, 40) . "\n";
                }
            }
            echo "\n";
        }
        
        if (empty($questions)) {
            echo "   ⚠️ AUCUNE QUESTION trouvée pour ce quiz!\n\n";
        }
        
    } else {
        echo "   ❌ Quiz NON TROUVÉ pour la clé '{$quizKey}'\n\n";
    }
} else {
    echo "4. Aucun quiz spécifié dans les paramètres\n";
    echo "   Pour tester un quiz, ajoutez ?quiz=informatique à l'URL\n\n";
}

echo "=== FIN DU TEST ===\n";
echo "</pre>";

// Afficher les liens de test
echo "<div style='padding:20px; background:#fff; margin:20px; border-radius:8px;'>";
echo "<h2>🔗 Liens de test:</h2>";
echo "<ul style='font-size:1.1rem; line-height:2;'>";
foreach ($quizzes as $q) {
    $testUrl = $_SERVER['PHP_SELF'] . "?page=quiz&action=play&quiz=" . urlencode($q['quiz_key']);
    echo "<li><a href='{$testUrl}' style='color:#e94560; font-weight:bold;'>{$q['title']}</a> (clé: {$q['quiz_key']})</li>";
}
echo "</ul>";
echo "</div>";
?>