<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';

use App\Models\Role;
use App\Helpers\Sanitizer;

// --- Controlador de Gestión de Roles ---

// 1. Proteger la página
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
    header('Location: ../login.php');
    exit();
}

$pageTitle = 'Gestión de Roles';
$errors = [];
$roleToEdit = new Role(); // Objeto vacío para el formulario de creación

// 2. Procesar peticiones POST (Crear, Actualizar, Eliminar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $nombre = Sanitizer::sanitizeString($_POST['nombre'] ?? '');

    if ($action === 'save') {
        if (empty($nombre)) {
            $errors[] = 'El nombre del rol no puede estar vacío.';
        } else {
            $role = $id ? Role::findById($id) : new Role();
            if ($role) {
                $role->setNombre($nombre);
                if (!$role->save()) {
                    $errors[] = 'Error al guardar el rol.';
                }
            } else {
                $errors[] = 'El rol que intenta editar no existe.';
            }
        }
    } elseif ($action === 'delete') {
        $role = Role::findById($id);
        if ($role) {
            // No permitir eliminar los primeros 3 roles (Admin, Vendedor, Cliente)
            if ($role->getId() <= 3) {
                 $errors[] = 'No se pueden eliminar los roles básicos del sistema.';
            } else {
                if (!$role->delete()) {
                    $errors[] = 'Error al eliminar el rol. Es posible que esté en uso.';
                }
            }
        }
    }
    
    // Si hubo una acción POST, recargar la página para limpiar la URL y mostrar los cambios.
    if(empty($errors)) {
        header('Location: roles.php');
        exit();
    }
}

// 3. Preparar datos para la vista
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $roleToEdit = Role::findById((int)$_GET['id']);
    if (!$roleToEdit) {
        header('Location: roles.php');
        exit();
    }
}

$roles = Role::findAll();

// 4. Incluir la vista
require_once ROOT_PATH . '/views/admin/layouts/header.php';
require_once ROOT_PATH . '/views/admin/roles/index.php';
require_once ROOT_PATH . '/views/admin/layouts/footer.php';
