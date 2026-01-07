<?php
declare(strict_types=1);

header("Content-Type: application/json; charset=utf-8");

require_once $_SERVER['DOCUMENT_ROOT'] . "/evaldoc/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/evaldoc/services/AuthService.php";

session_start();

if (empty($_SESSION['usuario_id'])) {
    echo json_encode(["success" => false, "message" => "No autenticado."]);
    exit;
}

$newPass = $_POST['new_pass'] ?? '';
$confirm = $_POST['confirm_pass'] ?? '';

if ($newPass === '' || $confirm === '') {
    echo json_encode(["success" => false, "message" => "Debes llenar todos los campos."]);
    exit;
}
if ($newPass !== $confirm) {
    echo json_encode(["success" => false, "message" => "Las contraseñas no coinciden."]);
    exit;
}
if (mb_strlen($newPass) < 8) {
    echo json_encode(["success" => false, "message" => "La contraseña debe tener al menos 8 caracteres."]);
    exit;
}

$auth = new AuthService();
$ok = $auth->changePassword((int) $_SESSION['usuario_id'], $newPass);

if ($ok) {
    $_SESSION['must_change_pass'] = false;
    echo json_encode(["success" => true, "message" => "Contraseña actualizada."]);
} else {
    echo json_encode(["success" => false, "message" => "No se pudo actualizar la contraseña."]);
}
