<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';

use App\Models\Part;
use App\Models\Section;
use App\Helpers\Sanitizer;
use App\Helpers\ImageHelper;

// --- Controlador del Formulario de Inventario ---

// 1. Proteger la página
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$pageTitle = 'Añadir Nueva Parte';
$part = new Part();
$errors = [];
$isEditMode = false;

// 2. Obtener todas las secciones para el dropdown
$sections = Section::findAll();

// 3. Determinar si es modo Edición
if (isset($_GET['id'])) {
    $partId = (int)$_GET['id'];
    $part = Part::findById($partId);

    if (!$part) {
        header('Location: inventario.php');
        exit();
    }
    $pageTitle = 'Editar Parte del Inventario';
    $isEditMode = true;
}

// 4. Manejar el envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar datos de texto
    $data = Sanitizer::sanitizeArray($_POST);
    
    // Poblar el objeto Part con los datos
    $part->setNombre($data['nombre'] ?? '');
    $part->setDescripcion($data['descripcion'] ?? null);
    $part->setTipoParte($data['tipo_parte'] ?? null);
    $part->setMarcaAuto($data['marca_auto'] ?? null);
    $part->setModeloAuto($data['modelo_auto'] ?? null);
    $part->setAñoAuto(isset($data['año_auto']) && $data['año_auto'] !== '' ? (int)$data['año_auto'] : null);
    $part->setPrecio(isset($data['precio']) && $data['precio'] !== '' ? (float)$data['precio'] : 0.0);
    $part->setCantidadDisponible(isset($data['cantidad_disponible']) && $data['cantidad_disponible'] !== '' ? (int)$data['cantidad_disponible'] : 0);
    $part->setSeccionId((int)($data['seccion_id'] ?? 0));
    
    // Validaciones básicas
    if (empty($part->getNombre())) $errors[] = "El nombre de la parte es requerido.";
    if ($part->getPrecio() <= 0) $errors[] = "El precio debe ser un número mayor que cero.";
    if ($part->getCantidadDisponible() < 0) $errors[] = "La cantidad no puede ser negativa.";
    if (empty($part->getSeccionId())) $errors[] = "Debe seleccionar una sección.";

    // 5. Manejar la subida de la imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $uploadsDir = 'uploads/parts';
        $thumbsDir = 'uploads/thumbs';
        
        // Crear directorios si no existen
        if (!is_dir(ROOT_PATH . '/' . $uploadsDir)) mkdir(ROOT_PATH . '/' . $uploadsDir, 0777, true);
        if (!is_dir(ROOT_PATH . '/' . $thumbsDir)) mkdir(ROOT_PATH . '/' . $thumbsDir, 0777, true);

        $imagePath = ImageHelper::uploadImage($_FILES['imagen'], $uploadsDir);
        
        if ($imagePath) {
            $thumbnailPath = rtrim($thumbsDir, '/') . '/thumb_' . basename($imagePath);
            if (ImageHelper::createThumbnail($imagePath, $thumbnailPath)) {
                $part->setImagenUrl($imagePath);
                $part->setThumbnailUrl($thumbnailPath);
            } else {
                $errors[] = "La imagen principal se subió, pero no se pudo crear el thumbnail.";
                // Opcional: borrar la imagen principal si el thumbnail falla
                unlink(ROOT_PATH . '/' . $imagePath);
            }
        } else {
            $errors[] = "Error al subir la imagen. Verifique el tipo (JPG, PNG, GIF) y el tamaño (máx 5MB).";
        }
    } elseif (!$isEditMode) {
        // La imagen es requerida al crear una nueva parte
        $errors[] = "Debe seleccionar una imagen para la parte.";
    }

    // 6. Guardar si no hay errores
    if (empty($errors)) {
        if ($part->save()) {
            header('Location: inventario.php');
            exit();
        } else {
            $errors[] = 'Hubo un error al guardar la parte en la base de datos.';
        }
    }
}

// 7. Incluir la vista del formulario
require_once ROOT_PATH . '/views/admin/layouts/header.php';
require_once ROOT_PATH . '/views/admin/inventario/form.php';
require_once ROOT_PATH . '/views/admin/layouts/footer.php';
