<?php
require_once __DIR__ . '/includes/bootstrap.php';

use App\Models\Part;
use App\Models\Section;

// --- Controlador del Catálogo Público ---

$pageTitle = 'Catálogo de Partes';

// Obtener todas las partes disponibles
$parts = Part::findAll(); // Podríamos filtrar por cantidad_disponible > 0 si quisiéramos

// Obtener las secciones para poder filtrar o mostrar nombres si es necesario
$sections = Section::findAll();
$sectionMap = [];
foreach ($sections as $section) {
    $sectionMap[$section->getId()] = $section->getNombre();
}


// Incluir la vista para mostrar el catálogo
require_once ROOT_PATH . '/views/public/catalog.php';
