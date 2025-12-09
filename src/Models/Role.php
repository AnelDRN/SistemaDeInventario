<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

class Role
{
    private ?int $id;
    private string $nombre;
    private bool $can_manage_users;
    private bool $can_manage_roles;
    private bool $can_manage_sections;
    private bool $can_manage_inventory;
    private bool $can_view_reports;

    private PDO $pdo;

    public function __construct(
        ?int $id = null, 
        string $nombre = '',
        bool $can_manage_users = false,
        bool $can_manage_roles = false,
        bool $can_manage_sections = false,
        bool $can_manage_inventory = false,
        bool $can_view_reports = false
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->can_manage_users = $can_manage_users;
        $this->can_manage_roles = $can_manage_roles;
        $this->can_manage_sections = $can_manage_sections;
        $this->can_manage_inventory = $can_manage_inventory;
        $this->can_view_reports = $can_view_reports;
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

    public function canManageUsers(): bool
    {
        return $this->can_manage_users;
    }

    public function canManageRoles(): bool
    {
        return $this->can_manage_roles;
    }

    public function canManageSections(): bool
    {
        return $this->can_manage_sections;
    }

    public function canManageInventory(): bool
    {
        return $this->can_manage_inventory;
    }

    public function canViewReports(): bool
    {
        return $this->can_view_reports;
    }

    // Setters (only for nombre for now, permissions are set via constructor/DB)
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
        return new self(
            $data['id'], 
            $data['nombre'],
            (bool)$data['can_manage_users'],
            (bool)$data['can_manage_roles'],
            (bool)$data['can_manage_sections'],
            (bool)$data['can_manage_inventory'],
            (bool)$data['can_view_reports']
        );
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
            $roles[] = new self(
                $data['id'], 
                $data['nombre'],
                (bool)$data['can_manage_users'],
                (bool)$data['can_manage_roles'],
                (bool)$data['can_manage_sections'],
                (bool)$data['can_manage_inventory'],
                (bool)$data['can_view_reports']
            );
        }
        return $roles;
    }

    /**
     * Saves the role data to the database (inserts or updates).
     * This method will need to be updated to handle permissions if roles become editable.
     */
    public function save(): bool
    {
        if ($this->id) {
            // Update
            $stmt = $this->pdo->prepare("UPDATE roles SET 
                nombre = :nombre, 
                can_manage_users = :can_manage_users,
                can_manage_roles = :can_manage_roles,
                can_manage_sections = :can_manage_sections,
                can_manage_inventory = :can_manage_inventory,
                can_view_reports = :can_view_reports
                WHERE id = :id");
            return $stmt->execute([
                'nombre' => $this->nombre, 
                'can_manage_users' => $this->can_manage_users,
                'can_manage_roles' => $this->can_manage_roles,
                'can_manage_sections' => $this->can_manage_sections,
                'can_manage_inventory' => $this->can_manage_inventory,
                'can_view_reports' => $this->can_view_reports,
                'id' => $this->id
            ]);
        } else {
            // Insert
            $stmt = $this->pdo->prepare("INSERT INTO roles (nombre, can_manage_users, can_manage_roles, can_manage_sections, can_manage_inventory, can_view_reports) VALUES (:nombre, :can_manage_users, :can_manage_roles, :can_manage_sections, :can_manage_inventory, :can_view_reports)");
            $result = $stmt->execute([
                'nombre' => $this->nombre,
                'can_manage_users' => $this->can_manage_users,
                'can_manage_roles' => $this->can_manage_roles,
                'can_manage_sections' => $this->can_manage_sections,
                'can_manage_inventory' => $this->can_manage_inventory,
                'can_view_reports' => $this->can_view_reports
            ]);
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
