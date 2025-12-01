<?php

namespace Config;

use PDO;
use PDOException;

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
            // En un entorno de producción, loguear el error y no mostrarlo directamente.
            // Por ahora, para desarrollo, podemos mostrarlo.
            // Deberíamos usar IErrorHandler aquí, pero aún no tenemos una implementación concreta de un logger.
            error_log("Error de conexión a la base de datos: " . $e->getMessage());
            die("Error de conexión a la base de datos.");
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
