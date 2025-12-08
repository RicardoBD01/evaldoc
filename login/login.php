<?php
require_once __DIR__ . '/../conn/conn.php';

$principal = new Principal();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $usuario = $principal->login($email, $password);

    if ($usuario) {
        // Iniciar sesión, redirigir, etc.
        session_start();
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];

        header('Location: /pages/dashboard.php');
        exit;
    } else {
        $error = "Correo o contraseña incorrectos";
    }
}
