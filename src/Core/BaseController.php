<?php
declare(strict_types=1);

namespace App\Core;

abstract class BaseController
{
    protected array $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Renders a view file, including header and footer.
     *
     * @param string $view The view file path (relative to the views directory).
     * @param array $data Data to be extracted for use in the view.
     */
    protected function view(string $view, array $data = []): void
    {
        extract($data);

        // Construct the full path to the view file
        $file = ROOT_PATH . "/views/{$view}.php";

        if (is_readable($file)) {
            // Include a generic public header/footer or an admin one
            if (str_starts_with($view, 'admin/')) {
                require_once ROOT_PATH . '/views/admin/layouts/header.php';
                require $file;
                require_once ROOT_PATH . '/views/admin/layouts/footer.php';
            } else {
                // For public views, we might have a different or no layout.
                // For now, just include the file.
                require $file;
            }
        } else {
            throw new \Exception("La vista $file no fue encontrada.");
        }
    }

    /**
     * Redirects to a different page using an absolute URL.
     *
     * @param string $url The path relative to the project root (e.g., '/public/index.php?/admin/users').
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . BASE_URL . '/' . ltrim($url, '/'));
        exit();
    }

    /**
     * Checks if the user is an admin, otherwise redirects to the login page.
     */
    protected function authorizeAdmin(): void
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
            // Point to the new MVC login route.
            $this->redirect('public/index.php?/login');
        }
    }
}
