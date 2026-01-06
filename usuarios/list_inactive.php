<?php
declare(strict_types=1);

require_once $_SERVER['DOCUMENT_ROOT'] . "/evaldoc/conn/conn.php";

$principal = new Principal();
$usuarios = $principal->obtenerUsuariosDesactivados();

if (empty($usuarios)) {
    echo "<tr><td colspan='7' class='text-center text-muted'>No hay usuarios desactivados</td></tr>";
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

    // Acción: Reactivar
    echo "<td class='text-center'>
            <button class='btn btn-sm btn-success btnReactivarUsuario'
                    data-id='{$id}'
                    title='Reactivar usuario'>
              <i class='fa-solid fa-rotate-left'></i>
            </button>
          </td>";

    echo "</tr>";
}
