<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';

use App\Models\Part;

// --- Controlador de Acciones de Inventario (Eliminar) ---

// 1. Proteger la página
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// 2. Validar que se recibieron los parámetros necesarios vía POST
$action = $_POST['action'] ?? null;
$partId = isset($_POST['id']) ? (int)$_POST['id'] : null;

if ($action !== 'delete' || !$partId) {
    header('Location: inventario.php');
    exit();
}

// 3. Encontrar la parte a eliminar
$part = Part::findById($partId);

if ($part) {
    // 4. Guardar las rutas de las imágenes antes de borrar el registro
    $imageUrl = $part->getImagenUrl();
    $thumbUrl = $part->getThumbnailUrl();

    // 5. Eliminar el registro de la base de datos
    if ($part->delete()) {
        // 6. Si se borró de la BD, eliminar los archivos de imagen del servidor
        if ($imageUrl && file_exists(ROOT_PATH . '/' . $imageUrl)) {
            unlink(ROOT_PATH . '/' . $imageUrl);
        }
        if ($thumbUrl && file_exists(ROOT_PATH . '/' . $thumbUrl)) {
            unlink(ROOT_PATH . '/' . $thumbUrl);
        }
    }
    // Si la eliminación de la BD falla, no hacemos nada con los archivos y simplemente redirigimos.
    // Se podría añadir un mensaje de error a la sesión para mostrarlo en la lista.
}

// 7. Redirigir de vuelta a la lista de inventario
header('Location: inventario.php');
exit();
