<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/evaldoc/conn/conn.php";

header("Content-Type: application/json; charset=utf-8");

$principal = new Principal();

$email = $_POST['email'] ?? '';
$password = $_POST['pass'] ?? '';

if ($email === '' || $password === '') {
    echo json_encode([
        "success" => false,
        "message" => "Debes llenar todos los campos."
    ]);
    exit;
}

$usuario = $principal->login($email, $password);

if ($usuario) {
    session_start();
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['nombre'] = $usuario['nombre'];
    $_SESSION['amaterno'] = $usuario['amaterno'];
    $_SESSION['apaterno'] = $usuario['apaterno'];
    $_SESSION['correo'] = $usuario['correo'];
    $_SESSION['rol'] = $usuario['rol'];
    $_SESSION['login'] = 'ok';

    echo json_encode([
        "success" => true,
        "message" => "Login correcto."
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Correo o contraseña incorrectos."
    ]);
}
