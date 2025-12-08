<?php
// EVALDOC/conn/conn.php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';

class Principal {
    private PDO $pdo;

    public function __construct() {
        // Solo se llama una vez: aquí ya tenemos la conexión lista
        $this->pdo = db();
    }

    /**
     * Método interno para ejecutar consultas preparadas
     */
    private function query(string $sql, array $params = []): PDOStatement {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /* =====================
       MÉTODOS CRUD BASE
       ===================== */

    // Ejemplo: registrar usuario
    public function registrarUsuario(string $nombre, string $email, string $password): bool {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nombre, email, password)
                VALUES (:nombre, :email, :password)";

        $stmt = $this->query($sql, [
            ':nombre' => $nombre,
            ':email' => $email,
            ':password' => $hash,
        ]);

        return $stmt->rowCount() > 0;
    }

    // Ejemplo: obtener usuario por email
    public function obtenerUsuarioPorEmail(string $email): ?array {
        $sql = "SELECT * FROM myapp_usuario WHERE correo_usuario = :email LIMIT 1";

        $stmt = $this->query($sql, [':email' => $email]);
        $usuario = $stmt->fetch();

        return $usuario ?: null;
    }

    // Ejemplo: login
    public function login(string $email, string $password): ?array {
        $usuario = $this->obtenerUsuarioPorEmail($email);

        if (!$usuario) {
            return null;
        }

        if (!password_verify($password, $usuario['password'])) {
            return null;
        }

        // Opcional: no devolver el hash
        unset($usuario['password']);
        return $usuario;
    }

    // Aquí después podemos ir añadiendo:
    // actualizarUsuario(), eliminarUsuario(), listarUsuarios(), etc.
}