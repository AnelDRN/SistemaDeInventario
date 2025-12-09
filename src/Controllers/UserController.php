<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\User;
use App\Models\Role;
use App\Helpers\Sanitizer;
use App\Helpers\FlashMessage;

class UserController extends BaseController
{
    private User $userModel;

    public function __construct(array $params = [])
    {
        parent::__construct($params);
        $this->userModel = new User();
    }

    public function showLoginForm(): void
    {
        // If user is already logged in, redirect based on role
        if (isset($_SESSION['user_id'])) {
            // Admin users go to the dashboard
            if ($_SESSION['role_id'] === 1) {
                $this->redirect('public/index.php?/admin/dashboard');
            } else {
                // Other users (e.g., Clientes) go to the home page
                $this->redirect('public/index.php');
            }
            return;
        }

        // Show the login form for guests
        $this->view('admin/login', ['pageTitle' => 'Login']);
    }

    /**
     * Handles user login processing.
     */
    public function login(): void
    {
        // Si se intenta acceder por GET, redirigir al formulario
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('public/index.php?/login');
            return;
        }

        $username = Sanitizer::sanitizeString($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $error = null;

        if (empty($username) || empty($password)) {
            $error = "El nombre de usuario y la contraseña son requeridos.";
        } else {
            $user = User::findByUsername($username);

            if ($user && password_verify($password, $user->getPasswordHash()) && $user->isActivo()) {
                // Login exitoso: guardar datos en la sesión
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['username'] = $user->getNombreUsuario();
                $_SESSION['role_id'] = $user->getRolId(); // Keep role_id for authorizeAdmin()
                
                // Store specific permissions from the Role object
                $_SESSION['user_permissions'] = [
                    'can_manage_users' => $user->getRol()->canManageUsers(),
                    'can_manage_roles' => $user->getRol()->canManageRoles(),
                    'can_manage_sections' => $user->getRol()->canManageSections(),
                    'can_manage_inventory' => $user->getRol()->canManageInventory(),
                    'can_view_reports' => $user->getRol()->canViewReports(),
                ];

                // --- Handle Redirect ---
                if (isset($_SESSION['redirect_to'])) {
                    $redirect_url = $_SESSION['redirect_to'];
                    unset($_SESSION['redirect_to']);
                    // Note: BaseController->redirect() does not handle full URLs well, needs relative path
                    // We need to strip BASE_URL if it's present or construct the path carefully
                    // Assuming redirect_to stores a path like '/cart/checkout'
                    $this->redirect('public/index.php?' . $redirect_url);
                } else {
                    // Default redirect based on role
                    if ($_SESSION['role_id'] === 1) { // Admin
                        $this->redirect('public/index.php?/admin/dashboard');
                    } else { // Other roles
                        $this->redirect('public/index.php');
                    }
                }
                return;
            } else {
                // Fallo en el login
                $error = 'Usuario o contraseña incorrectos, o la cuenta está inactiva.';
            }
        }

        // Si hay un error, volver a mostrar el formulario con el mensaje
        $this->view('admin/login', ['pageTitle' => 'Login', 'error' => $error]);
    }

    /**
     * Logs out the user.
     */
    public function logout(): void
    {
        session_unset();
        session_destroy();
        $this->redirect('public/index.php?/login');
    }

    /**
     * Shows the public user registration form.
     */
    public function showRegistrationForm(): void
    {
        // If user is already logged in, redirect to home
        if (isset($_SESSION['user_id'])) {
            $this->redirect('public/index.php');
            return;
        }

        $this->view('public/register', [
            'pageTitle' => 'Crear Nueva Cuenta',
            'nombre_usuario' => '',
            'email' => '',
            'errors' => []
        ]);
    }

    /**
     * Processes the public user registration form.
     */
    public function register(): void
    {
        // If user is already logged in, redirect
        if (isset($_SESSION['user_id'])) {
            $this->redirect('public/index.php');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = Sanitizer::sanitizeArray($_POST);
            
            $errors = $this->validateUserData($data, false, null);

            if (empty($errors)) {
                $user = new User();
                $user->setNombreUsuario($data['nombre_usuario']);
                $user->setEmail($data['email']);
                $user->setPasswordHash(password_hash($data['password'], PASSWORD_DEFAULT));
                $user->setRolId(3); // Force role to 3 (Cliente) for public registration
                $user->setActivo(true);

                if ($user->save()) {
                    // Automatically log in the new user
                    $_SESSION['user_id'] = $user->getId();
                    $_SESSION['username'] = $user->getNombreUsuario();
                    $_SESSION['role_id'] = $user->getRolId();

                    FlashMessage::setMessage('¡Registro exitoso! Ya puedes comentar.', 'success');
                    $this->redirect('public/index.php'); // Redirect to home page
                    return;
                } else {
                    $errors[] = "Hubo un error al guardar el usuario en la base de datos.";
                }
            }
            
            // If there are errors, show the form again with the data
            $this->view('public/register', [
                'errors' => $errors,
                'nombre_usuario' => $data['nombre_usuario'] ?? '',
                'email' => $data['email'] ?? '',
                'pageTitle' => 'Crear Nueva Cuenta'
            ]);

        } else {
            $this->redirect('public/index.php?/register');
        }
    }

    /**
     * Shows the form for a logged-in user to change their password.
     */
    public function showChangePasswordForm(): void
    {
        // Authorize any logged-in user
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('public/index.php?/login');
            return;
        }

        $this->view('user/change_password', [
            'pageTitle' => 'Cambiar Contraseña',
            'errors' => FlashMessage::getMessages('errors'),
            'successMessage' => FlashMessage::getMessages('success') ? FlashMessage::getMessages('success')[0] : null
        ]);
    }

    /**
     * Processes the password change request for a logged-in user.
     */
    public function changePassword(): void
    {
        // Authorize any logged-in user
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('public/index.php?/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('public/index.php?/profile/change-password');
            return;
        }

        $userId = (int)$_SESSION['user_id'];
        $user = User::findById($userId);

        if (!$user) {
            FlashMessage::setMessage('Usuario no encontrado.', 'danger');
            $this->redirect('public/index.php?/profile/change-password');
            return;
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmNewPassword = $_POST['confirm_new_password'] ?? '';

        $errors = [];

        // Validate current password
        if (!password_verify($currentPassword, $user->getPasswordHash())) {
            $errors[] = "La contraseña actual es incorrecta.";
        }

        // Validate new password
        if (empty($newPassword) || strlen($newPassword) < 6) {
            $errors[] = "La nueva contraseña debe tener al menos 6 caracteres.";
        }
        if ($newPassword !== $confirmNewPassword) {
            $errors[] = "La nueva contraseña y su confirmación no coinciden.";
        }

        if (empty($errors)) {
            $user->setPasswordHash(password_hash($newPassword, PASSWORD_DEFAULT));
            if ($user->save()) {
                FlashMessage::setMessage('Contraseña actualizada con éxito.', 'success');
                $this->redirect('public/index.php?/profile/change-password');
                return;
            } else {
                $errors[] = "Hubo un error al actualizar la contraseña.";
            }
        }
        
        // If there are errors, show the form again with messages
        FlashMessage::setMessages('errors', $errors);
        $this->redirect('public/index.php?/profile/change-password');
    }

    /**
     * Shows a list of users (Admin only).
     */
    public function index(): void
    {
        $this->authorizeAdmin();

        $users = User::findAll();
        
        // Cargar roles para mostrar en la vista, como en el script original
        $roles = Role::findAll();
        $roleMap = [];
        foreach ($roles as $role) {
            $roleMap[$role->getId()] = $role->getNombre();
        }

        $this->view('admin/users/index', ['users' => $users, 'roleMap' => $roleMap]);
    }

    /**
     * Shows the form to create a new user.
     */
    public function create(): void
    {
        $this->authorizeAdmin();
        $roles = Role::findAll();
        $pageTitle = 'Crear Usuario';
        $this->view('admin/users/form', [
            'isEditMode' => false, 
            'user' => new User(), 
            'roles' => $roles,
            'pageTitle' => $pageTitle
        ]);
    }

    /**
     * Stores a new user in the database.
     */
    public function store(): void
    {
        $this->authorizeAdmin();
        $pageTitle = 'Crear Usuario'; // Define pageTitle for re-rendering on error

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = Sanitizer::sanitizeArray($_POST);
            $errors = $this->validateUserData($data, false, null);

            $user = new User();
            $this->populateUserData($user, $data);

            if (empty($errors)) {
                $user->setPasswordHash(password_hash($data['password'], PASSWORD_DEFAULT));

                if ($user->save()) {
                    FlashMessage::setMessage('Usuario creado con éxito.', 'success');
                    $this->redirect('public/index.php?/admin/users');
                } else {
                    $errors[] = "Error al guardar el usuario.";
                }
            }
            
            // Si hay errores, volver a mostrar el formulario con los datos y errores
            $roles = Role::findAll();
            $this->view('admin/users/form', [
                'errors' => $errors, 
                'user' => $user, // Pasar el objeto User poblado
                'isEditMode' => false,
                'roles' => $roles,
                'pageTitle' => $pageTitle
            ]);
        } else {
            $this->redirect('public/index.php?/admin/users');
        }
    }
    
    /**
     * Shows the form to edit an existing user.
     */
    public function edit(): void
    {
        $this->authorizeAdmin();
        $id = (int)$this->params['id'];
        $user = User::findById($id);

        if (!$user) {
            $this->redirect('public/index.php?/admin/users');
            return;
        }
        
        $roles = Role::findAll();
        $pageTitle = 'Editar Usuario'; // Define pageTitle
        $this->view('admin/users/form', [
            'user' => $user, 
            'isEditMode' => true,
            'roles' => $roles,
            'pageTitle' => $pageTitle
        ]);
    }

    /**
     * Updates an existing user in the database.
     */
    public function update(): void
    {
        $this->authorizeAdmin();
        $pageTitle = 'Editar Usuario'; // Define pageTitle for re-rendering on error
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$this->params['id'];
            $user = User::findById($id);

            if (!$user) {
                $this->redirect('public/index.php?/admin/users');
                return;
            }

            $data = Sanitizer::sanitizeArray($_POST);
            $errors = $this->validateUserData($data, true, $id);

            // Poblar el objeto user con los nuevos datos para la re-población del form
            $this->populateUserData($user, $data);

            if (empty($errors)) {
                if (!empty($data['password'])) {
                    $user->setPasswordHash(password_hash($data['password'], PASSWORD_DEFAULT));
                }

                if ($user->save()) {
                    FlashMessage::setMessage('Usuario actualizado con éxito.', 'success');
                    $this->redirect('public/index.php?/admin/users');
                } else {
                    $errors[] = "Error al actualizar el usuario.";
                }
            }
            
            // Si hay errores, volver a mostrar el formulario con los datos y errores
            $roles = Role::findAll();
            $this->view('admin/users/form', [
                'errors' => $errors, 
                'user' => $user, // Pasar el objeto User poblado
                'isEditMode' => true, 
                'pageTitle' => $pageTitle,
                'roles' => $roles
            ]);
        } else {
            $this->redirect('public/index.php?/admin/users');
        }
    }

    /**
     * Deactivates a user (soft delete).
     */
    public function deactivate(): void
    {
        $this->authorizeAdmin();
        $id = (int)$this->params['id'];
        $user = User::findById($id);

        if ($user) {
            if ($user->softDelete()) {
                 FlashMessage::setMessage('Usuario desactivado con éxito.', 'warning');
            }
        }
        $this->redirect('public/index.php?/admin/users');
    }

    /**
     * Activates a user.
     */
    public function activate(): void
    {
        $this->authorizeAdmin();
        $id = (int)$this->params['id'];
        $user = User::findById($id);

        if ($user) {
            $user->setActivo(true);
            $user->save();
            FlashMessage::setMessage('Usuario activado con éxito.', 'success');
        }
        $this->redirect('public/index.php?/admin/users');
    }
    
    // --- Métodos privados de ayuda ---

    private function validateUserData(array $data, bool $isEditMode, ?int $userId = null): array
    {
        $errors = [];
        if (empty($data['nombre_usuario'])) $errors[] = "El nombre de usuario es requerido.";
        if (!Sanitizer::validateEmail($data['email'])) $errors[] = "El formato del email no es válido.";
        if (!$isEditMode && empty($data['password'])) {
            $errors[] = "La contraseña es requerida para nuevos usuarios.";
        }

        // Verificar unicidad de nombre de usuario
        $userByUsername = User::findByUsername($data['nombre_usuario']);
        if ($userByUsername && $userByUsername->getId() !== $userId) {
            $errors[] = "El nombre de usuario '" . htmlspecialchars($data['nombre_usuario']) . "' ya está en uso.";
        }

        // Verificar unicidad de email
        $userByEmail = User::findByEmail($data['email']);
        if ($userByEmail && $userByEmail->getId() !== $userId) {
            $errors[] = "El email '" . htmlspecialchars($data['email']) . "' ya está registrado.";
        }
        
        return $errors;
    }

    private function populateUserData(User $user, array $data): void
    {
        $user->setNombreUsuario($data['nombre_usuario'] ?? '');
        $user->setEmail($data['email'] ?? '');
        if (isset($data['rol_id'])) {
            $user->setRolId((int)$data['rol_id']);
        }
        $user->setActivo(isset($data['activo']));
    }
}
