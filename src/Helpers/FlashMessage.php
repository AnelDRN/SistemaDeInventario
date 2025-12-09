<?php
declare(strict_types=1);

namespace App\Helpers;

class FlashMessage
{
    private const FLASH_KEY = 'flash_messages';

    /**
     * Añade un mensaje flash a la cola. Mantiene compatibilidad con el nombre anterior.
     */
    public static function setMessage(string $type, string $message): void
    {
        if (!isset($_SESSION[self::FLASH_KEY])) {
            $_SESSION[self::FLASH_KEY] = [];
        }
        if (!isset($_SESSION[self::FLASH_KEY][$type])) {
            $_SESSION[self::FLASH_KEY][$type] = [];
        }
        $_SESSION[self::FLASH_KEY][$type][] = $message;
    }

    /**
     * Establece un array completo de mensajes para un tipo.
     */
    public static function setMessages(string $type, array $messages): void
    {
        if (!isset($_SESSION[self::FLASH_KEY])) {
            $_SESSION[self::FLASH_KEY] = [];
        }
        $_SESSION[self::FLASH_KEY][$type] = $messages;
    }
    
    /**
     * Obtiene los mensajes de un tipo y los limpia de la sesión.
     */
    public static function getMessages(string $type): ?array
    {
        if (isset($_SESSION[self::FLASH_KEY][$type])) {
            $messages = $_SESSION[self::FLASH_KEY][$type];
            unset($_SESSION[self::FLASH_KEY][$type]);
            if (empty($_SESSION[self::FLASH_KEY])) {
                unset($_SESSION[self::FLASH_KEY]);
            }
            return $messages;
        }
        return null;
    }

    /**
     * Muestra todos los mensajes flash agrupados por tipo.
     * Renombrado desde displayMessage para mayor claridad.
     */
    public static function displayAllMessages(): void
    {
        if (!isset($_SESSION[self::FLASH_KEY])) {
            return;
        }

        foreach ($_SESSION[self::FLASH_KEY] as $type => $messages) {
            if (empty($messages)) continue;
            
            $type = htmlspecialchars($type);
            echo "<div class='alert alert-{$type} alert-dismissible fade show' role='alert'>";
            if (count($messages) > 1) {
                echo '<ul>';
                foreach ($messages as $message) {
                    echo '<li>' . htmlspecialchars($message) . '</li>';
                }
                echo '</ul>';
            } else {
                echo htmlspecialchars($messages[0]);
            }
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
        }

        unset($_SESSION[self::FLASH_KEY]);
    }
}
