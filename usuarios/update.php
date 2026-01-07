<?php
declare(strict_types=1);

require_once $_SERVER['DOCUMENT_ROOT'] . "/evaldoc/services/UserService.php";
header("Content-Type: application/json; charset=utf-8");

try {
    $id = (int) ($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $apaterno = trim($_POST['apaterno'] ?? '');
    $amaterno = trim($_POST['amaterno'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $rol = (int) ($_POST['rol'] ?? 0);
    $pass = (string) ($_POST['pass'] ?? ''); // opcional

    if ($id <= 0 || $nombre === '' || $apaterno === '' || $correo === '' || $rol <= 0) {
        echo json_encode(["success" => false, "message" => "Datos inválidos."]);
        exit;
    }

    $amaterno = ($amaterno === '') ? null : $amaterno;

    // pass opcional: si viene vacío, el service no lo cambia
    $passOrNull = ($pass === '') ? null : $pass;

    $service = new UserService();
    $result = $service->actualizarUsuario($id, $nombre, $apaterno, $amaterno, $correo, $rol, $passOrNull);

    echo json_encode($result);
} catch (Throwable $e) {
    echo json_encode(["success" => false, "message" => "Error interno del servidor."]);
}
