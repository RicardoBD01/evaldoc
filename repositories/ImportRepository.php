<?php
declare(strict_types=1);

class ImportRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    private function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function getPeriodoId(string $codigo): int
    {
        $id = (int) $this->query(
            "SELECT id FROM periodos WHERE codigo = :c LIMIT 1",
            [':c' => $codigo]
        )->fetchColumn();

        return $id;
    }

    public function getRoleId(string $rol): int
    {
        $id = (int) $this->query(
            "SELECT id FROM roles WHERE rol = :r LIMIT 1",
            [':r' => $rol]
        )->fetchColumn();

        return $id;
    }

    public function findLoteByHash(int $periodoId, string $hash): ?array
    {
        $row = $this->query(
            "SELECT id, estado FROM import_lotes WHERE periodo_id=:p AND archivo_hash=:h LIMIT 1",
            [':p' => $periodoId, ':h' => $hash]
        )->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function createLote(int $periodoId, string $nombreArchivo, ?string $hash): int
    {
        return (int) $this->query(
            "INSERT INTO import_lotes (periodo_id, archivo_nombre, archivo_hash, estado)
             VALUES (:p, :n, :h, 'procesando')
             RETURNING id",
            [':p' => $periodoId, ':n' => $nombreArchivo, ':h' => $hash]
        )->fetchColumn();
    }

    public function finishLote(int $loteId, string $estado, int $erroresCount): void
    {
        $this->query(
            "UPDATE import_lotes
             SET estado = :e, errores_count = :c
             WHERE id = :id",
            [':e' => $estado, ':c' => $erroresCount, ':id' => $loteId]
        );
    }

    public function insertImportRegistro(int $loteId, int $rowNum, array $r): int
    {
        return (int) $this->query(
            "INSERT INTO import_registros
             (lote_id, row_num, departamento, materia_nombre, materia_clave, grupo, profesor_nombre, profesor_email,
              matricula, alumno_nombre, alumno_email, procesado, error)
             VALUES
             (:lote_id, :row_num, :departamento, :materia_nombre, :materia_clave, :grupo, :profesor_nombre, :profesor_email,
              :matricula, :alumno_nombre, :alumno_email, false, null)
             RETURNING id",
            [
                ':lote_id' => $loteId,
                ':row_num' => $rowNum,
                ':departamento' => $r['departamento'],
                ':materia_nombre' => $r['materia_nombre'],
                ':materia_clave' => $r['materia_clave'],
                ':grupo' => $r['grupo'],
                ':profesor_nombre' => $r['profesor_nombre'],
                ':profesor_email' => $r['profesor_email'],
                ':matricula' => $r['matricula'],
                ':alumno_nombre' => $r['alumno_nombre'],
                ':alumno_email' => $r['alumno_email'],
            ]
        )->fetchColumn();
    }

    public function markImportRegistroOk(int $id): void
    {
        $this->query("UPDATE import_registros SET procesado=true, error=null WHERE id=:id", [':id' => $id]);
    }

    public function markImportRegistroErr(int $id, string $error): void
    {
        $this->query("UPDATE import_registros SET procesado=false, error=:e WHERE id=:id", [':e' => $error, ':id' => $id]);
    }

    public function upsertDepartamento(string $nombre): int
    {
        return (int) $this->query(
            "INSERT INTO departamentos (nombre)
             VALUES (:n)
             ON CONFLICT (nombre) DO UPDATE SET nombre=EXCLUDED.nombre
             RETURNING id",
            [':n' => $nombre]
        )->fetchColumn();
    }

    public function upsertMateria(string $clave, string $nombre, int $departamentoId): int
    {
        return (int) $this->query(
            "INSERT INTO materias (clave, nombre, departamento_id)
             VALUES (:c, :n, :d)
             ON CONFLICT (clave) DO UPDATE
               SET nombre = EXCLUDED.nombre,
                   departamento_id = COALESCE(EXCLUDED.departamento_id, materias.departamento_id)
             RETURNING id",
            [':c' => $clave, ':n' => $nombre, ':d' => $departamentoId]
        )->fetchColumn();
    }

    public function findUsuarioByCorreo(string $correo): ?array
    {
        $row = $this->query(
            "SELECT id, correo, rol, must_change_pass FROM usuarios WHERE correo=:c LIMIT 1",
            [':c' => $correo]
        )->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function correoOcupado(string $correo): bool
    {
        return (bool) $this->query("SELECT 1 FROM usuarios WHERE correo=:c LIMIT 1", [':c' => $correo])->fetchColumn();
    }

    public function insertUsuario(array $u): int
    {
        return (int) $this->query(
            "INSERT INTO usuarios (nombre, apaterno, amaterno, correo, pass, rol, activo, must_change_pass)
             VALUES (:n, :ap, :am, :c, :p, :r, true, true)
             RETURNING id",
            [
                ':n' => $u['nombre'],
                ':ap' => $u['apaterno'],
                ':am' => $u['amaterno'],
                ':c' => $u['correo'],
                ':p' => $u['pass'],
                ':r' => $u['rol'],
            ]
        )->fetchColumn();
    }

    public function updateUsuarioNombre(int $id, string $n, string $ap, ?string $am): void
    {
        $this->query(
            "UPDATE usuarios SET nombre=:n, apaterno=:ap, amaterno=:am WHERE id=:id",
            [':n' => $n, ':ap' => $ap, ':am' => $am, ':id' => $id]
        );
    }

    public function updateUsuarioCorreo(int $id, string $correo): void
    {
        $this->query("UPDATE usuarios SET correo=:c WHERE id=:id", [':c' => $correo, ':id' => $id]);
    }

    public function upsertPerfilNombreCompleto(int $usuarioId, string $nombreCompleto): void
    {
        $this->query(
            "INSERT INTO perfiles_usuario (usuario_id, nombre_completo)
             VALUES (:u, :n)
             ON CONFLICT (usuario_id) DO UPDATE SET nombre_completo=EXCLUDED.nombre_completo",
            [':u' => $usuarioId, ':n' => $nombreCompleto]
        );
    }

    public function ensureDocente(int $usuarioId): void
    {
        $this->query("INSERT INTO docentes (usuario_id) VALUES (:u) ON CONFLICT (usuario_id) DO NOTHING", [':u' => $usuarioId]);
    }

    public function findEstudianteByMatricula(string $matricula): ?array
    {
        $row = $this->query(
            "SELECT e.usuario_id, u.correo
             FROM estudiantes e
             JOIN usuarios u ON u.id = e.usuario_id
             WHERE e.matricula=:m
             LIMIT 1",
            [':m' => $matricula]
        )->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function ensureEstudiante(int $usuarioId, string $matricula): void
    {
        $this->query(
            "INSERT INTO estudiantes (usuario_id, matricula)
             VALUES (:u, :m)
             ON CONFLICT (matricula) DO NOTHING",
            [':u' => $usuarioId, ':m' => $matricula]
        );
    }

    public function upsertGrupoOferta(int $periodoId, int $materiaId, string $grupo, int $docenteUsuarioId): int
    {
        return (int) $this->query(
            "INSERT INTO grupos_oferta (periodo_id, materia_id, grupo, docente_usuario_id)
             VALUES (:p, :m, :g, :d)
             ON CONFLICT (periodo_id, materia_id, grupo, docente_usuario_id) DO UPDATE
               SET grupo=EXCLUDED.grupo
             RETURNING id",
            [':p' => $periodoId, ':m' => $materiaId, ':g' => $grupo, ':d' => $docenteUsuarioId]
        )->fetchColumn();
    }

    public function ensureInscripcion(int $grupoOfertaId, int $estudianteUsuarioId): int
    {
        $stmt = $this->query(
            "INSERT INTO inscripciones (grupo_oferta_id, estudiante_usuario_id)
             VALUES (:go, :eu)
             ON CONFLICT (grupo_oferta_id, estudiante_usuario_id) DO NOTHING",
            [':go' => $grupoOfertaId, ':eu' => $estudianteUsuarioId]
        );
        return $stmt->rowCount(); // 1 si insertó, 0 si ya existía
    }
}
