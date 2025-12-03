<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';

use App\Models\Part;
use App\Models\Section; // Necesitaremos las secciones para mostrar sus nombres

// --- Controlador de Lista de Inventario ---

// 1. Proteger la página
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}
// Podríamos añadir una comprobación de rol si no todos los usuarios logueados pueden ver el inventario

$pageTitle = 'Gestión de Inventario';
$searchTerm = $_GET['search'] ?? '';

// 2. Obtener las partes (todas o las de la búsqueda)
if (!empty($searchTerm)) {
    $parts = Part::search($searchTerm);
} else {
    $parts = Part::findAll();
}

$sections = Section::findAll();

// Crear un mapa de ID de sección a nombre para fácil acceso en la vista
$sectionMap = [];
foreach ($sections as $section) {
    $sectionMap[$section->getId()] = $section->getNombre();
}

// 3. Incluir la Vista para mostrar la lista
require_once ROOT_PATH . '/views/admin/layouts/header.php';
require_once ROOT_PATH . '/views/admin/inventario/index.php';
require_once ROOT_PATH . '/views/admin/layouts/footer.php';
