<?php
declare(strict_types=1);

require_once $_SERVER['DOCUMENT_ROOT'] . "/evaldoc/conn/conn.php";

header("Content-Type: application/json; charset=utf-8");

try {
    $nombre = trim($_POST['nombre'] ?? '');
    $apaterno = trim($_POST['apaterno'] ?? '');
    $amaterno = trim($_POST['amaterno'] ?? '');
    $amaterno = ($amaterno === '') ? null : $amaterno;
    $correo = trim($_POST['correo'] ?? '');
    $pass = (string) ($_POST['pass'] ?? '');
    $rolRaw = $_POST['rol'] ?? '';

    // Validaciones server-side
    if ($nombre === '' || $apaterno === '' || $correo === '' || $pass === '' || $rolRaw === '') {
        echo json_encode(["success" => false, "message" => "Faltan campos obligatorios."]);
        exit;
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "message" => "El correo no es válido."]);
        exit;
    }

    $rol = (int) $rolRaw;
    if ($rol <= 0) {
        echo json_encode(["success" => false, "message" => "Rol inválido."]);
        exit;
    }

    if (strlen($pass) < 8) {
        echo json_encode(["success" => false, "message" => "La contraseña debe tener al menos 8 caracteres."]);
        exit;
    }

    $principal = new Principal();

    // OJO: tu tabla usa columna "pass" (TEXT).
    // Guardaremos hash aquí (recomendado)
    $passHash = password_hash($pass, PASSWORD_DEFAULT);

    $result = $principal->insertarUsuario(
        $nombre,
        $apaterno,
        $amaterno,
        $correo,
        $passHash,
        $rol
    );

    echo json_encode($result);
    exit;


} catch (Throwable $e) {
    // En producción log:
    // error_log($e->getMessage());
    echo json_encode(["success" => false, "message" => "Error interno del servidor."]);
}
