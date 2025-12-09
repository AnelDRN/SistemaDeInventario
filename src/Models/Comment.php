<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;
use App\Helpers\Sanitizer;

class Comment
{
    private ?int $id = null;
    private int $parte_id;
    private int $usuario_id;
    private ?int $parent_id = null;
    private string $texto_comentario;
    private ?string $fecha_creacion;

    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    // --- Getters & Setters ---
    public function getId(): ?int { return $this->id; }
    public function getParteId(): int { return $this->parte_id; }
    public function getUsuarioId(): int { return $this->usuario_id; }
    public function getParentId(): ?int { return $this->parent_id; }
    public function getTextoComentario(): string { return $this->texto_comentario; }
    public function getFechaCreacion(): ?string { return $this->fecha_creacion; }

    public function setParteId(int $id): void { $this->parte_id = $id; }
    public function setUsuarioId(int $id): void { $this->usuario_id = $id; }
    public function setParentId(?int $id): void { $this->parent_id = $id; }
    public function setTextoComentario(string $texto): void { $this->texto_comentario = Sanitizer::sanitizeString($texto); }

    /**
     * Saves a new comment or reply to the database.
     */
    public function save(): bool
    {
        $sql = "INSERT INTO comentarios (parte_id, usuario_id, parent_id, texto_comentario) 
                VALUES (:parte_id, :usuario_id, :parent_id, :texto_comentario)";
        
        $stmt = $this->pdo->prepare($sql);
        
        $params = [
            ':parte_id' => $this->parte_id,
            ':usuario_id' => $this->usuario_id,
            ':parent_id' => $this->parent_id,
            ':texto_comentario' => $this->texto_comentario,
        ];

        return $stmt->execute($params);
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
     * Finds all comments for a specific part ID and organizes them into a threaded structure.
     * Joins with `usuarios` to get the commenter's name.
     */
    public static function findAndThreadByPartId(int $partId): array
    {
        $pdo = Database::getInstance()->getConnection();
        $sql = "SELECT c.*, u.nombre_usuario 
                FROM comentarios c
                JOIN usuarios u ON c.usuario_id = u.id
                WHERE c.parte_id = :part_id
                ORDER BY c.fecha_creacion ASC"; // Order by ASC to build the tree correctly
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':part_id' => $partId]);
        
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Helper to build the tree
        $grouped = [];
        foreach ($comments as $comment) {
            $grouped[$comment['parent_id'] ?? 0][] = $comment;
        }

        $buildTree = function(int $parentId = 0) use (&$grouped, &$buildTree) {
            $branch = [];
            if (isset($grouped[$parentId])) {
                foreach ($grouped[$parentId] as $comment) {
                    $comment['children'] = $buildTree($comment['id']);
                    $branch[] = $comment;
                }
            }
            return $branch;
        };

        return $buildTree(0);
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
        $comment->parent_id = isset($data['parent_id']) ? (int)$data['parent_id'] : null;
        $comment->texto_comentario = $data['texto_comentario'];
        $comment->fecha_creacion = $data['fecha_creacion'];
        return $comment;
    }
     
    public function delete(): bool
    {
        if (!$this->id) return false;
        // The ON DELETE CASCADE constraint on `parent_id` will handle deleting children.
        $stmt = $this->pdo->prepare("DELETE FROM comentarios WHERE id = :id");
        return $stmt->execute([':id' => $this->id]);
    }
}

