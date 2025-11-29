<?php
/**
 * Contrôleur d'authentification - CORRIGÉ
 */

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
        
        // Ne démarrer la session que si elle n'est pas déjà active
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function loginForm() {
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function registerForm() {
        require_once __DIR__ . '/../views/auth/register.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?page=login');
            exit;
        }

        $identifier = $_POST['identifier'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->userModel->findByUsernameOrEmail($identifier);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'] ?? 'user';
            
            $this->userModel->updateLastLogin($user['id']);
            
            header('Location: ' . BASE_URL . '?page=quiz');
            exit;
        } else {
            $_SESSION['error'] = 'Identifiant ou mot de passe incorrect';
            header('Location: ' . BASE_URL . '?page=login');
            exit;
        }
    }

    public function register() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . BASE_URL . '?page=register');
        exit;
    }

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation améliorée
    if (empty($username) || empty($email) || empty($password)) {
        $_SESSION['error'] = 'Tous les champs sont requis';
        header('Location: ' . BASE_URL . '?page=register');
        exit;
    }

    // Validation du format d'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Format d\'email invalide';
        header('Location: ' . BASE_URL . '?page=register');
        exit;
    }

    // Validation du nom d'utilisateur
    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $_SESSION['error'] = 'Le nom d\'utilisateur doit contenir 3 à 20 caractères alphanumériques';
        header('Location: ' . BASE_URL . '?page=register');
        exit;
    }

    // Validation du mot de passe
    if (strlen($password) < 6) {
        $_SESSION['error'] = 'Le mot de passe doit contenir au moins 6 caractères';
        header('Location: ' . BASE_URL . '?page=register');
        exit;
    }

    try {
        // Vérifier l'unicité avant création
        if ($this->userModel->findByUsernameOrEmail($username)) {
            $_SESSION['error'] = 'Nom d\'utilisateur déjà utilisé';
            header('Location: ' . BASE_URL . '?page=register');
            exit;
        }

        if ($this->userModel->findByUsernameOrEmail($email)) {
            $_SESSION['error'] = 'Email déjà utilisé';
            header('Location: ' . BASE_URL . '?page=register');
            exit;
        }

        $userId = $this->userModel->create($username, $email, $password);
        
        if ($userId) {
            $_SESSION['success'] = 'Inscription réussie! Connectez-vous maintenant.';
            header('Location: ' . BASE_URL . '?page=login');
            exit;
        } else {
            $_SESSION['error'] = 'Erreur lors de l\'inscription';
            header('Location: ' . BASE_URL . '?page=register');
            exit;
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header('Location: ' . BASE_URL . '?page=register');
        exit;
    }
}

    public function logout() {
        session_destroy();
        header('Location: ' . BASE_URL);
        exit;
    }
}
?>