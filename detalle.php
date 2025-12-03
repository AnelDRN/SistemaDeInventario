<?php
require_once __DIR__ . '/includes/bootstrap.php';

use App\Models\Part;
use App\Models\Comment;
use App\Helpers\Sanitizer;

// --- Controlador de la Página de Detalle ---

// 1. Validar input y obtener la parte
$partId = isset($_GET['id']) ? (int)$_GET['id'] : null;
if (!$partId) {
    header('Location: index.php');
    exit();
}

$part = Part::findById($partId);
if (!$part) {
    // Si no se encuentra la parte, redirigir al catálogo
    header('Location: index.php');
    exit();
}

$pageTitle = htmlspecialchars($part->getNombre());
$errors = [];
$successMessage = '';

// 2. Revisar si hay mensajes de éxito en la sesión
if (isset($_SESSION['success_message'])) {
    $successMessage = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Limpiar el mensaje para no mostrarlo de nuevo
}

// 3. Procesar el envío de un nuevo comentario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        $errors[] = 'Necesitas iniciar sesión para poder comentar.';
    } else {
        $commentText = Sanitizer::sanitizeString($_POST['comment_text'] ?? '');
        if (empty($commentText)) {
            $errors[] = 'El comentario no puede estar vacío.';
        } else {
            $comment = new Comment();
            $comment->setParteId($part->getId());
            $comment->setUsuarioId((int)$_SESSION['user_id']);
            $comment->setTextoComentario($commentText);
            
            if ($comment->save()) {
                // Guardar mensaje de éxito y redirigir para prevenir reenvío del formulario
                $_SESSION['success_message'] = 'Tu comentario ha sido enviado y está pendiente de aprobación.';
                header('Location: detalle.php?id=' . $part->getId());
                exit();
            } else {
                $errors[] = 'Hubo un error al guardar tu comentario.';
            }
        }
    }
}


// 4. Obtener los comentarios aprobados para esta parte
$comments = Comment::findAllByPartId($part->getId(), 'aprobado');


// 5. Incluir la vista
require_once ROOT_PATH . '/views/public/detalle.php';
