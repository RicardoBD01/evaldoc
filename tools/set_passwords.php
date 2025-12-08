<?php
// evaldoc/tools/set_passwords.php

require_once __DIR__ . '/../config.php';   // ajusta la ruta si es necesario

$pdo = db();

// 1) Definir contraseña temporal
$plainPassword = 'EvalDoc2025!';

// 2) Generar hash con password_hash
$hash = password_hash($plainPassword, PASSWORD_DEFAULT);

try {
    // 3) Actualizar TODOS los usuarios (o solo los que aún no tienen password)
    $sql = "
        UPDATE myapp_usuario
        SET password = :hash
        WHERE password IS NULL
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':hash' => $hash]);

    echo "Usuarios actualizados: " . $stmt->rowCount() . PHP_EOL;
    echo "Contraseña temporal para todos: $plainPassword" . PHP_EOL;

} catch (PDOException $e) {
    echo "Error al actualizar contraseñas: " . $e->getMessage();
}
