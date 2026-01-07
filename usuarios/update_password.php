<?php
declare(strict_types=1);

require_once $_SERVER['DOCUMENT_ROOT'] . "/evaldoc/services/UserService.php";
header("Content-Type: application/json; charset=utf-8");

try {
    $id = (int) ($_POST['id'] ?? 0);
    $pass = (string) ($_POST['pass'] ?? '');

    if ($id <= 0 || $pass === '') {
        echo json_encode(["success" => false, "message" => "Datos inválidos."]);
        exit;
    }

    $service = new UserService();
    echo json_encode($service->cambiarContrasena($id, $pass));
} catch (Throwable $e) {
    echo json_encode(["success" => false, "message" => "Error interno del servidor."]);
}
