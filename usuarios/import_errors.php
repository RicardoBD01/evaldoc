<?php
declare(strict_types=1);

header("Content-Type: application/json; charset=utf-8");

require_once $_SERVER['DOCUMENT_ROOT'] . "/evaldoc/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/evaldoc/repositories/ImportRepository.php";

session_start();
if (empty($_SESSION['login']) || $_SESSION['login'] !== 'ok') {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "No autorizado."]);
    exit;
}

$loteId = (int) ($_GET['lote_id'] ?? 0);
if ($loteId <= 0) {
    echo json_encode(["success" => false, "message" => "lote_id inválido."]);
    exit;
}

$pdo = db();
$repo = new ImportRepository($pdo);

$rows = $repo->getErroresLote($loteId, 300);

echo json_encode([
    "success" => true,
    "count" => count($rows),
    "rows" => $rows
]);
