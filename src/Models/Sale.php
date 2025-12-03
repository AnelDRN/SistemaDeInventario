<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;
use App\Helpers\Sanitizer;

class Sale
{
    private ?int $id;
    private int $parte_original_id;
    private string $nombre_parte;
    private float $precio_venta;
    private int $usuario_vendedor_id;
    private ?string $fecha_venta;

    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }
    
    // --- Setters ---
    public function setParteOriginalId(int $id): void { $this->parte_original_id = $id; }
    public function setNombreParte(string $nombre): void { $this->nombre_parte = Sanitizer::sanitizeString($nombre); }
    public function setPrecioVenta(float $precio): void { $this->precio_venta = $precio; }
    public function setUsuarioVendedorId(int $id): void { $this->usuario_vendedor_id = $id; }

    /**
     * Saves the sale record to the database.
     */
    public function save(): bool
    {
        $sql = "INSERT INTO vendido_parte (parte_original_id, nombre_parte, precio_venta, usuario_vendedor_id) 
                VALUES (:parte_original_id, :nombre_parte, :precio_venta, :usuario_vendedor_id)";
        
        $stmt = $this->pdo->prepare($sql);
        
        $params = [
            ':parte_original_id' => $this->parte_original_id,
            ':nombre_parte' => $this->nombre_parte,
            ':precio_venta' => $this->precio_venta,
            ':usuario_vendedor_id' => $this->usuario_vendedor_id
        ];

        $result = $stmt->execute($params);

        if ($result) {
            $this->id = (int)$this->pdo->lastInsertId();
        }
        return $result;
    }
    
    public static function count(): int
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->query("SELECT COUNT(*) FROM vendido_parte");
        return (int)$stmt->fetchColumn();
    }

    public static function totalRevenue(): float
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->query("SELECT SUM(precio_venta) FROM vendido_parte");
        return (float)$stmt->fetchColumn();
    }
    
    /**
     * Retrieves the most recent sales records.
     */
    public static function findRecent(int $limit = 5): array
    {
        $pdo = Database::getInstance()->getConnection();
        // Unir con la tabla de usuarios para obtener el nombre del vendedor
        $sql = "SELECT v.*, u.nombre_usuario AS vendedor_nombre
                FROM vendido_parte v
                JOIN usuarios u ON v.usuario_vendedor_id = u.id
                ORDER BY v.fecha_venta DESC
                LIMIT :limit";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
