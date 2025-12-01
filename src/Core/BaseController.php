<?php

namespace App\Core;

class BaseController
{
    /**
     * Renders a view file.
     *
     * @param string $viewName The name of the view file (e.g., 'admin/users/index').
     * @param array $data Data to pass to the view.
     */
    protected function view(string $viewName, array $data = []): void
    {
        // Define path to views directory. Assumes project root is 'sistema-rastro'.
        $viewPath = __DIR__ . '/../../views/' . $viewName . '.php';

        if (file_exists($viewPath)) {
            // Extract data array to make variables accessible in the view
            extract($data);
            require_once $viewPath;
        } else {
            // Handle view not found error. In a real app, this would be more robust.
            // Possibly use an error handler here.
            echo "Error: View '{$viewName}' not found.";
            error_log("View not found: " . $viewPath);
        }
    }

    /**
     * Redirects to a specified URL.
     *
     * @param string $url The URL to redirect to.
     */
    protected function redirect(string $url): void
    {
        header("Location: " . $url);
        exit();
    }
}
