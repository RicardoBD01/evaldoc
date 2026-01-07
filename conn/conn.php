<?php
// EVALDOC/conn/conn.php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';

class Principal
{
    private PDO $pdo;

    public function __construct()
    {
        // Solo se llama una vez: aquí ya tenemos la conexión lista
        $this->pdo = db();
    }

    /**
     * Método interno para ejecutar consultas preparadas
     */
    private function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /* =====================
       MÉTODOS CRUD BASE
       ===================== */

    /**
     * Inserta un usuario
     * @param string $nombre
     * @param string $apaterno
     * @param mixed $amaterno
     * @param string $correo
     * @param string $passHash
     * @param int $rol
     * @return array{message: mixed, success: bool|array{message: string, success: bool}}
     */
    public function insertarUsuario(
        string $nombre,
        string $apaterno,
        ?string $amaterno,
        string $correo,
        string $passHash,
        int $rol
    ): array {

        $val = $this->validarCorreoUnico($correo);
        if (!$val['ok']) {
            return ['success' => false, 'message' => $val['message']];
        }

        try {
            $sql = "INSERT INTO usuarios (nombre, apaterno, amaterno, correo, pass, rol)
                VALUES (:nombre, :apaterno, :amaterno, :correo, :pass, :rol)";

            $this->query($sql, [
                ':nombre' => $nombre,
                ':apaterno' => $apaterno,
                ':amaterno' => $amaterno,
                ':correo' => $correo,
                ':pass' => $passHash,
                ':rol' => $rol,
            ]);

            return ['success' => true, 'message' => 'Usuario agregado correctamente.'];

        } catch (PDOException $e) {
            // Backup por si la UNIQUE constraint dispara
            if ($e->getCode() === '23505') {
                return ['success' => false, 'message' => 'El correo ya está registrado.'];
            }
            return ['success' => false, 'message' => 'Error al insertar el usuario.'];
        }
    }

    /**
     * Actualiza un usuario
     * @param int $id
     * @param string $nombre
     * @param string $apaterno
     * @param mixed $amaterno
     * @param string $correo
     * @param int $rol
     * @param mixed $passHash
     * @return array{message: string, success: bool}
     */
    public function actualizarUsuario(
        int $id,
        string $nombre,
        string $apaterno,
        ?string $amaterno,
        string $correo,
        int $rol,
        ?string $passHash // si es null, no cambia pass
    ): array {

        // Validar correo único excluyendo este usuario
        if ($this->correoExiste($correo, $id)) {
            return ["success" => false, "message" => "El correo ya está registrado."];
        }

        try {
            if ($passHash !== null) {
                $sql = "UPDATE usuarios
                    SET nombre = :nombre,
                        apaterno = :apaterno,
                        amaterno = :amaterno,
                        correo = :correo,
                        rol = :rol,
                        pass = :pass
                    WHERE id = :id
                    AND activo = true";
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
                    WHERE id = :id
                    AND activo = true";
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

            if ($stmt->rowCount() === 0) {
                return ["success" => false, "message" => "El usuario no existe o está desactivado."];
            }

            return [
                "success" => true,
                "message" => "Usuario actualizado correctamente."
            ];

        } catch (PDOException $e) {
            // Backup por UNIQUE constraint (recomendado tenerla en DB)
            if ($e->getCode() === '23505') {
                return ["success" => false, "message" => "El correo ya está registrado."];
            }
            return ["success" => false, "message" => "Error al actualizar el usuario."];
        }
    }

    /**
     * Actualiza la contraseña de un usuario
     * @param int $id
     * @param string $passHash
     * @return array{message: string, success: bool}
     */
    public function actualizarContrasenaUsuario(int $id, string $passHash): array
    {
        try {
            $sql = "UPDATE usuarios SET pass = :pass WHERE id = :id AND activo = true";
            $stmt = $this->query($sql, [
                ':pass' => $passHash,
                ':id' => $id,
            ]);

            if ($stmt->rowCount() === 0) {
                return ["success" => false, "message" => "El usuario no existe o está desactivado."];
            }
            return [
                "success" => true,
                "message" => "Contraseña actualizada correctamente."
            ];
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "No se pudo actualizar la contraseña."
            ];
        }
    }

    /**
     * "Elimina" un usuario
     * @param int $id
     * @return array{message: string, success: bool}
     */
    public function desactivarUsuario(int $id): array
    {
        try {
            // Validar existencia y que esté activo
            $stmt = $this->query(
                "SELECT 1 FROM usuarios WHERE id = :id AND activo = true",
                [':id' => $id]
            );

            if (!$stmt->fetchColumn()) {
                return ["success" => false, "message" => "El usuario no existe o ya fue desactivado."];
            }

            $this->query(
                "UPDATE usuarios SET activo = false WHERE id = :id",
                [':id' => $id]
            );

            return ["success" => true, "message" => "Usuario desactivado correctamente."];

        } catch (PDOException $e) {
            return ["success" => false, "message" => "Error al desactivar el usuario."];
        }
    }

    /**
     * Obtiene a los usuarios "eliminados"
     * @return array
     */
    public function obtenerUsuariosDesactivados(): array
    {
        $sql = "
                SELECT 
                    u.id,
                    u.nombre,
                    u.apaterno,
                    u.amaterno,
                    u.correo,
                    r.rol
                FROM usuarios u
                INNER JOIN roles r ON r.id = u.rol
                WHERE u.activo = false
                ORDER BY u.id ASC
            ";

        try {
            $stmt = $this->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * "Reactiva" un usuario
     * @param int $id
     * @return array{message: string, success: bool}
     */
    public function reactivarUsuario(int $id): array
    {
        try {
            // Validar que exista y esté desactivado
            $stmt = $this->query(
                "SELECT 1 FROM usuarios WHERE id = :id AND activo = false",
                [':id' => $id]
            );

            if (!$stmt->fetchColumn()) {
                return [
                    "success" => false,
                    "message" => "El usuario no existe o ya está activo."
                ];
            }

            // Reactivar
            $this->query(
                "UPDATE usuarios SET activo = true WHERE id = :id",
                [':id' => $id]
            );

            return [
                "success" => true,
                "message" => "Usuario reactivado correctamente."
            ];

        } catch (PDOException $e) {
            // error_log($e->getMessage());
            return [
                "success" => false,
                "message" => "Error al reactivar el usuario."
            ];
        }
    }

    /**
     * Obtiene a un usuario en base a su id
     * @param int $id
     */
    public function obtenerUsuarioPorId(int $id): ?array
    {
        $sql = "SELECT id, nombre, apaterno, amaterno, correo, rol
                FROM usuarios
                WHERE id = :id
                AND activo = true
                LIMIT 1";

        $stmt = $this->query($sql, [':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Obtiene un usuario en base a su email
     * @param string $email
     */
    public function obtenerUsuarioPorEmail(string $email): ?array
    {
        $sql = "SELECT * FROM usuarios WHERE correo = :email AND activo = true LIMIT 1";

        $stmt = $this->query($sql, [':email' => $email]);
        $usuario = $stmt->fetch();

        return $usuario ?: null;
    }

    /**
     * Realiza el proceso de login
     * @param string $email
     * @param string $password
     * @return array|null
     */
    public function login(string $email, string $password): ?array
    {
        $usuario = $this->obtenerUsuarioPorEmail($email);

        if (!$usuario) {
            return null;
        }

        if (!password_verify($password, $usuario['pass'])) {
            return null;
        }

        unset($usuario['pass']);
        return $usuario;
    }

    /**
     * Obtiene los roles existentes (docente, estudiante, etc.)
     * @return array|null
     */
    public function obtenerRoles(): ?array
    {
        $sql = "SELECT id, rol FROM roles ORDER BY rol ASC"; // roles: id, rol

        try {
            $stmt = $this->query($sql);
            $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Si no hay roles, devolvemos arreglo vacío (más cómodo que null)
            return $roles ?: [];
        } catch (PDOException $e) {
            // En producción conviene loguear el error:
            // error_log("Error obtenerRoles: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Verifica si un email ya está registrado
     * @param string $correo
     * @param mixed $excludeUserId
     * @return bool
     */
    public function correoExiste(string $correo, ?int $excludeUserId = null): bool
    {
        $sql = "SELECT 1 FROM usuarios WHERE correo = :correo AND activo = true";
        $params = [':correo' => $correo];

        // Para edición: ignorar el mismo usuario
        if ($excludeUserId !== null) {
            $sql .= " AND id <> :id";
            $params[':id'] = $excludeUserId;
        }

        $sql .= " LIMIT 1";

        $stmt = $this->query($sql, $params);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Valida que solo pueda registrarse un email una vez
     * @param string $correo
     * @param mixed $excludeUserId
     * @return array{message: string, ok: bool}
     */
    public function validarCorreoUnico(string $correo, ?int $excludeUserId = null): array
    {
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'message' => 'El correo no es válido.'];
        }

        if ($this->correoExiste($correo, $excludeUserId)) {
            return ['ok' => false, 'message' => 'El correo ya está registrado.'];
        }

        return ['ok' => true, 'message' => 'OK'];
    }

    /**
     * Consulta a todos los usuarios
     * @return array
     */
    public function obtenerUsuarios(): array
    {
        $sql = "
        SELECT 
            u.id,
            u.nombre,
            u.apaterno,
            u.amaterno,
            u.correo,
            r.rol
        FROM usuarios u
        INNER JOIN roles r ON r.id = u.rol
        WHERE u.activo = true
        ORDER BY u.id ASC
    ";

        try {
            $stmt = $this->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            // En producción: error_log($e->getMessage());
            return [];
        }
    }
}