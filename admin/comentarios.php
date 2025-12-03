<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';

use App\Models\Comment;

// --- Controlador de Moderación de Comentarios ---

// 1. Proteger la página
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
    header('Location: ../login.php');
    exit();
}

$pageTitle = 'Moderación de Comentarios';

// 2. Obtener todos los comentarios para el admin
$comments = Comment::findAllForAdmin();

// 3. Incluir la vista
require_once ROOT_PATH . '/views/admin/layouts/header.php';
require_once ROOT_PATH . '/views/admin/comments/index.php';
require_once ROOT_PATH . '/views/admin/layouts/footer.php';
