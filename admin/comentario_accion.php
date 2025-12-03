<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';

use App\Models\Comment;
use App\Helpers\FlashMessage;

// --- Controlador de Acciones de Comentarios ---
// 1. Proteger la página
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
    header('Location: ../login.php');
    exit();
}

// 2. Validar parámetros
$action = $_GET['action'] ?? null;
$commentId = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$action || !$commentId) {
    header('Location: comentarios.php');
    exit();
}

// 3. Encontrar el comentario
$comment = Comment::findById($commentId);

if ($comment) {
    // 4. Ejecutar la acción
    switch ($action) {
        case 'approve':
            if ($comment->updateStatus('aprobado')) {
                FlashMessage::setMessage('Comentario aprobado.', 'success');
            }
            break;
        case 'reject':
            if ($comment->updateStatus('rechazado')) {
                FlashMessage::setMessage('Comentario rechazado.', 'warning');
            }
            break;
        case 'delete':
            if ($comment->delete()) {
                FlashMessage::setMessage('Comentario eliminado permanentemente.', 'danger');
            }
            break;
    }
}

// 5. Redirigir de vuelta a la lista
header('Location: comentarios.php');
exit();
