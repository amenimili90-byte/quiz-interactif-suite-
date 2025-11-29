<?php
/**
 * Configuration de la base de données Oracle XE - VERSION CORRIGÉE
 */

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        try {
            // Configuration Oracle XE
            $host = 'localhost';
            $port = '1521';
            $service = 'XE';
            $username = 'quiz_user';
            $password = 'quiz_password';
            $charset = 'AL32UTF8';

            // DSN pour Oracle XE
            $tns = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=$host)(PORT=$port))(CONNECT_DATA=(SERVICE_NAME=$service)))";
            $dsn = "oci:dbname=" . $tns . ";charset=" . $charset;
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_CASE => PDO::CASE_LOWER,
            ];

            $this->pdo = new PDO($dsn, $username, $password, $options);
            
            // Test de connexion
            $this->pdo->query("SELECT 1 FROM DUAL");
            
        } catch (PDOException $e) {
            error_log("Erreur de connexion Oracle XE: " . $e->getMessage());
            throw new PDOException("Erreur de connexion Oracle XE: " . $e->getMessage());
        }
    }

    /**
     * Obtenir l'instance singleton de Database
     * Retourne l'objet Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Obtenir l'objet PDO
     * Retourne l'objet PDO pour utilisation directe
     */
    public function getPdo() {
        return $this->pdo;
    }

    /**
     * Exécuter une requête avec paramètres
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Erreur requête SQL: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Commencer une transaction
     */
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    /**
     * Valider une transaction
     */
    public function commit() {
        return $this->pdo->commit();
    }

    /**
     * Annuler une transaction
     */
    public function rollBack() {
        return $this->pdo->rollBack();
    }

    /**
     * Obtenir le dernier ID inséré
     */
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
}
?>