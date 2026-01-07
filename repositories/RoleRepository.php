<?php
// evaldoc/repositories/RoleRepository.php
declare(strict_types=1);

require_once __DIR__ . '/BaseRepository.php';

final class RoleRepository extends BaseRepository
{
    public function getAll(): array
    {
        $sql = "SELECT id, rol FROM roles ORDER BY rol ASC";
        $stmt = $this->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
