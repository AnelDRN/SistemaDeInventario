<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';

use App\Models\User;
use App\Helpers\FlashMessage;

// --- Controlador de Acciones de Usuario (Activar/Desactivar) ---

// 1. Proteger la página
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
    header('Location: ../login.php');
    exit();
}

// 2. Validar que se recibieron los parámetros necesarios
$action = $_GET['action'] ?? null;
$userId = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$action || !$userId) {
    header('Location: usuarios.php');
    exit();
}

// 3. Encontrar el usuario
$user = User::findById($userId);

if ($user) {
    // 4. Ejecutar la acción
    switch ($action) {
        case 'deactivate':
            if ($user->softDelete()) {
                FlashMessage::setMessage('Usuario desactivado con éxito.', 'warning');
            }
            break;
        case 'activate':
            $user->setActivo(true);
            if ($user->save()) {
                FlashMessage::setMessage('Usuario activado con éxito.', 'success');
            }
            break;
    }
}

// 5. Redirigir de vuelta a la lista de usuarios
FlashMessage::setMessage('Acción completada.', 'info');
header('Location: usuarios.php');
exit();
