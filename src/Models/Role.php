<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

class Role
{
    private ?int $id;
    private string $nombre;

    private PDO $pdo;

    public function __construct(?int $id = null, string $nombre = '')
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->pdo = Database::getInstance()->getConnection();
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    // Setters
    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    /**
     * Finds a role by its ID.
     */
    public static function findById(int $id): ?self
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM roles WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }
        return new self($data['id'], $data['nombre']);
    }

    /**
     * Retrieves all roles from the database.
     */
    public static function findAll(): array
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->query("SELECT * FROM roles ORDER BY id ASC");
        $roles = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $roles[] = new self($data['id'], $data['nombre']);
        }
        return $roles;
    }

    /**
     * Saves the role data to the database (inserts or updates).
     */
    public function save(): bool
    {
        if ($this->id) {
            // Update
            $stmt = $this->pdo->prepare("UPDATE roles SET nombre = :nombre WHERE id = :id");
            return $stmt->execute(['nombre' => $this->nombre, 'id' => $this->id]);
        } else {
            // Insert
            $stmt = $this->pdo->prepare("INSERT INTO roles (nombre) VALUES (:nombre)");
            $result = $stmt->execute(['nombre' => $this->nombre]);
            if ($result) {
                $this->id = (int)$this->pdo->lastInsertId();
            }
            return $result;
        }
    }

    /**
     * Deletes a role from the database.
     * Note: This is a hard delete. We should be careful if roles are in use.
     */
    public function delete(): bool
    {
        if (!$this->id) {
            return false;
        }
        // We should check if any user has this role before deleting.
        // For simplicity now, we just delete.
        $stmt = $this->pdo->prepare("DELETE FROM roles WHERE id = :id");
        return $stmt->execute(['id' => $this->id]);
    }
}
