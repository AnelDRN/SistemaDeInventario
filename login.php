<?php
// Incluir el archivo de arranque que carga todo el sistema
require_once __DIR__ . '/includes/bootstrap.php';

use App\Models\User;
use App\Helpers\Sanitizer;

// --- Lógica del Controlador de Login ---

// 1. Manejar la solicitud de logout ANTES de cualquier otra cosa.
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    // Redirigir para limpiar la URL de parámetros GET.
    header('Location: login.php');
    exit();
}

// 2. Si el usuario ya está logueado, redirigir al panel de admin.
if (isset($_SESSION['user_id'])) {
    header('Location: admin/index.php');
    exit();
}

$error = null;

// 3. Procesar el envío del formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = Sanitizer::sanitizeString($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "El nombre de usuario y la contraseña son requeridos.";
    } else {
        $user = User::findByUsername($username);

        if ($user && password_verify($password, $user->getPasswordHash()) && $user->isActivo()) {
            // Login exitoso: guardar datos en la sesión
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['username'] = $user->getNombreUsuario();
            $_SESSION['role_id'] = $user->getRolId();

            header('Location: admin/index.php');
            exit();
        } else {
            // Fallo en el login
            $error = 'Usuario o contraseña incorrectos, o la cuenta está inactiva.';
        }
    }
}

// 4. Incluir la Vista
// La variable $error estará disponible en la vista si se ha producido un error.
require_once __DIR__ . '/views/admin/login.php';
