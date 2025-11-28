<?php
/**
 * Modèle d'authentification
 */

class Auth {
    private $pdo;
    private $userModel;

    public function __construct() {
        $this->pdo = Database::getInstance();
        $this->userModel = new User();
    }

    public function validateCredentials($identifier, $password) {
        $user = $this->userModel->findByUsernameOrEmail($identifier);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        
        return false;
    }

    public function registerUser($username, $email, $password) {
        // Validation des données
        if (!$this->isValidUsername($username)) {
            throw new Exception('Nom d\'utilisateur invalide');
        }

        if (!$this->isValidEmail($email)) {
            throw new Exception('Email invalide');
        }

        if (strlen($password) < 6) {
            throw new Exception('Le mot de passe doit contenir au moins 6 caractères');
        }

        // Vérifier si l'utilisateur existe déjà
        if ($this->userModel->findByUsernameOrEmail($username) || 
            $this->userModel->findByUsernameOrEmail($email)) {
            throw new Exception('Nom d\'utilisateur ou email déjà utilisé');
        }

        return $this->userModel->create($username, $email, $password);
    }

    public function isValidUsername($username) {
        return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
    }

    public function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    public function login($userId, $username, $role = 'user') {
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
        $_SESSION['login_time'] = time();

        $this->userModel->updateLastLogin($userId);
    }

    public function logout() {
        session_unset();
        session_destroy();
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return $this->userModel->findById($_SESSION['user_id']);
        }
        return null;
    }
}