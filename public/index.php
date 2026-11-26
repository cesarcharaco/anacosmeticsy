<?php
// public/index.php

session_start();

define('BASE_PATH', dirname(__DIR__) . '/');

require_once BASE_PATH . 'app/Config/Env.php';
Env::load(BASE_PATH . '.env');

// BASE_URL desde .env, sin barra final
$baseUrl = getenv('BASE_URL') ?: '';
define('BASE_URL', rtrim($baseUrl, '/'));

define('APP_ENV', getenv('APP_ENV') ?: 'production');

if (APP_ENV === 'local') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

require_once BASE_PATH . 'app/Config/Database.php';

// Capturar la URL amigable de forma segura y limpiar querystrings (?)
$url = $_GET['url'] ?? '';

// -----------------------------------------------------------------------------
// CORRECCIÓN CLAVE: Eliminar cualquier parámetro GET (?) de la ruta analizada
// -----------------------------------------------------------------------------
if (($pos = strpos($url, '?')) !== false) {
    $url = substr($url, 0, $pos);
}

$url = trim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$urlSegments = $url !== '' ? explode('/', $url) : [];

// Si la URL está vacía, forzamos por defecto al controlador de Autenticación
$controllerName = !empty($urlSegments[0]) ? ucfirst($urlSegments[0]) . 'Controller' : 'AuthController';
$methodName = isset($urlSegments[1]) && $urlSegments[1] !== '' ? $urlSegments[1] : 'index';
$parameter = isset($urlSegments[2]) ? $urlSegments[2] : null;

$controllerPath = BASE_PATH . 'app/Controllers/' . $controllerName . '.php';

if (file_exists($controllerPath)) {
    require_once $controllerPath;
    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        if (method_exists($controller, $methodName)) {
            $controller->$methodName($parameter);
        } else {
            http_response_code(404);
            echo "Error 404: Método '$methodName' no encontrado en el controlador '$controllerName'.";
        }
    } else {
        http_response_code(404);
        echo "Error 404: La clase '$controllerName' no existe.";
    }
} else {
    http_response_code(404);
    echo "Error 404: El archivo del controlador no existe en la ruta: $controllerPath";
}