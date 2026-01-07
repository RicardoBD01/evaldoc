<?php
declare(strict_types=1);

require_once $_SERVER['DOCUMENT_ROOT'] . "/evaldoc/services/UserService.php";
header("Content-Type: application/json; charset=utf-8");

try {
    $nombre = trim($_POST['nombre'] ?? '');
    $apaterno = trim($_POST['apaterno'] ?? '');
    $amaterno = trim($_POST['amaterno'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $pass = (string) ($_POST['pass'] ?? '');
    $rol = (int) ($_POST['rol'] ?? 0);

    if ($nombre === '' || $apaterno === '' || $correo === '' || $pass === '' || $rol <= 0) {
        echo json_encode(["success" => false, "message" => "Faltan campos obligatorios."]);
        exit;
    }

    // amaterno opcional
    $amaterno = ($amaterno === '') ? null : $amaterno;

    $service = new UserService();
    $result = $service->crearUsuario($nombre, $apaterno, $amaterno, $correo, $pass, $rol);

    echo json_encode($result);
} catch (Throwable $e) {
    echo json_encode(["success" => false, "message" => "Error interno del servidor."]);
}
