<?php
// evaldoc/services/UserService.php
declare(strict_types=1);

require_once __DIR__ . '/../repositories/UserRepository.php';

final class UserService
{
    private UserRepository $users;

    public function __construct(?UserRepository $users = null)
    {
        $this->users = $users ?? new UserRepository();
    }

    public function validarCorreoUnico(string $correo, ?int $excludeUserId = null): array
    {
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'message' => 'El correo no es válido.'];
        }

        if ($this->users->emailExists($correo, $excludeUserId)) {
            return ['ok' => false, 'message' => 'El correo ya está registrado.'];
        }

        return ['ok' => true, 'message' => 'OK'];
    }

    public function crearUsuario(
        string $nombre,
        string $apaterno,
        ?string $amaterno,
        string $correo,
        string $passPlano,
        int $rol
    ): array {
        $val = $this->validarCorreoUnico($correo);
        if (!$val['ok'])
            return ['success' => false, 'message' => $val['message']];

        $hash = password_hash($passPlano, PASSWORD_DEFAULT);

        try {
            $id = $this->users->insert($nombre, $apaterno, $amaterno, $correo, $hash, $rol);
            return ['success' => true, 'message' => 'Usuario agregado correctamente.', 'id' => $id];
        } catch (PDOException $e) {
            if ($e->getCode() === '23505') {
                return ['success' => false, 'message' => 'El correo ya está registrado.'];
            }
            return ['success' => false, 'message' => 'Error al insertar el usuario.'];
        }
    }

    public function actualizarUsuario(
        int $id,
        string $nombre,
        string $apaterno,
        ?string $amaterno,
        string $correo,
        int $rol,
        ?string $passPlano = null
    ): array {
        $val = $this->validarCorreoUnico($correo, $id);
        if (!$val['ok'])
            return ['success' => false, 'message' => $val['message']];

        $passHash = null;
        if ($passPlano !== null && $passPlano !== '') {
            $passHash = password_hash($passPlano, PASSWORD_DEFAULT);
        }

        try {
            $affected = $this->users->update($id, $nombre, $apaterno, $amaterno, $correo, $rol, $passHash);
            if ($affected === 0) {
                return ['success' => false, 'message' => 'El usuario no existe o está desactivado.'];
            }
            return ['success' => true, 'message' => 'Usuario actualizado correctamente.'];
        } catch (PDOException $e) {
            if ($e->getCode() === '23505') {
                return ['success' => false, 'message' => 'El correo ya está registrado.'];
            }
            return ['success' => false, 'message' => 'Error al actualizar el usuario.'];
        }
    }

    public function desactivarUsuario(int $id): array
    {
        $u = $this->users->findById($id, false);
        if (!$u)
            return ['success' => false, 'message' => 'El usuario no existe.'];
        if ((bool) $u['activo'] === false)
            return ['success' => false, 'message' => 'El usuario ya está desactivado.'];

        $affected = $this->users->setActive($id, false);
        return $affected > 0
            ? ['success' => true, 'message' => 'Usuario desactivado correctamente.']
            : ['success' => false, 'message' => 'No se pudo desactivar el usuario.'];
    }

    public function reactivarUsuario(int $id): array
    {
        $u = $this->users->findById($id, false);
        if (!$u)
            return ['success' => false, 'message' => 'El usuario no existe.'];
        if ((bool) $u['activo'] === true)
            return ['success' => false, 'message' => 'El usuario ya está activo.'];

        $affected = $this->users->setActive($id, true);
        return $affected > 0
            ? ['success' => true, 'message' => 'Usuario reactivado correctamente.']
            : ['success' => false, 'message' => 'No se pudo reactivar el usuario.'];
    }

    public function cambiarContrasena(int $id, string $passPlano): array
    {
        if (strlen($passPlano) < 8) {
            return ['success' => false, 'message' => 'La contraseña debe tener al menos 8 caracteres.'];
        }

        $hash = password_hash($passPlano, PASSWORD_DEFAULT);
        $affected = $this->users->setPasswordHash($id, $hash);

        return $affected > 0
            ? ['success' => true, 'message' => 'Contraseña actualizada correctamente.']
            : ['success' => false, 'message' => 'El usuario no existe o está desactivado.'];
    }
}
