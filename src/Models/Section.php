<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;
use App\Helpers\Sanitizer;

class Section
{
    private ?int $id;
    private string $nombre;
    private ?string $descripcion;

    private PDO $pdo;

    public function __construct(?int $id = null, string $nombre = '', ?string $descripcion = null)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
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

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    // Setters
    public function setNombre(string $nombre): void
    {
        $this->nombre = Sanitizer::sanitizeString($nombre);
    }

    public function setDescripcion(?string $descripcion): void
    {
        $this->descripcion = $descripcion ? Sanitizer::sanitizeString($descripcion) : null;
    }

    /**
     * Finds a section by its ID.
     */
    public static function findById(int $id): ?self
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM secciones WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }
        return new self($data['id'], $data['nombre'], $data['descripcion']);
    }

    /**
     * Retrieves all sections from the database.
     */
    public static function findAll(): array
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->query("SELECT * FROM secciones ORDER BY nombre ASC");
        $sections = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sections[] = new self($data['id'], $data['nombre'], $data['descripcion']);
        }
        return $sections;
    }

    /**
     * Saves the section data to the database (inserts or updates).
     */
    public function save(): bool
    {
        if ($this->id) {
            // Update
            $stmt = $this->pdo->prepare("UPDATE secciones SET nombre = :nombre, descripcion = :descripcion WHERE id = :id");
            return $stmt->execute(['nombre' => $this->nombre, 'descripcion' => $this->descripcion, 'id' => $this->id]);
        } else {
            // Insert
            $stmt = $this->pdo->prepare("INSERT INTO secciones (nombre, descripcion) VALUES (:nombre, :descripcion)");
            $result = $stmt->execute(['nombre' => $this->nombre, 'descripcion' => $this->descripcion]);
            if ($result) {
                $this->id = (int)$this->pdo->lastInsertId();
            }
            return $result;
        }
    }

    /**
     * Deletes a section from the database.
     */
    public function delete(): bool
    {
        if (!$this->id) {
            return false;
        }
        // Before deleting, we should check if any 'partes' are using this section.
        // For now, we'll proceed with the delete for simplicity.
        $stmt = $this->pdo->prepare("DELETE FROM secciones WHERE id = :id");
        return $stmt->execute(['id' => $this->id]);
    }
}
