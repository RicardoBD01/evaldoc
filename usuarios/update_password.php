<?php
declare(strict_types=1);

require_once $_SERVER['DOCUMENT_ROOT'] . "/evaldoc/conn/conn.php";
header("Content-Type: application/json; charset=utf-8");

try {
    $id = (int) ($_POST['id'] ?? 0);
    $pass = (string) ($_POST['pass'] ?? '');

    if ($id <= 0) {
        echo json_encode(["success" => false, "message" => "ID inválido."]);
        exit;
    }

    if ($pass === '' || strlen($pass) < 8) {
        echo json_encode(["success" => false, "message" => "La contraseña debe tener al menos 8 caracteres."]);
        exit;
    }

    $principal = new Principal();
    $hash = password_hash($pass, PASSWORD_DEFAULT);

    $result = $principal->actualizarContrasenaUsuario($id, $hash);
    echo json_encode($result);
    exit;

} catch (Throwable $e) {
    // error_log($e->getMessage());
    echo json_encode(["success" => false, "message" => "Error interno del servidor."]);
}
