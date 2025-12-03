<?php
// Incluir el archivo de arranque que carga todo el sistema
require_once dirname(__DIR__) . '/includes/bootstrap.php';

use App\Models\User;
use App\Models\Role;

// --- Lógica del Controlador de Lista de Usuarios ---

// 1. Proteger la página: solo para administradores
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) { // Asumiendo que rol_id 1 es Admin
    header('Location: ../login.php');
    exit();
}

// 2. Obtener todos los usuarios y roles de la base de datos
$users = User::findAll();
$roles = Role::findAll();

// Crear un mapa de ID de rol a nombre para fácil acceso en la vista
$roleMap = [];
foreach ($roles as $role) {
    $roleMap[$role->getId()] = $role->getNombre();
}

// 3. Incluir la Vista para mostrar la lista de usuarios
$pageTitle = 'Gestión de Usuarios';
require_once ROOT_PATH . '/views/admin/layouts/header.php';
require_once ROOT_PATH . '/views/admin/users/index.php';
require_once ROOT_PATH . '/views/admin/layouts/footer.php';
