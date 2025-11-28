<?php
require_once 'config/database.php';

echo "🧪 Test de connexion Oracle XE...<br>";

try {
    $db = Database::getInstance();
    echo "✅ Connexion Oracle XE établie!<br>";
    
    // Test requête
    $stmt = $db->query("SELECT 'Oracle XE fonctionne !' as message FROM DUAL");
    $result = $stmt->fetch();
    echo "✅ " . $result['message'] . "<br>";
    
    // Test données
    $stmt = $db->query("SELECT COUNT(*) as count FROM quizzes");
    $quizzes = $stmt->fetch();
    echo "📊 Nombre de quiz: " . $quizzes['count'] . "<br>";
    
    $stmt = $db->query("SELECT quiz_key, title FROM quizzes");
    $all_quizzes = $stmt->fetchAll();
    
    echo "<br>📚 Quiz dans la base:<br>";
    foreach ($all_quizzes as $quiz) {
        echo " - " . $quiz['title'] . "<br>";
    }
    
    echo "<br>🎉 Oracle XE est parfaitement configuré !";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage();
}
?>