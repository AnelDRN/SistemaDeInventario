<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';

use App\Models\User;
use App\Helpers\Sanitizer;

// --- Controlador del Formulario de Usuario (Crear/Editar) ---

// 1. Proteger la página
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
    header('Location: ../login.php');
    exit();
}

$pageTitle = 'Crear Usuario';
$user = new User();
$errors = [];
$isEditMode = false;

// 2. Determinar si es modo Edición
if (isset($_GET['id'])) {
    $userId = (int)$_GET['id'];
    $user = User::findById($userId);

    if (!$user) {
        // Si no se encuentra el usuario, redirigir
        header('Location: usuarios.php');
        exit();
    }
    $pageTitle = 'Editar Usuario';
    $isEditMode = true;
}

// 3. Manejar el envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = Sanitizer::sanitizeArray($_POST);

    $user->setNombreUsuario($data['nombre_usuario'] ?? '');
    $user->setEmail($data['email'] ?? '');
    $user->setRolId((int)($data['rol_id'] ?? 2));
    // El 'activo' checkbox solo envía valor si está marcado.
    $user->setActivo(isset($data['activo']));

    // Validaciones
    if (empty($user->getNombreUsuario())) $errors[] = "El nombre de usuario es requerido.";
    if (!Sanitizer::validateEmail($user->getEmail())) $errors[] = "El formato del email no es válido.";
    
    // La contraseña solo es requerida si es un usuario nuevo
    if (!$isEditMode && empty($data['password'])) {
        $errors[] = "La contraseña es requerida para nuevos usuarios.";
    }

    // Si no hay errores, proceder a guardar
    if (empty($errors)) {
        // Solo actualizar la contraseña si se proporcionó una nueva
        if (!empty($data['password'])) {
            $user->setPasswordHash(password_hash($data['password'], PASSWORD_DEFAULT));
        }

        if ($user->save()) {
            header('Location: usuarios.php');
            exit();
        } else {
            $errors[] = 'Hubo un error al guardar el usuario en la base de datos.';
        }
    }
}

// 4. Incluir la vista del formulario
require_once ROOT_PATH . '/views/admin/layouts/header.php';
require_once ROOT_PATH . '/views/admin/users/form.php';
require_once ROOT_PATH . '/views/admin/layouts/footer.php';
