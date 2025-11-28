<?php
require_once 'config/database.php';

echo "<h2>🧪 Test de connexion Oracle</h2>";

try {
    $db = Database::getInstance();
    echo "<p style='color: green;'>✅ Connexion Oracle établie avec succès!</p>";
    
    // Tester les données
    $quizzes = $db->query("SELECT COUNT(*) as count FROM quizzes")->fetch();
    echo "<p><strong>Nombre de quiz:</strong> " . $quizzes['count'] . "</p>";
    
    $questions = $db->query("SELECT COUNT(*) as count FROM questions")->fetch();
    echo "<p><strong>Nombre de questions:</strong> " . $questions['count'] . "</p>";
    
    $users = $db->query("SELECT COUNT(*) as count FROM users")->fetch();
    echo "<p><strong>Nombre d'utilisateurs:</strong> " . $users['count'] . "</p>";
    
    // Afficher les quiz disponibles
    $stmt = $db->query("SELECT quiz_key, title FROM quizzes ORDER BY id");
    $quizzesList = $stmt->fetchAll();
    
    echo "<p><strong>Quiz disponibles:</strong></p>";
    echo "<ul>";
    foreach ($quizzesList as $quiz) {
        echo "<li>" . htmlspecialchars($quiz['title']) . " (key: " . $quiz['quiz_key'] . ")</li>";
    }
    echo "</ul>";
    
    echo "<p style='color: green;'>🎉 Base Oracle opérationnelle!</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur Oracle: " . $e->getMessage() . "</p>";
    echo "<p>Vérifiez que :</p>";
    echo "<ul>";
    echo "<li>Le service Oracle est démarré</li>";
    echo "<li>Le TNS est correctement configuré</li>";
    echo "<li>Les identifiants sont valides</li>";
    echo "</ul>";
}
?>