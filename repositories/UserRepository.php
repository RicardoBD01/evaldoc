<?php
// evaldoc/repositories/UserRepository.php
declare(strict_types=1);

require_once __DIR__ . '/BaseRepository.php';

final class UserRepository extends BaseRepository
{
    public function emailExists(string $correo, ?int $excludeUserId = null): bool
    {
        $sql = "SELECT 1 FROM usuarios WHERE correo = :correo";
        $params = [':correo' => $correo];

        if ($excludeUserId !== null) {
            $sql .= " AND id <> :id";
            $params[':id'] = $excludeUserId;
        }

        $sql .= " LIMIT 1";
        $stmt = $this->query($sql, $params);
        return (bool) $stmt->fetchColumn();
    }

    public function insert(
        string $nombre,
        string $apaterno,
        ?string $amaterno,
        string $correo,
        string $passHash,
        int $rol
    ): int {
        $sql = "INSERT INTO usuarios (nombre, apaterno, amaterno, correo, pass, rol, activo)
                VALUES (:nombre, :apaterno, :amaterno, :correo, :pass, :rol, true)
                RETURNING id";

        $stmt = $this->query($sql, [
            ':nombre' => $nombre,
            ':apaterno' => $apaterno,
            ':amaterno' => $amaterno,
            ':correo' => $correo,
            ':pass' => $passHash,
            ':rol' => $rol,
        ]);

        return (int) $stmt->fetchColumn();
    }

    public function update(
        int $id,
        string $nombre,
        string $apaterno,
        ?string $amaterno,
        string $correo,
        int $rol,
        ?string $passHash = null
    ): int {
        if ($passHash !== null) {
            $sql = "UPDATE usuarios
                    SET nombre = :nombre,
                        apaterno = :apaterno,
                        amaterno = :amaterno,
                        correo = :correo,
                        rol = :rol,
                        pass = :pass
                    WHERE id = :id AND activo = true";
            $params = [
                ':nombre' => $nombre,
                ':apaterno' => $apaterno,
                ':amaterno' => $amaterno,
                ':correo' => $correo,
                ':rol' => $rol,
                ':pass' => $passHash,
                ':id' => $id,
            ];
        } else {
            $sql = "UPDATE usuarios
                    SET nombre = :nombre,
                        apaterno = :apaterno,
                        amaterno = :amaterno,
                        correo = :correo,
                        rol = :rol
                    WHERE id = :id AND activo = true";
            $params = [
                ':nombre' => $nombre,
                ':apaterno' => $apaterno,
                ':amaterno' => $amaterno,
                ':correo' => $correo,
                ':rol' => $rol,
                ':id' => $id,
            ];
        }

        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    public function setPasswordHash(int $id, string $passHash): int
    {
        $sql = "UPDATE usuarios SET pass = :pass WHERE id = :id AND activo = true";
        $stmt = $this->query($sql, [':pass' => $passHash, ':id' => $id]);
        return $stmt->rowCount();
    }

    public function setActive(int $id, bool $active): int
    {
        $sql = "UPDATE usuarios SET activo = :activo WHERE id = :id";
        $stmt = $this->query($sql, [':activo' => $active, ':id' => $id]);
        return $stmt->rowCount();
    }

    public function findById(int $id, bool $onlyActive = true): ?array
    {
        $sql = "SELECT id, nombre, apaterno, amaterno, correo, rol, activo
                FROM usuarios
                WHERE id = :id" . ($onlyActive ? " AND activo = true" : "") . "
                LIMIT 1";

        $stmt = $this->query($sql, [':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function findByEmail(string $correo, bool $onlyActive = true): ?array
    {
        $sql = "SELECT id, nombre, apaterno, amaterno, correo, pass, rol, activo, must_change_pass
            FROM usuarios
            WHERE correo = :correo" . ($onlyActive ? " AND activo = true" : "") . "
            LIMIT 1";

        $stmt = $this->query($sql, [':correo' => $correo]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function getAllActive(): array
    {
        $sql = "
            SELECT u.id, u.nombre, u.apaterno, u.amaterno, u.correo, r.rol
            FROM usuarios u
            INNER JOIN roles r ON r.id = u.rol
            WHERE u.activo = true
            ORDER BY u.id ASC
        ";
        $stmt = $this->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getAllInactive(): array
    {
        $sql = "
            SELECT u.id, u.nombre, u.apaterno, u.amaterno, u.correo, r.rol
            FROM usuarios u
            INNER JOIN roles r ON r.id = u.rol
            WHERE u.activo = false
            ORDER BY u.id ASC
        ";
        $stmt = $this->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function setMustChangePass(int $id, bool $value): int
    {
        $stmt = $this->query(
            "UPDATE usuarios SET must_change_pass = :v WHERE id = :id",
            [':v' => $value, ':id' => $id]
        );
        return $stmt->rowCount();
    }

    public function setPasswordAndClearFlag(int $id, string $passHash): int
    {
        $stmt = $this->query(
            "UPDATE usuarios
         SET pass = :p, must_change_pass = false
         WHERE id = :id AND activo = true",
            [':p' => $passHash, ':id' => $id]
        );
        return $stmt->rowCount();
    }

}
