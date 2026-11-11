<?php
// public/index.php

// Iniciar sesión para control de usuarios
session_start();

// Definir rutas base del sistema para archivos (PHP)
define('BASE_PATH', dirname(__DIR__) . '/');

// -----------------------------------------------------------------------------
// AUTODETECCIÓN DINÁMICA DE LA URL BASE (Local y Producción)
// -----------------------------------------------------------------------------
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];

// Detecta el directorio raíz web de forma automática (ej. /anacosmeticsy/public en local o vacío/raíz en servidor)
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$baseUrl = rtrim($protocol . $host . str_replace('/public', '', $scriptDir), '/');

// Definir BASE_URL para los recursos estáticos (CSS, JS, Imágenes y Enlaces)
define('BASE_URL', $baseUrl);

// Cargar configuración de base de datos
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