<?php
declare(strict_types=1);

namespace App\Core;

use App\Interfaces\IErrorHandler;

class ErrorHandler implements IErrorHandler
{
    public function logError(string $message, array $context = []): void
    {
        $logMessage = "Error AplicACIÓN: " . $message;
        if (!empty($context)) {
            $logMessage .= " | Contexto: " . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
        error_log($logMessage);
    }

    public static function handleError(int $severity, string $message, string $file, int $line): bool
    {
        if (!(error_reporting() & $severity)) {
            return false;
        }
        throw new \ErrorException($message, 0, $severity, $file, $line);
    }

    public static function handleException(\Throwable $exception): void
    {
        http_response_code(500);

        $logMessage = sprintf(
            "Excepción no capturada: '%s' en %s:%d\nStack trace:\n%s",
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );
        error_log($logMessage);

        echo "<h1>Error Interno del Servidor</h1>";
        echo "<p>Ocurrió un error inesperado. Por favor, contacte al administrador.</p>";
        
        // Para desarrollo, se podría mostrar más información.
        // echo "<pre>" . htmlspecialchars($logMessage) . "</pre>";
    }
}
