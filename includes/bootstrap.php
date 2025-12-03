<?php
declare(strict_types=1);

// =================================================================
// BOOTSTRAP.PHP - El corazón de la aplicación
// Este archivo se incluirá en cada script de página (ej. login.php, admin/usuarios.php)
// =================================================================

// 1. Definir la ruta raíz del proyecto para tener rutas de archivo consistentes.
define('ROOT_PATH', dirname(__DIR__));

// 2. Configurar el manejo de errores global
// Esto captura cualquier error de PHP o excepción no controlada y muestra una página de error.
require_once ROOT_PATH . '/src/Interfaces/IErrorHandler.php';
require_once ROOT_PATH . '/src/Core/ErrorHandler.php';

error_reporting(E_ALL);
set_error_handler('App\Core\ErrorHandler::handleError');
set_exception_handler('App\Core\ErrorHandler::handleException');

// 3. Iniciar la sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 4. Autoloader de clases (PSR-4)
// Carga automáticamente los archivos de clases (ej. User.php) cuando se usan.
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = ROOT_PATH . '/src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});
