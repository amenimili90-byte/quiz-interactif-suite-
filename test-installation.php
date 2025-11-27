<?php
/**
 * Script de test de l'installation
 * À exécuter après l'installation pour vérifier la configuration
 */

// Désactiver l'affichage des erreurs pour la production
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test d'Installation - Quiz Interactif</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background: linear-gradient(135deg, #e0e7ff 0%, #f5f3ff 100%);
        }
        .container {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        h1 {
            color: #8b5cf6;
            border-bottom: 3px solid #8b5cf6;
            padding-bottom: 12px;
        }
        .test {
            margin: 20px 0;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #ccc;
        }
        .test-title {
            font-weight: bold;
            margin-bottom: 8px;
        }
        .success {
            background: #d1fae5;
            border-left-color: #10b981;
            color: #065f46;
        }
        .error {
            background: #fee2e2;
            border-left-color: #ef4444;
            color: #991b1b;
        }
        .warning {
            background: #fef3c7;
            border-left-color: #f59e0b;
            color: #92400e;
        }
        .info {
            background: #dbeafe;
            border-left-color: #3b82f6;
            color: #1e3a8a;
        }
        .icon {
            margin-right: 8px;
        }
        code {
            background: #f3f4f6;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        .stat-card {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #8b5cf6;
        }
        .stat-label {
            color: #6b7280;
            font-size: 14px;
            margin-top: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Test d'Installation - Quiz Interactif</h1>
        <p>Ce script vérifie que tous les composants sont correctement installés et configurés.</p>

        <?php
        $allGood = true;

        // Test 1 : Version PHP
        echo '<div class="test ' . (version_compare(PHP_VERSION, '7.4.0', '>=') ? 'success' : 'error') . '">';
        echo '<div class="test-title"><span class="icon">' . (version_compare(PHP_VERSION, '7.4.0', '>=') ? '✅' : '❌') . '</span>Version PHP</div>';
        if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
            echo 'PHP version ' . PHP_VERSION . ' détectée (OK)';
        } else {
            echo 'PHP version ' . PHP_VERSION . ' détectée. Version 7.4+ requise !';
            $allGood = false;
        }
        echo '</div>';

        // Test 2 : Extensions PHP
        $requiredExtensions = ['pdo', 'pdo_mysql', 'json', 'session'];
        $missingExtensions = [];
        
        foreach ($requiredExtensions as $ext) {
            if ($ext === 'session') {
                if (!function_exists('session_start')) {
                    $missingExtensions[] = $ext;
                }
            } else {
                if (!extension_loaded($ext)) {
                    $missingExtensions[] = $ext;
                }
            }
        }

        echo '<div class="test ' . (empty($missingExtensions) ? 'success' : 'error') . '">';
        echo '<div class="test-title"><span class="icon">' . (empty($missingExtensions) ? '✅' : '❌') . '</span>Extensions PHP</div>';
        if (empty($missingExtensions)) {
            echo 'Toutes les extensions requises sont présentes : ' . implode(', ', $requiredExtensions);
        } else {
            echo 'Extensions manquantes : <code>' . implode(', ', $missingExtensions) . '</code>';
            $allGood = false;
        }
        echo '</div>';

        // Test 3 : Fichier config.php
        echo '<div class="test ' . (file_exists('config.php') ? 'success' : 'error') . '">';
        echo '<div class="test-title"><span class="icon">' . (file_exists('config.php') ? '✅' : '❌') . '</span>Fichier config.php</div>';
        if (file_exists('config.php')) {
            echo 'Le fichier config.php existe et est accessible';
        } else {
            echo 'Le fichier config.php est introuvable !';
            $allGood = false;
        }
        echo '</div>';

        // Test 4 : Connexion à la base de données
        if (file_exists('config.php')) {
            require_once 'config.php';
            
            try {
                $pdo = getDB();
                echo '<div class="test success">';
                echo '<div class="test-title"><span class="icon">✅</span>Connexion à la base de données</div>';
                echo 'Connexion réussie à la base <code>' . DB_NAME . '</code> sur <code>' . DB_HOST . '</code>';
                echo '</div>';

                // Test 5 : Tables de la base de données
                $requiredTables = ['users', 'quizzes', 'questions', 'choices', 'scores', 'user_answers'];
                $stmt = $pdo->query("SHOW TABLES");
                $existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                $missingTables = array_diff($requiredTables, $existingTables);

                echo '<div class="test ' . (empty($missingTables) ? 'success' : 'error') . '">';
                echo '<div class="test-title"><span class="icon">' . (empty($missingTables) ? '✅' : '❌') . '</span>Tables de la base de données</div>';
                if (empty($missingTables)) {
                    echo 'Toutes les tables requises sont présentes : ' . implode(', ', $requiredTables);
                } else {
                    echo 'Tables manquantes : <code>' . implode(', ', $missingTables) . '</code><br>';
                    echo 'Veuillez importer le fichier database.sql';
                    $allGood = false;
                }
                echo '</div>';

                // Statistiques de la base de données
                if (empty($missingTables)) {
                    echo '<h2 style="margin-top: 30px; color: #8b5cf6;">📊 Statistiques de la Base de Données</h2>';
                    echo '<div class="stats">';

                    // Nombre d'utilisateurs
                    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
                    $userCount = $stmt->fetchColumn();
                    echo '<div class="stat-card">';
                    echo '<div class="stat-value">' . $userCount . '</div>';
                    echo '<div class="stat-label">Utilisateurs</div>';
                    echo '</div>';

                    // Nombre de quiz
                    $stmt = $pdo->query("SELECT COUNT(*) FROM quizzes WHERE active = 1");
                    $quizCount = $stmt->fetchColumn();
                    echo '<div class="stat-card">';
                    echo '<div class="stat-value">' . $quizCount . '</div>';
                    echo '<div class="stat-label">Quiz actifs</div>';
                    echo '</div>';

                    // Nombre de questions
                    $stmt = $pdo->query("SELECT COUNT(*) FROM questions WHERE active = 1");
                    $questionCount = $stmt->fetchColumn();
                    echo '<div class="stat-card">';
                    echo '<div class="stat-value">' . $questionCount . '</div>';
                    echo '<div class="stat-label">Questions</div>';
                    echo '</div>';

                    // Nombre de parties jouées
                    $stmt = $pdo->query("SELECT COUNT(*) FROM scores");
                    $scoreCount = $stmt->fetchColumn();
                    echo '<div class="stat-card">';
                    echo '<div class="stat-value">' . $scoreCount . '</div>';
                    echo '<div class="stat-label">Parties jouées</div>';
                    echo '</div>';

                    echo '</div>';

                    // Test 6 : Données de test
                    if ($quizCount === 0 || $questionCount === 0) {
                        echo '<div class="test warning">';
                        echo '<div class="test-title"><span class="icon">⚠️</span>Données de test</div>';
                        echo 'Aucun quiz ou question trouvé. Importez le fichier database.sql pour avoir des données de test.';
                        echo '</div>';
                    } else {
                        echo '<div class="test success">';
                        echo '<div class="test-title"><span class="icon">✅</span>Données de test</div>';
                        echo 'La base de données contient des quiz et des questions.';
                        echo '</div>';
                    }
                }

            } catch (PDOException $e) {
                echo '<div class="test error">';
                echo '<div class="test-title"><span class="icon">❌</span>Connexion à la base de données</div>';
                echo 'Erreur de connexion : <code>' . htmlspecialchars($e->getMessage()) . '</code><br>';
                echo 'Vérifiez les paramètres dans config.php';
                echo '</div>';
                $allGood = false;
            }
        }

        // Test 7 : Dossier API
        echo '<div class="test ' . (is_dir('api') ? 'success' : 'error') . '">';
        echo '<div class="test-title"><span class="icon">' . (is_dir('api') ? '✅' : '❌') . '</span>Dossier API</div>';
        if (is_dir('api')) {
            $apiFiles = ['auth.php', 'get-questions.php', 'verify-answer.php', 'save-score.php', 'get-leaderboard.php', 'get-stats.php'];
            $missingFiles = [];
            foreach ($apiFiles as $file) {
                if (!file_exists('api/' . $file)) {
                    $missingFiles[] = $file;
                }
            }
            if (empty($missingFiles)) {
                echo 'Tous les fichiers API sont présents';
            } else {
                echo 'Fichiers API manquants : <code>' . implode(', ', $missingFiles) . '</code>';
                $allGood = false;
            }
        } else {
            echo 'Le dossier api/ est introuvable !';
            $allGood = false;
        }
        echo '</div>';

        // Test 8 : Permissions
        $logsDir = 'logs';
        echo '<div class="test ' . (is_writable('.') ? 'success' : 'warning') . '">';
        echo '<div class="test-title"><span class="icon">' . (is_writable('.') ? '✅' : '⚠️') . '</span>Permissions d\'écriture</div>';
        if (is_writable('.')) {
            echo 'Le serveur peut écrire dans le répertoire (pour créer des logs)';
            if (!is_dir($logsDir)) {
                if (@mkdir($logsDir, 0755)) {
                    echo '<br>Dossier logs/ créé avec succès';
                }
            }
        } else {
            echo 'Le serveur ne peut pas écrire dans le répertoire. Les logs ne pourront pas être créés.';
        }
        echo '</div>';

        // Test 9 : Sessions
        if (session_status() === PHP_SESSION_ACTIVE) {
            echo '<div class="test success">';
            echo '<div class="test-title"><span class="icon">✅</span>Sessions PHP</div>';
            echo 'Les sessions PHP sont actives';
            echo '</div>';
        } else {
            echo '<div class="test warning">';
            echo '<div class="test-title"><span class="icon">⚠️</span>Sessions PHP</div>';
            echo 'Les sessions PHP ne sont pas démarrées. Cela peut causer des problèmes.';
            echo '</div>';
        }

        // Résumé final
        echo '<div style="margin-top: 40px; padding: 20px; background: ' . ($allGood ? '#d1fae5' : '#fee2e2') . '; border-radius: 8px; border-left: 4px solid ' . ($allGood ? '#10b981' : '#ef4444') . ';">';
        if ($allGood) {
            echo '<h2 style="margin: 0; color: #065f46;">🎉 Installation Réussie !</h2>';
            echo '<p style="margin: 10px 0 0 0; color: #065f46;">Tous les tests sont passés avec succès. Vous pouvez maintenant utiliser l\'application.</p>';
            echo '<p style="margin: 10px 0 0 0;"><a href="index.html" style="color: #8b5cf6; font-weight: bold;">→ Accéder au Quiz</a></p>';
        } else {
            echo '<h2 style="margin: 0; color: #991b1b;">⚠️ Problèmes Détectés</h2>';
            echo '<p style="margin: 10px 0 0 0; color: #991b1b;">Veuillez corriger les erreurs ci-dessus avant d\'utiliser l\'application.</p>';
            echo '<p style="margin: 10px 0 0 0;">Consultez le fichier <code>INSTALLATION.md</code> pour plus d\'aide.</p>';
        }
        echo '</div>';
        ?>

        <div style="margin-top: 30px; padding: 15px; background: #f3f4f6; border-radius: 8px;">
            <h3 style="margin-top: 0;">📝 Prochaines Étapes</h3>
            <ol style="margin: 10px 0; padding-left: 20px;">
                <li>Vérifier que tous les tests sont au vert</li>
                <li>Supprimer ce fichier de test pour la sécurité : <code>test-installation.php</code></li>
                <li>Modifier les identifiants de base de données par défaut</li>
                <li>Configurer HTTPS pour la production</li>
                <li>Créer votre premier compte utilisateur</li>
            </ol>
        </div>

        <div style="margin-top: 20px; text-align: center; color: #6b7280; font-size: 14px;">
            <p>Quiz Interactif PHP • Test d'Installation v1.0</p>
        </div>
    </div>
</body>
</html>