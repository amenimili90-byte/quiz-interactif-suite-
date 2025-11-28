<?php
/**
 * Point d'entrée principal - Routeur
 */

require_once '../config/database.php';
require_once '../config/routes.php';

// Autoloading simple pour les modèles et contrôleurs
spl_autoload_register(function ($class) {
    $paths = [
        '../app/models/',
        '../app/controllers/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Gestion des routes
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = '/QuizInteractif/public';
$path = str_replace($basePath, '', $requestUri);

if ($path === '') {
    $path = '/';
}

// Trouver la route correspondante
$handler = null;
if (isset($routes[$requestMethod][$path])) {
    $handler = $routes[$requestMethod][$path];
} else {
    // Gestion des routes dynamiques (ex: /quiz/play?quiz=informatique)
    foreach ($routes[$requestMethod] as $route => $routeHandler) {
        if (strpos($path, $route) === 0) {
            $handler = $routeHandler;
            break;
        }
    }
}

if ($handler) {
    list($controllerName, $methodName) = explode('@', $handler);
    $controller = new $controllerName();
    $controller->$methodName();
} else {
    // Page 404
    http_response_code(404);
    echo "Page non trouvée";
}