<?php
declare(strict_types=1);

require_once $_SERVER['DOCUMENT_ROOT'] . "/evaldoc/conn/conn.php";
header("Content-Type: application/json; charset=utf-8");

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(["success" => false, "message" => "ID inválido."]);
    exit;
}

$principal = new Principal();
$usuario = $principal->obtenerUsuarioPorId($id);
$roles = $principal->obtenerRoles(); // ya lo tienes o lo creamos si falta

if (!$usuario) {
    echo json_encode(["success" => false, "message" => "Usuario no encontrado."]);
    exit;
}

echo json_encode([
    "success" => true,
    "usuario" => $usuario,
    "roles" => $roles
]);
