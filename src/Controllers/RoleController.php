<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Role;
use App\Helpers\Sanitizer;
use App\Helpers\FlashMessage;

class RoleController extends BaseController
{
    /**
     * Muestra la lista de roles y el formulario de edición/creación.
     */
    public function index(): void
    {
        $this->authorizeAdmin();

        $pageTitle = 'Gestión de Roles';
        $errors = [];
        $roleToEdit = new Role(); 

        // Revisar si se está pidiendo editar un rol (aún podríamos soportar este GET param)
        if (isset($this->params['id'])) {
            $roleToEdit = Role::findById((int)$this->params['id']);
            if (!$roleToEdit) {
                $this->redirect('public/index.php?/admin/roles');
                return;
            }
        }

        $roles = Role::findAll();

        $this->view('admin/roles/index', [
            'pageTitle' => $pageTitle,
            'errors' => $errors,
            'roleToEdit' => $roleToEdit,
            'roles' => $roles
        ]);
    }

    /**
     * Guarda un nuevo rol o actualiza uno existente.
     */
    public function save(): void
    {
        $this->authorizeAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : null;
            $nombre = Sanitizer::sanitizeString($_POST['nombre'] ?? '');

            if (empty($nombre)) {
                FlashMessage::setMessage('El nombre del rol no puede estar vacío.', 'danger');
            } else {
                $role = $id ? Role::findById($id) : new Role();
                if ($role) {
                    $role->setNombre($nombre);
                    if ($role->save()) {
                        FlashMessage::setMessage('Rol guardado con éxito.', 'success');
                    } else {
                         FlashMessage::setMessage('Error al guardar el rol.', 'danger');
                    }
                } else {
                    FlashMessage::setMessage('El rol que intenta editar no existe.', 'danger');
                }
            }
        }
        $this->redirect('public/index.php?/admin/roles');
    }

    /**
     * Elimina un rol.
     */
    public function delete(): void
    {
        $this->authorizeAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
            $role = Role::findById($id);
            
            if ($role) {
                // No permitir eliminar los primeros 3 roles (Admin, Vendedor, Cliente)
                if ($role->getId() <= 3) {
                     FlashMessage::setMessage('No se pueden eliminar los roles básicos del sistema.', 'danger');
                } else {
                    if (!$role->delete()) {
                         // El error más común es una restricción de clave foránea
                        FlashMessage::setMessage('Error: El rol está asignado a uno o más usuarios y no puede ser eliminado.', 'danger');
                    } else {
                        FlashMessage::setMessage('Rol eliminado con éxito.', 'warning');
                    }
                }
            }
        }
        $this->redirect('public/index.php?/admin/roles');
    }
}
