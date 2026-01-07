<?php
// /evaldoc/pages/load.php
declare(strict_types=1);

header('Content-Type: text/html; charset=utf-8');

$page = $_GET['page'] ?? 'inicio';

$routes = [
    'usuarios' => [
        'view' => __DIR__ . '/../usuarios/index.php',
        'script' => 'usuarios.js',
    ],
    'inicio' => [
        'view' => __DIR__ . '/../pages/inicio.php',
        'script' => null,
    ],
];

if (!isset($routes[$page])) {
    http_response_code(404);
    echo "<h4>Página no encontrada</h4>";
    exit;
}

$pageScript = $routes[$page]['script'] ?? null;

// ✅ Si lo estás cargando por AJAX, este header le dice a app.js qué cargar
if (!empty($pageScript)) {
    header('X-Page-Script: ' . $pageScript);
}

require $routes[$page]['view'];
