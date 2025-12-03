<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';

use App\Models\Section;
use App\Helpers\Sanitizer;

// --- Controlador de Gestión de Secciones ---

// 1. Proteger la página
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
    header('Location: ../login.php');
    exit();
}

$pageTitle = 'Gestión de Secciones';
$errors = [];
$sectionToEdit = new Section(); // Objeto vacío para el formulario de creación

// 2. Procesar peticiones POST (Crear, Actualizar, Eliminar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $nombre = Sanitizer::sanitizeString($_POST['nombre'] ?? '');
    $descripcion = Sanitizer::sanitizeString($_POST['descripcion'] ?? null);

    if ($action === 'save') {
        if (empty($nombre)) {
            $errors[] = 'El nombre de la sección no puede estar vacío.';
        } else {
            $section = $id ? Section::findById($id) : new Section();
            if ($section) {
                $section->setNombre($nombre);
                $section->setDescripcion($descripcion);
                if (!$section->save()) {
                    $errors[] = 'Error al guardar la sección.';
                }
            } else {
                $errors[] = 'La sección que intenta editar no existe.';
            }
        }
    } elseif ($action === 'delete') {
        $section = Section::findById($id);
        if ($section) {
            if (!$section->delete()) {
                $errors[] = 'Error al eliminar la sección. Es posible que esté en uso por alguna parte del inventario.';
            }
        }
    }
    
    // Si no hubo errores en la acción, recargar la página para limpiar la URL y mostrar cambios.
    if(empty($errors)) {
        header('Location: secciones.php');
        exit();
    }
}

// 3. Preparar datos para la vista de edición
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $sectionToEdit = Section::findById((int)$_GET['id']);
    if (!$sectionToEdit) {
        header('Location: secciones.php');
        exit();
    }
}

// 4. Obtener todas las secciones para mostrarlas en la lista
$sections = Section::findAll();

// 5. Incluir la vista
require_once ROOT_PATH . '/views/admin/layouts/header.php';
require_once ROOT_PATH . '/views/admin/secciones/index.php';
require_once ROOT_PATH . '/views/admin/layouts/footer.php';
