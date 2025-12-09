<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Section;
use App\Helpers\Sanitizer;
use App\Helpers\FlashMessage;

class SectionController extends BaseController
{
    /**
     * Muestra la lista de secciones y el formulario de edición/creación.
     */
    public function index(): void
    {
        $this->authorizeAdmin();

        $pageTitle = 'Gestión de Secciones';
        $errors = [];
        $sectionToEdit = new Section(); 

        if (isset($this->params['id'])) {
            $sectionToEdit = Section::findById((int)$this->params['id']);
            if (!$sectionToEdit) {
                $this->redirect('public/index.php?/admin/secciones');
                return;
            }
        }

        $sections = Section::findAll();

        $this->view('admin/secciones/index', [
            'pageTitle' => $pageTitle,
            'errors' => $errors,
            'sectionToEdit' => $sectionToEdit,
            'sections' => $sections
        ]);
    }

    /**
     * Guarda una nueva sección o actualiza una existente.
     */
    public function save(): void
    {
        $this->authorizeAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : null;
            $nombre = Sanitizer::sanitizeString($_POST['nombre'] ?? '');
            $descripcion = Sanitizer::sanitizeString($_POST['descripcion'] ?? null);

            if (empty($nombre)) {
                FlashMessage::setMessage('El nombre de la sección no puede estar vacío.', 'danger');
            } else {
                $section = $id ? Section::findById($id) : new Section();
                if ($section) {
                    $section->setNombre($nombre);
                    $section->setDescripcion($descripcion);
                    if ($section->save()) {
                        FlashMessage::setMessage('Sección guardada con éxito.', 'success');
                    } else {
                         FlashMessage::setMessage('Error al guardar la sección.', 'danger');
                    }
                } else {
                    FlashMessage::setMessage('La sección que intenta editar no existe.', 'danger');
                }
            }
        }
        $this->redirect('public/index.php?/admin/secciones');
    }

    /**
     * Elimina una sección.
     */
    public function delete(): void
    {
        $this->authorizeAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
            $section = Section::findById($id);
            
            if ($section) {
                if (!$section->delete()) {
                    FlashMessage::setMessage('Error: La sección está asignada a una o más partes y no puede ser eliminada.', 'danger');
                } else {
                    FlashMessage::setMessage('Sección eliminada con éxito.', 'warning');
                }
            }
        }
        $this->redirect('public/index.php?/admin/secciones');
    }
}
