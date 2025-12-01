<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\User;
use App\Helpers\Sanitizer;

class UserController extends BaseController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Shows the login form.
     */
    public function showLoginForm(): void
    {
        $this->view('admin/login');
    }

    /**
     * Handles user login.
     */
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = Sanitizer::sanitizeString($_POST['username'] ?? '');
            $password = $_POST['password'] ?? ''; // Password not sanitized before hashing/verification

            $user = User::findByUsername($username);

            if ($user && password_verify($password, $user->getPasswordHash()) && $user->isActivo()) {
                // Login successful
                // In a real application, start session, set cookies, etc.
                session_start();
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['username'] = $user->getNombreUsuario();
                $_SESSION['role_id'] = $user->getRolId();

                // Redirect to admin dashboard or home
                $this->redirect('/admin/dashboard'); // Assuming a dashboard route
            } else {
                // Login failed
                $this->view('admin/login', ['error' => 'Usuario o contraseña incorrectos, o usuario inactivo.']);
            }
        } else {
            $this->redirect('/login'); // Redirect to show form on GET request
        }
    }

    /**
     * Logs out the user.
     */
    public function logout(): void
    {
        session_start();
        session_unset();
        session_destroy();
        $this->redirect('/login');
    }

    /**
     * Shows a list of users (Admin only).
     */
    public function index(): void
    {
        // Basic authorization check (e.g., must be admin)
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) { // Assuming role_id 1 is Admin
            $this->redirect('/login');
            return;
        }

        $users = User::findAll();
        $this->view('admin/users/index', ['users' => $users]);
    }

    /**
     * Shows the form to create a new user.
     */
    public function create(): void
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
            $this->redirect('/login');
            return;
        }
        $this->view('admin/users/create_edit'); // Use a generic form for create/edit
    }

    /**
     * Stores a new user or updates an existing one.
     */
    public function store(): void
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
            $this->redirect('/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = Sanitizer::sanitizeArray($_POST); // Sanitize all POST data

            $id = $data['id'] ?? null;
            $username = $data['username'] ?? '';
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';
            $rol_id = $data['rol_id'] ?? 2; // Default to Vendedor if not specified
            $activo = isset($data['activo']) && $data['activo'] === 'on' ? true : false;

            // Basic validation
            $errors = [];
            if (empty($username)) $errors[] = "El nombre de usuario es requerido.";
            if (!Sanitizer::validateEmail($email)) $errors[] = "El email no es válido.";
            if (empty($password) && $id === null) $errors[] = "La contraseña es requerida para un nuevo usuario.";

            if (empty($errors)) {
                $user = ($id !== null) ? User::findById((int)$id) : new User();
                if (!$user && $id !== null) {
                    $errors[] = "Usuario a actualizar no encontrado.";
                } elseif ($user) {
                    $user->setNombreUsuario($username);
                    $user->setEmail($email);
                    if (!empty($password)) { // Only update password if provided
                        $user->setPasswordHash(password_hash($password, PASSWORD_DEFAULT));
                    }
                    $user->setRolId((int)$rol_id);
                    $user->setActivo($activo);

                    if ($user->save()) {
                        $this->redirect('/admin/users');
                    } else {
                        $errors[] = "Error al guardar el usuario.";
                    }
                }
            }
            $this->view('admin/users/create_edit', ['errors' => $errors, 'user' => $_POST]); // Pass back original POST data for form repopulation
        } else {
            $this->redirect('/admin/users');
        }
    }

    /**
     * Shows the form to edit an existing user.
     * @param int $id User ID.
     */
    public function edit(int $id): void
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
            $this->redirect('/login');
            return;
        }

        $user = User::findById($id);
        if (!$user) {
            $this->redirect('/admin/users'); // User not found
            return;
        }
        $this->view('admin/users/create_edit', ['user' => $user]);
    }

    /**
     * Deactivates a user (soft delete).
     * @param int $id User ID.
     */
    public function deactivate(int $id): void
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
            $this->redirect('/login');
            return;
        }

        $user = User::findById($id);
        if ($user && $user->softDelete()) {
            // Success
        }
        $this->redirect('/admin/users');
    }

    /**
     * Activates a user.
     * @param int $id User ID.
     */
    public function activate(int $id): void
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
            $this->redirect('/login');
            return;
        }

        $user = User::findById($id);
        if ($user) {
            $user->setActivo(true);
            $user->save();
        }
        $this->redirect('/admin/users');
    }
}
