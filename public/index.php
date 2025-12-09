<?php
declare(strict_types=1);

/**
 * Front Controller
 */

// 1. Cargar el entorno de la aplicación
require dirname(__DIR__) . '/includes/bootstrap.php';

// 2. Crear una instancia del Router
$router = new App\Core\Router();

// --- RUTAS PÚBLICAS ---
$router->add('', ['controller' => 'HomeController', 'action' => 'index']); // Ruta raíz para el catálogo
$router->add('part/{id:\d+}', ['controller' => 'HomeController', 'action' => 'show']);
$router->add('part/comment', ['controller' => 'HomeController', 'action' => 'addComment']);

// 3. Añadir las rutas de la aplicación
// Sintaxis: $router->add('URL', ['controller' => 'NombreDelControlador', 'action' => 'nombreDeLaAccion']);

// Rutas de Ventas
$router->add('admin/venta/{id:\d+}', ['controller' => 'SaleController', 'action' => 'showForm']);
$router->add('admin/venta/process', ['controller' => 'SaleController', 'action' => 'process']);

// Rutas de Reportes
$router->add('admin/reports', ['controller' => 'ReportController', 'action' => 'index']);
$router->add('admin/reports/monthly', ['controller' => 'ReportController', 'action' => 'monthly']);

// Rutas de Comentarios
$router->add('admin/comment/delete', ['controller' => 'CommentController', 'action' => 'delete']);

// Rutas de Inventario
$router->add('admin/inventario', ['controller' => 'PartController', 'action' => 'index']);
$router->add('admin/inventario/create', ['controller' => 'PartController', 'action' => 'create']);
$router->add('admin/inventario/edit/{id:\d+}', ['controller' => 'PartController', 'action' => 'edit']);
$router->add('admin/inventario/save', ['controller' => 'PartController', 'action' => 'save']);
$router->add('admin/inventario/delete', ['controller' => 'PartController', 'action' => 'delete']);

// Rutas de Secciones
$router->add('admin/secciones', ['controller' => 'SectionController', 'action' => 'index']);
$router->add('admin/secciones/edit/{id:\d+}', ['controller' => 'SectionController', 'action' => 'index']);
$router->add('admin/secciones/save', ['controller' => 'SectionController', 'action' => 'save']);
$router->add('admin/secciones/delete', ['controller' => 'SectionController', 'action' => 'delete']);

// Rutas de Roles
$router->add('admin/roles', ['controller' => 'RoleController', 'action' => 'index']);
$router->add('admin/roles/edit/{id:\d+}', ['controller' => 'RoleController', 'action' => 'index']);
$router->add('admin/roles/save', ['controller' => 'RoleController', 'action' => 'save']);
$router->add('admin/roles/delete', ['controller' => 'RoleController', 'action' => 'delete']);

// Rutas de Admin
$router->add('admin/dashboard', ['controller' => 'AdminController', 'action' => 'index']);

// Rutas de Autenticación
$router->add('login', ['controller' => 'UserController', 'action' => 'showLoginForm']);
$router->add('login/process', ['controller' => 'UserController', 'action' => 'login']);
$router->add('logout', ['controller' => 'UserController', 'action' => 'logout']);
$router->add('register', ['controller' => 'UserController', 'action' => 'showRegistrationForm']);
$router->add('register/process', ['controller' => 'UserController', 'action' => 'register']);

// Rutas de ejemplo para el módulo de usuarios (que migraremos)
$router->add('admin/users', ['controller' => 'UserController', 'action' => 'index']);
$router->add('admin/users/create', ['controller' => 'UserController', 'action' => 'create']);
$router->add('admin/users/store', ['controller' => 'UserController', 'action' => 'store']);
$router->add('admin/users/edit/{id:\d+}', ['controller' => 'UserController', 'action' => 'edit']);
$router->add('admin/users/update/{id:\d+}', ['controller' => 'UserController', 'action' => 'update']);
$router->add('admin/users/deactivate/{id:\d+}', ['controller' => 'UserController', 'action' => 'deactivate']);
$router->add('admin/users/activate/{id:\d+}', ['controller' => 'UserController', 'action' => 'activate']);


// 4. Despachar la ruta
try {
    // Usamos QUERY_STRING para URLs del tipo: /public/index.php?/admin/users
    $url = $_SERVER['QUERY_STRING'] ?? '';

    // Extraer la parte de la ruta del QUERY_STRING.
    // La QUERY_STRING puede ser "admin/inventario" o "admin/inventario&search=faro"
    // o "admin/inventario?param=value".
    // Queremos solo la parte que el router debe usar para el matching de rutas.
    
    // Primero, obtener la parte antes del primer '?' si existe
    $route_path_with_query = strtok($url, '?'); 

    // Luego, de esa parte, obtener lo que esté antes del primer '&' si existe
    $route_path = strtok($route_path_with_query, '&');
    
    $router->dispatch($route_path);
} catch (\Throwable $e) { // Cambiado a Throwable para capturar todo tipo de errores
    // Manejo de errores con más detalle para depuración
    http_response_code($e->getCode() === 404 ? 404 : 500);
    echo "<h1>ERROR</h1>";
    echo "<p><strong>Mensaje:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
    echo "<h3>Stack Trace:</h3><pre>" . $e->getTraceAsString() . "</pre>";
}
