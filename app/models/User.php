<?php
/**
 * Modèle User - CORRIGÉ POUR ORACLE
 */

class User {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    /**
     * Créer un nouvel utilisateur
     */
  public function create($username, $email, $password) {
    try {
        // Vérifier d'abord si l'utilisateur existe déjà
        $existingUser = $this->findByUsernameOrEmail($username);
        if ($existingUser) {
            throw new Exception('Nom d\'utilisateur déjà utilisé');
        }
        
        $existingUser = $this->findByUsernameOrEmail($email);
        if ($existingUser) {
            throw new Exception('Email déjà utilisé');
        }
        
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->pdo->prepare("
            INSERT INTO users (username, email, password_hash) 
            VALUES (?, ?, ?)
        ");
        
        $result = $stmt->execute([$username, $email, $passwordHash]);
        
        if ($result) {
            // Récupérer l'ID de l'utilisateur créé
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ? $user['id'] : null;
        }
        
        return false;
        
    } catch (PDOException $e) {
        error_log("Erreur create user: " . $e->getMessage());
        
        // Vérifier spécifiquement l'erreur de contrainte d'unicité
        if (strpos($e->getMessage(), 'ORA-00001') !== false) {
            throw new Exception('Nom d\'utilisateur ou email déjà utilisé');
        }
        
        throw new Exception('Erreur lors de la création du compte');
    }
}

    /**
     * Trouver un utilisateur par username ou email
     */
    public function findByUsernameOrEmail($identifier) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM users 
                WHERE username = ? OR email = ?
            ");
            $stmt->execute([$identifier, $identifier]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur findByUsernameOrEmail: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Trouver un utilisateur par ID
     */
    public function findById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur findById: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Mettre à jour la date de dernière connexion
     */
    public function updateLastLogin($userId) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET last_login = CURRENT_TIMESTAMP 
                WHERE id = ?
            ");
            return $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log("Erreur updateLastLogin: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Compter le nombre total d'utilisateurs
     */
    public function getTotalCount() {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM users");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            error_log("Erreur getTotalCount: " . $e->getMessage());
            return 0;
        }
    }
}
?>