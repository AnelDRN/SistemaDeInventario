<?php

namespace Config;

class Database {
    private static $instance = null;
    private $connection;

    private $host = 'localhost'; // Replace with your DB host
    private $db_name = 'sistema_rastro'; // Replace with your DB name
    private $username = 'root'; // Replace with your DB username
    private $password = ''; // Replace with your DB password
    private $charset = 'utf8mb4';

    private function __construct() {
        $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
        $options = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->connection = new \PDO($dsn, $this->username, $this->password, $options);
        } catch (\PDOException $e) {
            // For now, re-throw the exception. Later, we can integrate an ILogger interface.
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): \PDO {
        return $this->connection;
    }
}
