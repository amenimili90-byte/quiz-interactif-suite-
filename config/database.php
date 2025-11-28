<?php
/**
 * Configuration de la base de données Oracle pour XE
 */

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        try {
            // Configuration Oracle XE
            $host = 'localhost';
            $port = '1521';
            $service = 'XE';  // ← CHANGÉ de 'orcl' à 'XE'
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
            throw new PDOException("Erreur de connexion Oracle XE: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function getPdo() {
        return $this->pdo;
    }
}
?>