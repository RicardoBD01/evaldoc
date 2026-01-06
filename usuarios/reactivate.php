<?php
declare(strict_types=1);

require_once $_SERVER['DOCUMENT_ROOT'] . "/evaldoc/conn/conn.php";
header("Content-Type: application/json; charset=utf-8");

try {
    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(["success" => false, "message" => "ID inválido."]);
        exit;
    }

    $principal = new Principal();
    $result = $principal->reactivarUsuario($id);

    echo json_encode($result);
    exit;

} catch (Throwable $e) {
    // error_log($e->getMessage());
    echo json_encode(["success" => false, "message" => "Error interno del servidor."]);
}
