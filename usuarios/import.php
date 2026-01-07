<?php
declare(strict_types=1);

header("Content-Type: application/json; charset=utf-8");

require_once $_SERVER['DOCUMENT_ROOT'] . "/evaldoc/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/evaldoc/app/Repositories/ImportRepository.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/evaldoc/app/Services/ImportService.php";

try {
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(["success" => false, "message" => "No se recibió el archivo correctamente."]);
        exit;
    }

    $tmp = $_FILES['file']['tmp_name'];
    $name = $_FILES['file']['name'] ?? 'archivo.xlsx';

    // Por tu regla: ya existe en BD. Aun así lo dejamos paramétrico.
    $periodo = $_POST['periodo'] ?? '2025-1';

    $pdo = db();

    $repo = new ImportRepository($pdo);
    $svc = new ImportService($repo, $pdo);

    $resp = $svc->importExcel($tmp, $name, $periodo);
    echo json_encode($resp);

} catch (Throwable $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
