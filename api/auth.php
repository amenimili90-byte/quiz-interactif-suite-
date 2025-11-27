<?php
/**
 * API d'authentification - Inscription et Connexion
 */

require_once '../config.php';

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSON(['success' => false, 'error' => 'Méthode non autorisée'], 405);
}

// Récupérer les données JSON
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

switch ($action) {
    case 'register':
        register($input);
        break;
    case 'login':
        login($input);
        break;
    case 'logout':
        logout();
        sendJSON(['success' => true, 'message' => 'Déconnexion réussie']);
        break;
    case 'check':
        checkAuth();
        break;
    default:
        sendJSON(['success' => false, 'error' => 'Action non valide'], 400);
}

/**
 * Inscription d'un nouvel utilisateur
 */
function register($data) {
    $username = sanitizeInput($data['username'] ?? '');
    $email = sanitizeInput($data['email'] ?? '');
    $password = $data['password'] ?? '';
    
    // Validation des données
    if (empty($username) || empty($email) || empty($password)) {
        sendJSON(['success' => false, 'error' => 'Tous les champs sont requis'], 400);
    }
    
    if (!isValidUsername($username)) {
        sendJSON(['success' => false, 'error' => 'Nom d\'utilisateur invalide (3-20 caractères alphanumériques)'], 400);
    }
    
    if (!isValidEmail($email)) {
        sendJSON(['success' => false, 'error' => 'Email invalide'], 400);
    }
    
    if (strlen($password) < 6) {
        sendJSON(['success' => false, 'error' => 'Le mot de passe doit contenir au moins 6 caractères'], 400);
    }
    
    try {
        $pdo = getDB();
        
        // Vérifier si l'utilisateur existe déjà
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->fetch()) {
            sendJSON(['success' => false, 'error' => 'Nom d\'utilisateur ou email déjà utilisé'], 409);
        }
        
        // Créer l'utilisateur
        $passwordHash = hashPassword($password);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $passwordHash]);
        
        $userId = $pdo->lastInsertId();
        
        // Connecter automatiquement l'utilisateur
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        
        sendJSON([
            'success' => true,
            'message' => 'Inscription réussie',
            'user' => [
                'id' => $userId,
                'username' => $username,
                'email' => $email
            ]
        ], 201);
        
    } catch (PDOException $e) {
        logError("Erreur lors de l'inscription", ['error' => $e->getMessage()]);
        sendJSON(['success' => false, 'error' => 'Erreur lors de l\'inscription'], 500);
    }
}

/**
 * Connexion d'un utilisateur
 */
function login($data) {
    $identifier = sanitizeInput($data['identifier'] ?? ''); // username ou email
    $password = $data['password'] ?? '';
    
    if (empty($identifier) || empty($password)) {
        sendJSON(['success' => false, 'error' => 'Identifiant et mot de passe requis'], 400);
    }
    
    try {
        $pdo = getDB();
        
        // Rechercher l'utilisateur par username ou email
        $stmt = $pdo->prepare("SELECT id, username, email, password_hash FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$identifier, $identifier]);
        $user = $stmt->fetch();
        
        if (!$user) {
            sendJSON(['success' => false, 'error' => 'Identifiant ou mot de passe incorrect'], 401);
        }
        
        // Vérifier le mot de passe
        if (!verifyPassword($password, $user['password_hash'])) {
            sendJSON(['success' => false, 'error' => 'Identifiant ou mot de passe incorrect'], 401);
        }
        
        // Mettre à jour la date de dernière connexion
        $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$user['id']]);
        
        // Créer la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        
        sendJSON([
            'success' => true,
            'message' => 'Connexion réussie',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email']
            ]
        ]);
        
    } catch (PDOException $e) {
        logError("Erreur lors de la connexion", ['error' => $e->getMessage()]);
        sendJSON(['success' => false, 'error' => 'Erreur lors de la connexion'], 500);
    }
}

/**
 * Vérifier l'état de connexion
 */
function checkAuth() {
    if (isLoggedIn()) {
        sendJSON([
            'success' => true,
            'authenticated' => true,
            'user' => [
                'id' => getCurrentUserId(),
                'username' => getCurrentUsername(),
                'email' => $_SESSION['email'] ?? ''
            ]
        ]);
    } else {
        sendJSON([
            'success' => true,
            'authenticated' => false
        ]);
    }
}
?>