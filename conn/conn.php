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


    // Obtener usuario por email
    public function obtenerUsuarioPorEmail(string $email): ?array
    {
        $sql = "SELECT * FROM usuarios WHERE correo = :email LIMIT 1";

        $stmt = $this->query($sql, [':email' => $email]);
        $usuario = $stmt->fetch();

        return $usuario ?: null;
    }

    // Login
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

    // Obtener roles
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

    // Verificar si correo ya existe en BD
    public function correoExiste(string $correo, ?int $excludeUserId = null): bool
    {
        $sql = "SELECT 1 FROM usuarios WHERE correo = :correo";
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

    // Valida que no se inserte correo duplicado
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

    // Obtiene todos los usuarios
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