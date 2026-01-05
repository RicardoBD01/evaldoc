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

    // Ejemplo: registrar usuario
    public function registrarUsuario(string $nombre, string $email, string $password): bool
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nombre, correo, pass)
                VALUES (:nombre, :email, :password)";

        $stmt = $this->query($sql, [
            ':nombre' => $nombre,
            ':email' => $email,
            ':password' => $hash,
        ]);

        return $stmt->rowCount() > 0;
    }

    // Ejemplo: obtener usuario por email
    public function obtenerUsuarioPorEmail(string $email): ?array
    {
        $sql = "SELECT * FROM usuarios WHERE correo = :email LIMIT 1";

        $stmt = $this->query($sql, [':email' => $email]);
        $usuario = $stmt->fetch();

        return $usuario ?: null;
    }

    // Ejemplo: login
    public function login(string $email, string $password): ?array
    {
        $usuario = $this->obtenerUsuarioPorEmail($email);

        if (!$usuario) {
            return null;
        }

        if (!password_verify($password, $usuario['pass'])) {
            return null;
        }

        // Opcional: no devolver el hash
        unset($usuario['pass']);
        return $usuario;
    }

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


    // Aquí después podemos ir añadiendo:
    // actualizarUsuario(), eliminarUsuario(), listarUsuarios(), etc.
}