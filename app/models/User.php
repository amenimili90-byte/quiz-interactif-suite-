<?php
/**
 * Modèle User
 */

class User {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

   public function create($username, $email, $password) {
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $this->pdo->prepare("
        INSERT INTO users (id, username, email, password_hash) 
        VALUES (seq_users.NEXTVAL, ?, ?, ?)
    ");
    return $stmt->execute([$username, $email, $passwordHash]);
}

    public function findByUsernameOrEmail($identifier) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM users 
            WHERE username = ? OR email = ?
        ");
        $stmt->execute([$identifier, $identifier]);
        return $stmt->fetch();
    }

    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function updateLastLogin($userId) {
        $stmt = $this->pdo->prepare("
            UPDATE users SET last_login = NOW() WHERE id = ?
        ");
        return $stmt->execute([$userId]);
    }
}