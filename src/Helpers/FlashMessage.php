<?php
declare(strict_types=1);

namespace App\Helpers;

class FlashMessage
{
    /**
     * Establece un mensaje flash en la sesión.
     * @param string $message El mensaje a mostrar.
     * @param string $type El tipo de alerta de Bootstrap ('success', 'danger', 'warning', 'info').
     */
    public static function setMessage(string $message, string $type = 'success'): void
    {
        $_SESSION['flash_message'] = [
            'message' => $message,
            'type' => $type
        ];
    }

    /**
     * Muestra el mensaje flash si existe y luego lo elimina de la sesión.
     * Debe ser llamado en el layout o la vista donde se quiera mostrar el mensaje.
     */
    public static function displayMessage(): void
    {
        if (isset($_SESSION['flash_message'])) {
            $flash = $_SESSION['flash_message'];
            $message = htmlspecialchars($flash['message']);
            $type = htmlspecialchars($flash['type']);

            echo "<div class='alert alert-{$type} alert-dismissible fade show' role='alert'>";
            echo $message;
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo "</div>";

            // Eliminar el mensaje de la sesión para que no se muestre de nuevo
            unset($_SESSION['flash_message']);
        }
    }
}
