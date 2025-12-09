<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;
use App\Helpers\Sanitizer;

class Part
{
    private ?int $id;
    private string $nombre;
    private ?string $descripcion;
    private ?string $tipo_parte;
    private ?string $marca_auto;
    private ?string $modelo_auto;
    private ?int $anio_auto;
    private float $precio;
    private int $cantidad_disponible;
    private ?string $imagen_url;
    private ?string $thumbnail_url;
    private int $seccion_id;
    private ?string $fecha_creacion;
    private ?string $fecha_actualizacion;

    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
        // Inicializar propiedades para evitar errores de "uninitialized" en PHP 8+
        $this->id = null;
        $this->nombre = '';
        $this->descripcion = null;
        $this->tipo_parte = null;
        $this->marca_auto = null;
        $this->modelo_auto = null;
        $this->anio_auto = null;
        $this->precio = 0.0;
        $this->cantidad_disponible = 0;
        $this->imagen_url = null;
        $this->thumbnail_url = null;
        $this->seccion_id = 0;
        $this->fecha_creacion = null;
        $this->fecha_actualizacion = null;
    }

    // --- Getters ---
    public function getId(): ?int { return $this->id; }
    public function getNombre(): string { return $this->nombre; }
    public function getDescripcion(): ?string { return $this->descripcion; }
    public function getTipoParte(): ?string { return $this->tipo_parte; }
    public function getMarcaAuto(): ?string { return $this->marca_auto; }
    public function getModeloAuto(): ?string { return $this->modelo_auto; }
    public function getAnioAuto(): ?int { return $this->anio_auto; }
    public function getPrecio(): float { return $this->precio; }
    public function getCantidadDisponible(): int { return $this->cantidad_disponible; }
    public function getImagenUrl(): ?string { return $this->imagen_url; }
    public function getThumbnailUrl(): ?string { return $this->thumbnail_url; }
    public function getSeccionId(): int { return $this->seccion_id; }
    public function getFechaCreacion(): ?string { return $this->fecha_creacion; }
    public function getFechaActualizacion(): ?string { return $this->fecha_actualizacion; }

    // --- Setters ---
    public function setId(?int $id): void { $this->id = $id; }
    public function setNombre(string $nombre): void { $this->nombre = Sanitizer::sanitizeString($nombre); }
    public function setDescripcion(?string $descripcion): void { $this->descripcion = $descripcion ? Sanitizer::sanitizeString($descripcion) : null; }
    public function setTipoParte(?string $tipo_parte): void { $this->tipo_parte = $tipo_parte ? Sanitizer::sanitizeString($tipo_parte) : null; }
    public function setMarcaAuto(?string $marca_auto): void { $this->marca_auto = $marca_auto ? Sanitizer::sanitizeString($marca_auto) : null; }
    public function setModeloAuto(?string $modelo_auto): void { $this->modelo_auto = $modelo_auto ? Sanitizer::sanitizeString($modelo_auto) : null; }
    public function setAnioAuto(?int $anio_auto): void { $this->anio_auto = $anio_auto; }
    public function setPrecio(float $precio): void { $this->precio = $precio; }
    public function setCantidadDisponible(int $cantidad): void { $this->cantidad_disponible = $cantidad; }
    public function setImagenUrl(?string $url): void { $this->imagen_url = $url; }
    public function setThumbnailUrl(?string $url): void { $this->thumbnail_url = $url; }
    public function setSeccionId(int $id): void { $this->seccion_id = $id; }

    /**
     * Populates the object from an associative array (e.g., from a DB fetch).
     */
    private function fromArray(array $data): void
    {
        $this->id = $data['id'] ?? null;
        $this->nombre = $data['nombre'] ?? '';
        $this->descripcion = $data['descripcion'] ?? null;
        $this->tipo_parte = $data['tipo_parte'] ?? null;
        $this->marca_auto = $data['marca_auto'] ?? null;
        $this->modelo_auto = $data['modelo_auto'] ?? null;
        $this->anio_auto = isset($data['anio_auto']) ? (int)$data['anio_auto'] : null;
        $this->precio = isset($data['precio']) ? (float)$data['precio'] : 0.0;
        $this->cantidad_disponible = isset($data['cantidad_disponible']) ? (int)$data['cantidad_disponible'] : 0;
        $this->imagen_url = $data['imagen_url'] ?? null;
        $this->thumbnail_url = $data['thumbnail_url'] ?? null;
        $this->seccion_id = (int)$data['seccion_id'];
        $this->fecha_creacion = $data['fecha_creacion'] ?? null;
        $this->fecha_actualizacion = $data['fecha_actualizacion'] ?? null;
    }

    public static function findById(int $id): ?self
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM partes WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) return null;

        $part = new self();
        $part->fromArray($data);
        return $part;
    }

    public static function findAll(?string $partType = null): array
    {
        $pdo = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM partes";
        $params = [];
        if ($partType !== null && $partType !== '') {
            $sql .= " WHERE tipo_parte = :tipo_parte";
            $params[':tipo_parte'] = $partType;
        }
        $sql .= " ORDER BY fecha_creacion DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $parts = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $part = new self();
            $part->fromArray($data);
            $parts[] = $part;
        }
        return $parts;
    }

    public static function search(string $searchTerm, ?string $partType = null): array
    {
        $pdo = Database::getInstance()->getConnection();
        $query = "SELECT * FROM partes 
                  WHERE (nombre LIKE :term_nombre 
                     OR tipo_parte LIKE :term_tipo 
                     OR marca_auto LIKE :term_marca 
                     OR modelo_auto LIKE :term_modelo)";
        $params = [
            ':term_nombre' => '%' . $searchTerm . '%',
            ':term_tipo' => '%' . $searchTerm . '%',
            ':term_marca' => '%' . $searchTerm . '%',
            ':term_modelo' => '%' . $searchTerm . '%'
        ];

        if ($partType !== null && $partType !== '') {
            $query .= " AND tipo_parte = :tipo_parte";
            $params[':tipo_parte'] = $partType;
        }
        $query .= " ORDER BY fecha_creacion DESC";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        
        $parts = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $part = new self();
            $part->fromArray($data);
            $parts[] = $part;
        }
        return $parts;
    }

    /**
     * Finds all unique part types available in the inventory.
     * @return array<string> An array of unique part type strings.
     */
    public static function findUniquePartTypes(): array
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->query("SELECT DISTINCT tipo_parte FROM partes WHERE tipo_parte IS NOT NULL AND tipo_parte != '' ORDER BY tipo_parte ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Finds all parts, optionally filtered by section ID.
     */
    public static function findAllBySection(?int $sectionId = null): array
    {
        $pdo = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM partes";
        $params = [];
        if ($sectionId !== null) {
            $sql .= " WHERE seccion_id = :seccion_id";
            $params[':seccion_id'] = $sectionId;
        }
        $sql .= " ORDER BY fecha_creacion DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $parts = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $part = new self();
            $part->fromArray($data);
            $parts[] = $part;
        }
        return $parts;
    }

    /**
     * Searches parts by term, optionally filtered by section ID.
     */
    public static function searchBySection(string $searchTerm, ?int $sectionId = null): array
    {
        $pdo = Database::getInstance()->getConnection();
        $query = "SELECT * FROM partes 
                  WHERE (nombre LIKE :term 
                     OR tipo_parte LIKE :term 
                     OR marca_auto LIKE :term 
                     OR modelo_auto LIKE :term)";
        
        $params = [':term' => '%' . $searchTerm . '%'];

        if ($sectionId !== null) {
            $query .= " AND seccion_id = :seccion_id";
            $params[':seccion_id'] = $sectionId; // FIX: Add the parameter to the array
        }
        $query .= " ORDER BY fecha_creacion DESC";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        
        $parts = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $part = new self();
            $part->fromArray($data);
            $parts[] = $part;
        }

        return $parts;
    }

    /**
     * Counts the total number of unique parts in inventory.
     */
    public static function count(): int
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->query("SELECT COUNT(*) FROM partes");
        return (int)$stmt->fetchColumn();
    }

    /**
     * Calculates the total value of all parts in inventory.
     */
    public static function totalValue(): float
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->query("SELECT SUM(precio * cantidad_disponible) FROM partes");
        return (float)$stmt->fetchColumn();
    }

    public function save(): bool
    {
        $sql = $this->id ? $this->getUpdateSql() : $this->getInsertSql();
        $stmt = $this->pdo->prepare($sql);
        $params = $this->getSqlParams();
        
        $result = $stmt->execute($params);

        if ($result && !$this->id) {
            $this->id = (int)$this->pdo->lastInsertId();
        }
        return $result;
    }

    public function delete(): bool
    {
        if (!$this->id) return false;
        // Also delete images from filesystem before deleting the record
        // This logic should be in the controller/action script
        $stmt = $this->pdo->prepare("DELETE FROM partes WHERE id = :id");
        return $stmt->execute(['id' => $this->id]);
    }
    
    private function getSqlParams(): array
    {
        $params = [
            ':nombre' => $this->nombre,
            ':descripcion' => $this->descripcion,
            ':tipo_parte' => $this->tipo_parte,
            ':marca_auto' => $this->marca_auto,
            ':modelo_auto' => $this->modelo_auto,
            ':anio_auto' => $this->anio_auto,
            ':precio' => $this->precio,
            ':cantidad_disponible' => $this->cantidad_disponible,
            ':imagen_url' => $this->imagen_url,
            ':thumbnail_url' => $this->thumbnail_url,
            ':seccion_id' => $this->seccion_id,
        ];
        if ($this->id) {
            $params[':id'] = $this->id;
        }
        return $params;
    }

    private function getInsertSql(): string
    {
        return "INSERT INTO `partes` (`nombre`, `descripcion`, `tipo_parte`, `marca_auto`, `modelo_auto`, `anio_auto`, `precio`, `cantidad_disponible`, `imagen_url`, `thumbnail_url`, `seccion_id`) 
                VALUES (:nombre, :descripcion, :tipo_parte, :marca_auto, :modelo_auto, :anio_auto, :precio, :cantidad_disponible, :imagen_url, :thumbnail_url, :seccion_id)";
    }

    private function getUpdateSql(): string
    {
        return "UPDATE `partes` SET 
                    `nombre` = :nombre, 
                    `descripcion` = :descripcion, 
                    `tipo_parte` = :tipo_parte, 
                    `marca_auto` = :marca_auto, 
                    `modelo_auto` = :modelo_auto, 
                    `anio_auto` = :anio_auto, 
                    `precio` = :precio, 
                    `cantidad_disponible` = :cantidad_disponible, 
                    `imagen_url` = :imagen_url, 
                    `thumbnail_url` = :thumbnail_url, 
                    `seccion_id` = :seccion_id
                WHERE `id` = :id";
    }

    /**
     * Decrements the stock for the current part.
     */
    public function decrementStock(int $amount = 1): bool
    {
        if (!$this->id || $this->cantidad_disponible < $amount) {
            return false;
        }
        
        $newQuantity = $this->cantidad_disponible - $amount;
        
        $stmt = $this->pdo->prepare("UPDATE partes SET cantidad_disponible = :cantidad WHERE id = :id");
        $result = $stmt->execute(['cantidad' => $newQuantity, 'id' => $this->id]);
        
        if ($result) {
            $this->cantidad_disponible = $newQuantity;
        }
        
        return $result;
    }
}
