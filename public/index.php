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

// --- RUTAS CARRITO ---
$router->add('cart', ['controller' => 'CartController', 'action' => 'index']);
$router->add('cart/add', ['controller' => 'CartController', 'action' => 'add']);
$router->add('cart/update', ['controller' => 'CartController', 'action' => 'update']);
$router->add('cart/remove', ['controller' => 'CartController', 'action' => 'remove']);
$router->add('cart/checkout', ['controller' => 'CartController', 'action' => 'checkout']);
$router->add('cart/order-summary', ['controller' => 'CartController', 'action' => 'orderSummary']); // New route
$router->add('cart/download-invoice', ['controller' => 'CartController', 'action' => 'downloadInvoice']); // New route

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
$router->add('admin/inventario/export-csv', ['controller' => 'PartController', 'action' => 'exportCsv']);

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
$router->add('profile/change-password', ['controller' => 'UserController', 'action' => 'showChangePasswordForm']);
$router->add('profile/change-password/process', ['controller' => 'UserController', 'action' => 'changePassword']);

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
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $script_name = $_SERVER['SCRIPT_NAME'] ?? ''; // e.g., /SDI/SistemaDeInventario/public/index.php

    $route = '';
    // Extraer la ruta base relativa al SCRIPT_NAME
    if (str_starts_with($request_uri, $script_name)) {
        $route = substr($request_uri, strlen($script_name));
    } else {
        // Fallback si la URI no empieza con el nombre del script
        $route = $request_uri;
    }

    // Normalizar la ruta para el router
    $final_route = '';
    if (str_starts_with($route, '?/')) {
        // Es una ruta MVC de tipo "?/admin/inventario"
        $final_route = substr($route, 2);
    } elseif (str_starts_with($route, '/?/')) {
        // Es una ruta MVC de tipo "/?/admin/inventario"
        $final_route = substr($route, 3);
    } elseif (str_starts_with($route, '?')) {
        // Es una URL tipo "?search=Faro". La ruta real para el router es vacía (root).
        $final_route = '';
    } else {
        // Es una ruta sin "?" o "?/", ej. "admin/dashboard" si se usa PathInfo
        // o si la URI no tenía query string.
        $final_route = $route;
    }

    // Quitar barras iniciales/finales para normalizar la ruta
    $final_route = trim($final_route, '/');

    // --- NEW LOGIC: Extract and parse query string from $final_route ---
    $query_pos = strpos($final_route, '?');
    if ($query_pos !== false) {
        $query_string_from_route = substr($final_route, $query_pos + 1);
        $final_route = substr($final_route, 0, $query_pos); // Update final_route to be just the path

        parse_str($query_string_from_route, $parsed_get_params);
        $_GET = array_merge($_GET, $parsed_get_params); // Merge into global $_GET
    }
    // --- END NEW LOGIC ---
    
    $router->dispatch($final_route);
} catch (\Throwable $e) { // Cambiado a Throwable para capturar todo tipo de errores
    // Manejo de errores con más detalle para depuración
    http_response_code($e->getCode() === 404 ? 404 : 500);
    echo "<h1>ERROR</h1>";
    echo "<p><strong>Mensaje:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
    echo "<h3>Stack Trace:</h3><pre>" . $e->getTraceAsString() . "</pre>";
}
