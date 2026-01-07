<?php
declare(strict_types=1);

require_once $_SERVER['DOCUMENT_ROOT'] . "/evaldoc/services/AuthService.php";

header("Content-Type: application/json; charset=utf-8");

$email = $_POST['email'] ?? '';
$password = $_POST['pass'] ?? '';

if ($email === '' || $password === '') {
    echo json_encode([
        "success" => false,
        "message" => "Debes llenar todos los campos."
    ]);
    exit;
}

$authService = new AuthService();
$usuario = $authService->login($email, $password);

if ($usuario) {
    session_start();

    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['nombre'] = $usuario['nombre'];
    $_SESSION['apaterno'] = $usuario['apaterno'];
    $_SESSION['amaterno'] = $usuario['amaterno'];
    $_SESSION['correo'] = $usuario['correo'];
    $_SESSION['rol'] = $usuario['rol'];
    $_SESSION['login'] = 'ok';
    $_SESSION['must_change_pass'] = (bool) ($usuario['must_change_pass'] ?? false);

    echo json_encode([
        "success" => true,
        "message" => "Login correcto.",
        "must_change_pass" => (bool) ($usuario['must_change_pass'] ?? false)
    ]);
    exit;
} else {
    echo json_encode([
        "success" => false,
        "message" => "Correo o contraseña incorrectos."
    ]);
}
