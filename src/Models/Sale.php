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

    // --- Getters ---
    public function getId(): ?int { return $this->id; }
    public function getPrecioVenta(): float { return $this->precio_venta; }
    
    
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

    /**
     * Retrieves all sales for a given month and year.
     */
    public static function findSalesByMonth(int $year, int $month): array
    {
        $pdo = Database::getInstance()->getConnection();
        $sql = "SELECT v.*, u.nombre_usuario AS vendedor_nombre
                FROM vendido_parte v
                JOIN usuarios u ON v.usuario_vendedor_id = u.id
                WHERE YEAR(v.fecha_venta) = :year AND MONTH(v.fecha_venta) = :month
                ORDER BY v.fecha_venta ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':year' => $year, ':month' => $month]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Calculates total revenue grouped by part category (section) for a given month and year.
     * @return array An associative array with category name and total revenue.
     */
    public static function getTotalRevenueByCategory(int $year, int $month): array
    {
        $pdo = Database::getInstance()->getConnection();
        $sql = "SELECT s.nombre AS category_name, SUM(vp.precio_venta) AS total_revenue
                FROM vendido_parte vp
                JOIN partes p ON vp.parte_original_id = p.id
                JOIN secciones s ON p.seccion_id = s.id
                WHERE YEAR(vp.fecha_venta) = :year AND MONTH(vp.fecha_venta) = :month
                GROUP BY s.nombre
                ORDER BY total_revenue DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':year' => $year, ':month' => $month]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieves the most sold parts for a given month and year.
     * @return array An associative array with part name and quantity sold.
     */
    public static function getMostSoldParts(int $year, int $month, int $limit = 5): array
    {
        $pdo = Database::getInstance()->getConnection();
        $sql = "SELECT vp.nombre_parte, COUNT(vp.id) AS quantity_sold
                FROM vendido_parte vp
                WHERE YEAR(vp.fecha_venta) = :year AND MONTH(vp.fecha_venta) = :month
                GROUP BY vp.nombre_parte
                ORDER BY quantity_sold DESC
                LIMIT :limit";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':year', $year, PDO::PARAM_INT);
        $stmt->bindValue(':month', $month, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
