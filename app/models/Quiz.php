<?php
/**
 * Modèle Quiz - CORRIGÉ POUR ORACLE CLOB
 */

class Quiz {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = Database::getInstance()->getPdo();
        } catch (Exception $e) {
            error_log("Erreur connexion PDO dans Quiz: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Convertir les CLOB en string
     */
    private function convertClobToString($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_resource($value)) {
                    $data[$key] = stream_get_contents($value);
                }
            }
        }
        return $data;
    }

    /**
     * Récupérer tous les quiz actifs
     */
    public function getAllActive() {
    try {
        $stmt = $this->pdo->prepare("
            SELECT id, quiz_key, 
                   DBMS_LOB.SUBSTR(title, 4000, 1) as title,
                   DBMS_LOB.SUBSTR(description, 4000, 1) as description,
                   icon, active
            FROM quizzes 
            WHERE active = 1 
            ORDER BY id
        ");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Debug des résultats
        error_log("Résultats getAllActive: " . print_r($results, true));
        
        return $results;
    } catch (PDOException $e) {
        error_log("Erreur getAllActive: " . $e->getMessage());
        return [];
    }
}
    /**
     * Récupérer tous les quiz (actifs et inactifs)
     */
    public function getAll() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, quiz_key,
                       DBMS_LOB.SUBSTR(title, 4000, 1) as title,
                       DBMS_LOB.SUBSTR(description, 4000, 1) as description,
                       icon, active 
                FROM quizzes 
                ORDER BY id
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getAll: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Trouver un quiz par sa clé
     */
public function findByKey($quizKey) {
    try {
        // Nettoyer la clé
        $quizKey = trim($quizKey);
        $quizKey = strtolower($quizKey); // Normaliser en minuscules
        
        error_log("🔍 findByKey() - Recherche: '{$quizKey}'");
        
        $stmt = $this->pdo->prepare("
            SELECT id, quiz_key,
                   DBMS_LOB.SUBSTR(title, 4000, 1) as title,
                   DBMS_LOB.SUBSTR(description, 4000, 1) as description,
                   icon, active 
            FROM quizzes 
            WHERE LOWER(TRIM(quiz_key)) = ? AND active = 1
        ");
        $stmt->execute([$quizKey]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            error_log("✅ findByKey() - TROUVÉ: '{$result['title']}' (ID: {$result['id']})");
        } else {
            error_log("❌ findByKey() - NON TROUVÉ pour: '{$quizKey}'");
            
            // Debug: afficher tous les quiz_key disponibles
            $debugStmt = $this->pdo->query("
                SELECT quiz_key, 
                       DBMS_LOB.SUBSTR(title, 4000, 1) as title,
                       active
                FROM quizzes
            ");
            $allKeys = $debugStmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("📋 Quiz disponibles dans la DB: " . print_r($allKeys, true));
        }
        
        return $result;
        
    } catch (PDOException $e) {
        error_log("🚨 Erreur findByKey: " . $e->getMessage());
        return null;
    }
}
    /**
     * Récupérer les questions d'un quiz avec leurs choix
     */

    /**
     * Créer un nouveau quiz
     */
    public function create($data) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO quizzes (quiz_key, title, description, icon, active)
                VALUES (?, ?, ?, ?, 1)
            ");
            
            return $stmt->execute([
                $data['quiz_key'],
                $data['title'],
                $data['description'],
                $data['icon']
            ]);
        } catch (PDOException $e) {
            error_log("Erreur create quiz: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Compter le nombre total de quiz
     */
    public function getTotalCount() {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM quizzes");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            error_log("Erreur getTotalCount: " . $e->getMessage());
            return 0;
        }
    }
    public function getQuestionsWithChoices($quizId) {
    try {
        error_log("🔍 getQuestionsWithChoices pour quiz ID: {$quizId}");
        
        // Récupérer les questions
        $stmt = $this->pdo->prepare("
            SELECT id,
                   DBMS_LOB.SUBSTR(question_text, 4000, 1) as question_text,
                   question_type, time_limit,
                   DBMS_LOB.SUBSTR(explanation, 4000, 1) as explanation,
                   difficulty, points, display_order
            FROM questions 
            WHERE quiz_id = ? AND active = 1
            ORDER BY display_order, id
        ");
        $stmt->execute([$quizId]);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("📋 Questions brutes récupérées: " . count($questions));
        
        if (empty($questions)) {
            error_log("⚠️ AUCUNE question trouvée pour quiz ID: {$quizId}");
            return [];
        }
        
        // Pour chaque question, récupérer ses choix
        foreach ($questions as &$question) {
            error_log("🔍 Récupération des choix pour question ID: {$question['id']}");
            
            $stmtChoices = $this->pdo->prepare("
                SELECT id,
                       DBMS_LOB.SUBSTR(choice_text, 4000, 1) as choice_text,
                       is_correct, display_order
                FROM choices 
                WHERE question_id = ?
                ORDER BY display_order, id
            ");
            $stmtChoices->execute([$question['id']]);
            $choices = $stmtChoices->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("   → Choix trouvés: " . count($choices));
            
            // Debug: afficher les choix
            if (empty($choices)) {
                error_log("   ❌ AUCUN choix pour question ID: {$question['id']}");
                
                // Vérification directe dans la base
                $debugStmt = $this->pdo->prepare("
                    SELECT COUNT(*) as nb FROM choices WHERE question_id = ?
                ");
                $debugStmt->execute([$question['id']]);
                $debugResult = $debugStmt->fetch(PDO::FETCH_ASSOC);
                error_log("   Debug DB: " . $debugResult['nb'] . " choix dans la table");
            } else {
                foreach ($choices as $idx => $choice) {
                    error_log("   Choix {$idx}: ID={$choice['id']}, Texte=" . substr($choice['choice_text'], 0, 30));
                }
            }
            
            $question['choices'] = $choices;
        }
        
        error_log("✅ Questions finales avec choix: " . count($questions));
        
        // Vérifier qu'au moins une question a des choix
        $questionsWithChoices = array_filter($questions, function($q) {
            return !empty($q['choices']);
        });
        
        if (count($questionsWithChoices) === 0) {
            error_log("⚠️ AUCUNE question avec choix!");
        } else {
            error_log("✅ " . count($questionsWithChoices) . " questions ont des choix");
        }
        
        return $questions;
        
    } catch (PDOException $e) {
        error_log("🚨 Erreur getQuestionsWithChoices: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        return [];
    }
}}
?>