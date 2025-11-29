<?php
/**
 * Point d'entrée - VERSION CORRIGÉE
 */

// Démarrer la session UNE SEULE FOIS
session_start();

// Configuration des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');

// Créer le dossier logs si nécessaire
if (!is_dir(__DIR__ . '/../logs')) {
    mkdir(__DIR__ . '/../logs', 0755, true);
}

// Chemin de base du projet
define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', '/quiz_interactif_suite/public/index.php');

// Inclure la base de données
require_once BASE_PATH . '/config/database.php';

// Autoloader pour les classes
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

// ⭐ CORRECTION CRITIQUE: Récupérer TOUS les paramètres AVANT le switch
$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'index';
$quiz = $_GET['quiz'] ?? '';

// Log pour debugging
error_log("=== ROUTING DEBUG ===");
error_log("URL: " . $_SERVER['REQUEST_URI']);
error_log("Page: {$page}");
error_log("Action: {$action}");
error_log("Quiz: {$quiz}");

// Router simple
try {
    switch ($page) {
        case 'home':
            // Page d'accueil
            require_once BASE_PATH . '/app/views/partials/header.php';
            ?>
            <section class="card center">
                <h2>Bienvenue sur Quiz Interactif</h2>
                <p style="margin: 20px 0; font-size: 1.1rem;">
                    Testez vos connaissances avec nos quiz interactifs !
                </p>
                <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; margin-top: 30px;">
                    <a href="<?php echo BASE_URL; ?>?page=quiz" class="btn primary">
                        <i class="fi fi-rr-play"></i> Commencer un Quiz
                    </a>
                    <a href="<?php echo BASE_URL; ?>?page=leaderboard" class="btn">
                        <i class="fi fi-rr-trophy"></i> Voir le Classement
                    </a>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo BASE_URL; ?>?page=login" class="btn">
                        <i class="fi fi-rr-sign-in-alt"></i> Se Connecter
                    </a>
                    <?php endif; ?>
                </div>
            </section>
            <?php
            require_once BASE_PATH . '/app/views/partials/footer.php';
            break;
            
        case 'quiz':
            error_log("→ Entrée dans case 'quiz'");
            error_log("   Action reçue: '{$action}'");
            error_log("   Quiz reçu: '{$quiz}'");
            
            $controller = new QuizController();
            
            if ($action === 'play' && !empty($quiz)) {
                error_log("✅ Appel de play() avec quiz='{$quiz}'");
                $controller->play();
            } elseif ($action === 'result') {
                error_log("✅ Appel de result()");
                $controller->result();
            } else {
                error_log("✅ Appel de index()");
                $controller->index();
            }
            break;
            
        case 'login':
            $controller = new AuthController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->login();
            } else {
                $controller->loginForm();
            }
            break;
            
        case 'register':
            $controller = new AuthController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->register();
            } else {
                $controller->registerForm();
            }
            break;
            
        case 'logout':
            $controller = new AuthController();
            $controller->logout();
            break;
            
        case 'leaderboard':
            $controller = new ScoreController();
            $controller->leaderboard();
            break;
            
        case 'stats':
            $controller = new ScoreController();
            $controller->stats();
            break;
            
        case 'admin':
            $controller = new AdminController();
            switch ($action) {
                case 'quizzes':
                    $controller->quizzes();
                    break;
                case 'questions':
                    $controller->questions();
                    break;
                case 'create-quiz':
                    $controller->createQuiz();
                    break;
                case 'create-question':
                    $controller->createQuestion();
                    break;
                default:
                    $controller->dashboard();
            }
            break;
            
        default:
            // Page 404
            http_response_code(404);
            require_once BASE_PATH . '/app/views/partials/header.php';
            ?>
            <section class="card center">
                <h2>404 - Page non trouvée</h2>
                <p style="margin: 20px 0;">La page que vous recherchez n'existe pas.</p>
                <a href="<?php echo BASE_URL; ?>" class="btn primary">
                    <i class="fi fi-rr-home"></i> Retour à l'accueil
                </a>
            </section>
            <?php
            require_once BASE_PATH . '/app/views/partials/footer.php';
    }
    
} catch (Exception $e) {
    // Affichage d'erreur détaillé
    error_log("EXCEPTION CRITIQUE: " . $e->getMessage());
    error_log("Trace: " . $e->getTraceAsString());
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Erreur - Quiz Interactif</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: #1a1a2e;
                color: #eee;
                padding: 40px;
            }
            .error-box {
                background: #16213e;
                border: 2px solid #e94560;
                border-radius: 12px;
                padding: 40px;
                max-width: 800px;
                margin: 0 auto;
            }
            h1 { color: #e94560; }
            pre {
                background: #0f1419;
                padding: 15px;
                border-radius: 8px;
                overflow-x: auto;
                font-size: 0.9rem;
            }
            a {
                display: inline-block;
                margin-top: 20px;
                padding: 12px 24px;
                background: #e94560;
                color: white;
                text-decoration: none;
                border-radius: 8px;
            }
        </style>
    </head>
    <body>
        <div class="error-box">
            <h1>🔴 Erreur 500</h1>
            <p><strong>Message :</strong> <?php echo htmlspecialchars($e->getMessage()); ?></p>
            <p><strong>Fichier :</strong> <?php echo htmlspecialchars($e->getFile()); ?></p>
            <p><strong>Ligne :</strong> <?php echo $e->getLine(); ?></p>
            <h3>Stack Trace :</h3>
            <pre><?php echo htmlspecialchars($e->getTraceAsString()); ?></pre>
            <a href="<?php echo BASE_URL; ?>">Retour à l'accueil</a>
        </div>
    </body>
    </html>
    <?php
}
?>