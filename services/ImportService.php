<?php
declare(strict_types=1);

require_once __DIR__ . '/../Repositories/ImportRepository.php';
require_once $_SERVER['DOCUMENT_ROOT'] . "/evaldoc/vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class ImportService
{
    public function __construct(private ImportRepository $repo, private PDO $pdo)
    {
    }

    private function cellStr($value): string
    {
        if ($value instanceof RichText)
            return $value->getPlainText();
        if (is_bool($value))
            return $value ? '1' : '0';
        if ($value === null)
            return '';
        return (string) $value;
    }
    private function clean(?string $s): string
    {
        $s = trim((string) $s);
        $s = preg_replace('/\s+/', ' ', $s);
        return $s ?? '';
    }

    private function extractEmail(string $raw): array
    {
        $raw = trim($raw);
        if ($raw === '')
            return ['', 'email_vacio'];
        if (preg_match('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', $raw, $m)) {
            $email = strtolower($m[0]);
            $warning = ($email !== strtolower(trim($raw))) ? 'email_normalizado' : '';
            return [$email, $warning];
        }
        return ['', 'email_invalido'];
    }

    private function slugify(string $s): string
    {
        $s = mb_strtolower(trim($s), 'UTF-8');
        $s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s) ?: $s;
        $s = preg_replace('/[^a-z0-9]+/', '.', $s);
        $s = trim((string) $s, '.');
        return $s ?: 'sin.nombre';
    }

    private function splitNombre(string $full): array
    {
        $full = $this->clean($full);
        if ($full === '')
            return ['SIN NOMBRE', 'SIN_APELLIDO', null];
        $parts = preg_split('/\s+/', $full) ?: [];
        $n = count($parts);
        if ($n >= 3) {
            $am = array_pop($parts);
            $ap = array_pop($parts);
            $nom = implode(' ', $parts);
            return [$nom ?: 'SIN NOMBRE', $ap ?: 'SIN_APELLIDO', $am ?: null];
        }
        if ($n === 2)
            return [$parts[0], $parts[1], null];
        return [$parts[0], 'SIN_APELLIDO', null];
    }

    private function findHeaderRow($sheet): int
    {
        for ($r = 1; $r <= 60; $r++) {
            $row = [];
            for ($c = 1; $c <= 15; $c++) {
                $row[] = strtoupper(trim($this->cellStr($sheet->getCell([$c, $r])->getValue())));
            }
            $joined = implode(' | ', $row);
            if (str_contains($joined, 'DEPARTAMENTO') && str_contains($joined, 'NOMBRE_ALUMNO'))
                return $r;
        }
        return 6;
    }

    public function importExcel(string $tmpFile, string $originalName, string $periodoCodigo = '2025-1'): array
    {
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $periodoId = $this->repo->getPeriodoId($periodoCodigo);
        if ($periodoId <= 0) {
            return ["success" => false, "message" => "Periodo no encontrado en BD: {$periodoCodigo}"];
        }

        $hash = hash_file('sha256', $tmpFile) ?: null;

        // idempotencia por hash+periodo (si existe y está completado, cortamos)
        if ($hash) {
            $l = $this->repo->findLoteByHash($periodoId, $hash);
            if ($l && ($l['estado'] ?? '') === 'completado') {
                return [
                    "success" => true,
                    "message" => "Este archivo ya fue importado (idempotencia por hash).",
                    "already_imported" => true,
                    "lote_id" => (int) $l['id']
                ];
            }
        }

        $roleDoc = $this->repo->getRoleId('Docente');
        $roleEst = $this->repo->getRoleId('Estudiante');
        if ($roleDoc <= 0 || $roleEst <= 0) {
            return ["success" => false, "message" => "No existen roles Docente/Estudiante en BD."];
        }

        $loteId = $this->repo->createLote($periodoId, $originalName, $hash);

        $stats = [
            "filas" => 0,
            "errores" => 0,
            "emails_sinteticos" => 0,
            "inscripciones_insertadas" => 0,
            "docentes_creados" => 0,
            "estudiantes_creados" => 0,
        ];

        $syntheticUsed = [];

        $spreadsheet = IOFactory::load($tmpFile);
        $sheet = $spreadsheet->getSheetByName('EvalDocente') ?? $spreadsheet->getActiveSheet();

        $headerRow = $this->findHeaderRow($sheet);
        $dataStart = $headerRow + 1;
        $maxRow = $sheet->getHighestRow();

        $this->pdo->beginTransaction();

        try {
            for ($r = $dataStart; $r <= $maxRow; $r++) {

                $dep = $this->clean($this->cellStr($sheet->getCell("A{$r}")->getValue()));
                $materiaNombre = $this->clean($this->cellStr($sheet->getCell("B{$r}")->getValue()));
                $materiaClave = $this->clean($this->cellStr($sheet->getCell("C{$r}")->getValue()));
                $grupo = $this->clean($this->cellStr($sheet->getCell("D{$r}")->getValue()));
                $docNombre = $this->clean($this->cellStr($sheet->getCell("E{$r}")->getValue()));
                $docEmailRaw = $this->clean($this->cellStr($sheet->getCell("F{$r}")->getValue()));
                $matricula = $this->clean($this->cellStr($sheet->getCell("G{$r}")->getValue()));
                $aluNombre = $this->clean($this->cellStr($sheet->getCell("H{$r}")->getValue()));
                $aluEmailRaw = $this->clean($this->cellStr($sheet->getCell("I{$r}")->getValue()));

                // saltar filas vacías
                if ($dep === '' && $materiaClave === '' && $docNombre === '' && $matricula === '' && $aluNombre === '') {
                    continue;
                }

                $stats["filas"]++;

                $regId = $this->repo->insertImportRegistro($loteId, $r, [
                    'departamento' => $dep,
                    'materia_nombre' => $materiaNombre,
                    'materia_clave' => $materiaClave,
                    'grupo' => $grupo,
                    'profesor_nombre' => $docNombre,
                    'profesor_email' => $docEmailRaw,
                    'matricula' => $matricula,
                    'alumno_nombre' => $aluNombre,
                    'alumno_email' => $aluEmailRaw
                ]);

                try {
                    // reglas mínimas
                    if ($materiaClave === '' || $grupo === '' || $docNombre === '' || $aluNombre === '') {
                        throw new RuntimeException("Fila incompleta: falta materia_clave/grupo/docente/alumno.");
                    }
                    if ($matricula === '') {
                        throw new RuntimeException("Matrícula vacía: no se puede crear estudiante.");
                    }

                    // upsert dept/materia
                    $depKey = $dep !== '' ? $dep : 'SIN_DEPARTAMENTO';
                    $depId = $this->repo->upsertDepartamento($depKey);

                    $matId = $this->repo->upsertMateria(
                        $materiaClave,
                        $materiaNombre !== '' ? $materiaNombre : $materiaClave,
                        $depId
                    );

                    // DOCENTE (correo real o sintético)
                    [$docEmail, $wDoc] = $this->extractEmail($docEmailRaw);
                    if ($docEmail === '') {
                        $base = "docente." . $this->slugify($docNombre);
                        $cand = "{$base}@noemail.local";
                        $i = 2;
                        while (isset($syntheticUsed[$cand])) {
                            $cand = "{$base}{$i}@noemail.local";
                            $i++;
                        }
                        $docEmail = $cand;
                        $syntheticUsed[$docEmail] = true;
                        $stats["emails_sinteticos"]++;
                    }

                    $docUser = $this->repo->findUsuarioByCorreo($docEmail);
                    if ($docUser) {
                        $docUid = (int) $docUser['id'];
                        [$n, $ap, $am] = $this->splitNombre($docNombre);
                        $this->repo->updateUsuarioNombre($docUid, $n, $ap, $am);
                        $this->repo->upsertPerfilNombreCompleto($docUid, $docNombre);
                    } else {
                        [$n, $ap, $am] = $this->splitNombre($docNombre);
                        $pass = password_hash('docente' . $docEmail, PASSWORD_BCRYPT);

                        $docUid = $this->repo->insertUsuario([
                            'nombre' => $n,
                            'apaterno' => $ap,
                            'amaterno' => $am,
                            'correo' => $docEmail,
                            'pass' => $pass,
                            'rol' => $roleDoc
                        ]);
                        $stats["docentes_creados"]++;
                        $this->repo->upsertPerfilNombreCompleto($docUid, $docNombre);
                    }
                    $this->repo->ensureDocente($docUid);

                    // ESTUDIANTE por matrícula (primero)
                    $est = $this->repo->findEstudianteByMatricula($matricula);

                    // correo alumno (real o sintético)
                    [$aluEmail, $wAlu] = $this->extractEmail($aluEmailRaw);
                    if ($aluEmail === '') {
                        $aluEmail = strtolower($matricula) . "@noemail.local";
                        $stats["emails_sinteticos"]++;
                    }

                    if ($est) {
                        $estUid = (int) $est['usuario_id'];
                        // si el correo nuevo no está ocupado, actualiza
                        if (!$this->repo->correoOcupado($aluEmail)) {
                            $this->repo->updateUsuarioCorreo($estUid, $aluEmail);
                        }
                        [$n, $ap, $am] = $this->splitNombre($aluNombre);
                        $this->repo->updateUsuarioNombre($estUid, $n, $ap, $am);
                        $this->repo->upsertPerfilNombreCompleto($estUid, $aluNombre);
                    } else {
                        $aluUser = $this->repo->findUsuarioByCorreo($aluEmail);
                        if ($aluUser) {
                            $estUid = (int) $aluUser['id'];
                            [$n, $ap, $am] = $this->splitNombre($aluNombre);
                            $this->repo->updateUsuarioNombre($estUid, $n, $ap, $am);
                            $this->repo->upsertPerfilNombreCompleto($estUid, $aluNombre);
                        } else {
                            [$n, $ap, $am] = $this->splitNombre($aluNombre);
                            $pass = password_hash($matricula, PASSWORD_BCRYPT);

                            $estUid = $this->repo->insertUsuario([
                                'nombre' => $n,
                                'apaterno' => $ap,
                                'amaterno' => $am,
                                'correo' => $aluEmail,
                                'pass' => $pass,
                                'rol' => $roleEst
                            ]);
                            $stats["estudiantes_creados"]++;
                            $this->repo->upsertPerfilNombreCompleto($estUid, $aluNombre);
                        }

                        $this->repo->ensureEstudiante($estUid, $matricula);
                    }

                    // Oferta + Inscripción (idempotente por índices únicos)
                    $goId = $this->repo->upsertGrupoOferta($periodoId, $matId, $grupo, $docUid);
                    $stats["inscripciones_insertadas"] += $this->repo->ensureInscripcion($goId, $estUid);

                    $this->repo->markImportRegistroOk($regId);

                } catch (Throwable $rowErr) {
                    $stats["errores"]++;
                    $this->repo->markImportRegistroErr($regId, $rowErr->getMessage());
                }
            }

            $this->repo->finishLote($loteId, 'completado', (int) $stats["errores"]);
            $this->pdo->commit();

            return [
                "success" => true,
                "message" => "Importación finalizada.",
                "periodo" => $periodoCodigo,
                "lote_id" => $loteId,
                "stats" => $stats
            ];

        } catch (Throwable $e) {
            $this->pdo->rollBack();
            $this->repo->finishLote($loteId, 'error', (int) $stats["errores"] + 1);

            return ["success" => false, "message" => "Error en importación: " . $e->getMessage()];
        }
    }
}
