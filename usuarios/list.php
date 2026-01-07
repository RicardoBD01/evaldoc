<?php
declare(strict_types=1);

require_once $_SERVER['DOCUMENT_ROOT'] . "/evaldoc/repositories/UserRepository.php";

$repo = new UserRepository();
$usuarios = $repo->getAllActive();

if (empty($usuarios)) {
    echo "<tr><td colspan='7' class='text-center text-muted'>No hay usuarios activos</td></tr>";
    exit;
}

foreach ($usuarios as $i => $u) {
    $id = (int) $u['id'];

    echo "<tr>";
    echo "<th scope='row'>" . ($i + 1) . "</th>";
    echo "<td>" . htmlspecialchars($u['nombre']) . "</td>";
    echo "<td>" . htmlspecialchars($u['apaterno']) . "</td>";
    echo "<td>" . htmlspecialchars($u['amaterno'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($u['correo']) . "</td>";
    echo "<td>" . htmlspecialchars($u['rol']) . "</td>";
    echo "<td class='text-center'>
            <button class='btn btn-sm btn-info btnEditarContraseña' data-id='{$id}'><i class='fa-solid fa-key'></i></button>
            <button class='btn btn-sm btn-warning btnEditarUsuario' data-id='{$id}'><i class='fa-regular fa-pen-to-square'></i></button>
            <button class='btn btn-sm btn-danger btnEliminarUsuario' data-id='{$id}'><i class='fa-regular fa-trash-can'></i></button>
          </td>";
    echo "</tr>";
}
