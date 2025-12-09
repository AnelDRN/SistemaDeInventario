<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Part;
use App\Models\Section;
use App\Helpers\Sanitizer;
use App\Helpers\ImageHelper;
use App\Helpers\FlashMessage;

class PartController extends BaseController
{
    /**
     * Muestra la lista de partes del inventario, con opción de búsqueda.
     */
    public function index(): void
    {
        $this->authorizeAdmin();

        $pageTitle = 'Gestión de Inventario';
        $searchTerm = Sanitizer::sanitizeString($_GET['search'] ?? '');

        if (!empty($searchTerm)) {
            $parts = Part::search($searchTerm);
        } else {
            $parts = Part::findAll();
        }

        $sections = Section::findAll();
        $sectionMap = [];
        foreach ($sections as $section) {
            $sectionMap[$section->getId()] = $section->getNombre();
        }

        $this->view('admin/inventario/index', [
            'pageTitle' => $pageTitle,
            'searchTerm' => $searchTerm,
            'parts' => $parts,
            'sectionMap' => $sectionMap
        ]);
    }

    /**
     * Muestra el formulario para crear una nueva parte.
     */
    public function create(): void
    {
        $this->authorizeAdmin();
        
        $pageTitle = 'Añadir Nueva Parte';
        $part = new Part();
        $sections = Section::findAll();
        
        $this->view('admin/inventario/form', [
            'pageTitle' => $pageTitle,
            'part' => $part,
            'sections' => $sections,
            'isEditMode' => false,
            'errors' => []
        ]);
    }

    /**
     * Muestra el formulario para editar una parte existente.
     */
    public function edit(): void
    {
        $this->authorizeAdmin();

        $partId = (int)$this->params['id'];
        $part = Part::findById($partId);

        if (!$part) {
            $this->redirect('public/index.php?/admin/inventario');
            return;
        }

        $pageTitle = 'Editar Parte del Inventario';
        $sections = Section::findAll();

        $this->view('admin/inventario/form', [
            'pageTitle' => $pageTitle,
            'part' => $part,
            'sections' => $sections,
            'isEditMode' => true,
            'errors' => []
        ]);
    }

    /**
     * Guarda una parte nueva o actualizada.
     * Este método se podría separar en store() y update() pero por simplicidad
     * y para reflejar el archivo original, se mantiene en uno.
     */
    public function save(): void
    {
        $this->authorizeAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('public/index.php?/admin/inventario');
            return;
        }

        $id = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $isEditMode = (bool)$id;

        $part = $isEditMode ? Part::findById($id) : new Part();
        if (!$part) {
            $this->redirect('public/index.php?/admin/inventario');
            return;
        }
        
        $data = Sanitizer::sanitizeArray($_POST);
        $errors = $this->validatePartData($data, $isEditMode);

        // Poblar el objeto con los datos, incluso si hay errores, para re-poblar el form.
        $this->populatePartData($part, $data);

        // Manejo de la imagen
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $this->handleImageUpload($part, $errors);
        } elseif (!$isEditMode) {
            $errors[] = "Debe seleccionar una imagen para la nueva parte.";
        }

        if (empty($errors)) {
            if ($part->save()) {
                FlashMessage::setMessage('Parte del inventario guardada con éxito.', 'success');
                $this->redirect('public/index.php?/admin/inventario');
                return;
            } else {
                $errors[] = 'Hubo un error al guardar la parte en la base de datos.';
            }
        }
        
        // Si hay errores, volver a mostrar el formulario
        $pageTitle = $isEditMode ? 'Editar Parte del Inventario' : 'Añadir Nueva Parte';
        $sections = Section::findAll();
        $this->view('admin/inventario/form', [
            'pageTitle' => $pageTitle,
            'part' => $part,
            'sections' => $sections,
            'isEditMode' => $isEditMode,
            'errors' => $errors
        ]);
    }

    /**
     * Elimina una parte del inventario.
     */
    public function delete(): void
    {
        $this->authorizeAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $part = Part::findById($id);

            if ($part) {
                $imageUrl = $part->getImagenUrl();
                $thumbUrl = $part->getThumbnailUrl();

                if ($part->delete()) {
                    FlashMessage::setMessage('Parte eliminada con éxito.', 'warning');
                    // Eliminar archivos de imagen del servidor
                    if ($imageUrl && file_exists(ROOT_PATH . '/' . $imageUrl)) {
                        unlink(ROOT_PATH . '/' . $imageUrl);
                    }
                    if ($thumbUrl && file_exists(ROOT_PATH . '/' . $thumbUrl)) {
                        unlink(ROOT_PATH . '/' . $thumbUrl);
                    }
                } else {
                    FlashMessage::setMessage('Error al eliminar la parte.', 'danger');
                }
            }
        }
        $this->redirect('public/index.php?/admin/inventario');
    }
    
    // --- Métodos privados de ayuda ---

    private function populatePartData(Part $part, array $data): void
    {
        $part->setNombre($data['nombre'] ?? '');
        $part->setDescripcion($data['descripcion'] ?? null);
        $part->setTipoParte($data['tipo_parte'] ?? null);
        $part->setMarcaAuto($data['marca_auto'] ?? null);
        $part->setModeloAuto($data['modelo_auto'] ?? null);
        $part->setAnioAuto(isset($data['anio_auto']) && $data['anio_auto'] !== '' ? (int)$data['anio_auto'] : null);
        $part->setPrecio(isset($data['precio']) && $data['precio'] !== '' ? (float)$data['precio'] : 0.0);
        $part->setCantidadDisponible(isset($data['cantidad_disponible']) && $data['cantidad_disponible'] !== '' ? (int)$data['cantidad_disponible'] : 0);
        $part->setSeccionId((int)($data['seccion_id'] ?? 0));
    }

    private function validatePartData(array $data, bool $isEditMode): array
    {
        $errors = [];
        if (empty($data['nombre'])) $errors[] = "El nombre de la parte es requerido.";
        if (!isset($data['precio']) || (float)$data['precio'] <= 0) $errors[] = "El precio debe ser un número mayor que cero.";
        if (!isset($data['cantidad_disponible']) || (int)$data['cantidad_disponible'] < 0) $errors[] = "La cantidad no puede ser negativa.";
        if (empty($data['seccion_id'])) $errors[] = "Debe seleccionar una sección.";
        return $errors;
    }
    
    private function handleImageUpload(Part $part, array &$errors): void
    {
        $uploadsDir = 'uploads/parts';
        $thumbsDir = 'uploads/thumbs';
        
        if (!is_dir(ROOT_PATH . '/' . $uploadsDir)) mkdir(ROOT_PATH . '/' . $uploadsDir, 0777, true);
        if (!is_dir(ROOT_PATH . '/' . $thumbsDir)) mkdir(ROOT_PATH . '/' . $thumbsDir, 0777, true);

        $imagePath = ImageHelper::uploadImage($_FILES['imagen'], $uploadsDir);
        
        if ($imagePath) {
            $thumbnailPath = rtrim($thumbsDir, '/') . '/thumb_' . basename($imagePath);
            if (ImageHelper::createThumbnail($imagePath, $thumbnailPath)) {
                // Si es una edición y había una imagen previa, borrarla
                if ($part->getId() && $part->getImagenUrl()) {
                    if (file_exists(ROOT_PATH . '/' . $part->getImagenUrl())) unlink(ROOT_PATH . '/' . $part->getImagenUrl());
                    if (file_exists(ROOT_PATH . '/' . $part->getThumbnailUrl())) unlink(ROOT_PATH . '/' . $part->getThumbnailUrl());
                }
                $part->setImagenUrl($imagePath);
                $part->setThumbnailUrl($thumbnailPath);
            } else {
                $errors[] = "La imagen principal se subió, pero no se pudo crear el thumbnail.";
                unlink(ROOT_PATH . '/' . $imagePath);
            }
        } else {
            $errors[] = "Error al subir la imagen. Verifique el tipo (JPG, PNG, GIF) y el tamaño (máx 5MB).";
        }
    }
}
