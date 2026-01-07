<?php
declare(strict_types=1);

require_once $_SERVER['DOCUMENT_ROOT'] . "/evaldoc/repositories/UserRepository.php";
header("Content-Type: application/json; charset=utf-8");

try {
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(["success" => false, "message" => "ID inválido."]);
        exit;
    }

    $repo = new UserRepository();
    $u = $repo->findById($id, true); // solo activos

    if (!$u) {
        echo json_encode(["success" => false, "message" => "Usuario no encontrado."]);
        exit;
    }

    echo json_encode(["success" => true, "usuario" => $u]);
} catch (Throwable $e) {
    echo json_encode(["success" => false, "message" => "Error interno del servidor."]);
}
