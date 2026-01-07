<?php
declare(strict_types=1);

header("Content-Type: application/json; charset=utf-8");

require_once $_SERVER['DOCUMENT_ROOT'] . "/evaldoc/vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

function cell_str($value): string
{
    if ($value instanceof RichText) {
        return $value->getPlainText();
    }
    if (is_bool($value)) {
        return $value ? '1' : '0';
    }
    if ($value === null) {
        return '';
    }
    return (string) $value;
}

function clean_str(?string $s): string
{
    $s = trim((string) $s);
    $s = preg_replace('/\s+/', ' ', $s);
    return $s ?? '';
}

function extract_email(string $raw): array
{
    $raw = trim($raw);
    if ($raw === '') {
        return ['', 'email_vacio'];
    }

    // Extraer email aunque tenga basura alrededor
    if (preg_match('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', $raw, $m)) {
        $email = strtolower($m[0]);
        $warning = ($email !== strtolower(trim($raw))) ? 'email_normalizado' : '';
        return [$email, $warning];
    }
    return ['', 'email_invalido'];
}

function find_periodo($sheet): string
{
    // Busca algo tipo 2025-1 en primeras filas
    for ($r = 1; $r <= 20; $r++) {
        for ($c = 1; $c <= 12; $c++) {
            $raw = $sheet->getCell([$c, $r])->getValue();
            $v = cell_str($raw);

            if ($v && preg_match('/\b(20\d{2})\s*-\s*([12])\b/', $v, $m)) {
                return $m[1] . '-' . $m[2];
            }
        }
    }
    return 'desconocido';
}

function find_header_row($sheet): int
{
    for ($r = 1; $r <= 60; $r++) {
        $row = [];
        for ($c = 1; $c <= 15; $c++) {
            $raw = $sheet->getCell([$c, $r])->getValue();
            $row[] = strtoupper(trim(cell_str($raw)));
        }
        $joined = implode(' | ', $row);
        if (str_contains($joined, 'DEPARTAMENTO') && str_contains($joined, 'NOMBRE_ALUMNO')) {
            return $r;
        }
    }
    return 6; // fallback
}

try {
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(["success" => false, "message" => "No se recibió el archivo correctamente."]);
        exit;
    }

    $tmp = $_FILES['file']['tmp_name'];

    $spreadsheet = IOFactory::load($tmp);
    $sheet = $spreadsheet->getSheetByName('EvalDocente') ?? $spreadsheet->getActiveSheet();

    $periodo = find_periodo($sheet);
    $headerRow = find_header_row($sheet);
    $dataStart = $headerRow + 1;

    // Mapeo fijo por tu Excel:
    // A:DEPARTAMENTO B:NOMBRE MATERIA C:CLAVE MATERIA D:GRUPO E:NOMBRE PROFESOR F:EMAIL PROFESOR
    // G:MATRICULA H:NOMBRE_ALUMNO I:EMAIL_ALUMNO
    $maxRow = $sheet->getHighestRow();

    $departamentos = []; // dept => ['docentes'=>...]
    $docentesSet = [];
    $alumnosUnicosSet = [];
    $filas = 0;
    $warningsGlobal = 0;

    // Para contar ofertas únicas por llave
    $ofertasSet = [];

    for ($r = $dataStart; $r <= $maxRow; $r++) {
        $dep = clean_str(cell_str($sheet->getCell("A{$r}")->getValue()));
        $materiaNombre = clean_str(cell_str($sheet->getCell("B{$r}")->getValue()));
        $materiaClave = clean_str(cell_str($sheet->getCell("C{$r}")->getValue()));
        $grupo = clean_str(cell_str($sheet->getCell("D{$r}")->getValue()));
        $docNombre = clean_str(cell_str($sheet->getCell("E{$r}")->getValue()));
        $docEmailRaw = clean_str(cell_str($sheet->getCell("F{$r}")->getValue()));
        $matricula = clean_str(cell_str($sheet->getCell("G{$r}")->getValue()));
        $aluNombre = clean_str(cell_str($sheet->getCell("H{$r}")->getValue()));
        $aluEmailRaw = clean_str(cell_str($sheet->getCell("I{$r}")->getValue()));

        // Saltar filas vacías
        if ($dep === '' && $materiaClave === '' && $docNombre === '' && $matricula === '' && $aluNombre === '') {
            continue;
        }

        $filas++;

        [$docEmail, $wDocEmail] = extract_email($docEmailRaw);
        [$aluEmail, $wAluEmail] = extract_email($aluEmailRaw);

        if ($wDocEmail)
            $warningsGlobal++;
        if ($wAluEmail)
            $warningsGlobal++;

        // Llaves de agrupación
        $depKey = ($dep !== '') ? $dep : 'SIN_DEPARTAMENTO';

        $docKey = ($docEmail !== '')
            ? $docEmail
            : ('SIN_EMAIL|' . strtoupper($docNombre ?: 'DOCENTE_DESCONOCIDO'));

        $docentesSet[$docKey] = true;

        $ofertaKey = $materiaClave . '|' . $grupo . '|' . $docKey;
        $ofertasSet[$depKey . '|' . $ofertaKey] = true;

        if ($matricula !== '') {
            $alumnosUnicosSet[$matricula] = true;
        }

        // Estructura
        if (!isset($departamentos[$depKey])) {
            $departamentos[$depKey] = [
                "nombre" => $depKey,
                "docentes" => []
            ];
        }

        if (!isset($departamentos[$depKey]["docentes"][$docKey])) {
            $departamentos[$depKey]["docentes"][$docKey] = [
                "nombre" => $docNombre ?: 'DOCENTE DESCONOCIDO',
                "email" => $docEmail,
                "warnings" => [],
                "ofertas" => []
            ];
            if ($wDocEmail) {
                $departamentos[$depKey]["docentes"][$docKey]["warnings"][] = $wDocEmail;
            }
        }

        $docRef = &$departamentos[$depKey]["docentes"][$docKey];

        if (!isset($docRef["ofertas"][$ofertaKey])) {
            $docRef["ofertas"][$ofertaKey] = [
                "materia_clave" => $materiaClave,
                "materia_nombre" => $materiaNombre,
                "grupo" => $grupo,
                "warnings" => [],
                "alumnos" => []
            ];
        }

        $ofertaRef = &$docRef["ofertas"][$ofertaKey];

        $alumnoWarnings = [];
        if ($wAluEmail) {
            $alumnoWarnings[] = $wAluEmail;
        }

        $ofertaRef["alumnos"][] = [
            "matricula" => $matricula,
            "nombre" => $aluNombre,
            "email" => $aluEmail,
            "warnings" => $alumnoWarnings,
            "row" => $r
        ];
    }

    // Convertir mapas a arreglos ordenados
    $departamentosArr = [];
    ksort($departamentos);

    foreach ($departamentos as $depKey => $depData) {
        $docArr = [];
        ksort($depData["docentes"]);

        foreach ($depData["docentes"] as $docKey => $docData) {
            $ofArr = [];
            ksort($docData["ofertas"]);

            foreach ($docData["ofertas"] as $ofKey => $ofData) {
                $ofArr[] = $ofData;
            }

            $docData["ofertas"] = $ofArr;
            $docArr[] = $docData;
        }

        $depData["docentes"] = $docArr;
        $departamentosArr[] = $depData;
    }

    echo json_encode([
        "success" => true,
        "periodo" => $periodo,
        "summary" => [
            "filas" => $filas,
            "departamentos" => count($departamentosArr),
            "docentes" => count($docentesSet),
            "ofertas" => count($ofertasSet),
            "alumnos_unicos" => count($alumnosUnicosSet),
            "warnings" => $warningsGlobal
        ],
        "departamentos" => $departamentosArr
    ]);

} catch (Throwable $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error interno al leer el Excel: " . $e->getMessage()
    ]);
}
