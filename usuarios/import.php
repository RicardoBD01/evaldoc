<?php
declare(strict_types=1);

header("Content-Type: application/json; charset=utf-8");

require_once $_SERVER['DOCUMENT_ROOT'] . "/evaldoc/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/evaldoc/repositories/ImportRepository.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/evaldoc/services/ImportService.php";

// Protección básica (ajusta si luego agregas permisos por rol)
session_start();
if (empty($_SESSION['login']) || $_SESSION['login'] !== 'ok') {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "No autorizado."]);
    exit;
}

// Evitar que el script muera en imports grandes
@set_time_limit(0);

// Validación simple de tipo por nombre
function is_excel_filename(string $name): bool
{
    $name = strtolower($name);
    return str_ends_with($name, '.xlsx') || str_ends_with($name, '.xls');
}

try {
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(["success" => false, "message" => "No se recibió el archivo correctamente."]);
        exit;
    }

    $tmp = $_FILES['file']['tmp_name'];
    $name = $_FILES['file']['name'] ?? 'archivo.xlsx';

    if (!is_excel_filename($name)) {
        echo json_encode(["success" => false, "message" => "Formato no válido. Sube un archivo .xlsx o .xls."]);
        exit;
    }

    // Por tu regla: ya existe en BD. Aun así lo dejamos paramétrico.
    $periodo = $_POST['periodo'] ?? '2025-1';

    // Normalizar espacios (por si viene '2025 - 1')
    $periodo = preg_replace('/\s+/', '', (string) $periodo);

    $pdo = db();

    $repo = new ImportRepository($pdo);
    $svc = new ImportService($repo, $pdo);

    $resp = $svc->importExcel($tmp, $name, $periodo);
    echo json_encode($resp);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error interno: " . $e->getMessage()]);
}
