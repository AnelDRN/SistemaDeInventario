<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;
use App\Helpers\Sanitizer;

class Comment
{
    private ?int $id;
    private int $parte_id;
    private int $usuario_id; // O podría ser un nombre/email de invitado
    private string $texto_comentario;
    private string $estado; // 'pendiente', 'aprobado', 'rechazado'
    private ?string $fecha_creacion;

    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    // --- Getters & Setters ---
    public function getId(): ?int { return $this->id; }
    public function getParteId(): int { return $this->parte_id; }
    public function getUsuarioId(): int { return $this->usuario_id; }
    public function getTextoComentario(): string { return $this->texto_comentario; }
    public function getEstado(): string { return $this->estado; }
    public function getFechaCreacion(): ?string { return $this->fecha_creacion; }

    public function setParteId(int $id): void { $this->parte_id = $id; }
    public function setUsuarioId(int $id): void { $this->usuario_id = $id; }
    public function setTextoComentario(string $texto): void { $this->texto_comentario = Sanitizer::sanitizeString($texto); }
    public function setEstado(string $estado): void { $this->estado = $estado; }

    /**
     * Saves a new comment to the database.
     */
    public function save(): bool
    {
        // Por defecto, los nuevos comentarios estarán pendientes
        $this->estado = 'pendiente';
        
        $sql = "INSERT INTO comentarios (parte_id, usuario_id, texto_comentario, estado) 
                VALUES (:parte_id, :usuario_id, :texto_comentario, :estado)";
        
        $stmt = $this->pdo->prepare($sql);
        
        $params = [
            ':parte_id' => $this->parte_id,
            ':usuario_id' => $this->usuario_id, // Asumimos que el usuario está logueado
            ':texto_comentario' => $this->texto_comentario,
            ':estado' => $this->estado
        ];

        return $stmt->execute($params);
    }
    
    /**
     * Updates the status of a comment.
     */
    public function updateStatus(string $newStatus): bool
    {
        if (!$this->id) return false;
        
        $allowedStatus = ['aprobado', 'rechazado', 'pendiente'];
        if (!in_array($newStatus, $allowedStatus)) return false;

        $this->estado = $newStatus;
        $stmt = $this->pdo->prepare("UPDATE comentarios SET estado = :estado WHERE id = :id");
        return $stmt->execute([':estado' => $this->estado, ':id' => $this->id]);
    }

    /**
     * Finds a comment by its ID.
     */
    public static function findById(int $id): ?self
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM comentarios WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) return null;

        return self::fromArray($data);
    }
    
    /**
     * Finds all comments for a specific part ID, optionally filtered by status.
     * Joins with `usuarios` to get the commenter's name.
     */
    public static function findAllByPartId(int $partId, string $status = 'aprobado'): array
    {
        $pdo = Database::getInstance()->getConnection();
        $sql = "SELECT c.*, u.nombre_usuario 
                FROM comentarios c
                JOIN usuarios u ON c.usuario_id = u.id
                WHERE c.parte_id = :part_id AND c.estado = :status
                ORDER BY c.fecha_creacion DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':part_id' => $partId, ':status' => $status]);
        
        $comments = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $comments[] = $data; // Devolvemos un array asociativo simple para la vista
        }
        return $comments;
    }
    
    /**
     * Finds all comments, regardless of part or status.
     * Joins with `usuarios` and `partes` for a comprehensive admin view.
     */
    public static function findAllForAdmin(): array
    {
        $pdo = Database::getInstance()->getConnection();
        $sql = "SELECT c.*, u.nombre_usuario, p.nombre as nombre_parte
                FROM comentarios c
                JOIN usuarios u ON c.usuario_id = u.id
                JOIN partes p ON c.parte_id = p.id
                ORDER BY c.fecha_creacion DESC";
        
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Creates a Comment object from an associative array.
     */
    private static function fromArray(array $data): self
    {
        $comment = new self();
        $comment->id = $data['id'];
        $comment->parte_id = $data['parte_id'];
        $comment->usuario_id = $data['usuario_id'];
        $comment->texto_comentario = $data['texto_comentario'];
        $comment->estado = $data['estado'];
        $comment->fecha_creacion = $data['fecha_creacion'];
        return $comment;
    }
     
    public function delete(): bool
    {
        if (!$this->id) return false;
        $stmt = $this->pdo->prepare("DELETE FROM comentarios WHERE id = :id");
        return $stmt->execute([':id' => $this->id]);
    }
}
