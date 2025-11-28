<?php
/**
 * Contrôleur d'authentification
 */

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
        session_start();
    }

    public function loginForm() {
        require_once '../app/views/auth/login.php';
    }

    public function registerForm() {
        require_once '../app/views/auth/register.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            exit;
        }

        $identifier = $_POST['identifier'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->userModel->findByUsernameOrEmail($identifier);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $this->userModel->updateLastLogin($user['id']);
            
            header('Location: /quiz');
            exit;
        } else {
            $_SESSION['error'] = 'Identifiant ou mot de passe incorrect';
            header('Location: /login');
            exit;
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /register');
            exit;
        }

        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // Validation simple
        if (empty($username) || empty($email) || empty($password)) {
            $_SESSION['error'] = 'Tous les champs sont requis';
            header('Location: /register');
            exit;
        }

        if ($this->userModel->findByUsernameOrEmail($username) || 
            $this->userModel->findByUsernameOrEmail($email)) {
            $_SESSION['error'] = 'Nom d\'utilisateur ou email déjà utilisé';
            header('Location: /register');
            exit;
        }

        if ($this->userModel->create($username, $email, $password)) {
            $_SESSION['success'] = 'Inscription réussie! Connectez-vous maintenant.';
            header('Location: /login');
            exit;
        } else {
            $_SESSION['error'] = 'Erreur lors de l\'inscription';
            header('Location: /register');
            exit;
        }
    }

    public function logout() {
        session_destroy();
        header('Location: /');
        exit;
    }
}