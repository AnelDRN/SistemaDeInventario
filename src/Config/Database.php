<?php
declare(strict_types=1);

namespace App\Config;

use PDO;
use PDOException;
use App\Core\ErrorHandler;

class Database
{
    private static ?Database $instance = null;
    private ?PDO $pdo = null;

    private string $host = 'localhost'; // WAMP default
    private string $db_name = 'sistema_rastro'; // To be created
    private string $username = 'root'; // WAMP default
    private string $password = ''; // WAMP default
    private string $charset = 'utf8mb4';

    private function __construct()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            // Relanzar la excepción para que sea capturada por nuestro manejador de excepciones global.
            // Esto detendrá la ejecución y mostrará una página de error amigable.
            throw $e;
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    // Prevenir la clonación
    private function __clone() {}

    // Prevenir la deserialización
    public function __wakeup()
    {
        throw new \Exception("Cannot deserialize a singleton.");
    }
}
