<?php
/**
 * Configuration pour Oracle Database
 */

// Configuration Oracle
define('DB_HOST', 'localhost');
define('DB_PORT', '1521');
define('DB_SERVICE', 'orcl'); // Ou votre SID
define('DB_USER', 'quiz_user');
define('DB_PASS', 'quiz_password');
define('DB_CHARSET', 'AL32UTF8');

// Configuration de sécurité
define('SESSION_LIFETIME', 3600);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900);

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Connexion à Oracle avec PDO
 */
function getDB() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            // DSN pour Oracle
            $tns = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=" . DB_HOST . ")(PORT=" . DB_PORT . "))(CONNECT_DATA=(SERVICE_NAME=" . DB_SERVICE . ")))";
            $dsn = "oci:dbname=" . $tns . ";charset=" . DB_CHARSET;
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_CASE => PDO::CASE_LOWER, // Noms de colonnes en minuscules
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Erreur de connexion Oracle: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erreur de connexion à la base de données']);
            exit;
        }
    }
    
    return $pdo;
}

/**
 * Obtenir le dernier ID inséré (Oracle utilise RETURNING)
 */
function getLastInsertId($pdo, $sequence) {
    $stmt = $pdo->query("SELECT {$sequence}.CURRVAL FROM DUAL");
    return $stmt->fetchColumn();
}

/**
 * Fonction pour envoyer une réponse JSON
 */
function sendJSON($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Fonction pour valider et nettoyer les entrées
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Vérifier si l'utilisateur est connecté
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Obtenir l'ID de l'utilisateur connecté
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Obtenir le nom de l'utilisateur connecté
 */
function getCurrentUsername() {
    return $_SESSION['username'] ?? 'Invité';
}

/**
 * Déconnecter l'utilisateur
 */
function logout() {
    session_unset();
    session_destroy();
}

/**
 * Obtenir l'adresse IP du client
 */
function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    }
    return $ip;
}

/**
 * Vérifier la validité d'un token CSRF
 */
function validateCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Générer un token CSRF
 */
function generateCSRF() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Logger les erreurs dans un fichier
 */
function logError($message, $context = []) {
    $logFile = __DIR__ . '/logs/error.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $contextStr = !empty($context) ? json_encode($context) : '';
    $logMessage = "[$timestamp] $message $contextStr\n";
    
    error_log($logMessage, 3, $logFile);
}

/**
 * Vérifier si la requête est une requête AJAX
 */
function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Valider l'email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valider le nom d'utilisateur
 */
function isValidUsername($username) {
    return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
}

/**
 * Hacher un mot de passe
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Vérifier un mot de passe
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Convertir un booléen Oracle (1/0) en booléen PHP
 */
function oracleBool($value) {
    return (int)$value === 1;
}

/**
 * Convertir un booléen PHP en Oracle (1/0)
 */
function toOracleBool($value) {
    return $value ? 1 : 0;
}

/**
 * Formater une date Oracle pour affichage
 */
function formatOracleDate($oracleDate) {
    if (empty($oracleDate)) return '';
    $date = new DateTime($oracleDate);
    return $date->format('d/m/Y H:i:s');
}

// Configuration des en-têtes CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Gérer les requêtes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
?>