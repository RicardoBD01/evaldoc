<?php
// evaldoc/services/AuthService.php
declare(strict_types=1);

require_once __DIR__ . '/../repositories/UserRepository.php';

final class AuthService
{
    private UserRepository $users;

    public function __construct(?UserRepository $users = null)
    {
        $this->users = $users ?? new UserRepository();
    }

    public function login(string $email, string $password): ?array
    {
        $usuario = $this->users->findByEmail($email, true);
        if (!$usuario)
            return null;

        if (!password_verify($password, (string) $usuario['pass'])) {
            return null;
        }

        unset($usuario['pass']);
        return $usuario; // incluye must_change_pass
    }

    public function changePassword(int $userId, string $newPassword): bool
    {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        return $this->users->setPasswordAndClearFlag($userId, $hash) > 0;
    }

}
