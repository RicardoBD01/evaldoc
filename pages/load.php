<?php
// /evaldoc/pages/load.php
declare(strict_types=1);

header('Content-Type: text/html; charset=utf-8');

$page = $_GET['page'] ?? 'dashboard';

/**
 * Lista blanca: SOLO permitimos estas páginas
 * (evita LFI / que pidan cualquier archivo del server)
 */
$routes = [
    'usuarios' => __DIR__ . '/../usuarios/index.php',
    'inicio' => __DIR__ . '/../pages/inicio.php',
    // agrega más:
    // 'materias'  => __DIR__ . '/../materias/index.php',
];

if (!isset($routes[$page])) {
    http_response_code(404);
    echo "<h4>Página no encontrada</h4>";
    exit;
}

require $routes[$page];