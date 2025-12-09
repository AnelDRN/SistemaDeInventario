<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;
use App\Helpers\Sanitizer;

class User
{
    private ?int $id;
    private string $nombre_usuario;
    private string $email;
    private string $password_hash;
    private Role $role; // Added Role object
    private bool $activo;
    private ?string $fecha_creacion;
    private ?string $fecha_actualizacion;

    private PDO $pdo;

    public function __construct(
        ?int $id = null,
        string $nombre_usuario = '',
        string $email = '',
        string $password_hash = '',
        Role $role = new Role(), // Default to a new Role object
        bool $activo = true,
        ?string $fecha_creacion = null,
        ?string $fecha_actualizacion = null
    ) {
        $this->id = $id;
        $this->nombre_usuario = $nombre_usuario;
        $this->email = $email;
        $this->password_hash = $password_hash;
        $this->role = $role;
        $this->activo = $activo;
        $this->fecha_creacion = $fecha_creacion;
        $this->fecha_actualizacion = $fecha_actualizacion;

        $this->pdo = Database::getInstance()->getConnection();
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getNombreUsuario(): string { return $this->nombre_usuario; }
    public function getEmail(): string { return $this->email; }
    public function getPasswordHash(): string { return $this->password_hash; }
    public function getRol(): Role { return $this->role; } // Getter for Role object
    public function getRolId(): int { return $this->role->getId() ?? 0; } // Getter for role ID
    public function isActivo(): bool { return $this->activo; }
    public function getFechaCreacion(): ?string { return $this->fecha_creacion; }
    public function getFechaActualizacion(): ?string { return $this->fecha_actualizacion; }

    // Permission Checkers
    public function canManageUsers(): bool { return $this->role->canManageUsers(); }
    public function canManageRoles(): bool { return $this->role->canManageRoles(); }
    public function canManageSections(): bool { return $this->role->canManageSections(); }
    public function canManageInventory(): bool { return $this->role->canManageInventory(); }
    public function canViewReports(): bool { return $this->role->canViewReports(); }

    // Setters (simplified for now, full validation would be in controller/service)
    public function setNombreUsuario(string $nombre_usuario): void { $this->nombre_usuario = Sanitizer::sanitizeString($nombre_usuario); }
    public function setEmail(string $email): void { $this->email = Sanitizer::sanitizeString($email); }
    public function setPasswordHash(string $password_hash): void { $this->password_hash = $password_hash; } // Hashing done outside model
    public function setRol(Role $role): void { $this->role = $role; } // Setter for Role object
    public function setRolId(int $rol_id): void { $this->role = Role::findById($rol_id) ?? new Role(); } // Setter for role ID, fetches Role object
    public function setActivo(bool $activo): void { $this->activo = $activo; }

    /**
     * Finds a user by their ID.
     * @param int $id
     * @return User|null
     */
    public static function findById(int $id): ?self
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }
        $role = Role::findById($data['rol_id']); // Fetch Role object
        if (!$role) return null; // Should not happen if DB is consistent

        return new self(
            $data['id'],
            $data['nombre_usuario'],
            $data['email'],
            $data['password_hash'],
            $role, // Pass Role object
            (bool)$data['activo'],
            $data['fecha_creacion'],
            $data['fecha_actualizacion']
        );
    }

    /**
     * Finds a user by their username.
     * @param string $username
     * @return User|null
     */
    public static function findByUsername(string $username): ?self
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nombre_usuario = :username");
        $stmt->execute(['username' => Sanitizer::sanitizeString($username)]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }
        $role = Role::findById($data['rol_id']); // Fetch Role object
        if (!$role) return null;

        return new self(
            $data['id'],
            $data['nombre_usuario'],
            $data['email'],
            $data['password_hash'],
            $role, // Pass Role object
            (bool)$data['activo'],
            $data['fecha_creacion'],
            $data['fecha_actualizacion']
        );
    }

    /**
     * Finds a user by their email.
     * @param string $email
     * @return User|null
     */
    public static function findByEmail(string $email): ?self
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => Sanitizer::sanitizeString($email)]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }
        $role = Role::findById($data['rol_id']); // Fetch Role object
        if (!$role) return null;

        return new self(
            $data['id'],
            $data['nombre_usuario'],
            $data['email'],
            $data['password_hash'],
            $role, // Pass Role object
            (bool)$data['activo'],
            $data['fecha_creacion'],
            $data['fecha_actualizacion']
        );
    }

    /**
     * Saves the user data to the database (either inserts or updates).
     * @return bool True on success, false on failure.
     */
    public function save(): bool
    {
        if ($this->id) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }

    private function insert(): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO usuarios (nombre_usuario, email, password_hash, rol_id, activo)
             VALUES (:nombre_usuario, :email, :password_hash, :rol_id, :activo)"
        );
        $result = $stmt->execute([
            'nombre_usuario' => $this->nombre_usuario,
            'email' => $this->email,
            'password_hash' => $this->password_hash,
            'rol_id' => $this->role->getId(), // Use Role object to get ID
            'activo' => $this->activo
        ]);

        if ($result) {
            $this->id = (int)$this->pdo->lastInsertId();
        }
        return $result;
    }

    private function update(): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE usuarios SET
             nombre_usuario = :nombre_usuario,
             email = :email,
             password_hash = :password_hash,
             rol_id = :rol_id,
             activo = :activo,
             fecha_actualizacion = CURRENT_TIMESTAMP
             WHERE id = :id"
        );
        return $stmt->execute([
            'nombre_usuario' => $this->nombre_usuario,
            'email' => $this->email,
            'password_hash' => $this->password_hash,
            'rol_id' => $this->role->getId(), // Use Role object to get ID
            'activo' => $this->activo,
            'id' => $this->id
        ]);
    }

    /**
     * Performs a soft delete on the user by setting 'activo' to false.
     * @return bool True on success, false on failure.
     */
    public function softDelete(): bool
    {
        if ($this->id === null) {
            return false; // Cannot soft delete a user that doesn't exist in DB
        }
        $stmt = $this->pdo->prepare("UPDATE usuarios SET activo = FALSE, fecha_actualizacion = CURRENT_TIMESTAMP WHERE id = :id");
        return $stmt->execute(['id' => $this->id]);
    }

    /**
     * Retrieves all users, optionally filtered by active status.
     * @param bool|null $active Filter by active status (true/false). If null, retrieves all.
     * @return array<User>
     */
    public static function findAll(?bool $active = null): array
    {
        $pdo = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM usuarios";
        $params = [];

        if ($active !== null) {
            $sql .= " WHERE activo = :activo";
            $params['activo'] = $active;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $users = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $role = Role::findById($data['rol_id']); // Fetch Role object
            if (!$role) continue; // Skip if role not found (shouldn't happen)

            $users[] = new self(
                $data['id'],
                $data['nombre_usuario'],
                $data['email'],
                $data['password_hash'],
                $role, // Pass Role object
                (bool)$data['activo'],
                $data['fecha_creacion'],
                $data['fecha_actualizacion']
            );
        }
        return $users;
    }

    /**
     * Counts the total number of users.
     */
    public static function count(): int
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->query("SELECT COUNT(*) FROM usuarios");
        return (int)$stmt->fetchColumn();
    }
}
